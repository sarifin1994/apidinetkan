<?php

namespace App\Console\Commands;

use App\Enums\InvoiceStatusEnum;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Wablas;
use App\Models\RadiusNas;
use App\Models\PppoeMember;
use Illuminate\Support\Str;
use App\Models\WablasMessage;
use App\Models\BillingSetting;
use App\Models\WablasTemplate;
use App\Enums\MemberStatusEnum;
use App\Models\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;


class ReactivateServicesCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reactivate_services:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reactivate Billing Services';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // get last 5 minutes updated and paid invoices
        $invoices = Invoice::with('service', 'service.member', 'service.pppoe')
            ->where('status', InvoiceStatusEnum::PAID)
            ->where('updated_at', '>=', Carbon::now()->subMinutes(5))
            ->get();

        foreach ($invoices as $invoice) {
            $service = $invoice->service;
            $member = $service->member;
            $pppoe = $service->pppoe;

            // check if the service is not suspended
            if ($pppoe->status !== 1 || $member->status !== MemberStatusEnum::ACTIVE) {
                continue;
            }

            Log::info('Reactivate the service for ' . $service->id_service . ' at ' . date('Y-m-d H:i:s'));

            // Execute reactivation process
            $pppoe->status = 1;
            $pppoe->save();

            $draw = [
                'username' => $pppoe->username,
                'nas'      => $pppoe->nas,
            ];

            if ($pppoe->nas !== null) {
                $nas_secret = RadiusNas::where('nasname', $pppoe->nas)
                    ->value('secret');

                $command = Process::path('/usr/bin/')->run("echo User-Name='{$draw['username']}' | radclient -r 1 {$draw['nas']}:3799 disconnect $nas_secret");
            } else {
                $nas_list = RadiusNas::where('group_id', $invoice->group_id)
                    ->select('nasname', 'secret')
                    ->get();

                foreach ($nas_list as $item) {
                    $command = Process::path('/usr/bin/')->run("echo User-Name='{$draw['username']}' | radclient -r 1 {$item->nasname}:3799 disconnect {$item->secret}");
                }
            }
        }

        Log::info('Reactivate cycle has been successfully executed at ' . date('Y-m-d H:i:s'));
    }
}
