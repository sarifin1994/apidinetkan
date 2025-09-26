<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use App\Models\Member;
use App\Models\OltUser;
use App\Models\OltDevice;
use Illuminate\View\View;
use App\Enums\OltDeviceEnum;
use Illuminate\Http\Request;
use App\Models\OltDeviceZone;
use App\Models\RadiusSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Modules\Olt\Services\OltService;
use Yajra\DataTables\Facades\DataTables;
use App\DataTables\Admin\OltDeviceDataTable;
use App\Http\Requests\Admin\OltDeviceRequest;
use App\Models\OltConfig;
use App\Models\OltDeviceOdb;
use App\Models\OltHistory;

class OltDeviceController extends Controller
{
    /**
     * Display a listing of OLT devices.
     */
    public function index(OltDeviceDataTable $dataTable, Request $request): View|JsonResponse
    {
        $user = $request->user();
        $oltCount = OltDevice::where('group_id', $user->id_group)->count();
        $license = ($user->load('license'))->license;
        $models = OltDeviceEnum::getSupportedModels($license);

        return $dataTable->render('olt.index_new', [
            'oltCount' => $oltCount,
            'license' => $license,
            'models' => $models,
        ]);
    }


    function convertToJWT($plainToken)
    {
        $key = "https://radiusqu.com"; // Gantilah dengan secret key yang aman
        $payload = [
            'token' => $plainToken,  // Masukkan token biasa ke dalam JWT
            'iat' => time(),         // Waktu dibuat (issued at)
            'exp' => time() + 3600,  // Expired dalam 1 jam
        ];

        return JWT::encode($payload, $key, 'HS256');
    }



