<?php

namespace App\Http\Controllers\Dinetkan;

use App\Http\Requests\Dinetkan\UserDinetkanRequest;
use App\Mail\EmailInvoiceNotif;
use App\Mail\TestEmail;
use App\Models\AdminDinetkanInvoice;
use App\Models\CategoryLicenseDinetkan;
use App\Models\Company;
use App\Models\Districts;
use App\Models\DocType;
use App\Models\LicenseDinetkan;
use App\Models\MappingAdons;
use App\Models\MappingUserLicense;
use App\Models\MasterMetro;
use App\Models\MasterMikrotik;
use App\Models\MasterPop;
use App\Models\Member;
use App\Models\Partnership\Mitra;
use App\Models\Province;
use App\Models\Regencies;
use App\Models\ServiceDetail;
use App\Models\ServiceLibre;
use App\Models\Setting\Mduitku;
use App\Models\User;
use App\Models\UserDinetkan;
use App\Models\UserDinetkanGraph;
use App\Models\UserDoc;
use App\Models\UserWhatsappGroup;
use App\Models\Villages;
use App\Models\Wablas;
use App\Models\Invoice;
use App\Models\PppoeUser;
use App\Models\RadiusNas;
use App\Models\Transaksi;
use App\Models\PppoeMember;
use App\Settings\SiteDinetkanSettings;
use function Carbon\toString;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\PppoeProfile;
use Illuminate\Http\Request;
use App\Models\WablasMessage;
use App\Models\BillingSetting;
use App\Models\WablasTemplate;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Enums\TransactionTypeEnum;
use App\Http\Controllers\Controller;
use App\Enums\TransactionCategoryEnum;
use Illuminate\Support\Facades\Process;
use function Livewire\Features\SupportTesting\commit;
use Modules\Payments\Repositories\Contracts\AdminDinetkanInvoiceRepositoryInterface;
use Modules\Payments\Repositories\Contracts\LicenseDinetkanRepositoryInterface;
use Modules\Payments\Services\AdminDinetkanPaymentService;
use Modules\Payments\Services\DuitkuService;
use mysql_xdevapi\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Web\Service;
use Spatie\Activitylog\Models\Activity;
use function Symfony\Component\Mailer\getMessage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Enums\DinetkanInvoiceStatusEnum;
use App\Enums\ServiceStatusEnum;
use Modules\Payments\ValueObjects\DuitkuConfig;

class InvoiceDinetkanController extends Controller
{
    public function __construct(
        private DuitkuService $duitkuService,
        private SiteDinetkanSettings $settings,

        private LicenseDinetkanRepositoryInterface $licenseDinetkanRepo,
        private AdminDinetkanInvoiceRepositoryInterface $adminDinetkanInvoiceRepo,
        private AdminDinetkanPaymentService $adminDinetkanPaymentService,
        private AdminDinetkanPaymentService $adminPaymentService,
    ) {
        $this->duitkuService->setConfig(new DuitkuConfig(
            idMerchant: $this->settings->duitku_merchant_code,
            apiKey: $this->settings->duitku_api_key,
            isProduction: !$this->settings->duitku_sandbox,
        ));
        $this->adminDinetkanPaymentService->setDuitkuService($this->duitkuService);
    }

    public function index(Request $request)
    {
        $licenses = LicenseDinetkan::all();
        $categories = CategoryLicenseDinetkan::get();
        $resellers = UserDinetkan::where('is_dinetkan',1)
            ->get();
        return view('backend.dinetkan.invoice_dinetkan.index',
            compact(
                'licenses',
                'categories','resellers'
            ));
    }

    public function order(Request $request)
    {
        $licenses = LicenseDinetkan::all();
        $categories = CategoryLicenseDinetkan::get();
        $resellers = UserDinetkan::where('is_dinetkan',1)->with('company')->get();
        $statuses = ServiceStatusEnum::getStatuses();
        $progress = ServiceStatusEnum::PROGRESS->value;
        return view('backend.dinetkan.invoice_dinetkan.order',
            compact(
                'licenses',
                'categories','resellers',
                'statuses','progress'
            ));
    }

    public function create_order(Request $request, SiteDinetkanSettings $settings)
    {
        $licenses = LicenseDinetkan::all();
        $categories = CategoryLicenseDinetkan::get();
        $resellers = UserDinetkan::where('is_dinetkan',1)->with('company')->get();
        $statuses = ServiceStatusEnum::getStatuses();
        $progress = ServiceStatusEnum::PROGRESS->value;
        $mitras = Mitra::where('shortname', multi_auth()->shortname)->where('status', 1)->select('id', 'name', 'id_mitra')->get();

            return view('backend.dinetkan.invoice_dinetkan.create_order',
                compact(
                    'licenses',
                    'categories','resellers',
                    'statuses','progress', 'settings',
                    'mitras'
                ));
    }

    public function create_new(Request $request){
        DB::beginTransaction();
        try{
//            echo ($request->id_mitra);exit;
            $id_mapping = $request->id_mapping;
            $license = LicenseDinetkan::where('id', $request->license_dinetkan_id)->first();
            $userdinetkan = UserDinetkan::where('dinetkan_user_id', $request->dinetkan_user_id)->first();
            $periodStart = "";
            $periodEnd = "";
            $subscribe = "";
            $type = "prabayar";
            if($userdinetkan->id_mitra_sales == null || $userdinetkan->id_mitra_sales == ''){
                $userdinetkan->update([
                    'id_mitra_sales' => $request->id_mitra
                ]);
            }
//            if($userdinetkan->id_mitra_sales != null && $userdinetkan->id_mitra_sales != ''){
//                $mitra = Mitra::where('id_mitra', $request->id_mitra)->select('id', 'name', 'id_mitra')->first();
//                return response()->json([$userdinetkan->username." tidak bisa di sign dengan sales ".$mitra->name], 500);
//            }
            if($request->statuses == ServiceStatusEnum::ACTIVE->value){

                if($request->payment_method == 'prabayar'){
                    $type = "prabayar";
                    $periodStart = Carbon::parse($request->active_date);
                    $periodEnd = Carbon::parse($request->payment_date)->format('Y-m-d');
                    $subscribe = Carbon::parse($periodStart)->format('d/m/Y') . ' s/d ' . Carbon::parse($periodEnd)->format('d/m/Y');
                }
                if($request->payment_method == 'pascabayar'){
                    $type = "pascabayar";
                    $periodStart = Carbon::parse($request->active_date)->format('Y-m-d');
                    $periodEnd = Carbon::parse($request->payment_date)->format('Y-m-d');
                    $subscribe = Carbon::parse($periodStart)->format('d/m/Y') . ' s/d ' . Carbon::parse($periodEnd)->format('d/m/Y');
                }
                if($request->prorata == "yes"){}
                if($request->prorata == "no"){}
                // create mapping service
                $cekmapping = MappingUserLicense::where('id', $id_mapping)->first();
                $service_id = $this->generateUniqueServiceId();
                if(!$cekmapping){
                    $order = MappingUserLicense::create(
                        [
                            'dinetkan_user_id' => $userdinetkan->dinetkan_user_id,
                            'license_id' => $license->id,
                            'status' => $request->statuses,
                            'no_invoice' => "",
                            'due_date' => $request->payment_date,
                            'category_id' => $license->category_id,
                            'active_date' => $request->active_date,
                            'remainder_day' => $request->remainder_day,
                            'payment_date' => $request->payment_date,
                            'payment_siklus' => $request->payment_siklus,
                            'payment_method' => $request->payment_method,
                            'prorata' => $request->prorata,
                            'id_mitra' => $request->id_mitra,
                            'service_id' => $service_id,
                            'notes'=> $request->notes,
                        ]);
                }

                if($cekmapping){
                    $order = $cekmapping;
                    $cekmapping->update(
                        [
                            'dinetkan_user_id' => $userdinetkan->dinetkan_user_id,
                            'license_id' => $license->id,
                            'status' => $request->statuses,
                            'no_invoice' => "",
                            'due_date' => $request->payment_date,
                            'category_id' => $license->category_id,
                            'active_date' => $request->active_date,
                            'remainder_day' => $request->remainder_day,
                            'payment_date' => $request->payment_date,
                            'payment_siklus' => $request->payment_siklus,
                            'payment_method' => $request->payment_method,
                            'prorata' => $request->prorata,
                            'id_mitra' => $request->id_mitra,
//                            'service_id' => $service_id,
                            'notes'=> $request->notes,
                        ]);
                }
                $cekadons = MappingAdons::query()->where('id_mapping',$order->id)->get();
                if($cekadons){
                    foreach ($cekadons as $ckm){
                        $ckm->delete();
                    }
                }
                    $desc                   = $request->desc;
                    $ppn                    = $request->ppn;
                    $monthly                = $request->monthly;
                    $qty                    = $request->qty;
                    $price                  = $request->price;
                    $total_price_ad         = 0;
                    $total_price_ad_monthly = 0;
                    $data = [];
                    if(isset($request->desc)){
                        if(count($desc) > 0){
                            for ($i = 0; $i < count($desc); $i++) {
                                $orderadons = MappingAdons::create(
                                    [
                                        'id_mapping' => $order->id,
                                        'description' => $desc[$i],
                                        'ppn' => $ppn[$i],
                                        'monthly' => $monthly[$i],
                                        'qty' => $qty[$i],
                                        'price' => $price[$i]
                                    ]);
                                $totalPpnAd = 0;
                                if($ppn[$i] > 0){
                                    $totalPpnAd = $ppn[$i] * ($qty[$i] * $price[$i]) / 100;
                                }
                                $total_price_ad = $total_price_ad + (($qty[$i] * $price[$i]) + $totalPpnAd);

                                if($monthly[$i] == "Yes"){
                                    $total_price_ad_monthly = $total_price_ad_monthly + (($qty[$i] * $price[$i]) + $totalPpnAd);
                                }
                            }
                        }
                    }
                $invoice = null;
                $license = $this->licenseDinetkanRepo->findById($license->id);
                if($license){
                    $prorate = hitungProrate($request->payment_method, $request->active_date, $request->payment_date, $license->id, $request->prorata);
                    $price_license = $prorate['harga_prorate']; // $license->price;
                    $ppn_license = $prorate['ppn']; // $license->ppn;
                    $total_ppn_license = 0;
                    if($license->ppn > 0){
                        $total_ppn_license = $ppn_license * $price_license / 100;
                    }
                    $existingInvoice = AdminDinetkanInvoice::where('group_id', $userdinetkan->dinetkan_user_id)
                        ->where('status', DinetkanInvoiceStatusEnum::UNPAID)
                        ->where('itemable_id', $license->id)
                        ->where('itemable_type', LicenseDinetkan::class)
                        ->where('id_mapping', $order->id)
                        ->first();
                    $existingInvoiceEX = AdminDinetkanInvoice::where('status', DinetkanInvoiceStatusEnum::EXPIRED)
                        ->where('itemable_id', $license->id)
                        ->where('itemable_type', LicenseDinetkan::class)
                        ->where('id_mapping', $order->id)
                        ->first();
                    if($existingInvoiceEX){
                        $existingInvoiceEX->update([
                            'status' => DinetkanInvoiceStatusEnum::UNPAID
                        ]);
                    }

                    if ($existingInvoice) {
                        $invoice = $existingInvoice;
                    } else{
                        $isRenewal = ($userdinetkan->license_id === $license->id);
                        $noInvoice = date('m') . rand(0000000, 9999999);

                        $invoice = new AdminDinetkanInvoice([
                            'group_id'              => $userdinetkan->id,
                            'itemable_id'           => $license->id,
                            'itemable_type'         => LicenseDinetkan::class,
                            'no_invoice'            => $noInvoice,
                            'item'                  => "Service : ". $license->name, //($isRenewal ? 'License Renewal: ' : '') . $license->name,
                            'price'                 => $price_license,
                            'price_adon'            => $total_price_ad,
                            'price_adon_monthly'    => $total_price_ad_monthly,
                            'ppn'                   => $ppn_license,
                            'total_ppn'             => $total_ppn_license,
                            'fee'                   => 0,
                            'discount'              => 0,
                            'discount_coupon'       => 0,//$priceData->discountCoupon,
                            'invoice_date'          => Carbon::now(),
                            'due_date'              => $periodEnd, //$dueDate->format('Y-m-d'),
                            'period'                => $periodEnd,
                            'subscribe'             => $subscribe,
                            'payment_type'          => $type,
                            'billing_period'        => $request->payment_siklus,
                            'payment_url'           => route('admin.invoice_dinetkan', $noInvoice),
                            'status'                => $request->invoice_status, // DinetkanInvoiceStatusEnum::PAID,
                            'dinetkan_user_id'      => $userdinetkan->dinetkan_user_id,
                            'id_mapping'            => $order->id
                        ]);
                        $this->adminDinetkanInvoiceRepo->save($invoice);
                    }
                }

                if($invoice){
                    if($invoice->status != DinetkanInvoiceStatusEnum::PAID->value){
                        $order = MappingUserLicense::where('id', $order->id)->first();
//                        $order->update([
//                            'no_invoice' => $invoice->no_invoice,
//                        ]);
                        $userdinetkan = UserDinetkan::where('dinetkan_user_id', $order->dinetkan_user_id)->first();
                        $userupdate = [
                            'dinetkan_next_due' => $periodEnd,
                            'active_date' => $request->active_date,
                            'remainder_day' => $request->remainder_day,
                            'payment_date' => $request->payment_date,
                            'payment_siklus' => $request->payment_siklus,
                            'payment_method' => $request->payment_method,
                            'prorata' => $request->prorata
                            ];
                        $userdinetkan->update($userupdate);
                        $dataupdate = [
                            'invoice_date'  => Carbon::now(),
                            'due_date'      => $periodEnd, // Carbon::parse($request->payment_date)->format('Y-m-d'), //Carbon::now()->addDays(5), //$periodEnd,
                            'period'        => $periodEnd,
                            'subscribe'     => $subscribe,
                            'payment_url'   => route('admin.invoice_dinetkan', $invoice->no_invoice),
                            'status'        => $request->invoice_status, // DinetkanInvoiceStatusEnum::PAID,
                        ];
                        $invoice->update($dataupdate);

                        DB::commit();

                        // send email from no_invoice
                        send_faktur_inv($invoice->no_invoice,$this->settings,'terbit');
                        return response()->json(['message' => 'Order Was Update and send Invoice To User'], 201);
                    }
                }
            }else{
                // cek dlu kalau sudah ada update,
                $cek_mapp = MappingUserLicense::query()->where('id', $request->id_mapping)->first();
                if($cek_mapp){
                    $userdinetkan = UserDinetkan::where('dinetkan_user_id', $request->dinetkan_user_id)->first();
                    // create mapping service
                    $cek_mapp->update(
                        [
                            'dinetkan_user_id' => $userdinetkan->dinetkan_user_id,
                            'license_id' => $license->id,
                            'status' => $request->statuses,
                            'no_invoice' => "", // $invoice->no_invoice,
                            'due_date' => null, // $dueDate->format('Y-m-d'),
                            'category_id' => $license->category_id,
                            'id_mitra' => $request->id_mitra,
                            'notes'=> $request->notes,
                        ]
                    );
                }else{

                    // kalau belum ada create
                    $userdinetkan = UserDinetkan::where('dinetkan_user_id', $request->dinetkan_user_id)->first();
                    // create mapping service

                    $service_id = $this->generateUniqueServiceId();
                    MappingUserLicense::create(
                        [
                            'dinetkan_user_id' => $userdinetkan->dinetkan_user_id,
                            'license_id' => $license->id,
                            'status' => $request->statuses,
                            'no_invoice' => "", // $invoice->no_invoice,
                            'due_date' => null, // $dueDate->format('Y-m-d'),
                            'category_id' => $license->category_id,
                            'service_id' => $service_id,
                            'id_mitra' => $request->id_mitra,
                            'notes'=> $request->notes,
                        ]
                    );
                }

                DB::commit();
                return response()->json(['message' => 'Success created successfully'], 201);
            }

            return response()->json(['message' => 'Failed created successfully'], 500);
        }catch (\Exception $e){
            DB::rollBack();
            return response()->json(['message' =>  $e->getMessage()], 500);
        }
    }

