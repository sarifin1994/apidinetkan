<?php

namespace Modules\Payments\Repositories;

use App\Enums\DinetkanInvoiceStatusEnum;
use App\Models\AdminDinetkanInvoice;
use App\Models\LicenseDinetkan;
use App\Models\User;
use App\Models\License;
use App\Models\AdminInvoice;
use App\Models\UserDinetkan;
use Modules\Payments\Repositories\Contracts\AdminDinetkanInvoiceRepositoryInterface;
use Modules\Payments\Repositories\Contracts\AdminInvoiceRepositoryInterface;

class AdminDinetkanInvoiceRepository implements AdminDinetkanInvoiceRepositoryInterface
{
    public function findByNoInvoice(string $no_invoice): ?AdminDinetkanInvoice
    {
        return AdminDinetkanInvoice::where('no_invoice', $no_invoice)->first();
    }

    public function countPaidInvoices(UserDinetkan $user, LicenseDinetkan $license): int
    {
        return AdminDinetkanInvoice::where('user_id', $user->id)
            ->where('itemable_id', $license->id)
            ->where('itemable_type', LicenseDinetkan::class)
            ->where('status', DinetkanInvoiceStatusEnum::PAID)
            ->count();
    }

    public function save(AdminDinetkanInvoice $invoice): void
    {
        $invoice->save();
    }
}
