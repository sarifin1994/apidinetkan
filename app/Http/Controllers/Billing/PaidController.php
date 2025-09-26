<?php

namespace App\Http\Controllers\Billing;

use App\Models\Member;
use App\Models\Wablas;
use App\Models\Invoice;
use App\Models\PppoeUser;
use App\Models\RadiusNas;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Models\WablasMessage;
use App\Models\BillingSetting;
use App\Models\WablasTemplate;
use Illuminate\Support\Carbon;
use App\Enums\TransactionTypeEnum;
use App\Http\Controllers\Controller;
use App\Enums\TransactionCategoryEnum;
use Illuminate\Support\Facades\Process;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;

class PaidController extends Controller
{
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $invoices = Invoice::query()
                ->where('invoice.group_id', $request->user()->id_group)
                ->where('status', 1)
                ->with('member', 'pppoe', 'service');
            return DataTables::of($invoices)
                ->addIndexColumn()
                ->editColumn('member_full_name', function ($row) {
                    return $row->member->full_name;
                })
                ->editColumn('member_kode_area', function ($row) {
                    return $row->pppoe->kode_area;
                })
                ->editColumn('price', function ($row) {
                    $price = is_numeric($row->price) ? $row->price : 0;
                    $ppn = is_numeric($row->ppn) ? $row->ppn : 0;
                    $discount = is_numeric($row->discount) ? $row->discount : 0;

                    $amount_ppn = ($price * $ppn) / 100;
                    $amount_discount = ($price * $discount) / 100;

                    if ($discount === null || $discount == 0) {
                        return $price + $amount_ppn;
                    } elseif ($ppn === null || $ppn == 0) {
                        return $price - $amount_discount;
                    } else {
                        return $price + $amount_ppn - $amount_discount;
                    }
                })
                ->addColumn('action', function ($row) {
                    $url = config('app.url');
                    $nowa = $row->member->wa ?? '081222339257';

                    if (!preg_match('/[^+0-9]/', trim($nowa))) {
                        // Check if the first two characters are "62"
                        if (substr(trim($nowa), 0, 2) == '62') {
                            $wa = trim($nowa);
                        } elseif (substr(trim($nowa), 0, 1) == '0') {
                            // Replace leading '0' with '62'
                            $wa = '62' . substr(trim($nowa), 1);
                        } else {
                            $wa = '';
                        }
                    } else {
                        $wa = '';
                    }

                    return '<a href="javascript:void(0)" id="undopay"
                    data-id="' . $row->id . '" data-ppp="' . $row->service->pppoe_id . '"
                    class="badge b-ln-height badge-danger">
                        <i class="fas fa-undo"></i>
                    </a>

                    <a href="https://wa.me/' . $wa . '" target="_blank" class="badge b-ln-height badge-success">
                        <i class="fab fa-whatsapp"></i>
                    </a>

                    <a href="' . route('invoice.pay', $row->no_invoice) . '" target="_blank" class="badge b-ln-height badge-warning">
                        <i class="fas fa-bank"></i>
                    </a>

                    <a href="/invoice/pdf/' . $row->no_invoice . '" target="_blank" id="print" class="badge b-ln-height badge-secondary">
                        <i class="fas fa-print"></i>
                    </a>';
                })
                ->toJson();
        }

        return view('billing.paid.index');
    }

    public function undopayInvoice(Request $request, Invoice $invoice)
    {
        if ($request->payment_type === 'Prabayar') {
            $next_due = Carbon::createFromFormat('Y-m-d', $request->next_due)->subMonthsWithNoOverflow(1);
            $next_invoice = $request->next_due;
        } elseif ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Fixed Date') {
            $next_due = Carbon::createFromFormat('Y-m-d', $request->next_due)->subMonthsWithNoOverflow(1);
            $next_invoice = Carbon::createFromFormat('Y-m-d', $request->next_due)->subMonthsWithNoOverflow(1);
        } elseif ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Billing Cycle') {
            $due_bc = BillingSetting::where('group_id', $request->user()->id_group)
                ->select('due_bc')
                ->first();
            $next_due = Carbon::createFromFormat('Y-m-d', $request->next_due)
                ->setDay($due_bc->due_bc)
                ->subMonths(1);
            $next_invoice = Carbon::createFromFormat('Y-m-d', $request->next_due)
                ->startOfMonth()
                ->subMonths(1);
        }

        $invoice->update([
            'next_due' => $next_due,
            'status' => 0,
        ]);

        activity()
            ->tap(function (Activity $activity) use ($request) {
                $activity->group_id = $request->user()->id_group;
            })
            ->event('Update')
            ->log('Cancel Invoice: ' . $request->no_invoice . ' a.n ' . $request->full_name . '');

        $transaction = Transaksi::where('group_id', $request->user()->id_group)
            ->where('invoice_id', $invoice->id)
            ->delete();

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Invoice Berhasil Dibatalkan',
        ]);
    }

    public function undopayInvoiceWA(Request $request, Invoice $invoice)
    {
        $billing = BillingSetting::where('group_id', $request->user()->id_group)
            ->select('notif_ps')
            ->first();
        if ($billing->notif_ps === 1) {
            $wa = $request->no_wa;
            if ($wa !== null) {
                $get_periode = date('Y-m-d', strtotime($request->periode));
                $periode_format = indonesiaDateFormat($get_periode);
                $amount_format = number_format($request->amount, 0, ',', '.');
                $total_format = number_format($request->payment_total, 0, ',', '.');
                $due_date_format = date('d/m/Y', strtotime($request->due_date));
                $invoice_date_format = date('d/m/Y', strtotime($request->invoice_date));
                if ($request->payment_method === '1') {
                    $payment_method = 'Cash';
                } else {
                    $payment_method = 'Transfer';
                }
                $template = WablasTemplate::where('group_id', $request->user()->id_group)
                    ->select('payment_cancel')
                    ->first()->payment_cancel;
                $shortcode = ['[nama_lengkap]', '[id_pelanggan]', '[username]', '[password]', '[paket_internet]', '[no_invoice]', '[tgl_invoice]', '[jumlah]', '[ppn]', '[discount]', '[total]', '[periode]', '[jth_tempo]', '[metode_pembayaran]', '[payment_midtrans]'];
                $source = [$request->full_name, $request->id_member, $request->pppoe_user, $request->pppoe_pass, $request->pppoe_profile, $request->no_invoice, $invoice_date_format, $amount_format, $request->ppn, $request->discount, $total_format, $periode_format, $due_date_format, $payment_method, $request->payment_url];
                $message = str_replace($shortcode, $source, $template);
                $message_format = str_replace('<br>', "\n", $message);

                $curl = curl_init();
                $wablas = Wablas::where('group_id', $request->user()->id_group)
                    ->select('token', 'sender')
                    ->first();
                $data = [
                    'api_key' => $wablas->token,
                    'sender' => $wablas->sender,
                    'number' => $wa,
                    'message' => $message_format,
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
                        'subject' => 'INVOICE CANCEL #' . $invoice->no_invoice,
                        'message' => preg_replace("/\r\n|\r|\n/", '<br>', $message),
                        'phone' => $row['number'],
                        'status' => $row['status'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    $pesan[] = $draw;
                }
                $save = WablasMessage::insert($pesan);
            }
        }

        //return response
        return response()->json([
            'success' => true,
            'message' => 'WA Berhasil Terkirim',
        ]);
    }
}
