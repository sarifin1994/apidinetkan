<?php

namespace App\Http\Controllers\Whatsapp;

use App\Http\Controllers\Controller;
use App\Models\Whatsapp\Watemplate;
use Illuminate\Http\Request;
use App\Models\Whatsapp\Mpwa;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Mapping\Pop;
use App\Models\Mapping\Odp;
use App\Models\Pppoe\PppoeUser;
use App\Models\Partnership\Mitra;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Setting\WaServer;
use App\Models\User;

class MpwaController extends Controller
{
    public function daftar(Request $request)
    {
        $wa = WaServer::where('wa_url',$request->mpwa_server)->first();
//        echo $wa->wa_server;exit;
        try {
            if($wa->wa_server == 'mpwa'){
                $curl = curl_init();
                $data = [
                    'api_key' => $wa->wa_api,
                    'username' => multi_auth()->shortname,
                    'password' => '12345678',
                    'email' => multi_auth()->email,
                    'expire' => 365,
                    'limit_device' => 1,
                ];
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($curl, CURLOPT_URL, 'https://' . $request->mpwa_server . '/create-user');
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

                $response = curl_exec($curl);
                curl_close($curl);
                $result = json_decode($response, true);

                $user = Mpwa::create([
                    'shortname' => multi_auth()->shortname,
                    'mpwa_server' => $request->mpwa_server,
                    'sender' => $request->no_wa_daftar,
                    'user_id' => $result['user_id'],
                    'api_key' => $result['api_key'],
                    'webhook' => '',
                    'mpwa_server_server' => $wa->wa_server ,
                ]);

                $curl = curl_init();
                $data = [
                    'api_key' => $user->api_key,
                    'device' => $user->sender,
                    'force' => true,
                ];
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($curl, CURLOPT_URL, 'https://' . $request->mpwa_server . '/generate-qr');
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                $response = curl_exec($curl);
                curl_close($curl);
                // $result = json_decode($response, true);
            }
            if($wa->wa_server == 'radiusqu'){
                $user = Mpwa::create([
                    'shortname' => multi_auth()->shortname,
                    'mpwa_server' => $request->mpwa_server,
                    'sender' => $request->no_wa_daftar,
                    'user_id' => $request->no_wa_daftar,
                    'api_key' => $request->no_wa_daftar, // $result['api_key'],
                    'webhook' => '',
                    'mpwa_server_server' => "radiusqu" ,
                ]);
                $this->start();
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return response()->json([
            'success' => false,
            'message' => 'Daftar WA Gateway Berhasil, Silakan Scan Device',
        ]);
    }
    public function index()
    {
        $wa_server = WaServer::get();
        $areas = Pop::where('shortname', multi_auth()->shortname)->orderBy('kode_area', 'desc')->get();
        $odps = Odp::where('shortname', multi_auth()->shortname)->orderBy('kode_odp', 'desc')->get();
        $mitras = Mitra::where('shortname', multi_auth()->shortname)->get();
        $statuses = PppoeUser::where('shortname', multi_auth()->shortname)->get();
        $whatsapp = Mpwa::where('shortname', multi_auth()->shortname)->first();
        
        if ($whatsapp == null) {
            return view('backend.whatsapp.register_new',compact('wa_server'));
        } else {
            try {
                if($whatsapp->mpwa_server_server == 'mpwa'){
                    $curl = curl_init();
                    $data = [
                        'api_key' => $whatsapp->api_key,
                        'number' => $whatsapp->sender,
                    ];
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                    curl_setopt($curl, CURLOPT_URL, 'https://' . $whatsapp->mpwa_server . '/info-device');
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                    $response = curl_exec($curl);
                    curl_close($curl);
                    $result = json_decode($response, true);
                    if($result['status'] == false){
                        $whatsapp->delete();
                        return view('backend.whatsapp.register_new',compact('wa_server'));
                    }
                    if (request()->ajax()) {
                        $curl2 = curl_init();
                        $data2 = [
                            'api_key' => $whatsapp->api_key,
                            'user_id' => $whatsapp->user_id,
                        ];
                        curl_setopt($curl2, CURLOPT_CUSTOMREQUEST, 'POST');
                        curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl2, CURLOPT_POSTFIELDS, http_build_query($data2));
                        curl_setopt($curl2, CURLOPT_URL, 'https://' . $whatsapp->mpwa_server . '/get-messages');
                        curl_setopt($curl2, CURLOPT_SSL_VERIFYHOST, 0);
                        curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, 0);
                        $response2 = curl_exec($curl2);
                        curl_close($curl2);
                        $result2 = json_decode($response2, true);
                        $messages = collect($result2['message']);
                        $selectedIds = request()->get('idsel') ?? [];
                        return DataTables::of($messages)
                            ->addColumn('checkbox', function ($row) use ($selectedIds) {
                                $checked = in_array($row['id'], $selectedIds) ? ' checked' : ''; // Periksa apakah ID ada dalam array
                                return '<input type="checkbox" class="row-cb form-check-input" id="checkbox_row' . $row['id'] . '" value="' . $row['id'] . '"' . $checked . ' />';
                            })
                            ->rawColumns(['action', 'checkbox'])
                            ->toJson();
                    }

                    return view('backend.whatsapp.index_new', compact('result', 'areas', 'odps', 'mitras', 'statuses', 'whatsapp'));
                }
                if($whatsapp->mpwa_server_server == 'radiusqu'){

                    if (request()->ajax()) {

                       $messages = [];
                       $selectedIds = request()->get('idsel') ?? [];
                       return DataTables::of($messages)
                           ->addColumn('checkbox', function ($row) use ($selectedIds) {
                               $checked = in_array($row['id'], $selectedIds) ? ' checked' : ''; // Periksa apakah ID ada dalam array
                               return '<input type="checkbox" class="row-cb form-check-input" id="checkbox_row' . $row['id'] . '" value="' . $row['id'] . '"' . $checked . ' />';
                           })
                           ->rawColumns(['action', 'checkbox'])
                           ->toJson();
                    }

                        return view('backend.whatsapp.index_new', compact('areas', 'odps', 'mitras', 'statuses', 'whatsapp'));
                }
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }
    }

    public function scan(Request $request)
    {
        $user = Mpwa::where('shortname', multi_auth()->shortname)->first();
        try {
            if($user->mpwa_server_server == 'mpwa'){
                $curl = curl_init();
                $data = [
                    'api_key' => $user->api_key,
                    'device' => $user->sender,
                    'force' => true,
                ];
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($curl, CURLOPT_URL, 'https://' . $user->mpwa_server . '/generate-qr');
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                $response = curl_exec($curl);
                curl_close($curl);
                $result = json_decode($response, true);
                // dd($result);
                if (!isset($result['qrcode'])) {
                     return response()->json([
                         'success' => false,
                         'message' => 'Generate QR gagal, silakan coba lagi',
                     ]);
                 } else {
                     return response()->json([
                         'success' => true,
                         'message' => 'Generate QR berhasil, silakan scan device',
                         'data' => $result['qrcode'],
                    ]);
                }
            }
            if($user->mpwa_server_server == 'radiusqu'){
                $this->start();
                if (!$user->qr_url) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Generate QR gagal, silakan coba lagi',
                    ]);
                } else {
                    return response()->json([
                        'success' => true,
                        'message' => 'Generate QR berhasil, silakan scan device',
                        'data' => $user->qr_url,
                    ]);
                }

            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy(Request $request)
    {
        // $message = WablasMessage::whereIn('id', $request->ids)->delete();
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Data Berhasil Dihapus',
        // ]);
    }
    public function show()
    {
        // $wablas = Wablas::where('group_id', multi_auth()->shortname)->get();
        // return response()->json([
        //     'success' => true,
        //     'data' => $wablas,
        // ]);
    }
    public function update(Request $request, Mpwa $whatsapp)
    {
        // $validator = Validator::make($request->all(), [
        //     'sender' => 'required|string|min:8|max:15|unique:mpwa.setting',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'error' => $validator->errors(),
        //     ]);
        // }
        $user = Mpwa::where('shortname', multi_auth()->shortname)->first();
        if($user->mpwa_server_server == 'mpwa'){
            try {
                $curl = curl_init();
                $data = [
                    'api_key' => $user->api_key,
                    'device' => $request->no_wa,
                    'force' => true,
                ];
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($curl, CURLOPT_URL, 'https://' . $user->mpwa_server . '/generate-qr');
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                $response = curl_exec($curl);
                curl_close($curl);
                $result = json_decode($response, true);
                if (!isset($result['status'])) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Ganti nomor gagal, silakan coba lagi',
                    ]);
                } else {
                    $whatsapp->update([
                        'sender' => $request->no_wa,
                    ]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Ganti nomor berhasil, silakan scan device',
                    ]);
                }
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }
        if($user->mpwa_server_server == 'radiusqu'){
            $whatsapp->update([
                'sender' => $request->no_wa,
            ]);
            $this->start();
            return response()->json([
                'success' => true,
                'message' => 'Ganti nomor berhasil, silakan scan device',
            ]);

        }
    }

    public function getTemplate(Request $request)
    {
        $data = Watemplate::where('shortname', multi_auth()->shortname)->first();
        return response()->json($data);
    }

    public function updateAccountActive(Request $request, Watemplate $id)
    {
        $account_active = preg_replace("/\r\n|\r|\n/", '<br>', $request->account_active);
        $id->update([
            'account_active' => $account_active,
            // 'invoice_overdue' => $request->invoice_overdue,
            // 'payment_paid' => $request->payment_paid,
            // 'payment_cancel' => $request->payment_cancel,
            // 'account_active' => $request->account_active,
            // 'account_suspend' => $request->account_suspend,
        ]);
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $id,
        ]);
    }

    public function updateInvoiceTerbit(Request $request, Watemplate $id)
    {
        $invoice_terbit = preg_replace("/\r\n|\r|\n/", '<br>', $request->invoice_terbit);
        $id->update([
            'invoice_terbit' => $invoice_terbit,
            // 'invoice_overdue' => $request->invoice_overdue,
            // 'payment_paid' => $request->payment_paid,
            // 'payment_cancel' => $request->payment_cancel,
            // 'account_active' => $request->account_active,
            // 'account_suspend' => $request->account_suspend,
        ]);
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $id,
        ]);
    }
    public function updateInvoiceReminder(Request $request, Watemplate $id)
    {
        $invoice_reminder = preg_replace("/\r\n|\r|\n/", '<br>', $request->invoice_reminder);
        $id->update([
            'invoice_reminder' => $invoice_reminder,
        ]);
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $id,
        ]);
    }
    public function updateInvoiceOverdue(Request $request, Watemplate $id)
    {
        $invoice_overdue = preg_replace("/\r\n|\r|\n/", '<br>', $request->invoice_overdue);
        $id->update([
            'invoice_overdue' => $invoice_overdue,
        ]);
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $id,
        ]);
    }
    public function updatePaymentPaid(Request $request, Watemplate $id)
    {
        $payment_paid = preg_replace("/\r\n|\r|\n/", '<br>', $request->payment_paid);
        $id->update([
            'payment_paid' => $payment_paid,
        ]);
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $id,
        ]);
    }
    public function updatePaymentCancel(Request $request, Watemplate $id)
    {
        $payment_cancel = preg_replace("/\r\n|\r|\n/", '<br>', $request->payment_cancel);
        $id->update([
            'payment_cancel' => $payment_cancel,
        ]);
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $id,
        ]);
    }

    public function updateOpenPelanggan(Request $request, Watemplate $id)
    {
        $tiket_open_pelanggan = preg_replace("/\r\n|\r|\n/", '<br>', $request->tiket_open_pelanggan);
        $id->update([
            'tiket_open_pelanggan' => $tiket_open_pelanggan,
        ]);
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $id,
        ]);
    }

    public function updateOpenTeknisi(Request $request, Watemplate $id)
    {
        $tiket_open_teknisi = preg_replace("/\r\n|\r|\n/", '<br>', $request->tiket_open_teknisi);
        $id->update([
            'tiket_open_teknisi' => $tiket_open_teknisi,
        ]);
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $id,
        ]);
    }

    public function updateClosedPelanggan(Request $request, Watemplate $id)
    {
        $tiket_close_pelanggan = preg_replace("/\r\n|\r|\n/", '<br>', $request->tiket_close_pelanggan);
        $id->update([
            'tiket_close_pelanggan' => $tiket_close_pelanggan,
        ]);
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $id,
        ]);
    }

    public function updateClosedTeknisi(Request $request, Watemplate $id)
    {
        $tiket_close_teknisi = preg_replace("/\r\n|\r|\n/", '<br>', $request->tiket_close_teknisi);
        $id->update([
            'tiket_close_teknisi' => $tiket_close_teknisi,
        ]);
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $id,
        ]);
    }

    public function resendMessage(Request $request)
    {
        set_time_limit(0);
        $whatsapp = Mpwa::where('shortname', multi_auth()->shortname)->first();
        if($whatsapp->mpwa_server_server == 'mpwa'){
            $multiCurl = [];
            $result = [];
            $mh = curl_multi_init();

            foreach ($request->ids as $id) {
                $data = [
                    'api_key' => $whatsapp->api_key,
                    'id' => $id,
                ];

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($curl, CURLOPT_URL, 'https://' . $whatsapp->mpwa_server . '/resend-message/api');
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

                curl_multi_add_handle($mh, $curl);
                $multiCurl[] = $curl;
            }

            // Eksekusi semua request secara paralel
            $running = null;
            do {
                curl_multi_exec($mh, $running);
                curl_multi_select($mh);
            } while ($running > 0);

            // Ambil hasil dan tutup masing-masing cURL
            foreach ($multiCurl as $curl) {
                $response = curl_multi_getcontent($curl);
                $result[] = $response;
                curl_multi_remove_handle($mh, $curl);
                curl_close($curl);
            }
            curl_multi_close($mh);
        }
        if($whatsapp->mpwa_server_server == 'mpwa'){

        }

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dikirim ulang',
            'data' => $result,
        ]);
    }
    public function deleteMessage(Request $request)
    {
        set_time_limit(0);
        $whatsapp = Mpwa::where('shortname', multi_auth()->shortname)->first();
        $multiHandle = curl_multi_init();
        $curlHandles = [];
        $responses = [];

        try {
            // Inisialisasi setiap cURL handle untuk setiap id
            foreach ($request->ids as $id) {
                $curl = curl_init();
                $data = [
                    'api_key' => $whatsapp->api_key,
                    'id' => $id,
                ];

                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($curl, CURLOPT_URL, 'https://' . $whatsapp->mpwa_server . '/delete-message/api');
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

                // Simpan handle untuk kemudian diproses dan mapping ke id
                $curlHandles[$id] = $curl;
                curl_multi_add_handle($multiHandle, $curl);
            }

            // Eksekusi semua cURL secara paralel
            $running = null;
            do {
                curl_multi_exec($multiHandle, $running);
                curl_multi_select($multiHandle);
            } while ($running > 0);

            // Ambil respon dari masing-masing handle
            foreach ($curlHandles as $id => $curl) {
                $response = curl_multi_getcontent($curl);
                $responses[$id] = json_decode($response, true);
                // Hapus handle individual
                curl_multi_remove_handle($multiHandle, $curl);
                curl_close($curl);
            }

            curl_multi_close($multiHandle);

            return response()->json([
                'success' => true,
                'message' => 'Pesan berhasil dihapus',
                'responses' => $responses, // Optional: untuk menampilkan respon tiap request
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // public function getUser(Request $request)
    // {
    //     $countuserbyArea = Member::where('group_id', multi_auth()->shortname)
    //         ->where('kode_area', $request->kode_area)
    //         ->count();
    //     $userbyArea = Member::where('group_id', multi_auth()->shortname)
    //         ->where('kode_area', $request->kode_area)
    //         ->select('kode_area', 'wa')
    //         ->get();
    //     return response()->json([
    //         'success' => true,
    //         'count' => $countuserbyArea,
    //         'data' => $userbyArea,
    //     ]);
    // }

    public function getAllUserActive()
    {
        $countuser = PppoeUser::where('shortname', multi_auth()->shortname)->where('status', 1)->count();
        $user = PppoeUser::where('shortname', multi_auth()->shortname)->where('status', 1)->select('wa')->get();
        return response()->json([
            'success' => true,
            'countuser' => $countuser,
            'data' => $user,
        ]);
    }

    public function getAllUserActive_owner()
    {
        $countuser = User::where('role','Admin')->where('status', 1)->count();
        $user = User::where('role','Admin')->where('status', 1)->select('whatsapp')->get();
        return response()->json([
            'success' => true,
            'countuser' => $countuser,
            'data' => $user,
        ]);
    }

    public function getAllUserTrial_owner()
    {
        $countuser = User::where('role','Admin')->where('license_id', 1)->count();
        $user = User::where('role','Admin')->where('license_id', 1)->select('whatsapp')->get();
        return response()->json([
            'success' => true,
            'countuser' => $countuser,
            'data' => $user,
        ]);
    }


    public function getAllUserExpired_owner()
    {
        $countuser = User::where('role','Admin')->where('status', 3)->whereNot('license_id', 1)->count();
        $user = User::where('role','Admin')->where('status', 3)->whereNot('license_id', 1)->select('whatsapp')->get();
        return response()->json([
            'success' => true,
            'countuser' => $countuser,
            'data' => $user,
        ]);
    }


    public function getAllUserSuspend()
    {
        $countuser = PppoeUser::where('shortname', multi_auth()->shortname)->where('status', 2)->count();
        $user = PppoeUser::where('shortname', multi_auth()->shortname)->where('status', 2)->select('wa')->get();
        return response()->json([
            'success' => true,
            'countuser' => $countuser,
            'data' => $user,
        ]);
    }

    public function getAllUserArea(Request $request)
    {
        $countuser = PppoeUser::where('shortname', multi_auth()->shortname)->where('kode_area', $request->kode_area)->count();
        $user = PppoeUser::where('shortname', multi_auth()->shortname)->where('kode_area', $request->kode_area)->select('wa')->get();
        return response()->json([
            'success' => true,
            'countuser' => $countuser,
            'data' => $user,
        ]);
    }

    public function getAllUserOdp(Request $request)
    {
        $countuser = PppoeUser::where('shortname', multi_auth()->shortname)->where('kode_odp', $request->kode_odp)->count();
        $user = PppoeUser::where('shortname', multi_auth()->shortname)->where('kode_odp', $request->kode_odp)->select('wa')->get();
        return response()->json([
            'success' => true,
            'countuser' => $countuser,
            'data' => $user,
        ]);
    }

    public function sendBroadcast(Request $request)
    {
        $user = Mpwa::where('shortname', multi_auth()->shortname)->first();
        try {
            if($user->mpwa_server_server == 'mpwa'){

                $curl = curl_init();
                $data = [
                    'api_key' => $user->api_key,
                    'sender' => $user->sender,
                    'number' => implode('|', $request->wa),
                    'message' => $request->message,
                ];
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($curl, CURLOPT_URL, 'https://' . $user->mpwa_server . '/send-message');
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                $response = curl_exec($curl);
                curl_close($curl);
                $result = json_decode($response, true);
                sleep(5);
                if ($result['status'] = true) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Pesan broadcast berhasil terkirim',
                    ]);
                }
            }
            if($user->mpwa_server_server == 'radiusqu'){
                $list_wa = $request->wa;
                foreach ($list_wa as $lwa){
                    $user_login = User::where('id', multi_auth()->id)->first();
                    $_id = $user_login->whatsapp."_".env('APP_ENV');
                    $nomorhp = gantiformat_hp($lwa);
                    $url = env('WHATSAPP_URL_NEW')."send-message/".$_id;
                    $params = array(
                        "jid" => $nomorhp."@s.whatsapp.net",
                        "content" => array(
                            "text" => $request->message,
                        )
                    );
                    $this->makeRequest($url, "POST", $params);

                }
                sleep(5);
                return response()->json([
                    'success' => true,
                    'message' => 'Pesan broadcast berhasil terkirim',
                ]);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function start(){
        $this->hentikan_whatsapp();
        $this->hapus_whatsapp();
        $this->tambah_whatsapp();
        $this->start_whatsapp();
        return response()->json(['message' => 'WhatsApp Start Successfully'], 201);
    }

    protected function hentikan_whatsapp(){
        $_id = multi_auth()->whatsapp."_".env('APP_ENV');
        $url = env('WHATSAPP_URL_NEW')."close/".$_id;
        $response = $this->makeRequest($url, "POST");
        return $response;
    }

    protected function hapus_whatsapp(){
        $_id = multi_auth()->whatsapp."_".env('APP_ENV');
        $url = env('WHATSAPP_URL_NEW').'/'.$_id;
        $params = [];
        $response = $this->makeRequest($url, "DELETE", $params);
        return $response;
    }


    protected function tambah_whatsapp(){
        $_id = multi_auth()->whatsapp."_".env('APP_ENV');
        $url = env('WHATSAPP_URL_NEW');
        $params = array(
            "_id" => $_id,
            "connectionUpdateWebhook" => env('APP_URL').'notification/whatsapp/receive_qr_admin/'.multi_auth()->id, //env('APP_URL').'notification/whatsapp/receive_qr_admin/'.multi_auth()->id,
            "messagesUpsertWebhook" => env('APP_URL').'notification/whatsapp/receive_message_admin/'.multi_auth()->id //env('APP_URL').'notification/whatsapp/receive_message_admin/'.multi_auth()->id
        );
        $response = $this->makeRequest($url, "POST", $params);
        Log::info("daftar wa radius session => ".$_id);
        return $response;
    }


    protected function start_whatsapp(){
        $_id = multi_auth()->whatsapp."_".env('APP_ENV');
        $url = env('WHATSAPP_URL_NEW')."start/".$_id;
        $response = $this->makeRequest($url, "POST");
        return $response;
    }

    protected function makeRequest($url, $method="GET", $params = []){
        try {
            $data = null;
            $response = null;
            if($method == "GET"){
                $response = Http::get($url, $params);
            }
            if($method == "POST"){
                $response = Http::post($url, $params);
            }
            if($method == "DELETE"){
                $response = Http::delete($url);
            }
            if($method == "PATCH"){
                $response = Http::patch($url, $params);
            }
            if($method == "PUT"){
                $response = Http::put($url, $params);
            }
            if ($response->successful()) {
                $data = $response->json();
            }
            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
