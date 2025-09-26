<?php

namespace Modules\Payments\Repositories\Contracts;

use App\Models\AdminInvoice;
use App\Models\License;
use App\Models\User;

interface AdminInvoiceRepositoryInterface
{
    public function findByNoInvoice(string $no_invoice): ?AdminInvoice;
    public function countPaidInvoices(User $user, License $license): int;
    public function save(AdminInvoice $invoice): void;
}