    function getSNMPTypes()
    {
        $jwtToken = '@token=' . $this->convertToJWT(session()->get('_token'));
        $url = "http://103.184.122.170/api/snmp/types";

        // Inisialisasi cURL
        $ch = curl_init();

        // Set opsi cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $jwtToken",  // Set header token
            "Accept: application/json"
        ]);

        // Eksekusi request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Cek status code

        // Cek jika terjadi error
        if (curl_errno($ch)) {
            return "cURL Error: " . curl_error($ch);
        }

        curl_close($ch);

        // Jika status code bukan 200, kembalikan pesan error
        if ($httpCode !== 200) {
            return "Error: HTTP Status $httpCode - " . $response;
        }

        // Konversi hasil JSON ke array PHP
        return json_decode($response, true);
    }

    /**
     * Store a newly created OLT device.
     */
    public function store(OltDeviceRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();
        $license = $user->license;

        $model = $data['model'];

        if ($model != 'zte' && $model != 'fiberhome') {
            $type = $data['type'];
            $oltCount = OltDevice::where('group_id', $user->id_group)
                ->where('type', $type)
                ->count();
            if ($type === 'epon' && $oltCount >= $license->olt_epon_limit) {
                return response()->json(['message' => 'You have reached the maximum limit of EPON OLT'], 400);
            }

            if ($type === 'gpon' && $oltCount >= $license->olt_gpon_limit) {
                return response()->json(['message' => 'You have reached the maximum limit of GPON OLT'], 400);
            }

            if ($type === 'epon' && !$license->olt_epon) {
                return response()->json(['message' => 'EPON OLT is not enabled in your license'], 400);
            }

            if ($type === 'gpon' && !$license->olt_gpon) {
                return response()->json(['message' => 'GPON OLT is not enabled in your license'], 400);
            }
        }

        if ($data['model'] && !in_array($data['model'], $license->olt_models)) {
            return response()->json(['message' => 'Unsupported OLT model'], 400);
        }

        try {
            OltDevice::create([
                ...$data,
                'group_id' => $request->user()->id_group,
                'token' => '',
                'user_id' => $request->user()->id,
            ]);

            return response()->json(['message' => 'OLT created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating OLT: ' . $data], 500);
        }
    }

    /**
     * Show the specified OLT device.
     */
    public function edit(OltDevice $olt): JsonResponse
    {
        return response()->json($olt);
    }

    /**
     * Update the specified OLT device.
     */
    public function update(OltDeviceRequest $request, OltDevice $olt): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();
        $license = $user->license;

        if ($data['model'] != 'fiberhome' && $data['model'] != 'zte') {
            $type = $data['type'];
            if ($type === 'epon' && !$license->olt_epon) {
                return response()->json(['message' => 'EPON OLT is not enabled in your license'], 400);
            }

            if ($type === 'gpon' && !$license->olt_gpon) {
                return response()->json(['message' => 'GPON OLT is not enabled in your license'], 400);
            }
        }

        if ($data['model'] && !in_array($data['model'], $license->olt_models)) {
            return response()->json(['message' => 'Unsupported OLT model'], 400);
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        try {
            $olt->update($data);
            return response()->json(['message' => 'OLT updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating OLT'], 500);
        }
    }

    /**
     * Remove the specified OLT device.
     */
    public function destroy(OltDevice $olt)
    {
        try {
            $olt->delete();
            return redirect()->back()->with('success', 'OLT deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting OLT');
        }
    }

    /**
     * Authenticate with an OLT device.
     */
    public function do_auth_device(Request $request)
    {
        $user = $request->user();
        $olt = OltDevice::find($request->id);

        if (!$olt) {
            $request->session()->flash('error', 'OLT not found!');
            return response()->json(['success' => false, 'message' => 'OLT Not Found'], 404);
        }

        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token ?? '',
            $olt->udp_port ?? '',
            $olt->snmp_read_write ?? '',
            $olt->version ?? ''
        );

        $connect = $oltService->login();
        // print_r($connect);
        if (!array_filter($connect)) {
            $request->session()->flash('error', 'Failed to connect to OLT');
            return response()->json(['success' => false, 'message' => 'Failed to connect to OLT'], 500);
        } else {
            $boardInfo = $oltService->ifExntry();
            if ($olt->model->value == 'zte' || $olt->model->value == 'fiberhome') {
                $port = empty($olt->udp_port) ? ' ' : 'port ' . $olt->udp_port;
                if (!array_filter($boardInfo) && !isset($boardInfo['response']['management_onu'])) {
                    $request->session()->flash('error', "OLT $olt->name unreachable : SNMP $port timeout");
                    return response()->json(['success' => false, 'message' => "OLT $olt->name unreachable : SNMP $port timeout"], 500);
                }
            }


            if (isset($connect['status']) && $connect['status'] !== 'success') {
                if ($olt->model !== 'zte' && $olt->model !== 'fiberhome') {
                    $request->session()->flash('error', 'Username or password is incorrect');
                    return response()->json(['success' => false, 'message' => 'Username or password is incorrect'], 401);
                }
            }

            $token = $connect['token'] ?? null;

            if ($token) {
                $olt->update(['token' => $token]);
            }

            $sess_data = [
                'id_olt' => $olt->id,
                'token' => $token ?: '',
                'namaolt' => $olt->name,
                'host' => $olt->host,
            ];

            $request->session()->put($sess_data);
            return response()->json(['success' => true, 'message' => 'Login successful', 'data' => $boardInfo], 200);
        }
    }




    /**
     * Logout from OLT device.
     */
    public function deviceLogout(Request $request)
    {
        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');

        $olt = OltDevice::find($idOlt);

        if (!$olt) {
            $request->session()->flash('error', 'No OLT session found to logout.');
            return redirect()->to('/' . strtolower($user->role) . '/olt');
        }

        // Instantiate OltService
        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token
        );

        // Call logout
        $logout = $oltService->logout();

        // Assuming the API returns a code indicating success
        // Clear OLT session data
        $request->session()->forget(['id_olt', 'token', 'host', 'namaolt']);
        $request->session()->flash('success', 'Anda telah logout dari OLT ' . $namaolt);

        return redirect()->to('/' . strtolower($user->role) . '/olt/device/logout');
    }

    /**
     * Show OLT dashboard.
     */
    public function show_olt(Request $request, OltDevice $id)
    {
        $user = $request->user();
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $idOlt = $request->session()->get('id_olt');

        if (is_null($token)) {
            $request->session()->flash('error', 'Silahkan klik connect terlebih dahulu');
            return redirect()->to('/' . strtolower($user->role) . '/olt');
        }

        $olt = OltDevice::find($idOlt);
        if (!$olt) {
            $request->session()->flash('error', 'Invalid OLT session');
            return redirect()->to('/' . strtolower($user->role) . '/olt');
        }


        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token ?? '',
            $olt->udp_port ?? '',
            $olt->snmp_read_write ?? '',
            $olt->version ?? ''
        );
        // Instantiate OltService


        // Fetch Board Info


        $boardInfo = $oltService->getBoardInfo();

        if (!$boardInfo) {
            // try to re-login
            $connect = $oltService->login();

            if (isset($connect['status']) && $connect['status'] !== 'success') {
                $request->session()->flash('error', 'Username or password is incorrect');
                return redirect()->to('/' . strtolower($user->role) . '/olt');
            }

            $token = $connect['token'] ?? null;

            if ($token) {
                $olt->update(['token' => $token]);
                $request->session()->put(['token' => $token]);
            }

            $boardInfo = $oltService->getBoardInfo();
        }

        if ($olt->model->value == 'zte' || $olt->model->value == 'fiberhome') {
            $port = empty($olt->udp_port) ? ' ' : 'port ' . $olt->udp_port;
            if ($boardInfo && !isset($boardInfo['response']['management_onu'])) {
                $request->session()->flash('error', "OLT $olt->name unreachable : SNMP $port timeout");
                return redirect()->to('/' . strtolower($user->role) . '/olt');
            }
            $statusMapping = [
                "1" => "logging",
                "2" => "los",
                "3" => "syncMib",
                "4" => "working",
                "5" => "dyingGasp",
                "6" => "authFailed",
                "7" => "offline",
                "logging" => "1",
                "los" => "2",
                "syncMib" => "3",
                "working" => "4",
                "dyingGasp" => "5",
                "authFailed" => "6",
                "offline" => "7"
            ];

            // CARI JUMLAH STATUS ONU
            $phaseCounts = array_count_values(array_column($boardInfo['response']['onu_status'], 'zxAnGponSrvOnuPhaseStatus'));
            // Set default count ke 0 jika tidak ada status tertentu
            foreach ($statusMapping as $key => $status) {
                if (!isset($phaseCounts[$status])) {
                    $phaseCounts[$status] = 0;
                }
            }
            // // CARI JUMLAH STATUS ONU



            $data = [
                'title' => 'Dashboard OLT',
                'namaolt' => $namaolt,
                'olt' => $olt,
                'onu_status' => $phaseCounts, // Memasukkan hasil perhitungan ke dalam `onu_status`
                // 'management_onu' => $boardInfo['response']['management_onu'] ?? [],
                // 'unconfigured_onu' => $boardInfo['response']['unconfigured_onu'] ?? [],
                // 'device' => $systemInfo['response']['data'] ?? [],
            ];
        } else {
            if (!$boardInfo) {
                $request->session()->flash('error', 'Failed to fetch OLT data');
                return redirect()->to('/' . strtolower($user->role) . '/olt');
            }
            $systemInfo = $oltService->getSystemInfo();
            $data = [
                'title' => 'Dashboard OLT',
                'namaolt' => $namaolt,
                'olt' => $olt,
                'pon' => $boardInfo['response']['data'] ?? [],
                'device' => $systemInfo['response']['data'] ?? [],
            ];
        }
        return view('olt.device.dashboard', compact('data'));
    }

    // GET TOKEN OLT ZTE & FIBERHOME
    public function getSnmpToken(array $params): ?string
    {
        $apiUrl = 'http://103.184.122.170/api/snmp/token';

        try {
            // Kirim POST request ke API eksternal
            $response = Http::post($apiUrl, $params);

            // Periksa apakah request berhasil
            if ($response->successful()) {
                $data = $response->json();
                return $data['token'] ?? null;
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }


    /**
     * Show OLT PON details.
     */
    public function show_pon(Request $request, $id)
    {
        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');

        // Find the OltDevice
        $olt = OltDevice::find($idOlt);
        if (!$olt) {
            $request->session()->flash('error', 'Invalid OLT session');
            return redirect()->to('/' . strtolower($user->role) . '/olt');
        }

        // Instantiate OltService
        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token
        );

        // Fetch PON data
        $boardInfo = $oltService->getBoardInfo();

        // Fetch ONU Allow List for the specified port
        $onuAllowList = $oltService->getOnuList($id);

        $data = [
            'title' => 'OLT PON ' . $id,
            'id' => $id,
            'pon' => $boardInfo['response']['data'] ?? [],
            'onu' => $onuAllowList['response']['data'] ?? [],
        ];

        if ($request->ajax()) {
            return DataTables::of($data['onu'])
                ->addIndexColumn()
                ->addColumn('area', function ($row) use ($idOlt) {
                    $user = OltUser::where('olt_id', $idOlt)
                        ->where('port_id', $row['port_id'])
                        ->where('onu_id', $row['onu_id'])
                        ->with('ppp:id,kode_area')
                        ->first();
                    return $user ? $user->ppp->kode_area : '-';
                })
                ->addColumn('odp', function ($row) use ($idOlt) {
                    $user = OltUser::where('olt_id', $idOlt)
                        ->where('port_id', $row['port_id'])
                        ->where('onu_id', $row['onu_id'])
                        ->with('ppp:id,kode_odp')
                        ->first();
                    return $user ? $user->ppp->kode_odp : '-';
                })
                ->addColumn('member', function ($row) use ($idOlt) {
                    $user = OltUser::where('olt_id', $idOlt)
                        ->where('port_id', $row['port_id'])
                        ->where('onu_id', $row['onu_id'])
                        ->with('member:id,full_name')
                        ->first();
                    return $user ? $user->member->full_name : '-';
                })
                ->toJson();
        }

        return view('olt.device.onu', compact('data'));
    }

    /**
     * Show all OLT PON details.
     */
    public function show_pon_all(Request $request)
    {
        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');

        // Find the OltDevice
        $olt = OltDevice::find($idOlt);
        if (!$olt) {
            $request->session()->flash('error', 'Invalid OLT session');
            return redirect()->to('/' . strtolower($user->role) . '/olt');
        }

        // Instantiate OltService
        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token
        );

        // Fetch PON data
        $boardInfo = $oltService->getBoardInfo();

        // Fetch ONU Allow List All
        $onuAllowListAll = $oltService->getOnuTable();

        $data = [
            'title' => 'OLT PON All',
            'pon' => $boardInfo['response']['data'] ?? [],
            'onu' => $onuAllowListAll['response']['data'] ?? [],
        ];

        if ($request->ajax()) {
            return DataTables::of($data['onu'])
                ->addIndexColumn()
                ->addColumn('area', function ($row) use ($idOlt) {
                    $user = OltUser::where('olt_id', $idOlt)
                        ->where('port_id', $row['port_id'])
                        ->where('onu_id', $row['onu_id'])
                        ->with('ppp:id,kode_area')
                        ->first();
                    return $user ? $user->ppp->kode_area : '-';
                })
                ->addColumn('odp', function ($row) use ($idOlt) {
                    $user = OltUser::where('olt_id', $idOlt)
                        ->where('port_id', $row['port_id'])
                        ->where('onu_id', $row['onu_id'])
                        ->with('ppp:id,kode_odp')
                        ->first();
                    return $user ? $user->ppp->kode_odp : '-';
                })
                ->addColumn('member', function ($row) use ($idOlt) {
                    $user = OltUser::where('olt_id', $idOlt)
                        ->where('port_id', $row['port_id'])
                        ->where('onu_id', $row['onu_id'])
                        ->with('member:id,full_name')
                        ->first();
                    return $user ? $user->member->full_name : '-';
                })
                ->toJson();
        }

        return view('olt.device.onu', compact('data'));
    }

    /**
     * Show ONU details.
     */
    public function show_onu(Request $request, $port, $onu)
    {
        $user = $request->user();
        $members = Member::where('group_id', $user->id_group)->get();
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');

        if (is_null($token)) {
            $request->session()->flash('error', 'Silahkan klik connect terlebih dahulu');
            return redirect()->to('/' . strtolower($user->role) . '/olt');
        }

        $olt = OltDevice::find($idOlt);
        if (!$olt) {
            $request->session()->flash('error', 'Invalid OLT session');
            return redirect()->to('/' . strtolower($user->role) . '/olt');
        }

        // Instantiate OltService
        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token
        );

        // Fetch OltUser record
        $record = OltUser::where('olt_id', $idOlt)
            ->where('port_id', $port)
            ->where('onu_id', $onu)
            ->with('ppp', 'member', 'ppp.data')
            ->first();

        if ($record) {
            $session = RadiusSession::with('ppp:username,status')
                ->where('username', $record->ppp->username)
                ->orderBy('id', 'desc')
                ->first();
        }

        // Fetch ONU Data
        $onuData = $oltService->getOnuData($port, $onu);

        $data = [
            'title' => 'OLT PON ' . $port,
            'id' => $port,
            'onu' => (object)($onuData['response']['data'] ?? []),
            'user' => $record,
            'session' => $session ?? null,
        ];

        return view('olt.device.detail-onu', compact('data', 'members'));
    }

    /**
     * Reboot a specific ONU.
     */
    public function reboot_onu(Request $request, $port, $onu)
    {
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');

        if (is_null($token)) {
            $request->session()->flash('error', 'Silahkan klik connect terlebih dahulu');
            return redirect()->back();
        }

        $idOlt = $request->session()->get('id_olt');
        $olt = OltDevice::find($idOlt);
        if (!$olt) {
            $request->session()->flash('error', 'Invalid OLT session');
            return redirect()->back();
        }

        // Instantiate OltService
        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token
        );

        // Call rebootOnu
        $reboot = $oltService->rebootOnu($port, $onu);

        // Handle the response accordingly
        if ($rebootResult && isset($rebootResult['success']) && $rebootResult['success'] == true) {
            return response()->json(['success' => true, 'message' => 'ONU Berhasil Direboot']);
        } else {
            return response()->json(['success' => false, 'message' => 'ONU Gagal Direboot'], 500);
        }
    }

    /**
     * Reboot the entire OLT system.
     */
    public function reboot_olt(Request $request, $oltId)
    {
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');

        if (is_null($token)) {
            $request->session()->flash('error', 'Silahkan klik connect terlebih dahulu');
            return redirect()->back();
        }

        $olt = OltDevice::find($oltId);
        if (!$olt) {
            $request->session()->flash('error', 'Invalid OLT session');
            return redirect()->back();
        }

        // Instantiate OltService
        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token
        );

        // Call rebootSystem
        $reboot = $oltService->rebootSystem();

        if ($reboot && isset($reboot['success']) && $reboot['success'] == true) {
            // Clear OLT session data
            $request->session()->forget(['id_olt', 'token', 'host', 'namaolt']);

            return response()->json(['success' => true, 'message' => 'OLT Berhasil Direboot']);
        } else {
            $request->session()->flash('error', 'Reboot OLT failed.');
            return response()->json(['success' => false, 'message' => 'Reboot OLT failed.'], 500);
        }
    }

    /**
     * Save the OLT system configuration.
     */
    public function save_olt(Request $request, $oltId)
    {
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');

        if (is_null($token)) {
            $request->session()->flash('error', 'Silahkan klik connect terlebih dahulu');
            return redirect()->back();
        }

        $olt = OltDevice::find($oltId);
        if (!$olt) {
            $request->session()->flash('error', 'Invalid OLT session');
            return redirect()->back();
        }

        // Instantiate OltService
        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token
        );

        // Call saveSystem
        $save = $oltService->saveSystem();

        if ($saveResult && isset($saveResult['success']) && $saveResult['success'] == true) {
            return response()->json(['success' => true, 'message' => 'OLT Configuration Berhasil Disimpan']);
        } else {
            return response()->json(['success' => false, 'message' => 'OLT Configuration Gagal Disimpan'], 500);
        }
    }

    /**
     * Rename an ONU.
     */
    public function rename(Request $request)
    {
        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $port = $request->port_id;
        $onu = $request->onu_id;
        $name = $request->onu_name;

        if (is_null($token)) {
            $request->session()->flash('error', 'Silahkan klik connect terlebih dahulu');
            return redirect()->to('/' . strtolower($user->role) . '/olt');
        }

        $olt = OltDevice::find($idOlt);
        if (!$olt) {
            $request->session()->flash('error', 'Invalid OLT session');
            return redirect()->to('/' . strtolower($user->role) . '/olt');
        }

        // Instantiate OltService
        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token
        );

        // Call changeName
        $changeName = $oltService->changeName($port, $onu, $name);

        if ($changeNameResult && isset($changeNameResult['success']) && $changeNameResult['success'] == true) {
            return response()->json(['success' => true, 'message' => 'ONU Berhasil Direname']);
        } else {
            return response()->json(['success' => false, 'message' => 'Rename ONU Gagal'], 500);
        }
    }

    /**
     * Sync OLT user data.
     */
    public function sync(Request $request)
    {
        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');

        $port = $request->port_id;
        $onu = $request->onu_id;
        $pppoe_id = $request->pppoe_id;
        $member_id = $request->member_id;

        if (is_null($token)) {
            $request->session()->flash('error', 'Silahkan klik connect terlebih dahulu');
            return redirect()->to('/' . strtolower($user->role) . '/olt');
        }

        $olt = OltDevice::find($idOlt);
        if (!$olt) {
            $request->session()->flash('error', 'Invalid OLT session');
            return redirect()->to('/' . strtolower($user->role) . '/olt');
        }

        // Instantiate OltService
        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token
        );

        // Check if OltUser exists
        $existing = OltUser::where('olt_id', $idOlt)
            ->where('port_id', $port)
            ->where('onu_id', $onu)
            ->first();

        if ($existing === null) {
            // Create new OltUser
            $newUser = OltUser::create([
                'group_id' => $user->id_group,
                'pppoe_id' => $pppoe_id,
                'member_id' => $member_id,
                'olt_id' => $idOlt,
                'port_id' => $port,
                'onu_id' => $onu,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Disimpan',
                'data' => $newUser,
            ]);
        } else {
            // Update existing OltUser
            $existing->update([
                'pppoe_id' => $pppoe_id,
                'member_id' => $member_id,
                'olt_id' => $idOlt,
                'port_id' => $port,
                'onu_id' => $onu,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Diupdate',
                'data' => $existing,
            ]);
        }
    }

    /**
     * Delete an ONU.
     */
    public function delete_onu(Request $request, $port, $onu)
    {
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $mac_address = $request->mac;

        if (is_null($token)) {
            $request->session()->flash('error', 'Silahkan klik connect terlebih dahulu');
            return redirect()->back();
        }

        $olt = OltDevice::find($idOlt);
        if (!$olt) {
            $request->session()->flash('error', 'Invalid OLT session');
            return redirect()->back();
        }

        // Instantiate OltService
        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token
        );

        // Call deleteOnuList
        $delete = $oltService->deleteOnuList($port, $onu, $mac_address);

        // Delete OltUser record
        OltUser::where('olt_id', $idOlt)
            ->where('port_id', $port)
            ->where('onu_id', $onu)
            ->delete();

        return response()->json(['success' => true, 'message' => 'ONU Berhasil Dihapus']);
    }

    // UNCONFIG ONU
    public function unconfigured_olt(Request $request)
    {
        // Mengambil data dari API eksternal
        $user = $request->user();
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $idOlt = $request->session()->get('id_olt');

        $olt = OltDevice::find($idOlt);
        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token ?? '',
            $olt->udp_port ?? '',
            $olt->snmp_read_write ?? '',
            $olt->version ?? ''
        );
        // Instantiate OltService


        // Fetch Board Info
        $boardInfo = $oltService->getBoardInfo();

        // TYPE PON UNCOFIGURED
        $mapArray4 = array_column($boardInfo['response']['data_unconfigured_onu_type'], 'zxAnPonSrvChannelType', 'id');
        foreach ($boardInfo['response']['unconfigured_onu'] as &$item) {
            $id = $item['id'];
            $item['zxAnPonSrvChannelType'] = $mapArray4[$id] ?? null; // Tambahkan data dari array2
        }

        $mapArray6 = array_column($boardInfo['response']['data_onu'], 'ifName', 'id');
        foreach ($boardInfo['response']['unconfigured_onu'] as &$item) {
            $id = preg_replace('/\..*/', '', $item['id']);
            $item['ifName'] =  $mapArray6[$id] ?? null; // Tambahkan data dari array2
        }
        // TYPE PON UNCOFIGURED
        $data = $boardInfo['response']['unconfigured_onu']; // Mengubah data JSON menjadi array

        // Mengembalikan data dalam format yang sesuai untuk DataTables
        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),  // Bisa disesuaikan jika ada filter
            'data' => $data,
        ]);
    }



    function getRegMod(Request $request)
    {
        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $url = "http://103.184.122.170/api/snmp/find/zxAnGponOnuMgmtRegMode";

        // Inisialisasi cURL
        $ch = curl_init();

        // Set opsi cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",  // Set header token
            "Accept: application/json"
        ]);

        // Eksekusi request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Cek status code

        // Cek jika terjadi error
        if (curl_errno($ch)) {
            return "cURL Error: " . curl_error($ch);
        }

        curl_close($ch);

        // Jika status code bukan 200, kembalikan pesan error
        if ($httpCode !== 200) {
            return "Error: HTTP Status $httpCode - " . $response;
        }

        // Konversi hasil JSON ke array PHP
        return json_decode($response, true);
    }

    function getStatusOnu(Request $request)
    {
        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $url = "http://103.184.122.170/api/snmp/find/zxAnGponOnuMgmtTargetStatus";

        // Inisialisasi cURL
        $ch = curl_init();

        // Set opsi cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",  // Set header token
            "Accept: application/json"
        ]);

        // Eksekusi request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Cek status code

        // Cek jika terjadi error
        if (curl_errno($ch)) {
            return "cURL Error: " . curl_error($ch);
        }

        curl_close($ch);

        // Jika status code bukan 200, kembalikan pesan error
        if ($httpCode !== 200) {
            return "Error: HTTP Status $httpCode - " . $response;
        }

        // Konversi hasil JSON ke array PHP
        return json_decode($response, true);
    }

    function getVPort(Request $request)
    {
        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $url = "http://103.184.122.170/api/snmp/find/zxAnGponOnuMgmtVportMode";

        // Inisialisasi cURL
        $ch = curl_init();

        // Set opsi cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",  // Set header token
            "Accept: application/json"
        ]);

        // Eksekusi request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Cek status code

        // Cek jika terjadi error
        if (curl_errno($ch)) {
            return "cURL Error: " . curl_error($ch);
        }

        curl_close($ch);

        // Jika status code bukan 200, kembalikan pesan error
        if ($httpCode !== 200) {
            return "Error: HTTP Status $httpCode - " . $response;
        }

        // Konversi hasil JSON ke array PHP
        return json_decode($response, true);
    }

    function getRowStatus(Request $request)
    {
        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $url = "http://103.184.122.170/api/snmp/find/zxAnGponOnuMgmtRowStatus";

        // Inisialisasi cURL
        $ch = curl_init();

        // Set opsi cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",  // Set header token
            "Accept: application/json"
        ]);

        // Eksekusi request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Cek status code

        // Cek jika terjadi error
        if (curl_errno($ch)) {
            return "cURL Error: " . curl_error($ch);
        }

        curl_close($ch);

        // Jika status code bukan 200, kembalikan pesan error
        if ($httpCode !== 200) {
            return "Error: HTTP Status $httpCode - " . $response;
        }

        // Konversi hasil JSON ke array PHP
        return json_decode($response, true);
    }

    function getOnuType(Request $request)
    {
        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $url = "http://103.184.122.170/api/snmp/zxAnPonOnuTypeTable";

        // Inisialisasi cURL
        $ch = curl_init();

        // Set opsi cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",  // Set header token
            "Accept: application/json"
        ]);

        // Eksekusi request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Cek status code

        // Cek jika terjadi error
        if (curl_errno($ch)) {
            return "cURL Error: " . curl_error($ch);
        }

        curl_close($ch);

        // Jika status code bukan 200, kembalikan pesan error
        if ($httpCode !== 200) {
            return "Error: HTTP Status $httpCode - " . $response;
        }

        // Konversi hasil JSON ke array PHP
        return json_decode($response, true);
    }


    function add_unconfig_onu(Request $request)
    {

        // Mengambil data dari request
        $id_olt = $request->input('id_olt');
        $nama_olt = $request->input('nama_olt');
        $name = $request->input('name');
        $description = empty($request->input('description')) ? '' : $request->input('description');
        $sn = $request->input('sn');
        $onu_type = !empty($request->input('onu_type')) ? $request->input('onu_type') : 'ZTE';
        $reg_mod = !empty($request->input('reg_mod')) ? $request->input('onureg_modtype') : 1;
        $status_onu = (int) 2;
        $vport = (int) 1;
        $row_status = (int) 4;
        $onu_mode = $request->input('onu_mode');
        $vlan_id = (int) $request->input('vlan_id');
        $id_zone = $request->input('id_zone');
        $id_odb = $request->input('id_odb');
        $download_speed = $request->input('download_speed');
        $upload_speed = $request->input('upload_speed');
        $external_id = $request->input('external_id');
        $address = $request->input('address');
        $contact = $request->input('contact');
        $user = $request->user();
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $idOlt = $request->session()->get('id_olt');

        $zone = OltDeviceZone::find($id_zone);

        // Log Data yang akan dikirim ke API

        // Token yang digunakan untuk otentikasi
        $token = $request->session()->get('token');

        // Periksa apakah token tersedia
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token not found',
            ], 401); // Unauthorized
        }

        // CEK DULU IDNYA UDAH ADA DIPAKE ATAU BELUM
        $olt = OltDevice::find($idOlt);
        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token ?? '',
            $olt->udp_port ?? '',
            $olt->snmp_read_write ?? '',
            $olt->version ?? ''
        );
        // Instantiate OltService


        // Fetch Board Info
        $gpom = '';
        $boardInfo = $oltService->getBoardInfo();
        $same_id = false;
        if (!empty($boardInfo)) {
            $mapArray6 = array_column($boardInfo['response']['data_onu'], 'ifName', 'id');
            foreach ($boardInfo['response']['management_onu'] as $item) {
                $id = preg_replace('/\..*/', '', $item['id']);
                if ($id_olt == $item['id']) {
                    $gpom = $mapArray6[$id];
                    $same_id = true;
                    break;
                }
            }
        }
        // CEK DULU IDNYA UDAH ADA DIPAKE ATAU BELUM

        if ($same_id) {
            $lastItem  = end($boardInfo['response']['management_onu']);
            $lastId = $lastItem['id'];
            $lastNumber = substr(strrchr($lastId, "."), 1) + 1; // Ambil setelah titik
            $id_olt = preg_replace('/\.\d+$/', ".$lastNumber", $id_olt);
        }

        $headers = [
            "Authorization: Bearer $token",  // Set header token
            "Content-Type: application/json"
        ];

        $name1 = str_replace(' ', '_', $zone->zone_name);
        $tgl = date('Ymd_H:i:s');
        // UNCONFIG
        $url = "http://103.184.122.170/api/snmp/zxAnGponSrvOnuMgmtTable/" . urlencode($id_olt);

        // DATA UNTUK ADD UNCONFIG DULU
        $data_unconfig = [
            "zxAnGponOnuMgmtRegMode" =>  (int) 1,
            "zxAnGponOnuMgmtSn" => $sn,
            "zxAnGponOnuMgmtName" => $name,
            "zxAnGponOnuMgmtTypeName" => $onu_type,
            "zxAnGponOnuMgmtDesc" => "zone_{$name1}_authd_{$tgl}",
            "zxAnGponOnuMgmtTargetStatus" =>  (int) 2,
            "zxAnGponOnuMgmtVportMode" =>  (int) 1, // Asumsikan ini adalah nilai default
            "zxAnGponOnuMgmtRowStatus" => (int) 4
        ];
        // DATA UNTUK ADD UNCONFIG DULU

        $add_unconfig = json_decode($this->http_post($url, $headers, $data_unconfig), true);
        if ($add_unconfig['status'] == 'error') {
            return $add_unconfig;
        }
        // UNCONFIG

        // TCONF
        //  CEK DULU BUAT AMBIL NILAI BELAKANNGYA
        $id_tconf = "";
        $id_onu_tconf = "";
        $id_baru = preg_match('/^\d+/', $id_olt, $matches);
        $result1 = $matches[0]; // Hasilnya: "285278721"
        $tconf = $oltService->getTConfType();
        $no = 0;
        if (!empty($tconf)) {
            foreach ($tconf as $da) {
                $idd = explode('.', $da['id']);
                $result = $idd[0]; // Hasilnya: "285278721.1
                if ($result == $result1) {
                    $id_onu_tconf = $da['id'];
                    break;
                }
            }
        }
        $partsss = explode('.', $id_onu_tconf);
        $new_int_tconf = end($partsss); // Hasilnya: "1"
        $id_tconf = $id_olt . '.' . $new_int_tconf;
        //  CEK DULU BUAT AMBIL NILAI BELAKANNGYA

        $url_tconf = "http://103.184.122.170/api/snmp/zxAnGponSrvTcontTable/" . urlencode($id_tconf);

        $data_tconfig = [
            "zxAnGponSrvTcontName" =>  "tconf" . $new_int_tconf,
            "zxAnGponSrvTcontBwPrfName" => $download_speed,
            "zxAnGponSrvTcontRowStatus" => (int) 4,
        ];

        $tconfig = json_decode($this->http_post($url_tconf, $headers, $data_tconfig), true);

        if ($tconfig['status'] == 'error') {
            return $tconfig;
        }
        // TCONF

        // GEMPORT
        // GEM PORT
        $id_gemport = "";
        $id_onu_gemport = "";
        $id_baru = preg_match('/^\d+/', $id_olt, $matches);
        $result1 = $matches[0]; // Hasilnya: "285278721"
        $gemport = $oltService->getGemPort();
        $no1 = 0;

        // CEK DAN AMBIL NILAI BELAKANGNYA
        if (!empty($gemport)) {
            foreach ($gemport as $da) {
                $idd = explode('.', $da['id']);
                $result = $idd[0]; // Hasilnya: "285278721.1
                if ($result == $result1) {
                    $id_onu_gemport = $da['id'];
                    break;
                }
            }
        }
        $part_gemp = explode('.', $id_onu_gemport);
        $new_int_gem = end($part_gemp); // Hasilnya: "1"
        $id_gemport = $id_olt . '.' . $new_int_gem;
        // CEK DAN AMBIL NILAI BELAKANGNYA

        // DATA GEMPORT
        $data_gemport = [
            "zxAnGponSrvGemPortName" => "gemport" . $new_int_gem,
            "zxAnGponSrvGemPortTcontIndex" => (int) 1,
            "zxAnGponSrvGemPortQueueOfTcont" => (int) 0,
            "zxAnGponSrvGemPortEnable" => (int) 1,
            "zxAnGponSrvGemPortUsTrafficPrf" => 'default',
            "zxAnGponSrvGemPortDsTrafficPrf" => $upload_speed,
            "zxAnGponSrvGemPortEncrypt" => (int) 2,
            "zxAnGponSrvGemPortRowStatus" => (int) 4
        ];
        // DATA GEMPORT


        // GEM PORT
        // INSERT GEMPORT
        // URL API eksternal
        $url_gemport = "http://103.184.122.170/api/snmp/zxAnGponSrvGemPortTable/" . urlencode($id_gemport);

        $gemport = json_decode($this->http_post($url_gemport, $headers, $data_gemport), true);

        if ($gemport['status'] == 'error') {
            return $gemport;
        }
        // GEMPORT

        // SERVICE PORT GEMPORT
        $partss = explode('.', $id_olt);
        $id_srvport = reverseToId($partss[0], $partss[1]);

        $data_vport_service = [
            "zxAnSrvPortServiceMode" => 4,
            "zxAnSrvPortUserVid" => $vlan_id,
            "zxAnSrvPortCVid" => $vlan_id,
            "zxAnSrvPortBrgIfId" => 1,
            "zxAnSrvPortDesc" => "",
            "zxAnSrvPortAdminStatus" => 1,
            "zxAnSrvPortCtagCos" => 255,
            "zxAnSrvPortSVid" => 0,
            "zxAnSrvPortStagCos" => 255,
            "zxAnSrvPortQueueId" => 255,
            "zxAnSrvPortRowStatus" => 4
        ];

        $url_service = "http://103.184.122.170/api/snmp/zxAnSrvPortConfTable/" . urlencode($id_srvport);

        $srv_port = json_decode($this->http_post($url_service, $headers, $data_vport_service), true);
        if ($srv_port['status'] == 'error') {
            return $srv_port;
        }
        // SERVICE PORT GEMPORT



        // ONU SERVICE
        $vlans = [];
        array_push($vlans, $vlan_id);
        $hexResult = convertVlansToHexOctet($vlans);


        // contoh hanya VLAN 100
        $data_mpp = [
            "zxAnGponRmServiceGemPort" => 1,
            "zxAnGponRmServiceMapType" => 2,
            "zxAnGponRmServiceMapVid" => $hexResult,
            "zxAnGponRmServiceRowStatus" => 4,
        ];

        // $url_service1 = "http://103.184.122.170/api/snmp/zxAnGponRmServiceMapTable/" . urlencode($id_srvport);

        // $srv_port1 = json_decode($this->http_post($url_service1, $headers, $data_mpp), true);
        // if ($srv_port1['status'] == 'error') {
        //     return $srv_port1;
        // }
        // ONU SERVICE

        // CONFIG
        // INSER CONFIG LOCAL
        $data_config = [
            'group_id' => $user->id_group,
            'id_olt' => $idOlt,
            'id_onu' => $id_olt,
            'id_zone' => $id_zone,
            'id_odb' => $id_odb,
            'onu_external_id' => $external_id,
            'onu_mode' => $onu_mode,
            'vlan_id' => $vlan_id,
            'ifName_new' =>  $gpom,
            'ifName_old' =>  $gpom,
            'address' => $address, // atau null kalau nullable
            'contact' => $contact, // atau null kalau nullable
        ];
        $data_sea = OltConfig::create($data_config);
        // INSER CONFIG LOCAL

        // INSERT HISTORY
        $data_history = [
            'group_id' => $user->id_group,
            'id_olt' => $idOlt,
            'id_onu' => $id_olt,
            'history_desc' => 'ONU ' . $sn . ' ' . $gpom . ' authorized',
            'desc_type' => 1,
        ];
        $hiss = OltHistory::create($data_history);
        // INSERT HISTORY
        // CONFIG

        return $data_config;
    }

    // UNCONFIG ONU

    // CONFIG ONU
    public function configured_onu(Request $request)
    {
        // Mengambil data dari API eksternal
        $user = $request->user();
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $idOlt = $request->session()->get('id_olt');

        $olt = OltDevice::find($idOlt);
        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token ?? '',
            $olt->udp_port ?? '',
            $olt->snmp_read_write ?? '',
            $olt->version ?? ''
        );
        // Instantiate OltService


        // Fetch Board Info
        $boardInfo = $oltService->getBoardInfo();



        // STATUS ONU
        $mapArray2 = array_column($boardInfo['response']['onu_status'], 'zxAnGponSrvOnuPhaseStatus', 'id');
        foreach ($boardInfo['response']['management_onu'] as &$item) {
            $id = $item['id'];
            $item['zxAnGponSrvOnuPhaseStatus'] = $mapArray2[$id] ?? null;

            $zone = OltConfig::where('id_olt', $idOlt)
                ->where('group_id', $user->id_group)
                ->where('id_onu', $id)
                ->first(); // Tambahkan data dari array2

            if ($zone) {
                $item['zone'] = $zone->zone_olt->zone_name ?? null; // pastikan kolom 'zone_name' ada di tabel zone
                $item['odb'] = $zone->odb->odb_name ?? 'None'; // pastikan kolom 'odb_name' ada di tabel odb
                $item['onu_mode'] = $zone->onu_mode == 1 ? 'Router' : 'Bridging';
            } else {
                $item['zone'] = null;
                $item['odb'] = 'None';
                $item['onu_mode'] = 'Router';
            }
        }
        // STATUS ONU

        // STATUS ONLINE
        $mapArray7 = array_column($boardInfo['response']['onu_status'], 'zxAnGponSrvOnuLastOnlineTime', 'id');
        foreach ($boardInfo['response']['management_onu'] as &$item) {
            $id = $item['id'];
            $item['zxAnGponSrvOnuLastOnlineTime'] = $mapArray7[$id] ?? null; // Tambahkan data dari array2
        }
        // STATUS ONLINE

        // POWER REDAMAN ONU
        $mapArray3 = array_column($boardInfo['response']['rtx_onu'], 'zxAnPonRxOpticalPower', 'id');
        foreach ($boardInfo['response']['management_onu'] as &$item) {
            $id = $item['id'];
            $item['zxAnPonRxOpticalPower'] = $mapArray3[$id] ?? null; // Tambahkan data dari array2
        }
        // POWER REDAMAN ONU

        // VOIP
        $mapArray5 = array_column($boardInfo['response']['data_voip'], 'zxAnGponRmVoipLineName', 'id');
        foreach ($boardInfo['response']['management_onu'] as &$item) {
            $id = $item['id'];
            $item['zxAnGponRmVoipLineName'] = $mapArray5[$id] ?? null; // Tambahkan data dari array2
        }
        // VOIP

        // DATA ONU
        $mapArray6 = array_column($boardInfo['response']['data_onu'], 'ifName', 'id');
        foreach ($boardInfo['response']['management_onu'] as &$item) {
            $id = preg_replace('/\..*/', '', $item['id']);
            $item['ifName'] =  $namaolt . ' ' . $mapArray6[$id] ?? null; // Tambahkan data dari array2
        }
        // DATA ONU

        // DATA VLAN
        $mapArray10 = array_column($boardInfo['response']['vlan'], 'zxAnVlanIfConfTaggedVlanList', 'id');
        foreach ($boardInfo['response']['management_onu'] as &$item) {
            $id2 = explode('.', $item['id']);
            $new_id = reverseToIdV2($id2[0], $id2[1]);
            $item['zxAnVlanIfConfTaggedVlanList'] = $mapArray10[$new_id] ?? null; // Tambahkan data dari array2
        }
        // DATA VLAN

        // DATA WAN
        $mapArray16 = array_column($boardInfo['response']['wan'], 'zxAnGponRmWanIpMode', 'id');
        foreach ($boardInfo['response']['management_onu'] as &$item) {
            $id = preg_replace('/\.\d+$/', '', $item['id']);
            $item['zxAnGponRmWanIpMode'] = $mapArray16[$id] ?? null; // Tambahkan data dari array2
        }
        // DATA WAN


        $data = $boardInfo['response']['management_onu']; // Mengubah data JSON menjadi array

        // Mengembalikan data dalam format yang sesuai untuk DataTables
        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),  // Bisa disesuaikan jika ada filter
            'data' => $data,
        ]);
    }

    public function show_pon_zte_fiber(Request $request, $id)
    {
        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');

        // Find the OltDevice
        $olt = OltDevice::find($idOlt);
        if (!$olt) {
            $request->session()->flash('error', 'Invalid OLT session');
            return redirect()->to('/' . strtolower($user->role) . '/olt');
        }

        // Mengambil data dari API eksternal
        $user = $request->user();
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $idOlt = $request->session()->get('id_olt');

        $olt = OltDevice::find($idOlt);
        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token ?? '',
            $olt->udp_port ?? '',
            $olt->snmp_read_write ?? '',
            $olt->version ?? '',
            $olt->token ?? ''
        );
        // Instantiate OltService


        // Fetch Board Info
        $onuAllowList = $oltService->getOnuZTEFIBER($id);
        $boardInfo = $oltService->getBoardInfo();
        $zone = OltDeviceZone::where('zone_name', 'Zone 1')->first();
        $id_zone = $zone ? $zone->id : null;


        if ($boardInfo) {
            $onu = OltConfig::where('id_olt', $idOlt)
                ->where('group_id', $user->id_group)
                ->where('id_onu', $id)
                ->first();
            $new_data = $boardInfo['response']['management_onu'];
            // DATA ONU
            $gpon = null;
            $sn = null;
            $vlan = '';
            $mapArray6 = array_column($boardInfo['response']['data_onu'], 'ifName', 'id');
            foreach ($boardInfo['response']['management_onu'] as &$item) {
                $id11 = preg_replace('/\..*/', '', $item['id']);
                $gpon =  $mapArray6[$id11] ?? null; // Tambahkan data dari array2
                $sn =  $item['zxAnGponOnuMgmtSn']; // Tambahkan data dari array2
                break;
            }

             // DATA VLAN
             $mapArray12 = array_column($boardInfo['response']['vlan'], 'zxAnVlanIfConfTaggedVlanList', 'id');
             foreach ($onuAllowList['response']['management_onu'] as &$item) {
                 $id2 = explode('.', $item['id']);
                 $new_id = reverseToIdV2($id2[0], $id2[1]);
                 $vlan = (int) $mapArray12[$new_id] ?? null; // Tambahkan data dari array2
                 break;
             }
             // DATA VLAN
            // DATA ONU

            if (empty($onu)) {
                $data = [
                    'group_id' => $user->id_group,
                    'id_olt' => $idOlt,
                    'id_onu' => $id,
                    'id_zone' => $id_zone,
                    'onu_external_id' => strtoupper($sn),
                    'onu_mode' => 1,
                    'ifName_new' =>  $gpon,
                    'ifName_old' =>  $gpon,
                    'vlan_id' => $vlan
                ];

                OltConfig::create($data);

                $data_history = [
                    'group_id' => $user->id_group,
                    'id_olt' => $idOlt,
                    'id_onu' => $id,
                    'history_desc' => 'Imported from OLT config',
                    'desc_type' => 1
                ];

                OltHistory::create($data_history);
            }
        }




        // Fetch PON data
        // $boardInfo = $oltService->getBoardInfo();

        // Fetch ONU Allow List for the specified port

        $data = [
            'title' => 'OLT PON ' . $id,
            'id' => $id,
            'namaolt' => $olt->name,
        ];
        return view('olt.device.onu_zte_fiber', compact('data'));
    }

    public function getProfileOnu(Request $request, $id)
    {
        // Mengambil data dari API eksternal
        $user = $request->user();
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $idOlt = $request->session()->get('id_olt');

        $olt = OltDevice::find($idOlt);
        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token ?? '',
            $olt->udp_port ?? '',
            $olt->snmp_read_write ?? '',
            $olt->version ?? '',
            $olt->token ?? ''
        );
        // Instantiate OltService


        // Fetch Board Info
        $onuAllowList = $oltService->getOnuZTEFIBER($id);
        $boardInfo = $oltService->getBoardInfo();

        $port = empty($olt->udp_port) ? ' ' : 'port ' . $olt->udp_port;
        if (!array_filter($boardInfo) && !isset($boardInfo['response']['management_onu'])) {
            $request->session()->flash('error', "OLT $olt->name unreachable : SNMP $port timeout");
            return ['success' => false, 'message' => "OLT $olt->name unreachable : SNMP $port timeout"];
        } else {
            // STATUS ONU
            $mapArray2 = array_column($boardInfo['response']['onu_status'], 'zxAnGponSrvOnuPhaseStatus', 'id');
            foreach ($onuAllowList['response']['management_onu'] as &$item) {
                $id = $item['id'];
                $item['zxAnGponSrvOnuPhaseStatus'] = $mapArray2[$id] ?? null; // Tambahkan data dari array2
            }
            // STATUS ONU

            // STATUS ONLINE
            $mapArray7 = array_column($boardInfo['response']['onu_status'], 'zxAnGponSrvOnuLastOnlineTime', 'id');
            foreach ($onuAllowList['response']['management_onu'] as &$item) {
                $id = $item['id'];
                $item['zxAnGponSrvOnuLastOnlineTime'] = $mapArray7[$id] ?? null; // Tambahkan data dari array2
            }
            // STATUS ONLINE

            // POWER REDAMAN ONU
            $mapArray3 = array_column($boardInfo['response']['rtx_onu'], 'zxAnPonRxOpticalPower', 'id');
            foreach ($onuAllowList['response']['management_onu'] as &$item) {
                $id = $item['id'];
                $item['zxAnPonRxOpticalPower'] = $mapArray3[$id] ?? null; // Tambahkan data dari array2
            }
            // POWER REDAMAN ONU

            // VOIP
            $mapArray5 = array_column($boardInfo['response']['data_voip'], 'zxAnGponRmVoipLineName', 'id');
            foreach ($onuAllowList['response']['management_onu'] as &$item) {
                $id = $item['id'];
                $item['zxAnGponRmVoipLineName'] = $mapArray5[$id] ?? null; // Tambahkan data dari array2
            }
            // VOIP

            // DATA ONU
            $mapArray6 = array_column($boardInfo['response']['data_onu'], 'ifName', 'id');
            foreach ($onuAllowList['response']['management_onu'] as &$item) {
                $id = preg_replace('/\..*/', '', $item['id']);
                $item['ifName'] =  $mapArray6[$id] ?? null; // Tambahkan data dari array2
            }
            // DATA ONU

            // LOCAL SERVER
            foreach ($onuAllowList['response']['management_onu'] as &$item) {
                $id = $item['id'];

                $zone = OltConfig::where('id_olt', $idOlt)
                    ->where('group_id', $user->id_group)
                    ->where('id_onu', $id)
                    ->first();

                $item['id_zone'] = $zone->id_zone ?? null;
                $item['address'] = $zone->address ?? 'None';
                $item['contact'] = $zone->contact ?? 'None';
                $item['zone_name'] = $zone->zone_olt->zone_name ?? 'None';

                $item['id_odb'] = $zone->id_odb ?? null;
                $item['odb_name'] = $zone->odb->odb_name ?? 'None';
                $item['external_id'] = $zone->onu_external_id;
                $item['onu_mode'] = $zone->onu_mode == 1 ? 'Routing' : 'Bridging';
                $item['id_onu_mode'] = $zone->onu_mode;
            }
            //LOCAL SERVER

            // LENGTH WAVE
            $mapArray11 = array_column($boardInfo['response']['wave'], 'zxAnGponSrvOnuFiberLen', 'id');
            foreach ($onuAllowList['response']['management_onu'] as &$item) {
                $id = $item['id'];
                $item['zxAnGponSrvOnuFiberLen'] = $mapArray11[$id] ?? null; // Tambahkan data dari array2
            }
            // LENGTH WAVE

            // DATA VLAN
            $mapArray12 = array_column($boardInfo['response']['vlan'], 'zxAnVlanIfConfTaggedVlanList', 'id');
            foreach ($onuAllowList['response']['management_onu'] as &$item) {
                $id2 = explode('.', $item['id']);
                $new_id = reverseToIdV2($id2[0], $id2[1]);
                $item['zxAnVlanIfConfTaggedVlanList'] = $mapArray12[$new_id] ?? null; // Tambahkan data dari array2
            }
            // DATA VLAN

            // DATA WAN
            $mapArray16 = array_column($boardInfo['response']['wan'], 'zxAnGponRmWanIpMode', 'id');
            foreach ($onuAllowList['response']['management_onu'] as &$item) {
                $itemId = $item['id']; // "285278721.3"

                // Cari langsung di WAN yang cocok
                foreach ($boardInfo['response']['wan'] as $wan) {
                    $wanId = preg_replace('/\.\d+$/', '', $wan['id']); // "285278721.3"

                    if ($wanId === $itemId) {
                        $item['zxAnGponRmWanIpMode'] = $wan['zxAnGponRmWanIpMode'];
                        $item['zxAnGponRmWanPppoeUsername'] = $wan['zxAnGponRmWanPppoeUsername'];
                        $item['zxAnGponRmWanPppoePassword'] = $wan['zxAnGponRmWanPppoePassword'];
                        $item['zxAnGponRmWanVlanPrf'] = $wan['zxAnGponRmWanVlanPrf'];
                        $item['zxAnGponRmWanRspTraceRoute'] = $wan['zxAnGponRmWanRspTraceRoute'];
                        break; // Stop looping kalau sudah ketemu
                    }
                }

                // Optional: kalau gak ketemu, bisa set null biar aman
                if (!isset($item['zxAnGponRmWanIpMode'])) {
                    $item['zxAnGponRmWanIpMode'] = null;
                }
            }
            // DATA WAN

            $data = $onuAllowList['response']['management_onu']; // Mengubah data JSON menjadi array
            return $data;
        }



        // Mengembalikan data dalam format yang sesuai untuk DataTables
    }

    function getUnconfigOnu(Request $request)
    {
        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $url = "http://103.184.122.170/api/snmp/zxAnGponSrvUnConfOnuTable";

        // Inisialisasi cURL
        $ch = curl_init();

        // Set opsi cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",  // Set header token
            "Accept: application/json"
        ]);

        // Eksekusi request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Cek status code

        // Cek jika terjadi error
        if (curl_errno($ch)) {
            return "cURL Error: " . curl_error($ch);
        }

        curl_close($ch);

        // Jika status code bukan 200, kembalikan pesan error
        if ($httpCode !== 200) {
            return "Error: HTTP Status $httpCode - " . $response;
        }

        // Konversi hasil JSON ke array PHP
        return json_decode($response, true);
    }

    function updateonu(Request $request)
    {
        $id_olt = $request->input('id_olt');
        $id_tindakan = (int) $request->input('id_tindakan');
        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        if ($id_tindakan === 3) {
            $sn_olt = !empty($request->input('sn_olt')) ? $request->input('sn_olt') : '';
            $type_onu = !empty($request->input('type_onu')) ? $request->input('type_onu') : '';

            $data = [
                'zxAnGponOnuMgmtTypeName' => $type_onu,
                'zxAnGponOnuMgmtSn' => $sn_olt,
            ];

            // Token yang digunakan untuk otentikasi
            $token = $request->session()->get('token');

            // Periksa apakah token tersedia
            if (!$token) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token not found',
                ], 401); // Unauthorized
            }

            // URL API eksternal
            $url = "http://103.184.122.170/api/snmp/zxAnSrvPortConfTable/" . urlencode($id_olt);

            // Inisialisasi cURL
            $ch = curl_init();
            $headers = [
                "Authorization: Bearer $token",  // Set header token
                "Content-Type: application/json"
            ];

            // Set opsi cURL
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Mengirim data dalam format JSON
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            // Eksekusi request
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Cek status code

            // Cek jika terjadi error
            if (curl_errno($ch)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'cURL Error: ' . curl_error($ch),
                    'http_code' => $httpCode
                ], $httpCode); // Internal Server Error
            }

            curl_close($ch);

            // Log HTTP Response
            // Jika status code bukan 200, kembalikan pesan error
            if ($httpCode !== 200) {

                return response()->json([
                    'status' => 'error',
                    'message' => "HTTP Status $httpCode - $response - $url - $id_olt",
                ], $httpCode);
            }

            // Konversi hasil JSON ke array PHP

            $data_history = [
                'group_id' => $user->id_group,
                'id_olt' => $idOlt,
                'id_onu' => $id_olt,
                'history_desc' => 'Update Replace ONU by SN',
                'desc_type' => 3
            ];

            $das = OltHistory::create($data_history);
        }

        if ($id_tindakan === 4) {
            $name_olt = !empty($request->input('name_onu')) ? $request->input('name_onu') : '';
            $id_zone = !empty($request->input('id_zone')) ? $request->input('id_zone') : null;
            $id_odb = !empty($request->input('id_odb')) ? $request->input('id_odb') : null;
            $address_onu = !empty($request->input('address_onu')) ? $request->input('address_onu') : '';
            $contact_onu = !empty($request->input('contact_onu')) ? $request->input('contact_onu') : '';

            $data = [
                'zxAnGponOnuMgmtName' => $name_olt,
            ];

            $olt = OltConfig::where('id_olt', $idOlt)
                ->where('group_id', $user->id_group)
                ->where('id_onu', $id_olt)
                ->first();

            if (!empty($olt)) {
                $olt->id_zone = $id_zone; // Ubah nilai field 
                $olt->id_odb = $id_odb; // Ubah nilai field
                $olt->address = $address_onu;
                $olt->contact = $contact_onu;
                $olt->save(); // Simpan perubahan
            }

            $data_history = [
                'group_id' => $user->id_group,
                'id_olt' => $idOlt,
                'id_onu' => $id_olt,
                'history_desc' => 'Update Location Details',
                'desc_type' => 4
            ];

            $das = OltHistory::create($data_history);
        }

        if ($id_tindakan === 5) {
            $onu_external = !empty($request->input('onu_external')) ? $request->input('onu_external') : '';

            $olt = OltConfig::where('id_olt', $idOlt)
                ->where('group_id', $user->id_group)
                ->where('id_onu', $id_olt)
                ->first();

            if (!empty($olt)) {
                $olt->onu_external_id = $onu_external; // Ubah nilai field 
                $olt->save(); // Simpan perubahan
            }

            $data_history = [
                'group_id' => $user->id_group,
                'id_olt' => $idOlt,
                'id_onu' => $id_olt,
                'history_desc' => 'Update Onu External ID',
                'desc_type' => 5
            ];

            $das = OltHistory::create($data_history);
        }

        if ($id_tindakan === 6) {
            $vlan_id = !empty($request->input('vlan_id')) ? (int) $request->input('vlan_id') : '';

            $partss = explode('.', $id_olt);
            $id_srvport = reverseToId($partss[0], $partss[1]);

            // HAPUS DULU
            $data_vport_service_hapus = [
                "zxAnSrvPortRowStatus" => 6
            ];
            // HAPUS DULU

            $data_vport_service = [
                "zxAnSrvPortUserVid" => $vlan_id,
                "zxAnSrvPortCVid" => $vlan_id,
                "zxAnSrvPortBrgIfId" => 1,
                "zxAnSrvPortDesc" => "",
                "zxAnSrvPortAdminStatus" => 1,
                "zxAnSrvPortCtagCos" => 255,
                "zxAnSrvPortSVid" => 0,
                "zxAnSrvPortStagCos" => 255,
                "zxAnSrvPortQueueId" => 255,
                "zxAnSrvPortRowStatus" => 4
            ];

            // URL API eksternal
            $headers = [
                "Authorization: Bearer $token",  // Set header token
                "Content-Type: application/json"
            ];


            $url = "http://103.184.122.170/api/snmp/zxAnSrvPortConfTable/" . urlencode($id_srvport);

            /// HAPUS DULU
            $this->http_post($url, $headers, $data_vport_service_hapus);
            /// HAPUS DULU

            // INSERT BARU
            $this->http_post($url, $headers, $data_vport_service);
            // INSERT BARU



            $data_history = [
                'group_id' => $user->id_group,
                'id_olt' => $idOlt,
                'id_onu' => $id_olt,
                'history_desc' => 'Update VLANs',
                'desc_type' => 6
            ];

            $das = OltHistory::create($data_history);
        }

        if ($id_tindakan === 7) {

            // Instantiate OltService
            $vlan_profile = $request->input('profile_vlan');
            $wan_mode = (int) $request->input('wan_mode');
            $username = $request->input('username');
            $password = $request->input('password');
            $ip_addr = $request->input('ip_addr');
            $subnet_mask = $request->input('subnet_mask');
            $default_gateway = $request->input('default_gateway');
            $dns1 = $request->input('dns1') ?? '';
            $dns2 = $request->input('dns2') ?? '';
            $wan_remote = (int) $request->input('wan_remote');
            $data = [
                'zxAnGponRmWanVlanPrf' => $vlan_profile,
                "zxAnGponRmWanIpConfRowStatus" => 4,
                "zxAnGponRmWanRspPing" => $wan_remote,
                "zxAnGponRmWanRspTraceRoute" => $wan_remote,
                'zxAnGponRmWanIpMode' => $wan_mode
            ];
            if ($wan_mode == 1) {
                $data['zxAnGponRmWanIpAddrType'] = 1;
                $data['zxAnGponRmWanCurrGatewayType'] = 1;
                $data['zxAnGponRmWanCurrPriDnsType'] = empty($dns1) ? 0 : 1;
                $data['zxAnGponRmWanCurrSecDnsType'] = empty($dns1) ? 0 : 1;
                $data['zxAnGponRmWanIpAddr'] = strtoupper(bin2hex(inet_pton($ip_addr)));
                $data['zxAnGponRmWanIpAddrPfxLen'] = (int) subnetMaskToPrefix($subnet_mask);
                $data['zxAnGponRmWanCurrGateway'] = strtoupper(bin2hex(inet_pton($default_gateway)));
                $data['zxAnGponRmWanCurrPriDns'] = empty($dns1) ? '00000000' : strtoupper(bin2hex(inet_pton($dns1)));
                $data['zxAnGponRmWanCurrSecDns'] = empty($dns2) ? '00000000' : strtoupper(bin2hex(inet_pton($dns2)));
            } else if ($wan_mode == 2) {
                $data['zxAnGponRmWanIpAddrType'] = 1;
            } else {
                $data["zxAnGponRmWanPppoeAuthMode"] = 1;
                $data["zxAnGponRmWanPppoeUsername"] = $username;
                $data["zxAnGponRmWanPppoePassword"] = $password;
                $data["zxAnGponRmWanPppoeServiceName"] = "";
            }

            // Token yang digunakan untuk otentikasi
            $token = $request->session()->get('token');

            // Periksa apakah token tersedia
            if (!$token) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token not found',
                ], 401); // Unauthorized
            }

            $olt = OltDevice::find($idOlt);
            $oltService = new OltService(
                $olt->model,
                $olt->host,
                $olt->username,
                $olt->password,
                $olt->token ?? '',
                $olt->udp_port ?? '',
                $olt->snmp_read_write ?? '',
                $olt->version ?? '',
                $olt->token ?? ''
            );

            $id_tconf = "";
            $id_onu_tconf = "";
            $id_baru = preg_match('/^\d+/', $id_olt, $matches);
            $result1 = $matches[0]; // Hasilnya: "285278721"
            $tconf = $oltService->getTConfType();
            $no = 0;
            if (!empty($tconf)) {
                foreach ($tconf as $da) {
                    $idd = explode('.', $da['id']);
                    $result = $idd[0]; // Hasilnya: "285278721.1
                    if ($result == $result1) {
                        $id_onu_tconf = $da['id'];
                        break;
                    }
                }
            }
            $partsss = explode('.', $id_onu_tconf);
            $new_int_tconf = end($partsss); // Hasilnya: "1"
            $id_tconf = $id_olt . '.' . $new_int_tconf;
            //  CEK DULU BUAT AMBIL NILAI BELAKANNGYA

            // URL API eksternal
            $url = "http://103.184.122.170/api/snmp/zxAnGponRmWanIpConfTable/" . urlencode($id_tconf);

            // Inisialisasi cURL
            $ch = curl_init();
            $headers = [
                "Authorization: Bearer $token",  // Set header token
                "Content-Type: application/json"
            ];

            // Set opsi cURL
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Mengirim data dalam format JSON
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            // Eksekusi request
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Cek status code

            // Cek jika terjadi error
            if (curl_errno($ch)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'cURL Error: ' . curl_error($ch),
                    'http_code' => $httpCode
                ], $httpCode); // Internal Server Error
            }

            curl_close($ch);

            // Log HTTP Response
            // Jika status code bukan 200, kembalikan pesan error
            if ($httpCode !== 200) {

                return response()->json([
                    'status' => 'error',
                    'message' => "HTTP Status $httpCode - $response - $url - $id_olt",
                    'data' => $data
                ], $httpCode);
            }

            // Konversi hasil JSON ke array PHP

            $data_history = [
                'group_id' => $user->id_group,
                'id_olt' => $idOlt,
                'id_onu' => $id_olt,
                'history_desc' => 'Update Onu Mode',
                'desc_type' => 7
            ];

            $das = OltHistory::create($data_history);
        }

        // Data yang akan dikirim

        return json_decode($user, true);

        // Log Data yang akan dikirim ke API


    }

    function updatespeed(Request $request)
    {
        $id_srvport = $request->input('id_srvport');
        $id_gemport = $request->input('id_gemport');
        $id_olt = $request->input('id_olt');
        $download_speed = $request->input('download_speed');
        $upload_speed = $request->input('upload_speed');
        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');

        // UPDATE BANDWIDTH DULU
        $partsss = explode('.', $id_srvport);
        $new_int_tconf = end($partsss); // Hasilnya: "1"
        $dataB = [
            // "zxAnGponSrvTcontName" =>  "tconf" . $new_int_tconf,
            "zxAnGponSrvTcontBwPrfName" => $download_speed,
            // "zxAnGponSrvTcontRowStatus" =>  4,
        ];

        // $dataB1 = [
        //     "zxAnGponSrvTcontRowStatus" => 6
        // ];

        $part_gemp = explode('.', $id_gemport);
        $new_int_gem = end($part_gemp); // Hasilnya: "1"

        $dataT = [
            // "zxAnGponSrvGemPortName" => "gemport" . $new_int_gem,
            // "zxAnGponSrvGemPortTcontIndex" => (int) 1,
            // "zxAnGponSrvGemPortQueueOfTcont" => (int) 0,
            // "zxAnGponSrvGemPortEnable" => (int) 1,
            "zxAnGponSrvGemPortUsTrafficPrf" => $upload_speed,
            "zxAnGponSrvGemPortDsTrafficPrf" => $upload_speed,
            // "zxAnGponSrvGemPortEncrypt" => (int) 2,
            // "zxAnGponSrvGemPortRowStatus" => (int) 4
        ];

        $dataT1 = [
            "zxAnGponSrvGemPortRowStatus" => (int) 6
        ];

        // Token yang digunakan untuk otentikasi
        $token = $request->session()->get('token');

        // Periksa apakah token tersedia
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token not found',
            ], 401); // Unauthorized
        }

        // URL API eksternal
        $url = "http://103.184.122.170/api/snmp/zxAnGponSrvTcontTable/" . urlencode($id_srvport);
        $url1 = "http://103.184.122.170/api/snmp/zxAnGponSrvGemPortTable/" . urlencode($id_gemport);

        // Inisialisasi cURL
        $headers = [
            "Authorization: Bearer $token",  // Set header token
            "Content-Type: application/json"
        ];

        $partss = explode('.', $id_olt);
        $id_srvport1 = reverseToId($partss[0], $partss[1]);

        $olt = OltConfig::where('id_olt',$idOlt)
                ->where('group_id',$user->id_group)
                ->where('id_onu',$id_olt)
                ->first();
        $vlan_id = $olt->vlan_id ?? '';

        // $data_vport_service = [
        //     "zxAnSrvPortServiceMode" => 4,
        //     "zxAnSrvPortUserVid" => $vlan_id,
        //     "zxAnSrvPortCVid" => $vlan_id,
        //     "zxAnSrvPortBrgIfId" => 1,
        //     "zxAnSrvPortDesc" => "",
        //     "zxAnSrvPortAdminStatus" => 1,
        //     "zxAnSrvPortCtagCos" => 255,
        //     "zxAnSrvPortSVid" => 0,
        //     "zxAnSrvPortStagCos" => 255,
        //     "zxAnSrvPortQueueId" => 255,
        //     "zxAnSrvPortRowStatus" => 4
        // ];

        $url_service = "http://103.184.122.170/api/snmp/zxAnSrvPortConfTable/" . urlencode($id_srvport1);

        // $srv_del = $this->http_post($url, $headers, $dataB1);
        // $gem_del = $this->http_post($url1, $headers, $dataT1);

        $srv_add = json_decode($this->http_post($url, $headers, $dataB),true);
        $gem_add = json_decode($this->http_post($url1, $headers, $dataT),true);
        // $add_srv = json_decode($this->http_post($url_service,$headers,$data_vport_service),true);

        // $id_olt = preg_replace('/\.[^.]*$/', '', $id_srvport);




        $data_history = [
            'group_id' => $user->id_group,
            'id_olt' => $idOlt,
            'id_onu' => $id_olt,
            'history_desc' => 'Update Speed Profile',
            'desc_type' => 8
        ];

        $das = OltHistory::create($data_history);

        return $srv_add;



        // UPDATE BANDWIDTH DULU
    }

    public function setting_zone(Request $request)
    {
        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');

        // Find the OltDevice
        $olt = OltDevice::find($idOlt);
        if (!$olt) {
            $request->session()->flash('error', 'Invalid OLT session');
            return redirect()->to('/' . strtolower($user->role) . '/olt');
        }


        $data = [
            'title' => 'Setting Zone ',
            'namaolt' => $olt->name,
        ];
        return view('olt.device.setting_zone', compact('data'));
    }

    public function zone_list(Request $request)
    {
        // Mengambil data dari sesi & request
        $user = $request->user();
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $idOlt = $request->session()->get('id_olt');

        // Ambil data zona dari database
        $olt = OltDeviceZone::where('id_olt', $idOlt)
            ->where('group_id', $user->id_group)
            ->get();

        // Cek jika tidak ada data
        if ($olt->isEmpty()) {
            return response()->json([
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'message' => 'No zones found',
            ]);
        }

        // Kembalikan data dalam format DataTables
        return response()->json([
            'draw' => intval($request->input('draw', 1)),
            'recordsTotal' => $olt->count(),
            'recordsFiltered' => $olt->count(),
            'data' => $olt,
        ]);
    }

    function updatezone(Request $request)
    {
        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $igGroup = $user->id_group;
        $zoneName = $request->input('zoneName');
        $data = [
            'group_id' => $igGroup,
            'id_olt' => $idOlt,
            'zone_name' => $zoneName
        ];

        // Data yang akan dikirim


        // Log Data yang akan dikirim ke API

        // Token yang digunakan untuk otentikasi
        $token = $request->session()->get('token');

        // Periksa apakah token tersedia
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token not found',
            ], 401); // Unauthorized
        }

        try {
            OltDeviceZone::create([
                'group_id' => $igGroup,
                'id_olt' => $idOlt,
                'zone_name' => $zoneName
            ]);
            return response()->json(['message' => 'Zone created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating Zone: ' . $e], 500);
        }
    }

    function delete_zone(Request $request, $id)
    {
        $user = $request->user();
        $token = $request->session()->get('token');

        $data = OltDeviceZone::find($id);

        if (!$data) {
            return response()->json(['status' => 'error', 'message' => 'Data not found'], 404);
        }

        $data->delete();

        return response()->json(['status' => 'success', 'message' => 'Data deleted successfully']);
    }

    public function setting_odb(Request $request)
    {
        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');

        // Find the OltDevice
        $olt = OltDevice::find($idOlt);
        if (!$olt) {
            $request->session()->flash('error', 'Invalid OLT session');
            return redirect()->to('/' . strtolower($user->role) . '/olt');
        }


        $data = [
            'title' => 'Setting ODBs ',
            'namaolt' => $olt->name,
        ];
        return view('olt.device.setting_odb', compact('data'));
    }

    public function odb_list(Request $request)
    {
        // Mengambil data dari sesi & request
        $user = $request->user();
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $idOlt = $request->session()->get('id_olt');

        // Ambil data zona dari database


        $olt = OltDeviceOdb::with('zone_olt')->where('id_olt', $idOlt)->where('group_id', $user->id_group)->get();


        // Cek jika tidak ada data
        if ($olt->isEmpty()) {
            return response()->json([
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'message' => 'No zones found',
            ]);
        }

        // Kembalikan data dalam format DataTables
        return response()->json([
            'draw' => intval($request->input('draw', 1)),
            'recordsTotal' => $olt->count(),
            'recordsFiltered' => $olt->count(),
            'data' => $olt,
        ]);
    }

    function getZone(Request $request)
    {
        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $olt = OltDeviceZone::where('id_olt', $idOlt)
            ->where('group_id', $user->id_group)
            ->get();
        // Konversi hasil JSON ke array PHP
        return json_decode($olt, true);
    }

    function updateodb(Request $request)
    {
        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $igGroup = $user->id_group;
        $zoneName = $request->input('zoneName');
        $id_zone = $request->input('id_zone');
        $port = $request->input('port');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $data = [
            'group_id' => $igGroup,
            'id_olt' => $idOlt,
            'id_zone' => $id_zone,
            'odb_name' => $zoneName,
            'port' => $port,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];

        // Data yang akan dikirim


        // Log Data yang akan dikirim ke API

        // Token yang digunakan untuk otentikasi
        $token = $request->session()->get('token');

        // Periksa apakah token tersedia
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token not found',
            ], 401); // Unauthorized
        }

        try {
            OltDeviceOdb::create($data);
            return response()->json(['message' => 'ODBs created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating ODBs: ' . $e], 500);
        }
    }

    function delete_odb(Request $request, $id)
    {
        $user = $request->user();
        $token = $request->session()->get('token');

        $data = OltDeviceOdb::find($id);

        if (!$data) {
            return response()->json(['status' => 'error', 'message' => 'Data not found'], 404);
        }

        $data->delete();

        return response()->json(['status' => 'success', 'message' => 'Data deleted successfully']);
    }

    public function setting_vlan(Request $request)
    {
        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');

        // Find the OltDevice
        $olt = OltDevice::find($idOlt);
        if (!$olt) {
            $request->session()->flash('error', 'Invalid OLT session');
            return redirect()->to('/' . strtolower($user->role) . '/olt');
        }


        $data = [
            'title' => 'Setting VLAN ',
            'namaolt' => $olt->name,
        ];
        return view('olt.device.setting_vlan', compact('data'));
    }

    function vlan_list(Request $request)
    {
        // Mengambil data dari API eksternal
        $user = $request->user();
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $idOlt = $request->session()->get('id_olt');

        $olt = OltDevice::find($idOlt);
        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token ?? '',
            $olt->udp_port ?? '',
            $olt->snmp_read_write ?? '',
            $olt->version ?? ''
        );
        // Instantiate OltService


        // Fetch Board Info
        $boardInfo = $oltService->getVLANInfo();

        // Mengembalikan data dalam format yang sesuai untuk DataTables
        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => count($boardInfo),
            'recordsFiltered' => count($boardInfo),  // Bisa disesuaikan jika ada filter
            'data' => $boardInfo,
        ]);
    }

    function updatevlan(Request $request)
    {

        // Mengambil data dari request
        $vlan_id = (int) $request->input('vlan_id');
        $vlan_name = $request->input('vlan_name');
        $vlan_description = empty($request->input('vlan_description')) ? '' : $request->input('vlan_description');

        // Data yang akan dikirim
        $data = [
            "zxAnVlanName" => $vlan_name,
            "zxAnVlanDesc" => $vlan_description,
            "zxAnVlanRowStatus" => (int) 4
        ];

        //CEK VLAN DULU

        // CEK VLAN DULU

        // Log Data yang akan dikirim ke API

        // Token yang digunakan untuk otentikasi
        $token = $request->session()->get('token');

        // Periksa apakah token tersedia
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token not found',
            ], 401); // Unauthorized
        }

        // URL API eksternal
        $url = "http://103.184.122.170/api/snmp/zxAnVlanTable/" . urlencode($vlan_id);

        // Inisialisasi cURL
        $ch = curl_init();
        $headers = [
            "Authorization: Bearer $token",  // Set header token
            "Content-Type: application/json"
        ];

        // Set opsi cURL
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Mengirim data dalam format JSON
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        // Eksekusi request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Cek status code

        // Cek jika terjadi error
        if (curl_errno($ch)) {
            return response()->json([
                'status' => 'error',
                'message' => 'cURL Error: ' . curl_error($ch),
                'http_code' => $httpCode
            ], $httpCode); // Internal Server Error
        }

        curl_close($ch);

        // Log HTTP Response
        // Jika status code bukan 200, kembalikan pesan error
        if ($httpCode !== 200) {

            return response()->json([
                'status' => 'error',
                'message' => "HTTP Status $httpCode - $response",
            ], $httpCode);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => "Success",
            ], $httpCode);
        }
    }

    function delete_vlan(Request $request, $id)
    {
        // Data yang akan dikirim
        $data = [
            "zxAnVlanRowStatus" => (int) 6
        ];

        // Log Data yang akan dikirim ke API

        // Token yang digunakan untuk otentikasi
        $token = $request->session()->get('token');

        // Periksa apakah token tersedia
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token not found',
            ], 401); // Unauthorized
        }

        // URL API eksternal
        $url = "http://103.184.122.170/api/snmp/zxAnVlanTable/" . urlencode($id);

        // Inisialisasi cURL
        $ch = curl_init();
        $headers = [
            "Authorization: Bearer $token",  // Set header token
            "Content-Type: application/json"
        ];

        // Set opsi cURL
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Mengirim data dalam format JSON
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        // Eksekusi request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Cek status code

        // Cek jika terjadi error
        if (curl_errno($ch)) {
            return response()->json([
                'status' => 'error',
                'message' => 'cURL Error: ' . curl_error($ch),
                'http_code' => $httpCode
            ], $httpCode); // Internal Server Error
        }

        curl_close($ch);

        // Log HTTP Response
        // Jika status code bukan 200, kembalikan pesan error
        if ($httpCode !== 200) {

            return response()->json([
                'status' => 'error',
                'message' => "HTTP Status $httpCode - $response",
            ], $httpCode);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => "Success",
            ], $httpCode);
        }
    }

    public function setting_port_vlan(Request $request)
    {
        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');

        // Find the OltDevice
        $olt = OltDevice::find($idOlt);
        if (!$olt) {
            $request->session()->flash('error', 'Invalid OLT session');
            return redirect()->to('/' . strtolower($user->role) . '/olt');
        }


        $data = [
            'title' => 'Setting VLAN PORT',
            'namaolt' => $olt->name,
        ];
        return view('olt.device.setting_vlan_port', compact('data'));
    }

    function vlan_port_list(Request $request)
    {
        // Mengambil data dari API eksternal
        $user = $request->user();
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $idOlt = $request->session()->get('id_olt');

        $olt = OltDevice::find($idOlt);
        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token ?? '',
            $olt->udp_port ?? '',
            $olt->snmp_read_write ?? '',
            $olt->version ?? ''
        );
        // Instantiate OltService


        // Fetch Board Info
        $boardInfo = $oltService->getVLANPortInfo();

        // Mengembalikan data dalam format yang sesuai untuk DataTables
        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => count($boardInfo),
            'recordsFiltered' => count($boardInfo),  // Bisa disesuaikan jika ada filter
            'data' => $boardInfo,
        ]);
    }

    function updateportvlan(Request $request)
    {

        // Mengambil data dari request
        $vlan_id = $request->input('vlan_id');
        $vlan_name = $request->input('vlan_type');
        $id_vlan = $request->input('id_vlan');

        // Data yang akan dikirim
        $data = [
            "zxAnVlanIfConfVlanCmd" => (int) $vlan_name,
            "zxAnVlanIfConfVlanList" => $id_vlan,
        ];

        // Log Data yang akan dikirim ke API

        // Token yang digunakan untuk otentikasi
        $token = $request->session()->get('token');

        // Periksa apakah token tersedia
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token not found',
            ], 401); // Unauthorized
        }

        // URL API eksternal
        $url = "http://103.184.122.170/api/snmp/zxAnVlanIfConfTable/" . urlencode($vlan_id);

        // Inisialisasi cURL
        $ch = curl_init();
        $headers = [
            "Authorization: Bearer $token",  // Set header token
            "Content-Type: application/json"
        ];

        // Set opsi cURL
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Mengirim data dalam format JSON
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        // Eksekusi request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Cek status code

        // Cek jika terjadi error
        if (curl_errno($ch)) {
            return response()->json([
                'status' => 'error',
                'message' => 'cURL Error: ' . curl_error($ch),
                'http_code' => $httpCode
            ], $httpCode); // Internal Server Error
        }

        curl_close($ch);

        // Log HTTP Response
        // Jika status code bukan 200, kembalikan pesan error
        if ($httpCode !== 200) {

            return response()->json([
                'status' => 'error',
                'message' => "HTTP Status $httpCode - $response",
            ], $httpCode);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => "Success",
            ], $httpCode);
        }
    }

    public function setting_bandwidth(Request $request)
    {
        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');

        // Find the OltDevice
        $olt = OltDevice::find($idOlt);
        if (!$olt) {
            $request->session()->flash('error', 'Invalid OLT session');
            return redirect()->to('/' . strtolower($user->role) . '/olt');
        }


        $data = [
            'title' => 'Setting Bandwidth Onu',
            'namaolt' => $olt->name,
        ];
        return view('olt.device.setting_bandwidth', compact('data'));
    }

    public function setting_onu_type(Request $request)
    {
        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');

        // Find the OltDevice
        $olt = OltDevice::find($idOlt);
        if (!$olt) {
            $request->session()->flash('error', 'Invalid OLT session');
            return redirect()->to('/' . strtolower($user->role) . '/olt');
        }


        $data = [
            'title' => 'Setting Onu Type',
            'namaolt' => $olt->name,
        ];
        return view('olt.device.setting_onu_type', compact('data'));
    }

    function onu_type_list(Request $request)
    {
        // Mengambil data dari API eksternal
        $user = $request->user();
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $idOlt = $request->session()->get('id_olt');

        $olt = OltDevice::find($idOlt);
        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token ?? '',
            $olt->udp_port ?? '',
            $olt->snmp_read_write ?? '',
            $olt->version ?? ''
        );
        // Instantiate OltService


        // Fetch Board Info
        $boardInfo = $oltService->getOnuType();

        // Mengembalikan data dalam format yang sesuai untuk DataTables
        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => count($boardInfo),
            'recordsFiltered' => count($boardInfo),  // Bisa disesuaikan jika ada filter
            'data' => $boardInfo,
        ]);
    }

    function updatetypepon(Request $request)
    {

        // Mengambil data dari request
        $id = $request->input('id');
        $name = $request->input('onu_name');
        $onu_type = (int) $request->input('onu_type');
        $onu_desc = empty($request->input('onu_desc')) ? '' : $request->input('onu_desc');

        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $index = '';

        // ini untuk konversi name ke id dulu
        $url_konversi = "http://103.184.122.170/api/snmp/converter";

        $ch_konversi = curl_init();
        $headers_konversi = [
            "Authorization: Bearer $token",  // Set header token
            "Content-Type: application/json"
        ];

        $data_konversi = [
            'index' => $name
        ];

        // Set opsi cURL
        curl_setopt($ch_konversi, CURLOPT_HTTPHEADER, $headers_konversi);
        curl_setopt($ch_konversi, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch_konversi, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_konversi, CURLOPT_POSTFIELDS, json_encode($data_konversi)); // Mengirim data dalam format JSON
        curl_setopt($ch_konversi, CURLOPT_URL, $url_konversi);
        curl_setopt($ch_konversi, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch_konversi, CURLOPT_SSL_VERIFYPEER, 0);

        // Eksekusi request
        $response = curl_exec($ch_konversi);
        $httpCode = curl_getinfo($ch_konversi, CURLINFO_HTTP_CODE); // Cek status code

        // Cek jika terjadi error
        if (curl_errno($ch_konversi)) {
            return response()->json([
                'status' => 'error',
                'message' => 'cURL Error Konversi: ' . curl_error($ch_konversi),
                'http_code' => $httpCode
            ], $httpCode); // Internal Server Error
        }

        curl_close($ch_konversi);

        // Log HTTP Response
        // Jika status code bukan 200, kembalikan pesan error
        if ($httpCode !== 200) {

            return response()->json([
                'status' => 'error',
                'message' => "HTTP Status Konversi $httpCode - $response",
            ], $httpCode);
        } else {
            $index_new = json_decode($response, true);

            // INI BARU CEK DATA YANG SAMA
            $olt = OltDevice::find($idOlt);
            $oltService = new OltService(
                $olt->model,
                $olt->host,
                $olt->username,
                $olt->password,
                $olt->token ?? '',
                $olt->udp_port ?? '',
                $olt->snmp_read_write ?? '',
                $olt->version ?? ''
            );
            // Instantiate OltService

            $boardInfo = $oltService->getOnuType();

            if (!empty($boardInfo)) {
                foreach ($boardInfo as $da) {
                    $parts = explode('.', $da['id']); // Pisahkan berdasarkan titik
                    array_shift($parts); // Hapus elemen pertama
                    $parts = implode('.', $parts); // Gabungkan kembali tanpa elemen pertama
                    $parts1 = explode('.', $index_new['result']); // Pisahkan berdasarkan titik
                    array_shift($parts1); // Hapus elemen pertama
                    $parts1 = implode('.', $parts1);

                    if ($parts === $parts1) {
                        return response()->json([
                            'status' => 'error',
                            'message' => "Terdapat ID yang memiliki Profile Yang Sama",
                        ], 301);
                    }
                }
            }

            $data = [
                // "zxAnPonOnuTypeName" => $name,
                "zxAnPonOnuTypePonType" => (int) $onu_type,
                "zxAnPonOnuTypeDesc" => $onu_desc,
                "zxAnPonOnuTypeAttr1" => 255,
                "zxAnPonOnuTypeAttr2" => 255,
                "zxAnPonOnuTypeAttr3" => 255,
                "zxAnPonOnuTypeAttr4" => 255,
                "zxAnPonOnuTypeAttr5" => 2,
                "zxAnPonOnuTypeAttr6" => 7,
                "zxAnPonOnuTypeAttr9" => 0,
                "zxAnPonOnuTypeAttr8" => 0,
                "zxAnPonOnuTypeRowStatus" => (int) 4
            ];

            // Log Data yang akan dikirim ke API
            $arrayIndex = explode('.', $index_new['result']);
            $lengIndex = count($arrayIndex);
            $new_id = $index_new['result'];
            // Pisahkan berdasarkan titik

            // URL API eksternal
            $url = "http://103.184.122.170/api/snmp/zxAnPonOnuTypeTable/" . urlencode($new_id);

            // Inisialisasi cURL
            $ch = curl_init();
            $headers = [
                "Authorization: Bearer $token",  // Set header token
                "Content-Type: application/json"
            ];

            // Set opsi cURL
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Mengirim data dalam format JSON
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            // Eksekusi request
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Cek status code

            // Cek jika terjadi error
            if (curl_errno($ch)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'cURL Error: ' . curl_error($ch),
                    'http_code' => $httpCode
                ], $httpCode); // Internal Server Error
            }

            curl_close($ch);

            // Log HTTP Response
            // Jika status code bukan 200, kembalikan pesan error
            if ($httpCode !== 200) {

                return response()->json([
                    'status' => 'error',
                    'message' => "HTTP Status $httpCode - $response",
                ], $httpCode);
            } else {
                return response()->json([
                    'status' => 'success',
                    'message' => "Success",
                ], $httpCode);
            }
        }
        // Data yang akan dikirim
    }

    function delete_onu_type(Request $request, $id)
    {
        // Data yang akan dikirim
        $data = [
            "zxAnPonOnuTypeRowStatus" => (int) 6
        ];

        // Log Data yang akan dikirim ke API

        // Token yang digunakan untuk otentikasi
        $token = $request->session()->get('token');

        // Periksa apakah token tersedia
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token not found',
            ], 401); // Unauthorized
        }

        // URL API eksternal
        $url = "http://103.184.122.170/api/snmp/zxAnPonOnuTypeTable/" . urlencode($id);

        // Inisialisasi cURL
        $ch = curl_init();
        $headers = [
            "Authorization: Bearer $token",  // Set header token
            "Content-Type: application/json"
        ];

        // Set opsi cURL
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Mengirim data dalam format JSON
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        // Eksekusi request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Cek status code

        // Cek jika terjadi error
        if (curl_errno($ch)) {
            return response()->json([
                'status' => 'error',
                'message' => 'cURL Error: ' . curl_error($ch),
                'http_code' => $httpCode
            ], $httpCode); // Internal Server Error
        }

        curl_close($ch);

        // Log HTTP Response
        // Jika status code bukan 200, kembalikan pesan error
        if ($httpCode !== 200) {

            return response()->json([
                'status' => 'error',
                'message' => "HTTP Status $httpCode - $response",
            ], $httpCode);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => "Success",
            ], $httpCode);
        }
    }

    function bandwidth_list(Request $request)
    {
        // Mengambil data dari API eksternal
        $user = $request->user();
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $idOlt = $request->session()->get('id_olt');

        $olt = OltDevice::find($idOlt);
        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token ?? '',
            $olt->udp_port ?? '',
            $olt->snmp_read_write ?? '',
            $olt->version ?? ''
        );
        // Instantiate OltService


        // Fetch Board Info
        $boardInfo = $oltService->getBandWidth();

        // Mengembalikan data dalam format yang sesuai untuk DataTables
        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => count($boardInfo),
            'recordsFiltered' => count($boardInfo),  // Bisa disesuaikan jika ada filter
            'data' => $boardInfo,
        ]);
    }

    function updatebandwidth(Request $request)
    {
        $user = $request->user();
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $idOlt = $request->session()->get('id_olt');

        // Mengambil data dari request
        $id = $request->input('id');
        $name = $request->input('band_name');
        $type = (int) $request->input('band_type');
        $speed_fixed = (int) $request->input('speed_fixed');
        $speed_assured = (int) $request->input('speed_assured');
        $speed_best = (int) $request->input('speed_best');

        $token = $request->session()->get('token');

        // Periksa apakah token tersedia
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token not found',
            ], 401); // Unauthorized
        }

        $index = '';

        // ini untuk konversi name ke id dulu
        $url_konversi = "http://103.184.122.170/api/snmp/converter";

        $ch_konversi = curl_init();
        $headers_konversi = [
            "Authorization: Bearer $token",  // Set header token
            "Content-Type: application/json"
        ];

        $data_konversi = [
            'index' => $name
        ];

        // Set opsi cURL
        curl_setopt($ch_konversi, CURLOPT_HTTPHEADER, $headers_konversi);
        curl_setopt($ch_konversi, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch_konversi, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_konversi, CURLOPT_POSTFIELDS, json_encode($data_konversi)); // Mengirim data dalam format JSON
        curl_setopt($ch_konversi, CURLOPT_URL, $url_konversi);
        curl_setopt($ch_konversi, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch_konversi, CURLOPT_SSL_VERIFYPEER, 0);

        // Eksekusi request
        $response = curl_exec($ch_konversi);
        $httpCode = curl_getinfo($ch_konversi, CURLINFO_HTTP_CODE); // Cek status code

        // Cek jika terjadi error
        if (curl_errno($ch_konversi)) {
            return response()->json([
                'status' => 'error',
                'message' => 'cURL Error Konversi: ' . curl_error($ch_konversi),
                'http_code' => $httpCode
            ], $httpCode); // Internal Server Error
        }

        curl_close($ch_konversi);

        // Log HTTP Response
        // Jika status code bukan 200, kembalikan pesan error
        if ($httpCode !== 200) {

            return response()->json([
                'status' => 'error',
                'message' => "HTTP Status Konversi $httpCode - $response",
            ], $httpCode);
        } else {
            $index_new = json_decode($response, true);

            // INI BARU CEK DATA YANG SAMA
            $olt = OltDevice::find($idOlt);
            $oltService = new OltService(
                $olt->model,
                $olt->host,
                $olt->username,
                $olt->password,
                $olt->token ?? '',
                $olt->udp_port ?? '',
                $olt->snmp_read_write ?? '',
                $olt->version ?? ''
            );
            // Instantiate OltService

            $boardInfo = $oltService->getBandWidth();

            if (!empty($boardInfo)) {
                foreach ($boardInfo as $da) {
                    $parts = explode('.', $da['id']); // Pisahkan berdasarkan titik
                    array_shift($parts); // Hapus elemen pertama
                    $parts = implode('.', $parts); // Gabungkan kembali tanpa elemen pertama

                    $parts1 = explode('.', $index_new['result']); // Pisahkan berdasarkan titik
                    array_shift($parts1); // Hapus elemen pertama
                    $parts1 = implode('.', $parts1);

                    if ($parts === $parts1) {
                        return response()->json([
                            'status' => 'error',
                            'message' => "Terdapat ID yang memiliki Profile Yang Sama",
                        ], 301);
                    }
                }
            }

            $data_band = [
                // "zxAnGponSrvBwPrfFixed" => $speed_fixed,
                // "zxAnGponSrvBwPrfAssured" => $speed_assured,
                // "zxAnGponSrvBwPrfMaximum" => $speed_best,
                "zxAnGponSrvBwPrfType" => $type,
                // "zxAnGponSrvBwPrfPriority" => 0,
                // "zxAnGponSrvBwPrfWeight" => 0,
                "zxAnGponSrvBwPrfRowStatus" => 4
            ];

            if ($type == 1) {
                $data_band['zxAnGponSrvBwPrfFixed'] = $speed_fixed;
            } else if ($type == 2) {
                $data_band['zxAnGponSrvBwPrfAssured'] = $speed_assured;
            } else if ($type == 3) {
                $data_band['zxAnGponSrvBwPrfAssured'] = $speed_assured;
                $data_band['zxAnGponSrvBwPrfMaximum'] = $speed_best;
            } else if ($type == 4) {
                $data_band['zxAnGponSrvBwPrfMaximum'] = $speed_best;
            } else if ($type == 5) {
                $data_band['zxAnGponSrvBwPrfFixed'] = $speed_fixed;
                $data_band['zxAnGponSrvBwPrfAssured'] = $speed_assured;
                $data_band['zxAnGponSrvBwPrfMaximum'] = $speed_best;
            }

            $arrayIndex = explode('.', $index_new['result']);
            $lengIndex = count($arrayIndex);
            $new_id = $index_new['result'];
            // Pisahkan berdasarkan titik



            $url_add = "http://103.184.122.170/api/snmp/zxAnGponSrvBandwidthPrfTable/" . $new_id;
            $ch_add = curl_init();
            $headers_add = [
                "Authorization: Bearer $token",  // Set header token
                "Content-Type: application/json"
            ];

            // Set opsi cURL
            curl_setopt($ch_add, CURLOPT_HTTPHEADER, $headers_add);
            curl_setopt($ch_add, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch_add, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch_add, CURLOPT_POSTFIELDS, json_encode($data_band)); // Mengirim data dalam format JSON
            curl_setopt($ch_add, CURLOPT_URL, $url_add);
            curl_setopt($ch_add, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch_add, CURLOPT_SSL_VERIFYPEER, 0);

            // Eksekusi request
            $response_add = curl_exec($ch_add);
            $httpCode_add = curl_getinfo($ch_add, CURLINFO_HTTP_CODE); // Cek status code

            // Cek jika terjadi error
            if (curl_errno($ch_add)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'cURL Error: ' . curl_error($ch_add),
                    'http_code' => $httpCode_add
                ], $httpCode_add); // Internal Server Error
            }

            curl_close($ch_add);

            // Log HTTP Response
            // Jika status code bukan 200, kembalikan pesan error
            if ($httpCode_add !== 200) {

                return response()->json([
                    'status' => 'error',
                    'message' => "HTTP Status $httpCode_add - $response_add - $new_id",
                ], $httpCode_add);
            } else {
                return response()->json([
                    'status' => 'success',
                    'message' => "Success",
                ], $httpCode_add);
            }
        }
    }

    function delete_bandwidth(Request $request, $id)
    {
        // Data yang akan dikirim
        $data = [
            "zxAnGponSrvBwPrfRowStatus" => (int) 6
        ];

        // Log Data yang akan dikirim ke API

        // Token yang digunakan untuk otentikasi
        $token = $request->session()->get('token');

        // Periksa apakah token tersedia
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token not found',
            ], 401); // Unauthorized
        }

        // URL API eksternal
        $url = "http://103.184.122.170/api/snmp/zxAnGponSrvBandwidthPrfTable/" . urlencode($id);

        // Inisialisasi cURL
        $ch = curl_init();
        $headers = [
            "Authorization: Bearer $token",  // Set header token
            "Content-Type: application/json"
        ];

        // Set opsi cURL
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Mengirim data dalam format JSON
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        // Eksekusi request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Cek status code

        // Cek jika terjadi error
        if (curl_errno($ch)) {
            return response()->json([
                'status' => 'error',
                'message' => 'cURL Error: ' . curl_error($ch),
                'http_code' => $httpCode
            ], $httpCode); // Internal Server Error
        }

        curl_close($ch);

        // Log HTTP Response
        // Jika status code bukan 200, kembalikan pesan error
        if ($httpCode !== 200) {

            return response()->json([
                'status' => 'error',
                'message' => "HTTP Status $httpCode - $response",
            ], $httpCode);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => "Success",
            ], $httpCode);
        }
    }

    public function setting_traffic(Request $request)
    {
        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');

        // Find the OltDevice
        $olt = OltDevice::find($idOlt);
        if (!$olt) {
            $request->session()->flash('error', 'Invalid OLT session');
            return redirect()->to('/' . strtolower($user->role) . '/olt');
        }


        $data = [
            'title' => 'Setting Traffic Profile',
            'namaolt' => $olt->name,
        ];
        return view('olt.device.setting_traffic', compact('data'));
    }

    function traffic_list(Request $request)
    {
        // Mengambil data dari API eksternal
        $user = $request->user();
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $idOlt = $request->session()->get('id_olt');

        $olt = OltDevice::find($idOlt);
        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token ?? '',
            $olt->udp_port ?? '',
            $olt->snmp_read_write ?? '',
            $olt->version ?? ''
        );
        // Instantiate OltService


        // Fetch Board Info
        $boardInfo = $oltService->getTraffic();

        // Mengembalikan data dalam format yang sesuai untuk DataTables
        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => count($boardInfo),
            'recordsFiltered' => count($boardInfo),  // Bisa disesuaikan jika ada filter
            'data' => $boardInfo,
        ]);
    }

    function updatetraffic(Request $request)
    {
        $user = $request->user();
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $idOlt = $request->session()->get('id_olt');

        // Mengambil data dari request
        $name = $request->input('traffic_name');
        $sir = (int) empty($request->input('sir')) ? 0 : (int)$request->input('sir');
        $pir = (int) empty($request->input('pir')) ? 0 : (int)$request->input('pir');
        $cbs = (int) empty($request->input('cbs')) ? 0 : (int)$request->input('cbs');
        $pbs = (int) empty($request->input('pbs')) ? 0 : (int)$request->input('pbs');

        $token = $request->session()->get('token');

        // Periksa apakah token tersedia
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token not found',
            ], 401); // Unauthorized
        }

        $index = '';

        // ini untuk konversi name ke id dulu
        $url_konversi = "http://103.184.122.170/api/snmp/converter";

        $ch_konversi = curl_init();
        $headers_konversi = [
            "Authorization: Bearer $token",  // Set header token
            "Content-Type: application/json"
        ];

        $data_konversi = [
            'index' => $name
        ];

        // Set opsi cURL
        curl_setopt($ch_konversi, CURLOPT_HTTPHEADER, $headers_konversi);
        curl_setopt($ch_konversi, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch_konversi, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_konversi, CURLOPT_POSTFIELDS, json_encode($data_konversi)); // Mengirim data dalam format JSON
        curl_setopt($ch_konversi, CURLOPT_URL, $url_konversi);
        curl_setopt($ch_konversi, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch_konversi, CURLOPT_SSL_VERIFYPEER, 0);

        // Eksekusi request
        $response = curl_exec($ch_konversi);
        $httpCode = curl_getinfo($ch_konversi, CURLINFO_HTTP_CODE); // Cek status code

        // Cek jika terjadi error
        if (curl_errno($ch_konversi)) {
            return response()->json([
                'status' => 'error',
                'message' => 'cURL Error Konversi: ' . curl_error($ch_konversi),
                'http_code' => $httpCode
            ], $httpCode); // Internal Server Error
        }

        curl_close($ch_konversi);

        // Log HTTP Response
        // Jika status code bukan 200, kembalikan pesan error
        if ($httpCode !== 200) {

            return response()->json([
                'status' => 'error',
                'message' => "HTTP Status Konversi $httpCode - $response",
            ], $httpCode);
        } else {
            $index_new = json_decode($response, true);

            // INI BARU CEK DATA YANG SAMA
            $olt = OltDevice::find($idOlt);
            $oltService = new OltService(
                $olt->model,
                $olt->host,
                $olt->username,
                $olt->password,
                $olt->token ?? '',
                $olt->udp_port ?? '',
                $olt->snmp_read_write ?? '',
                $olt->version ?? ''
            );
            // Instantiate OltService

            $boardInfo = $oltService->getTraffic();

            if (!empty($boardInfo)) {
                foreach ($boardInfo as $da) {
                    $parts = explode('.', $da['id']); // Pisahkan berdasarkan titik
                    array_shift($parts); // Hapus elemen pertama
                    $parts = implode('.', $parts); // Gabungkan kembali tanpa elemen pertama
                    $parts1 = explode('.', $index_new['result']); // Pisahkan berdasarkan titik
                    array_shift($parts1); // Hapus elemen pertama
                    $parts1 = implode('.', $parts1);

                    if ($parts === $parts1) {
                        return response()->json([
                            'status' => 'error',
                            'message' => "Terdapat ID yang memiliki Profile Yang Sama",
                        ], 301);
                    }
                }
            }

            $data_band = [
                "zxAnGponSrvTrafficPrfSir" => $sir,
                "zxAnGponSrvTrafficPrfPir" => $pir,
                "zxAnGponSvrTrafficPrfCbs" => $cbs,
                "zxAnGponSvrTrafficPrfPbs" => $pbs,
                "zxAnGponSrvTrafficPrfRowStatus" => (int) 4
            ];

            $arrayIndex = explode('.', $index_new['result']);
            $lengIndex = count($arrayIndex);
            $new_id = $index_new['result'];
            // Pisahkan berdasarkan titik



            $url_add = "http://103.184.122.170/api/snmp/zxAnGponSrvTrafficPrfTable/" . $new_id;
            $ch_add = curl_init();
            $headers_add = [
                "Authorization: Bearer $token",  // Set header token
                "Content-Type: application/json"
            ];

            // Set opsi cURL
            curl_setopt($ch_add, CURLOPT_HTTPHEADER, $headers_add);
            curl_setopt($ch_add, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch_add, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch_add, CURLOPT_POSTFIELDS, json_encode($data_band)); // Mengirim data dalam format JSON
            curl_setopt($ch_add, CURLOPT_URL, $url_add);
            curl_setopt($ch_add, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch_add, CURLOPT_SSL_VERIFYPEER, 0);

            // Eksekusi request
            $response_add = curl_exec($ch_add);
            $httpCode_add = curl_getinfo($ch_add, CURLINFO_HTTP_CODE); // Cek status code

            // Cek jika terjadi error
            if (curl_errno($ch_add)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'cURL Error: ' . curl_error($ch_add),
                    'http_code' => $httpCode_add
                ], $httpCode_add); // Internal Server Error
            }

            curl_close($ch_add);

            // Log HTTP Response
            // Jika status code bukan 200, kembalikan pesan error
            if ($httpCode_add !== 200) {

                return response()->json([
                    'status' => 'error',
                    'message' => "HTTP Status $httpCode_add - $url_add",
                ], $httpCode_add);
            } else {
                return response()->json([
                    'status' => 'success',
                    'message' => "Success",
                ], $httpCode_add);
            }
        }
    }

    function delete_traffic(Request $request, $id)
    {
        // Data yang akan dikirim
        $data = [
            "zxAnGponSrvTrafficPrfRowStatus" => (int) 6
        ];

        // Log Data yang akan dikirim ke API

        // Token yang digunakan untuk otentikasi
        $token = $request->session()->get('token');

        // Periksa apakah token tersedia
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token not found',
            ], 401); // Unauthorized
        }

        // URL API eksternal
        $url = "http://103.184.122.170/api/snmp/zxAnGponSrvTrafficPrfTable/" . urlencode($id);

        // Inisialisasi cURL
        $ch = curl_init();
        $headers = [
            "Authorization: Bearer $token",  // Set header token
            "Content-Type: application/json"
        ];

        // Set opsi cURL
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Mengirim data dalam format JSON
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        // Eksekusi request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Cek status code

        // Cek jika terjadi error
        if (curl_errno($ch)) {
            return response()->json([
                'status' => 'error',
                'message' => 'cURL Error: ' . curl_error($ch),
                'http_code' => $httpCode
            ], $httpCode); // Internal Server Error
        }

        curl_close($ch);

        // Log HTTP Response
        // Jika status code bukan 200, kembalikan pesan error
        if ($httpCode !== 200) {

            return response()->json([
                'status' => 'error',
                'message' => "HTTP Status $httpCode - $response",
            ], $httpCode);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => "Success",
            ], $httpCode);
        }
    }

    public function setting_vlan_profile(Request $request)
    {
        $user = $request->user();
        $idOlt = $request->session()->get('id_olt');
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');

        // Find the OltDevice
        $olt = OltDevice::find($idOlt);
        if (!$olt) {
            $request->session()->flash('error', 'Invalid OLT session');
            return redirect()->to('/' . strtolower($user->role) . '/olt');
        }


        $data = [
            'title' => 'Setting VLAN Profile',
            'namaolt' => $olt->name,
        ];
        return view('olt.device.setting_vlan_profile', compact('data'));
    }

    function vlan_profile_list(Request $request)
    {
        // Mengambil data dari API eksternal
        $user = $request->user();
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $idOlt = $request->session()->get('id_olt');

        $olt = OltDevice::find($idOlt);
        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token ?? '',
            $olt->udp_port ?? '',
            $olt->snmp_read_write ?? '',
            $olt->version ?? ''
        );
        // Instantiate OltService


        // Fetch Board Info
        $boardInfo = $oltService->getVlanProfile();

        // Mengembalikan data dalam format yang sesuai untuk DataTables
        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => count($boardInfo),
            'recordsFiltered' => count($boardInfo),  // Bisa disesuaikan jika ada filter
            'data' => $boardInfo,
        ]);
    }

    function updatevlanprofile(Request $request)
    {
        $user = $request->user();
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $idOlt = $request->session()->get('id_olt');

        // Mengambil data dari request
        $name = $request->input('vlan_name');
        $mode_vlan = (int) empty($request->input('mode_vlan')) ? 0 : (int)$request->input('mode_vlan');
        $id_vlan = (int) empty($request->input('id_vlan')) ? 0 : (int)$request->input('id_vlan');
        $cos = (int) empty($request->input('cos')) ? 0 : (int)$request->input('cos');

        $token = $request->session()->get('token');

        // Periksa apakah token tersedia
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token not found',
            ], 401); // Unauthorized
        }

        $index = '';

        // ini untuk konversi name ke id dulu
        $url_konversi = "http://103.184.122.170/api/snmp/converter";

        $ch_konversi = curl_init();
        $headers_konversi = [
            "Authorization: Bearer $token",  // Set header token
            "Content-Type: application/json"
        ];

        $data_konversi = [
            'index' => $name
        ];

        // Set opsi cURL
        curl_setopt($ch_konversi, CURLOPT_HTTPHEADER, $headers_konversi);
        curl_setopt($ch_konversi, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch_konversi, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_konversi, CURLOPT_POSTFIELDS, json_encode($data_konversi)); // Mengirim data dalam format JSON
        curl_setopt($ch_konversi, CURLOPT_URL, $url_konversi);
        curl_setopt($ch_konversi, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch_konversi, CURLOPT_SSL_VERIFYPEER, 0);

        // Eksekusi request
        $response = curl_exec($ch_konversi);
        $httpCode = curl_getinfo($ch_konversi, CURLINFO_HTTP_CODE); // Cek status code

        // Cek jika terjadi error
        if (curl_errno($ch_konversi)) {
            return response()->json([
                'status' => 'error',
                'message' => 'cURL Error Konversi: ' . curl_error($ch_konversi),
                'http_code' => $httpCode
            ], $httpCode); // Internal Server Error
        }

        curl_close($ch_konversi);

        // Log HTTP Response
        // Jika status code bukan 200, kembalikan pesan error
        if ($httpCode !== 200) {

            return response()->json([
                'status' => 'error',
                'message' => "HTTP Status Konversi $httpCode - $response",
            ], $httpCode);
        } else {
            $index_new = json_decode($response, true);

            // INI BARU CEK DATA YANG SAMA
            $olt = OltDevice::find($idOlt);
            $oltService = new OltService(
                $olt->model,
                $olt->host,
                $olt->username,
                $olt->password,
                $olt->token ?? '',
                $olt->udp_port ?? '',
                $olt->snmp_read_write ?? '',
                $olt->version ?? ''
            );
            // Instantiate OltService

            $boardInfo = $oltService->getVlanProfile();

            if (!empty($boardInfo)) {
                foreach ($boardInfo as $da) {
                    $parts = explode('.', $da['id']); // Pisahkan berdasarkan titik
                    array_shift($parts); // Hapus elemen pertama
                    $parts = implode('.', $parts); // Gabungkan kembali tanpa elemen pertama

                    $parts1 = explode('.', $index_new['result']); // Pisahkan berdasarkan titik
                    array_shift($parts1); // Hapus elemen pertama
                    $parts1 = implode('.', $parts1);

                    if ($parts === $parts1) {
                        return response()->json([
                            'status' => 'error',
                            'message' => "Terdapat ID yang memiliki Profile Yang Sama",
                        ], 301);
                    }
                }
            }

            $data_band = [
                "zxAnGponRmVlanPrfTagMode" => $mode_vlan,
                "zxAnGponRmVlanPrfCVid" => $id_vlan,
                "zxAnGponRmVlanPrfCtagCos" => $cos,
                "zxAnGponRmVlanPrfRowStatus" => (int) 4,
            ];

            $arrayIndex = explode('.', $index_new['result']);
            $lengIndex = count($arrayIndex);
            $new_id = $index_new['result'];
            // Pisahkan berdasarkan titik



            $url_add = "http://103.184.122.170/api/snmp/zxAnGponRmVlanPrfTable/" . $new_id;
            $ch_add = curl_init();
            $headers_add = [
                "Authorization: Bearer $token",  // Set header token
                "Content-Type: application/json"
            ];

            // Set opsi cURL
            curl_setopt($ch_add, CURLOPT_HTTPHEADER, $headers_add);
            curl_setopt($ch_add, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch_add, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch_add, CURLOPT_POSTFIELDS, json_encode($data_band)); // Mengirim data dalam format JSON
            curl_setopt($ch_add, CURLOPT_URL, $url_add);
            curl_setopt($ch_add, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch_add, CURLOPT_SSL_VERIFYPEER, 0);

            // Eksekusi request
            $response_add = curl_exec($ch_add);
            $httpCode_add = curl_getinfo($ch_add, CURLINFO_HTTP_CODE); // Cek status code

            // Cek jika terjadi error
            if (curl_errno($ch_add)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'cURL Error: ' . curl_error($ch_add),
                    'http_code' => $httpCode_add
                ], $httpCode_add); // Internal Server Error
            }

            curl_close($ch_add);

            // Log HTTP Response
            // Jika status code bukan 200, kembalikan pesan error
            if ($httpCode_add !== 200) {

                return response()->json([
                    'status' => 'error',
                    'message' => "HTTP Status $httpCode_add - $url_add",
                ], $httpCode_add);
            } else {
                return response()->json([
                    'status' => 'success',
                    'message' => "Success",
                ], $httpCode_add);
            }
        }
    }

    function delete_vlan_profile(Request $request, $id)
    {
        // Data yang akan dikirim
        $data = [
            "zxAnGponRmVlanPrfRowStatus" => (int) 6
        ];

        // Log Data yang akan dikirim ke API

        // Token yang digunakan untuk otentikasi
        $token = $request->session()->get('token');

        // Periksa apakah token tersedia
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token not found',
            ], 401); // Unauthorized
        }

        // URL API eksternal
        $url = "http://103.184.122.170/api/snmp/zxAnGponRmVlanPrfTable/" . urlencode($id);

        // Inisialisasi cURL
        $ch = curl_init();
        $headers = [
            "Authorization: Bearer $token",  // Set header token
            "Content-Type: application/json"
        ];

        // Set opsi cURL
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Mengirim data dalam format JSON
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        // Eksekusi request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Cek status code

        // Cek jika terjadi error
        if (curl_errno($ch)) {
            return response()->json([
                'status' => 'error',
                'message' => 'cURL Error: ' . curl_error($ch),
                'http_code' => $httpCode
            ], $httpCode); // Internal Server Error
        }

        curl_close($ch);

        // Log HTTP Response
        // Jika status code bukan 200, kembalikan pesan error
        if ($httpCode !== 200) {

            return response()->json([
                'status' => 'error',
                'message' => "HTTP Status $httpCode - $response",
            ], $httpCode);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => "Success",
            ], $httpCode);
        }

        function vlan_list_bind(Request $request, $id)
        {
            // Mengambil data dari API eksternal
            $user = $request->user();
            $token = $request->session()->get('token');
            $host = $request->session()->get('host');
            $namaolt = $request->session()->get('namaolt');
            $idOlt = $request->session()->get('id_olt');

            $olt = OltDevice::find($idOlt);
            $oltService = new OltService(
                $olt->model,
                $olt->host,
                $olt->username,
                $olt->password,
                $olt->token ?? '',
                $olt->udp_port ?? '',
                $olt->snmp_read_write ?? '',
                $olt->version ?? ''
            );
            // Instantiate OltService


            // Fetch Board Info
            $boardInfo = $oltService->getVlanBind($id);
            if (empty($boardInfo)) {
                $boardInfo = [];
            }

            // Mengembalikan data dalam format yang sesuai untuk DataTables
            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => count($boardInfo),
                'recordsFiltered' => count($boardInfo),  // Bisa disesuaikan jika ada filter
                'data' => $boardInfo,
            ]);
        }
    }

    function vlan_list_bind(Request $request, $id)
    {
        // Mengambil data dari API eksternal
        $user = $request->user();
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $idOlt = $request->session()->get('id_olt');
        $id_new = explode('.', $id);
        $id = reverseToIdV2($id_new[0], $id_new[1]);

        $olt = OltDevice::find($idOlt);
        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token ?? '',
            $olt->udp_port ?? '',
            $olt->snmp_read_write ?? '',
            $olt->version ?? ''
        );
        // Instantiate OltService


        // Fetch Board Info
        $boardInfo = $oltService->getVlanBind($id);
        $data_new = [
            ['id' => 100]
        ];
        if (empty($boardInfo)) {
            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => count($data_new),
                'recordsFiltered' => count($data_new),  // Bisa disesuaikan jika ada filter
                'data' => $data_new,
            ]);
        } else {
            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => count($boardInfo),
                'recordsFiltered' => count($boardInfo),  // Bisa disesuaikan jika ada filter
                'data' => $boardInfo,
            ]);
        }
        // Mengembalikan data dalam format yang sesuai untuk DataTables

    }

    function http_post($url, $headers, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Mengirim data dalam format JSON
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        // Eksekusi request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Cek status code

        // Cek jika terjadi error
        if (curl_errno($ch)) {
            return json_encode([
                'status' => 'error',
                'message' => 'cURL Error: ' . curl_error($ch),
                'http_code' => $httpCode
            ]);
        }

        curl_close($ch);

        // Log HTTP Response
        // Jika status code bukan 200, kembalikan pesan error
        if ($httpCode !== 200) {
            return json_encode([
                'status' => 'error',
                'message' => "HTTP Status $httpCode - $response - $url",
                'data' => $data
            ]);
        } else {
            return json_encode([
                'status' => 'success',
                'response' => $response
            ]);
        }
    }

    function getHistory(Request $request, $id)
    {
        $user = $request->user();
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $idOlt = $request->session()->get('id_olt');

        $response = OltHistory::with('user')
            ->where('id_olt', $idOlt)
            ->where('group_id', $user->id_group)
            ->where('id_onu', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return json_decode($response, true);
    }

    function speed_table(Request $request, $id)
    {
        // Mengambil data dari API eksternal
        $user = $request->user();
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $idOlt = $request->session()->get('id_olt');
        $id_new = explode('.', $id);

        $olt = OltDevice::find($idOlt);
        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token ?? '',
            $olt->udp_port ?? '',
            $olt->snmp_read_write ?? '',
            $olt->version ?? ''
        );
        // Instantiate OltService
        $boardInfo = $oltService->getBoardInfo();

        if (!empty($boardInfo)) {
            $id_gemport = '';
            $id_srvport = '';
            $upload = '';
            $download = '';

            foreach ($boardInfo['response']['download'] as $gas) {
                $pos = strrpos($gas['id'], '.');
                $newId = ($pos !== false) ? substr($gas['id'], 0, $pos) : $gas['id'];

                if ($newId === $id) {
                    $id_gemport = $gas['id'];
                    $download = $gas['zxAnGponSrvGemPortDsTrafficPrf'];
                    break;
                }
            }

            foreach ($boardInfo['response']['upload'] as $gas) {
                $pos = strrpos($gas['id'], '.');
                $newId = ($pos !== false) ? substr($gas['id'], 0, $pos) : $gas['id'];

                if ($newId === $id) {
                    $id_srvport = $gas['id'];
                    $upload = $gas['zxAnGponSrvTcontBwPrfName'];
                    break;
                }
            }

            $data = [
                'id_gemport' => $id_gemport,
                'id_srvport' => $id_srvport,
                'upload' => $upload,
                'download' => $download
            ];

            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => count($data),
                'recordsFiltered' => count($data),  // Bisa disesuaikan jika ada filter
                'data' => [$data],
            ]);
        }
        return response()->json([
            'data' => [],
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
        ]);
    }

    function deleteONU(Request $request, $id) {
        $user = $request->user();
        $token = $request->session()->get('token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');
        $idOlt = $request->session()->get('id_olt');
          // URL API eksternal
          $url = "http://103.184.122.170/api/snmp/zxAnGponSrvOnuMgmtTable/" . urlencode($id);

          $data = [
            "zxAnGponOnuMgmtRowStatus" => (int) 6
          ];

          // Inisialisasi cURL
          $headers = [
              "Authorization: Bearer $token",  // Set header token
              "Content-Type: application/json"
          ];

          if (OltConfig::where('id_olt', $idOlt)->where('group_id', $user->id_group)->where('id_onu', $id)->exists()) {
                OltConfig::where('id_olt', $idOlt)->where('group_id', $user->id_group)->where('id_onu', $id)->delete();
          }

          if (OltHistory::where('id_olt', $idOlt)->where('group_id', $user->id_group)->where('id_onu', $id)->exists()) {
             OltHistory::where('id_olt', $idOlt)->where('group_id', $user->id_group)->where('id_onu', $id)->delete();
          }

          $resutl =  $this->http_post($url,$headers,$data);
          return $resutl;
    }

    // CONFIG ONU
}
