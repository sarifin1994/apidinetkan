<?php

namespace App\Console\Commands;

use App\Enums\UserStatusEnum;
use App\Enums\ServiceStatusEnum;
use App\Mail\EmailInvoiceNotif;
use App\Mail\TestEmail;
use App\Models\AdminDinetkanInvoice;
use App\Models\AdminInvoice;
use App\Models\LicenseDinetkan;
use App\Models\MappingAdons;
use App\Models\MappingUserLicense;
use App\Models\MasterMikrotik;
use App\Models\ServiceDetail;
use App\Models\SmtpSetting;
use App\Models\UserDinetkan;
use App\Models\UsersWhatsapp;
use App\Models\WatemplateDinetkan;
use App\Services\CustomMailerService;
use App\Settings\LicenseDinetkanSettings;
use App\Settings\SiteDinetkanSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Member;
use App\Models\Wablas;
use App\Models\PppoeMember;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\WablasMessage;
use App\Models\BillingSetting;
use App\Models\License;
use App\Models\WablasTemplate;
use App\Settings\LicenseSettings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use function Livewire\Features\SupportTesting\commit;
use Modules\Payments\Repositories\Contracts\LicenseDinetkanRepositoryInterface;
use Modules\Payments\Services\AdminDinetkanPaymentService;
use Modules\Payments\Services\AdminPaymentService;
use PHPUnit\Framework\Constraint\Count;
use App\Enums\DinetkanInvoiceStatusEnum;
use RouterOS\Client;
use RouterOS\Query;

