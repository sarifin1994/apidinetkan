<?php

namespace App\Http\Controllers\Admin\Account;

use App\Models\AdminDinetkanInvoice;
use App\Models\BillingService;
use App\Models\MappingAdons;
use App\Models\MappingUserLicense;
use App\Models\MasterMikrotik;
use App\Models\ServiceDetail;
use App\Models\Setting\Mduitku;
use App\Models\User;
use App\Enums\EponDeviceEnum;
use App\Settings\SiteDinetkanSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Payments\Repositories\Contracts\AdminDinetkanInvoiceRepositoryInterface;
use Modules\Payments\Repositories\Contracts\LicenseDinetkanRepositoryInterface;
use Modules\Payments\Services\AdminDinetkanPaymentService;
use Modules\Payments\Services\DuitkuService;
use Modules\Payments\Services\TripayService;
use Modules\Payments\ValueObjects\DuitkuConfig;
use Modules\Payments\ValueObjects\TripayConfig;
use App\Enums\DinetkanInvoiceStatusEnum;
use RouterOS\Client;
use RouterOS\Query;

final class LicenseDinetkanController extends Controller
{
    public function __construct(
        private LicenseDinetkanRepositoryInterface $licenseRepo,
        private AdminDinetkanInvoiceRepositoryInterface $adminInvoiceRepo,
        private TripayService $tripayService,
        private DuitkuService $duitkuService,
        private AdminDinetkanPaymentService $adminDinetkanPaymentService,
        private SiteDinetkanSettings $settings,
    ) {
        $this->tripayService->setConfig(new TripayConfig(
            merchantCode: $this->settings->tripay_merchant_code,
            apiKey: $this->settings->tripay_api_key,
            privateKey: $this->settings->tripay_private_key,
            isProduction: !$this->settings->tripay_sandbox,
        ));
        $this->duitkuService->setConfig(new DuitkuConfig(
            idMerchant: $this->settings->duitku_merchant_code,
            apiKey: $this->settings->duitku_api_key,
            isProduction: !$this->settings->duitku_sandbox,
        ));
//        $this->adminDinetkanPaymentService->setTripayService($this->tripayService);
        $this->adminDinetkanPaymentService->setDuitkuService($this->duitkuService);
    }

public function index()
{
    $licenses = [];
    foreach ($this->licenseRepo->all() as $license) {
        $oltModels = $license->olt_models;
        $oltModels = $oltModels ? EponDeviceEnum::getSupportedModels($license) : [];
        $oltModels = $oltModels ? collect($oltModels)->map(fn(EponDeviceEnum $model) => $model->label())->toArray() : [];

            $licenses[] = [
                'title'        => $license->name,
                'price'        => $license->price_plan,
                'features'     => [
                    "Up to {$license->limit_nas} NAS",
                    "Up to {$license->limit_pppoe} PPPoE Users",
                    "Up to {$license->limit_hs} Hotspot Users",
                    "Up to {$license->limit_vpn} VPN",
                    "Up to {$license->limit_vpn_remote} VPN Remote",
                    "Up to {$license->limit_user} Staff Users",
                    ["Up to {$license->olt_epon_limit} EPON OLT", $license->olt_epon, $license->olt_epon],
                    ["Up to {$license->olt_gpon_limit} GPON OLT", $license->olt_gpon, $license->olt_gpon],
                    ["EPON OLT Management", $license->olt_epon],
                    ["GPON OLT Management", $license->olt_gpon],
                    ["Supported OLT Models", $oltModels, $oltModels],
                    ["WhatsApp Gateway", $license->whatsapp],
                    'Cluster Radius Server',
                    'Cluster Database System',
                    ['Midtrans / Tripay / Duitku / Xendit Payment Gateway', $license->payment_gateway],
                ],
                'url'          => $license->id,
                'button_text'  => "Choose {$license->name}",
            ];
        }

    return view('backend.accounts.licensing_dinetkan.index', compact('licenses'));
}

public function order(Request $request, string $id,$couponCode = "")
{

    $license = $this->licenseRepo->findById((int)$id);
    abort_unless($license, 404);
    /** @var User $user */
    $user = $request->user();
    $currentLicense = $user->license_id ? $this->licenseRepo->findById($user->license_id) : null;

    // Disallow downgrade
    if ($currentLicense && $currentLicense->price > $license->price) {
        return back()->with('error', 'You cannot downgrade your license. Please contact support.');
    }

    // Disallow ordering the same license
    if ($currentLicense && $currentLicense->id === $license->id) {
        return back()->with('error', 'You already have this license.');
    }

    // Disallow ordering the same license exceeds the max buy limit
    $maxBuyLimit = $license->max_buy;
    $isExceeds = $maxBuyLimit > 0 ? $this->adminInvoiceRepo->countPaidInvoices($user, $license) >= $maxBuyLimit : false;

    if ($isExceeds) {
        return back()->with('error', 'You have reached the maximum purchase limit for this license.');
    }
    $priceData = $this->adminDinetkanPaymentService->calculateLicenseOrderPrice($license, $user);
    $priceDataNew = $priceData;
    $cekPriceDataNew = $this->adminDinetkanPaymentService->get_promo_coupon($user,$license,$priceData, $couponCode);
    if($cekPriceDataNew['success'] == false){
        return redirect()->route('admin.account.licensing.order',$id)->with('warning', $cekPriceDataNew['messages']);
    }
    if($cekPriceDataNew['success'] == true){
        $priceDataNew = $cekPriceDataNew['priceData'];
    }
    return view('backend.accounts.licensing_dinetkan.order', [
        'license' => $license,
        'currentLicense' => $currentLicense,
        'priceData' => $priceDataNew,
        'couponCode' => $couponCode
    ]);
}

public function placeOrder(Request $request, int $licenseId)
{
    /** @var User $user */
    $user = $request->user();
    $license = $this->licenseRepo->findById($licenseId);
    abort_unless($license, 404);

    $invoice = $this->adminDinetkanPaymentService->createLicenseInvoice($license, $user, $request->couponCode);

    if ($invoice->price === 0) {
        $this->adminDinetkanPaymentService->markInvoiceAsPaid($invoice);
        return redirect()->route('admin.account.licensing.thank-you', $invoice->no_invoice);
    }

    return redirect()->route('admin.invoice_dinetkan', $invoice->no_invoice);
}

public function invoice(Request $request, string $no_invoice)
{
    $invoice = AdminDinetkanInvoice::where('no_invoice', $no_invoice)->first();// $this->adminInvoiceRepo->findByNoInvoice($no_invoice);
    abort_unless($invoice, 404);

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

    $mapping = MappingUserLicense::where('id', $invoice->id_mapping)->first();
    $adons = MappingAdons::where('id_mapping', $mapping->id)->get();
    $paymentMethod = $this->get_payment_method();
    $panduan = get_panduan($invoice->bank);
//    print_r($panduan);exit;
    return view('backend.accounts.licensing_dinetkan.invoice', [
        'activeGateway' => $activeGateway,
        'invoice' => $invoice,
        'priceData' => $priceData,
        'config' => $config,
        'settings' => $this->settings,
        'adons' => $adons,
        'total_ppn_ad' => 0,
        'paymentMethod' => $paymentMethod,
        'panduan' => $panduan
    ]);
}


