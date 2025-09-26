<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Balancehistory;
use App\Models\MappingAdons;
use App\Models\Pppoe\PppoeProfile;
use App\Models\Pppoe\PppoeUser;
use App\Models\SmtpSetting;
use App\Models\User;
use App\Services\CustomMailerService;
use Illuminate\Http\Request;
use App\Models\Invoice\Invoice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
use Illuminate\Support\Facades\DB; // Pastikan line ini ada


class UnpaidController extends Controller
{
    public function index()
    {
        $ppp = PppoeUser::where('shortname', multi_auth()->shortname)->whereNotNull('payment_type')->select('id', 'id_pelanggan', 'full_name')->get();

        if (request()->ajax()) {
            $periodestr = request()->get('periode');

            if ($periodestr === null) {
                if (multi_auth()->role !== 'Mitra') {
                    // Perhitungan total di invoice lokal
                    $totalunpaid  = Invoice::query()
                        ->where('shortname', multi_auth()->shortname)
                        ->where('status', 'unpaid')
                        ->count();
                    $totaltagihan = Invoice::query()
                        ->where('shortname', multi_auth()->shortname)
                        ->where('status', 'unpaid')
                        ->sum('price');
                    $totalkomisi  = Invoice::query()
                        ->where('shortname', multi_auth()->shortname)
                        ->where('status', 'unpaid')
                        ->sum('komisi');

                    // Ambil data invoice lokal (tanpa join)
                    $invoices = Invoice::query()
                        ->where('shortname', multi_auth()->shortname)
                        ->where('status', 'unpaid')
                        ->get();
                } else {
                    $totalunpaid  = Invoice::query()
                        ->where('shortname', multi_auth()->shortname)
                        ->where('status', 'unpaid')
                        ->where('mitra_id', multi_auth()->id)
                        ->count();
                    $totaltagihan = Invoice::query()
                        ->where('shortname', multi_auth()->shortname)
                        ->where('status', 'unpaid')
                        ->where('mitra_id', multi_auth()->id)
                        ->sum('price');
                    $totalkomisi  = Invoice::query()
                        ->where('shortname', multi_auth()->shortname)
                        ->where('status', 'unpaid')
                        ->where('mitra_id', multi_auth()->id)
                        ->sum('komisi');

                    $invoices = Invoice::query()
                        ->where('shortname', multi_auth()->shortname)
                        ->where('status', 'unpaid')
                        ->where('mitra_id', multi_auth()->id)
                        ->get();
                }
            } else {
                $month = date('m', strtotime($periodestr));
                $year  = date('Y', strtotime($periodestr));

                if (multi_auth()->role !== 'Mitra') {
                    $totalunpaid  = Invoice::query()
                        ->where('shortname', multi_auth()->shortname)
                        ->whereMonth('due_date', $month)
                        ->whereYear('due_date', $year)
                        ->where('status', 'unpaid')
                        ->count();
                    $totaltagihan = Invoice::query()
                        ->where('shortname', multi_auth()->shortname)
                        ->whereMonth('due_date', $month)
                        ->whereYear('due_date', $year)
                        ->where('status', 'unpaid')
                        ->sum('price');
                    $totalkomisi  = Invoice::query()
                        ->where('shortname', multi_auth()->shortname)
                        ->whereMonth('due_date', $month)
                        ->whereYear('due_date', $year)
                        ->where('status', 'unpaid')
                        ->sum('komisi');

                    $invoices = Invoice::query()
                        ->where('shortname', multi_auth()->shortname)
                        ->whereMonth('due_date', $month)
                        ->whereYear('due_date', $year)
                        ->where('status', 'unpaid')
                        ->get();
                } else {
                    $totalunpaid  = Invoice::query()
                        ->where('shortname', multi_auth()->shortname)
                        ->whereMonth('due_date', $month)
                        ->whereYear('due_date', $year)
                        ->where('status', 'unpaid')
                        ->where('mitra_id', multi_auth()->id)
                        ->count();
                    $totaltagihan = Invoice::query()
                        ->where('shortname', multi_auth()->shortname)
                        ->whereMonth('due_date', $month)
                        ->whereYear('due_date', $year)
                        ->where('status', 'unpaid')
                        ->where('mitra_id', multi_auth()->id)
                        ->sum('price');
                    $totalkomisi  = Invoice::query()
                        ->where('shortname', multi_auth()->shortname)
                        ->whereMonth('due_date', $month)
                        ->whereYear('due_date', $year)
                        ->where('status', 'unpaid')
                        ->where('mitra_id', multi_auth()->id)
                        ->sum('komisi');

                    $invoices = Invoice::query()
                        ->where('shortname', multi_auth()->shortname)
                        ->whereMonth('due_date', $month)
                        ->whereYear('due_date', $year)
                        ->where('status', 'unpaid')
                        ->where('mitra_id', multi_auth()->id)
                        ->get();
                }
            }

            // Ambil data user_pppoe dari database remote (pastikan koneksi "mysql_remote" sudah dikonfigurasi di config/database.php dan .env)
            $userIds = $invoices->pluck('id_pelanggan')->unique();
            $userPppoe = DB::connection('frradius_auth')->table('user_pppoe')
                ->whereIn('id', $userIds)
                ->get()
                ->keyBy('id');

            // Gabungkan data invoice dengan data user_pppoe secara manual
            $mergedInvoices = $invoices->map(function ($invoice) use ($userPppoe) {
                $invoice->full_name = isset($userPppoe[$invoice->id_pelanggan])
                    ? $userPppoe[$invoice->id_pelanggan]->full_name
                    : null;
                $invoice->kode_area = isset($userPppoe[$invoice->id_pelanggan])
                    ? $userPppoe[$invoice->id_pelanggan]->kode_area
                    : null;
                return $invoice;
            });

            $selectedIds = request()->get('idsel') ?? [];

            return DataTables::of($mergedInvoices)
                ->with([
                    'totalunpaid'  => $totalunpaid,
                    'totaltagihan' => $totaltagihan,
                    'totalkomisi'  => $totalkomisi,
                ])
                // ->filterColumn('full_name', function ($query, $keyword) {
                //     // Karena full_name kini sudah menjadi properti pada object, gunakan field tersebut
                //     $sql = 'LOWER(full_name) like ?';
                //     $query->whereRaw($sql, ["%{$keyword}%"]);
                // })
                // ->filterColumn('kode_area', function ($query, $keyword) {
                //     $sql = 'LOWER(kode_area) like ?';
                //     $query->whereRaw($sql, ["%{$keyword}%"]);
                // })
                ->addColumn('checkbox', function ($row) use ($selectedIds) {
                    $checked = in_array($row->id, $selectedIds) ? ' checked' : '';
                    return '<input type="checkbox" class="row-cb form-check-input" id="checkbox_row' . $row->id . '" value="' . $row->id . '"' . $checked . ' />';
                })
                ->addColumn('total', function ($row) {
                    $amount_ppn      = ($row->price * $row->ppn) / 100;
                    $amount_discount = $row->discount;
                    $amount_price_adon = $row->price_adon ?? 0;
                    if ($row->discount === null) {
                        return $row->price + $amount_ppn + $amount_price_adon;
                    } elseif ($row->ppn === null) {
                        return $row->price - $amount_discount + $amount_price_adon;
                    } else {
                        return $row->price + $amount_ppn - $amount_discount + $amount_price_adon;
                    }
                })
                ->addColumn('action', function ($row) {
                    return '
    <a href="javascript:void(0)" id="pay" data-id="' . $row->id . '" data-pelanggan_id="' . $row->id_pelanggan . '" class="btn btn-primary text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
        <i class="ti ti-currency-dollar"></i> PAY
    </a>
    <a href="javascript:void(0)" id="resend" data-id="' . $row->id . '" class="btn btn-success text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
        <i class="ti ti-send"></i>
    </a>
    <a href="' . $row->payment_url . '" target="_blank" data-id="' . $row->id . '" class="btn btn-secondary text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
        <i class="ti ti-link"></i>
    </a>
    <a href="javascript:void(0)" id="delete" data-id="' . $row->id . '" class="btn btn-danger text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
        <i class="ti ti-trash"></i>
    </a>';
                })
                ->rawColumns(['action', 'checkbox'])
                ->toJson();
        }

        if (multi_auth()->role === 'Mitra' && multi_auth()->billing !== 1) {
            return redirect()->back();
        }
        return view('backend.invoice.unpaid.index_new', compact('ppp'));
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
        $amount_discount = $discount;
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
        } elseif ($payment_type === 'Prabayar' && $billing_period === 'Renewable') {
            $periode = Carbon::createFromFormat('Y-m-d', $due_date);
            $get_periode = date('Y-m-d', strtotime($periode));
            $periode_format = tgl_indo($get_periode);
            $next_invoice = Carbon::createFromFormat('Y-m-d', $due_date)->addMonthsWithNoOverflow(1)->toDateString();
        }
        $cek_invoice_renewable = Invoice::where('id_pelanggan', $pelanggan_id)->where('status', 'unpaid')->exists();
        if ($billing_period === 'Renewable' && $cek_invoice_renewable) {
            return response()->json([
                'success' => false,
                'message' => 'Data Invoice Renewable Masih Ada',
            ], 422);
        } else {
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
        }

