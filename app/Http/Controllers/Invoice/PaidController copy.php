<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Pppoe\PppoeProfile;
use App\Models\Pppoe\PppoeUser;
use App\Models\SmtpSetting;
use App\Models\User;
use App\Services\CustomMailerService;
use Illuminate\Http\Request;
use App\Models\Invoice\Invoice;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Setting\BillingSetting;
use App\Models\Whatsapp\Mpwa;
use App\Models\Whatsapp\Watemplate;
use App\Models\Keuangan\Transaksi;
use App\Models\Keuangan\TransaksiMitra;
use App\Models\Partnership\Mitra;
use App\Exports\InvoiceExport;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Activitylog\Contracts\Activity;

class PaidController extends Controller
{
    public function index()
    {
        $ppp = PppoeUser::where('shortname', multi_auth()->shortname)->select('id', 'id_pelanggan', 'full_name')->get();

        if (request()->ajax()) {
            $periodestr = request()->get('periode');
            if ($periodestr === null) {
                if (multi_auth()->role !== 'Mitra') {
                    $totalpaid = Invoice::query()->where('shortname', multi_auth()->shortname)->where('status', 'paid')->count();
                    $totalnominal = Invoice::query()->where('shortname', multi_auth()->shortname)->where('status', 'paid')->sum('price');
                    $totalkomisi = Invoice::query()->where('shortname', multi_auth()->shortname)->where('status', 'paid')->sum('komisi');
                    $invoices = Invoice::query()->select('invoice.*', 'user_pppoe.full_name as full_name', 'user_pppoe.kode_area as kode_area')->join('frradius_auth.user_pppoe as user_pppoe', 'invoice.id_pelanggan', '=', 'user_pppoe.id')->where('invoice.shortname', multi_auth()->shortname)->where('invoice.status', 'paid');
                } else {
                    $totalpaid = Invoice::query()->where('shortname', multi_auth()->shortname)->where('status', 'paid')->where('invoice.mitra_id', multi_auth()->id)->count();
                    $totalnominal = Invoice::query()->where('shortname', multi_auth()->shortname)->where('status', 'paid')->where('invoice.mitra_id', multi_auth()->id)->sum('price');
                    $totalkomisi = Invoice::query()->where('shortname', multi_auth()->shortname)->where('status', 'paid')->where('invoice.mitra_id', multi_auth()->id)->sum('komisi');
                    $invoices = Invoice::query()->select('invoice.*', 'user_pppoe.full_name as full_name', 'user_pppoe.kode_area as kode_area')->join('frradius_auth.user_pppoe as user_pppoe', 'invoice.id_pelanggan', '=', 'user_pppoe.id')->where('invoice.shortname', multi_auth()->shortname)->where('invoice.status', 'paid')->where('invoice.mitra_id', multi_auth()->id);
                }
            } else {
                $month = date('m', strtotime($periodestr));
                $year = date('Y', strtotime($periodestr));
                if (multi_auth()->role !== 'Mitra') {
                    $totalpaid = Invoice::query()->where('shortname', multi_auth()->shortname)->whereMonth('due_date', $month)->whereYear('due_date', $year)->where('status', 'paid')->count();
                    $totalnominal = Invoice::query()->where('shortname', multi_auth()->shortname)->whereMonth('due_date', $month)->whereYear('due_date', $year)->where('status', 'paid')->sum('price');
                    $totalkomisi = Invoice::query()->where('shortname', multi_auth()->shortname)->whereMonth('due_date', $month)->whereYear('due_date', $year)->where('status', 'paid')->sum('komisi');
                    $invoices = Invoice::query()->select('invoice.*', 'user_pppoe.full_name as full_name', 'user_pppoe.kode_area as kode_area')->join('frradius_auth.user_pppoe as user_pppoe', 'invoice.id_pelanggan', '=', 'user_pppoe.id')->where('invoice.shortname', multi_auth()->shortname)->whereMonth('invoice.due_date', $month)->whereYear('invoice.due_date', $year)->where('invoice.status', 'paid');
                } else {
                    $totalpaid = Invoice::query()->where('shortname', multi_auth()->shortname)->whereMonth('due_date', $month)->whereYear('due_date', $year)->where('status', 'paid')->where('invoice.mitra_id', multi_auth()->id)->count();
                    $totalnominal = Invoice::query()->where('shortname', multi_auth()->shortname)->whereMonth('due_date', $month)->whereYear('due_date', $year)->where('status', 'paid')->where('invoice.mitra_id', multi_auth()->id)->sum('price');
                    $totalkomisi = Invoice::query()->where('shortname', multi_auth()->shortname)->whereMonth('due_date', $month)->whereYear('due_date', $year)->where('status', 'paid')->where('invoice.mitra_id', multi_auth()->id)->sum('komisi');
                    $invoices = Invoice::query()->select('invoice.*', 'user_pppoe.full_name as full_name', 'user_pppoe.kode_area as kode_area')->join('frradius_auth.user_pppoe as user_pppoe', 'invoice.id_pelanggan', '=', 'user_pppoe.id')->where('invoice.shortname', multi_auth()->shortname)->whereMonth('invoice.due_date', $month)->whereYear('invoice.due_date', $year)->where('invoice.status', 'paid')->where('invoice.mitra_id', multi_auth()->id);
                }
            }
            $selectedIds = request()->get('idsel') ?? [];
            return DataTables::of($invoices)
                ->with(['totalpaid' => $totalpaid, 'totalnominal' => $totalnominal,'totalkomisi' => $totalkomisi])
                ->filterColumn('full_name', function ($query, $keyword) {
                    $sql = 'LOWER(user_pppoe.full_name) like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->filterColumn('kode_area', function ($query, $keyword) {
                    $sql = 'LOWER(user_pppoe.kode_area) like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->addColumn('checkbox', function ($row) use ($selectedIds) {
                    $checked = in_array($row['id'], $selectedIds) ? ' checked' : ''; // Periksa apakah ID ada dalam array
                    return '<input type="checkbox" class="row-cb form-check-input" id="checkbox_row' . $row['id'] . '" value="' . $row['id'] . '"' . $checked . ' />';
                })
                ->addColumn('total', function ($row) {
                    $amount_ppn = ($row->price * $row->ppn) / 100;
                    $amount_discount = ($row->price * $row->discount) / 100;
                    if ($row->discount === null) {
                        return $total_plus_ppn = $row->price + $amount_ppn;
                    } elseif ($row->ppn === null) {
                        return $total_plus_discount = $row->price - $amount_discount;
                    } else {
                        return $total_plus_ppn_discount = $row->price + $amount_ppn - $amount_discount;
                    }
                })
                ->addColumn('action', function ($row) {
                    return '
                <a href="javascript:void(0)" id="unpay"
                data-id="' .
                        $row->id .
                        '" data-pelanggan_id="' .
                        $row->id_pelangan .
                        '" class="btn btn-primary text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                    <span class="material-symbols-outlined">delete_history</span> UNPAY
            </a>
            <a href="javascript:void(0)" id="delete" data-id="' .
                        $row->id .
                        '" class="btn btn-danger text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
            <span class="material-symbols-outlined">delete</span>
            </a>';
                    // <a href="' .
                    //             $row->payment_url .
                    //             '" target="_blank" data-id="' .
                    //             $row->id .
                    //             '" class="btn btn-primary text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                    // <span class="material-symbols-outlined">captive_portal</span>
                    // </a>
                })
                ->rawColumns(['action', 'checkbox'])
                ->toJson();
        }
        return view('backend.invoice.paid.index', compact('ppp'));
    }

