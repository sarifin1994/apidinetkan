<?php

namespace Modules\Payments\Services;

use App\Models\Member;
use App\Models\Invoice;
use App\Models\PppoeUser;
use App\Models\BillingSetting;
use Illuminate\Support\Carbon;
use App\Enums\InvoiceStatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Enums\TransactionCategoryEnum;
use App\Enums\TransactionPaymentMethodEnum;
use Modules\Notification\Services\WablasNotificationService;
use Modules\Payments\Repositories\Contracts\TransaksiRepositoryInterface;
use Modules\Data\Services\PppoeService;

final class MemberPaymentService
{
    public function __construct(
        protected TransaksiRepositoryInterface $transaksiRepo,
        protected WablasNotificationService $notificationService,
        protected PppoeService $pppoeService,
        private TripayService $tripayService
    ) {}

    public function createTransaction(Invoice $invoice, string $selectedMethod, int $adminFee): array
    {
        $ppnAmount = $invoice->price * $invoice->ppn / 100;
        $discountAmount = $invoice->price * $invoice->discount / 100;
        $totalAmount = $invoice->price + $adminFee + $ppnAmount - $discountAmount;

        $signature = $this->tripayService->createSignature($invoice->no_invoice, $totalAmount);

        $data = [
            'method'         => $selectedMethod,
            'merchant_ref'   => $invoice->no_invoice,
            'amount'         => $totalAmount,
            'customer_name'  => $invoice->member->full_name,
            'customer_email' => $invoice->member->email,
            'customer_phone' => $invoice->member->wa,
            'order_items'    => [
                [
                    'sku'      => $invoice->sku ?? 'default-sku',
                    'name'     => $invoice->item,
                    'price'    => $invoice->price,
                    'quantity' => 1,
                ],
                [
                    'sku'      => 'ppn',
                    'name'     => 'PPN',
                    'price'    => $ppnAmount,
                    'quantity' => 1,
                ],
                [
                    'sku'      => 'discount',
                    'name'     => 'Discount',
                    'price'    => -$discountAmount,
                    'quantity' => 1,
                ],
                [
                    'sku'      => 'admin_fee',
                    'name'     => 'Biaya Admin',
                    'price'    => $adminFee,
                    'quantity' => 1,
                ],
            ],
            'callback_url' => route('notification.tripay'),
            'return_url'   => route('invoice.pay', $invoice->no_invoice),
            'expired_time' => time() + (24 * 60 * 60),
            'signature'    => $signature,
        ];

        return $this->tripayService->createTransaction($invoice, $data);
    }

    public function handleSuccessfulPayment(Invoice $invoice): void
    {
        $group_id = $invoice->group_id;
        $member = Member::find($invoice->member_id);
        if (!$member) {
            return;
        }

        // Calculate next due date
        if ($invoice->payment_type === 'Prabayar') {
            $next_due = Carbon::now()->addMonthNoOverflow(1);
        } else {
            $due_bc = BillingSetting::where('group_id', $group_id)->value('due_bc');
            $next_due = Carbon::createFromFormat('Y-m-d', $invoice->due_date)->setDay($due_bc)->addMonthNoOverflow();
        }

        $invoice->update(['paid_date' => Carbon::today()->toDateString(), 'status' => InvoiceStatusEnum::PAID]);
        $member->update(['next_due' => $next_due]);

        // Create transaction record
        $this->transaksiRepo->create([
            'group_id'       => $group_id,
            'invoice_id'     => $invoice->id,
            'invoice_type'   => Invoice::class,
            'type'           => TransactionTypeEnum::INCOME->value,
            'category'       => TransactionCategoryEnum::INVOICE->value,
            'item'           => 'Invoice',
            'deskripsi'      => "Payment #{$invoice->no_invoice} a.n {$member->full_name}",
            'price'          => $invoice->price,
            'tanggal'        => Carbon::now(),
            'payment_method' => TransactionPaymentMethodEnum::TRANSFER->value,
            'admin'          => 'system',
        ]);

        // Notify user if enabled
        $billing = BillingSetting::where('group_id', $group_id)->select('notif_ps')->first();
        if ($billing && $billing->notif_ps === 1 && $member->wa) {
            $message = "Payment for invoice #{$invoice->no_invoice} has been received.";
            $this->notificationService->sendWablasMessage($member->wa, $message, $group_id);
        }

        // Handle PPPoE user status
        $pppoe = PppoeUser::find($member->pppoe_id);
        if ($pppoe && $pppoe->status === 2 && Invoice::where([['member_id', $invoice->member_id], ['status', InvoiceStatusEnum::UNPAID]])->count() === 0) {
            $pppoe->update(['status' => 1]);
            $this->pppoeService->disconnectUser($pppoe, $group_id);
        }
    }
}
