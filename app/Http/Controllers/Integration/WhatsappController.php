<?php

namespace App\Http\Controllers\Integration;

use App\Models\Area;
use App\Models\Member;
use App\Models\Wablas;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\WablasMessage;
use App\Models\WablasTemplate;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class WhatsappController extends Controller
{
    public function index(Request $request)
    {
        $license = ($request->user()->load('license'))->license;

        if (!$license || !$license->whatsapp) {
            if ($request->user()->role !== 'Admin') {
                return redirect()->to('/')->with('error', 'Lisensi anda tidak mendukung fitur ini, silakan hubungi admin anda.');
            }

            return redirect()->route('admin.account.info')->with('error', 'Lisensi anda tidak mendukung fitur ini, silakan untuk mengupgrade lisensi anda.');
        }

        $areas = Area::where('group_id', $request->user()->id_group)
            ->select('id', 'kode_area', 'deskripsi')
            ->orderBy('kode_area', 'desc')
            ->get();

        if (request()->ajax()) {
            $message = WablasMessage::query()->where('group_id', $request->user()->id_group);
            $selectedIds = request()->get('idsel') ?? [];
            return DataTables::of($message)
                ->addColumn('checkbox', function ($row) use ($selectedIds) {
                    $checked = in_array($row->id, $selectedIds) ? ' checked' : ''; // Periksa apakah ID ada dalam array
                    return '<input type="checkbox" class="row-cb form-check-input" id="checkbox_row' . $row->id . '" value="' . $row->id . '"' . $checked . ' />';
                })
                ->rawColumns(['action', 'checkbox'])
                ->addColumn('action', function ($row) {
                    if ($row->status === 'pending') {
                        return '<center><a href="javascript:void(0)" id="sendMessage" data-id="' .
                            $row->id .
                            '" data-wa="' .
                            $row->phone .
                            '" data-message="' .
                            $row->message .
                            '"><i class="fas fa-paper-plane text-warning"></i>
                </a></center>';
                    } elseif ($row->status === 'success') {
                        return '<center><a href="javascript:void(0)"><i class="fas fa-circle-check text-success"></i>
                </a></center>';
                    } else {
                        return '<center><a href="javascript:void(0)" id="sendMessage" data-id="' .
                            $row->id .
                            '" data-wa="' .
                            $row->phone .
                            '" data-message="' .
                            $row->message .
                            '"><i class="fas fa-paper-plane text-warning"></i>
                </a></center>';
                    }
                })
                ->toJson();
        }

        $wablas = Wablas::where('group_id', $request->user()->id_group)
            ->select('token', 'sender')
            ->first();
        $curl = curl_init();
        $data = [
            'api_key' => $wablas->token,
            'sender' => $wablas->sender,
        ];
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_URL, config('services.whatsapp.url') . '/info-device');
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($response, true);

        return view('integrations.whatsapp.index', compact('result', 'areas'));
    }

    public function destroy(Request $request)
    {
        $message = WablasMessage::whereIn('id', $request->ids)->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }
    public function show(Request $request)
    {
        $wablas = Wablas::where('group_id', $request->user()->id_group)->get();
        return response()->json([
            'success' => true,
            'data' => $wablas,
        ]);
    }
    public function update(Request $request, Wablas $whatsapp)
    {
        $validator = Validator::make($request->all(), [
            'sender' => 'required|string|min:8|max:15|unique:db_wablas.setting',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }
        $curl = curl_init();
        $data = [
            'api_key' => $request->token,
            'sender' => '081222339257',
            'sender_klien' => $request->sender,
        ];
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_URL, config('services.whatsapp.url') . '/create-device');
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($response, true);

        $sender = $result['data']['body'];
        $whatsapp->update([
            'sender' => $sender,
            'token' => '6WI9PiH8if9AkYZIhtvTbceaIjih0a',
        ]);
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Nomor Pengirim Berhasil Diganti',
            'data' => $whatsapp,
        ]);
    }

    public function scan(Request $request)
    {
        $curl = curl_init();
        $data = [
            'api_key' => $request->token,
            'device' => $request->sender,
            'force' => true,
        ];
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_URL, config('services.whatsapp.url') . '/generate-qr');
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($response, true);
        if (!isset($result['qrcode'])) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf Device Sudah Terhubung',
            ]);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Generate QR Berhasil. Silakan Scan Device',
                'data' => $result['qrcode'],
            ]);
        }
    }

    public function logout(Request $request)
    {
        $curl = curl_init();
        $data = [
            'api_key' => $request->token,
            'sender' => $request->sender,
        ];
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_URL, config('services.whatsapp.url') . '/logout-device');
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($response, true);
        return response()->json([
            'success' => true,
            'message' => 'Device Berhasil Dilogout',
        ]);
    }

    public function getTemplate(Request $request)
    {
        $data = WablasTemplate::where('group_id', $request->user()->id_group)->get();
        return response()->json($data);
    }

    public function updateAccountActive(Request $request, WablasTemplate $id)
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

    public function updateInvoiceTerbit(Request $request, WablasTemplate $id)
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
    public function updateInvoiceReminder(Request $request, WablasTemplate $id)
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
    public function updateInvoiceOverdue(Request $request, WablasTemplate $id)
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
    public function updatePaymentPaid(Request $request, WablasTemplate $id)
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
    public function updatePaymentCancel(Request $request, WablasTemplate $id)
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

    public function resendMessage(Request $request)
    {
        $wablas = Wablas::where('group_id', $request->user()->id_group)
            ->select('token', 'sender')
            ->first();
        $data = [
            'api_key' => $wablas->token,
            'sender' => $wablas->sender,
            'number' => $request->wa,
            'message' => str_replace('<br>', "\n", $request->message),
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_URL, config('services.whatsapp.url') . '/send-message');
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($curl);
        $result = json_decode($response, true);
        curl_close($curl);

        foreach ($result['data'] as $idm) {
            $sukses = WablasMessage::where('id_message', $idm['note'])->update([
                'status' => 'success'
            ]);
            $gagal = WablasMessage::whereNot('id_message', $idm['note']->update([
                'status' => 'failed',
            ]));
        }

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dikirim ulang',
        ]);
    }

    public function getUser(Request $request)
    {
        $countuserbyArea = Member::where('group_id', $request->user()->id_group)
            ->where('kode_area', $request->kode_area)
            ->count();
        $userbyArea = Member::where('group_id', $request->user()->id_group)
            ->where('kode_area', $request->kode_area)
            ->select('kode_area', 'wa')
            ->get();
        return response()->json([
            'success' => true,
            'count' => $countuserbyArea,
            'data' => $userbyArea,
        ]);
    }

    public function getUserAll(Request $request)
    {
        $countuser = Member::where('group_id', $request->user()->id_group)->count();
        $countarea = Area::where('group_id', $request->user()->id_group)->count();
        $userAll = Member::where('group_id', $request->user()->id_group)
            ->select('wa')
            ->get();

        return response()->json([
            'success' => true,
            'countuser' => $countuser,
            'countarea' => $countarea,
            'data' => $userAll,
        ]);
    }

    public function sendBroadcast(Request $request)
    {
        if ($request->tipe === 'all') {
            // $pesan = [];
            // foreach ($request->wa as $wa) {
            //     $draw = [
            //         'group_id' => $request->user()->id_group,
            //         'id_message' => Str::random(30),
            //         'subject' => $request->subject_all,
            //         'message' => $request->message_all,
            //         'phone' => $wa,
            //         'status' => 'pending',
            //         'created_at' => Carbon::now(),
            //         'updated_at' => Carbon::now(),
            //     ];
            //     $pesan[] = $draw;
            //     $id_message[] = $draw['id_message'];
            // }
            // $save = WablasMessage::insert($pesan);

            $curl = curl_init();
            $wablas = Wablas::where('group_id', $request->user()->id_group)
                ->select('token', 'sender')
                ->first();
            $data = [
                'api_key' => $wablas->token,
                'sender' => $wablas->sender,
                'number' => implode('|', $request->wa),
                'message' => $request->message_all,
            ];
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($curl, CURLOPT_URL, config('services.whatsapp.url') . '/send-message');
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

            $response = curl_exec($curl);
            curl_close($curl);
            $result = json_decode($response, true);
            $pesan = [];
            foreach ($result['data'] as $row) {
                $draw = [
                    'group_id' => $request->user()->id_group,
                    'id_message' => $row['note'],
                    'subject' => $request->subject_all,
                    'message' => $request->message_all,
                    'phone' => $row['number'],
                    'status' => $row['status'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $pesan[] = $draw;
            }
            $save = WablasMessage::insert($pesan);

            return response()->json([
                'success' => true,
                'message' => 'Pesan Berhasil Terkirim ke ' . $result['count'] . ' Pelanggan',
            ]);
        } else {
            // $pesan = [];
            // foreach ($request->wa as $wa) {
            //     $draw = [
            //         'group_id' => $request->user()->id_group,
            //         'id_message' => Str::random(30),
            //         'subject' => $request->subject_area,
            //         'message' => $request->message_area,
            //         'phone' => $wa,
            //         'status' => 'pending',
            //         'created_at' => Carbon::now(),
            //         'updated_at' => Carbon::now(),
            //     ];
            //     $pesan[] = $draw;
            // }
            // $save = WablasMessage::insert($pesan);

            $wablas = Wablas::where('group_id', $request->user()->id_group)
                ->select('token', 'sender')
                ->first();
            $data = [
                'api_key' => $wablas->token,
                'sender' => $wablas->sender,
                'number' => implode('|', $request->wa),
                'message' => $request->message_area,
            ];
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($curl, CURLOPT_URL, config('services.whatsapp.url') . '/send-message');
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

            $response = curl_exec($curl);
            curl_close($curl);
            $result = json_decode($response, true);
            $pesan = [];
            foreach ($result['data'] as $row) {
                $draw = [
                    'group_id' => $request->user()->id_group,
                    'id_message' => $row['note'],
                    'subject' => $request->subject_area,
                    'message' => $request->message_area,
                    'phone' => $row['number'],
                    'status' => $row['status'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $pesan[] = $draw;
            }
            $save = WablasMessage::insert($pesan);

            return response()->json([
                'success' => true,
                'message' => 'Pesan Berhasil Terkirim ke ' . $result['count'] . ' Pelanggan',
            ]);
        }
    }

    public function resend(Request $request)
    {
        $pending = WablasMessage::where('group_id', $request->user()->id_group)->whereIn('id', $request->ids)->get();
        foreach ($pending as $row) {
            $wablas = Wablas::where('group_id', $row->group_id)
                ->select('token', 'sender')
                ->first();
            $data = [
                'api_key' => $wablas->token,
                'sender' => $wablas->sender,
                'number' => $row->phone,
                'message' => str_replace('<br>', "\n", $row->message),
                'id_message' => $row->id_message,
            ];
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($curl, CURLOPT_URL, config('services.whatsapp.url') . '/send-message');
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

            $response = curl_exec($curl);
            curl_close($curl);
            $result = json_decode($response, true);
            foreach ($result['data'] as $idm) {
                $sukses = WablasMessage::where('id_message', $idm['note'])->update([
                    'status' => 'success'
                ]);
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'Pesan pending berhasil dikirim ulang',
        ]);
    }
}
