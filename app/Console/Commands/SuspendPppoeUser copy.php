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

class SuspendPppoeUser extends Command
{
    protected $signature = 'pppoe:suspend';
    protected $description = 'Suspend user pppoe';

    public function handle()
    {
        set_time_limit(0);
        $ssh_user = env('IP_RADIUS_USERNAME');
        $ssh_host = env('IP_RADIUS_SERVER');
        $today = Carbon::now()->toDateString();
        $users = User::where('role', 'Admin')->whereIn('status', [1, 3])
            ->with('c_pppoe1.c_profile')
            ->get();

        foreach ($users as $user) {
            $shortname = $user->shortname;

            // Ambil data konfigurasi satu kali per user
            $billingSetting = BillingSetting::where('shortname', $shortname)->first();
            $mpwa = Mpwa::where('shortname', $shortname)->first();
            $watemplate = Watemplate::where('shortname', $shortname)->first();

            if (!$billingSetting || !$watemplate) {
                Log::error("Data billling setting dan wa template tidak ada untuk shortname: {$shortname}");
                continue;
            }

            $sd = $billingSetting->suspend_date;
            $notif_sm = $billingSetting->notif_sm;
            $template = $watemplate->invoice_overdue;
            // Ambil list NAS sekali per user
            $nasList = RadiusNas::where('shortname', $shortname)
                ->select('nasname', 'secret')
                ->get();

            foreach ($user->c_pppoe1 as $pppoe) {
                // Jika next_due tersedia, targetDate adalah next_due + suspend_date hari
                $targetDate = !empty($pppoe->next_due)
                    ? Carbon::createFromFormat('Y-m-d', $pppoe->next_due)
                        ->addDays($sd)
                        ->toDateString()
                    : null;

                // Proses suspend hanya jika hari ini sama dengan target date
                if ($today === $targetDate) {
                    // Cek apakah terdapat invoice unpaid untuk pppoe ini
                    $invoice = Invoice::where('shortname', $pppoe->shortname)
                        ->where('id_pelanggan', $pppoe->id)
                        ->where('status', 'unpaid')
                        ->first();

                    if ($invoice) {
                        // Update status menjadi suspended (2)
                        $pppoe->update(['status' => 2]);

                        // Jalankan perintah disconnect menggunakan radclient
                        try {
                            if (is_null($pppoe->nas)) {
                                foreach ($nasList as $nas) {
                                    $userAttr = escapeshellarg("User-Name = '{$pppoe->username}'");
                                    $commandStr = "echo $userAttr | radclient -r 1 {$nas->nasname}:3799 disconnect {$nas->secret}";
                                    $ssh_command = "ssh {$ssh_user}@{$ssh_host} \"{$commandStr}\"";
                                    $process = Process::run($ssh_command);
                                    \Log::info('Hasil eksekusi SSH disconnect:', [
                                        'output' => $process->output(),
                                        'success' => $process->successful(),
                                    ]);    
                                }
                            } else {
                                $radiusNas = RadiusNas::where('shortname', $shortname)
                                    ->where('nasname', $pppoe->nas)
                                    ->select('secret')
                                    ->first();
                                if ($radiusNas) {
                                    $userAttr = escapeshellarg("User-Name = '{$pppoe->username}'");
                                    $commandStr = "echo $userAttr | radclient -r 1 {$pppoe->nas}:3799 disconnect {$radiusNas->secret}";
                                    $ssh_command = "ssh {$ssh_user}@{$ssh_host} \"{$commandStr}\"";
                                    $process = Process::run($ssh_command);
                                    \Log::info('Hasil eksekusi SSH disconnect:', [
                                        'output' => $process->output(),
                                        'success' => $process->successful(),
                                    ]);
                                } else {
                                    Log::error("Data RadiusNas tidak ditemukan untuk NAS {$pppoe->nas} dengan shortname {$shortname}");
                                }
                            }
                        } catch (\Exception $e) {
                            Log::error("Exception saat menjalankan radclient untuk {$pppoe->username}: " . $e->getMessage());
                        }

                        // Hitung total invoice: harga + PPN - diskon
                        $amount_ppn = ($invoice->price * $invoice->ppn) / 100;
                        $amount_discount = ($invoice->price * $invoice->discount) / 100;
                        $total = $invoice->price + $amount_ppn - $amount_discount;

                        // Kirim notifikasi WA jika diaktifkan dan nomor WA tersedia
                        if ($notif_sm === 1 && !empty($pppoe->wa)) {
                            $shortcode = [
                                '[nama_lengkap]', '[id_pelanggan]', '[username]','[password]','[alamat]', '[paket_internet]',
                                '[tipe_pembayaran]', '[siklus_tagihan]', '[no_invoice]', '[tgl_invoice]',
                                '[harga]', '[ppn]', '[discount]', '[total]', '[jth_tempo]', '[periode]', '[subscribe]','[link_pembayaran]'
                            ];
                            $source = [
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
                                $invoice->payment_url
                            ];
                            $message = str_replace($shortcode, $source, $template);
                            $message_format = str_replace('<br>', "\n", $message);

                            try {
                                $curl = curl_init();
                                $data = [
                                    'api_key' => $mpwa->api_key,
                                    'sender'  => $mpwa->sender,
                                    'number'  => $pppoe->wa,
                                    'message' => $message_format,
                                ];
                                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                                curl_setopt($curl, CURLOPT_URL, 'https://' . $mpwa->mpwa_server . '/send-message');
                                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                                curl_exec($curl);
                                curl_close($curl);
                                sleep(5); // Jeda 5 detik untuk menghindari overload
                            } catch (\Exception $e) {
                                Log::error("Pesan WA gagal dikirim untuk {$shortname} - pppoe: {$pppoe->username}", ['error' => $e->getMessage()]);
                                continue;
                            }
                        }
                    }
                    Log::info("Suspend pppoe user berhasil untuk {$shortname} - pppoe: {$pppoe->username}");
                }
            }
        }
    }
}
