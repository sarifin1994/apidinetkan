<?php

namespace Modules\Payments\Services;

use App\Models\AdminDinetkanInvoice;
//use App\Models\AdminInvoice;
use App\Enums\InvoiceStatusEnum;
//use Illuminate\Support\Facades\Log;
use App\Models\Setting\Mduitku;
use App\Settings\SiteDinetkanSettings;
use Illuminate\Support\Facades\Http;
use Modules\Payments\ValueObjects\DuitkuConfig;
use App\Enums\DinetkanInvoiceStatusEnum;

class DuitkuService
{
    public DuitkuConfig $config;

    public function setConfig(DuitkuConfig $config): void
    {
        $this->config = $config;
    }

    public function createTransaction(Invoice|AdminInvoice $invoice)
    {
        $amount_discount = $invoice->price * $invoice->discount / 100;
        $amount_ppn      = ($invoice->price - $invoice->discount_coupon - $amount_discount) * $invoice->ppn / 100;
        $discount_coupon = $invoice->discount_coupon;
//        $gross_amount    = $invoice->price + $this->config->getAdminFee() + $amount_ppn - $amount_discount - $invoice->discount_coupon;
        $gross_amount    = $invoice->price + $invoice->fee + $amount_ppn - $amount_discount - $invoice->discount_coupon;
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

//        if ($this->config->getAdminFee() > 0) {
//            $itemDetails[] = [
//                'name'     => 'Biaya Admin',
//                'price'    => (int) $this->config->getAdminFee(),
//                'quantity' => 1,
//            ];
//        }
if ($invoice->fee > 0) {
    $itemDetails[] = [
        'name'     => 'Biaya Admin',
        'price'    => (int) $invoice->fee,
        'quantity' => 1,
    ];
}

$customerVaName = match (true) {
$invoice instanceof Invoice => $invoice->member->full_name,
            $invoice instanceof AdminInvoice => $invoice->admin->name,
            default => '',
        };
        $email = match (true) {
        $invoice instanceof Invoice => $invoice->member->email ?? '',
            $invoice instanceof AdminInvoice => $invoice->admin->email ?? '',
            default => '',
        };
        $phoneNumber = match (true) {
        $invoice instanceof Invoice => $invoice->member->wa ?? '',
            $invoice instanceof AdminInvoice => $invoice->admin->whatsapp ?? '',
            default => '',
        };

        $transaction = [
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
            'callbackUrl'     => route('notification.admin.duitku'),
            'returnUrl'       => route('admin.invoice', $invoice->no_invoice),
            'expiryPeriod'    => 60 * 24, // 1440 minutes
        ];

        // Decide sandbox vs production
        $url = $this->config->isProduction()
            ? 'https://api-prod.duitku.com/api/merchant/createInvoice'
            : 'https://api-sandbox.duitku.com/api/merchant/createInvoice';

        // Use millisecond timestamp to avoid "Request Expired"
        $timestamp = (int) round(microtime(true) * 1000);

        // Generate signature with HMAC-SHA256
        $signature = $this->generateSignature($timestamp);

        // Send the POST request
        $response = Http::withHeaders([
            'Accept'               => 'application/json',
            'Content-Type'         => 'application/json',
            'x-duitku-signature'   => $signature,
            'x-duitku-timestamp'   => $timestamp,
            'x-duitku-merchantcode' => $this->config->getIdMerchant(),
        ])->post($url, $transaction);

        return $response;
    }


    public function createTransactionDinetkan(AdminDinetkanInvoice $invoice, SiteDinetkanSettings $setting)
{

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

//        if ($this->config->getAdminFee() > 0) {
//            $itemDetails[] = [
//                'name'     => 'Biaya Admin',
//                'price'    => (int) $this->config->getAdminFee(),
//                'quantity' => 1,
//            ];
//        }
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

        $transaction = [
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
        ];

        // Decide sandbox vs production
        $url = $setting->duitku_sandbox == false
            ? 'https://api-prod.duitku.com/api/merchant/createInvoice'
            : 'https://api-sandbox.duitku.com/api/merchant/createInvoice';
        // Use millisecond timestamp to avoid "Request Expired"
        $timestamp = (int) round(microtime(true) * 1000);

        // Generate signature with HMAC-SHA256
        $signature = $this->generateSignatureDinetkan($timestamp,$setting);

        // Send the POST request
        $response = Http::withHeaders([
            'Accept'               => 'application/json',
            'Content-Type'         => 'application/json',
            'x-duitku-signature'   => $signature,
            'x-duitku-timestamp'   => $timestamp,
            'x-duitku-merchantcode' => $setting->duitku_merchant_code,
        ])->post($url, $transaction);

        return $response;
    }

