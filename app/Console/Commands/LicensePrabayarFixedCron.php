<?php

namespace App\Console\Commands;

use App\Enums\UserStatusEnum;
use App\Models\AdminInvoice;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Member;
use App\Models\Wablas;
use App\Models\PppoeMember;
use Illuminate\Support\Str;
use App\Models\WablasMessage;
use App\Models\BillingSetting;
use App\Models\License;
use App\Models\WablasTemplate;
use App\Settings\LicenseSettings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Payments\Services\AdminPaymentService;

class LicensePrabayarFixedCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'license:check:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Admin License Prabayar and Send Notification';



    /**
     * Execute the console command.
     */
    public function handle(
        AdminPaymentService $adminPaymentService
    ) {
        $users = User::select('id_group')->where('role', 'Admin')->get();
        $now   = Carbon::now();

        foreach ($users as $user) {
            $userDue = Carbon::parse($user->next_due);

            // if user is not active, skip the process
            if ($user->status !== UserStatusEnum::ACTIVE || !$user->license_id) {
                continue;
            }

            // if user is past due date, skip the process
            if ($now->gt($userDue)) {
                continue;
            }

            $settings = app(LicenseSettings::class);

            $dayBeforeDue = $settings->day_before_due;
            $dayBeforeDue = $userDue->copy()->subDays($dayBeforeDue);

            // if user is not due yet, skip the process
            if ($now->lt($dayBeforeDue)) {
                continue;
            }

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

            // // Check if invoice already created
            // $license = License::where('id', $user->license_id)->first();
            // $invoice = AdminInvoice::where('id_group', $user->id_group)
            //     ->where('license_id', $user->license_id)
            //     ->where('period', $user->next_due)
            //     ->first();

            // // if no invoice found, create new invoice and send notification
            // if (!$invoice) {
            //     $invoice = $adminPaymentService->createLicenseInvoice($license, $user);
            //     $priceData = $adminPaymentService->calculateLicenseOrderPrice($license, $user, $invoice);

            //     // send notification
            //     $template = $settings->invoice_created_template;
            //     $sources = [
            //         $user->name,
            //         $user->license->name,
            //         $invoice->no_invoice,
            //         $invoice->created_at->format('d/m/Y'),
            //         $invoice->due_date->format('d/m/Y'),
            //         $priceData->price,
            //         $priceData->ppnPercentage,
            //         $priceData->discountPercentage,
            //         $priceData->total,
            //         $invoice->period,
            //         $invoice->payment_link,
            //     ];

            //     $message = str_replace($shortcodes, $sources, $template);
            //     $message = str_replace('<br>', "\n", $message);

            //     $messageId   = Str::random(30);
            //     $messageData = [
            //         'group_id'   => $user->id_group,
            //         'id_message' => $messageId,
            //         'subject'    => 'INVOICE TERBIT #' . $invoice->no_invoice,
            //         'message'    => preg_replace("/\r\n|\r|\n/", '<br>', $message),
            //         'phone'      => $user->whatsapp,
            //         'status'     => 'pending',
            //         'created_at' => Carbon::now(),
            //         'updated_at' => Carbon::now(),
            //     ];
            //     WablasMessage::insert([$messageData]);

            //     // Send message via Wablas API
            //     $wablas = Wablas::where('group_id', $user->id_group)
            //         ->select('token', 'sender')
            //         ->first();

            //     if ($wablas) {
            //         $data = [
            //             'api_key'    => $wablas->token,
            //             'sender'     => $wablas->sender,
            //             'number'     => $user->whatsapp,
            //             'message'    => $message,
            //             'id_message' => $messageId,
            //         ];
            //         $curl = curl_init();
            //         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            //         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            //         curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            //         curl_setopt($curl, CURLOPT_URL, config('services.whatsapp.url') . '/send-message');
            //         curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            //         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

            //         $response = curl_exec($curl);
            //         curl_close($curl);
            //     }

            //     continue;
            // }

            // // if invoice already created, send reminder notification
            // $priceData = $adminPaymentService->calculateLicenseOrderPrice($license, $user, $invoice);

            // // send notification
            // $template = $settings->invoice_reminder_template;
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
            //     'subject'    => 'INVOICE TERBIT #' . $invoice->no_invoice,
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
            //     curl_setopt($curl, CURLOPT_URL, config('services.whatsapp.url') . '/send-message');
            //     curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            //     curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

            //     $response = curl_exec($curl);
            //     curl_close($curl);
            // }
        }
    }
}
