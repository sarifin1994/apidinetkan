<?php

namespace Modules\Payments\Services;

use App\Models\Invoice;
use Midtrans\Snap;
use Midtrans\Config;
use Carbon\Carbon;
use Midtrans\Notification as MidtransNotification;
use Modules\Payments\ValueObjects\MidtransConfig;

class MidtransService
{
    public MidtransConfig $config;

    public function setConfig(MidtransConfig $midtransConfig): void
    {
        $this->config = $midtransConfig;
        Config::$serverKey = $this->config->getServerKey();
        Config::$clientKey = $this->config->getClientKey();
        Config::$isProduction = $this->config->isProduction();
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function generateSnapToken(Invoice $invoice)
    {
        $amount_ppn = $invoice->price * $invoice->ppn / 100;
        $amount_discount = $invoice->price * $invoice->discount / 100;
        $gross_amount = $invoice->price + $this->config->getAdminFee() + $amount_ppn - $amount_discount;

        $transaction = [
            'transaction_details' => [
                'order_id' => $invoice->no_invoice,
                'gross_amount' => $gross_amount,
            ],
            'customer_details' => [
                'first_name' => $invoice->member->full_name,
                'phone' => $invoice->member->wa,
                'billing_address' => $invoice->member->address,
            ],
            'item_details' => [
                [
                    'id' => 'a1',
                    'price' => $invoice->price,
                    'quantity' => 1,
                    'name' => $invoice->item,
                ],
                [
                    'id' => 'a2',
                    'price' => $amount_ppn,
                    'quantity' => 1,
                    'name' => 'PPN',
                ],
                [
                    'id' => 'a3',
                    'price' => -$amount_discount,
                    'quantity' => 1,
                    'name' => 'Discount',
                ],
                [
                    'id' => 'a4',
                    'price' => $this->config->getAdminFee(),
                    'quantity' => 1,
                    'name' => 'Biaya Admin',
                ],
            ],
        ];

        if (!$invoice->snap_token) {
            $snap_token = Snap::getSnapToken($transaction);
            $invoice->update(['snap_token' => $snap_token]);
            return $snap_token;
        }

        return $invoice->snap_token;
    }

    public function handleNotification($notification)
    {
        $transactionStatus = $notification->transaction_status;
        $orderId = $notification->order_id;
        $invoice = Invoice::where('no_invoice', $orderId)->first();

        if ($invoice && $transactionStatus === 'settlement' && $invoice->status === 0) {
            return $invoice;
        }

        return null;
    }

    public function getNotification(array $requestData): ?object
    {
        try {
            $notif = new MidtransNotification();
            return $notif->getResponse();
        } catch (\Exception $e) {
            return null;
        }
    }
}