class DinetkanSuspendServiceCycle extends Command
{
    protected $signature = 'dinetkan_suspend_service:check:cron';
    protected $description = 'Check Admin License Prabayar and Send Notification';
    public function handle(SiteDinetkanSettings $settings,AdminDinetkanPaymentService $adminPaymentService) {

        // Set timezone
        config(['app.timezone' => 'Asia/Jakarta']);
        date_default_timezone_set('Asia/Jakarta');
        DB::beginTransaction();
        try{
            // cari dlu semua user dinetkan
            $userdinetkans = UserDinetkan::where('is_dinetkan', 1)->get();
            $now = Carbon::now()->format('Y-m-d');
            foreach($userdinetkans as $user){
                $remember_day = 0;
                //case NEW = 0;
                //case UNPAID = 1;
                //case PAID = 2;
                //case CANCEL = 3;
                //case EXPIRED= 4;

                $services = MappingUserLicense::where('dinetkan_user_id', $user->dinetkan_user_id)->where('status', 1)->get();
                if(count($services) > 0){
                    foreach ($services as $service){
//                        if($service){
                            $adons = MappingAdons::where('id_mapping', $service->id)->where('monthly','yes')->get();
                            $total_price_ad_monthly = 0;
                            if(count($adons) > 0){
                                foreach ($adons as $adon){
                                    $totalPpnAd = 0;
                                    if($adon->ppn > 0){
                                        $totalPpnAd = $adon->ppn * ($adon->qty * $adon->price) / 100;
                                    }
                                    $total_price_ad_monthly = $total_price_ad_monthly + (($adon->qty * $adon->price) + $totalPpnAd);
                                }
                            }

                            // cari sudah ada invoice atau belum dengan status unpaid, jika belum maka buat invoice baru.
                            $invoices = AdminDinetkanInvoice::where('dinetkan_user_id', $user->dinetkan_user_id)
                                ->where('itemable_id', $service->license_id)
                                ->where('status', DinetkanInvoiceStatusEnum::UNPAID)
                                ->get();
                            if(count($invoices) > 0 && count($invoices) < 2){
                                foreach ($invoices as $invoice){
                                    if($service->status == ServiceStatusEnum::ACTIVE){
                                        $maxdateinv = Carbon::parse($service->due_date)->format('Y-m-d');
                                        if($invoice->status == DinetkanInvoiceStatusEnum::UNPAID){
                                            if($now == $maxdateinv){
                                                // update service jadi suspend ServiceStatusEnum::SUSPEND
                                                $service->update([
                                                    'due_date' => Carbon::parse($service->due_date)->addMonthsWithNoOverflow(1)
                                                ]);
                                            }
                                        }
                                        DB::commit();
                                        $today = Carbon::now();
                                        $watemplate = WatemplateDinetkan::firstWhere('shortname', 'dinetkan');
                                        $template = $watemplate->invoice_overdue;
                                        $smtp = SmtpSetting::firstWhere('shortname', 'dinetkan');
                                        // $this->processSingleInvoice($user, $invoice, $today, $template, $smtp);
                                    }
                                }
                            }

                            if(count($invoices) >= 2){
                                foreach ($invoices as $invoice){
                                    if($service->status == ServiceStatusEnum::ACTIVE){
                                        $maxdateinv = Carbon::parse($service->due_date)->format('Y-m-d');
                                        if($invoice->status == DinetkanInvoiceStatusEnum::UNPAID){
                                            if($now == $maxdateinv){
                                                // update service jadi suspend ServiceStatusEnum::SUSPEND
                                                $service->update([
                                                    'status' => ServiceStatusEnum::SUSPEND
                                                ]);
                                                $this->disabled_vlan($service->service_id);
                                            }
                                        }
                                        DB::commit();
                                        $today = Carbon::now();
                                        $watemplate = WatemplateDinetkan::firstWhere('shortname', 'dinetkan');
                                        $template = $watemplate->invoice_overdue;
                                        $smtp = SmtpSetting::firstWhere('shortname', 'dinetkan');
                                        // $this->processSingleInvoice($user, $invoice, $today, $template, $smtp);
                                    }
                                }
                            }
                            if(count($invoices) <= 0){
                                $maxdate = Carbon::parse($service->due_date)->format('Y-m-d');//->addDays($remember_day);
                                $maxdateformat = Carbon::parse($maxdate)->format('Y-m-d');
                                if($now == $maxdate){
//                                    if($service->status != ServiceStatusEnum::ACTIVE){

                                        $service->update([
                                            'status' => ServiceStatusEnum::SUSPEND
                                        ]);

                                        if($service->payment_method == 'prabayar'){
                                            $type = "prabayar";
                                            $periodStart = \Illuminate\Support\Carbon::parse($service->due_date);
                                            $periodEnd = Carbon::parse($service->due_date)->addMonthsWithNoOverflow(1)->format('Y-m-d');
                                            $subscribe = Carbon::parse($periodStart)->format('d/m/Y') . ' s/d ' . Carbon::parse($periodEnd)->format('d/m/Y');
                                        }
                                        if($service->payment_method == 'pascabayar'){
                                            $type = "pascabayar";
                                            $periodStart = \Illuminate\Support\Carbon::parse($service->due_date);
                                            $periodEnd = Carbon::parse($service->due_date)->addMonthsWithNoOverflow(1)->format('Y-m-d');
                                            $subscribe = Carbon::parse($periodStart)->format('d/m/Y') . ' s/d ' . Carbon::parse($periodEnd)->format('d/m/Y');
                                        }
                                        if($service->prorata == "yes"){}
                                        if($service->prorata == "no"){}
                                        // create mapping service

                                        $adons = MappingAdons::where('id_mapping', $service->id)->where('monthly','yes')->get();
                                        $total_price_ad_monthly = 0;
                                        if(count($adons) > 0){
                                            foreach ($adons as $adon){
                                                $totalPpnAd = 0;
                                                if($adon->ppn > 0){
                                                    $totalPpnAd = $adon->ppn * ($adon->qty * $adon->price) / 100;
                                                }
                                                $total_price_ad_monthly = $total_price_ad_monthly + (($adon->qty * $adon->price) + $totalPpnAd);
                                            }
                                        }
                                        $invoice = null;
                                        $license = LicenseDinetkan::where('id', $service->license_id)->first();
                                        if($license){
                                            $noInvoice = date('m') . rand(0000000, 9999999);
//                                            $invoice = AdminDinetkanInvoice::create([
//                                                'group_id'              => $user->id,
//                                                'itemable_id'           => $license->id,
//                                                'itemable_type'         => LicenseDinetkan::class,
//                                                'no_invoice'            => $noInvoice,
//                                                'item'                  => 'Service : ' . $license->name,
//                                                'price'                 => $license->price,
//                                                'price_adon'            => 0,
//                                                'price_adon_monthly'    => $total_price_ad_monthly,
//                                                'ppn'                   => $license->ppn,
//                                                'total_ppn'             => ($license->price * $license->ppn) / 100,
//                                                'fee'                   => 0,
//                                                'discount'              => 0,
//                                                'discount_coupon'       => 0,//$priceData->discountCoupon,
//                                                'invoice_date'          => Carbon::now(),
//                                                'period'                => $periodEnd,
//                                                'subscribe'             => $subscribe,
//                                                'payment_type'          => $service->payment_method ? $service->payment_method : '',
//                                                'billing_period'        => $service->payment_siklus ? $service->payment_siklus : 1,
//                                                'payment_url'           => route('admin.invoice_dinetkan', $noInvoice),
//                                                'status'                => DinetkanInvoiceStatusEnum::UNPAID,
//                                                'dinetkan_user_id'      => $user->dinetkan_user_id,
//                                                'id_mapping'            => $service->id,
//                                                'due_date'              =>  Carbon::now()->addDays(5)
//                                            ]);
                                        }
//                                    }
                                }
                            }
//                        }
                        DB::commit();
                        $this->disabled_vlan($service->service_id);
                    }
                }
            }
        }catch (\Exception $ex){
            Log::error($ex->getMessage());
        }
    }

