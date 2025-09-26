<?php

namespace App\Console\Commands;

use App\Enums\UserStatusEnum;
use App\Mail\EmailInvoiceNotif;
use App\Mail\TestEmail;
use App\Models\AdminDinetkanInvoice;
use App\Models\AdminInvoice;
use App\Models\LicenseDinetkan;
use App\Models\MappingAdons;
use App\Models\MappingUserLicense;
use App\Models\SmtpSetting;
use App\Models\UserDinetkan;
use App\Models\UsersWhatsapp;
use App\Models\WatemplateDinetkan;
use App\Models\Whatsapp\Mpwa;
use App\Models\Whatsapp\Watemplate;
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

class DinetkanInvoiceServiceCycle extends Command
{
    protected $signature = 'dinetkan_invoice_service:check:cron';
    protected $description = 'Check Admin License Prabayar and Send Notification';

    public function handle(SiteDinetkanSettings $settings,AdminDinetkanPaymentService $adminPaymentService) {

        // Set timezone to Asia/Jakarta
        config(['app.timezone' => 'Asia/Jakarta']);
        date_default_timezone_set('Asia/Jakarta');
        DB::beginTransaction();
        try{
            // cari dlu semua user dinetkan
            $userdinetkans = UserDinetkan::where('is_dinetkan', 1)
                ->get();
            $now   = Carbon::now();//->addMonthsWithNoOverflow(1);
            foreach($userdinetkans as $user){
                $remember_day = 5;
                if($user->remember_day > 0){
                    $remember_day = $user->remember_day;
                }
                //case NEW = 0;
                //case UNPAID = 1;
                //case PAID = 2;
                //case CANCEL = 3;
                //case EXPIRED= 4;

                $services = MappingUserLicense::where('dinetkan_user_id', $user->dinetkan_user_id)->where('status', 1)->get();
                if(count($services) > 0){
                    $periodStart = "";
                    $periodEnd = "";
                    $subscribe = "";
                    foreach ($services as $service){
                        $maxdate = Carbon::parse($service->due_date)->addDays($remember_day);
                        $mindate = Carbon::parse($service->due_date)->subDays($remember_day);
                        if($now >= $mindate && $now <= $maxdate){
                            if($service){
                                // cari sudah ada invoice atau belum dengan status unpaid, jika belum maka buat invoice baru.
                                $invoice = AdminDinetkanInvoice::where('dinetkan_user_id', $user->dinetkan_user_id)
                                    ->where('itemable_id', $service->license_id)
                                    ->where('status', 1)
                                    ->get();
                                if(count($invoice) < 2){
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
                                        $invoice = AdminDinetkanInvoice::create([
                                            'group_id'              => $user->id,
                                            'itemable_id'           => $license->id,
                                            'itemable_type'         => LicenseDinetkan::class,
                                            'no_invoice'            => $noInvoice,
                                            'item'                  => 'Service : ' . $license->name,
                                            'price'                 => $license->price,
                                            'price_adon'            => 0,
                                            'price_adon_monthly'    => $total_price_ad_monthly,
                                            'ppn'                   => $license->ppn,
                                            'total_ppn'             => ($license->price * $license->ppn) / 100,
                                            'fee'                   => 0,
                                            'discount'              => 0,
                                            'discount_coupon'       => 0,//$priceData->discountCoupon,
                                            'invoice_date'          => Carbon::now(),
                                            'due_date'              => null, //$dueDate->format('Y-m-d'),
                                            'period'                => $periodEnd,
                                            'subscribe'             => $subscribe,
                                            'payment_type'          => $service->payment_method ? $service->payment_method : '',
                                            'billing_period'        => $service->payment_siklus ? $service->payment_siklus : 1,
                                            'payment_url'           => route('admin.invoice_dinetkan', $noInvoice),
                                            'status'                => DinetkanInvoiceStatusEnum::UNPAID,
                                            'dinetkan_user_id'      => $user->dinetkan_user_id,
                                            'id_mapping'            => $service->id,
                                            'due_date'              => Carbon::parse($service->due_date)->addDays($remember_day)
                                        ]);
                                        DB::commit();
                                        $today = Carbon::now();
                                        $watemplate = WatemplateDinetkan::firstWhere('shortname', 'dinetkan');
                                        $template = $watemplate->invoice_terbit;
                                        $smtp = SmtpSetting::firstWhere('shortname', 'dinetkan');
                                        $this->processSingleInvoice($user, $invoice, $today, $template, $smtp, $adminPaymentService, $settings, $service);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }catch (\Exception $ex){
            Log::error($ex->getMessage());
        }
    }


    protected function processSingleInvoice($user, $invoice, $today, $template, $smtp, $adminPaymentService, $settings, $service)
    {
        $placeholders = [
            '[nama_lengkap]', '[id_pelanggan]', '[username]', '[password]', '[alamat]', '[paket_internet]',
            '[tipe_pembayaran]', '[siklus_tagihan]', '[no_invoice]', '[tgl_invoice]',
            '[harga]', '[ppn]', '[discount]', '[total]', '[jth_tempo]', '[periode]', '[subscribe]', '[link_pembayaran]'
        ];
        $total = $invoice->price + $invoice->total_ppn + $invoice->price_adon_monthly;
        $values = [
            $user->name,
            $service->service_id,
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
            Carbon::parse($invoice->due_date)->translatedFormat('F Y'),
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
                Log::info("Invoice cycle genearte running");

            } catch (\Exception $e) {
                Log::error("[ReminderInvoice] Exception sending WA for invoice {$invoice->id}: " . $e->getMessage());
            }
        }

//         send email
        if($smtp){
            try{
                $priceData = $adminPaymentService->calculateLicenseOrderPrice($invoice->itemable, $invoice->admin, $invoice);
                $mapping = MappingUserLicense::where('id', $invoice->id_mapping)->first();
                $adons = MappingAdons::where('id_mapping', $mapping->id)->get();

                $total_ppn_ad = 0;
                $logoPath = public_path('assets/images/dinetkan_logo.png');

                $pdf = Pdf::loadView('accounts.licensing_dinetkan.invoice_pdf_new',
                    compact(
                        'invoice',
                        'priceData',
                        'settings',
                        'adons',
                        'total_ppn_ad',
                        'logoPath'
                    ))->setPaper('a4', 'potrait');

                // Simpan ke storage sementara
                $pdfPath = storage_path("app/invoice/invoice_{$invoice->no_invoice}.pdf");
                $pdf->save($pdfPath);
                $data = [
                    'messages' => $message_orig,
                    'user_name' => $user->username,
                    'notification' => 'Informasi Invoice',
                    'url' => route('admin.invoice_dinetkan', $invoice->no_invoice),
                ];
                app(CustomMailerService::class)->sendWithUserSmtpCron(
                    'emails.generate_invoice',
                    $data,
                    $user->email,
                    'Invoice',
                    $smtp,
                    $pdfPath
                );
//                Log::info("[invoice:reminder-notification] Success sending email to {$user->username}: ");
            }catch (\Exception $e){
                Log::error("[invoice:reminder-notification] Exception sending email to {$user->username}: " . $e->getMessage());
            }
        }
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
