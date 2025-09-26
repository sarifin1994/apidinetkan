<?php

namespace App\Http\Controllers\Olt;

use App\Http\Controllers\Controller;
use App\Models\Olt\OltDevice;
use Illuminate\Http\Request;
use App\Library\HsgqAPI;
use App\Models\Member;
use App\Models\RadiusSession;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class HsgqController extends Controller
{
    private $HsgqAPI;

    public function __construct()
    {
        $this->HsgqAPI = new HsgqAPI();
    }

    public function do_auth_device(Request $request)
    {
        $olt = OltDevice::where('id', $request->id)->first();
        $idnya = $olt->id;
        $host = $olt->host;
        $username = $olt->username;
        $password = $olt->password;
        $cookies = $olt->cookies;
        $nama = $olt->name;

        if ($cookies == null) {
            $connect = HsgqAPI::login($host, $username, $password);

            if (is_array($connect) && isset($connect['response']) && is_array($connect['response'])) {
                $responseCode = $connect['response']['code'];
                if ($responseCode == '4028') {
                    $request->session->flash('errors', ['Password OLT salah']);
                    return redirect()->to('/olt');
                } elseif ($responseCode == '4031') {
                    $request->session->flash('errors', ['Username tidak ditemukan pada sistem OLT']);
                    return redirect()->to('/olt');
                } elseif ($responseCode == '1') {
                    $xtoken = $connect['xToken'];

                    $olt->update([
                        'cookies' => $xtoken,
                    ]);

                    $sess_data = [
                        'id_olt' => $idnya,
                        'x-token' => $xtoken,
                        'namaolt' => $nama,
                        'host' => $host,
                    ];

                    $request()->session()->put($sess_data);

                    return response()->json([
                        'success' => true,
                        'message' => 'OLT Connected',
                        'session' => $session,
                    ]);
                }
            } else {
                $request->session()->flash('errors', 'OLT Not connected !');
                return response()->json([
                    'success' => true,
                    'message' => 'OLT Not Connected',
                ]);
            }
        } else {
            $sess_data = [
                'id_olt' => $idnya,
                'x-token' => $cookies,
                'namaolt' => $nama,
                'host' => $host,
            ];

            $request->session()->put($sess_data);
            return response()->json([
                'success' => true,
                'message' => 'OLT Berhasil Terhubung',
            ]);
        }
    }

    public function deviceLogout(Request $request)
    {
        $idOlt = $request->session()->get('id_olt');
        $xtoken = $request->session()->get('x-token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');

        $cekolt = OltDevice::query()->find($idOlt);

        $username = $cekolt->username;

        $logoutDevice = $this->HsgqAPI->logout($host, $xtoken, $username);
        $response = json_decode($logoutDevice, true);
        if ($response['code'] == '1') {
            $request->session()->pull('id_olt');
            $request->session()->pull('x-token');
            $request->session()->pull('host');
            $request->session()->pull('namaolt');

            $request->session()->flash('success', ['Anda telah logout dari OLT ' . $namaolt]);
            return redirect()->to('/olt');
        }
    }

    public function show_olt(Request $request, OltDevice $id)
    {
        $idOlt = $request->session()->get('id_olt');
        $xtoken = $request->session()->get('x-token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');

        if ($xtoken == null) {
            $request->session()->flash('errors', ['Silahkan klik connect terlebih dahulu']);
            return redirect()->to('/olt');
        } else {
            $cekolt = OltDevice::query()->find($idOlt);

            $idnya = $cekolt->id;
            $username = $cekolt->username;
            $password = $cekolt->password;

            $result = $this->HsgqAPI->getBoardInfo($host, $xtoken);
            $response = json_decode($result, true);

            if ($response === null) {
                $request->session()->flash('errors', 'IP Address OLT Salah! Harap Periksa Kembali');
                return redirect()->back();
            }

            if ($response['code'] == '0') {
                $connect = $this->HsgqAPI->login($host, $username, $password);

                $responseCode = $connect['response']['code'];

                if ($responseCode == '4028') {
                    $request->session()->flash('errors', 'Password OLT Salah! Harap Periksa Kembali');
                    return redirect()->back();
                } elseif ($responseCode == '4031') {
                    $request->session()->flash('errors', 'Username Not Found! Haarap Periksa Kembali');
                    return redirect()->back();
                } elseif ($responseCode == '4036') {
                    $request->session()->flash('errors', 'Username Not Found! Harap Periksa Kembali');
                    return redirect()->back();
                } elseif ($responseCode == '4033') {
                    $request->session()->flash('errors', 'Password OLT Salah! Harap Periksa Kembali');
                    return redirect()->back();
                } elseif ($responseCode == '1') {
                    $xtoken = $connect['xToken'];

                    $cekolt->update([
                        'cookies' => $xtoken,
                    ]);

                    $sess_data = [
                        'x-token' => $xtoken,
                    ];

                    $request->session()->put($sess_data);
                    return redirect()->to('/olt/hsgq/dashboard');
                }
            } else {
                $result_system = $this->HsgqAPI->getSystemInfo($host, $xtoken);
                $result_time = $this->HsgqAPI->getInfoTime($host, $xtoken);
                $response_time = json_decode($result_time, true);
                $response_system = json_decode($result_system, true);
                if (isset($response['data'])) {
                    $data = [
                        'title' => 'Dashboard OLT',
                        'namaolt' => $namaolt,
                        'pon' => $response['data'],
                        'device' => $response_system['data'],
                        'time' => $response_time['data'],
                    ];
                } else {
                    // fallback aman
                    $data = [
                        'title' => 'Dashboard OLT',
                        'namaolt' => 'NULL',
                        'pon' => 'NULL',
                        'device' => 'NULL',
                        'time' => 'NULL',
                    ];
                }
               
                return view('backend.olt.hsgq.dashboard', compact('data'));
            }
        }
    }

    public function show($id)
    {
        //return response
        $olt = OltDevice::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $olt,
        ]);
    }

    public function show_pon(Request $request, $id)
    {
        $idOlt = $request->session()->get('id_olt');
        $xtoken = $request->session()->get('x-token');
        $host = $request->session()->get('host');

        if ($xtoken == null) {
            $this->session()->flash('errors', ['Silahkan klik connect terlebih dahulu']);
            return redirect()->to('/olt');
        } else {
            $getPon = $this->HsgqAPI->getBoardInfo($host, $xtoken);
            $resPon = json_decode($getPon, true);
            $tipeOlt = OltDevice::where('id', $idOlt)->first();
            if ($tipeOlt->version === 'GPON') {
                $result = $this->HsgqAPI->getOnuAllowListGpon($host, $id, $xtoken);
                $response = json_decode($result, true);
                // dd($response);
                $data = [
                    'title' => 'OLT PON ' . $id,
                    'id' => $id,
                    'pon' => $resPon['data'],
                    'onu' => $response['data'],
                ];
                if (request()->ajax()) {
                    return DataTables::of($data['onu'])->addIndexColumn()->toJson();
                }
                return view('backend.olt.hsgq.onu_gpon', compact('data'));
            } else {
                $result = $this->HsgqAPI->getOnuAllowList($host, $id, $xtoken);
                $response = json_decode($result, true);
                $data = [
                    'title' => 'OLT PON ' . $id,
                    'id' => $id,
                    'pon' => $resPon['data'],
                    'onu' => $response['data'],
                ];
                if (request()->ajax()) {
                    return DataTables::of($data['onu'])->addIndexColumn()->toJson();
                }
                return view('backend.olt.hsgq.onu', compact('data'));
            }
        }
    }

    public function show_pon_all(Request $request)
    {
        $idOlt = $request->session()->get('id_olt');
        $xtoken = $request->session()->get('x-token');
        $host = $request->session()->get('host');

        if ($xtoken == null) {
            $this->session()->flash('errors', ['Silahkan klik connect terlebih dahulu']);
            return redirect()->to('/olt');
        } else {
            $getPon = $this->HsgqAPI->getBoardInfo($host, $xtoken);
            $resPon = json_decode($getPon, true);
            $result = $this->HsgqAPI->getOnuAllowListAll($host, $xtoken);
            $response = json_decode($result, true);
            // dd($response);

            $data = [
                'title' => 'OLT PON All',
                'pon' => $resPon['data'],
                'onu' => $response['data'],
            ];
            if (request()->ajax()) {
                return DataTables::of($data['onu'])->addIndexColumn()->toJson();
            }
            return view('backend.olt.hsgq.onu', compact('data'));
        }
    }

    public function show_onu(Request $request, $port, $onu)
    {
        $idOlt = $request->session()->get('id_olt');
        $xtoken = $request->session()->get('x-token');
        $host = $request->session()->get('host');
        $port = request()->segment(4);
        $onu = request()->segment(6);
        if ($xtoken == null) {
            $this->session()->flash('errors', ['Silahkan klik connect terlebih dahulu']);
            return redirect()->to('/olt');
        } else {
            // // $getDataCustomer = $this->dashboardModel->getDataSecretByPortIDAndOnuID($portnya, $onunya, $idOlt);
            // $user = OltUser::where('olt_id', $idOlt)->where('port_id', $port)->where('onu_id', $onu)->with('ppp', 'member')->first();
            // if ($user !== null) {
            //     $session = RadiusSession::with('ppp:username,status')->where('username', $user->ppp->username)->orderBy('id', 'desc')->first();
            //     $result = $this->HsgqAPI->getOnuData($host, $port, $onu, $xtoken);
            //     $data = [
            //         'title' => 'OLT PON ' . $port,
            //         'id' => $port,
            //         'optic' => $result['optical'],
            //         'base' => $result['base'],
            //         'user' => $user,
            //         'session' => $session,
            //     ];
            // } else {
            $tipeOlt = OltDevice::where('id', $idOlt)->first();
            if ($tipeOlt->version === 'GPON') {
                
            } else {
                $result = $this->HsgqAPI->getOnuData($host, $port, $onu, $xtoken);
                $data = [
                    'title' => 'OLT PON ' . $port,
                    'id' => $port,
                    'optic' => $result['optical'],
                    'base' => $result['base'],
                    // 'user' => $user,
                ];
                // }

                // dd($data);
                return view('backend.olt.hsgq.detail_onu', compact('data'));
            }
        }
    }

    public function reboot_onu(Request $request, $port, $onu)
    {
        $xtoken = $request->session()->get('x-token');
        $host = $request->session()->get('host');
        $onu = request()->segment(7);

        if ($xtoken == null) {
            $this->session()->flash('errors', ['Silahkan klik connect terlebih dahulu']);
            return redirect()->back();
        } else {
            $this->HsgqAPI->rebootOnu($host, $port, $onu, $xtoken);
            return response()->json([
                'success' => true,
                'message' => 'ONU Berhasil Direboot',
            ]);
        }
    }

    public function reboot_olt(Request $request, $olt)
    {
        $idOlt = $request->session()->get('id_olt');
        $xtoken = $request->session()->get('x-token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');

        if ($xtoken == null) {
            $this->session()->flash('errors', ['Silahkan klik connect terlebih dahulu']);
            return redirect()->back();
        } else {
            $this->HsgqAPI->rebootSystem($host, $xtoken);

            $request->session()->pull('id_olt');
            $request->session()->pull('x-token');
            $request->session()->pull('host');
            $request->session()->pull('namaolt');
            return response()->json([
                'success' => true,
                'message' => 'OLT Berhasil Direboot',
            ]);
        }
    }

    public function save_olt(Request $request, $olt)
    {
        $idOlt = $request->session()->get('id_olt');
        $xtoken = $request->session()->get('x-token');
        $host = $request->session()->get('host');
        $namaolt = $request->session()->get('namaolt');

        if ($xtoken == null) {
            $this->session()->flash('errors', ['Silahkan klik connect terlebih dahulu']);
            return redirect()->back();
        } else {
            $this->HsgqAPI->saveSystem($host, $xtoken);
            return response()->json([
                'success' => true,
                'message' => 'OLT Configuration Berhasil Disimpan',
            ]);
        }
    }

    public function rename(Request $request)
    {
        $idOlt = $request->session()->get('id_olt');
        $xtoken = $request->session()->get('x-token');
        $host = $request->session()->get('host');
        $port = $request->port_id;
        $onu = $request->onu_id;
        $name = $request->onu_name;
        // $pppoe_id = $request->pppoe_id;
        // $member_id = $request->member_id;

        if ($xtoken == null) {
            $request->session()->flash('errors', ['Silahkan klik connect terlebih dahulu']);
            return redirect()->to('/olt');
        } else {
            $this->HsgqAPI->changeName($host, $port, $onu, $name, $xtoken);
            return response()->json([
                'success' => true,
                'message' => 'ONU Berhasil Direname',
            ]);
        }
    }

    // public function sync(Request $request)
    // {
    //     $idOlt = $request->session()->get('id_olt');
    //     $xtoken = $request->session()->get('x-token');
    //     $host = $request->session()->get('host');
    //     $port = $request->port_id;
    //     $onu = $request->onu_id;
    //     $pppoe_id = $request->pppoe_id;
    //     $member_id = $request->member_id;

    //     if ($xtoken == null) {
    //         $request->session()->flash('errors', ['Silahkan klik connect terlebih dahulu']);
    //         return redirect()->to('/olt');
    //     } else {
    //         $cek_user = OltUser::where('olt_id', $idOlt)->where('port_id', $port)->where('onu_id', $onu)->first();
    //         if ($cek_user === null) {
    //             $user = OltUser::create([
    //                 'shortname' => multi_auth()->shortname,
    //                 'pppoe_id' => $pppoe_id,
    //                 'member_id' => $member_id,
    //                 'olt_id' => $idOlt,
    //                 'port_id' => $port,
    //                 'onu_id' => $onu,
    //             ]);
    //             //return response
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'Data Berhasil Disimpan',
    //                 'data' => $user,
    //             ]);
    //         } else {
    //             $user = OltUser::where('olt_id', $idOlt)->where('port_id', $port)->where('onu_id', $onu);
    //             $user->update([
    //                 'pppoe_id' => $pppoe_id,
    //                 'member_id' => $member_id,
    //                 'olt_id' => $idOlt,
    //                 'port_id' => $port,
    //                 'onu_id' => $onu,
    //             ]);
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'Data Berhasil Diupdate',
    //                 'data' => $user,
    //             ]);
    //         }
    //     }
    // }

    public function delete_onu(Request $request, $port, $onu)
    {
        $idOlt = $request->session()->get('id_olt');
        $xtoken = $request->session()->get('x-token');
        $host = $request->session()->get('host');
        $onu = request()->segment(7);
        $macaddr = $request->mac;

        if ($xtoken == null) {
            $this->session()->flash('errors', ['Silahkan klik connect terlebih dahulu']);
            return redirect()->back();
        } else {
            $this->HsgqAPI->deleteOnuList($host, $port, $onu, $macaddr, $xtoken);
            return response()->json([
                'success' => true,
                'message' => 'ONU Berhasil Dihapus',
            ]);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:3', 'max:255', Rule::unique('olt_device')->where('shortname', multi_auth()->shortname)],
            'username' => 'required',
            'password' => 'required',
            'host' => ['required', Rule::unique('olt_device')],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $olt = OltDevice::create([
            'shortname' => multi_auth()->shortname,
            'name' => $request->name,
            'type' => $request->type,
            'host' => $request->host,
            'username' => $request->username,
            'password' => $request->password,
            'cookies' => '812u37y123y721y3',
        ]);
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $olt,
        ]);
    }

    public function update(Request $request, OltDevice $olt)
    {
        $olt->update([
            'name' => $request->name,
            'host' => $request->ip,
            'username' => $request->username,
            'password' => $request->password,
        ]);

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $olt,
        ]);
    }

    public function destroy($id)
    {
        $olt = OltDevice::findOrFail($id);
        $olt->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }
}