        if ($invoice) {
            $pelanggan = PppoeUser::where('id', $invoice->id_pelanggan);
            $pelanggan->update([
                'next_invoice' => $next_invoice,
            ]);
            $mappingadons = MappingAdons::query()->where('id_pelanggan_pppoe', $pelanggan->first()->id_pelanggan)
                ->get();
            $total_price_ad_monthly = 0;
            $total_price_ad = 0;
            foreach ($mappingadons as $mpp) {
                MappingAdons::create(
                    [
                        'id_mapping' => 0,
                        'description' => $mpp->description,
                        'ppn' => $mpp->ppn,
                        'monthly' => $mpp->monthly,
                        'qty' => $mpp->qty,
                        'price' => $mpp->price,
                        'no_invoice' => $invoice->no_invoice,
                        'id_pelanggan_pppoe' => $pelanggan->first()->id_pelanggan
                    ]);
                $totalPpnAd = 0;
                if($mpp->ppn > 0){
                    $totalPpnAd = $mpp->ppn * ($mpp->qty * $mpp->price) / 100;
                }
                $total_price_ad = $total_price_ad + (($mpp->qty * $mpp->price) + $totalPpnAd);

                if($mpp->monthly == "Yes"){
                    $total_price_ad_monthly = $total_price_ad_monthly + (($mpp->qty * $mpp->price) + $totalPpnAd);
                }
            }
            $invoice->update([
                'price_adon_monthly' => $total_price_ad_monthly,
                'price_adon' => $total_price_ad
            ]);

            $notif_it = BillingSetting::where('shortname', multi_auth()->shortname)->first()->notif_it;
            if ($notif_it === 1 && $wa !== null) {

                $mpwa = Mpwa::where('shortname', multi_auth()->shortname)->first();
                $row = PppoeUser::where('id', $invoice->id_pelanggan)->with('rprofile')->first();

                $description_adon = [];
                $mappingadons = MappingAdons::query()->where('id_pelanggan_pppoe', $row->id_pelanggan)
                    ->where('no_invoice', $invoice->no_invoice)->get();
                if($mappingadons){
                    foreach ($mappingadons as $mapp){
                        $description_adon[] = $mapp->description;
                    }
                }
//                $shortcode = ['[nama_lengkap]', '[id_pelanggan]', '[username]', '[password]', '[alamat]', '[paket_internet]', '[tipe_pembayaran]', '[siklus_tagihan]', '[no_invoice]', '[tgl_invoice]', '[harga]', '[ppn]', '[discount]', '[total]', '[jth_tempo]', '[periode]', '[subscribe]', '[link_pembayaran]'];
                $shortcode = [
                    '[nama_lengkap]', '[id_pelanggan]', '[username]', '[password]', '[alamat]', '[paket_internet]',
                    '[tipe_pembayaran]', '[siklus_tagihan]', '[no_invoice]', '[tgl_invoice]',
                    '[harga]', '[ppn]', '[discount]', '[total]', '[jth_tempo]', '[periode]', '[subscribe]', '[link_pembayaran]'
                    ,'[description_adon]','[total_adons]','[total_invoice]'
                ];
//                $source = [
//                    $row->full_name,
//                    $row->id_pelanggan,
//                    $row->username,
//                    $row->value,
//                    $row->address,
//                    $row->profile,
//                    $row->payment_type,
//                    $row->billing_period,
//                    $invoice->no_invoice,
//                    $invoice_date_format,
//                    $amount_format,
//                    $ppn,
//                    $discount,
//                    $total_format,
//                    $due_date_format,
//                    $periode_format,
//                    $invoice->subscribe,
//                    $invoice->payment_url,
//                    implode(', ', $description_adon),
//                    number_format($invoice->price_adon,0,',','.'),
//                    number_format(($total), 0, ',', '.')
//                ];
                $source = [
                    $row->full_name,
                    $row->id_pelanggan,
                    $row->username,
                    $row->value,
                    $row->address,
                    $row->c_profile->name,
                    $row->payment_type,
                    $row->billing_period,
                    $invoice->no_invoice,
                    Carbon::parse($invoice->invoice_date)->translatedFormat('d F Y'),
                    number_format($invoice->price, 0, ',', '.'),
                    $invoice->ppn,
                    $invoice->discount,
                    number_format(($total + $invoice->price_adon), 0, ',', '.'),
                    Carbon::parse($invoice->due_date)->translatedFormat('d F Y'),
                    Carbon::parse($invoice->period)->translatedFormat('F Y'),
                    $invoice->subscribe,
                    $invoice->payment_url,
                    implode(', ', $description_adon),
                    number_format($invoice->price_adon,0,',','.'),
                    number_format(($total), 0, ',', '.')
                ];
                $template = Watemplate::where('shortname', $row->shortname)->first()->invoice_terbit;
                $message = str_replace($shortcode, $source, $template);
                $message_format = str_replace('<br>', "\n", $message);
Log::info($message_format);
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
            'no_invoice' => $request->no_invoice,
            'payment_url' => multi_auth()->domain . '/pay/' . $request->no_invoice,
            'snap_token' => null,
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

        $unpaid = Invoice::query()->where('id_pelanggan', $invoice->id_pelanggan)
            ->where('status', 'unpaid')
            ->where('id', '!=', $request->id)->first();
        if($unpaid){
            if($unpaid->id < $request->id){
                return response()->json(['message' => "Harap selesaikan tagihan sebelumnya", "response_data_status" => false]);
            }
        }
        $invoice->response_data_status = true;
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
        if ($request->payment_type === 'Prabayar' && $request->billing_period === 'Fixed Date') {
            $next_due = Carbon::createFromFormat('Y-m-d', $request->due_date)->addMonthsWithNoOverflow(1);
            $next_invoice = Carbon::createFromFormat('Y-m-d', $request->due_date)->addMonthsWithNoOverflow(1);
        } elseif ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Fixed Date') {
            $next_due = Carbon::createFromFormat('Y-m-d', $request->due_date)->addMonthsWithNoOverflow(1);
            $next_invoice = Carbon::createFromFormat('Y-m-d', $request->due_date)->addMonthsWithNoOverflow(1);
        } elseif ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Billing Cycle') {
            $due_bc = BillingSetting::where('shortname', multi_auth()->shortname)->select('due_bc')->first();
            $next_due = Carbon::createFromFormat('Y-m-d', $request->due_date)->setDay($due_bc->due_bc)->addMonths(1);
            $next_invoice = Carbon::createFromFormat('Y-m-d', $request->due_date)->startOfMonth()->addMonths(1);
        } elseif ($request->payment_type === 'Prabayar' && $request->billing_period === 'Renewable') {
            $due_date = Carbon::createFromFormat('Y-m-d', $request->due_date);
            $pay_date = Carbon::now(); // misalnya ini adalah tanggal pembayaran (bisa juga $request->pay_date kalau ada)

            if ($due_date->lessThan($pay_date)) {
                // Jika due_date lebih kecil dari hari ini, set berdasarkan tanggal bayar
                $next_due = $pay_date->copy()->addMonthsWithNoOverflow(1);
                $next_invoice = $pay_date->copy()->addMonthsWithNoOverflow(1);
            } else {
                $next_due = $due_date->copy()->addMonthsWithNoOverflow(1);
                $next_invoice = $due_date->copy()->addMonthsWithNoOverflow(1);
            }
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

                $balancehistory = Balancehistory::create([
                    'id_mitra' => $transaksi->mitra_id,
                    'id_reseller' => '',
                    'tx_amount' => $transaksi->nominal,
                    'notes' => $transaksi->deskripsi,
                    'type' => 'in',
                    'tx_date' => Carbon::now(),
                    'id_transaksi' => $transaksi->id
                ]);

                $updatemitra = Mitra::where('shortname', multi_auth()->shortname)->where('id', $invoice->mitra_id)->first();
                if($updatemitra){
                    $lastbalance = $updatemitra->balance;
                    $updatemitra->update([
                        'balance' => $lastbalance + (int)$transaksi->nominal
                    ]);
                }
            }