    public function generate_va_api(Request $request, SiteDinetkanSettings $setting){
        if(isset($request->api_key_ext)){
            if(env('API_KEY_EXT') == $request->api_key_ext){
                $generate_va = $this->generate_va($request,$setting);
                return [
                    'status' => true,
                    'message' => 'Virtual Account Generated',
                    'data' => $generate_va,
                    'panduan' => get_panduan($request->payment_method)
                ];
            } else{
                return [
                    'status' => false,
                    'message' => 'Wrong api key',
                    'data' => []
                ];
            }
        }else{
            return [
                'status' => false,
                'message' => 'Wrong api key',
                'data' => []
            ];
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
        $gross_amount = $gross_amount  + $invoice->price_adon  + $invoice->price_adon_monthly;
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

//        Log::info($transaction);
        $response = makeRequest($url, "POST", $transaction);
//        Log::info($response);
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
                    'bank_name' => $request->bank_name,
                    'panduan' => get_panduan($request->payment_method)
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


public function invoice_pdf(Request $request, string $no_invoice)
{
    $invoice = $this->adminInvoiceRepo->findByNoInvoice($no_invoice);

    abort_unless($invoice, 404);

    $activeGateway = $this->settings->active_gateway;
    $priceData = $this->adminDinetkanPaymentService->calculateLicenseOrderPrice($invoice->itemable, $invoice->admin, $invoice);

    $config = match ($activeGateway) {
    'tripay' => new TripayConfig(
    merchantCode: $this->settings->tripay_merchant_code,
                    apiKey: $this->settings->tripay_api_key,
                    privateKey: $this->settings->tripay_private_key,
                    isProduction: !$this->settings->tripay_sandbox,
                ),
                'duitku' => new DuitkuConfig(
    idMerchant: $this->settings->duitku_merchant_code,
                    apiKey: $this->settings->duitku_api_key,
                    isProduction: !$this->settings->duitku_sandbox,
                ),
                default => null,
            };

            $tripayMethods = $activeGateway === 'tripay' ? $this->tripayService->getPaymentMethods($priceData) : [];
            // Render PDF dari Blade
            $settings = $this->settings;
            $pdf = Pdf::loadView('backend.accounts.licensing_dinetkan.invoice_pdf',
                compact(
                    'invoice',
                    'priceData',
                    'settings'
                ))->setPaper('a4', 'landscape');

            // Simpan ke storage sementara
            $pdfPath = storage_path("app/invoice/invoice_{$invoice->no_invoice}.pdf");
            $pdf->save($pdfPath);
        }

public function pay(Request $request, string $no_invoice)
{
//        if(){
//            $this->adminDinetkanPaymentService->markInvoiceAsPaid($invoice);
//        }
    $request->validate(['payment_method' => 'nullable|string']);
    $selectedMethod = $request->input('payment_method');

    $invoice = $this->adminInvoiceRepo->findByNoInvoice($no_invoice);
    abort_unless($invoice, 404);

    $priceData = $this->adminDinetkanPaymentService->calculateLicenseOrderPrice($invoice->itemable, $invoice->admin, $invoice);
//        $priceDataNew = $this->adminDinetkanPaymentService->get_promo_coupon($invoice->admin,$invoice->itemable,$priceData, $invoice->couponname);
    $activeGateway = $this->settings->active_gateway;

    if ($activeGateway === 'tripay' && !$selectedMethod) {
        return back()->with('error', 'Please select a payment method');
    }

    if ($activeGateway === 'tripay') {
        $validMethods = array_map(fn($method) => $method->code, $this->tripayService->getPaymentMethods($priceData));
            if (!in_array($selectedMethod, $validMethods, true)) {
                return back()->with('error', 'Invalid payment method');
            }
        }

    $config = match ($activeGateway) {
    'tripay' => new TripayConfig(
    merchantCode: $this->settings->tripay_merchant_code,
                apiKey: $this->settings->tripay_api_key,
                privateKey: $this->settings->tripay_private_key,
                isProduction: !$this->settings->tripay_sandbox,
            ),
            'duitku' => new DuitkuConfig(
    idMerchant: $this->settings->duitku_merchant_code,
                apiKey: $this->settings->duitku_api_key,
                isProduction: !$this->settings->duitku_sandbox,
            ),
            default => null,
        };

        if (!$config) {
            return back()->with('error', 'Invalid payment gateway');
        }

        $response = match ($activeGateway) {
        'tripay' => $this->adminDinetkanPaymentService->createTripayTransaction($invoice, $selectedMethod),
            'duitku' => $this->adminDinetkanPaymentService->createDuitkuTransaction($invoice),
            default => null,
        };

        $paymentUrl = match ($activeGateway) {
        'tripay' => $response['data']['checkout_url'],
            'duitku' => $response,
            default => null,
        };

        if ($response && $paymentUrl) {
            return response()->json(['url' => $paymentUrl]);
        }

        return back()->with('error', 'Failed to initiate payment with Tripay.');
    }

public function tripayNotification(Request $request)
{
    $requestData = $request->all();
    $signature = $request->header('X-Callback-Signature');

    if (!$this->tripayService->validateSignature($requestData, $signature)) {
        return response()->json(['success' => false, 'message' => 'Invalid signature'], 403);
    }

    $handledInvoice = $this->adminDinetkanPaymentService->handleTripayNotification($request->all(), $request->header('X-Callback-Signature'));

    if ($handledInvoice) {
        return response()->json(['success' => true, 'message' => 'Invoice successfully paid and processed']);
    }

    return response()->json(['success' => false, 'message' => 'Transaction status not handled'], 400);
}

public function duitkuNotification(Request $request)
{
    $handledInvoice = $this->adminDinetkanPaymentService->handleDuitkuNotification($request->all());

    if ($handledInvoice) {
        send_faktur_inv($handledInvoice->no_invoice,$this->settings,'lunas');
        return response()->json(['success' => true, 'message' => 'Invoice successfully paid and processed']);
    }

    return response()->json(['success' => false, 'message' => 'Transaction status not handled'], 400);
}

public function thankYou(string $invoiceNo)
{
    $invoice = $this->adminInvoiceRepo->findByNoInvoice($invoiceNo);

    return view('backend.accounts.licensing_dinetkan.thank-you', compact('invoice'));
}

public function search(Request $request)
{
    $invoice = [];
    $user_id = "";
    if ($request->dinetkan_user_id) {
        $user_id = $request->dinetkan_user_id;
        $invoice = AdminDinetkanInvoice::query()
            ->where('dinetkan_user_id', $request->dinetkan_user_id)
            ->where('status', DinetkanInvoiceStatusEnum::UNPAID)->get();
    }
    return view('backend.accounts.licensing_dinetkan.search', [
        'invoice' => $invoice,
        'user_id' => $user_id
    ]);
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
                $paymentMethod[$key] = $val;
            };
            return $paymentMethod;
        } else {
            return [];
        }
    }
}
