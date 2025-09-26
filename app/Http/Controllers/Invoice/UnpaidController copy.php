<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Pppoe\PppoeProfile;
use App\Models\Pppoe\PppoeUser;
use Illuminate\Http\Request;
use App\Models\Invoice\Invoice;
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
use App\Models\Radius\RadiusNas;
use Illuminate\Support\Facades\Process;
use Spatie\Activitylog\Contracts\Activity;

class UnpaidController extends Controller
{
    public function index()
    {
        $ppp = PppoeUser::where('shortname', multi_auth()->shortname)->whereNotNull('payment_type')->select('id', 'id_pelanggan', 'full_name')->get();

        if (request()->ajax()) {
            $periodestr = request()->get('periode');
            if ($periodestr === null) {
                if (multi_auth()->role !== 'Mitra') {
                    $totalunpaid = Invoice::query()->where('shortname', multi_auth()->shortname)->where('status', 'unpaid')->count();
                    $totaltagihan = Invoice::query()->where('shortname', multi_auth()->shortname)->where('status', 'unpaid')->sum('price');
                    $totalkomisi = Invoice::query()->where('shortname', multi_auth()->shortname)->where('status', 'unpaid')->sum('komisi');
                    $invoices = Invoice::query()->select('invoice.*', 'user_pppoe.full_name as full_name', 'user_pppoe.kode_area as kode_area')->join('frradius_auth.user_pppoe as user_pppoe', 'invoice.id_pelanggan', '=', 'user_pppoe.id')->where('invoice.shortname', multi_auth()->shortname)->where('invoice.status', 'unpaid');
                } else {
                    $totalunpaid = Invoice::query()->where('shortname', multi_auth()->shortname)->where('status', 'unpaid')->where('invoice.mitra_id', multi_auth()->id)->count();
                    $totaltagihan = Invoice::query()->where('shortname', multi_auth()->shortname)->where('status', 'unpaid')->where('invoice.mitra_id', multi_auth()->id)->sum('price');
                    $totalkomisi = Invoice::query()->where('shortname', multi_auth()->shortname)->where('status', 'unpaid')->where('invoice.mitra_id', multi_auth()->id)->sum('komisi');
                    $invoices = Invoice::query()->select('invoice.*', 'user_pppoe.full_name as full_name', 'user_pppoe.kode_area as kode_area')->join('frradius_auth.user_pppoe as user_pppoe', 'invoice.id_pelanggan', '=', 'user_pppoe.id')->where('invoice.shortname', multi_auth()->shortname)->where('invoice.status', 'unpaid')->where('invoice.mitra_id', multi_auth()->id);
                }
            } else {
                $month = date('m', strtotime($periodestr));
                $year = date('Y', strtotime($periodestr));
                if (multi_auth()->role !== 'Mitra') {
                    $totalunpaid = Invoice::query()->where('shortname', multi_auth()->shortname)->whereMonth('due_date', $month)->whereYear('due_date', $year)->where('status', 'unpaid')->count();
                    $totaltagihan = Invoice::query()->where('shortname', multi_auth()->shortname)->whereMonth('due_date', $month)->whereYear('due_date', $year)->where('status', 'unpaid')->sum('price');
                    $totalkomisi = Invoice::query()->where('shortname', multi_auth()->shortname)->whereMonth('due_date', $month)->whereYear('due_date', $year)->where('status', 'unpaid')->sum('komisi');
                    $invoices = Invoice::query()->select('invoice.*', 'user_pppoe.full_name as full_name', 'user_pppoe.kode_area as kode_area')->join('frradius_auth.user_pppoe as user_pppoe', 'invoice.id_pelanggan', '=', 'user_pppoe.id')->where('invoice.shortname', multi_auth()->shortname)->whereMonth('invoice.due_date', $month)->whereYear('invoice.due_date', $year)->where('invoice.status', 'unpaid');
                } else {
                    $totalunpaid = Invoice::query()->where('shortname', multi_auth()->shortname)->whereMonth('due_date', $month)->whereYear('due_date', $year)->where('status', 'unpaid')->where('invoice.mitra_id', multi_auth()->id)->count();
                    $totaltagihan = Invoice::query()->where('shortname', multi_auth()->shortname)->whereMonth('due_date', $month)->whereYear('due_date', $year)->where('status', 'unpaid')->where('invoice.mitra_id', multi_auth()->id)->sum('price');
                    $totalkomisi = Invoice::query()->where('shortname', multi_auth()->shortname)->whereMonth('due_date', $month)->whereYear('due_date', $year)->where('status', 'unpaid')->where('invoice.mitra_id', multi_auth()->id)->sum('komisi');
                    $invoices = Invoice::query()->select('invoice.*', 'user_pppoe.full_name as full_name', 'user_pppoe.kode_area as kode_area')->join('frradius_auth.user_pppoe as user_pppoe', 'invoice.id_pelanggan', '=', 'user_pppoe.id')->where('invoice.shortname', multi_auth()->shortname)->whereMonth('invoice.due_date', $month)->whereYear('invoice.due_date', $year)->where('invoice.status', 'unpaid')->where('invoice.mitra_id', multi_auth()->id);
                }
            }
            $selectedIds = request()->get('idsel') ?? [];
            return DataTables::of($invoices)
                ->with(['totalunpaid' => $totalunpaid, 'totaltagihan' => $totaltagihan, 'totalkomisi' => $totalkomisi])
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
                <a href="javascript:void(0)" id="pay"
                data-id="' .
                        $row->id .
                        '" data-pelanggan_id="' .
                        $row->id_pelangan .
                        '" class="btn btn-primary text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                    <span class="material-symbols-outlined">credit_score</span> PAY
            </a>
            <a href="javascript:void(0)" id="resend" data-id="' .
                        $row->id .
                        '" class="btn btn-success text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
            <span class="material-symbols-outlined">message</span>
            </a>

             <a href="' .
                        $row->payment_url .
                        '" target="_blank" data-id="' .
                        $row->id .
                        '" class="btn btn-secondary text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
            <span class="material-symbols-outlined">captive_portal</span>
            </a>
            <a href="javascript:void(0)" id="delete" data-id="' .
                        $row->id .
                        '" class="btn btn-danger text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
            <span class="material-symbols-outlined">delete</span>
            </a>';
                })
                ->rawColumns(['action', 'checkbox'])
                ->toJson();
        }
        if (multi_auth()->role === 'Mitra' && multi_auth()->billing !== 1) {
            return redirect()->back();
        }
        return view('backend.invoice.unpaid.index', compact('ppp'));
    }

    public function getPelanggan(Request $request)
    {
        $pelanggan = PppoeUser::where('id', $request->id)->with('rprofile', 'rinvoice')->first();
        return response()->json($pelanggan);
    }
    // public function show() {}
    public function generateInvoice(Request $request)
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

        $validator = Validator::make($request->all(), [
            'item' => 'required',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        // data member
        $pelanggan_id = $request->pelanggan_id;
        $payment_type = $request->payment_type;
        $billing_period = $request->billing_period;
        $item = $request->item;
        $price = str_replace('.', '', $request->amount);
        $ppn = $request->ppn;
        $discount = $request->discount;
        $due_date = $request->next_due;
        $subscribe = $request->subscribe;
        $invoice_date = $request->today;
        // untuk notif wa
        $id_pelanggan = $request->id_pelanggan;
        $full_name = $request->full_name;
        $username = $request->username;
        $password = $request->password;
        $profile = $request->profile;
        $wa = $request->wa;

        $amount_ppn = ($price * $ppn) / 100;
        $amount_discount = ($price * $discount) / 100;
        $total = $price + $amount_ppn - $amount_discount;

        $amount_format = number_format($price, 0, '.', '.');
        $total_format = number_format($total, 0, '.', '.');
        $invoice_date_format = Carbon::parse($invoice_date)->translatedFormat('d F Y');
        $due_date_format = Carbon::parse($due_date)->translatedFormat('d F Y');

        if ($payment_type === 'Prabayar' && $billing_period === 'Fixed Date') {
            $periode = Carbon::createFromFormat('Y-m-d', $due_date);
            $get_periode = date('Y-m-d', strtotime($periode));
            $periode_format = tgl_indo($get_periode);
            $next_invoice = Carbon::createFromFormat('Y-m-d', $due_date)->addMonthsWithNoOverflow(1)->toDateString();
        } elseif ($payment_type === 'Pascabayar' && $billing_period === 'Fixed Date') {
            $periode = Carbon::createFromFormat('Y-m-d', $due_date)->subMonthsWithNoOverflow(1);
            $get_periode = date('Y-m-d', strtotime($periode));
            $periode_format = tgl_indo($get_periode);
            $next_invoice = Carbon::createFromFormat('Y-m-d', $due_date)->addMonthsWithNoOverflow(1)->toDateString();
        } elseif ($payment_type === 'Pascabayar' && $billing_period === 'Billing Cycle') {
            $periode = Carbon::createFromFormat('Y-m-d', $due_date)->subMonthsWithNoOverflow(1);
            $get_periode = date('Y-m-d', strtotime($periode));
            $periode_format = tgl_indo($get_periode);
            $next_invoice = Carbon::createFromFormat('Y-m-d', $due_date)->startOfMonth()->addMonthsWithNoOverflow(1)->toDateString();
        }
        $no_invoice = date('m') . rand(10000000, 99999999);
        $invoice = Invoice::create([
            'shortname' => multi_auth()->shortname,
            'id_pelanggan' => $pelanggan_id,
            'no_invoice' => $no_invoice,
            'item' => $item,
            'price' => $price,
            'ppn' => $ppn,
            'discount' => $discount,
            'komisi' => $request->komisi,
            'invoice_date' => $invoice_date,
            'due_date' => $due_date,
            'period' => $periode,
            'subscribe' => $subscribe,
            'payment_type' => $payment_type,
            'billing_period' => $billing_period,
            'payment_url' => multi_auth()->domain . '/pay/' . $no_invoice,
            'status' => 'unpaid',
            'mitra_id' => $request->mitra_id,
        ]);

        if ($invoice) {
            $pelanggan = PppoeUser::where('id', $invoice->id_pelanggan);
            $pelanggan->update([
                'next_invoice' => $next_invoice,
            ]);

            $notif_it = BillingSetting::where('shortname', multi_auth()->shortname)->first()->notif_it;
            if ($notif_it === 1 && $wa !== null) {
                $mpwa = Mpwa::where('shortname', multi_auth()->shortname)->first();
                $row = PppoeUser::where('id', $invoice->id_pelanggan)->with('rprofile')->first();
                $shortcode = ['[nama_lengkap]', '[id_pelanggan]', '[username]', '[password]', '[alamat]', '[paket_internet]', '[tipe_pembayaran]', '[siklus_tagihan]', '[no_invoice]', '[tgl_invoice]', '[harga]', '[ppn]', '[discount]', '[total]', '[jth_tempo]', '[periode]', '[subscribe]', '[link_pembayaran]'];
                $source = [$row->full_name, $row->id_pelanggan, $row->username, $row->value, $row->address, $row->profile, $row->payment_type, $row->billing_period, $invoice->no_invoice, $invoice_date_format, $amount_format, $ppn, $discount, $total_format, $due_date_format, $periode_format, $invoice->subscribe, $invoice->payment_url];
                $template = Watemplate::where('shortname', $row->shortname)->first()->invoice_terbit;
                $message = str_replace($shortcode, $source, $template);
                $message_format = str_replace('<br>', "\n", $message);

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
        }

        activity()
            ->tap(function (Activity $activity) {
                $activity->shortname = multi_auth()->shortname;
            })
            ->event('Create')
            ->log('Create Manual Invoice: ' . $invoice->no_invoice . ' a.n ' . $request->full_name . '');

        return response()->json([
            'success' => true,
            'message' => 'Invoice Berhasil Dibuat',
            'data' => $invoice,
        ]);
    }

    public function update(Request $request, Invoice $unpaid)
    {
        $unpaid->update([
            'item' => $request->item,
            'ppn' => $request->ppn,
            'discount' => $request->discount,
            'price' => str_replace('.', '', $request->amount),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diupdate',
            'data' => $unpaid,
        ]);
    }
    public function getUnpaid(Request $request)
    {
        $invoice = Invoice::with('rpppoe')->where('id', $request->id)->first();
        return response()->json($invoice);
    }

    public function payInvoice(Request $request, Invoice $invoice)
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
            $next_due = Carbon::createFromFormat('Y-m-d', $request->due_date)->addMonthsWithNoOverflow(1);
            $next_invoice = Carbon::createFromFormat('Y-m-d', $request->due_date)->addMonthsWithNoOverflow(1);
        } elseif ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Fixed Date') {
            $next_due = Carbon::createFromFormat('Y-m-d', $request->due_date)->addMonthsWithNoOverflow(1);
            $next_invoice = Carbon::createFromFormat('Y-m-d', $request->due_date)->addMonthsWithNoOverflow(1);
        } elseif ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Billing Cycle') {
            $due_bc = BillingSetting::where('shortname', multi_auth()->shortname)->select('due_bc')->first();
            $next_due = Carbon::createFromFormat('Y-m-d', $request->due_date)->setDay($due_bc->due_bc)->addMonths(1);
            $next_invoice = Carbon::createFromFormat('Y-m-d', $request->due_date)->startOfMonth()->addMonths(1);
        }

        $invoice->update([
            'paid_date' => Carbon::today()->toDateString(),
            'status' => 'paid',
        ]);

        if ($invoice) {
            $pelanggan = PppoeUser::where('id', $request->pelanggan_id);
            $pelanggan->update([
                'next_due' => $next_due,
                'next_invoice' => $next_invoice,
            ]);

            $transaksi = Transaksi::create([
                'shortname' => multi_auth()->shortname,
                'id_data' => $invoice->id,
                'tanggal' => Carbon::now(),
                'tipe' => 'Pemasukan',
                'kategori' => 'Invoice',
                'deskripsi' => "Payment #$request->no_invoice a.n $request->full_name",
                'nominal' => $request->payment_total - $request->komisi,
                'metode' => $request->payment_method,
                'created_by' => multi_auth()->username ?? multi_auth()->name,
            ]);
            if ($request->mitra_id != 0 && $request->komisi !== null) {
                $nama_mitra = Mitra::where('shortname', multi_auth()->shortname)->where('id', $request->mitra_id)->first()->name;
                $transaksi = TransaksiMitra::create([
                    'shortname' => multi_auth()->shortname,
                    'id_data' => $invoice->id,
                    'tanggal' => Carbon::now(),
                    'tipe' => 'Pemasukan',
                    'kategori' => 'Komisi',
                    'deskripsi' => "Komisi $nama_mitra #$request->no_invoice a.n $request->full_name",
                    'mitra_id' => $request->mitra_id,
                    'nominal' => $request->komisi,
                    'metode' => $request->payment_method,
                    'created_by' => multi_auth()->username ?? multi_auth()->name,
                ]);
            }

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
                $shortcode = ['[nama_lengkap]', '[id_pelanggan]', '[username]', '[password]', '[alamat]', '[paket_internet]', '[tipe_pembayaran]', '[siklus_tagihan]', '[no_invoice]', '[tgl_invoice]', '[harga]', '[ppn]', '[discount]', '[total]', '[jth_tempo]', '[periode]', '[tgl_bayar]', '[subscribe]', '[metode_pembayaran]'];
                $source = [$row->full_name, $row->id_pelanggan, $row->username, $row->value, $row->address, $row->profile, $request->payment_type, $request->billing_period, $request->no_invoice, $invoice_date_format, $amount_format, $request->ppn, $request->discount, $total_format, $due_date_format, $periode_format, $paid_date_format, $invoice->subscribe, $request->payment_method];
                $template = Watemplate::where('shortname', $row->shortname)->first()->payment_paid;
                $message = str_replace($shortcode, $source, $template);
                $message_format = str_replace('<br>', "\n", $message);

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

            $cek_inv = Invoice::where('id_pelanggan', $request->pelanggan_id)->where('status', 'unpaid')->count();
            if ($request->ppp_status === '2' && $cek_inv === 0) {
                $ssh_user = env('IP_RADIUS_USERNAME');
                $ssh_host = env('IP_RADIUS_SERVER');
                $ppp = PppoeUser::where('id', $request->pelanggan_id);
                $ppp->update([
                    'status' => 1,
                ]);
                if ($request->nas === null) {
                    $nas = RadiusNas::where('shortname', multi_auth()->shortname)->select('nasname', 'secret')->get();
                    foreach ($nas as $row) {
                        $command = "echo User-Name='$request->pppoe_user' | radclient -r 1 $row[nasname]:3799 disconnect $row[secret]";
                        $ssh_command = "ssh {$ssh_user}@{$ssh_host} \"{$command}\"";
                        $process = Process::run($ssh_command);
                        if ($process->successful()) {
                            \Log::info('DISCONNECT SUCCESS', ['output' => $process->output()]);
                        } else {
                            \Log::error('DISCONNECT FAILED', ['error' => $process->errorOutput()]);
                        }
                        
                    }
                } else {
                    $secret = RadiusNas::where('shortname', multi_auth()->shortname)->where('nasname', $request->nas)->select('secret')->first();
                    $command = "echo User-Name='$request->pppoe_user' | radclient -r 1 $request->nas:3799 disconnect $secret->secret";
                    $ssh_command = "ssh {$ssh_user}@{$ssh_host} \"{$command}\"";
                    $process = Process::run($ssh_command);
                    if ($process->successful()) {
                        \Log::info('DISCONNECT SUCCESS', ['output' => $process->output()]);
                    } else {
                        \Log::error('DISCONNECT FAILED', ['error' => $process->errorOutput()]);
                    }
                    
                }
            }
        }

        if (multi_auth()->role !== 'Mitra') {
            activity()
                ->tap(function (Activity $activity) {
                    $activity->shortname = multi_auth()->shortname;
                })
                ->event('Update')
                ->log('Pay Invoice: ' . $invoice->no_invoice . ' a.n ' . $invoice->rpppoe->full_name . '');
        } else {
            activity()
                ->tap(function (Activity $activity) {
                    $activity->shortname = multi_auth()->shortname;
                    $activity->causer_id = multi_auth()->id;
                    $activity->causer_type = 'App\\Models\\Partnership\\Mitra';
                })
                ->event('Update')
                ->log('Pay Invoice: ' . $invoice->no_invoice . ' a.n ' . $invoice->rpppoe->full_name . '');
        }

        return response()->json([
            'success' => true,
            'message' => 'Invoice Berhasil Dibayar',
        ]);
    }

    public function payMassal(Request $request)
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
        $invoice_update = Invoice::whereIn('id', $request->ids);
        $invoice_update->update([
            'paid_date' => Carbon::today()->toDateString(),
            'status' => 'paid',
        ]);
        if ($invoice_update) {
            $invoices = Invoice::whereIn('id', $request->ids)->with('rpppoe')->get();
            $notif_ps = BillingSetting::where('shortname', multi_auth()->shortname)->first()->notif_ps;
            $results = [];
            foreach ($invoices as $invoice) {
                if ($invoice->payment_type === 'Prabayar') {
                    $next_due = Carbon::createFromFormat('Y-m-d', $invoice->due_date)->addMonthsWithNoOverflow(1);
                    $next_invoice = Carbon::createFromFormat('Y-m-d', $invoice->due_date)->addMonthsWithNoOverflow(1);
                } elseif ($invoice->payment_type === 'Pascabayar' && $invoice->billing_period === 'Fixed Date') {
                    $next_due = Carbon::createFromFormat('Y-m-d', $invoice->due_date)->addMonthsWithNoOverflow(1);
                    $next_invoice = Carbon::createFromFormat('Y-m-d', $invoice->due_date)->addMonthsWithNoOverflow(1);
                } elseif ($invoice->payment_type === 'Pascabayar' && $invoice->billing_period === 'Billing Cycle') {
                    $due_bc = BillingSetting::where('shortname', multi_auth()->shortname)->select('due_bc')->first();
                    $next_due = Carbon::createFromFormat('Y-m-d', $invoice->due_date)->setDay($due_bc->due_bc)->addMonths(1);
                    $next_invoice = Carbon::createFromFormat('Y-m-d', $invoice->due_date)->startOfMonth()->addMonths(1);
                }
                $pelanggan = PppoeUser::where('id', $invoice->id_pelanggan);
                $pelanggan->update([
                    'next_due' => $next_due,
                    'next_invoice' => $next_invoice,
                ]);

                $full_name = $invoice->rpppoe->full_name;
                $nominal_ppn = ($invoice->price * $invoice->ppn) / 100;
                $nominal_discount = ($invoice->price * $invoice->discount) / 100;
                $payment_total = $invoice->price + $nominal_ppn - $nominal_discount;
                $komisi = PppoeProfile::where('id', $invoice->rpppoe->profile_id)->first()->fee_mitra;

                $transaksi = Transaksi::create([
                    'shortname' => multi_auth()->shortname,
                    'id_data' => $invoice->id,
                    'tanggal' => Carbon::now(),
                    'tipe' => 'Pemasukan',
                    'kategori' => 'Invoice',
                    'deskripsi' => "Payment #$invoice->no_invoice a.n $full_name",
                    'nominal' => $payment_total - $komisi,
                    'metode' => $request->payment_method,
                    'created_by' => multi_auth()->username ?? multi_auth()->name,
                ]);
                if ($invoice->mitra_id != 0 && $request->komisi !== null) {
                    $nama_mitra = Mitra::where('shortname', multi_auth()->shortname)->where('id', $invoice->mitra_id)->first()->name;
                    $transaksi = TransaksiMitra::create([
                        'shortname' => multi_auth()->shortname,
                        'id_data' => $invoice->id,
                        'tanggal' => Carbon::now(),
                        'tipe' => 'Pemasukan',
                        'kategori' => 'Komisi',
                        'deskripsi' => "Komisi $nama_mitra #$invoice->no_invoice a.n $full_name",
                        'mitra_id' => $invoice->mitra_id,
                        'nominal' => $komisi,
                        'metode' => $request->payment_method,
                        'created_by' => multi_auth()->username ?? multi_auth()->name,
                    ]);
                }

                if ($notif_ps === 1 && $invoice->rpppoe->wa !== null) {
                    $amount_format = number_format($invoice->price, 0, '.', '.');
                    $total_format = number_format($payment_total, 0, '.', '.');
                    $invoice_date_format = Carbon::parse($invoice->invoice_date)->translatedFormat('d F Y');
                    $due_date_format = Carbon::parse($invoice->due_date)->translatedFormat('d F Y');
                    $get_periode = date('Y-m-d', strtotime($invoice->period));
                    $periode_format = tgl_indo($get_periode);
                    $mpwa = Mpwa::where('shortname', multi_auth()->shortname)->first();
                    $paid_date_format = date('d/m/Y', strtotime($invoice->paid_date));
                    // $row = PppoeUser::where('id', $invoice->id_pelanggan)->with('rprofile')->first();
                    // $inv = Invoice::where('id',$request->invoice_id)->first();
                    $shortcode = ['[nama_lengkap]', '[id_pelanggan]', '[username]', '[password]', '[alamat]', '[paket_internet]', '[tipe_pembayaran]', '[siklus_tagihan]', '[no_invoice]', '[tgl_invoice]', '[harga]', '[ppn]', '[discount]', '[total]', '[jth_tempo]', '[periode]', '[tgl_bayar]', '[subscribe]', '[metode_pembayaran]'];
                    $source = [$invoice->rpppoe->full_name, $invoice->rpppoe->id_pelanggan, $invoice->rpppoe->username, $invoice->rpppoe->value, $invoice->rpppoe->address, $invoice->rpppoe->profile, $invoice->payment_type, $invoice->billing_period, $invoice->no_invoice, $invoice_date_format, $amount_format, $invoice->ppn, $invoice->discount, $total_format, $due_date_format, $periode_format, $paid_date_format, $invoice->subscribe, $request->payment_method];
                    $template = Watemplate::where('shortname', $invoice->shortname)->first()->payment_paid;
                    $message = str_replace($shortcode, $source, $template);
                    $message_format = str_replace('<br>', "\n", $message);

                    try {
                        $curl = curl_init();
                        $data = [
                            'api_key' => $mpwa->api_key,
                            'sender' => $mpwa->sender,
                            'number' => $invoice->rpppoe->wa,
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
                        // return $e->getMessage();
                        // return response()->json([
                        //     'success' => true,
                        //     'message' => 'Invoice Berhasil Dibayar',
                        // ]);
                        $results[] = "Invoice #{$invoice->no_invoice} berhasil diproses namun notifikasi gagal terkirim.";
                    }
                    $results[] = "Invoice #{$invoice->no_invoice} berhasil diproses dan notifikasi sukses terkirim.";
                } else {
                    $results[] = "Invoice #{$invoice->no_invoice} berhasil diproses.";
                }

                $cek_inv = Invoice::where('id_pelanggan', $invoice->id_pelanggan)->where('status', 'unpaid')->count();
                if ($invoice->rpppoe->status === 2 && $cek_inv === 0) {
                    $ppp = PppoeUser::where('id', $invoice->id_pelanggan);
                    $ppp->update([
                        'status' => 1,
                    ]);
                    $username = $invoice->rpppoe->username;
                    $nas = $invoice->rpppoe->nas;
                    if ($invoice->rpppoe->nas === null) {
                        $ssh_user = env('IP_RADIUS_USERNAME');
                        $ssh_host = env('IP_RADIUS_SERVER');
                        $nas = RadiusNas::where('shortname', multi_auth()->shortname)->select('nasname', 'secret')->get();
                        foreach ($nas as $row) {
                            $command = "echo User-Name='$username' | radclient -r 1 $row[nasname]:3799 disconnect $row[secret]";
                            $ssh_command = "ssh {$ssh_user}@{$ssh_host} \"{$command}\"";
                            $process = Process::run($ssh_command);
                            if ($process->successful()) {
                                \Log::info('DISCONNECT SUCCESS', ['output' => $process->output()]);
                            } else {
                                \Log::error('DISCONNECT FAILED', ['error' => $process->errorOutput()]);
                            }
                            
                        }
                    } else {
                        $secret = RadiusNas::where('shortname', multi_auth()->shortname)->where('nasname', $nas)->select('secret')->first();
                        $command = "echo User-Name='$username' | radclient -r 1 $nas:3799 disconnect $secret->secret";
                        $ssh_command = "ssh {$ssh_user}@{$ssh_host} \"{$command}\"";
                        $process = Process::run($ssh_command);
                        if ($process->successful()) {
                            \Log::info('DISCONNECT SUCCESS', ['output' => $process->output()]);
                        } else {
                            \Log::error('DISCONNECT FAILED', ['error' => $process->errorOutput()]);
                        }
                        
                    }
                }

                if (multi_auth()->role !== 'Mitra') {
                    activity()
                        ->tap(function (Activity $activity) {
                            $activity->shortname = multi_auth()->shortname;
                        })
                        ->event('Update')
                        ->log('Pay Invoice: ' . $invoice->no_invoice . ' a.n ' . $invoice->rpppoe->full_name . '');
                } else {
                    activity()
                        ->tap(function (Activity $activity) {
                            $activity->shortname = multi_auth()->shortname;
                            $activity->causer_id = multi_auth()->id;
                            $activity->causer_type = 'App\\Models\\Partnership\\Mitra';
                        })
                        ->event('Update')
                        ->log('Pay Invoice: ' . $invoice->no_invoice . ' a.n ' . $invoice->rpppoe->full_name . '');
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Invoice Berhasil Dibayar',
                'details' => $results,
            ]);
        }
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

    public function resendUnpaid(Request $request)
    {
        $invoice = Invoice::where('id', $request->id)->first();
        $periode = Carbon::createFromFormat('Y-m-d', $invoice->period);
        $periode_format = Carbon::parse($periode)->translatedFormat('F Y');
        $subscribe = $invoice->subscribe;

        $amount_ppn = ($invoice->price * $invoice->ppn) / 100;
        $amount_discount = ($invoice->price * $invoice->discount) / 100;
        $total = $invoice->price + $amount_ppn - $amount_discount;

        // $notif_it = BillingSetting::where('shortname', $invoice->shortname)->first()->notif_it;
        $user = PppoeUser::where('id', $invoice->id_pelanggan)->with('rprofile')->first();
        if ($user->wa !== null) {
            $mpwa = Mpwa::where('shortname', $invoice->shortname)->first();
            $shortcode = ['[nama_lengkap]', '[id_pelanggan]', '[username]', '[password]', '[alamat]', '[paket_internet]', '[tipe_pembayaran]', '[siklus_tagihan]', '[no_invoice]', '[tgl_invoice]', '[harga]', '[ppn]', '[discount]', '[total]', '[jth_tempo]', '[periode]', '[subscribe]', '[link_pembayaran]'];
            $source = [$user->full_name, $user->id_pelanggan, $user->username, $user->value, $user->address, $user->rprofile->name, $invoice->payment_type, $invoice->billing_period, $invoice->no_invoice, Carbon::parse($invoice->invoice_date)->translatedFormat('d F Y'), number_format($invoice->price, 0, ',', '.'), $invoice->ppn, $invoice->discount, number_format($total, 0, ',', '.'), Carbon::parse($invoice->due_date)->translatedFormat('d F Y'), $periode_format, $subscribe, $invoice->payment_url];
            $template = Watemplate::where('shortname', $invoice->shortname)->first()->invoice_terbit;
            $message = str_replace($shortcode, $source, $template);
            $message_format = str_replace('<br>', "\n", $message);

            try {
                $curl = curl_init();
                $data = [
                    'api_key' => $mpwa->api_key,
                    'sender' => $mpwa->sender,
                    'number' => $user->wa,
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
                $result = json_decode($response, true);
            } catch (\Exception $e) {
                // return $e->getMessage();
                $result = 'Error';
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'Notifikasi Invoice Berhasil Dikirim',
            'result' => $result,
        ]);
    }

    public function exportUnpaid(Request $request)
    {
        $id = $request->ids;
        return Excel::download(new InvoiceExport($id), 'invoice.xlsx');
    }
}