            $notif_ps = BillingSetting::where('shortname', multi_auth()->shortname)->first();
            if ($notif_ps) {
                $notif_ps = $notif_ps->notif_ps;
                if ($notif_ps === 1 && $request->wa !== null) {
                    $amount_format = number_format($request->amount, 0, '.', '.');
                    $price_adon = $invoice->price_adon ? $invoice->price_adon : 0;
                    $total_format = number_format(($request->payment_total + $price_adon), 0, '.', '.');
                    $invoice_date_format = Carbon::parse($request->invoice_date)->translatedFormat('d F Y');
                    $due_date_format = Carbon::parse($request->due_date)->translatedFormat('d F Y');
                    $get_periode = date('Y-m-d', strtotime($request->period));
                    $periode_format = tgl_indo($get_periode);
                    $mpwa = Mpwa::where('shortname', multi_auth()->shortname)->first();
                    $smtp = SmtpSetting::where('shortname', multi_auth()->shortname)->first();
                    $paid_date_format = date('d/m/Y', strtotime($invoice->paid_date));
                    $row = PppoeUser::where('id', $invoice->id_pelanggan)->with('rprofile')->first();
                    // $inv = Invoice::where('id',$request->invoice_id)->first();
                    $shortcode = ['[nama_lengkap]', '[id_pelanggan]', '[username]', '[password]', '[alamat]', '[paket_internet]', '[tipe_pembayaran]', '[siklus_tagihan]', '[no_invoice]', '[tgl_invoice]', '[harga]', '[ppn]', '[discount]', '[total]', '[jth_tempo]', '[periode]', '[tgl_bayar]', '[subscribe]', '[metode_pembayaran]'];
                    $source = [$row->full_name, $row->id_pelanggan, $row->username, $row->value, $row->address, $row->profile, $request->payment_type, $request->billing_period, $request->no_invoice, $invoice_date_format, $amount_format, $request->ppn, $request->discount, $total_format, $due_date_format, $periode_format, $paid_date_format, $invoice->subscribe, $request->payment_method];
                    $template = Watemplate::where('shortname', $row->shortname)->first()->payment_paid;
                    $message = str_replace($shortcode, $source, $template);
                    $message_format = str_replace('<br>', "\n", $message);

                    if($mpwa){
                        if ($mpwa->mpwa_server_server == 'mpwa') {
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
                                curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5); // Timeout saat mencoba koneksi
                                curl_setopt($curl, CURLOPT_TIMEOUT, 5);        // Timeout total eksekusi cURL

                                $response = curl_exec($curl);
                                curl_close($curl);
                                // $result = json_decode($response, true);

                            } catch (\Exception $e) {
                                return $e->getMessage();
                            }
                        }

                        if ($mpwa->mpwa_server_server == 'radiusqu' && $mpwa->is_login == 1) {
                            $nomorhp = gantiformat_hp($row->wa);
                            $user_wa = User::where('shortname', $mpwa->shortname)->first();
                            $_id = $user_wa->whatsapp . "_" . env('APP_ENV');
                            $apiUrl = env('WHATSAPP_URL_NEW') . "send-message/" . $_id; //env('CACTI_ENDPOINT').'cacti/logout/'.$_id;
                            try {
                                $params = array(
                                    "jid" => $nomorhp . "@s.whatsapp.net",
                                    "content" => array(
                                        "text" => $message_format
                                    )
                                );
                                // Kirim POST request ke API eksternal
                                // Http::post($apiUrl, $params);
                                $response = Http::post($apiUrl, $params);
                                if ($response->successful()) {
                                    $json = $response->json();
                                    $status = $json->status;
                                    $receiver = $nomorhp;
                                    $shortname = $user_wa->shortname;
                                    save_wa_log($shortname, $receiver, $message_format, $status);
                                }
                            } catch (\Exception $e) {
                                //                            return "Invoice #{$invoice->no_invoice} berhasil diproses namun notifikasi gagal terkirim.";
                            }
                        }
                    }

