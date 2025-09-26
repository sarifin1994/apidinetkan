<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Wablas;
use App\Models\License;
use App\Models\RadiusNas;
use App\Models\PppoeMember;
use Illuminate\Support\Str;
use App\Models\AdminInvoice;
use App\Enums\UserStatusEnum;
use App\Models\WablasMessage;
use App\Models\BillingSetting;
use App\Models\WablasTemplate;
use Illuminate\Console\Command;
use App\Settings\LicenseSettings;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Modules\Payments\Services\AdminPaymentService;

class LicenseSuspendFixedCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'license:suspend:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Suspend Fixed Date';

    /**
     * Execute the console command.
     */
    public function handle(
        AdminPaymentService $adminPaymentService
    ) {
        $users = User::select('id', 'id_group', 'username', 'whatsapp', 'license_id', 'next_due')
            ->where('status', UserStatusEnum::ACTIVE)
            ->where('next_due', '<=', Carbon::now()->format('Y-m-d'))
            ->where('role', 'Admin')->get();
        $now   = Carbon::now();

        Log::info('Admin suspend cycle has been started at ' . date('Y-m-d H:i:s'));
        // Log::info('Total users: ' . count($users));

        foreach ($users as $user) {
            // Update user status to suspended by unassigning the license
            $user->license_id = null;
            $user->status     = UserStatusEnum::OVERDUE;
            $user->save();

            Log::info('User ' . $user->username . ' has been suspended at ' . date('Y-m-d H:i:s'));

            // Other users under the same group will be suspended as well
            User::where('id_group', $user->id_group)
                ->where('status', UserStatusEnum::ACTIVE)
                ->update(['status' => UserStatusEnum::OVERDUE]);

            // $settings = app(LicenseSettings::class);

            // $shortcodes = [
            //     '[name]',
            //     '[license_name]',
            //     '[invoice_number]',
            //     '[invoice_date]',
            //     '[invoice_due_date]',
            //     '[amount]',
            //     '[ppn]',
            //     '[discount]',
            //     '[total]',
            //     '[period]',
            //     '[payment_link]',
            // ];

            // Check if invoice already created
            // $license = License::where('id', $user->license_id)->first();
            // $invoice = AdminInvoice::where('id_group', $user->id_group)
            //     ->where('license_id', $user->license_id)
            //     ->where('period', $user->next_due)
            //     ->first();

            // if invoice already created, send reminder notification
            // $priceData = $adminPaymentService->calculateLicenseOrderPrice($license, $user, $invoice);

            // // send notification
            // $template = $settings->invoice_overdue_template;
            // $sources = [
            //     $user->name,
            //     $user->license->name,
            //     $invoice->no_invoice,
            //     $invoice->created_at->format('d/m/Y'),
            //     $invoice->due_date->format('d/m/Y'),
            //     $priceData->price,
            //     $priceData->ppnPercentage,
            //     $priceData->discountPercentage,
            //     $priceData->total,
            //     $invoice->period,
            //     $invoice->payment_link,
            // ];

            // $message = str_replace($shortcodes, $sources, $template);
            // $message = str_replace('<br>', "\n", $message);

            // $messageId   = Str::random(30);
            // $messageData = [
            //     'group_id'   => $user->id_group,
            //     'id_message' => $messageId,
            //     'subject'    => 'INVOICE OVERDUE #' . $invoice->no_invoice,
            //     'message'    => preg_replace("/\r\n|\r|\n/", '<br>', $message),
            //     'phone'      => $user->whatsapp,
            //     'status'     => 'pending',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ];
            // WablasMessage::insert([$messageData]);

            // // Send message via Wablas API
            // $wablas = Wablas::where('group_id', $user->id_group)
            //     ->select('token', 'sender')
            //     ->first();

            // if ($wablas) {
            //     $data = [
            //         'api_key'    => $wablas->token,
            //         'sender'     => $wablas->sender,
            //         'number'     => $user->whatsapp,
            //         'message'    => $message,
            //         'id_message' => $messageId,
            //     ];
            //     $curl = curl_init();
            //     curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            //     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            //     curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            //     curl_setopt($curl, CURLOPT_URL, config('services.whatsapp.url') . '/end-message');
            //     curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            //     curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

            //     $response = curl_exec($curl);
            //     curl_close($curl);
            // }
        }

        Log::info('Admin suspend cycle has been successfully executed at ' . date('Y-m-d H:i:s'));
    }
}
