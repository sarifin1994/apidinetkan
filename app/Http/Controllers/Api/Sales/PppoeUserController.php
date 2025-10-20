<?php


namespace App\Http\Controllers\Api\Sales;


use App\Exports\PppoeUserExport;
use App\Imports\PppoeUserImport;
use App\Models\CountNumbering;
use App\Models\Invoice\Invoice;
use App\Models\Keuangan\Transaksi;
use App\Models\LicenseDinetkan;
use App\Models\Mapping\Odp;
use App\Models\Mapping\Pop;
use App\Models\MappingAdons;
use App\Models\Mikrotik\Nas;
use App\Models\Owner\License;
use App\Models\Partnership\Mitra;
use App\Models\Pppoe\PppoeProfile;
use App\Models\Pppoe\PppoeUser;
use App\Models\Province;
use App\Models\Radius\RadiusNas;
use App\Models\Radius\RadiusSession;
use App\Models\Setting\BillingSetting;
use App\Models\SmtpSetting;
use App\Models\User;
use App\Models\Whatsapp\Mpwa;
use App\Models\Whatsapp\Watemplate;
use App\Services\CustomMailerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Activitylog\Contracts\Activity;
use Yajra\DataTables\Facades\DataTables;

class PppoeUserController
{

    public function index(Request $request)
    {
        $licensedinetkan = [];
        if ($request->user()->role === 'Mitra') {
            $totaluser = PppoeUser::where('shortname', $request->user()->shortname)->where('mitra_id', $request->user()->id)->count();
            $totalactive = PppoeUser::where('shortname', $request->user()->shortname)->where('mitra_id', $request->user()->id)->where('status', 1)->count();
            $totalsuspend = PppoeUser::where('shortname', $request->user()->shortname)->where('mitra_id', $request->user()->id)->where('status', 2)->count();
            $totaldisabled = PppoeUser::where('shortname', $request->user()->shortname)->where('mitra_id', $request->user()->id)->where('status', 0)->count();
        } else {
            $totaluser = PppoeUser::where('shortname', $request->user()->shortname)->count();
            $totalactive = PppoeUser::where('shortname', $request->user()->shortname)->where('status', 1)->count();
            $totalsuspend = PppoeUser::where('shortname', $request->user()->shortname)->where('status', 2)->count();
            $totaldisabled = PppoeUser::where('shortname', $request->user()->shortname)->where('status', 0)->count();
        }
        $areas = Pop::where('shortname', $request->user()->shortname)->select('id', 'kode_area', 'deskripsi')->orderBy('kode_area', 'desc')->get();
        $odps = Odp::where('shortname', $request->user()->shortname)->get();
        $nas = Nas::where('shortname', $request->user()->shortname)->select('ip_router', 'name')->get();
        if ($request->user()->role !== 'Mitra') {
            if($request->user()->is_dinetkan == 1 || $request->user()->ext_role == "dinetkan"){
                $licensedinetkan = LicenseDinetkan::get()->map(function ($item) {
                    $obj = new \stdClass();
                    $obj->id_dinetkan = $item->id . '_dinetkan';
                    $obj->name = $item->name;
                    return $obj;
                });
                $licensedinetkan = json_decode(json_encode($licensedinetkan->values()));
            }
            $profiles = PppoeProfile::where('shortname', $request->user()->shortname)->where('status', 1)->select('id', 'name', 'price')->get();
        } else {
            $id = json_decode($request->user()->profile, true); // true biar hasilnya array, bukan object
            if ($id !== null) {
                $profiles = PppoeProfile::where('shortname', $request->user()->shortname)->whereIn('id', $id)->where('status', 1)->select('id', 'name', 'price')->get();
            } else {
                $profiles = [];
            }
        }
        $mitras = Mitra::where('shortname', $request->user()->shortname)->where('status', 1)->select('id', 'name', 'id_mitra')->get();

        $perPage = $request->get('per_page', 10); // default 10 item per halaman
        if ($request->user()->role === 'Mitra') {
            $query = PppoeUser::query()
                ->where('user_pppoe.shortname', $request->user()->shortname)
                ->where('mitra_id', $request->user()->id)->with('mnas', 'session', 'rprofile', 'rarea', 'rodp', 'rmitra');
        } else {
            $query = PppoeUser::query()
                ->where('user_pppoe.shortname', $request->user()->shortname)
                ->when(request('area'), function ($query, $area) {
                    $query->where('kode_area', $area); // atau sesuaikan dengan nama kolom relasi jika pakai relasi rarea
                })
                ->when(request('status'), function ($query, $status) {
                    $query->where('status', $status);
                })
                ->when(request('nas'), function ($query, $nas) {
                    $query->where('nas', $nas); // atau sesuaikan dengan relasi `mnas`
                })
                ->when(request('mitra'), function ($query, $mitra) {
                    $query->where('mitra_id', $mitra); // atau relasi `rmitra`
                })
                ->with('mnas', 'session', 'rprofile', 'rarea', 'rodp', 'rmitra');
        }
        $users = $query->orderBy('id', 'desc')->paginate($perPage);
        return response()->json($users);
//        ->addColumn('profile_name', function ($row) {
//        $name = "";
//        if($row->rprofile->name ){
//            $name = $row->rprofile->name;
//        } else{
//            $idx = str_replace('_dinetkan', '', $row->profile_id);
//            $licensedinetkan = LicenseDinetkan::where('id', $idx)->first();
//            if($licensedinetkan != null){
//                $name = $licensedinetkan->name;
//            }
//        }
//        return $name;
//    })

    }
    public function getKodeOdp(Request $request)
    {
        $data['odp'] = Odp::where('shortname', $request->user()->shortname)
            ->where('kode_area_id', $request->kode_area_id)
            ->orderBy('kode_odp')
            ->get(['kode_odp']);
        return response()->json($data);
    }

    public function getPrice(Request $request)
    {
        $data = [];
        if (str_contains($request->id, '_dinetkan')) {
            // String mengandung '_dinetkan'
            $id = str_replace('_dinetkan', '', $request->id);
            $data = LicenseDinetkan::where('id', $id)->get(['price', 'ppn']);
            return response()->json($data);
        }
        $data = PppoeProfile::where('shortname', $request->user()->shortname)
            ->where('id', $request->id)
            ->get(['price']);
        return response()->json($data);
    }

    public function show(PppoeUser $user)
    {
        $data = PppoeUser::with('mnas', 'rprofile', 'rarea', 'rodp')
            ->with('addon')->find($user->id);
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'min:1', 'max:255', Rule::unique('frradius_auth.user_pppoe')->where('shortname', $request->user()->shortname)],
            'password' => 'required_if:type,pppoe',
            'profile' => 'required|string',
            'full_name' => 'required|string|min:2',
            'email' => 'required',
            'ktp' => 'required',
            'province_id' => 'required',
            'regency_id' => 'required',
            'district_id' => 'required',
            'village_id' => 'required',
            'type' => 'required'
        ]);

