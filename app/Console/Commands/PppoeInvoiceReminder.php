<?php


namespace App\Console\Commands;


use App\Models\Invoice;
use App\Models\MappingAdons;
use App\Models\Setting\BillingSetting;
use App\Models\SmtpSetting;
use App\Models\User;
use App\Models\Whatsapp\Mpwa;
use App\Models\Whatsapp\Watemplate;
use App\Services\CustomMailerService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PppoeInvoiceReminder extends Command
{
    protected $signature = 'pppoeinvoice:reminder';
    protected $description = 'notifikasi pppoe invoice unpaid';

    public function handle()
    {
        // Set timezone
        config(['app.timezone' => 'Asia/Jakarta']);
        date_default_timezone_set('Asia/Jakarta');

        // Process in chunks
        User::where('role', 'Admin')
            ->whereIn('status', [1, 3])
            ->where('is_dinetkan','0')
            ->with(['c_pppoe_reminder'])
            ->chunkById(50, function ($users) {
                foreach ($users as $user) {
                    $this->processUser($user);
                }
            });

        return 0;
    }

    protected function processUser($user)
    {
        $shortname = $user->shortname;
        $domain = $user->domain;

        // Load settings and templates
        $billing = BillingSetting::firstWhere('shortname', $shortname);
        $mpwa = Mpwa::firstWhere('shortname', $shortname);
        $smtp = SmtpSetting::firstWhere('shortname', $shortname);
        $watemplate = Watemplate::firstWhere('shortname', $shortname);

        if (! $billing || ! $watemplate) {
            Log::error("[FixedDateInvoice][{$shortname}] Missing billingSetting or watemplate");
            return;
        }

        $invFdDays = (int) $billing->inv_fd;
        $notifIt = (bool) $billing->notif_it;
        $template = $watemplate->invoice_reminder;
        $today = Carbon::today()->toDateString();

        foreach ($user->c_pppoe_reminder as $pppoe) {
            $invoice = \App\Models\Invoice\Invoice::query()->where('status', 'unpaid')
                ->where('id_pelanggan', $pppoe->id)->get();

            if (! $invoice) {
                Log::error("[FixedDateInvoice][{$shortname}] Failed to create invoice for {$pppoe->username}");
                continue;
            }

            foreach ($invoice as $inv){
                // Send WA notification if enabled
                if ($notifIt && ! empty($pppoe->wa)) {
                    $this->sendWaNotification($pppoe, $inv, $template, $mpwa, $shortname, $smtp);
                }

                Log::info("[FixedDateInvoice][{$shortname}] Invoice generated for {$pppoe->username}");
            }
        }
    }

    protected function sendWaNotification($pppoe, $invoice, string $template, Mpwa $mpwa, string $shortname, SmtpSetting $smtp)
    {
        $amountPpn = ($invoice->price * $invoice->ppn) / 100;
        $amountDiscount = $invoice->discount;
        $total = $invoice->price + $amountPpn - $amountDiscount;

        $description_adon = [];
        $mappingadons = MappingAdons::query()->where('id_pelanggan_pppoe', $pppoe->id_pelanggan)->where('no_invoice', $invoice->no_invoice)->get();
        if($mappingadons){
            foreach ($mappingadons as $mapp){
                $description_adon[] = $mapp->description;
            }
        }

        $placeholders = [
            '[nama_lengkap]', '[id_pelanggan]', '[username]', '[password]', '[alamat]', '[paket_internet]',
            '[tipe_pembayaran]', '[siklus_tagihan]', '[no_invoice]', '[tgl_invoice]',
            '[harga]', '[ppn]', '[discount]', '[total]', '[jth_tempo]', '[periode]', '[subscribe]', '[link_pembayaran]'
            ,'[description_adon]','[total_adons]','[total_invoice]'
        ];

        $values = [
            $pppoe->full_name,
            $pppoe->id_pelanggan,
            $pppoe->username,
            $pppoe->value,
            $pppoe->address,
            $pppoe->c_profile->name,
            $pppoe->payment_type,
            $pppoe->billing_period,
            $invoice->no_invoice,
            Carbon::parse($invoice->invoice_date)->translatedFormat('d F Y'),
            number_format($invoice->price, 0, ',', '.'),
            $invoice->ppn,
            $invoice->discount,
            number_format(($total + $invoice->price_adon), 0, ',', '.'),
            Carbon::parse($invoice->due_date)->translatedFormat('d F Y'),
            Carbon::parse($invoice->period)->translatedFormat('F Y'),
            $invoice->subscribe,
            $invoice->payment_url,
            implode(', ', $description_adon),
            number_format($invoice->price_adon,0,',','.'),
            number_format(($total), 0, ',', '.')
        ];

        $message_orig = str_replace($placeholders, $values, $template);
        $message = str_replace('<br>', "\n", $message_orig);


        // send email
        if($smtp){
            try{
                $data = [
                    'messages' => $message_orig,
                    'user_name' => $pppoe->username,
                    'notification' => 'Invoice Notification'
                ];
                app(CustomMailerService::class)->sendWithUserSmtpCron(
                    'emails.test',
                    $data,
                    $pppoe->email,
                    'Invoice',
                    $smtp
                );
                Log::info("[user:suspend] Success sending email to {$pppoe->username}: ");
            }catch (\Exception $e){
                Log::error("[user:suspend] Exception sending email to {$pppoe->username}: " . $e->getMessage());
            }
        }

        if($mpwa->mpwa_server_server == 'mpwa'){
            try {
                $response = Http::asForm()->post("https://{$mpwa->mpwa_server}/send-message", [
                    'api_key' => $mpwa->api_key,
                    'sender'  => $mpwa->sender,
                    'number'  => $pppoe->wa,
                    'message' => $message,
                ]);

                if (! $response->successful()) {
                    Log::error("[FixedDateInvoice][{$shortname}] WA failed ({$response->status()}) for {$pppoe->username}: {$response->body()}");
                }
            } catch (\Exception $e) {
                Log::error("[FixedDateInvoice][{$shortname}] Exception sending WA for {$pppoe->username}: " . $e->getMessage());
            }
        }

        if($mpwa->mpwa_server_server == 'radiusqu' && $mpwa->is_login == 1){
            $nomorhp = gantiformat_hp($pppoe->whatsapp);
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
                $response = Http::post($apiUrl, $params);
                // if($response->successful()){
                //     $json = $response->json();
                //     $status = $json->status;
                //     $receiver = $nomorhp;
                //     $shortname = $user_wa->shortname;
                //     save_wa_log($shortname,$receiver,$message,$status);
                // }

            } catch (\Exception $e) {
                Log::error("[user:suspend] Exception sending WA to {$user->username}: " . $e->getMessage());
            }
        }

        // Throttle requests to avoid overload
        sleep(5);
    }

}
