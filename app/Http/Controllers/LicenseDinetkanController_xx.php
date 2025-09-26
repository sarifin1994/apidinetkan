<?php

namespace App\Http\Controllers;

use App\Models\AdminDinetkanInvoice;
use App\Models\User;
use App\Enums\EponDeviceEnum;
use App\Settings\SiteDinetkanSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Payments\Repositories\Contracts\AdminDinetkanInvoiceRepositoryInterface;
use Modules\Payments\Repositories\Contracts\LicenseDinetkanRepositoryInterface;
use Modules\Payments\Services\AdminDinetkanPaymentService;
use Modules\Payments\Services\DuitkuService;
use Modules\Payments\Services\TripayService;
use Modules\Payments\ValueObjects\DuitkuConfig;
use Modules\Payments\ValueObjects\TripayConfig;
use App\Enums\DinetkanInvoiceStatusEnum;

final class LicenseDinetkanControllerXx extends Controller
{
    public function __construct(
        private LicenseDinetkanRepositoryInterface $licenseRepo,
        private AdminDinetkanInvoiceRepositoryInterface $adminInvoiceRepo,
        private TripayService $tripayService,
        private DuitkuService $duitkuService,
        private AdminDinetkanPaymentService $adminPaymentService,
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
        $this->adminPaymentService->setTripayService($this->tripayService);
        $this->adminPaymentService->setDuitkuService($this->duitkuService);
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

    return view('accounts.licensing_dinetkan.index', compact('licenses'));
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
    $priceData = $this->adminPaymentService->calculateLicenseOrderPrice($license, $user);
    $priceDataNew = $priceData;
    $cekPriceDataNew = $this->adminPaymentService->get_promo_coupon($user,$license,$priceData, $couponCode);
    if($cekPriceDataNew['success'] == false){
        return redirect()->route('admin.account.licensing.order',$id)->with('warning', $cekPriceDataNew['messages']);
    }
    if($cekPriceDataNew['success'] == true){
        $priceDataNew = $cekPriceDataNew['priceData'];
    }
    return view('accounts.licensing_dinetkan.order', [
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

    $invoice = $this->adminPaymentService->createLicenseInvoice($license, $user, $request->couponCode);

    if ($invoice->price === 0) {
        $this->adminPaymentService->markInvoiceAsPaid($invoice);
        return redirect()->route('admin.account.licensing.thank-you', $invoice->no_invoice);
    }

    return redirect()->route('admin.invoice_dinetkan', $invoice->no_invoice);
}

public function invoice(Request $request, string $no_invoice)
{
    $invoice = $this->adminInvoiceRepo->findByNoInvoice($no_invoice);

    abort_unless($invoice, 404);

    $activeGateway = $this->settings->active_gateway;
    $priceData = $this->adminPaymentService->calculateLicenseOrderPrice($invoice->itemable, $invoice->admin, $invoice);

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

        return view('accounts.licensing_dinetkan.invoice', [
            'activeGateway' => $activeGateway,
            'invoice' => $invoice,
            'tripayMethods' => $tripayMethods,
            'priceData' => $priceData,
            'config' => $config,
            'settings' => $this->settings,
        ]);
    }


public function invoice_pdf(Request $request, string $no_invoice)
{
    $invoice = $this->adminInvoiceRepo->findByNoInvoice($no_invoice);

    abort_unless($invoice, 404);

    $activeGateway = $this->settings->active_gateway;
    $priceData = $this->adminPaymentService->calculateLicenseOrderPrice($invoice->itemable, $invoice->admin, $invoice);

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
            $pdf = Pdf::loadView('accounts.licensing_dinetkan.invoice_pdf',
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
//            $this->adminPaymentService->markInvoiceAsPaid($invoice);
//        }
    $request->validate(['payment_method' => 'nullable|string']);
    $selectedMethod = $request->input('payment_method');

    $invoice = $this->adminInvoiceRepo->findByNoInvoice($no_invoice);
    abort_unless($invoice, 404);

    $priceData = $this->adminPaymentService->calculateLicenseOrderPrice($invoice->itemable, $invoice->admin, $invoice);
//        $priceDataNew = $this->adminPaymentService->get_promo_coupon($invoice->admin,$invoice->itemable,$priceData, $invoice->couponname);
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
        'tripay' => $this->adminPaymentService->createTripayTransaction($invoice, $selectedMethod),
            'duitku' => $this->adminPaymentService->createDuitkuTransaction($invoice),
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

    $handledInvoice = $this->adminPaymentService->handleTripayNotification($request->all(), $request->header('X-Callback-Signature'));

    if ($handledInvoice) {
        return response()->json(['success' => true, 'message' => 'Invoice successfully paid and processed']);
    }

    return response()->json(['success' => false, 'message' => 'Transaction status not handled'], 400);
}

public function duitkuNotification(Request $request)
{
    Storage::disk('local')->append('example.txt', json_encode($request->all(), JSON_PRETTY_PRINT) . "\n\n");
    $handledInvoice = $this->adminPaymentService->handleDuitkuNotification($request->all());

    if ($handledInvoice) {
        return response()->json(['success' => true, 'message' => 'Invoice successfully paid and processed']);
    }

    return response()->json(['success' => false, 'message' => 'Transaction status not handled'], 400);
}

public function thankYou(string $invoiceNo)
{
    $invoice = $this->adminInvoiceRepo->findByNoInvoice($invoiceNo);

    return view('accounts.licensing_dinetkan.thank-you', compact('invoice'));
}

public function search(Request $request)
{
    $invoice = [];
    $user_id = "";
    if($request->dinetkan_user_id){
        $user_id = $request->dinetkan_user_id;
        $invoice = AdminDinetkanInvoice::query()
            ->where('dinetkan_user_id', $request->dinetkan_user_id)
            ->where('status', DinetkanInvoiceStatusEnum::UNPAID)->get();
    }
    return view('accounts.licensing_dinetkan.search', [
        'invoice' => $invoice,
        'user_id' => $user_id
    ]);
}
}
