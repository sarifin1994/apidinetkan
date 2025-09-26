<?php

namespace Modules\Payments\Repositories\Contracts;

use App\Models\AdminDinetkanInvoice;
use App\Models\LicenseDinetkan;
use App\Models\UserDinetkan;

interface AdminDinetkanInvoiceRepositoryInterface
{
    public function findByNoInvoice(string $no_invoice): ?AdminDinetkanInvoice;
    public function countPaidInvoices(UserDinetkan $user, LicenseDinetkan $license): int;
    public function save(AdminDinetkanInvoice $invoice): void;
}
