<?php

namespace Modules\Payments\Repositories;

use App\Models\Invoice;
use Modules\Payments\Repositories\Contracts\InvoiceRepositoryInterface;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function findByNoInvoice(string $no_invoice): ?Invoice
    {
        return Invoice::with('member')->where('no_invoice', $no_invoice)->first();
    }

    public function save(Invoice $invoice): void
    {
        $invoice->save();
    }
}