                    // send email
                    if ($smtp->username != null && $smtp->username != "" && $smtp->password != null && $smtp->password != "") {
                        if($row->email){
                            try {
                                $data = [
                                    'messages' => $message,
                                    'user_name' => $row->username,
                                    'notification' => 'Paid Invoice Notification'
                                ];
                                app(CustomMailerService::class)->sendWithUserSmtpCron(
                                    'emails.test',
                                    $data,
                                    $row->email,
                                    'Invoice',
                                    $smtp
                                );
                            } catch (\Exception $e) {
                                //                            $results[] = "Invoice #{$invoice->no_invoice} berhasil diproses namun notifikasi gagal terkirim.";
                            }
                        }
                    }
                }
            }

            $cek_inv = Invoice::where('id_pelanggan', $request->pelanggan_id)->where('status', 'unpaid')->count();
            if ($request->ppp_status === '2' && $cek_inv === 0) {
                $ssh_user = env('IP_RADIUS_USERNAME');
                $ssh_host = env('IP_RADIUS_SERVER');
                $sshOptions = ['-o', 'BatchMode=yes', '-o', 'StrictHostKeyChecking=no'];
                $sshOptionsString = implode(' ', $sshOptions);

                $ppp = PppoeUser::where('id', $request->pelanggan_id);
                $ppp->update([
                    'status' => 1,
                ]);
                if ($request->nas === null) {
                    $nas = RadiusNas::where('shortname', multi_auth()->shortname)->select('nasname', 'secret')->get();
                    foreach ($nas as $row) {
                        $userAttr = escapeshellarg("User-Name = '{$request->pppoe_user}'");
                        $command = "echo $userAttr | radclient -r 1 {$row['nasname']}:3799 disconnect {$row['secret']}";
                        $ssh_command = "ssh {$sshOptionsString} {$ssh_user}@{$ssh_host} \"{$command}\"";
                        $process = Process::run($ssh_command);
                    }
                } else {
                    $secret = RadiusNas::where('shortname', multi_auth()->shortname)->where('nasname', $request->nas)->select('secret')->first();
                    $userAttr = escapeshellarg("User-Name = '{$request->pppoe_user}'");
                    $command = "echo $userAttr | radclient -r 1 {$request->nas}:3799 disconnect {$secret->secret}";
                    $ssh_command = "ssh {$sshOptionsString} {$ssh_user}@{$ssh_host} \"{$command}\"";
                    $process = Process::run($ssh_command);
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
                if ($invoice->payment_type === 'Prabayar' && $invoice->billing_period === 'Fixed Date') {
                    $next_due = Carbon::createFromFormat('Y-m-d', $invoice->due_date)->addMonthsWithNoOverflow(1);
                    $next_invoice = Carbon::createFromFormat('Y-m-d', $invoice->due_date)->addMonthsWithNoOverflow(1);
                } elseif ($invoice->payment_type === 'Pascabayar' && $invoice->billing_period === 'Fixed Date') {
                    $next_due = Carbon::createFromFormat('Y-m-d', $invoice->due_date)->addMonthsWithNoOverflow(1);
                    $next_invoice = Carbon::createFromFormat('Y-m-d', $invoice->due_date)->addMonthsWithNoOverflow(1);
                } elseif ($invoice->payment_type === 'Pascabayar' && $invoice->billing_period === 'Billing Cycle') {
                    $due_bc = BillingSetting::where('shortname', multi_auth()->shortname)->select('due_bc')->first();
                    $next_due = Carbon::createFromFormat('Y-m-d', $invoice->due_date)->setDay($due_bc->due_bc)->addMonths(1);
                    $next_invoice = Carbon::createFromFormat('Y-m-d', $invoice->due_date)->startOfMonth()->addMonths(1);
                } elseif ($invoice->payment_type === 'Prabayar' && $invoice->billing_period === 'Renewable') {
                    $due_date = Carbon::createFromFormat('Y-m-d', $invoice->due_date);
                    $pay_date = Carbon::now(); // misalnya ini adalah tanggal pembayaran (bisa juga $request->pay_date kalau ada)

                    if ($due_date->lessThan($pay_date)) {
                        // Jika due_date lebih kecil dari hari ini, set berdasarkan tanggal bayar
                        $next_due = $pay_date->copy()->addMonthsWithNoOverflow(1);
                        $next_invoice = $pay_date->copy()->addMonthsWithNoOverflow(1);
                    } else {
                        $next_due = $due_date->copy()->addMonthsWithNoOverflow(1);
                        $next_invoice = $due_date->copy()->addMonthsWithNoOverflow(1);
                    }
                }
                $pelanggan = PppoeUser::where('id', $invoice->id_pelanggan);
                $pelanggan->update([
                    'next_due' => $next_due,
                    'next_invoice' => $next_invoice,
                ]);

                $full_name = $invoice->rpppoe->full_name;
                $nominal_ppn = ($invoice->price * $invoice->ppn) / 100;
                $nominal_discount = $invoice->discount;
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

                    $balancehistory = Balancehistory::create([
                        'id_mitra' => $transaksi->mitra_id,
                        'id_reseller' => '',
                        'tx_amount' => $transaksi->nominal,
                        'notes' => $transaksi->deskripsi,
                        'type' => 'in',
                        'tx_date' => Carbon::now(),
                        'id_transaksi' => $transaksi->id
                    ]);

                    $updatemitra = Mitra::where('shortname', multi_auth()->shortname)->where('id', $invoice->mitra_id)->first();
                    if($updatemitra){
                        $lastbalance = $updatemitra->balance;
                        $updatemitra->update([
                            'balance' => $lastbalance + (int)$transaksi->nominal
                        ]);
                    }
                }

                if ($notif_ps === 1 && $invoice->rpppoe->wa !== null) {
                    $amount_format = number_format($invoice->price, 0, '.', '.');
                    $total_format = number_format($payment_total, 0, '.', '.');
                    $invoice_date_format = Carbon::parse($invoice->invoice_date)->translatedFormat('d F Y');
                    $due_date_format = Carbon::parse($invoice->due_date)->translatedFormat('d F Y');
                    $get_periode = date('Y-m-d', strtotime($invoice->period));
                    $periode_format = tgl_indo($get_periode);
                    $mpwa = Mpwa::where('shortname', multi_auth()->shortname)->first();
                    $smtp = SmtpSetting::where('shortname', multi_auth()->shortname)->first();
                    $paid_date_format = date('d/m/Y', strtotime($invoice->paid_date));
                    // $row = PppoeUser::where('id', $invoice->id_pelanggan)->with('rprofile')->first();
                    // $inv = Invoice::where('id',$request->invoice_id)->first();
                    $shortcode = ['[nama_lengkap]', '[id_pelanggan]', '[username]', '[password]', '[alamat]', '[paket_internet]', '[tipe_pembayaran]', '[siklus_tagihan]', '[no_invoice]', '[tgl_invoice]', '[harga]', '[ppn]', '[discount]', '[total]', '[jth_tempo]', '[periode]', '[tgl_bayar]', '[subscribe]', '[metode_pembayaran]'];
                    $source = [$invoice->rpppoe->full_name, $invoice->rpppoe->id_pelanggan, $invoice->rpppoe->username, $invoice->rpppoe->value, $invoice->rpppoe->address, $invoice->rpppoe->profile, $invoice->payment_type, $invoice->billing_period, $invoice->no_invoice, $invoice_date_format, $amount_format, $invoice->ppn, $invoice->discount, $total_format, $due_date_format, $periode_format, $paid_date_format, $invoice->subscribe, $request->payment_method];
                    $template = Watemplate::where('shortname', $invoice->shortname)->first()->payment_paid;
                    $message = str_replace($shortcode, $source, $template);
                    $message_format = str_replace('<br>', "\n", $message);

                    if ($mpwa->mpwa_server_server == 'mpwa') {
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
                    }

                    if ($mpwa->mpwa_server_server == 'radiusqu' && $mpwa->is_login == 1) {
                        $nomorhp = gantiformat_hp($invoice->rpppoe->wa);
                        $user_wa = User::where('shortname', $mpwa->shortname)->first();
                        $_id = $user_wa->whatsapp . "_" . env('APP_ENV');
                        $apiUrl = env('WHATSAPP_URL_NEW') . "send-message/" . $_id; //env('CACTI_ENDPOINT').'cacti/logout/'.$_id;
                        try {
                            $params = array(
                                "jid" => $nomorhp . "@s.whatsapp.net",
                                "content" => array(
                                    "text" => $message_format
                                )
                            );
                            // Kirim POST request ke API eksternal
                            // Http::post($apiUrl, $params);
                            $response = Http::post($apiUrl, $params);
                            if ($response->successful()) {
                                $json = $response->json();
                                $status = $json->status;
                                $receiver = $nomorhp;
                                $shortname = $user_wa->shortname;
                                save_wa_log($shortname, $receiver, $message_format, $status);
                            }
                        } catch (\Exception $e) {
                            $results[] = "Invoice #{$invoice->no_invoice} berhasil diproses namun notifikasi gagal terkirim.";
                        }
                    }

                    // send email
                    if ($smtp) {
                        try {
                            $data = [
                                'messages' => $message,
                                'user_name' => $invoice->rpppoe->username,
                                'notification' => 'Paid Invoice Notification'
                            ];
                            app(CustomMailerService::class)->sendWithUserSmtpCron(
                                'emails.test',
                                $data,
                                $invoice->rpppoe->email,
                                'Invoice',
                                $smtp
                            );
                        } catch (\Exception $e) {
                            $results[] = "Invoice #{$invoice->no_invoice} berhasil diproses namun notifikasi gagal terkirim.";
                        }
                    }
                    $results[] = "Invoice #{$invoice->no_invoice} berhasil diproses dan notifikasi sukses terkirim.";
                } else {
                    $results[] = "Invoice #{$invoice->no_invoice} berhasil diproses.";
                }

                $cek_inv = Invoice::where('id_pelanggan', $invoice->id_pelanggan)->where('status', 'unpaid')->count();
                if ($invoice->rpppoe->status === 2 && $cek_inv === 0) {
                    $ssh_user = env('IP_RADIUS_USERNAME');
                    $ssh_host = env('IP_RADIUS_SERVER');
                    $sshOptions = ['-o', 'BatchMode=yes', '-o', 'StrictHostKeyChecking=no'];
                    $sshOptionsString = implode(' ', $sshOptions);
                    $ppp = PppoeUser::where('id', $invoice->id_pelanggan);
                    $ppp->update([
                        'status' => 1,
                    ]);
                    $username = $invoice->rpppoe->username;
                    $nas = $invoice->rpppoe->nas;
                    if ($invoice->rpppoe->nas === null) {
                        $nas = RadiusNas::where('shortname', multi_auth()->shortname)->select('nasname', 'secret')->get();
                        foreach ($nas as $row) {
                            $escaped = escapeshellarg("User-Name = '{$username}'");
                            $command = "echo $escaped | radclient -r 1 {$row['nasname']}:3799 disconnect {$row['secret']}";
                            $ssh_command = "ssh {$sshOptionsString} {$ssh_user}@{$ssh_host} \"{$command}\"";
                            $process = Process::run($ssh_command);
                        }
                    } else {
                        $secret = RadiusNas::where('shortname', multi_auth()->shortname)->where('nasname', $nas)->select('secret')->first();
                        $escaped = escapeshellarg("User-Name = '{$username}'");
                        $command = "echo $escaped | radclient -r 1 {$nas}:3799 disconnect {$secret->secret}";
                        $ssh_command = "ssh {$sshOptionsString} {$ssh_user}@{$ssh_host} \"{$command}\"";
                        $process = Process::run($ssh_command);
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
        $amount_discount = $invoice->discount;
        $total = $invoice->price + $amount_ppn - $amount_discount;

        // $notif_it = BillingSetting::where('shortname', $invoice->shortname)->first()->notif_it;
        $user = PppoeUser::where('id', $invoice->id_pelanggan)->with('rprofile')->first();
        if ($user->wa !== null) {
            $mpwa = Mpwa::where('shortname', $invoice->shortname)->first();
            $description_adon = [];
            $mappingadons = MappingAdons::query()->where('id_pelanggan_pppoe', $user->id_pelanggan)->where('no_invoice', $invoice->no_invoice)->get();
            if($mappingadons){
                foreach ($mappingadons as $mapp){
                    $description_adon[] = $mapp->description;
                }
            }
            $shortcode = ['[nama_lengkap]', '[id_pelanggan]', '[username]', '[password]', '[alamat]', '[paket_internet]', '[tipe_pembayaran]', '[siklus_tagihan]', '[no_invoice]', '[tgl_invoice]', '[harga]', '[ppn]', '[discount]',
                '[total]', '[jth_tempo]', '[periode]', '[subscribe]', '[link_pembayaran]'
                ,'[description_adon]','[total_adons]','[total_invoice]'];
            $source = [$user->full_name, $user->id_pelanggan, $user->username, $user->value, $user->address, $user->rprofile->name, $invoice->payment_type, $invoice->billing_period, $invoice->no_invoice, Carbon::parse($invoice->invoice_date)->translatedFormat('d F Y'), number_format($invoice->price, 0, ',', '.'), $invoice->ppn, $invoice->discount,
                number_format(($total + $invoice->price_adon), 0, ',', '.'), Carbon::parse($invoice->due_date)->translatedFormat('d F Y'), $periode_format, $subscribe, $invoice->payment_url,
                implode(', ', $description_adon), number_format($invoice->price_adon,0,',','.'), number_format(($total), 0, ',', '.')];
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
