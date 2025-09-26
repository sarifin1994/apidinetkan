<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Invoice\Invoice;
use App\Models\Setting\BillingSetting;
use App\Models\Whatsapp\Mpwa;
use App\Models\Whatsapp\Watemplate;
use App\Models\Radius\RadiusNas;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SuspendPppoeUser extends Command
{
    protected $signature = 'pppoe:suspend';
    protected $description = 'Suspend users whose invoices are overdue and disconnect them via RADIUS';

    public function handle()
    {
        set_time_limit(0);
        // Pastikan timezone Asia/Jakarta
        config(['app.timezone' => 'Asia/Jakarta']);
        date_default_timezone_set('Asia/Jakarta');

        // Ambil kredensial dan opsi SSH
        $sshUser    = env('IP_RADIUS_USERNAME');
        $sshHost    = env('IP_RADIUS_SERVER');
        $sshOptions = [
            '-o', 'BatchMode=yes',
            '-o', 'StrictHostKeyChecking=no',
        ];

        $radPort    = env('RADIUS_DISCONNECT_PORT', 3799);
        $radRetries = env('RADIUS_DISCONNECT_RETRIES', 1);

        User::where('role', 'Admin')
            ->whereIn('status', [1, 3])
            ->with('c_pppoe1.c_profile')
            ->chunkById(100, function($users) use ($sshUser, $sshHost, $sshOptions, $radPort, $radRetries) {
                foreach ($users as $user) {
                    $this->processUser($user, $sshUser, $sshHost, $sshOptions, $radPort, $radRetries);
                }
            });
    }

    protected function processUser($user, $sshUser, $sshHost, array $sshOptions, $radPort, $radRetries)
    {
        $shortname = $user->shortname;
        $billing   = BillingSetting::firstWhere('shortname', $shortname);
        $template  = Watemplate::firstWhere('shortname', $shortname)->invoice_overdue ?? null;
        $mpwa      = Mpwa::firstWhere('shortname', $shortname);
        $nasList   = RadiusNas::where('shortname', $shortname)->get(['nasname','secret']);

        if (! $billing || ! $template || $nasList->isEmpty()) {
            // Log::error("[{$shortname}] missing config or template or NAS list");
            return;
        }

        $today         = Carbon::today()->toDateString();
        $suspendDays   = (int) $billing->suspend_date;
        $notifyEnabled = (bool) $billing->notif_sm;

        foreach ($user->c_pppoe1 as $pppoe) {
            if (empty($pppoe->next_due)) continue;

            $targetDate = Carbon::parse($pppoe->next_due)
                ->addDays($suspendDays)
                ->toDateString();
            if ($today !== $targetDate) continue;

            $invoice = Invoice::where([
                ['shortname',    $shortname],
                ['id_pelanggan', $pppoe->id],
                ['status',       'unpaid'],
            ])->first();
            if (! $invoice) continue;

            // Suspend PPPoE
            $pppoe->update(['status' => 2]);

            // Disconnect via RADIUS over SSH with options
            $this->disconnectFromNas(
                $pppoe->username,
                $pppoe->nas,
                $nasList,
                $sshUser,
                $sshHost,
                $sshOptions,
                $radPort,
                $radRetries
            );

            // Kirim WA jika perlu
            if ($notifyEnabled && ! empty($pppoe->wa)) {
                $this->sendWaNotification($pppoe, $invoice, $template, $mpwa, $shortname);
            }

            Log::info("[{$shortname}] Suspended and processed PPPoE: {$pppoe->username}");
        }
    }

    protected function disconnectFromNas(
        $username,
        $nasName,
        $nasList,
        $sshUser,
        $sshHost,
        array $sshOptions,
        $port,
        $retries
    ) {
        $userAttr = "User-Name={$username}";
        $targets  = $nasName ? $nasList->where('nasname', $nasName) : $nasList;

        if ($targets->isEmpty()) {
            Log::error("No NAS targets for user: {$username}");
            return;
        }

        foreach ($targets as $nas) {
            $cmdString = sprintf(
                'printf "%s\n" | radclient -r %d %s:%d disconnect %s',
                $userAttr,
                $retries,
                $nas->nasname,
                $port,
                $nas->secret
            );

            // Build SSH command with options
            $cmd = array_merge(
                ['ssh'],
                $sshOptions,
                ["{$sshUser}@{$sshHost}", $cmdString]
            );

            try {
                $process = Process::run($cmd);
                if ($process->successful()) {
                    Log::info("disconnect success for {$username}@{$nas->nasname}");
                } else {
                    // Log::error("disconnect failed for {$username}@{$nas->nasname}", [
                    //     'exit'   => $process->exitCode(),
                    //     'stderr' => trim($process->errorOutput()),
                    // ]);
                }
            } catch (\Exception $e) {
                Log::error("Exception disconnecting {$username}@{$nas->nasname}: " . $e->getMessage());
            }
        }
    }

    protected function sendWaNotification($pppoe, $invoice, $template, $mpwa, $shortname)
    {
        $amountPpn      = ($invoice->price * $invoice->ppn) / 100;
        $amountDiscount = $invoice->discount;
        $total          = $invoice->price + $amountPpn - $amountDiscount;

        $placeholders = [
            '[nama_lengkap]', '[id_pelanggan]', '[username]', '[password]',
            '[alamat]', '[paket_internet]', '[tipe_pembayaran]', '[siklus_tagihan]',
            '[no_invoice]', '[tgl_invoice]', '[harga]', '[ppn]', '[discount]',
            '[total]', '[jth_tempo]', '[periode]', '[subscribe]', '[link_pembayaran]'
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

        $message = str_replace($placeholders, $values, $template);
        $message = str_replace('<br>', "\n", $message);

        if($mpwa->mpwa_server_server == 'mpwa'){
            try {
                $response = Http::post(
                    "https://{$mpwa->mpwa_server}/send-message",
                    [
                        'api_key' => $mpwa->api_key,
                        'sender'  => $mpwa->sender,
                        'number'  => $pppoe->wa,
                        'message' => $message,
                    ]
                );

                if ($response->status() !== 200) {
                    Log::error("WA failed ({$shortname}): HTTP {$response->status()}", ['body' => $response->body()]);
                }
            } catch (\Exception $e) {
                Log::error("Exception sending WA for {$shortname}-{$pppoe->username}: " . $e->getMessage());
            }
        }
        if($mpwa->mpwa_server_server == 'radiusqu'){
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
                Log::error("Exception sending WA for {$shortname}-{$pppoe->username}: " . $e->getMessage());
            }
        }
    }
}