    public function getPaid(Request $request)
    {
        $invoice = Invoice::with('rpppoe')->where('id', $request->id)->first();
        return response()->json($invoice);
    }

    public function unpayInvoice(Request $request, Invoice $invoice)
    {
        function tgl_indo($tanggal)
        {
            $bulan = [
                1 => 'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember',
            ];
            $pecahkan = explode('-', $tanggal);
            return $bulan[(int) $pecahkan[1]] . ' ' . $pecahkan[0];
        }

        if ($request->payment_type === 'Prabayar') {
            $next_due = Carbon::createFromFormat('Y-m-d', $request->next_due)->subMonthsWithNoOverflow(1);
            // $next_invoice = $request->next_due;
        } elseif ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Fixed Date') {
            $next_due = Carbon::createFromFormat('Y-m-d', $request->next_due)->subMonthsWithNoOverflow(1);
            // $next_invoice = Carbon::createFromFormat('Y-m-d', $request->next_due)->subMonthsWithNoOverflow(1);
        } elseif ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Billing Cycle') {
            $due_bc = BillingSetting::where('shortname', multi_auth()->shortname)->select('due_bc')->first();
            $next_due = Carbon::createFromFormat('Y-m-d', $request->next_due)->setDay($due_bc->due_bc)->subMonths(1);
            // $next_invoice = Carbon::createFromFormat('Y-m-d', $request->next_due)
            //     ->startOfMonth()
            //     ->subMonths(1);
        }

        $invoice->update([
            'paid_date' => null,
            'status' => 'unpaid',
        ]);

        if ($invoice) {
            $pelanggan = PppoeUser::where('id', $request->pelanggan_id);
            $pelanggan->update([
                'next_due' => $next_due,
            ]);

            $transaction = Transaksi::where('shortname', multi_auth()->shortname)->where('kategori', 'Invoice')->where('id_data', $invoice->id)->delete();
            $transaction_mitra = TransaksiMitra::where('shortname', multi_auth()->shortname)->where('kategori', 'Komisi')->where('id_data', $invoice->id)->delete();

            $notif_ps = BillingSetting::where('shortname', multi_auth()->shortname)->first()->notif_ps;
            if ($notif_ps === 1 && $request->wa !== null) {
                $amount_format = number_format($request->amount, 0, '.', '.');
                $total_format = number_format($request->payment_total, 0, '.', '.');
                $invoice_date_format = Carbon::parse($request->invoice_date)->translatedFormat('d F Y');
                $due_date_format = Carbon::parse($request->due_date)->translatedFormat('d F Y');
                $get_periode = date('Y-m-d', strtotime($request->period));
                $periode_format = tgl_indo($get_periode);
                $mpwa = Mpwa::where('shortname', multi_auth()->shortname)->first();
                $paid_date_format = date('d/m/Y', strtotime($invoice->paid_date));
                $row = PppoeUser::where('id', $invoice->id_pelanggan)->with('rprofile')->first();
                // $inv = Invoice::where('id',$request->invoice_id)->first();
                $shortcode = ['[nama_lengkap]', '[id_pelanggan]', '[alamat]', '[paket_internet]', '[tipe_pembayaran]', '[siklus_tagihan]', '[no_invoice]', '[tgl_invoice]', '[harga]', '[ppn]', '[discount]', '[total]', '[jth_tempo]', '[periode]', '[tgl_bayar]', '[subscribe]', '[link_pembayaran]'];
                $source = [$row->full_name, $row->id_pelanggan, $row->address, $row->profile, $request->payment_type, $request->billing_period, $request->no_invoice, $invoice_date_format, $amount_format, $request->ppn, $request->discount, $total_format, $due_date_format, $periode_format, $paid_date_format, $request->subscribe, $request->payment_method];
                $template = Watemplate::where('shortname', $row->shortname)->first()->payment_cancel;
                $message = str_replace($shortcode, $source, $template);
                $message_format = str_replace('<br>', "\n", $message);

                if($mpwa->mpwa_server_server == 'mpwa'){
                    try {
                        $curl = curl_init();
                        $data = [
                            'api_key' => $mpwa->api_key,
                            'sender' => $mpwa->sender,
                            'number' => $row->wa,
                            'message' => $message_format,
                        ];
                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                        curl_setopt($curl, CURLOPT_URL, 'https://' . $mpwa->mpwa_server . '/send-message');
                        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                        $response = curl_exec($curl);
                        curl_close($curl);
                        // $result = json_decode($response, true);
                    } catch (\Exception $e) {
                        return $e->getMessage();
                    }
                }
                if($mpwa->mpwa_server_server == 'radiusqu' && $mpwa->is_login == 1){
                    $nomorhp = gantiformat_hp($row->wa);
                    $user_wa = User::where('shortname', $mpwa->shortname)->first();
                    $_id = $user_wa->whatsapp."_".env('APP_ENV');
                    $apiUrl = env('WHATSAPP_URL_NEW')."send-message/".$_id; //env('CACTI_ENDPOINT').'cacti/logout/'.$_id;
                    try {
                        $params = array(
                            "jid" => $nomorhp."@s.whatsapp.net",
                            "content" => array(
                                "text" => $message_format
                            )
                        );
                        // Kirim POST request ke API eksternal
                        Http::post($apiUrl, $params);

                    } catch (\Exception $e) {
                    }
                }

                // send email
                $smtp = SmtpSetting::where('shortname', multi_auth()->shortname)->first();
                if($smtp){
                    try{
                        $data = [
                            'messages' => $message,
                            'user_name' => $row->username,
                            'notification' => 'Payment Cancel Notification'
                        ];
                        app(CustomMailerService::class)->sendWithUserSmtpCron(
                            'emails.test',
                            $data,
                            $row->email,
                            'Invoice',
                            $smtp
                        );
                    }catch (\Exception $e){
                    }
                }
            }
        }

        // $cek_inv = Invoice::where([['member_id', $request->member_id], ['status', 0]])->count();
        // if ($request->ppp_status === '2' && $cek_inv === 0) {
        //     $ppp = PppoeUser::where('id', $request->ppp_id);
        //     $ppp->update([
        //         'status' => 1,
        //     ]);
        //     if ($request->nas === null) {
        //         $nas = RadiusNas::where('shortname', $shortname)->select('nasname', 'secret')->get();
        //         foreach ($nas as $nasitem) {
        //             $command = Process::path('/usr/bin/')->run("echo User-Name='$request->pppoe_user' | radclient -r 1 $nasitem[nasname]:3799 disconnect $nasitem[secret]");
        //         }
        //     } else {
        //         $nas_secret = RadiusNas::where('shortname', $shortname)->where('nasname', $request->nas)->select('secret')->first();
        //         $command = Process::path('/usr/bin/')->run("echo User-Name='$request->pppoe_user' | radclient -r 1 $request->nas:3799 disconnect $nas_secret->secret");
        //     }
        // }

        if(multi_auth()->role !== 'Mitra'){
            activity()
            ->tap(function (Activity $activity) {
                $activity->shortname = multi_auth()->shortname;
            })
            ->event('Update')
            ->log('Unpay Invoice: ' . $invoice->no_invoice . ' a.n ' . $invoice->rpppoe->full_name . '');
        }else{
            activity()
            ->tap(function (Activity $activity) {
                $activity->shortname = multi_auth()->shortname;
                $activity->causer_id = multi_auth()->id;
                $activity->causer_type = 'App\\Models\\Partnership\\Mitra';
            })
            ->event('Update')
            ->log('Unpay Invoice: ' . $invoice->no_invoice . ' a.n ' . $invoice->rpppoe->full_name . '');
        }

        return response()->json([
            'success' => true,
            'message' => 'Invoice Berhasil Dibatalkan',
        ]);
    }

    // public function printInvoice(Request $request)
    // {
    //     $no_invoice = last(request()->segments());
    //     $invoice = Invoice::where('shortname', multi_auth()->shortname)
    //         ->where('no_invoice', $no_invoice)
    //         ->with('member')
    //         ->get();
    //     $pdf = Pdf::loadView('backend.billing.invoice.print_thermal', compact('invoice'));
    //     return $pdf->stream();
    // }
    public function destroy($id)
    {
        $unpaid = Invoice::findOrFail($id);
        $unpaid->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }

    public function exportUnpaid(Request $request)
    {
        $id = $request->id;
        return Excel::download(new InvoiceExport($id), 'invoice.xlsx');
    }
}
