<?php

namespace App\Console\Commands;

use App\Models\SmtpSetting;
use App\Services\CustomMailerService;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Setting\BillingSetting;
use App\Models\Whatsapp\Mpwa;
use App\Models\Whatsapp\Watemplate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ReminderInvoice extends Command
{
    protected $signature = 'invoice:reminder-notification';
    protected $description = 'Send reminder notifications for unpaid invoices';

    public function handle()
    {
        // Set timezone to Asia/Jakarta
        config(['app.timezone' => 'Asia/Jakarta']);
        date_default_timezone_set('Asia/Jakarta');

        $today = Carbon::today()->toDateString();

        // Process users in chunks
        User::where('role', 'Admin')
            ->whereIn('status', [1, 3])
            ->where('is_dinetkan','0')
            ->where('shortname','development')
            ->with(['c_invoice' => function($q) {
                $q->where('status', 'unpaid');
            }, 'c_invoice.c_pppoe'])
            ->chunkById(100, function ($users) use ($today) {
                foreach ($users as $user) {
                    $this->processUserInvoices($user, $today);
                }
            });
    }

    protected function processUserInvoices($user, string $today)
    {
        $shortname = $user->shortname;

        // Load settings and templates
        $billingSetting = BillingSetting::firstWhere('shortname', $shortname);
        $mpwa = Mpwa::firstWhere('shortname', $shortname);
        $smtp = SmtpSetting::firstWhere('shortname', $shortname);
        $watemplate = Watemplate::firstWhere('shortname', $shortname);

        if (! $billingSetting || ! $watemplate) {
            Log::error("[ReminderInvoice][{$shortname}] Missing billingSetting or watemplate");
            return;
        }

        $reminderDays = (int) $billingSetting->notif_ir;
        if ($reminderDays <= 0) {
            // Reminder disabled
            return;
        }
        foreach ($user->c_invoice as $invoice) {
            $this->processSingleInvoice($user, $invoice, $shortname, $mpwa, $watemplate->invoice_reminder, $today, $reminderDays, $smtp);
        }
    }

    protected function processSingleInvoice($user, $invoice, $shortname, $mpwa, $template, $today, int $reminderDays, SmtpSetting $smtp)
    {
        // Calculate reminder date: due_date minus reminderDays
        try {
            $dueDate = Carbon::createFromFormat('Y-m-d', $invoice->due_date);
            $targetDate = $dueDate->subDays($reminderDays)->toDateString();
        } catch (\Exception $e) {
            Log::error("[ReminderInvoice][{$shortname}] Invalid due_date for invoice {$invoice->id}: {$invoice->due_date}");
            return;
        }
        if ($today !== $targetDate) {
            return;
        }

        // Prepare data
        $pppoe = $invoice->c_pppoe;
        $waNumber = $pppoe->wa ?? null;
        if (! $waNumber) {
            Log::warning("[ReminderInvoice][{$shortname}] No WA number for invoice {$invoice->id}");
            return;
        }

        $amountPpn = ($invoice->price * $invoice->ppn) / 100;
        $amountDiscount = $invoice->discount;
        $total = $invoice->price + $amountPpn - $amountDiscount;

        $placeholders = [
            '[nama_lengkap]', '[id_pelanggan]', '[username]', '[password]', '[alamat]', '[paket_internet]',
            '[tipe_pembayaran]', '[siklus_tagihan]', '[no_invoice]', '[tgl_invoice]',
            '[harga]', '[ppn]', '[discount]', '[total]', '[jth_tempo]', '[periode]', '[subscribe]', '[link_pembayaran]'
        ];

        $values = [
            $pppoe->full_name,
            $pppoe->id_pelanggan,
            $pppoe->username,
            $pppoe->value,
            $pppoe->address,
            $pppoe->c_profile->name,
            $invoice->payment_type,
            $invoice->billing_period,
            $invoice->no_invoice,
            Carbon::parse($invoice->invoice_date)->translatedFormat('d F Y'),
            number_format($invoice->price, 0, ',', '.'),
            $invoice->ppn,
            $invoice->discount,
            number_format($total, 0, ',', '.'),
            Carbon::parse($invoice->due_date)->translatedFormat('d F Y'),
            Carbon::parse($invoice->period)->translatedFormat('F Y'),
            $invoice->subscribe,
            $invoice->payment_url,
        ];

        $message_orig = str_replace($placeholders, $values, $template);
        $message = str_replace('<br>', "\n", $message_orig);

        if($mpwa->mpwa_server_server == 'mpwa'){

            // Send via HTTP client
            try {
//                $response = Http::asForm()->post("https://{$mpwa->mpwa_server}/send-message", [
//                    'api_key' => $mpwa->api_key,
//                    'sender'  => $mpwa->sender,
//                    'number'  => $waNumber,
//                    'message' => $message,
//                ]);
//
//                if ($response->successful()) {
//                    Log::info("[ReminderInvoice][{$shortname}] Reminder sent for invoice {$invoice->id}");
//                } else {
//                    Log::error("[ReminderInvoice][{$shortname}] WA failed ({$response->status()}) for invoice {$invoice->id}: {$response->body()}");
//                }
            } catch (\Exception $e) {
                Log::error("[ReminderInvoice][{$shortname}] Exception sending WA for invoice {$invoice->id}: " . $e->getMessage());
            }
        }
        if($mpwa->mpwa_server_server == 'radiusqu' && $mpwa->is_login == 1){
            $nomorhp = gantiformat_hp($pppoe->wa);
            $user_wa = User::where('shortname', $mpwa->shortname)->first();
            $_id = $user_wa->whatsapp."_".env('APP_ENV');
            $apiUrl = env('WHATSAPP_URL_NEW')."send-message/".$_id; //env('CACTI_ENDPOINT').'cacti/logout/'.$_id;
            try {
                $params = array(
                    "jid" => $nomorhp."@s.whatsapp.net",
                    "content" => array(
                        "text" => $message
                    )
                );
                // Kirim POST request ke API eksternal
                // Http::post($apiUrl, $params);
                $response = Http::post($apiUrl, $params);
                // if($response->successful()){
                //     $json = $response->json();
                //     $status = $json->status;
                //     $receiver = $nomorhp;
                //     $shortname = $user_wa->shortname;
                //     save_wa_log($shortname,$receiver,$message,$status);
                // }

            } catch (\Exception $e) {
                Log::error("[ReminderInvoice][{$shortname}] Exception sending WA for invoice {$invoice->id}: " . $e->getMessage());
            }
        }

        // send email
        if($smtp){
            try{
                $data = [
                    'messages' => $message_orig,
                    'user_name' => $pppoe->username,
                    'notification' => 'Reminder Invoice Notification'
                ];
                app(CustomMailerService::class)->sendWithUserSmtpCron(
                    'emails.test',
                    $data,
                    $pppoe->email,
                    'Invoice',
                    $smtp
                );
                Log::info("[invoice:reminder-notification] Success sending email to {$pppoe->username}: ");
            }catch (\Exception $e){
                Log::error("[invoice:reminder-notification] Exception sending email to {$pppoe->username}: " . $e->getMessage());
            }
        }



        // Throttle requests
        sleep(5);
    }
}
