<?php

namespace Modules\Payments\Services;

use App\Enums\InvoiceStatusEnum;
use App\Enums\TripayPaymentChannelEnum;
use App\Models\Invoice;
use App\Models\AdminInvoice;
use Illuminate\Support\Facades\Http;
use Modules\Payments\ValueObjects\PaymentMethod;
use Modules\Payments\ValueObjects\PriceData;
use Modules\Payments\ValueObjects\TripayConfig;

class TripayService
{
    public TripayConfig $config;

    public function setConfig(TripayConfig $config)
    {
        $this->config = $config;
    }

    public function getPaymentMethods(PriceData $priceData): array
    {
        $url = $this->config->isProduction()
            ? 'https://tripay.co.id/api/merchant/payment-channel'
            : 'https://tripay.co.id/api-sandbox/merchant/payment-channel';
        $methods = TripayPaymentChannelEnum::getPaymentMethods();

        if (config('app.env') === 'local') {
            return collect($methods)->map(function ($name, $code) {
                return new PaymentMethod(
                    code: $code,
                    name: $name,
                    fee: 0
                );
            })->values()->all();
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config->getApiKey(),
        ])->get($url);

        $result = $response->json();

        if ($result['success'] !== true) {
            throw new \Exception('Failed to get payment methods from Tripay: ' . $result['message']);
        }

        // Filter methods from the result api to return only active payment methods that have minimum and maximum amount that less than or equal to the invoice amount
        $filteredMethods = array_filter($methods, function ($method) use ($result, $priceData) {
            $methodData = collect($result['data'])->firstWhere('code', $method);
            return $methodData && $methodData['active'] && $methodData['minimum_amount'] <= $priceData->total && $methodData['maximum_amount'] >= $priceData->total;
        }, ARRAY_FILTER_USE_KEY);

        return collect($filteredMethods)->map(function ($name, $code) use ($result) {
            $methodData = collect($result['data'])->firstWhere('code', $code);
            $fee = $methodData['total_fee']['flat'] ?? 0;

            return new PaymentMethod(
                code: $code,
                name: $name,
                fee: $fee
            );
        })->values()->all();
    }

    public function createTransaction(Invoice|AdminInvoice $invoice, array $data): array
    {
        $url = $this->config->isProduction()
            ? 'https://tripay.co.id/api/transaction/create'
            : 'https://tripay.co.id/api-sandbox/transaction/create';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config->getApiKey(),
        ])->post($url, $data);

        $result = $response->json();

        if (isset($result['success']) && $result['success'] == true) {
            $invoice->update(['payment_url' => $result['data']['checkout_url']]);
        }

        return $result;
    }

    public function handleNotification($data)
    {
        $transactionStatus = $data['status'];
        $orderId = $data['merchant_ref'];
        $invoice = Invoice::where('no_invoice', $orderId)->first();

        if ($transactionStatus === 'PAID' && $invoice && $invoice->status === 0) {
            return $invoice;
        }

        return null;
    }

    public function handleAdminNotification(array $data): ?AdminInvoice
    {
        $transactionStatus = $data['status'] ?? null;
        $orderId = $data['merchant_ref'] ?? null;

        if (!$orderId) {
            return null;
        }

        $invoice = AdminInvoice::where('no_invoice', $orderId)->first();
        if ($transactionStatus === 'PAID' && $invoice && $invoice->status === InvoiceStatusEnum::UNPAID) {
            return $invoice;
        }

        return null;
    }

    public function createSignature(string $merchantRef, float $amount): string
    {
        return hash_hmac('sha256', $this->config->getMerchantCode() . $merchantRef . $amount, $this->config->getPrivateKey());
    }

    public function validateSignature(array $requestData, ?string $callbackSignature): bool
    {
        if (!$callbackSignature) {
            return false;
        }

        $dataSignature = hash_hmac('sha256', json_encode($requestData), $this->config->getPrivateKey());
        return hash_equals($dataSignature, $callbackSignature);
    }
}
