<?php

namespace Modules\Payments\Services;

use App\Enums\InvoiceStatusEnum;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Modules\Payments\ValueObjects\XenditConfig;
use Xendit\Configuration;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\InvoiceStatus;

class XenditService
{
    public XenditConfig $config;

    public function setConfig(XenditConfig $config): void
    {
        $this->config = $config;
        Configuration::setXenditKey($config->getSecretKey());
    }

    public function createTransaction(Invoice $invoice)
    {
        $amount_ppn = $invoice->price * $invoice->ppn / 100;
        $amount_discount = $invoice->price * $invoice->discount / 100;
        $gross_amount = $invoice->price + $this->config->getAdminFee() + $amount_ppn - $amount_discount;

        $items = [
            [
                'id' => 1,
                'name' => $invoice->item,
                'price' => $invoice->price,
                'quantity' => 1,
            ],
        ];
        $fees = [];

        if ($amount_ppn > 0) {
            $fees[] = [
                'type' => 'PPN',
                'value' => $amount_ppn,
            ];
        }

        if ($amount_discount > 0) {
            $fees[] = [
                'type' => 'Discount',
                'value' => -$amount_discount,
            ];
        }

        if ($this->config->getAdminFee() > 0) {
            $fees[] = [
                'type' => 'Biaya Admin',
                'value' => $this->config->getAdminFee(),
            ];
        }

        $transaction = [
            'external_id' => $invoice->no_invoice,
            'description' => $invoice->item,
            'amount' => $gross_amount,
            'success_redirect_url' => route('invoice.pay', $invoice->no_invoice),
            'failure_redirect_url' => route('invoice.pay', $invoice->no_invoice),
            'invoice_duration' => 60 * 60 * 24,
            'items' => $items,
        ];

        if (!empty($fees)) {
            $transaction['fees'] = $fees;
        }

        $order = new InvoiceApi();
        $xenditInvoice = new CreateInvoiceRequest($transaction);

        $response = $order->createInvoice($xenditInvoice);

        $invoice->update(['payment_url' => $response->getInvoiceUrl()]);

        return $response;
    }

    public function handleNotification(Request $request): Invoice
    {
        $token = $request->header('x-callback-token');
        $status = $request->input('status');
        $config = $this->config;

        if ($token !== $config->getWebhookVerificationKey()) {
            throw new \Exception('Invalid token');
        }

        $invoice_id = $request->input('external_id');
        $invoice = Invoice::where('no_invoice', $invoice_id)->first();

        if (!$invoice) {
            throw new \Exception('Invoice not found');
        }

        return $invoice;
    }
}
