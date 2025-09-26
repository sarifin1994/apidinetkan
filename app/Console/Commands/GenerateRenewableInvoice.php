<?php

namespace App\Console\Commands;

use App\Models\MappingAdons;
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

class GenerateRenewableInvoice extends Command
{
    protected $signature = 'invoice:renewable';
    protected $description = 'Generate invoices for renewable PPPoE users before due date';

    public function handle()
    {
        // Set timezone
        config(['app.timezone' => 'Asia/Jakarta']);
        date_default_timezone_set('Asia/Jakarta');

        // Process in chunks
        User::where('role', 'Admin')
            ->whereIn('status', [1, 3])
            ->where('is_dinetkan','0')
            ->with(['c_pppoe_renewable.c_profile'])
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
        $template = $watemplate->invoice_terbit;
        $today = Carbon::today()->toDateString();

        foreach ($user->c_pppoe_fixed as $pppoe) {
            if ($pppoe->billing_period !== 'Fixed Date' || empty($pppoe->next_invoice)) {
                continue;
            }

            // Calculate target date
            $targetDate = $this->calculateTargetDate($pppoe->next_invoice, $invFdDays);
            if ($targetDate !== $today) {
                continue;
            }

            $invoiceData = $this->prepareInvoiceData($pppoe);
            $invoiceNumber = $this->generateInvoiceNumber();

            // Create invoice
            $invoice = app(\App\Models\Invoice\Invoice::class)::create(array_merge(
                $invoiceData,
                [
                    'shortname'      => $shortname,
                    'id_pelanggan'   => $pppoe->id,
                    'no_invoice'     => $invoiceNumber,
                    'item'           => "Internet: {$pppoe->username} | {$pppoe->c_profile->name}",
                    'price'          => $pppoe->c_profile->price,
                    'ppn'            => $pppoe->ppn,
                    'discount'       => $pppoe->discount,
                    'invoice_date'   => $today,
                    'due_date'       => $pppoe->next_invoice,
                    'period'         => $invoiceData['period'],
                    'subscribe'      => $invoiceData['subscribe'],
                    'payment_type'   => $pppoe->payment_type,
                    'billing_period' => $pppoe->billing_period,
                    'payment_url'    => $domain . '/pay/' . $invoiceNumber,
                    'status'         => 'unpaid',
                    'mitra_id'       => $pppoe->mitra_id,
                    'komisi'         => $pppoe->c_profile->fee_mitra,
                ]
            ));

            if (! $invoice) {
                Log::error("[FixedDateInvoice][{$shortname}] Failed to create invoice for {$pppoe->username}");
                continue;
            }


            $cekmapping = MappingAdons::query()->where('id_pelanggan_pppoe', $pppoe->id_pelanggan)->where('monthly', 'Yes') ->get();
            if(count($cekmapping) > 0){
                $total_price_ad = 0;
                $total_price_ad_monthly=0;
                foreach ($cekmapping as $mapp)
                    $mappingadons = MappingAdons::create(
                        [
                            'id_mapping' => 0,
                            'description' => $mapp->description,
                            'ppn' => $mapp->ppn,
                            'monthly' => $mapp->monthly,
                            'qty' => $mapp->qty,
                            'price' => $mapp->price,
                            'no_invoice' => $mapp->no_invoice,
                            'id_pelanggan_pppoe' => $mapp->id_pelanggan_pppoe
                        ]);
                $totalPpnAd = 0;
                if($mapp->ppn > 0){
                    $totalPpnAd = $mapp->ppn * ($mapp->qty * $mapp->price) / 100;
                }
                $total_price_ad = $total_price_ad + (($mapp->qty * $mapp->price) + $totalPpnAd);

                if($mapp->monthly == "Yes"){
                    $total_price_ad_monthly = $total_price_ad_monthly + (($mapp->qty * $mapp->price) + $totalPpnAd);
                }
                $invoice->update([
                    'price_adon_monthly' => $total_price_ad_monthly,
                    'price_adon' => $total_price_ad
                ]);
            }

            // Update next_invoice on PPPoE
            $pppoe->update(['next_invoice' => $invoiceData['next_invoice']]);

            // Send WA notification if enabled
            if ($notifIt && ! empty($pppoe->wa)) {
                $this->sendWaNotification($pppoe, $invoice, $template, $mpwa, $shortname, $smtp);
            }

            Log::info("[FixedDateInvoice][{$shortname}] Invoice generated for {$pppoe->username}");
        }
    }

    protected function calculateTargetDate(string $nextInvoice, int $invFdDays): string
    {
        try {
            return Carbon::createFromFormat('Y-m-d', $nextInvoice)
                ->subDays($invFdDays)
                ->toDateString();
        } catch (\Exception $e) {
            return '';
        }
    }

    protected function prepareInvoiceData($pppoe): array
    {
        $date = Carbon::createFromFormat('Y-m-d', $pppoe->next_invoice);
        $paymentType = $pppoe->payment_type;

        if ($paymentType === 'Prabayar') {
            $periodStart = $date;
            $periodEnd = $date->copy()->addMonthNoOverflow();
        } else {
            $periodStart = $date->copy()->subMonthNoOverflow();
            $periodEnd = $date;
        }

        $subscribe = $periodStart->format('d/m/Y') . ' s.d ' . $periodEnd->format('d/m/Y');

        return [
            'period'       => $periodStart,
            'subscribe'    => $subscribe,
            'next_invoice' => $date->copy()->addMonthNoOverflow()->toDateString(),
        ];
    }

    protected function generateInvoiceNumber(): string
    {
//        return now()->format('m') . rand(10000000, 99999999);
        return build_no_invoice('RQ');
    }

    protected function sendWaNotification($pppoe, $invoice, string $template, Mpwa $mpwa, string $shortname, SmtpSetting $smtp)
    {
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
            $pppoe->payment_type,
            $pppoe->billing_period,
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
                Log::error("[FixedDateInvoice][{$shortname}] Exception sending WA for {$pppoe->username}: " . $e->getMessage());
            }

        }

        // Throttle requests to avoid overload
        sleep(5);
    }
}