    protected function disabled_vlan($service_id){
        $service = ServiceDetail::where('service_id', $service_id)->first();
        if($service){
            if($service->id_mikrotik != null){
                $mikrotik = MasterMikrotik::where('id', $service->id_mikrotik)->first();
                if($mikrotik){
                    if( $mikrotik->ip != null){
                        $client = new Client([
                            'host' => $mikrotik->ip,
                            'user' => $mikrotik->username,
                            'pass' => $mikrotik->password,
                            'port' => $mikrotik->port, // port API Mikrotik kamu
                            'timeout' => 10,
                        ]);
                        $query = new Query('/interface/vlan/set');
                        $query->equal('.id', $service->vlan_id);  // Ganti *F dengan ID VLAN yang ingin diubah
                        $query->equal('disabled', 'yes');  // 'no' untuk enable
                        $hasil = $client->query($query)->read();
//        return response()->json($hasil);
                    }
                }
            }
        }
    }


    protected function processSingleInvoice($user, $invoice, $today, $template, $smtp)
    {
        $placeholders = [
            '[nama_lengkap]', '[id_pelanggan]', '[username]', '[password]', '[alamat]', '[paket_internet]',
            '[tipe_pembayaran]', '[siklus_tagihan]', '[no_invoice]', '[tgl_invoice]',
            '[harga]', '[ppn]', '[discount]', '[total]', '[jth_tempo]', '[periode]', '[subscribe]', '[link_pembayaran]'
        ];
        $total = $invoice->price + $invoice->total_ppn + $invoice->price_adon_monthly;
        $values = [
            $user->name,
            $user->dinetkan_user_id,
            $user->username,
            "",
            $user->address,
            $invoice->item,
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
        $user_dinetkan = User::where('shortname', 'dinetkan')->first();
        $mpwa = UsersWhatsapp::where('user_id', $user_dinetkan->id)->first();
        if($mpwa){
            $nomorhp = gantiformat_hp($user->whatsapp);
            $_id = $user_dinetkan->whatsapp."_".env('APP_ENV');
            $apiUrl = env('WHATSAPP_URL_NEW')."send-message/".$_id; //env('CACTI_ENDPOINT').'cacti/logout/'.$_id;
            try {
                $params = array(
                    "jid" => $nomorhp."@s.whatsapp.net",
                    "content" => array(
                        "text" => $message
                    )
                );
                // Kirim POST request ke API eksternal
                $response = Http::timeout(10)->post($apiUrl, $params);
                // if($response->successful()){
                //     $json = $response->json();
                //     $status = $json->status;
                //     $receiver = $nomorhp;
                //     $shortname = $user_dinetkan->shortname;
                //     save_wa_log($shortname,$receiver,$message,$status);
                // }
//                Log::info("Invoice cycle suspend running");

            } catch (\Exception $e) {
                Log::error("[SuspendInvoice] Exception sending WA for invoice {$invoice->id}: " . $e->getMessage());
            }
        }

        // send email
        if($smtp){
            try{
                $data = [
                    'messages' => $message_orig,
                    'user_name' => $user->username,
                    'notification' => 'Reminder Invoice Notification'
                ];
                app(CustomMailerService::class)->sendWithUserSmtpCron(
                    'emails.test',
                    $data,
                    $user->email,
                    'Invoice',
                    $smtp
                );
//                Log::info("[invoice:reminder-suspend] Success sending email to {$user->username}: ");
            }catch (\Exception $e){
                Log::error("[invoice:reminder-suspend] Exception sending email to {$user->username}: " . $e->getMessage());
            }
        }



        // Throttle requests
//        sleep(5);
    }

    function gantiformat($nomorhp) {
        //Terlebih dahulu kita trim dl
        $nomorhp = trim($nomorhp);
        //bersihkan dari karakter yang tidak perlu
        $nomorhp = strip_tags($nomorhp);
        // Berishkan dari spasi
        $nomorhp= str_replace(" ","",$nomorhp);
        // bersihkan dari bentuk seperti  (022) 66677788
        $nomorhp= str_replace("(","",$nomorhp);
        // bersihkan dari format yang ada titik seperti 0811.222.333.4
        $nomorhp= str_replace(".","",$nomorhp);

        //cek apakah mengandung karakter + dan 0-9
        if(!preg_match('/[^+0-9]/',trim($nomorhp))){
            // cek apakah no hp karakter 1-3 adalah +62
            if(substr(trim($nomorhp), 0, 2)=='62'){
                $nomorhp= trim($nomorhp);
            }
            // cek apakah no hp karakter 1 adalah 0
            elseif(substr($nomorhp, 0, 1)=='0'){
                $nomorhp= '62'.substr($nomorhp, 1);
            }
        }
        return $nomorhp;
    }
}