    public function active(Request $request)
    {
        if (! $request->ajax()) {
            return abort(404);
        }

        $mapping =  MappingUserLicense::where('status', ServiceStatusEnum::ACTIVE)->with('user')->with('service')->get();
        return DataTables::of($mapping)
            ->addIndexColumn()

            ->editColumn('first_name', function ($row) {
                $admin = $row->user;
                if (!$admin) {
                    return '';
                }
                return $admin->first_name ? $admin->first_name : $admin->shortname;
            })
            ->editColumn('last_name', function ($row) {
                $admin = $row->user;
                if (!$admin) {
                    return '';
                }
                return $admin->last_name;
            })
            ->editColumn('service', function ($row) {
                $admin = $row->service;
                if (!$admin) {
                    return '';
                }
                return $admin->name;
            })
            ->editColumn('due_date', function ($row) {
                return $row->due_date;
            })
            ->editColumn('service_id', function ($row) {
                return '<a href="' . route('dinetkan.invoice_dinetkan.order.service_detail', ($row->service_id ? $row->service_id : 0) ) . '" type="button" class="btn btn-light-success" >'.($row->service_id ? $row->service_id : 0).'</a>';
            })
            ->editColumn('status', function ($row) {
                return match ($row->status) {
                                ServiceStatusEnum::NEW => '<span class="badge bg-warning">NEW</span>',
                                ServiceStatusEnum::INACTIVE => '<span class="badge bg-warning">INACTIVE</span>',
                                ServiceStatusEnum::ACTIVE => '<span class="badge bg-success">ACTIVE</span>',
                                default => '<span class="badge bg-danger">Canceled</span>',
                    };
                })
            ->addColumn('action', function ($row) {
                return
                    '<a href="' . route('dinetkan.invoice_dinetkan.order.upgrade', ($row->service_id ? $row->service_id: 0) ) . '" type="button" class="btn btn-light-success" >Upgrade</a>'.
                    '<a type="button" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;" class="edit-icon cancel btn btn-danger btn-sm text-white" data-id="' . $row->id . '">Cancel</a>';
            })
            ->addColumn('mitra', function ($row) {
                $mitra = "";
                if(isset($row->user->id_mitra)){
                    $cekmitra = Mitra::where('id_mitra', $row->id_mitra)->first();
                    if(isset($cekmitra->name)){
                        $mitra = $cekmitra->name ." (".$cekmitra->id_mitra.")";
                    }
                }
                return $mitra;
            })
            ->rawColumns(['action', 'status', 'service_id'])
            ->addColumn('company', function ($row){
                $cname = '';
                $company = Company::query()->where('group_id', $row->user->id)->first();
                if($company){
                    $cname = $company->name;
                }
                return $cname;
            })
            ->toJson();
    }

    public function new(Request $request)
    {
        if (! $request->ajax()) {
            return abort(404);
        }

        $mapping =  UserDinetkan::where('status', 4)->where('is_dinetkan', 1)->get();
        foreach ($mapping as $mapp){
            $cek = MappingUserLicense::where('dinetkan_user_id', $mapp->dinetkan_user_id)->first();
            if($cek != null){
                $mapp->update([
                    'status' => 4,
                ]);
            }
        }

        return DataTables::of($mapping)
            ->addIndexColumn()

            ->editColumn('first_name', function ($row) {
                return isset($row->first_name) ? $row->first_name : $row->shortname;
            })
            ->editColumn('last_name', function ($row) {
                return $row->last_name;
            })
            ->editColumn('service', function ($row) {
                return '';
            })
            ->editColumn('due_date', function ($row) {
                return '';
            })
            ->editColumn('service_id', function ($row) {
                return 0;
            })
            ->editColumn('status', function ($row) {
                return '<span class="badge bg-warning">New</span>';
            })
            ->addColumn('action', function ($row) {
//                return '<a type="button" class="edit-icon edit btn btn-light-warning" data-id="' . $row->id . '">Edit</a>';
//
                return '<a href="' . route('dinetkan.users_dinetkan.detail2', $row->dinetkan_user_id) . '" type="button" class="btn btn-light-primary" >Detail</a>';;
//                return '';
            })
            ->addColumn('mitra', function ($row) {
                $mitra = "";
                if(isset($row->user->id_mitra)){
                    $cekmitra = Mitra::where('id_mitra', $row->id_mitra)->first();
                    if(isset($cekmitra->name)){
                        $mitra = $cekmitra->name ." (".$cekmitra->id_mitra.")";
                    }
                }
                return $mitra;
            })
            ->rawColumns(['action', 'status'])
            ->toJson();
    }

    public function progress(Request $request)
    {
        if (! $request->ajax()) {
            return abort(404);
        }

        $mapping =  MappingUserLicense::where('status', ServiceStatusEnum::PROGRESS)->with('user')->with('service')->get();

        return DataTables::of($mapping)
            ->addIndexColumn()

            ->editColumn('first_name', function ($row) {
                $admin = $row->user;
                if (!$admin) {
                    return '';
                }
                return $admin->first_name ? $admin->first_name : $admin->shortname;
            })
            ->editColumn('last_name', function ($row) {
                $admin = $row->user;
                if (!$admin) {
                    return '';
                }
                return $admin->last_name;
            })
            ->editColumn('service', function ($row) {
                $admin = $row->service;
                if (!$admin) {
                    return '';
                }
                return $admin->name;
            })
            ->editColumn('due_date', function ($row) {
                return $row->due_date;
            })
            ->editColumn('service_id', function ($row) {
//                return $row->service_id;
                return '<a href="' . route('dinetkan.invoice_dinetkan.order.service_detail', ($row->service_id ? $row->service_id : 0) ) . '" type="button" class="btn btn-light-success" >'.($row->service_id ? $row->service_id : 0).'</a>';
            })
            ->editColumn('status', function ($row) {
                return match ($row->status) {
                                    ServiceStatusEnum::PROGRESS => '<span class="badge bg-warning">Progress</span>',
                                    ServiceStatusEnum::INACTIVE => '<span class="badge bg-warning">INACTIVE</span>',
                                    ServiceStatusEnum::ACTIVE => '<span class="badge bg-success">ACTIVE</span>',
                                    default => '<span class="badge bg-danger">Canceled</span>',
                        };
                    })
            ->addColumn('action', function ($row) {
//                    return '<a type="button" class="edit-icon edit btn btn-light-warning" data-id="' . $row->id . '">Edit</a>';
                return '<a href="' . route('dinetkan.invoice_dinetkan.order.edit', $row->id ) . '" type="button" class="btn btn-light-warning" >Edit</a>';
            })
            ->addColumn('mitra', function ($row) {
                $mitra = "";
                if(isset($row->user->id_mitra)){
                    $cekmitra = Mitra::where('id_mitra', $row->id_mitra)->first();
                    if(isset($cekmitra->name)){
                        $mitra = $cekmitra->name ." (".$cekmitra->id_mitra.")";
                    }

                }
                return $mitra;
            })
            ->rawColumns(['action', 'status', 'service_id'])
            ->addColumn('company', function ($row){
                $cname = '';
                $company = Company::query()->where('group_id', $row->user->id)->first();
                if($company){
                    $cname = $company->name;
                }
                return $cname;
            })
            ->toJson();
    }


    public function inactive(Request $request)
    {
        if (! $request->ajax()) {
            return abort(404);
        }

        $mapping =  MappingUserLicense::where('status', ServiceStatusEnum::INACTIVE)->with('user')->with('service')->get();

        return DataTables::of($mapping)
            ->addIndexColumn()

            ->editColumn('first_name', function ($row) {
                $admin = $row->user;
                if (!$admin) {
                    return '';
                }
                return $admin->first_name ? $admin->first_name : $admin->shortname;
            })
            ->editColumn('last_name', function ($row) {
                $admin = $row->user;
                if (!$admin) {
                    return '';
                }
                return $admin->last_name;
            })
            ->editColumn('service', function ($row) {
                $admin = $row->service;
                if (!$admin) {
                    return '';
                }
                return $admin->name;
            })
            ->editColumn('service_id', function ($row) {
//                return $row->service_id;
                return '<a href="' . route('dinetkan.invoice_dinetkan.order.service_detail', ($row->service_id ? $row->service_id : 0) ) . '" type="button" class="btn btn-light-success" >'.($row->service_id ? $row->service_id : 0).'</a>';
            })
            ->editColumn('status', function ($row) {
                return match ($row->status) {
                ServiceStatusEnum::NEW => '<span class="badge bg-warning">NEW</span>',
                                    ServiceStatusEnum::INACTIVE => '<span class="badge bg-warning">INACTIVE</span>',
                                    ServiceStatusEnum::ACTIVE => '<span class="badge bg-success">ACTIVE</span>',
                                    default => '<span class="badge bg-danger">Canceled</span>',
                        };
                    })
            ->addColumn('action', function ($row) {
                return '<a href="" class="btn btn-xs btn-danger" title="Cancel">Cancel</a>';
            })
            ->addColumn('mitra', function ($row) {
                $mitra = "";
                if(isset($row->user->id_mitra)){
                    $cekmitra = Mitra::where('id_mitra', $row->id_mitra)->first();
                    if(isset($cekmitra->name)){
                        $mitra = $cekmitra->name ." (".$cekmitra->id_mitra.")";
                    }
                }
                return $mitra;
            })
            ->rawColumns(['action', 'status', 'service_id'])
            ->addColumn('company', function ($row){
                $cname = '';
                $company = Company::query()->where('group_id', $row->user->id)->first();
                if($company){
                    $cname = $company->name;
                }
                return $cname;
            })
            ->toJson();
    }


