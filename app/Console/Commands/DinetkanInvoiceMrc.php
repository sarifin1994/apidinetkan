<?php

namespace App\Console\Commands;

use App\Enums\DinetkanInvoiceStatusEnum;
use App\Models\AdminInvoice;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class DinetkanInvoiceMrc extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:expired:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired invoice';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // delete all unpaid invoices that been created since 1 day ago
        $invoices = AdminInvoice::where('status', DinetkanInvoiceStatusEnum::UNPAID)
            ->where('created_at', '<', Carbon::now()->subDay())
            ->get();

        foreach ($invoices as $invoice) {
            $invoice->delete();
        }
    }
}
