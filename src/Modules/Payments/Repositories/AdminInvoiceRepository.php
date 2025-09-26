<?php

namespace Modules\Payments\Repositories;

use App\Enums\InvoiceStatusEnum;
use App\Models\User;
use App\Models\License;
use App\Models\AdminInvoice;
use Modules\Payments\Repositories\Contracts\AdminInvoiceRepositoryInterface;

class AdminInvoiceRepository implements AdminInvoiceRepositoryInterface
{
    public function findByNoInvoice(string $no_invoice): ?AdminInvoice
    {
        return AdminInvoice::where('no_invoice', $no_invoice)->first();
    }

    public function countPaidInvoices(User $user, License $license): int
    {
        return AdminInvoice::where('user_id', $user->id)
            ->where('itemable_id', $license->id)
            ->where('itemable_type', License::class)
            ->where('status', InvoiceStatusEnum::PAID)
            ->count();
    }

    public function save(AdminInvoice $invoice): void
    {
        $invoice->save();
    }
}