    public function suspend(Request $request)
    {
        if (! $request->ajax()) {
            return abort(404);
        }

        $mapping =  MappingUserLicense::where('status', ServiceStatusEnum::SUSPEND)->with('user')->with('service')->get();
        return DataTables::of($mapping)
            ->addIndexColumn()

            ->editColumn('first_name', function ($row) {
                $admin = $row->user;
                if (!$admin) {
                    return '';
                }
                return $admin->first_name ? $admin->first_name : $admin->shortname;
            })
            ->editColumn('last_name', function ($row) {
                $admin = $row->user;
                if (!$admin) {
                    return '';
                }
                return $admin->last_name;
            })
            ->editColumn('service', function ($row) {
                $admin = $row->service;
                if (!$admin) {
                    return '';
                }
                return $admin->name;
            })
            ->editColumn('service_id', function ($row) {
//                return $row->service_id;
                return '<a href="' . route('dinetkan.invoice_dinetkan.order.service_detail', ($row->service_id ? $row->service_id : 0) ) . '" type="button" class="btn btn-light-success" >'.($row->service_id ? $row->service_id : 0).'</a>';
            })
            ->editColumn('status', function ($row) {
                return match ($row->status) {
                ServiceStatusEnum::NEW => '<span class="badge bg-warning">NEW</span>',
                                        ServiceStatusEnum::INACTIVE => '<span class="badge bg-warning">INACTIVE</span>',
                                        ServiceStatusEnum::ACTIVE => '<span class="badge bg-success">ACTIVE</span>',
                                        ServiceStatusEnum::SUSPEND => '<span class="badge bg-danger">SUSPEND</span>',
                                        default => '<span class="badge bg-danger">Canceled</span>',
                            };
                        })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('dinetkan.invoice_dinetkan.order.edit', $row->id ) . '" type="button" class="btn btn-light-warning" >Edit</a>';
            })
            ->addColumn('mitra', function ($row) {
                $mitra = "";
                if(isset($row->user->id_mitra)){
                    $cekmitra = Mitra::where('id_mitra', $row->id_mitra)->first();
                    if(isset($cekmitra->name)){
                        $mitra = $cekmitra->name ." (".$cekmitra->id_mitra.")";
                    }
                }
                return $mitra;
            })
            ->rawColumns(['action', 'status', 'service_id'])
            ->addColumn('company', function ($row){
                $cname = '';
                if($row->user){
                    $company = Company::query()->where('group_id', $row->user->id)->first();
                    if($company){
                        $cname = $company->name;
                    }
                }
                return $cname;
            })
            ->toJson();
    }

    public function cancel(Request $request)
    {
        if (! $request->ajax()) {
            return abort(404);
        }

        $mapping =  MappingUserLicense::where('status', ServiceStatusEnum::CANCEL)->with('user')->with('service')->get();

        return DataTables::of($mapping)
            ->addIndexColumn()

            ->editColumn('first_name', function ($row) {
                $admin = $row->user;
                if (!$admin) {
                    return '';
                }
                return $admin->first_name ? $admin->first_name : $admin->shortname;
            })
            ->editColumn('last_name', function ($row) {
                $admin = $row->user;
                if (!$admin) {
                    return '';
                }
                return $admin->last_name;
            })
            ->editColumn('service', function ($row) {
                $admin = $row->service;
                if (!$admin) {
                    return '';
                }
                return $admin->name;
            })
            ->editColumn('due_date', function ($row) {
                return $row->due_date;
            })
            ->editColumn('service_id', function ($row) {
//                return $row->service_id;
                return '<a href="' . route('dinetkan.invoice_dinetkan.order.service_detail', ($row->service_id ? $row->service_id : 0) ) . '" type="button" class="btn btn-light-success" >'.($row->service_id ? $row->service_id : 0).'</a>';
            })
            ->editColumn('status', function ($row) {
                return match ($row->status) {
                ServiceStatusEnum::PROGRESS => '<span class="badge bg-warning">Progress</span>',
                                        ServiceStatusEnum::INACTIVE => '<span class="badge bg-warning">INACTIVE</span>',
                                        ServiceStatusEnum::ACTIVE => '<span class="badge bg-success">ACTIVE</span>',
                                        default => '<span class="badge bg-danger">Canceled</span>',
                            };
                        })
            ->addColumn('action', function ($row) {
//                return '<a type="button" class="edit-icon edit badge badge-info" title="Cancel" data-id="' . $row->id . '">Edit</a>';
                return '';
            })
            ->addColumn('mitra', function ($row) {
                $mitra = "";
                if(isset($row->user->id_mitra)){
                    $cekmitra = Mitra::where('id_mitra', $row->id_mitra)->first();
                    if(isset($cekmitra->name)){
                        $mitra = $cekmitra->name ." (".$cekmitra->id_mitra.")";
                    }
                }
                return $mitra;
            })
            ->rawColumns(['action', 'status', 'service_id'])
            ->addColumn('company', function ($row){
                $cname = '';
                if($row->user){
                    $company = Company::query()->where('group_id', $row->user->id)->first();
                    if($company){
                        $cname = $company->name;
                    }
                }
                return $cname;
            })
            ->toJson();
    }


    public function overdue(Request $request)
    {
        if (! $request->ajax()) {
            return abort(404);
        }

        $mapping =  MappingUserLicense::where('status', ServiceStatusEnum::OVERDUE)->with('user')->with('service')->get();

        return DataTables::of($mapping)
            ->addIndexColumn()

            ->editColumn('first_name', function ($row) {
                $admin = $row->user;
                if (!$admin) {
                    return '';
                }
                return $admin->first_name ? $admin->first_name : $admin->shortname;
            })
            ->editColumn('last_name', function ($row) {
                $admin = $row->user;
                if (!$admin) {
                    return '';
                }
                return $admin->last_name;
            })
            ->editColumn('service', function ($row) {
                $admin = $row->service;
                if (!$admin) {
                    return '';
                }
                return $admin->name;
            })
            ->editColumn('service_id', function ($row) {
//                return $row->service_id;
                return '<a href="' . route('dinetkan.invoice_dinetkan.order.service_detail', ($row->service_id ? $row->service_id : 0) ) . '" type="button" class="btn btn-light-success" >'.($row->service_id ? $row->service_id : 0).'</a>';
            })
            ->editColumn('status', function ($row) {
                return match ($row->status) {
                ServiceStatusEnum::NEW => '<span class="badge bg-warning">NEW</span>',
                                        ServiceStatusEnum::INACTIVE => '<span class="badge bg-warning">INACTIVE</span>',
                                        ServiceStatusEnum::ACTIVE => '<span class="badge bg-success">ACTIVE</span>',
                                        ServiceStatusEnum::OVERDUE => '<span class="badge bg-danger">OVERDUE</span>',
                                        default => '<span class="badge bg-danger">Canceled</span>',
                            };
                        })
            ->addColumn('action', function ($row) {
                return '<a href="" class="btn btn-xs btn-danger" title="Cancel">Cancel</a>';
            })
            ->addColumn('mitra', function ($row) {
                $mitra = "";
                if(isset($row->user->id_mitra)){
                    $cekmitra = Mitra::where('id_mitra', $row->id_mitra)->first();
                    if(isset($cekmitra->name)){
                        $mitra = $cekmitra->name ." (".$cekmitra->id_mitra.")";
                    }
                }
                return $mitra;
            })
            ->rawColumns(['action', 'status', 'service_id'])
            ->addColumn('company', function ($row){
                $cname = '';
                $company = Company::query()->where('group_id', $row->user->id)->first();
                if($company){
                    $cname = $company->name;
                }
                return $cname;
            })
            ->toJson();
    }

    public function unpaid(Request $request)
    {
        if (! $request->ajax()) {
            return abort(404);
        }

        $invoices = AdminDinetkanInvoice::where('status', DinetkanInvoiceStatusEnum::UNPAID)
//            ->where('due_date','>=', Carbon::now())
            ->with('admin')
            ->with('mapping_mitra_admin')
            ->get();

        return DataTables::of($invoices)
            ->addIndexColumn()

            ->editColumn('first_name', function ($row) {
                $admin = $row->admin;
                if (!$admin) {
                    return '';
                }
                return $admin->first_name ? $admin->first_name : $admin->shortname;
            })
            ->editColumn('last_name', function ($row) {
                $admin = $row->admin;
                if (!$admin) {
                    return '';
                }
                return $admin->last_name;
            })
            ->editColumn('status', function ($row) {
                return match ($row->status) {
                DinetkanInvoiceStatusEnum::UNPAID => '<span class="badge bg-warning">Unpaid</span>',
                    DinetkanInvoiceStatusEnum::PAID => '<span class="badge bg-success">Paid</span>',
                    default => '<span class="badge bg-danger">Canceled</span>',
                };
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('dinetkan.invoice_dinetkan.detail', $row->no_invoice) . '"  class="btn btn-light-primary " title="Manual Pay">Manual Pay</a>';
            })
            ->addColumn('service_id', function ($row) {
                $service_id = '';
                if (isset($row->mapping_mitra_admin->service_id)) {
                    return $service_id = $row->mapping_mitra_admin->service_id;
                }
                return $service_id;
            })
            ->rawColumns(['action', 'status'])
            ->addColumn('company', function ($row){
                $cname = '';
                if($row->admin){
                    $company = Company::query()->where('group_id', $row->admin->id)->first();
                    if($company){
                        $cname = $company->name;
                    }
                }
                return $cname;
            })
            ->toJson();
    }


    public function paid(Request $request)
    {
        if (! $request->ajax()) {
            return abort(404);
        }

        $invoices = AdminDinetkanInvoice::query()
            ->where('status', DinetkanInvoiceStatusEnum::PAID)
            ->with('admin')
            ->with('mapping_mitra_admin')
            ->get();

        return DataTables::of($invoices)
            ->addIndexColumn()

            ->editColumn('first_name', function ($row) {
                $admin = $row->admin;
                if (!$admin) {
                    return '';
                }
                return $admin->first_name ? $admin->first_name : $admin->shortname;
            })
            ->editColumn('last_name', function ($row) {
                $admin = $row->admin;
                if (!$admin) {
                    return '';
                }
                return $admin->last_name;
            })
            ->editColumn('status', function ($row) {
                return match ($row->status) {
                DinetkanInvoiceStatusEnum::UNPAID => '<span class="badge bg-warning">Unpaid</span>',
                    DinetkanInvoiceStatusEnum::PAID => '<span class="badge bg-success">Paid</span>',
                    default => '<span class="badge bg-danger">Canceled</span>',
                };
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('dinetkan.invoice_dinetkan.detail', $row->no_invoice) . '"  class="btn btn-light-primary " title="Manual Pay">Manual Pay</a>';
            })
            ->addColumn('service_id', function ($row) {
                $service_id = '';
                if (isset($row->mapping_mitra_admin->service_id)) {
                    return $service_id = $row->mapping_mitra_admin->service_id;
                }
                return $service_id;
            })
            ->rawColumns(['action', 'status'])
            ->addColumn('company', function ($row){
                $cname = '';
                if($row->admin){
                    $company = Company::query()->where('group_id', $row->admin->id)->first();
                    if($company){
                        $cname = $company->name;
                    }
                }
                return $cname;
            })
            ->toJson();
    }

    public function expired(Request $request)
    {
        if (! $request->ajax()) {
            return abort(404);
        }

        $invoices = AdminDinetkanInvoice::query()
            ->whereIn('status', [DinetkanInvoiceStatusEnum::EXPIRED,DinetkanInvoiceStatusEnum::CANCEL])
            ->with('admin')
            ->with('mapping_mitra_admin')
            ->get();
        return DataTables::of($invoices)
            ->addIndexColumn()

            ->editColumn('first_name', function ($row) {
                $admin = $row->admin;
                if (!$admin) {
                    return '';
                }
                return $admin->first_name ? $admin->first_name : $admin->shortname;
            })
            ->editColumn('last_name', function ($row) {
                $admin = $row->admin;
                if (!$admin) {
                    return '';
                }
                return $admin->last_name;
            })
            ->editColumn('status', function ($row) {
                return match ($row->status) {
                DinetkanInvoiceStatusEnum::EXPIRED => '<span class="badge bg-">warning</span>',
                    DinetkanInvoiceStatusEnum::CANCEL => '<span class="badge bg-danger">CANCEL</span>',
                    default => '<span class="badge bg-danger">Canceled</span>',
                };
            })
            ->addColumn('action', function ($row) {
                return '';
            })
            ->addColumn('service_id', function ($row) {
                $service_id = '';
                if (isset($row->mapping_mitra_admin->service_id)) {
                    return $service_id = $row->mapping_mitra_admin->service_id;
                }
                return $service_id;
            })
            ->rawColumns(['action', 'status'])
            ->addColumn('company', function ($row){
                $cname = '';
                if($row->admin){
                    $company = Company::query()->where('group_id', $row->admin->id)->first();
                    if($company){
                        $cname = $company->name;
                    }
                }
                return $cname;
            })
            ->toJson();
    }

    public function indexcc(Request $request)
    {
        if ($request->ajax()) {
            $invoices = Invoice::where('invoice.group_id', $request->user()->id_group)
                ->where('status', 0)
                ->with([
                    'member:id,full_name,wa,kode_area,address',
                    'service.pppoe',
                    'service.profile'
                ]);

            return DataTables::of($invoices)
                ->addIndexColumn()
                ->editColumn('price', function ($row) {
                    $amount_ppn = ($row->price * $row->ppn) / 100;
                    $amount_discount = ($row->price * $row->discount) / 100;

                    if ($row->discount === null) {
                        // No discount
                        return $row->price + $amount_ppn;
                    } elseif ($row->ppn === null) {
                        // No PPN
                        return $row->price - $amount_discount;
                    } else {
                        // Both PPN and discount
                        return $row->price + $amount_ppn - $amount_discount;
                    }
                })
                ->addColumn('action', function ($row) {
                    $url = config('app.url');

                    // Prepare the WhatsApp number
                    $nowa = $row->member->wa ?? '081222339257';
                    $wa = '';
                    if (!preg_match('/[^+0-9]/', trim($nowa))) {
                        if (substr(trim($nowa), 0, 2) == '62') {
                            $wa = trim($nowa);
                        } elseif (substr(trim($nowa), 0, 1) == '0') {
                            $wa = '62' . substr(trim($nowa), 1);
                        }
                    }

                    // Return full HTML, wrapping no_invoice in a badge:
                    return '
                        <a href="javascript:void(0)" id="pay"
                           data-id="' . $row->id . '"
                           data-ppp="' . $row->service->pppoe_id . '"
                           class="badge b-ln-height badge-primary">
                           CONFIRM
                        </a>
                        <a href="https://wa.me/' . $wa . '" target="_blank" class="badge b-ln-height badge-success">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="' . route('invoice.pay', $row->no_invoice) . '" target="_blank" class="badge b-ln-height badge-warning">
                            <i class="fas fa-bank"></i>
                        </a>
                        <a href="/invoice/pdf/' . $row->no_invoice . '" target="_blank" id="print" class="badge b-ln-height badge-secondary">
                            <i class="fas fa-print"></i>
                        </a>
                        <a href="javascript:void(0)"
                           class="badge b-ln-height badge-danger" id="delete" data-id="' . $row->id . '">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    ';
                })
                ->toJson();
        }

        // Non-AJAX part: just return the view
        $members = Member::where('group_id', $request->user()->id_group)
            ->select('id', 'full_name')
            ->get();

        return view('billing.unpaid.index', compact('members'));
    }

    public function getServicesByMember(Request $request, string $memberId)
    {
        $services = PppoeMember::where('member_id', $memberId)
            ->whereHas('member', function ($query) use ($request) {
                $query->where('group_id', $request->user()->id_group);
            })
            ->with(['pppoe:id,username', 'profile:id,name,price'])
            ->get();

        return response()->json($services);
    }

    public function getServiceDetails(Request $request, string $serviceId)
    {
        $service = PppoeMember::where('id', $serviceId)
            ->whereHas('member', function ($query) use ($request) {
                $query->where('group_id', $request->user()->id_group);
            })
            ->with(['pppoe:id,username,value,profile', 'profile:id,name,price', 'invoices'])
            ->firstOrFail();

        return response()->json($service);
    }

    public function getProfile(Request $request)
    {
        $profile = PppoeProfile::where('group_id', $request->user()->id_group)
            ->where('id', $request->profile_id)
            ->get(['id', 'name', 'price']);
        return response()->json($profile);
    }

    public function show() {}

    public function generateInvoice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item' => 'required',
            'amount' => 'required',
            'pppoe_member_id' => 'required|exists:frradius.pppoe_member,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $pppoeMember = PppoeMember::findOrFail($request->pppoe_member_id);
        $member_id = $pppoeMember->member_id;

        $price = str_replace('.', '', $request->amount);
        $ppn = $request->ppn;
        $discount = $request->discount;
        $due_date = $request->next_due;
        $invoice_date = $request->today;
        $payment_type = $request->payment_type;
        $billing_period = $request->billing_period;
        $subscribe = $request->subscribe;
        $full_name = $request->full_name;

        $amount_ppn = ($price * $ppn) / 100;
        $amount_discount = ($price * $discount) / 100;
        $total = $price + $amount_ppn - $amount_discount;

        // Determine period and next_invoice
        if ($payment_type === 'Prabayar' && $billing_period === 'Fixed Date') {
            $periode = Carbon::createFromFormat('Y-m-d', $due_date);
            $get_periode = date('Y-m-d', strtotime($periode));
            $next_invoice = Carbon::createFromFormat('Y-m-d', $due_date)->addMonthsWithNoOverflow(1)->toDateString();
        } elseif ($payment_type === 'Pascabayar' && $billing_period === 'Fixed Date') {
            $periode = Carbon::createFromFormat('Y-m-d', $due_date)->subMonthsWithNoOverflow(1);
            $get_periode = date('Y-m-d', strtotime($periode));
            $next_invoice = Carbon::createFromFormat('Y-m-d', $due_date)->addMonthsWithNoOverflow(1)->toDateString();
        } elseif ($payment_type === 'Pascabayar' && $billing_period === 'Billing Cycle') {
            $periode = Carbon::createFromFormat('Y-m-d', $due_date)->subMonthsWithNoOverflow(1);
            $get_periode = date('Y-m-d', strtotime($periode));
            $next_invoice = Carbon::createFromFormat('Y-m-d', $due_date)
                ->startOfMonth()
                ->addMonthsWithNoOverflow(1)
                ->toDateString();
        }

        // Example of generating a custom invoice number
        // $no_invoice = 'INV-'.date('ymd').'-'.rand(1000, 9999);
        // Or keep your original format:
        $no_invoice = date('m') . rand(0000000, 9999999);

        $invoice = Invoice::create([
            'group_id' => $request->user()->id_group,
            'pppoe_id' => $pppoeMember->pppoe_id,
            'member_id' => $member_id,
            'pppoe_member_id' => $pppoeMember->id,
            'no_invoice' => $no_invoice,
            'item' => $request->item,
            'price' => $price,
            'ppn' => $ppn,
            'discount' => $discount,
            'invoice_date' => $invoice_date,
            'due_date' => $due_date,
            'period' => $periode ?? null,
            'subscribe' => $subscribe,
            'payment_type' => $payment_type,
            'billing_period' => $billing_period,
            'payment_url' => route('invoice.pay', $no_invoice),
            'status' => 0,
        ]);

        $pppoeMember->update([
            'next_invoice' => $next_invoice,
        ]);

        activity()
            ->tap(function (Activity $activity) use ($request) {
                $activity->group_id = $request->user()->id_group;
            })
            ->event('Create')
            ->log('Create Manual Invoice: ' . $invoice->no_invoice . ' for ' . $full_name);

        return response()->json([
            'success' => true,
            'message' => 'Invoice Berhasil Dibuat',
            'data' => $invoice,
        ]);
    }

    public function generateInvoiceWA(Request $request, Invoice $invoice_id)
    {
        // Load invoice with necessary relationships
        $invoice = Invoice::with(['service.member', 'service.pppoe', 'service.profile'])
            ->where('id', $invoice_id->id)
            ->firstOrFail();

        // Extract data
        $member = $invoice->service->member;
        $pppoeUser = $invoice->service->pppoe;
        $profile = $invoice->service->profile;

        $full_name = $member->full_name;
        $id_member = $member->id_member;
        $wa = $member->wa;
        $username = $pppoeUser->username;
        $password = $pppoeUser->value;
        $profile_name = $profile->name;
        $payment_type = $invoice->payment_type;
        $billing_period = $invoice->billing_period;
        $item = $invoice->item;
        $price = $invoice->price;
        $ppn = $invoice->ppn;
        $discount = $invoice->discount;
        $due_date = $invoice->due_date;
        $subscribe = $invoice->subscribe;
        $invoice_date = $invoice->invoice_date;

        // Compute total
        $amount_ppn = ($price * $ppn) / 100;
        $amount_discount = ($price * $discount) / 100;
        $total = $price + $amount_ppn - $amount_discount;

        // Format amounts and dates
        $amount_format = number_format($price, 0, '.', '.');
        $total_format = number_format($total, 0, '.', '.');
        $invoice_date_format = date('d/m/Y', strtotime($invoice_date));
        $due_date_format = date('d/m/Y', strtotime($due_date));

        // Compute periode / next_invoice
        if ($payment_type === 'Prabayar' && $billing_period === 'Fixed Date') {
            $periode = Carbon::createFromFormat('Y-m-d', $due_date);
            $get_periode = date('Y-m-d', strtotime($periode));
            $periode_format = indonesiaDateFormat($get_periode);
            $next_invoice = Carbon::createFromFormat('Y-m-d', $due_date)->addMonthsWithNoOverflow(1)->toDateString();
        } elseif ($payment_type === 'Pascabayar' && $billing_period === 'Fixed Date') {
            $periode = Carbon::createFromFormat('Y-m-d', $due_date)->subMonthsWithNoOverflow(1);
            $get_periode = date('Y-m-d', strtotime($periode));
            $periode_format = indonesiaDateFormat($get_periode);
            $next_invoice = Carbon::createFromFormat('Y-m-d', $due_date)->addMonthsWithNoOverflow(1)->toDateString();
        } elseif ($payment_type === 'Pascabayar' && $billing_period === 'Billing Cycle') {
            $periode = Carbon::createFromFormat('Y-m-d', $due_date)->subMonthsWithNoOverflow(1);
            $get_periode = date('Y-m-d', strtotime($periode));
            $periode_format = indonesiaDateFormat($get_periode);
            $next_invoice = Carbon::createFromFormat('Y-m-d', $due_date)
                ->startOfMonth()
                ->addMonthsWithNoOverflow(1)
                ->toDateString();
        }

        $billing = BillingSetting::where('group_id', $request->user()->id_group)
            ->select('notif_it')
            ->first();

        $no_invoice = $invoice->no_invoice;
        $payment_url = $invoice->payment_url;

        if ($billing->notif_it === 1) {
            if ($wa !== null) {
                $template = WablasTemplate::where('group_id', $request->user()->id_group)
                    ->select('invoice_terbit')
                    ->first()->invoice_terbit;

                $shortcode = [
                    '[nama_lengkap]',
                    '[id_pelanggan]',
                    '[username]',
                    '[password]',
                    '[paket_internet]',
                    '[no_invoice]',
                    '[tgl_invoice]',
                    '[jumlah]',
                    '[ppn]',
                    '[discount]',
                    '[total]',
                    '[periode]',
                    '[jth_tempo]',
                    '[payment_midtrans]'
                ];
                $source = [
                    $full_name,
                    $id_member,
                    $username,
                    $password,
                    $profile_name,
                    $no_invoice,
                    $invoice_date_format,
                    $amount_format,
                    $ppn,
                    $discount,
                    $total_format,
                    $periode_format,
                    $due_date_format,
                    $payment_url
                ];
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
                        'subject' => 'INVOICE TERBIT #' . $no_invoice,
                        'message' => preg_replace("/\r\n|\r|\n/", '<br>', $message),
                        'phone' => $row['number'],
                        'status' => $row['status'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    $pesan[] = $draw;
                }
                WablasMessage::insert($pesan);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'WA Berhasil Terkirim',
        ]);
    }

    public function printInvoice(Request $request)
    {
        $no_invoice = last(request()->segments());
        $invoice = Invoice::where('group_id', $request->user()->id_group)
            ->where('no_invoice', $no_invoice)
            ->with('member')
            ->get();

        $pdf = Pdf::loadView('billing.invoice.print_thermal', compact('invoice'));

        return $pdf->stream();
    }

    public function payInvoice(Request $request, Invoice $invoice)
    {
        $group_id = $request->user()->id_group;

        if ($request->payment_type === 'Prabayar') {
            $next_due = Carbon::createFromFormat('Y-m-d', $request->due_date)->addMonthsWithNoOverflow(1);
            $next_invoice = Carbon::createFromFormat('Y-m-d', $request->due_date)->addMonthsWithNoOverflow(1);
        } elseif ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Fixed Date') {
            $next_due = Carbon::createFromFormat('Y-m-d', $request->due_date)->addMonthsWithNoOverflow(1);
            $next_invoice = Carbon::createFromFormat('Y-m-d', $request->due_date)->addMonthsWithNoOverflow(1);
        } elseif ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Billing Cycle') {
            $due_bc = BillingSetting::where('group_id', $group_id)->select('due_bc')->first();
            $next_due = Carbon::createFromFormat('Y-m-d', $request->due_date)
                ->setDay($due_bc->due_bc)
                ->addMonths(1);
            $next_invoice = Carbon::createFromFormat('Y-m-d', $request->due_date)
                ->startOfMonth()
                ->addMonths(1);
        }

        $transaksi = Transaksi::create([
            'group_id' => $group_id,
            'invoice_id' => $invoice->id,
            'invoice_type' => Invoice::class,
            'type' => TransactionTypeEnum::INCOME,
            'category' => TransactionCategoryEnum::INVOICE,
            'item' => 'Invoice',
            'deskripsi' => "Payment #$request->no_invoice a.n $request->full_name",
            'price' => $request->payment_total,
            'tanggal' => Carbon::now(),
            'payment_method' => $request->payment_method,
            'admin' => $request->user()->username,
        ]);

        $invoice->update([
            'next_due' => $next_due,
            'next_invoice' => $next_invoice,
            'paid_date' => Carbon::today()->toDateString(),
            'status' => 1,
        ]);

        $cek_inv = Invoice::where([['member_id', $request->member_id], ['status', 0]])->count();
        if ($request->ppp_status === '2' && $cek_inv === 0) {
            $ppp = PppoeUser::where('id', $request->ppp_id);
            $ppp->update([
                'status' => 1,
            ]);
            if ($request->nas === null) {
                $nas = RadiusNas::where('group_id', $group_id)->select('nasname', 'secret')->get();
                foreach ($nas as $nasitem) {
                    $command = Process::path('/usr/bin/')->run("echo User-Name='$request->pppoe_user' | radclient -r 1 $nasitem[nasname]:3799 disconnect $nasitem[secret]");
                }
            } else {
                $nas_secret = RadiusNas::where('group_id', $group_id)
                    ->where('nasname', $request->nas)
                    ->select('secret')
                    ->first();
                $command = Process::path('/usr/bin/')->run("echo User-Name='$request->pppoe_user' | radclient -r 1 $request->nas:3799 disconnect $nas_secret->secret");
            }
        }

        activity()
            ->tap(function (Activity $activity) use ($request) {
                $activity->group_id = $request->user()->id_group;
            })
            ->event('Update')
            ->log('Pay Invoice: ' . $request->no_invoice . ' a.n ' . $request->full_name . '');

        return response()->json([
            'success' => true,
            'message' => 'Invoice Berhasil Dibayar',
        ]);
    }

    public function payInvoiceWA(Request $request, Invoice $invoice)
    {
        $group_id = $request->user()->id_group;
        $billing = BillingSetting::where('group_id', $group_id)->select('notif_ps')->first();
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
                $template = WablasTemplate::where('group_id', $group_id)->select('payment_paid')->first()->payment_paid;
                $shortcode = ['[nama_lengkap]', '[id_pelanggan]', '[username]', '[password]', '[paket_internet]', '[no_invoice]', '[tgl_invoice]', '[jumlah]', '[ppn]', '[discount]', '[total]', '[periode]', '[jth_tempo]', '[metode_pembayaran]', '[payment_midtrans]'];
                $source = [$request->full_name, $request->id_member, $request->pppoe_user, $request->pppoe_pass, $request->pppoe_profile, $request->no_invoice, $invoice_date_format, $amount_format, $request->ppn, $request->discount, $total_format, $periode_format, $due_date_format, $payment_method, $request->payment_url];
                $message = str_replace($shortcode, $source, $template);
                $message_format = str_replace('<br>', "\n", $message);

                $curl = curl_init();
                $wablas = Wablas::where('group_id', $group_id)->select('token', 'sender')->first();
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
                        'group_id' => $group_id,
                        'id_message' => $row['note'],
                        'subject' => 'INVOICE PAID #' . $invoice->no_invoice,
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

        return response()->json([
            'success' => true,
            'message' => 'WA Berhasil Terkirim',
        ]);
    }

    public function destroy($id)
    {
        $unpaid = Invoice::findOrFail($id);
        $unpaid->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }

    public function create(Request $request){
        $license = LicenseDinetkan::where('id', $request->license_dinetkan_id)->first();
        $userdinetkan = UserDinetkan::where('dinetkan_user_id', $request->dinetkan_user_id)->first();
        $data = array(
            'vlan' => $request->vlan,
            'metro' => $request->metro,
            'vendor' => $request->vendor,
            'trafic_mrtg' => $request->trafic_mrtg,
            'ip_prefix' => $request->ip_prefix,
            'tree' => $request->trafic_mrtg_tree,
            'node' => $request->trafic_mrtg_tree_node,
            'graph' => $request->trafic_mrtg_graph,
        );
        $userdinetkan->update($data);
        if($request->statuses == ServiceStatusEnum::ACTIVE->value){
            // create mapping service
            $userdinetkan->update(['dinetkan_next_due' => Carbon::parse($request->next_due)->format('Y-m-d')]);
            $order = MappingUserLicense::create(
                [
                    'dinetkan_user_id' => $userdinetkan->dinetkan_user_id,
                    'license_id' => $license->id,
                    'status' => $request->statuses,
                    'no_invoice' => "", //$invoice->no_invoice,
                    'due_date' => Carbon::parse($request->next_due)->format('Y-m-d'), // $dueDate->format('Y-m-d'),
                    'category_id' => $license->category_id
                ]
            );
//            return response()->json(['message' => 'Success created successfully'], 201);


        // set invoice due  _date 1 month from now and status unpaid and send notif to whatsapp and email

        $invoice = $this->placeOrder($userdinetkan, $license->id, $request->is_otc);
        if($invoice){
            if($invoice->status != DinetkanInvoiceStatusEnum::NEW->value){
                $order = MappingUserLicense::where('id', $order->id)->first();

                $order->update([
                    'no_invoice' => $invoice->no_invoice,
                ]);
                $user = UserDinetkan::where('dinetkan_user_id', $order->dinetkan_user_id)->first();
                $dueDate = Carbon::parse($request->next_due); //Carbon::now();
                $periodStart = $dueDate->format('Y-m-d');
                $periodEnd = Carbon::parse($dueDate)->addMonthNoOverflow()->format('Y-m-d');
                $subscribe = Carbon::parse($periodStart)->format('d/m/Y') . ' s/d ' . Carbon::parse($periodEnd)->format('d/m/Y');

                $userupdate = ['dinetkan_next_due' => $periodEnd];
                $user->update($userupdate);
                $dataupdate = [
                    'invoice_date'  => Carbon::now(),
                    'due_date'      => $periodStart, //Carbon::now()->addDays(5), //$periodEnd,
                    'period'        => $periodEnd,
                    'subscribe'     => $subscribe,
                    'payment_url'   => route('admin.invoice_dinetkan', $invoice->no_invoice),
                    'status'        => DinetkanInvoiceStatusEnum::UNPAID
                ];
                $invoice->update($dataupdate);

                $orderupdate = ['due_date' => Carbon::parse($request->next_due)->format('Y-m-d')];
                $order->update($orderupdate);

                $maxdate = Carbon::now()->addDays(5);
                $maxdateformat = Carbon::parse($maxdate)->format('d-m-Y');
                $totalppn = 0;
                if($invoice->ppn > 0){
                    $totalppn = $invoice->price * $invoice->ppn / 100;
                }
                $totalotc = 0;
                if($invoice->price_otc > 0){
                    $totalotc = $invoice->price_otc;
                }
                if($invoice->ppn_otc > 0){
                    $totalppnotc = $invoice->price_otc * $invoice->ppn_otc / 100;
                    $totalotc = $invoice->price_otc + $totalppnotc;
                }

                $total = $invoice->price + $totalppn + $totalotc;
                $total_format = number_format($total, 0, '.', '.');
                $details = [
                    'email' => $user->email,
                    'subject' => 'Invoice Remider',
                    'fullname' => $user->first_name.' '.$user->last_name,
                    'no_invoice' => $invoice->no_invoice,
                    'invoice_date' => $invoice->invoice_date,
                    'due_date' => $invoice->due_date,
                    'total' => $total_format,
                    'max_date' => $maxdateformat,
                    'url' => route('admin.invoice_dinetkan', $invoice->no_invoice),
                    'item' => $invoice->item,
                    'view' => 'notif_invoice',
                ];

                $settings = $this->settings;
                $priceData = $this->adminPaymentService->calculateLicenseOrderPrice($invoice->itemable, $invoice->admin, $invoice);
                $pdf = Pdf::loadView('accounts.licensing_dinetkan.invoice_pdf',
                    compact(
                        'invoice',
                        'priceData',
                        'settings'
                    ))->setPaper('a4', 'potrait');

                // Simpan ke storage sementara
                $pdfPath = storage_path("app/invoice/invoice_{$invoice->no_invoice}.pdf");
                $pdf->save($pdfPath);
                Mail::to($details['email'])->send(new EmailInvoiceNotif($details,$pdfPath));

                // proses send to wa
                $message = "Kepada ".$user->first_name.' '.$user->last_name;
                $message .="\r\n";
                $message .="Kami berharap email ini sampai kepada Anda dengan baik. Silakan rincian faktur Anda:";
                $message .="\r\n";
                $message .="Nomor Invoice : ".$invoice->no_invoice;
                $message .="\r\n";
                $message .="Service : ".$invoice->item;
                $message .="\r\n";
                $message .="Tanggal Invoice : ".$invoice->invoice_date;
                $message .="\r\n";
                $message .="Tanggal Jatuh Tempo : ".$invoice->due_date;
                $message .="\r\n";
                $message .="Total Pembayaran : ".$total_format;
                $message .="\r\n";
                $message .="Harap melakukan pembayaran sebelum tanggal ".$maxdateformat." untuk menghindari biaya keterlambatan dan Order akan di suspend. Anda dapat melihat dan membayar faktur Anda dengan mengklik tombol di bawah ini:";
                $message .="\r\n";
                $message .="Link Invoice ".route('admin.invoice_dinetkan', $invoice->no_invoice);
                $message .="\r\n";
                $message .="Terima Kasih";
                $message .="\r\n";
                $message .="Salam,";
                $message .="\r\n";
                $message .="Dinetkan";

                $nomorhp = $this->gantiformat($user->whatsapp);

                $owner = User::where('role', 'Owner')->first();
                $_id = $owner->whatsapp;
                $apiUrl = env('WHATSAPP_URL_NEW')."send-message/".$_id; //env('CACTI_ENDPOINT').'cacti/logout/'.$_id;
                try {
                    $params = array(
                        "jid" => $nomorhp."@s.whatsapp.net",
                        "content" => array(
                            "text" => $message
                        )
                    );
                    // Kirim POST request ke API eksternal
                    // Http::post($apiUrl, $params);
                    $response = Http::timeout(10)->post($apiUrl, $params);
                    // if($response->successful()){
                    //     $json = $response->json();
                    //     $status = $json->status;
                    //     $receiver = $nomorhp;
                    //     $shortname = $owner->shortname;
                    //     save_wa_log($shortname,$receiver,$message,$status);
                    // }

                } catch (\Exception $e) {

                }

                return response()->json(['message' => 'Order Was Update and send Invoice To User'], 201);
            }
        }


    }else{
            // $userdinetkan->next_due = $request->next_due;
            $invoice = $this->placeOrder($userdinetkan, $license->id);
            if($invoice){
                // create mapping service
                MappingUserLicense::create(
                    [
                        'dinetkan_user_id' => $userdinetkan->dinetkan_user_id,
                        'license_id' => $license->id,
                        'status' => $request->statuses,
                        'no_invoice' => $invoice->no_invoice,
                        'due_date' => null, // $dueDate->format('Y-m-d'),
                        'category_id' => $license->category_id
                    ]
                );
                return response()->json(['message' => 'Success created successfully'], 201);
            }
        }

        return response()->json(['message' => 'Failed created successfully'], 500);

    }

    public function placeOrder(UserDinetkan $user, int $licenseId, $is_otc = 0)
    {
        $invoice = null;
        $license = $this->licenseDinetkanRepo->findById($licenseId);
        if($license){
            $invoice = $this->adminDinetkanPaymentService->createLicenseInvoice($license, $user, '', $is_otc);
            return $invoice;
        }
        return $invoice;
    }

    protected function cacti_login(){
        $_id = Str::lower(Str::replace(' ', '', multi_auth()->name));
        $apiUrl = env('CACTI_ENDPOINT').'cacti/login/'.$_id;
        try {
            $params = array(
                        "action" =>"login",
                        "login_username" => "wijaya",
                        "login_password" => "wijaya@2024"
                    );
            // Kirim POST request ke API eksternal
            $response = Http::timeout(10)->post($apiUrl, $params);
            Storage::disk('local')->append('owner_cacti.txt', json_encode($response->json(), JSON_PRETTY_PRINT) . "\n\n");
            // Periksa apakah request berhasil
            if ($response->successful()) {
                $data = $response->json();
                return $data['success'] ?? null;
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    protected function get_tree_mrtg(){
        try {
            $params = array(
                "action" =>"get_node",
                "tree_id" => "0",
                "id" => "%23"
            );
            $_id = Str::lower(Str::replace(' ', '', multi_auth()->name));
            $apiUrl = env('CACTI_ENDPOINT').'cacti/graph_view/'.$_id.'?' . urldecode(http_build_query($params)) ;
            // Kirim POST request ke API eksternal
            $response = Http::timeout(10)->get($apiUrl);
            // Periksa apakah request berhasil
            if ($response->successful()) {
                $data = $response->json();
                return $data ?? null;
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }




    public function get_graph_mrtg($id, $page=1){
        // step 2
        try {
            $params = array(
                'action' => 'tree_content',
                'node' => $id,
                'tree_id' => 2,
                'leaf_id' => 15,
                'hgd' => '',
                'header' =>false,
                'page' => $page
            );
            $_id = Str::lower(Str::replace(' ', '', multi_auth()->name));
            $apiUrl = env('CACTI_ENDPOINT').'cacti/v2/graph_view/'.$_id.'?' . urldecode(http_build_query($params));
            // Kirim POST request ke API eksternal
            $response = Http::timeout(10)->get($apiUrl);

            // Periksa apakah request berhasil
            if ($response->successful()) {
//                $data = $response->json();

                $data = collect($response->json())->map(function ($datax) {
                    return [
                        'rra_id' => $datax['rra_id'],
                        'local_graph_id' => $datax['local_graph_id'],
                        'graph_start' => $datax['graph_start'],
                        'graph_end' => $datax['graph_end'],
                        'graph_height' => $datax['graph_height'],
                        'graph_width' => $datax['graph_width'],
                        'Title' => $datax['GraphXport'][0]['Title'],

                    ];
                });
//                print_r($data);exit;
                return $data ?? null;
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    public function get_graph_mrtgX($node){
        try {
            $params = array(
                'action' => 'tree_content',
                'node' => $node,
                'tree_id' => '2',
                'leaf_id' => '15',
                'hgd' => '',
                'header' => false
            );
            $apiUrl = 'http://103.184.122.170/api/cacti/graph_view?' . urldecode(http_build_query($params)) ;
            // Kirim POST request ke API eksternal
            $response = Http::timeout(10)->get($apiUrl);

            // Periksa apakah request berhasil
            if ($response->successful()) {
                $data = $response->json();
                return $data ?? null;
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    public function detail($id){
        $invoice = AdminDinetkanInvoice::where('no_invoice', $id)->first();
        $mapping = MappingUserLicense::where('dinetkan_user_id', $invoice->dinetkan_user_id)->where('license_id', $invoice->itemable_id)->first();
        $adons = MappingAdons::where('id_mapping', $mapping->id)->get();
        $activeGateway = $this->settings->active_gateway;
        $priceData = $this->adminDinetkanPaymentService->calculateLicenseOrderPrice($invoice->itemable, $invoice->admin, $invoice);

        $config = match ($activeGateway) {
                'duitku' => new DuitkuConfig(
            idMerchant: $this->settings->duitku_merchant_code,
                    apiKey: $this->settings->duitku_api_key,
                    isProduction: !$this->settings->duitku_sandbox,
                ),
                default => null,
            };

            return view('backend.dinetkan.invoice_dinetkan.detail_invoice', [
                'activeGateway' => $activeGateway,
                'invoice' => $invoice,
                'priceData' => $priceData,
                'config' => $config,
                'settings' => $this->settings,
                'adons' => $adons,
                'total_ppn_ad' => 0,
            ]);
    }

    public function detail_update(Request $request){
        $invoice = AdminDinetkanInvoice::where('no_invoice', $request->no_invoice)->first();
        if($request->status == 1){
            // update status mapping\

            $mapping = MappingUserLicense::where('dinetkan_user_id', $invoice->dinetkan_user_id)
                ->where('license_id', $invoice->itemable_id)
                ->where('id', $invoice->id_mapping)
                ->first();
            $mapping->update([
                'status' => ServiceStatusEnum::ACTIVE,
                'due_date' => Carbon::parse($mapping->due_date)->addMonthsWithNoOverflow(1)
            ]);
            $this->adminDinetkanPaymentService->markInvoiceAsPaid($invoice);
            $invoice = AdminDinetkanInvoice::where('no_invoice', $request->no_invoice)->first();
            $invoice->update([
                'pay_from' => $request->pay_from,
                'notes' => $request->notes
            ]);

            $maxdate = Carbon::now()->addDays(5);
            $maxdateformat = Carbon::parse($maxdate)->format('d-m-Y');
            $totalppn = 0;
            if($invoice->ppn > 0){
                $totalppn = $invoice->price * $invoice->ppn / 100;
            }
            $totalotc = 0;
            if($invoice->price_otc > 0){
                $totalotc = $invoice->price_otc;
            }
            if($invoice->ppn_otc > 0){
                $totalppnotc = $invoice->price_otc * $invoice->ppn_otc / 100;
                $totalotc = $invoice->price_otc + $totalppnotc;
            }


            $total = $invoice->price + $totalppn + $totalotc;
            $total_format = number_format($total, 0, '.', '.');
            $user = UserDinetkan::where('dinetkan_user_id', $invoice->dinetkan_user_id)->first();
            $details = [
                'email' => $user->email,
                'subject' => 'Invoice Remider',
                'fullname' => $user->first_name.' '.$user->last_name,
                'no_invoice' => $invoice->no_invoice,
                'invoice_date' => $invoice->invoice_date,
                'due_date' => $invoice->due_date,
                'total' => $total_format,
                'max_date' => $maxdateformat,
                'url' => route('admin.invoice_dinetkan', $invoice->no_invoice),
                'item' => $invoice->item,
                'view' => 'notif_invoice',
                'status' => $invoice->status->value
            ];

            $settings = $this->settings;
            $priceData = $this->adminPaymentService->calculateLicenseOrderPrice($invoice->itemable, $invoice->admin, $invoice);
            $pdf = Pdf::loadView('accounts.licensing_dinetkan.invoice_pdf',
                compact(
                    'invoice',
                    'priceData',
                    'settings'
                ))->setPaper('a4', 'potrait');

            // Simpan ke storage sementara
            $pdfPath = storage_path("app/invoice/invoice_{$invoice->no_invoice}.pdf");
            $pdf->save($pdfPath);

            Mail::to($details['email'])->send(new EmailInvoiceNotif($details,$pdfPath));
        }
        return redirect()->route('dinetkan.invoice_dinetkan.index')->with('success', 'Tagihan was updated');
    }

    public function single_order($id){
        $order = MappingUserLicense::where('id', $id)->first();
        return response()->json($order);
    }

    public function cancel_mapping(Request $request, $id){
        $order = MappingUserLicense::where('id', $id)->first();
        if($order){
            // update order to cancel
            $order->update([
                'status' => ServiceStatusEnum::CANCEL
            ]);
            $invoices = AdminDinetkanInvoice::where('no_invoice', $order->no_invoice)->get();
            foreach ($invoices as $invoice){
                if($invoice){
                    $invoice->update([
                        'status' => DinetkanInvoiceStatusEnum::CANCEL
                    ]);
                }
            }
        }
        return response()->json(['message' => 'Order and invoice was canceled'], 201);
    }

    public function update_mapping(Request $request, $id){
        DB::beginTransaction();
        try{
            $order = MappingUserLicense::where('id', $id)->first();

            $order->update([
                'license_id' => $request->license_dinetkan_id,
                'status' => $request->statuses,
                'due_date' => null, // $dueDate->format('Y-m-d'),
                'category_id' => $request->category_id
            ]);
//            print_r($order);exit;
            if($request->statuses == ServiceStatusEnum::ACTIVE->value && $order->status != ServiceStatusEnum::ACTIVE->value){
                // set invoice due_date 1 month from now and status unpaid and send notif to whatsapp and email
                $invoice = AdminDinetkanInvoice::where('no_invoice', $order->no_invoice)->first();
                if($invoice){
                    if($invoice->status != DinetkanInvoiceStatusEnum::NEW->value){
                        $user = UserDinetkan::where('dinetkan_user_id', $order->dinetkan_user_id)->first();
                        $dueDate = Carbon::parse($request->next_due); //Carbon::now();
                        $periodStart = $dueDate->format('Y-m-d');
                        $periodEnd = Carbon::parse($dueDate)->addMonthNoOverflow()->format('Y-m-d');
                        $subscribe = Carbon::parse($periodStart)->format('d/m/Y') . ' s/d ' . Carbon::parse($periodEnd)->format('d/m/Y');

                        $userupdate = ['dinetkan_next_due' => $periodEnd];
                        $user->update($userupdate);
                        $dataupdate = [
                            'invoice_date'  => Carbon::now(),
                            'due_date'      => $periodStart, //Carbon::now()->addDays(5), //$periodEnd,
                            'period'        => $periodEnd,
                            'subscribe'     => $subscribe,
                            'payment_url'   => route('admin.invoice_dinetkan', $invoice->no_invoice),
                            'status'        => DinetkanInvoiceStatusEnum::UNPAID
                        ];
                        $invoice->update($dataupdate);

                        $orderupdate = ['due_date' => Carbon::parse($request->next_due)->format('Y-m-d')];
                        $order->update($orderupdate);

                        $maxdate = Carbon::now()->addDays(5);
                        $maxdateformat = Carbon::parse($maxdate)->format('d-m-Y');
                        $totalppn = 0;
                        if($invoice->ppn > 0){
                            $totalppn = $invoice->price * $invoice->ppn / 100;
                        }
                        $totalotc = 0;
                        if($invoice->price_otc > 0){
                            $totalotc = $invoice->price_otc;
                        }
                        if($invoice->ppn_otc > 0){
                            $totalppnotc = $invoice->price_otc * $invoice->ppn_otc / 100;
                            $totalotc = $invoice->price_otc + $totalppnotc;
                        }

                        $total = $invoice->price + $totalppn + $totalotc;
                        $total_format = number_format($total, 0, '.', '.');
                        $details = [
                            'email' => $user->email,
                            'subject' => 'Invoice Remider',
                            'fullname' => $user->first_name.' '.$user->last_name,
                            'no_invoice' => $invoice->no_invoice,
                            'invoice_date' => $invoice->invoice_date,
                            'due_date' => $invoice->due_date,
                            'total' => $total_format,
                            'max_date' => $maxdateformat,
                            'url' => route('admin.invoice_dinetkan', $invoice->no_invoice),
                            'item' => $invoice->item,
                            'view' => 'notif_invoice'
                        ];

                        $settings = $this->settings;
                        $priceData = $this->adminPaymentService->calculateLicenseOrderPrice($invoice->itemable, $invoice->admin, $invoice);
                        $pdf = Pdf::loadView('accounts.licensing_dinetkan.invoice_pdf',
                            compact(
                                'invoice',
                                'priceData',
                                'settings'
                            ))->setPaper('a4', 'potrait');

                        // Simpan ke storage sementara
//                        $pdfPath = storage_path("app/invoice/invoice_{$invoice->no_invoice}.pdf");
//                        $pdf->save($pdfPath);
//
//
//                        Mail::to($details['email'])->send(new EmailInvoiceNotif($details,$pdfPath));

                        // proses send to wa
                        $message = "Kepada ".$user->first_name.' '.$user->last_name;
                        $message .="\r\n";
                        $message .="Kami berharap email ini sampai kepada Anda dengan baik. Silakan rincian faktur Anda:";
                        $message .="\r\n";
                        $message .="Nomor Invoice : ".$invoice->no_invoice;
                        $message .="\r\n";
                        $message .="Service : ".$invoice->item;
                        $message .="\r\n";
                        $message .="Tanggal Invoice : ".$invoice->invoice_date;
                        $message .="\r\n";
                        $message .="Tanggal Jatuh Tempo : ".$invoice->due_date;
                        $message .="\r\n";
                        $message .="Total Pembayaran : ".$total_format;
                        $message .="\r\n";
                        $message .="Harap melakukan pembayaran sebelum tanggal ".$maxdateformat." untuk menghindari biaya keterlambatan dan Order akan di suspend. Anda dapat melihat dan membayar faktur Anda dengan mengklik tombol di bawah ini:";
                        $message .="\r\n";
                        $message .="Link Invoice ".route('admin.invoice_dinetkan', $invoice->no_invoice);
                        $message .="\r\n";
                        $message .="Terima Kasih";
                        $message .="\r\n";
                        $message .="Salam,";
                        $message .="\r\n";
                        $message .="Dinetkan";

                        $nomorhp = $this->gantiformat($user->whatsapp);

                        $owner = User::where('role', 'Owner')->first();
                        $_id = $owner->whatsapp;
                        $apiUrl = env('WHATSAPP_URL_NEW')."send-message/".$_id; //env('CACTI_ENDPOINT').'cacti/logout/'.$_id;
                        try {
                            $params = array(
                                "jid" => $nomorhp."@s.whatsapp.net",
                                "content" => array(
                                    "text" => $message
                                )
                            );
                            // Kirim POST request ke API eksternal
//                            Http::post($apiUrl, $params);

                        } catch (\Exception $e) {
                            DB::rollback();
                            return response()->json(['message' => 'Error creating: ' . $e->getMessage()], 500);
                        }
                    }
                }
            }
            DB::commit();
            return response()->json(['message' => 'Order Was Update and send Invoice To User'], 201);
        }catch (Exception $e){
            DB::rollback();
        }
        return response()->json(['message' => 'Order Was Update'], 201);
    }

    function gantiformat($nomorhp) {
        //Terlebih dahulu kita trim dl
        $nomorhp = trim($nomorhp);
        //bersihkan dari karakter yang tidak perlu
        $nomorhp = strip_tags($nomorhp);
        // Berishkan dari spasi
        $nomorhp= str_replace(" ","",$nomorhp);
        // bersihkan dari bentuk seperti  (022) 66677788
        $nomorhp= str_replace("(","",$nomorhp);
        // bersihkan dari format yang ada titik seperti 0811.222.333.4
        $nomorhp= str_replace(".","",$nomorhp);

        //cek apakah mengandung karakter + dan 0-9
        if(!preg_match('/[^+0-9]/',trim($nomorhp))){
            // cek apakah no hp karakter 1-3 adalah +62
            if(substr(trim($nomorhp), 0, 2)=='62'){
                $nomorhp= trim($nomorhp);
            }
            // cek apakah no hp karakter 1 adalah 0
            elseif(substr($nomorhp, 0, 1)=='0'){
                $nomorhp= '62'.substr($nomorhp, 1);
            }
        }
        return $nomorhp;
    }

    public function get_by_pelanggan(Request $request){
        if(!isset($request->id_pelanggan)){
            return response()->json(['message' => 'Parameter id_pelanggan required'], 500);
        }
//        if(!isset($request->bulan)){
//            return response()->json(['message' => 'Parameter bulan required'], 500);
//        }
//        if(!isset($request->tahun)){
//            return response()->json(['message' => 'Parameter tahun required'], 500);
//        }
        $user = UserDinetkan::where('dinetkan_user_id', $request->id_pelanggan)->first();
        $data = [];
        if($user){
            $company_name = "";
            $company = Company::where('group_id', $user->id)->first();
            if($company){
                $company_name = $company->name;
            }
            $invoices = AdminDinetkanInvoice::where('status', DinetkanInvoiceStatusEnum::UNPAID)
//            ->where('due_date','>=', Carbon::now())
                ->where('dinetkan_user_id', $request->id_pelanggan)
                ->with('admin')
//                ->whereMonth('period', $request->bulan) // Bulan: 07
//                ->whereYear('period', $request->tahun) // Tahun: 2025
                ->get();
            if(count($invoices) > 0){
                foreach ($invoices as $inv){
                    $data[] = [
                        'invoice_id' => $inv->id,
                        'no_invoice' => $inv->no_invoice,
                        'due_date' => $inv->due_date,
                        'item' => $inv->item,
                        'price' => $inv->price,
                        'ppn' => $inv->ppn,
                        'total_ppn' => $inv->total_ppn,
                        'price_adon' => $inv->price_adon,
                        'price_adon_monthly' => $inv->price_adon_monthly,
                        'total' => ($inv->price + $inv->total_ppn + $inv->price_adon + $inv->price_adon_monthly)

                    ];
                }
            }
        }
        $response = [
            'fullname' => $user->name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'id_pelanggan' => $request->id_pelanggan,
//            'bulan' => $request->bulan,
//            'tahun' => $request->tahun,
            'perusahaan' => $company_name,
            'data' => $data
        ];
        return response()->json($response, 200);
    }



    protected function get_payment_method()
    {
        $duitku = Mduitku::where('shortname', 'dinetkan')->first();
        // Set kode merchant anda
        $merchantCode = $duitku->id_merchant;
        // Set merchant key anda
        $apiKey = $duitku->api_key;
        // catatan: environtment untuk sandbox dan passport berbeda

        $datetime = date('Y-m-d H:i:s');
        $paymentAmount = 10000;
        $signature = hash('sha256', $merchantCode . $paymentAmount . $datetime . $apiKey);

        $params = array(
            'merchantcode' => $merchantCode,
            'amount' => $paymentAmount,
            'datetime' => $datetime,
            'signature' => $signature
        );
        $url = 'https://sandbox.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod';
        if (env('APP_ENV') == 'production') {
            $url = 'https://passport.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod';
        }
        $response = makeRequest($url, "POST", $params);
        $data = $response;
        $paymentMethod = [];
        $paymentMethod[] = 'Select Payment';
        if (isset($data['paymentFee'])) {
            $filteredVA = collect($data['paymentFee'])->filter(function ($item) {
                return Str::contains($item['paymentName'], ' VA');
            });

            // Contoh: ambil hanya paymentName-nya
            $paymentNames = $filteredVA->pluck('paymentName', 'paymentMethod');

            // Tampilkan
            foreach ($paymentNames as $key => $val) {
                $paymentMethod[$key] = [
                    'bank' => $val,
                    'panduan' => get_panduan($key)
                ];
            };
            return $paymentMethod;
        } else {
            return [];
        }
    }



    public function generate_va(Request $request, SiteDinetkanSettings $setting){
    $invoice = AdminDinetkanInvoice::where('id', $request->invoice_id)->first();
    // otc
    $amount_ppn_otc = 0;
    $gross_amount_otc = 0;
    if($invoice->price_otc > 0){
        if($invoice->ppn_otc > 0){
            $amount_ppn_otc = $invoice->price_otc * $invoice->ppn_otc / 100;
        }
        $gross_amount_otc = $invoice->price_otc + $amount_ppn_otc;
    }
    // otc
    $amount_discount = $invoice->price * $invoice->discount / 100;
    $amount_ppn      = ($invoice->price - $invoice->discount_coupon - $amount_discount) * $invoice->ppn / 100;
    $discount_coupon = $invoice->discount_coupon;
    //        $gross_amount    = $invoice->price + $this->config->getAdminFee() + $amount_ppn - $amount_discount - $invoice->discount_coupon;

    $gross_amount    = $invoice->price + $invoice->fee + $amount_ppn - $amount_discount - $invoice->discount_coupon;
    $gross_amount = $gross_amount + $gross_amount_otc;
    $itemDetails = [
        [
            'name'     => $invoice->item,
            'price'    => (int) $invoice->price,
            'quantity' => 1,
        ],
    ];

    if ($amount_ppn > 0) {
        $itemDetails[] = [
            'name'     => 'PPN',
            'price'    => (int) round($amount_ppn),
            'quantity' => 1,
        ];
    }

    if ($amount_discount > 0) {
        $itemDetails[] = [
            'name'     => 'Discount',
            'price'    => (int) -round($amount_discount),
            'quantity' => 1,
        ];
    }

    if ($discount_coupon > 0) {
        $itemDetails[] = [
            'name'     => 'Discount Promo',
            'price'    => (int) -round($discount_coupon),
            'quantity' => 1,
        ];
    }
    if ($invoice->fee > 0) {
        $itemDetails[] = [
            'name'     => 'Biaya Admin',
            'price'    => (int) $invoice->fee,
            'quantity' => 1,
        ];
    }

    $customerVaName = match (true) {
    $invoice instanceof Invoice => $invoice->member->full_name,
            $invoice instanceof AdminDinetkanInvoice => $invoice->admin->name,
            default => '',
        };
        $email = match (true) {
        $invoice instanceof Invoice => $invoice->member->email ?? '',
            $invoice instanceof AdminDinetkanInvoice => $invoice->admin->email ?? '',
            default => '',
        };
        $phoneNumber = match (true) {
        $invoice instanceof Invoice => $invoice->member->wa ?? '',
            $invoice instanceof AdminDinetkanInvoice => $invoice->admin->whatsapp ?? '',
            default => '',
        };

        $duitku = Mduitku::where('shortname', 'dinetkan')->first();
        $user = User::where('dinetkan_user_id', $invoice->dinetkan_user_id)->first();
        // true for sandbox mode
        $url = "https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry";

        if(env('APP_ENV') == 'production'){
            $url = "https://passport.duitku.com/webapi/api/merchant/v2/inquiry";
        }
        $timestamp = (int) round(microtime(true) * 1000);
        $merchantCode = $duitku->id_merchant;
        $paymentMethod = $request->payment_method;
        $signature = md5($merchantCode . $invoice->no_invoice . (int) round($gross_amount) . $duitku->api_key);
        $transaction = [
            'merchantcode'    => $merchantCode,
            'paymentMethod'   => $paymentMethod,
            'merchantOrderId' => $invoice->no_invoice,
            'paymentAmount'   => (int) round($gross_amount),
            'productDetails'  => $invoice->item,
            'additionalParam' => '',
            'merchantUserInfo' => '',
            'customerVaName'  => $customerVaName,
            'email'           => $email,
            'phoneNumber'     => $phoneNumber,
            'itemsDetails'    => $itemDetails,
            'customerDetails' => [
                'firstName'   => $customerVaName,
                'email'       => $email,
                'phoneNumber' => $phoneNumber,
            ],
            'callbackUrl'     => route('notification.admin.duitku_dinetkan'),
            'returnUrl'       => route('admin.invoice_dinetkan', $invoice->no_invoice),
            'expiryPeriod'    => 60 * 24, // 1440 minutes
            'signature'       => $signature
        ];

        $response = makeRequest($url, "POST", $transaction);
        if(isset($response['statusCode'])){
            if($response['statusCode'] == '00' && $response['vaNumber'] != ''){
                $data_update=[
                    'virtual_account' => $response['vaNumber'],
                    'bank' => $request->payment_method,
                    'bank_name' => $request->bank_name,
                    'reference' => $response['reference']
                ];
                $invoice->update($data_update);
                return [
                    'vaNumber' => $response['vaNumber'],
                    'bank_name' => $request->bank_name
                ];
            }
            return $response;
        }
        return $response;
    }
    public function generateSignatureDinetkan($timestampMs, $setting)
    {
        $merchantCode = $setting->duitku_merchant_code; //$this->config->getIdMerchant();
        $apiKey       = $setting->duitku_api_key; //$this->config->getApiKey();

        return hash_hmac('sha256', $merchantCode . $timestampMs, $apiKey);
    }

    public function get_by_invoice_id(Request $request){
        if(!isset($request->invoice_id)){
            return response()->json(['message' => 'Parameter invoice_id required'], 500);
        }
        $inv = AdminDinetkanInvoice::where('id', $request->invoice_id)
            ->with('admin')
            ->first();
        if($inv){
            $user = UserDinetkan::where('dinetkan_user_id', $inv->dinetkan_user_id)->first();
            $company_name = "";
            $company = Company::where('group_id', $user->id)->first();
            if($company){
                $company_name = $company->name;
            }
            $status_desc = "UNPAID";
            if($inv->status == DinetkanInvoiceStatusEnum::PAID->value){
                $status_desc = "PAID";
            }
            $data = [
                'invoice_id' => $inv->id,
                'no_invoice' => $inv->no_invoice,
                'due_date' => $inv->due_date,
                'item' => $inv->item,
                'price' => $inv->price,
                'ppn' => $inv->ppn,
                'total_ppn' => $inv->total_ppn,
                'price_adon' => $inv->price_adon,
                'price_adon_monthly' => $inv->price_adon_monthly,
                'total' => ($inv->price + $inv->total_ppn + $inv->price_adon + $inv->price_adon_monthly),
                'status' => $inv->status,
                'status_desc' => $status_desc
            ];
            $response = [
                'fullname' => $user->name,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'id_pelanggan' => $user->dinetkan_user_id,
    //            'bulan' => $request->bulan,
    //            'tahun' => $request->tahun,
                'perusahaan' => $company_name,
                'data' => $data
            ];
            return response()->json($response, 200);
        } else{
            return response()->json(['message' => 'Invoice not found'], 500);
        }
}

function generateUniqueServiceId()
{
    do {
        // 1. Generate nomor random (bisa angka saja atau kombinasi)
        $prefix = "1".Carbon::now()->format('Ym');
        $randomNumber = mt_rand(1111, 9999); // Contoh: 6 digit angka
        $service_id = $prefix.$randomNumber;
        // 2. Cek apakah service_id tersebut sudah ada
        $exists = MappingUserLicense::where('service_id', $service_id)->exists();

    } while ($exists); // Ulangi jika sudah ada

    return $service_id; // Kembalikan jika unik
}

function get_service_detail($service_id){

    $vlan = null;//get_vlan_mikrotik();
    $this->cacti_logout();
    $this->cacti_login();
    $mapping = MappingUserLicense::where('service_id', $service_id)->with('service')->first();
    $service_detail = ServiceDetail::where('service_id', $service_id)->first();

    $data_service = [
        'service_id' => $service_id,
    ];
    if (!$service_detail) {
        $service_detail = ServiceDetail::create($data_service);
    }

    $provinces = [];
    $regencies = [];
    $districts = [];
    $villages = [];$regencies = [];
    if ($service_detail->province_id) {
        $regencies = Regencies::where('province_id', $service_detail->province_id)->get();
    }
    $districts = [];
    if ($service_detail->regency_id) {
        $districts = Districts::where('regency_id', $service_detail->regency_id)->get();
    }
    $villages = [];
    if ($service_detail->district_id) {
        $villages = Villages::where('district_id', $service_detail->district_id)->get();
    }
    $docType = DocType::all();
    $listDoc = UserDoc::with('docType')->where('service_id', $service_id)->get();

    $provinces = Province::query()
        ->orderBy('name', 'asc')
        ->get();
    $userdinetkanGraph = UserDinetkanGraph::where('dinetkan_user_id',$mapping->dinetkan_user_id)->where('service_id', $service_id)->get();
    $pop = MasterPop::get();
    $tree = $this->get_tree_mrtg();
    $metro = MasterMetro::all();
    $mikrotik = MasterMikrotik::all();
    $mikrotik_detail = null;
    if(isset($service_detail->id_mikrotik)){
        $mikrotik_detail = MasterMikrotik::where('id', $service_detail->id_mikrotik)->first();
    }
    $servicelibre = ServiceLibre::query()->where('service_id', $service_id)->get();
    $vlannms = getVlansFromNms();
    $devices = getDevicesFromNms();
    $wag = UserWhatsappGroup::where('user_id', multi_auth()->id)->get();
    return view('backend.dinetkan.invoice_dinetkan.service_detail',
        compact('docType', 'mapping', 'service_detail', 'listDoc'
            ,'provinces','regencies','districts','villages','userdinetkanGraph','pop','tree','metro','vlan', 'mikrotik','mikrotik_detail', 'wag'
            ,'vlannms','servicelibre','devices'));
}

    public function update_service_detail(Request $request)
    {
        try {
            $service = ServiceDetail::where('service_id', $request->service_id)->first();
            $data = [
                'service_id' => $request->service_id,
                'email' => $request->email,
                'whatsapp' => $request->whatsapp,

                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'id_card' => $request->id_card,
                'npwp' => $request->npwp,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'province_id' => $request->province_id,
                'regency_id' => $request->regency_id,
                'district_id' => $request->district_id,
                'village_id' => $request->village_id,
                'address' => $request->address,
            ];
            if ($service) {
                $service->update($data);
            }
            if (!$service) {
                ServiceDetail::create($data);
            }
            return response()->json(['message' => 'Service Update successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error Service Update: ' . $e->getMessage()], 500);
        }
    }



    public function get_tree_node_mrtg($id){
    // step 2
    try {
        $params = array(
            "action" =>"get_node",
            "tree_id" => "0",
            "id" => $id
        );
        $_id = Str::lower(Str::replace(' ', '', multi_auth()->name));
        $apiUrl = env('CACTI_ENDPOINT').'/cacti/graph_view/'.$_id.'?' . urldecode(http_build_query($params)) ;
        // Kirim POST request ke API eksternal
        $response = Http::timeout(10)->get($apiUrl);

        // Periksa apakah request berhasil
        //            print_r($response->json());exit;
        if ($response->successful()) {
            $data = $response->json();
            return $data ?? null;
        }
    } catch (\Exception $e) {
        return null;
    }

    return null;
}


    protected function cacti_logout(){
        //        http://103.184.122.170/api/cacti/logout/:_id
        $_id = Str::lower(Str::replace(' ', '', multi_auth()->name));
        $apiUrl = env('CACTI_ENDPOINT').'cacti/logout/'.$_id;
        try {
            // Kirim POST request ke API eksternal
            $response = Http::timeout(10)->get($apiUrl);
            // Periksa apakah request berhasil
            if ($response->successful()) {
                $data = $response->json();
                return $data['success'] ?? null;
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    public function mikrotik_update_service_detail(Request $request)
    {
        try {
            $service = ServiceDetail::where('service_id', $request->service_id)->first();
            $vlan = explode("|", $request->id_vlan);
            $data = [
                'service_id' => $request->service_id,
                'id_mikrotik' => $request->id_mikrotik,
                'vlan_id' => $vlan[0],
                'vlan_name' => $vlan[1],
            ];
            if ($service) {
                $service->update($data);
            }
            if (!$service) {
                $service = ServiceDetail::create($data);
            }
//            return redirect()->back()->with('success', 'Data Updated successfully');
            return response()->json(['message' => 'Api Mikrotik update successfuly'], 201);
        } catch (\Exception $e) {
//            return redirect()->back()->with('success', 'Data Updated error '. $e->getMessage());
            return response()->json(['message' => 'Api Mikrotik update error '. $e->getMessage()], 500);
        }
    }



    public function edit_order($id, Request $request, SiteDinetkanSettings $settings)
    {
        $licenses = LicenseDinetkan::all();
        $categories = CategoryLicenseDinetkan::get();
        $resellers = UserDinetkan::where('is_dinetkan',1)->with('company')->get();
        $statuses = ServiceStatusEnum::getStatuses();
        $progress = ServiceStatusEnum::PROGRESS->value;
        $mapping = MappingUserLicense::where('id', $id)->first();
        $mitras = Mitra::where('shortname', multi_auth()->shortname)->where('status', 1)->select('id', 'name', 'id_mitra')->get();

            return view('backend.dinetkan.invoice_dinetkan.edit_order',
                compact(
                    'licenses',
                    'categories','resellers',
                    'statuses','progress', 'settings',
                    'mitras',
                    'mapping'
                ));
    }

    public function update_active_graph(Request $request){
        try {
            $service = ServiceDetail::where('service_id', $request->service_id)->first();
            Log::info($service);
                $data = [
                    'graph_type' => $request->graph_type
                ];
                if ($service) {
                    Log::info($data);
                    $service->update($data);
                    return response()->json(['message' => 'Chart update successfuly'], 201);
                }
                if (!$service) {
                    $service = ServiceDetail::create($data);
                    return response()->json(['message' => 'Chart Create successfuly'], 201);
                }

        } catch (\Exception $e) {
            return response()->json(['message' => 'Chart update error '. $e->getMessage()], 500);
        }
    }

    function update_service_id(){

        $cekmapping = MappingUserLicense::query()->where('service_id', null)
            ->orWhere('service_id',0)->get();
        foreach ($cekmapping as $row){
            $service_id = $this->generateUniqueServiceId();
            $row->update(['service_id' => $service_id]);
        }
        return response()->json($cekmapping, 201);
    }


    public function by_license(Request $request, $license_id)
{
//        ServiceStatusEnum
    $user = UserDinetkan::where('dinetkan_user_id', $request->dinetkan_user_id)->first();
    $cekmapping = MappingUserLicense::where('dinetkan_user_id', $request->dinetkan_user_id)->where('license_id', $license_id)->where('status', 1)->first();
    if($cekmapping != null && $request->is_edit == true && $cekmapping->id == $request->id_mapping){
        return response()->json(
            [
                'success' => false,
                'mulai' => "",
                'akhir' => "",
                'hari_pakai' => "",
                'harga_asli' => "",
                'harga_prorate' => "",
                'ppn' => "",
                'message' => 'User '.$user->name.' memiliki service tersebut dan aktif'
            ]
        );
    }
    $prorate = hitungProrate($request->payment_method, $request->active_date, $request->payment_date, $license_id, $request->prorata);
    return response()->json($prorate);
}

    public function by_id_mitra(Request $request){
        $user = User::query()->where('dinetkan_user_id', $request->dinetkan_user_id)->first();
        $mitra = Mitra::where('id_mitra', $request->id_mitra)->where('status', 1)->select('id', 'name', 'id_mitra')->first();
        if($user){
            if($user->id_mitra_sales!=null && $user->id_mitra_sales!= '' && $user->id_mitra_sales != $request->id_mitra){
                return response()->json(['success' => false, 'message' => $user->username." tidak bisa di sign dengan sales ".$mitra->name], 201);
            }
            if($user->id_mitra_sales == $request->id_mitra){
                return response()->json(['success' => true, 'message' => ""], 201);
            }
        }
        return response()->json(['success' => true, 'message' => ""], 201);
    }

    public function get_user($id){
        $user = UserDinetkan::query()->where('dinetkan_user_id', $id)->first();
        return response()->json($user,201);
    }

    public function libre_update_service_detail(Request $request)
    {
        try {
            $ifName = $request->ifName;
            $hostname = $request->hostname;
            $servicelibre = ServiceLibre::where('service_id', $request->service_id)->where('ifName', $ifName)->where('hostname', $hostname)->first();
            $data = [
                'service_id' => $request->service_id,
                'ifName' => $ifName,
                'hostname' => $hostname,
            ];
            if ($servicelibre) {
                $servicelibre->update($data);
            }
            if (!$servicelibre) {
                $servicelibre = ServiceLibre::create($data);
            }
    //            return redirect()->back()->with('success', 'Data Updated successfully');
            return response()->json(['message' => 'Api Libre update successfuly'], 201);
        } catch (\Exception $e) {
    //            return redirect()->back()->with('success', 'Data Updated error '. $e->getMessage());
            return response()->json(['message' => 'Api Libre update error '. $e->getMessage()], 500);
        }
    }

    public function get_ifname($hostname){
        return getIfnameFromNms($hostname);
    }

    public function delete_ifname($id){
        $libre = ServiceLibre::query()->where('id', $id)->first();
        $libre->delete();
        return response()->json(['message' => 'Api Libre update successfuly'], 201);
    }

    public function delete_service_doc($id){
        $doc = UserDoc::query()->where('id', $id)->first();
        $doc->delete();
        return response()->json(['message' => 'Documen berhasil dihapus'], 201);
    }
    public function update_service_detail_2(Request $request)
    {
        try {
            $service = ServiceDetail::where('service_id', $request->service_id)->first();
            $data = [
                    'metro_id' => $request->metro_id,
                    'vendor' => $request->vendor,
                    'ip_prefix' => $request->ip_prefix,
                    'pop_id' => $request->pop_id,
                    'vlan' => $request->vlan_text,
                    'sn_modem' => $request->sn_modem
            ];
            if ($service) {
                $service->update($data);
            }
            if (!$service) {
                ServiceDetail::create($data);
            }
            return response()->json(['message' => 'Service Update successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error Service Update: ' . $e->getMessage()], 500);
        }
    }

    public function upgrade($id)
    {
        $licenses = LicenseDinetkan::all();
        $categories = CategoryLicenseDinetkan::get();
        $resellers = UserDinetkan::where('is_dinetkan',1)->with('company')->get();
        $statuses = ServiceStatusEnum::getStatuses();
        $progress = ServiceStatusEnum::PROGRESS->value;
        $mitras = Mitra::where('shortname', multi_auth()->shortname)->where('status', 1)->select('id', 'name', 'id_mitra')->get();
        $mapping = MappingUserLicense::query()->where('service_id', $id)->first();
        $curr_lic = LicenseDinetkan::query()->where('id', $mapping->license_id)->first();
            return view('backend.dinetkan.invoice_dinetkan.upgrade',
                compact(
                    'licenses',
                    'categories','resellers',
                    'statuses','progress',
                    'mitras', 'mapping','curr_lic'
                ));
    }

    public function check_price(Request $request){
        $current_mapping = MappingUserLicense::query()->where('service_id', $request->service_id)->first();
        $curr_lic = LicenseDinetkan::query()->where('id', $current_mapping->license_id)->first();
        $new_lic = LicenseDinetkan::query()->where('id', $request->license_dinetkan_id)->first();
        $new_price = $this->calculateProrate($curr_lic->price, $new_lic->price, $current_mapping->due_date, $request->upgrade_date, $request->jenis);
        return response()->json(['message' => 'Check Harga Ditemukan', 'data' => $new_price],201);
    }

    public function calculateProrate_new(int $oldPrice, int $newPrice, string $dueDate, ?string $upgradeDate = null): array
    {

    }
    /**
     * Hitung tagihan upgrade paket prorata
     *
     * @param int $oldPrice harga paket lama per bulan
     * @param int $newPrice harga paket baru per bulan
     * @param string $dueDate tanggal jatuh tempo (format Y-m-d)
     * @param string|null $upgradeDate tanggal upgrade (default = today)
     * @return array
     */
    public function calculateProrate(int $oldPrice, int $newPrice, string $dueDate, ?string $upgradeDate = null, $jenis = 1): array
    {
        if($jenis == 1){
            $upgradeDate = $upgradeDate ? Carbon::parse($upgradeDate) : now();
            $dueDate = Carbon::parse($dueDate);

            // start date = 30 hari sebelum due date
//            $startDate = $dueDate->copy()->submont(30);
            $startDate = Carbon::parse($dueDate)->addMonthsWithNoOverflow(-1);

            // jumlah hari dalam periode
            $totalDays = $dueDate->diffInDays($startDate);

            // hitung hari terpakai & sisa
            $usedDays = $upgradeDate->diffInDays($startDate);
            $remainingDays = $totalDays - $usedDays;

            // harga harian
            $oldDaily = $oldPrice / $totalDays;
            $newDaily = $newPrice / $totalDays;

            // biaya prorata
            $costOldUsed = $oldDaily * $usedDays;
            $costNewRemaining = $newDaily * $remainingDays;

            // rumus prorata
            $totalProrate = $costOldUsed + $costNewRemaining - $oldPrice;

            return [
                'start_date' => $startDate->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'upgrade_date' => $upgradeDate->toDateString(),
                'total_days' => $totalDays,
                'used_days' => $usedDays < 0 ? ($usedDays * -1) : $usedDays,
                'remaining_days' => $remainingDays < 0 ? ($remainingDays * -1) : $remainingDays,
                'old_daily' => round($oldDaily),
                'new_daily' => round($newDaily),
                'cost_old_used' => round($costOldUsed),
                'cost_new_remaining' => round($costNewRemaining),
                'total_prorate' => round($totalProrate),
            ];
        }

        if($jenis == 2){
            $upgradeDate = $upgradeDate ? Carbon::parse($upgradeDate) : now();
            $dueDate = Carbon::parse($dueDate);

            // start date = 30 hari sebelum due date
            $startDate = Carbon::parse($dueDate)->addMonthsWithNoOverflow(-1);

            // jumlah hari dalam periode
            $totalDays = $dueDate->diffInDays($startDate);

            // hitung hari terpakai & sisa
            $usedDays = $upgradeDate->diffInDays($startDate);
            $remainingDays = $totalDays - $usedDays;

            // harga harian
            $oldDaily = $oldPrice / $totalDays;
            $newDaily = $newPrice / $totalDays;

            // biaya prorata
            $costOldUsed = $oldDaily * $usedDays;
            $costNewRemaining = $newDaily * $remainingDays;

            // rumus prorata
            $oldPricex = $oldPrice - $costOldUsed;
            $totalProrate = $newPrice - $oldPricex;

            return [
                'start_date' => $startDate->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'upgrade_date' => $upgradeDate->toDateString(),
                'total_days' => $totalDays,
                'used_days' => $usedDays < 0 ? ($usedDays * -1) : $usedDays,
                'remaining_days' => $remainingDays < 0 ? ($remainingDays * -1) : $remainingDays,
                'old_daily' => round($oldDaily),
                'new_daily' => round($newDaily),
                'cost_old_used' => round($costOldUsed),
                'cost_new_remaining' => round($costNewRemaining),
                'total_prorate' => round($totalProrate),
            ];
        }
    }

    public function create_upgrade(Request $request){
        DB::beginTransaction();
        try{
            $license = LicenseDinetkan::where('id', $request->license_dinetkan_id)->first();
            $cekmapping = MappingUserLicense::where('service_id', $request->service_id)->first();
            $userdinetkan = UserDinetkan::where('dinetkan_user_id', $cekmapping->dinetkan_user_id)->first();
            if($cekmapping->payment_method == 'prabayar'){
                $type = "prabayar";
                $periodStart = Carbon::parse($request->upgrade_date);
                $periodEnd = Carbon::parse($cekmapping->due_date)->format('Y-m-d');
                $subscribe = Carbon::parse($periodStart)->format('d/m/Y') . ' s/d ' . Carbon::parse($periodEnd)->format('d/m/Y');
            }
            if($cekmapping->payment_method == 'pascabayar'){
                $type = "pascabayar";
                $periodStart = Carbon::parse($request->upgrade_date)->format('Y-m-d');
                $periodEnd = Carbon::parse($cekmapping->due_date)->format('Y-m-d');
                $subscribe = Carbon::parse($periodStart)->format('d/m/Y') . ' s/d ' . Carbon::parse($periodEnd)->format('d/m/Y');
            }
            if($cekmapping){
                $order = $cekmapping;
                $cekmapping->update(
                    [
                        'license_id' => $license->id,
                        'category_id' => $license->category_id,
                        'active_date' => $request->upgrade_date,
                    ]);
                if($request->jenis == 2){
                    $cekmapping->update([
                            'due_date' => Carbon::parse($request->upgrade_date)->addMonthsWithNoOverflow(1),
                        ]);
                }
            }
            $cekadons = MappingAdons::query()->where('id_mapping',$order->id)->get();
            if($cekadons){
                foreach ($cekadons as $ckm){
                    $ckm->delete();
                }
            }
            $desc                   = $request->desc;
            $ppn                    = $request->ppn;
            $monthly                = $request->monthly;
            $qty                    = $request->qty;
            $price                  = $request->price;
            $total_price_ad         = 0;
            $total_price_ad_monthly = 0;
            $data = [];
            if(isset($request->desc)){
                if(count($desc) > 0){
                    for ($i = 0; $i < count($desc); $i++) {
                        $orderadons = MappingAdons::create(
                            [
                                'id_mapping' => $order->id,
                                'description' => $desc[$i],
                                'ppn' => $ppn[$i],
                                'monthly' => $monthly[$i],
                                'qty' => $qty[$i],
                                'price' => $price[$i]
                            ]);
                        $totalPpnAd = 0;
                        if($ppn[$i] > 0){
                            $totalPpnAd = $ppn[$i] * ($qty[$i] * $price[$i]) / 100;
                        }
                        $total_price_ad = $total_price_ad + (($qty[$i] * $price[$i]) + $totalPpnAd);

                        if($monthly[$i] == "Yes"){
                            $total_price_ad_monthly = $total_price_ad_monthly + (($qty[$i] * $price[$i]) + $totalPpnAd);
                        }
                    }
                }
            }
            $invoice = null;
            $license = $this->licenseDinetkanRepo->findById($license->id);
            if($license){
                $total_ppn_license = 0;
                if($request->ppn > 0){
                    $total_ppn_license = $request->total_prorate * $request->ppn / 100;
                }
                $noInvoice = date('m') . rand(0000000, 9999999);

                $invoice = new AdminDinetkanInvoice([
                    'group_id'              => $userdinetkan->id,
                    'itemable_id'           => $license->id,
                    'itemable_type'         => LicenseDinetkan::class,
                    'no_invoice'            => $noInvoice,
                    'item'                  => "Service : ". $license->name, //($isRenewal ? 'License Renewal: ' : '') . $license->name,
                    'price'                 => (int)$request->total_prorate,
                    'price_adon'            => $total_price_ad,
                    'price_adon_monthly'    => $total_price_ad_monthly,
                    'ppn'                   => $request->ppn,
                    'total_ppn'             => $total_ppn_license,
                    'fee'                   => 0,
                    'discount'              => 0,
                    'discount_coupon'       => 0,//$priceData->discountCoupon,
                    'invoice_date'          => Carbon::now(),
                    'due_date'              => $periodEnd, //$dueDate->format('Y-m-d'),
                    'period'                => $periodEnd,
                    'subscribe'             => $subscribe,
                    'payment_type'          => $type,
                    'billing_period'        => $cekmapping->payment_siklus,
                    'payment_url'           => route('admin.invoice_dinetkan', $noInvoice),
                    'status'                => DinetkanInvoiceStatusEnum::UNPAID,
                    'dinetkan_user_id'      => $userdinetkan->dinetkan_user_id,
                    'id_mapping'            => $order->id,
                    'is_upgrade'            => 1
                ]);
                $this->adminDinetkanInvoiceRepo->save($invoice);
            }
            DB::commit();
            return response()->json(['message' => 'Berhasil upgrade service'], 201);
        }catch (\Exception $e){
            DB::rollBack();
            return response()->json(['message' => 'Gagal upgrade service '.$e->getMessage()], 500);
        }
    }

}