    public function handleNotification(array $requestData)
{
    $merchantCode = $requestData['merchantCode'];
    $amount       = $requestData['amount'];
    $orderId      = $requestData['merchantOrderId'];
    $productDetail = $requestData['productDetail'];
    $additionalParam = $requestData['additionalParam'];
    $paymentCode = $requestData['paymentCode'];
    $resultCode = $requestData['resultCode'];
    $merchantUserId = $requestData['merchantUserId'];
    $reference = $requestData['reference'];
    // MD5(merchantcode + amount + merchantOrderId + merchantKey)
    $signature = $requestData['signature'];
    $publisherOrderId = $requestData['publisherOrderId'];
    $spUserHash = $requestData['spUserHash'] ?? '';
    $settlementDate = $requestData['settlementDate'] ?? '';
    $issuerCode = $requestData['issuerCode'] ?? '';

    $invoice = Invoice::where('no_invoice', $orderId)->first();
    $signatureKey = md5($merchantCode . $amount . $orderId . $this->config->getApiKey());

    if ($invoice && $signature === $signatureKey && $resultCode === '00' && $invoice->status === 0) {
        return $invoice;
    }

    return null;
}

    public function handleAdminNotification(array $requestData)
{
    $merchantCode = $requestData['merchantCode'];
    $amount       = $requestData['amount'];
    $orderId      = $requestData['merchantOrderId'];
    $productDetail = $requestData['productDetail'];
    $additionalParam = $requestData['additionalParam'];
    $paymentCode = $requestData['paymentCode'];
    $resultCode = $requestData['resultCode'];
    $merchantUserId = $requestData['merchantUserId'];
    $reference = $requestData['reference'];
    // MD5(merchantcode + amount + merchantOrderId + merchantKey)
    $signature = $requestData['signature'];
    $publisherOrderId = $requestData['publisherOrderId'];
    $spUserHash = $requestData['spUserHash'] ?? '';
    $settlementDate = $requestData['settlementDate'] ?? '';
    $issuerCode = $requestData['issuerCode'] ?? '';

    $invoice = AdminInvoice::where('no_invoice', $orderId)->first();
    $signatureKey = md5($merchantCode . $amount . $orderId . $this->config->getApiKey());

    if ($invoice && $resultCode == '00' && $invoice->status === InvoiceStatusEnum::UNPAID) {
        return $invoice;
    }

    return null;
}



    public function handleAdminNotificationDinetkan(array $requestData)
{
    $merchantCode = $requestData['merchantCode'];
    $amount       = $requestData['amount'];
    $orderId      = $requestData['merchantOrderId'];
    $productDetail = $requestData['productDetail'];
    $additionalParam = $requestData['additionalParam'];
    $paymentCode = $requestData['paymentCode'];
    $resultCode = $requestData['resultCode'];
    $merchantUserId = $requestData['merchantUserId'];
    $reference = $requestData['reference'];
    // MD5(merchantcode + amount + merchantOrderId + merchantKey)
    $signature = $requestData['signature'];
    $publisherOrderId = $requestData['publisherOrderId'];
    $spUserHash = $requestData['spUserHash'] ?? '';
    $settlementDate = $requestData['settlementDate'] ?? '';
    $issuerCode = $requestData['issuerCode'] ?? '';

    $invoice = AdminDinetkanInvoice::where('no_invoice', $orderId)->first();
    $signatureKey = md5($merchantCode . $amount . $orderId . $this->config->getApiKey());

    if ($invoice && $resultCode == '00' && $invoice->status === DinetkanInvoiceStatusEnum::UNPAID) {
        return $invoice;
    }

    return null;
}

    /**
     * Generate signature: HMAC-SHA256 of (merchantCode + timestampMs) using apiKey as secret.
     */
    public function generateSignature($timestampMs)
{
    $merchantCode = $this->config->getIdMerchant();
    $apiKey       = $this->config->getApiKey();

    return hash_hmac('sha256', $merchantCode . $timestampMs, $apiKey);
}

    public function generateSignatureDinetkan($timestampMs, $setting)
{
    $merchantCode = $setting->duitku_merchant_code; //$this->config->getIdMerchant();
    $apiKey       = $setting->duitku_api_key; //$this->config->getApiKey();

    return hash_hmac('sha256', $merchantCode . $timestampMs, $apiKey);
}
}