//        if ($validator->fails()) {
//            return response()->json([
//                'error' => $validator->errors(),
//            ]);
//        }
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->toArray(),
                // contoh hasil:
                // { "username": ["The username field is required."], "email": ["The email must be a valid email address."] }
            ], 422);
        }

        if ($request->mitra_id == null) {
            $mitra_id = 0;
        } else {
            $mitra_id = $request->mitra_id;
        }

        if (in_array($request->user()->role, ['Admin', 'Teknisi'])) {
            if ($request->status === null) {
                $status = 0;
            } else {
                $status = $request->status;
            }
        } else {
            $status = 0;
        }

        if ($request->user()->role !== 'Mitra') {
            if(str_contains($request->profile, '_dinetkan')){

            }else{
                if (PppoeUser::where('shortname', $request->user()->shortname)->count() >= License::where('id', $request->user()->license_id)->select('limit_pppoe')->first()->limit_pppoe) {
                    return response()->json([
                        'error' => 'Maaf lisensi anda sudah limit, silakan upgrade!',
                    ]);
                }
            }
        }

        if ($request->option_billing === '1') {
            $today = Carbon::now()->format('Y-m-d');
            $cek_reg_date = Carbon::createFromFormat('Y-m-d', $today)->subMonthsWithNoOverflow(2)->toDateString();
            $cek_reg_date_bc = Carbon::createFromFormat('Y-m-d', $today)->startOfMonth()->subMonthsWithNoOverflow(2)->toDateString();
            $tgl = date('d', strtotime($request->reg_date));
//            $no_invoice = date('m') . rand(10000000, 99999999);
            $no_invoice = build_no_invoice('RQ');

            if ($request->reg_date < $cek_reg_date) {
                return response()->json([
                    'error' => 'Maaf gagal membuat user, silakan ubah tanggal aktif!',
                ]);
            } elseif ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Billing Cycle' && $request->reg_date < $cek_reg_date_bc) {
                return response()->json([
                    'success' => 'Maaf gagal membuat user, silakan ubah tanggal aktif!',
                ]);

                // SETTING PARAMETER

                // PRABAYAR PAID
            } elseif ($request->payment_type === 'Prabayar' && $request->payment_status === 'paid' && $request->reg_date >= $cek_reg_date) {
                $next_due = Carbon::createFromFormat('Y-m-d', $request->reg_date)->addMonthsWithNoOverflow(1);
                $next_invoice = Carbon::createFromFormat('Y-m-d', $request->reg_date)->addMonthsWithNoOverflow(1);
                $price = str_replace('.', '', $request->amount);
                $total = str_replace('.', '', $request->payment_total);
                $period = Carbon::createFromFormat('Y-m-d', $request->reg_date);
                $awal = date('d/m/Y', strtotime($request->reg_date));
                $akhir = date('d/m/Y', strtotime('+1 month', strtotime($request->reg_date)));

                // PRABAYAR UNPAID
            } elseif ($request->payment_type === 'Prabayar' && $request->payment_status === 'unpaid' && $request->reg_date > $cek_reg_date) {
                $next_due = $request->reg_date;
                $next_invoice = Carbon::createFromFormat('Y-m-d', $request->reg_date)->addMonthsWithNoOverflow(1);
                $price = str_replace('.', '', $request->amount);
                $period = Carbon::createFromFormat('Y-m-d', $request->reg_date);
                $awal = date('d/m/Y', strtotime($request->reg_date));
                $akhir = date('d/m/Y', strtotime('+1 month', strtotime($request->reg_date)));
                // PASCABAYAR FIXED DATE
            } elseif ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Fixed Date' && $request->reg_date > $cek_reg_date) {
                $next_due = Carbon::createFromFormat('Y-m-d', $request->reg_date)->addMonthsWithNoOverflow(1);
                $next_invoice = Carbon::createFromFormat('Y-m-d', $request->reg_date)->addMonthsWithNoOverflow(1);

                // PASCABAYAR BILLING CYCLE PRORATE
            } elseif ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Billing Cycle' && $request->reg_date < $cek_reg_date_bc) {
                $due_bc = BillingSetting::where('shortname', $request->user()->shortname)->select('due_bc')->first();
                $next_due = Carbon::createFromFormat('Y-m-d', $request->reg_date)->setDay($due_bc->due_bc)->addMonths(1);
                $next_invoice = Carbon::createFromFormat('Y-m-d', $request->reg_date)->startOfMonth()->addMonths(1);
                $price = str_replace('.', '', $request->amount);
                $period = Carbon::createFromFormat('Y-m-d', $request->reg_date)->toDateString();
                $next_invoice = Carbon::createFromFormat('Y-m-d', $request->reg_date)->startOfMonth()->addMonthsWithNoOverflow(1)->toDateString();
                $akhir_day = Carbon::createFromFormat('Y-m-d', $request->reg_date)->endOfMonth()->toDateString();

                $awal = date('d/m/Y', strtotime($request->reg_date));
                $akhir = date('d/m/Y', strtotime($akhir_day));

                // MASIH PR DISINI
                $jml_day = Carbon::createFromFormat('Y-m-d', $request->reg_date)->month()->daysInMonth;
                $jml_usage = Carbon::parse($request->reg_date)->diffInDays($akhir_day);
                $daily_price0 = $price / $jml_day;
                $daily_price = number_format($daily_price0, 0, '.', '');
                $prorate = $jml_usage * $daily_price;
                $total = number_format($prorate, 0, '.', '');

                // PASCABAYAR BILILNG CYCLE NON PRORATE
            } elseif ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Billing Cycle' && $request->reg_date > $cek_reg_date_bc) {
                $due_bc = BillingSetting::where('shortname', $request->user()->shortname)->select('due_bc')->first();
                $next_due = Carbon::createFromFormat('Y-m-d', $request->reg_date)->setDay($due_bc->due_bc)->addMonths(1);
                $next_invoice = Carbon::createFromFormat('Y-m-d', $request->reg_date)->startOfMonth()->addMonths(1);
            }
