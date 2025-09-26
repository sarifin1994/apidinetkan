<?php

namespace Modules\Payments\Repositories\Contracts;

use App\Models\Invoice;

interface InvoiceRepositoryInterface
{
    public function findByNoInvoice(string $no_invoice): ?Invoice;
    public function save(Invoice $invoice): void;
}
