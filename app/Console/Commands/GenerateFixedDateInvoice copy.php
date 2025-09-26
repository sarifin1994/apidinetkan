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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenerateFixedDateInvoice extends Command
{
    protected $signature = 'invoice_copy:fixed-date';
    protected $description = 'Generate invoices for Fixed Date (run before n due date)';

    public function handle()
    {
        set_time_limit(0);
        $today = Carbon::now()->toDateString();
        $users = User::where('role', 'Admin')->whereIn('status', [1, 3])
            ->with('c_pppoe_fixed.c_profile')
            ->get();

        foreach ($users as $user) {
            $shortname = $user->shortname;
            $domain = $user->domain;
            // Ambil data setting satu kali
            $billingSetting = BillingSetting::where('shortname', $shortname)->first();
            $mpwa           = Mpwa::where('shortname', $shortname)->first();
            $watemplate     = Watemplate::where('shortname', $shortname)->first();

            if (!$billingSetting || !$watemplate) {
                Log::error("Data billling setting dan wa template tidak ada untuk shortname: {$shortname}");
                continue;
            }

            $inv_fd  = $billingSetting->inv_fd;
            $notif_it = $billingSetting->notif_it;
            $template = $watemplate->invoice_terbit;

            // Proses setiap pppoe fixed dari user
            foreach ($user->c_pppoe_fixed as $pppoe) {
                if ($pppoe->billing_period !== 'Fixed Date') {
                    continue;
                }

                try {
                    $targetDate = Carbon::createFromFormat('Y-m-d', $pppoe->next_invoice)
                        ->subDays($inv_fd)
                        ->toDateString();
                } catch (\Carbon\Exceptions\InvalidFormatException $e) {
                    // Jika format tanggal tidak sesuai, lewati iterasi saat ini
                    continue;
                }
                                

                if ($today !== $targetDate) {
                    continue;
                }

                // Inisialisasi variabel untuk invoice
                $periode       = null;
                $periode_format = null;
                $next_invoice  = null;
                $subscribe     = null;

                // Proses berdasarkan payment type
                if ($pppoe->payment_type === 'Prabayar') {
                    // Prabayar: ambil periode dari next_invoice (tanpa pengurangan bulan)
                    $periode = Carbon::createFromFormat('Y-m-d', $pppoe->next_invoice);
                    $periode_format = $periode->translatedFormat('F Y');
                    $next_invoice = $periode->copy()->addMonthsWithNoOverflow(1)->toDateString();

                    $periode_awal  = $periode->toDateString();
                    $periode_akhir = $periode->copy()->addMonthsWithNoOverflow(1)->toDateString();
                    $awal  = Carbon::parse($periode_awal)->format('d/m/Y');
                    $akhir = Carbon::parse($periode_akhir)->format('d/m/Y');
                    $subscribe = "{$awal} s.d {$akhir}";
                } elseif ($pppoe->payment_type === 'Pascabayar') {
                    // Pascabayar: periode dihitung dari next_invoice dikurangi 1 bulan
                    $periode = Carbon::createFromFormat('Y-m-d', $pppoe->next_invoice)
                        ->subMonthsWithNoOverflow(1);
                    $periode_format = $periode->translatedFormat('F Y');
                    $next_invoice = Carbon::createFromFormat('Y-m-d', $pppoe->next_invoice)
                        ->addMonthsWithNoOverflow(1)
                        ->toDateString();

                    $periode_awal  = $periode->toDateString();
                    // Untuk periode akhir, gunakan next_invoice sebagai batas (tanpa modifikasi lebih lanjut)
                    $periode_akhir = Carbon::createFromFormat('Y-m-d', $pppoe->next_invoice)->toDateString();
                    $awal  = Carbon::parse($periode_awal)->format('d/m/Y');
                    $akhir = Carbon::parse($periode_akhir)->format('d/m/Y');
                    $subscribe = "{$awal} s.d {$akhir}";
                } else {
                    // Jika tidak sesuai dengan kondisi Fixed Date, lewati
                    continue;
                }

                $item  = 'Internet: ' . $pppoe->username . ' | ' . $pppoe->c_profile->name;
                $price = $pppoe->c_profile->price;
                $no_invoice = date('m') . rand(10000000, 99999999);

                $invoice = Invoice::create([
                    'shortname'      => $user->shortname,
                    'id_pelanggan'   => $pppoe->id,
                    'no_invoice'     => $no_invoice,
                    'item'           => $item,
                    'price'          => $price,
                    'ppn'            => $pppoe->ppn,
                    'discount'       => $pppoe->discount,
                    'invoice_date'   => Carbon::now()->toDateString(),
                    'due_date'       => $pppoe->next_invoice,
                    'period'         => $periode, // bisa disimpan sebagai tanggal atau string
                    'subscribe'      => $subscribe,
                    'payment_type'   => $pppoe->payment_type,
                    'billing_period' => $pppoe->billing_period,
                    'payment_url'    => $domain . '/pay/' . $no_invoice,
                    'status'         => 'unpaid',
                    'mitra_id'       => $pppoe->mitra_id,
                    'komisi'         => $pppoe->c_profile->fee_mitra,
                ]);

                if ($invoice) {
                    // Update next_invoice untuk pppoe
                    $pppoe->update(['next_invoice' => $next_invoice]);

                    $amount_ppn = ($invoice->price * $invoice->ppn) / 100;
                    $amount_discount = ($invoice->price * $invoice->discount) / 100;
                    $total = $invoice->price + $amount_ppn - $amount_discount;

                    // Jika notifikasi diaktifkan dan nomor WA tersedia
                    if ($notif_it == 1 && !empty($pppoe->wa)) {
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
                            $periode_format,
                            $invoice->subscribe,
                            $invoice->payment_url
                        ];
                        $message = str_replace($shortcode, $source, $template);
                        $message_format = str_replace('<br>', "\n", $message);

                        if($mpwa->mpwa_server_server == 'mpwa'){
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
                                sleep(5); // Jeda 5 detik
                            } catch (\Exception $e) {
                                Log::error("Pesan gagal dikirim untuk shortname: {$user->shortname} - pppoe: {$pppoe->username}", ['error' => $e->getMessage()]);
                                continue;
                            }
                        }
                        if($mpwa->mpwa_server_server == 'radiusqu') {
                            $nomorhp = gantiformat_hp($pppoe->wa);
                            $user_wa = User::where('shortname', $mpwa->shortname)->first();
                            $_id = $user_wa->whatsapp."_".env('APP_ENV');
                            $apiUrl = env('WHATSAPP_URL_NEW')."send-message/".$_id; //env('CACTI_ENDPOINT').'cacti/logout/'.$_id;
                            try {
                                $params = array(
                                    "jid" => $nomorhp."@s.whatsapp.net",
                                    "content" => array(
                                        "text" => $message_format
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

                            }
                        }
                    }
                    Log::info("Invoice fixed date berhasil dibuat untuk shortname: {$user->shortname} - pppoe: {$pppoe->username}");
                } else {
                    Log::error("Invoice fixed date gagal dibuat untuk shortname: {$user->shortname} - pppoe: {$pppoe->username}");
                    continue;
                }
            }
            sleep(100);
        }
    }
}
