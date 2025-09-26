<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Invoice\Invoice;
use App\Models\Pppoe\PppoeUser;
use App\Models\Setting\BillingSetting;
use App\Models\Whatsapp\Mpwa;
use App\Models\Whatsapp\Watemplate;
use Illuminate\Support\Facades\Log;

class ReminderInvoice extends Command
{
    protected $signature = 'invoice_copy:reminder-notification';
    protected $description = 'Reminder notification for invoices unpaid';

    public function handle()
    {
        set_time_limit(0);
        $today = Carbon::now()->toDateString();
        $users = User::where('role', 'Admin')->whereIn('status', [1, 3])
            ->with('c_invoice.c_pppoe')
            ->get();

        foreach ($users as $user) {
            $shortname = $user->shortname;

            // Ambil data setting sekali per user
            $billingSetting = BillingSetting::where('shortname', $shortname)->first();
            $mpwa           = Mpwa::where('shortname', $shortname)->first();
            $watemplate     = Watemplate::where('shortname', $shortname)->first();

            if (!$billingSetting || !$watemplate) {
                Log::error("Data billling setting dan wa template tidak ada untuk shortname: {$shortname}");
                continue;
            }

            $notif_reminder = $billingSetting->notif_ir;
            $template       = $watemplate->invoice_reminder;

            // Proses hanya jika notifikasi reminder diaktifkan
            if ($notif_reminder == 0) {
                continue;
            }

            foreach ($user->c_invoice as $invoice) {
                // Hitung target date: due_date dikurangi notif_reminder hari
                try {
                    $targetDate = Carbon::createFromFormat('Y-m-d', $invoice->due_date)
                        ->subDays($notif_reminder)
                        ->toDateString();
                } catch (\Exception $e) {
                    Log::error("Gagal parsing due_date untuk invoice ID {$invoice->id}: " . $e->getMessage());
                    continue;
                }

                if ($today !== $targetDate || $invoice->status !== 'unpaid') {
                    continue;
                }

                // Hitung total invoice: price + (price * ppn/100) - (price * discount/100)
                $amount_ppn = ($invoice->price * $invoice->ppn) / 100;
                $amount_discount = ($invoice->price * $invoice->discount) / 100;
                $total = $invoice->price + $amount_ppn - $amount_discount;

                // Pastikan nomor WA tersedia. Gunakan relasi c_pppoe (sesuaikan dengan model Anda)
                $waNumber = $invoice->c_pppoe->wa ?? null;
                if ($waNumber) {
                    $shortcode = [
                        '[nama_lengkap]', '[id_pelanggan]', '[username]','[password]','[alamat]', '[paket_internet]',
                        '[tipe_pembayaran]', '[siklus_tagihan]', '[no_invoice]', '[tgl_invoice]',
                        '[harga]', '[ppn]', '[discount]', '[total]', '[jth_tempo]', '[periode]', '[subscribe]','[link_pembayaran]'
                    ];
                    $source = [
                        $invoice->c_pppoe->full_name,
                        $invoice->c_pppoe->id_pelanggan,
                        $invoice->c_pppoe->username,
                        $invoice->c_pppoe->value,
                        $invoice->c_pppoe->address,
                        $invoice->c_pppoe->profile,
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
                    $message = str_replace($shortcode, $source, $template);
                    $message_format = str_replace('<br>', "\n", $message);

                    try {
                        $curl = curl_init();
                        $data = [
                            'api_key' => $mpwa->api_key,
                            'sender'  => $mpwa->sender,
                            'number'  => $waNumber,
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
                        Log::error("Pesan gagal dikirim untuk user: {$shortname}, invoice ID {$invoice->id}", ['error' => $e->getMessage()]);
                        continue;
                    }
                }

                Log::info("Notifikasi reminder berhasil dikirim untuk user: {$shortname}, invoice ID {$invoice->id}");
            }
        }
    }
}