//            dd($request);exit;
            $user = PppoeUser::create([
                'shortname' => $request->user()->shortname,
                'username' => $request->username,
                'value' => $request->password,
                'profile' => $request->profile,
                'nas' => $request->nas,
                'lock_mac' => $request->lock_mac,
                'mac' => $request->mac,
                'status' => 1,
                'full_name' => $request->full_name,
                'id_pelanggan' => generateMemberIdPPPOE(), //$request->id_pelanggan,
                'profile_id' => $request->profile_id,
                'kode_area' => $request->kode_area,
                'kode_odp' => $request->kode_odp,
                'mitra_id' => $mitra_id,
                'wa' => $request->wa,
                'address' => $request->address,
                'payment_type' => $request->payment_type,
                'payment_status' => $request->payment_status,
                'billing_period' => $request->billing_period,
                'ppn' => $request->ppn,
                'discount' => $request->discount,
                'reg_date' => $request->reg_date,
                'next_due' => $next_due,
                'next_invoice' => $next_invoice,
                'tgl' => $tgl,
                'status' => $status,
                'created_by' => $request->user()->username ?? $request->user()->name,
                'email' => $request->email,
                'ktp' => $request->ktp,
                'npwp' => $request->npwp,
                'province_id' => $request->province_id,
                'regency_id' => $request->regency_id,
                'district_id' => $request->district_id,
                'village_id' => $request->village_id,
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'type' => $request->type,
                'ip_address' => $request->ip_address,
                'pks' => $request->pks,
                'sn_modem' => $request->sn_modem,
            ]);
            $komisix = 0;
            $profilex = PppoeProfile::where('id', $request->profile_id)->first();
            if($profilex != null){
                $komisix = $komisix + $profilex->fee_mitra;
            }

            if(str_contains($request->profile_id, '_dinetkan')){
                $komisix = 0;
                $idy = str_replace('_dinetkan', '', $request->profile_id);
                $profiley = LicenseDinetkan::where('id', $idy)->first();
                if($profiley != null){
                    $komisix = $komisix + $profiley->komisi_mitra;
                }
            }

            // PRABAYAR PAID
            if ($request->payment_type === 'Prabayar' && $request->payment_status === 'paid' && $request->reg_date >= $cek_reg_date) {
                $invoice = Invoice::create([
                    'shortname' => $request->user()->shortname,
                    'id_pelanggan' => $user->id,
                    'no_invoice' => $no_invoice,
                    'item' => "Internet: $user->id_pelanggan | $user->profile",
                    'price' => $price,
                    'ppn' => $user->ppn,
                    'discount' => $user->discount,
                    'invoice_date' => $user->reg_date,
                    'due_date' => $user->reg_date,
                    'period' => $period,
                    'subscribe' => $awal . ' ' . 's/d' . ' ' . $akhir,
                    'reg_date' => $user->reg_date,
                    'next_due' => $next_due,
                    'payment_type' => $user->payment_type,
                    'billing_period' => $user->billing_period,
                    'payment_url' => $request->user()->domain . '/pay/' . $no_invoice,
                    'paid_date' => $user->reg_date,
                    'status' => 'paid',
                    'mitra_id' => $user->mitra_id,
                    'komisi' => $komisix
                ]);

                $transaksi = Transaksi::create([
                    'shortname' => $request->user()->shortname,
                    'id_data' => $invoice->id,
                    'tipe' => 'Pemasukan',
                    'kategori' => 'Invoice',
                    'deskripsi' => "PSB Payment #$invoice->no_invoice a.n $user->full_name",
                    'nominal' => $total,
                    'tanggal' => Carbon::now(),
                    'metode' => 'Cash',
                    'created_by' => $request->user()->username ?? $request->user()->name,
                ]);
                $desc                   = $request->desc_ad;
                $ppn_ad                 = $request->ppn_ad;
                $monthly                = $request->monthly_ad;
                $qty                    = $request->qty_ad;
                $price                  = $request->price_ad;
                $total_price_ad         = 0;
                $total_price_ad_monthly = 0;
                if(isset($request->desc_ad)){
                    if(count($desc) > 0){
                        for ($i = 0; $i < count($desc); $i++) {
                            $mappingadons = MappingAdons::create(
                                [
                                    'id_mapping' => 0,
                                    'description' => $desc[$i],
                                    'ppn' => $ppn_ad[$i],
                                    'monthly' => $monthly[$i],
                                    'qty' => $qty[$i],
                                    'price' => $price[$i],
                                    'no_invoice' => $invoice->no_invoice,
                                    'id_pelanggan_pppoe' => $user->id_pelanggan
                                ]);
                            $totalPpnAd = 0;
                            if($ppn_ad[$i] > 0){
                                $totalPpnAd = $ppn_ad[$i] * ($qty[$i] * $price[$i]) / 100;
                            }
                            $total_price_ad = $total_price_ad + (($qty[$i] * $price[$i]) + $totalPpnAd);

                            if($monthly[$i] == "Yes"){
                                $total_price_ad_monthly = $total_price_ad_monthly + (($qty[$i] * $price[$i]) + $totalPpnAd);
                            }
                        }
                    }
                    $invoice->update([
                        'price_adon_monthly' => $total_price_ad_monthly,
                        'price_adon' => $total_price_ad
                    ]);
                }
                // PRABAYAR UNPAID
            } elseif ($request->payment_type === 'Prabayar' && $request->payment_status === 'unpaid' && $request->reg_date > $cek_reg_date) {
                $invoice = Invoice::create([
                    'shortname' => $request->user()->shortname,
                    'id_pelanggan' => $user->id,
                    'no_invoice' => $no_invoice,
                    'item' => "Internet: $user->id_pelanggan | $user->profile",
                    'price' => $price,
                    'ppn' => $user->ppn,
                    'discount' => $user->discount,
                    'invoice_date' => $user->reg_date,
                    'due_date' => $user->reg_date,
                    'period' => $period,
                    'subscribe' => $awal . ' ' . 's/d' . ' ' . $akhir,
                    'reg_date' => $user->reg_date,
                    'next_due' => $next_due,
                    'payment_type' => $user->payment_type,
                    'billing_period' => $user->billing_period,
                    'payment_url' => $request->user()->domain . '/pay/' . $no_invoice,
                    'paid_date' => $user->reg_date,
                    'status' => 'unpaid',
                    'mitra_id' => $user->mitra_id,
                    'komisi' => $komisix
                ]);

                $desc                   = $request->desc_ad;
                $ppn_ad                 = $request->ppn_ad;
                $monthly                = $request->monthly_ad;
                $qty                    = $request->qty_ad;
                $price                  = $request->price_ad;
                $total_price_ad         = 0;
                $total_price_ad_monthly = 0;
                if(isset($request->desc_ad)){
                    if(count($desc) > 0){
                        for ($i = 0; $i < count($desc); $i++) {
                            $mappingadons = MappingAdons::create(
                                [
                                    'id_mapping' => 0,
                                    'description' => $desc[$i],
                                    'ppn' => $ppn_ad[$i],
                                    'monthly' => $monthly[$i],
                                    'qty' => $qty[$i],
                                    'price' => $price[$i],
                                    'no_invoice' => $invoice->no_invoice,
                                    'id_pelanggan_pppoe' => $user->id_pelanggan
                                ]);
                            $totalPpnAd = 0;
                            if($ppn_ad[$i] > 0){
                                $totalPpnAd = $ppn_ad[$i] * ($qty[$i] * $price[$i]) / 100;
                            }
                            $total_price_ad = $total_price_ad + (($qty[$i] * $price[$i]) + $totalPpnAd);

                            if($monthly[$i] == "Yes"){
                                $total_price_ad_monthly = $total_price_ad_monthly + (($qty[$i] * $price[$i]) + $totalPpnAd);
                            }
                        }
                    }
                    $invoice->update([
                        'price_adon_monthly' => $total_price_ad_monthly,
                        'price_adon' => $total_price_ad
                    ]);
                }
            }
        } else {
            $user = PppoeUser::create([
                'shortname' => $request->user()->shortname,
                'username' => $request->username,
                'value' => $request->password,
                'profile' => $request->profile,
                'nas' => $request->nas,
                'lock_mac' => $request->lock_mac,
                'mac' => $request->mac,
                'status' => 1,
                'full_name' => $request->full_name,
                'id_pelanggan' => generateMemberIdPPPOE(), //->id_pelanggan,
                'profile_id' => $request->profile_id,
                'kode_area' => $request->kode_area,
                'kode_odp' => $request->kode_odp,
                'mitra_id' => $mitra_id,
                'wa' => $request->wa,
                'address' => $request->address,
                'status' => $status,
                'created_by' => $request->user()->username ?? $request->user()->name,
                'email' => $request->email,
                'ktp' => $request->ktp,
                'npwp' => $request->npwp,
                'province_id' => $request->province_id,
                'regency_id' => $request->regency_id,
                'district_id' => $request->district_id,
                'village_id' => $request->village_id,
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'type' => $request->type,
                'ip_address' => $request->ip_address,
                'pks' => $request->pks,
                'sn_modem' => $request->sn_modem,
            ]);
        }

        if ($request->user()->role === 'Mitra') {
            $totaluser = PppoeUser::where('shortname', $request->user()->shortname)->where('mitra_id', $request->user()->id)->count();
            $totalactive = PppoeUser::where('shortname', $request->user()->shortname)->where('mitra_id', $request->user()->id)->where('status', 1)->count();
            $totalsuspend = PppoeUser::where('shortname', $request->user()->shortname)->where('mitra_id', $request->user()->id)->where('status', 2)->count();
            $totaldisabled = PppoeUser::where('shortname', $request->user()->shortname)->where('mitra_id', $request->user()->id)->where('status', 0)->count();
        } else {
            $totaluser = PppoeUser::where('shortname', $request->user()->shortname)->count();
            $totalactive = PppoeUser::where('shortname', $request->user()->shortname)->where('status', 1)->count();
            $totalsuspend = PppoeUser::where('shortname', $request->user()->shortname)->where('status', 2)->count();
            $totaldisabled = PppoeUser::where('shortname', $request->user()->shortname)->where('status', 0)->count();
        }

        if ($request->user()->role !== 'Mitra') {
            activity()
                ->tap(function (Activity $activity) {
                    $activity->shortname = $request->user()->shortname;
                })
                ->event('Create')
                ->log('Create New User PPPoE: ' . $request->username . '');
        }
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'totaluser' => $totaluser,
            'totalactive' => $totalactive,
            'totalsuspend' => $totalsuspend,
            'totaldisabled' => $totaldisabled,
        ]);
    }

    public function update(Request $request, PppoeUser $user)
    {
        // $validator = Validator::make($request->all(), [
        //     'username' => 'required|string|min:5',
        //     'password' => 'required|string|min:2',
        //     'profile' => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'error' => $validator->errors(),
        //     ]);
        // }

        if ($request->mitra_id == null) {
            $mitra_id = 0;
        } else {
            $mitra_id = $request->mitra_id;
        }
        if ($request->user()->role === 'Admin') {
            if ($request->option_billing === '1') {
                // PRABAYAR FIXED DATE
                if ($request->payment_type === 'Prabayar' && $request->billing_period === 'Fixed Date') {
                    $next_due = $request->next_due;
                    $next_invoice = $request->next_due;
                    if ($request->reg_date === null) {
                        $reg_date = Carbon::createFromFormat('Y-m-d', $request->next_due)->subMonthsWithNoOverflow(1);
                    } else {
                        $reg_date = $request->reg_date;
                    }
                    // PRABAYAR RENEWABLE
                } else if ($request->payment_type === 'Prabayar' && $request->billing_period === 'Renewable') {
                    $next_due = $request->next_due;
                    $next_invoice = $request->next_due;
                    if ($request->reg_date === null) {
                        $reg_date = Carbon::createFromFormat('Y-m-d', $request->next_due)->subMonthsWithNoOverflow(1);
                    } else {
                        $reg_date = $request->reg_date;
                    }
                    // PASCABAYAR FIXED DATE
                } elseif ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Fixed Date') {
                    $next_due = $request->next_due;
                    $next_invoice = $request->next_due;
                    if ($request->reg_date === null) {
                        $reg_date = Carbon::createFromFormat('Y-m-d', $request->next_due)->subMonthsWithNoOverflow(1);
                    } else {
                        $reg_date = $request->reg_date;
                    }
                    // PASCABAYAR BILILNG CYCLE
                } elseif ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Billing Cycle') {
                    $due_bc = BillingSetting::where('shortname', $request->user()->shortname)->select('due_bc')->first();
                    $next_due = Carbon::createFromFormat('Y-m-d', $request->next_due)->setDay($due_bc->due_bc);
                    $next_invoice = Carbon::createFromFormat('Y-m-d', $request->next_due)->startOfMonth();

                    if ($request->reg_date === null) {
                        $reg_date = Carbon::createFromFormat('Y-m-d', $request->next_due)->subMonthsWithNoOverflow(1)->startOfMonth();
                    } else {
                        $reg_date = $request->reg_date;
                    }
                } else {
                    $next_due = null;
                    $next_invoice = null;
                    $reg_date = null;
                }

                $invoice = Invoice::where('id_pelanggan', $user->id)->where('status', 'unpaid')->count();
                if ($invoice === 0) {
                    $user->update([
                        'username' => $request->username,
                        'value' => $request->password,
                        'profile' => $request->profile,
                        'nas' => $request->nas,
                        'lock_mac' => $request->lock_mac,
                        'mac' => $request->mac,
                        'full_name' => $request->full_name,
                        'profile_id' => $request->profile_id,
                        'kode_area' => $request->kode_area,
                        'kode_odp' => $request->kode_odp,
                        'mitra_id' => $mitra_id,
                        'wa' => $request->wa,
                        'npwp' => $request->npwp,
                        'ktp' => $request->ktp,
                        'address' => $request->address,
                        'ppn' => $request->ppn,
                        'discount' => $request->discount,
                        'payment_type' => $request->payment_type,
                        'billing_period' => $request->billing_period,
                        'next_due' => $next_due,
                        'next_invoice' => $next_invoice,
                        'reg_date' => $reg_date,
                        'province_id' => $request->province_id,
                        'regency_id' => $request->regency_id,
                        'district_id' => $request->district_id,
                        'village_id' => $request->village_id,
                        'email' => $request->email,
                        'longitude' => $request->longitude,
                        'latitude' => $request->latitude,
                        'type' => $request->type,
                        'ip_address' => $request->ip_address,
                        'pks' => $request->pks,
                        'sn_modem' => $request->sn_modem,
                    ]);
                } else {
                    $user->update([
                        'username' => $request->username,
                        'value' => $request->password,
                        'profile' => $request->profile,
                        'nas' => $request->nas,
                        'lock_mac' => $request->lock_mac,
                        'mac' => $request->mac,
                        'full_name' => $request->full_name,
                        'profile_id' => $request->profile_id,
                        'kode_area' => $request->kode_area,
                        'kode_odp' => $request->kode_odp,
                        'mitra_id' => $mitra_id,
                        'wa' => $request->wa,
                        'npwp' => $request->npwp,
                        'ktp' => $request->ktp,
                        'address' => $request->address,
                        'province_id' => $request->province_id,
                        'regency_id' => $request->regency_id,
                        'district_id' => $request->district_id,
                        'village_id' => $request->village_id,
                        'longitude' => $request->longitude,
                        'latitude' => $request->latitude,
                        // 'ppn' => $request->ppn,
                        // 'discount' => $request->discount,
                        // 'payment_type' => $request->payment_type,
                        // 'billing_period' => $request->billing_period,
                        // 'next_due' => $next_due,
                        // 'next_invoice' => $next_invoice,
                        // 'reg_date' => $reg_date,
                        'type' => $request->type,
                        'ip_address' => $request->ip_address,
                        'pks' => $request->pks,
                        'sn_modem' => $request->sn_modem,
                    ]);

                    $invoice = Invoice::where('id_pelanggan', $user->id)->where('status', 'unpaid')->first();
                    $mappingadons = MappingAdons::query()->where('id_pelanggan_pppoe', $user->id_pelanggan)->get();
                    foreach ($mappingadons as $ad){
                        $ad->delete();
                    }
                    $desc                   = $request->desc_ad;
                    $ppn_ad                 = $request->ppn_ad;
                    $monthly                = $request->monthly_ad;
                    $qty                    = $request->qty_ad;
                    $price                  = $request->price_ad;
                    $total_price_ad         = 0;
                    $total_price_ad_monthly = 0;
                    if(isset($request->desc_ad)){
                        if(count($desc) > 0){
                            for ($i = 0; $i < count($desc); $i++) {
                                $mappingadons = MappingAdons::create(
                                    [
                                        'id_mapping' => 0,
                                        'description' => $desc[$i],
                                        'ppn' => $ppn_ad[$i],
                                        'monthly' => $monthly[$i],
                                        'qty' => $qty[$i],
                                        'price' => $price[$i],
                                        'no_invoice' => $invoice->no_invoice,
                                        'id_pelanggan_pppoe' => $user->id_pelanggan
                                    ]);
                                $totalPpnAd = 0;
                                if($ppn_ad[$i] > 0){
                                    $totalPpnAd = $ppn_ad[$i] * ($qty[$i] * $price[$i]) / 100;
                                }
                                $total_price_ad = $total_price_ad + (($qty[$i] * $price[$i]) + $totalPpnAd);

                                if($monthly[$i] == "Yes"){
                                    $total_price_ad_monthly = $total_price_ad_monthly + (($qty[$i] * $price[$i]) + $totalPpnAd);
                                }
                            }
                        }
                        $invoice->update([
                            'price_adon_monthly' => $total_price_ad_monthly,
                            'price_adon' => $total_price_ad
                        ]);
                    }
                }
            } else {
                $user->update([
                    'username' => $request->username,
                    'value' => $request->password,
                    'profile' => $request->profile,
                    'nas' => $request->nas,
                    'lock_mac' => $request->lock_mac,
                    'mac' => $request->mac,
                    'full_name' => $request->full_name,
                    'profile_id' => $request->profile_id,
                    'kode_area' => $request->kode_area,
                    'kode_odp' => $request->kode_odp,
                    'mitra_id' => $mitra_id,
                    'wa' => $request->wa,
                    'npwp' => $request->npwp,
                    'ktp' => $request->ktp,
                    'address' => $request->address,
                    'payment_type' => null,
                    'billing_period' => null,
                    'next_due' => null,
                    'next_invoice' => null,
                    'province_id' => $request->province_id,
                    'regency_id' => $request->regency_id,
                    'district_id' => $request->district_id,
                    'village_id' => $request->village_id,
                    'longitude' => $request->longitude,
                    'latitude' => $request->latitude,
                    'type' => $request->type,
                    'ip_address' => $request->ip_address,
                    'pks' => $request->pks,
                    'sn_modem' => $request->sn_modem,
                ]);
            }
        } else {
            $user->update([
                'username' => $request->username,
                'value' => $request->password,
                // 'profile' => $request->profile,
                // 'nas' => $request->nas,
                'lock_mac' => $request->lock_mac,
                'mac' => $request->mac,
                'full_name' => $request->full_name,
                // 'profile_id' => $request->profile_id,
                'kode_area' => $request->kode_area,
                'kode_odp' => $request->kode_odp,
                // 'mitra_id' => $mitra_id,
                'wa' => $request->wa,
                'npwp' => $request->npwp,
                'ktp' => $request->ktp,
                'address' => $request->address,
                'province_id' => $request->province_id,
                'regency_id' => $request->regency_id,
                'district_id' => $request->district_id,
                'village_id' => $request->village_id,
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'type' => $request->type,
                'ip_address' => $request->ip_address,
                'pks' => $request->pks,
                'sn_modem' => $request->sn_modem,
            ]);
        }

        if ($request->user()->role === 'Mitra') {
            $totaluser = PppoeUser::where('shortname', $request->user()->shortname)->where('mitra_id', $request->user()->id)->count();
            $totalactive = PppoeUser::where('shortname', $request->user()->shortname)->where('mitra_id', $request->user()->id)->where('status', 1)->count();
            $totalsuspend = PppoeUser::where('shortname', $request->user()->shortname)->where('mitra_id', $request->user()->id)->where('status', 2)->count();
            $totaldisabled = PppoeUser::where('shortname', $request->user()->shortname)->where('mitra_id', $request->user()->id)->where('status', 0)->count();
        } else {
            $totaluser = PppoeUser::where('shortname', $request->user()->shortname)->count();
            $totalactive = PppoeUser::where('shortname', $request->user()->shortname)->where('status', 1)->count();
            $totalsuspend = PppoeUser::where('shortname', $request->user()->shortname)->where('status', 2)->count();
            $totaldisabled = PppoeUser::where('shortname', $request->user()->shortname)->where('status', 0)->count();
        }

        if ($request->user()->role !== 'Mitra') {
            activity()
                ->tap(function (Activity $activity) {
                    $activity->shortname = $request->user()->shortname;
                })
                ->event('Update')
                ->log('Update User PPPoE: ' . $request->username . '');
        }
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diupdate',
            'totaluser' => $totaluser,
            'totalactive' => $totalactive,
            'totalsuspend' => $totalsuspend,
            'totaldisabled' => $totaldisabled,
        ]);
    }

    public function disable(Request $request)
    {
        // SSH configuration
        $sshUser = env('IP_RADIUS_USERNAME');
        $sshHost = env('IP_RADIUS_SERVER');
        $sshOptions = ['-o', 'BatchMode=yes', '-o', 'StrictHostKeyChecking=no'];
        $sshOptionsString = implode(' ', $sshOptions);

        // Fetch users to disable
        $userList = PppoeUser::whereIn('id', $request->ids)->select('username', 'nas')->get();

        // Preload default NAS entries for users without specific NAS
        $defaultNasList = RadiusNas::where('shortname', $request->user()->shortname)->get(['nasname', 'secret']);

        foreach ($userList as $user) {
            // Determine NAS entries for this user
            $nasItems = $user->nas ? RadiusNas::where('nasname', $user->nas)->get(['nasname', 'secret']) : $defaultNasList;

            foreach ($nasItems as $nas) {
                // Build radclient disconnect command
                $usernameArg = escapeshellarg("User-Name = '{$user->username}'");
                $radclientCmd = "echo {$usernameArg} | radclient -r 1 {$nas->nasname}:3799 disconnect {$nas->secret}";

                // Build SSH command with options
                $sshCommand = "ssh {$sshOptionsString} {$sshUser}@{$sshHost} \"{$radclientCmd}\"";
                $process = Process::run($sshCommand);

                // Log execution details
                // Log::info('SSH disconnect executed', [
                //     'command' => $sshCommand,
                //     'output' => $process->output(),
                //     'success' => $process->successful(),
                // ]);

                if (!$process->successful()) {
                    // Log::error('SSH disconnect failed', [
                    //     'command' => $sshCommand,
                    //     'error' => $process->errorOutput(),
                    // ]);
                }
            }

            // Record activity per user
            activity()
                ->tap(fn(Activity $activity) => ($activity->shortname = $request->user()->shortname))
                ->event('Update')
                ->log("Disable User PPPoE: {$user->username}");
        }

        // Update user statuses in bulk
        PppoeUser::whereIn('id', $request->ids)->update(['status' => 2]);

        // Retrieve updated totals
        $totals = PppoeUser::where('shortname', $request->user()->shortname)->selectRaw('count(*) as totaluser')->selectRaw('sum(status = 1) as totalactive')->selectRaw('sum(status = 2) as totalsuspend')->selectRaw('sum(status = 0) as totaldisabled')->first();

        // Return response
        return response()->json([
            'success' => true,
            'message' => 'User Berhasil Dinonaktifkan',
            'data' => $userList,
            'totaluser' => $totals->totaluser,
            'totalactive' => $totals->totalactive,
            'totalsuspend' => $totals->totalsuspend,
            'totaldisabled' => $totals->totaldisabled,
        ]);
    }

    public function enable(Request $request)
    {
        // SSH configuration
        $sshUser = env('IP_RADIUS_USERNAME');
        $sshHost = env('IP_RADIUS_SERVER');
        $sshOptions = ['-o', 'BatchMode=yes', '-o', 'StrictHostKeyChecking=no'];
        $sshOptionsString = implode(' ', $sshOptions);

        // Fetch users to enable
        $userList = PppoeUser::whereIn('id', $request->ids)->select('username', 'nas')->get();

        // Preload default NAS entries for users without specific NAS
        $defaultNasList = RadiusNas::where('shortname', $request->user()->shortname)->get(['nasname', 'secret']);

        foreach ($userList as $user) {
            // Determine NAS entries for this user
            $nasItems = $user->nas ? RadiusNas::where('nasname', $user->nas)->get(['nasname', 'secret']) : $defaultNasList;

            foreach ($nasItems as $nas) {
                // Build radclient disconnect command (as per original flow)
                $usernameArg = escapeshellarg("User-Name = '{$user->username}'");
                $radclientCmd = "echo {$usernameArg} | radclient -r 1 {$nas->nasname}:3799 disconnect {$nas->secret}";

                // Build SSH command with options
                $sshCommand = "ssh {$sshOptionsString} {$sshUser}@{$sshHost} \"{$radclientCmd}\"";
                $process = Process::run($sshCommand);

                // Log execution details
                // Log::info('SSH reconnect executed', [
                //     'command' => $sshCommand,
                //     'output' => $process->output(),
                //     'success' => $process->successful(),
                // ]);

                if (!$process->successful()) {
                    // Log::error('SSH reconnect failed', [
                    //     'command' => $sshCommand,
                    //     'error' => $process->errorOutput(),
                    // ]);
                }
            }

            // Record activity per user
            activity()
                ->tap(fn(Activity $activity) => ($activity->shortname = $request->user()->shortname))
                ->event('Update')
                ->log("Enable User PPPoE: {$user->username}");
        }

        // Update user statuses in bulk
        PppoeUser::whereIn('id', $request->ids)->update(['status' => 1]);

        // Retrieve updated totals
        $totals = PppoeUser::where('shortname', $request->user()->shortname)->selectRaw('count(*) as totaluser')->selectRaw('sum(status = 1) as totalactive')->selectRaw('sum(status = 2) as totalsuspend')->selectRaw('sum(status = 0) as totaldisabled')->first();

        // Return response
        return response()->json([
            'success' => true,
            'message' => 'User Berhasil Diaktifkan',
            'data' => $userList,
            'totaluser' => $totals->totaluser,
            'totalactive' => $totals->totalactive,
            'totalsuspend' => $totals->totalsuspend,
            'totaldisabled' => $totals->totaldisabled,
        ]);
    }

    public function kick(Request $request)
    {
        $ssh_user = env('IP_RADIUS_USERNAME');
        $ssh_host = env('IP_RADIUS_SERVER');
        $sshOptions = ['-o', 'BatchMode=yes', '-o', 'StrictHostKeyChecking=no'];
        $sshOptionsString = implode(' ', $sshOptions);

        $user = PppoeUser::where('shortname', $request->user()->shortname)->where('username', $request->username)->select('username', 'nas')->first();
        if ($user->nas === null) {
            $nas = RadiusNas::where('shortname', $request->user()->shortname)->select('nasname', 'secret')->get();
            foreach ($nas as $item) {
                $escapedUsername = escapeshellarg("User-Name = '{$user->username}'");
                $command = "echo $escapedUsername | radclient -r 1 {$item['nasname']}:3799 disconnect {$item['secret']}";
                $ssh_command = "ssh {$sshOptionsString} {$ssh_user}@{$ssh_host} \"{$command}\"";
                $process = Process::run($ssh_command);
            }
        } else {
            $secret = RadiusNas::where('nasname', $user->nas)->select('secret')->first();
            $username = escapeshellarg("User-Name = '{$user->username}'");
            $command = "echo $username | radclient -r 1 {$user->nas}:3799 disconnect {$secret->secret}";
            $ssh_command = "ssh {$sshOptionsString} {$ssh_user}@{$ssh_host} \"{$command}\"";
            $process = Process::run($ssh_command);
        }

        //return response
        return response()->json([
            'success' => true,
            'message' => 'User Berhasil Dikick',
        ]);
    }

    public function regist(Request $request)
    {
        $user = PppoeUser::whereIn('id', $request->ids);
        $user->update([
            'status' => 1,
        ]);
        $notif_sm = BillingSetting::where('shortname', $request->user()->shortname)->first()->notif_sm;
        if ($notif_sm === 1) {
            $mpwa = Mpwa::where('shortname', $request->user()->shortname)->first();
            $users_pppoe = PppoeUser::whereIn('id', $request->ids)->with('rprofile')->get();
            foreach ($users_pppoe as $row) {
                $amount_ppn = ($row->rprofile->price * $row->ppn) / 100;
                $amount_discount = ($row->rprofile->price * $row->discount) / 100;
                $total = $row->rprofile->price + $amount_ppn - $amount_discount;
                $shortcode = ['[nama_lengkap]', '[id_pelanggan]', '[username]', '[password]', '[alamat]', '[paket_internet]', '[harga]', '[ppn]', '[discount]', '[total]', '[tipe_pembayaran]', '[siklus_tagihan]', '[tgl_aktif]', '[jth_tempo]'];
                $source = [$row->full_name, $row->id_pelanggan, $row->username, $row->password, $row->address, $row->profile, number_format($row->rprofile->price, 0, '.', '.'), $row->ppn, $row->discount, number_format($total, 0, '.', '.'), $row->payment_type, $row->billing_period, date('d/m/Y', strtotime($row->reg_date)), date('d/m/Y', strtotime($row->next_due))];
                $template = Watemplate::where('shortname', $row->shortname)->first()->account_active;
                $message = str_replace($shortcode, $source, $template);
                $message_format = str_replace('<br>', "\n", $message);

                if($mpwa->mpwa_server_server == "mpwa"){
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
                        // Http::post($apiUrl, $params);
                        $response = Http::post($apiUrl, $params);
                        if($response->successful()){
                            $json = $response->json();
                            $status = $json->status;
                            $receiver = $nomorhp;
                            $shortname = $user_wa->shortname;
                            save_wa_log($shortname,$receiver,$message_format,$status);
                        }

                    } catch (\Exception $e) {
                    }
                }
                $smtp = SmtpSetting::firstWhere('shortname', $mpwa->shortname);
                if($smtp){
                    try{
                        $data = [
                            'messages' => $message,
                            'user_name' => $row->username,
                            'notification' => 'Activation Notification'
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

        $totaluser = PppoeUser::where('shortname', $request->user()->shortname)->count();
        $totalactive = PppoeUser::where('shortname', $request->user()->shortname)->where('status', 1)->count();
        $totalsuspend = PppoeUser::where('shortname', $request->user()->shortname)->where('status', 2)->count();
        $totaldisabled = PppoeUser::where('shortname', $request->user()->shortname)->where('status', 0)->count();

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Progres Registrasi Berhasil',
            'data' => $user,
            'totaluser' => $totaluser,
            'totalactive' => $totalactive,
            'totalsuspend' => $totalsuspend,
            'totaldisabled' => $totaldisabled,
        ]);
    }

    public function delete(Request $request)
    {
        // SSH configuration
        // $sshUser = env('IP_RADIUS_USERNAME');
        // $sshHost = env('IP_RADIUS_SERVER');
        // $sshOptions = ['-o', 'BatchMode=yes', '-o', 'StrictHostKeyChecking=no'];
        // $sshOptionsString = implode(' ', $sshOptions);

        // // Fetch users to delete
        // $userList = PppoeUser::whereIn('id', $request->ids)->select('username', 'nas')->get();

        // // Preload default NAS entries for users without specific NAS
        // $defaultNasList = RadiusNas::where('shortname', $request->user()->shortname)->get(['nasname', 'secret']);

        // foreach ($userList as $user) {
        //     $nasItems = $user->nas ? RadiusNas::where('nasname', $user->nas)->get(['nasname', 'secret']) : $defaultNasList;

        //     foreach ($nasItems as $nas) {
        //         $usernameArg = escapeshellarg("User-Name = '{$user->username}'");
        //         $radclientCmd = "echo {$usernameArg} | radclient -r 1 {$nas->nasname}:3799 disconnect {$nas->secret}";
        //         $sshCommand = "ssh {$sshOptionsString} {$sshUser}@{$sshHost} \"{$radclientCmd}\"";
        //         $process = Process::run($sshCommand);

        //         // Log::info('SSH delete disconnect executed', [
        //         //     'command' => $sshCommand,
        //         //     'output' => $process->output(),
        //         //     'success' => $process->successful(),
        //         // ]);

        //         if (!$process->successful()) {
        //             // Log::error('SSH delete disconnect failed', [
        //             //     'command' => $sshCommand,
        //             //     'error' => $process->errorOutput(),
        //             // ]);
        //         }
        //     }

        //     activity()
        //         ->tap(fn(Activity $activity) => ($activity->shortname = $request->user()->shortname))
        //         ->event('Delete')
        //         ->log("Delete User PPPoE: {$user->username}");
        // }

        // Delete user records
        PppoeUser::whereIn('id', $request->ids)->delete();
        Invoice::whereIn('id_pelanggan', $request->ids)->delete();
        $totals = PppoeUser::where('shortname', $request->user()->shortname)->selectRaw('count(*) as totaluser')->selectRaw('sum(status = 1) as totalactive')->selectRaw('sum(status = 2) as totalsuspend')->selectRaw('sum(status = 0) as totaldisabled')->first();

        return response()->json([
            'success' => true,
            'message' => 'User Berhasil Dihapus',
            'totaluser' => $totals->totaluser,
            'totalactive' => $totals->totalactive,
            'totalsuspend' => $totals->totalsuspend,
            'totaldisabled' => $totals->totaldisabled,
        ]);
    }

    public function online()
    {
        if (request()->ajax()) {
            $sub = RadiusSession::selectRaw('MAX(start) as latest_start, username')->where('shortname', $request->user()->shortname)->groupBy('username');
            $online = RadiusSession::select(['user_session.session_id', 'user_session.username', 'user_session.ip', 'user_session.mac', 'user_session.input', 'user_session.output', 'user_session.uptime', 'user_session.start', 'user_session.stop', 'user_session.nas_address'])
                ->joinSub($sub, 'latest', function ($join) {
                    $join->on('user_session.username', '=', 'latest.username')->on('user_session.start', '=', 'latest.latest_start');
                })
                ->where([
                    ['user_session.shortname', '=', $request->user()->shortname],
                    ['user_session.status', '=', 1],
                    ['user_session.type', '=', 2],
                    ['user_session.stop', '=', null], // hanya yang belum stop
                ])
                ->with('mnas', 'ppp:username,full_name,kode_area,kode_odp')
                ->get();

            return DataTables::of($online)->addIndexColumn()->toJson();
        }
        return view('backend.pppoe.online.index_new');
    }

    public function offline()
    {
        if (request()->ajax()) {
            // Ambil session terakhir per user
            $sub = RadiusSession::selectRaw('MAX(start) as latest_start, username')->where('shortname', $request->user()->shortname)->groupBy('username');

            // Join dengan session asli untuk ambil seluruh datanya
            $offline = RadiusSession::select(['user_session.session_id', 'user_session.username', 'user_session.ip', 'user_session.mac', 'user_session.input', 'user_session.output', 'user_session.nas_address', 'user_session.uptime', 'user_session.start', 'user_session.stop', 'user_session.status'])
                ->joinSub($sub, 'latest', function ($join) {
                    $join->on('user_session.username', '=', 'latest.username')->on('user_session.start', '=', 'latest.latest_start');
                })
                ->where('user_session.shortname', $request->user()->shortname)
                ->where('user_session.type', 2)
                ->where('user_session.status', 2) // offline only
                ->with('mnas', 'ppp:username,full_name,kode_area,kode_odp')
                ->get();

            return DataTables::of($offline)->addIndexColumn()->toJson();
        }
        return view('backend.pppoe.offline.index_new');
    }
    public function sync()
    {
        $ssh_user = env('IP_RADIUS_USERNAME');
        $ssh_host = env('IP_RADIUS_SERVER');
        $sshOptions = ['-o', 'BatchMode=yes', '-o', 'StrictHostKeyChecking=no'];
        $sshOptionsString = implode(' ', $sshOptions);

        $user_nas = PppoeUser::where('shortname', $request->user()->shortname)->select('username', 'nas')->get();
        foreach ($user_nas as $row) {
            if ($row->nas === null) {
                $nas = RadiusNas::where('shortname', $request->user()->shortname)->select('nasname', 'secret')->get();
                foreach ($nas as $item) {
                    $username = escapeshellarg("User-Name = '{$row->username}'");
                    $command = "echo $username | radclient -r 1 {$item['nasname']}:3799 disconnect {$item['secret']}";
                    $ssh_command = "ssh {$sshOptionsString} {$ssh_user}@{$ssh_host} \"{$command}\"";
                    $process = Process::run($ssh_command);
                }
            } else {
                $secret = RadiusNas::where('nasname', $row->nas)->select('secret')->first();
                $username = escapeshellarg("User-Name = '{$row->username}'");
                $command = "echo $username | radclient -r 1 {$row->nas}:3799 disconnect {$secret->secret}";
                $ssh_command = "ssh {$sshOptionsString} {$ssh_user}@{$ssh_host} \"{$command}\"";
                $process = Process::run($ssh_command);
            }
            RadiusSession::where('shortname', $request->user()->shortname)->where('username', $row->username)->delete();
        }
        activity()
            ->tap(function (Activity $activity) {
                $activity->shortname = $request->user()->shortname;
            })
            ->event('Update')
            ->log('Resync All User PPPoE');

        return response()->json([
            'success' => true,
            'message' => 'User Berhasil Disinkronkan',
        ]);
    }

    public function syncOffline(Request $request)
    {
        $offline = RadiusSession::where('shortname', $request->user()->shortname)->where('username', $request->username)->where('type', 2)->where('status', 2)->delete();
        activity()
            ->tap(function (Activity $activity) {
                $activity->shortname = $request->user()->shortname;
            })
            ->event('Update')
            ->log('Delete Session Offline');

        return response()->json([
            'success' => true,
            'message' => 'Sesi Berhasil Dihapus',
            'data' => $offline,
        ]);
    }

    public function clearSession(Request $request)
    {
        $session = RadiusSession::where('shortname', $request->user()->shortname)->where('username', $request->username)->delete();
        return response()->json([
            'success' => true,
            'message' => 'Sesi Berhasil Dihapus',
            'data' => $session,
        ]);
    }

    public function export()
    {
        return Excel::download(new PppoeUserExport(), 'pppoe_users.xlsx');
    }

    public function getSession(Request $request)
    {
        $sessions = RadiusSession::where('shortname', $request->user()->shortname)->where('username', $request->username)->orderBy('id', 'desc')->get();
        return response()->json($sessions);
    }

    public function import(Request $request)
    {
        // Validasi file import (sesuaikan ekstensi file yang diperbolehkan)
        $request->validate([
            'select_file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new PppoeUserImport(), $request->file('select_file'));
            return redirect()->back()->with('success', 'Import user berhasil.');
        } catch (\Exception $e) {
            // Untuk debugging, Anda bisa menyimpan pesan error ke session dan menampilkannya di view
            return redirect()
                ->back()
                ->with('error', 'Import user gagal: ' . $e->getMessage());
        }
    }

    public function generateMemberId(): string
    {
        // Get the latest member ID or start from 0
        $lastMember = CountNumbering::where('tipe', 'pppoe_user')->first();
        if($lastMember == null){
            $lastMember = CountNumbering::create([
                'tipe' => 'pppoe_user',
                'count' => 0
            ]);
        }
        $lastNumber = $lastMember ? (int)$lastMember->count : 0;
        $nextNumber = $lastNumber + 1;

        // Format to 8 digits with leading zeros

        $lastMember->count = $nextNumber;
        $lastMember->save();
        $pid = $request->user()->id;//str_pad($request->user()->id, 6, '0', STR_PAD_LEFT);
        $userid = \Illuminate\Support\Carbon::now()->format('Ym').str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        return $pid.$userid;
    }
}
