<?php

namespace App\Console\Commands;

use App\Enums\UserStatusEnum;
use App\Mail\EmailInvoiceNotif;
use App\Mail\TestEmail;
use App\Models\AdminDinetkanInvoice;
use App\Models\AdminInvoice;
use App\Models\MappingUserLicense;
use App\Models\UserDinetkan;
use App\Settings\LicenseDinetkanSettings;
use App\Settings\SiteDinetkanSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Member;
use App\Models\Wablas;
use App\Models\PppoeMember;
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
use Modules\Payments\Repositories\Contracts\LicenseDinetkanRepositoryInterface;
use Modules\Payments\Services\AdminDinetkanPaymentService;
use Modules\Payments\Services\AdminPaymentService;

class DinetkanInvoiceNotif extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dinetkan_invoice_notif:check:cron';

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
        SiteDinetkanSettings $settings,
        AdminDinetkanPaymentService $adminPaymentService) {
        $dinetkanInvoice = AdminDinetkanInvoice::where('status',1)->get();
        $now   = Carbon::now();
        $userdinetkans = UserDinetkan::where('is_dinetkan', 1)->get();
        foreach ($userdinetkans as $dinetkan){
            $maxdate = Carbon::parse($dinetkan->payment_date)->addDays($dinetkan->remainder_day);
            $maxdateformat = Carbon::parse($maxdate)->format('Y-m-d');

            $mindate = Carbon::parse($dinetkan->payment_date)->subDays($dinetkan->remainder_day);
            $mindateformat = Carbon::parse($mindate)->format('Y-m-d');
            if($dinetkan->payment_date != null){
                if($now >= $mindateformat && $now <= $maxdateformat){
                }
            }
        }
        foreach ($dinetkanInvoice as $invoice) {
            $maxdate = Carbon::parse($invoice->due_date)->addDays(5);
            $maxdateformat = Carbon::parse($maxdate)->format('d-m-Y');
            $user = UserDinetkan::where('dinetkan_user_id', $invoice->dinetkan_user_id)->first();
            if($now <= $invoice->due_date || $now <= $maxdate){

                $totalppn = 0;
                if($invoice->ppn > 0){
                    $totalppn = $invoice->price * $invoice->ppn / 100;
                }
                $totalotc = 0;
                if($invoice->price_otc > 0){
                    $totalotc = $invoice->price_otc;
                }
                if($invoice->ppn_otc > 0){
                    $totalppnotc = $invoice->price_otc * $invoice->ppn_otc / 100;
                    $totalotc = $invoice->price_otc + $totalppnotc;
                }

                $total = $invoice->price + $totalppn + $totalotc;
                $total_format = number_format($total, 0, '.', '.');
                $details = [
                    'email' => $user->email,
                    'subject' => 'Invoice Remider',
                    'fullname' => $user->first_name.' '.$user->last_name,
                    'no_invoice' => $invoice->no_invoice,
                    'invoice_date' => $invoice->invoice_date,
                    'due_date' => $invoice->due_date,
                    'total' => $total_format,
                    'max_date' => $maxdateformat,
                    'url' => route('admin.invoice_dinetkan', $invoice->no_invoice),
                    'item' => $invoice->item,
                    'view' => 'notif_invoice'
                ];

                $settings = $settings;
                $priceData = $adminPaymentService->calculateLicenseOrderPrice($invoice->itemable, $invoice->admin, $invoice);
                $pdf = Pdf::loadView('accounts.licensing_dinetkan.invoice_pdf',
                    compact(
                        'invoice',
                        'priceData',
                        'settings'
                    ))->setPaper('a4', 'potrait');

                // Simpan ke storage sementara
                $pdfPath = storage_path("app/invoice/invoice_{$invoice->no_invoice}.pdf");
                $pdf->save($pdfPath);

                Mail::to($details['email'])->send(new EmailInvoiceNotif($details,$pdfPath));
//                Mail::to($details['email'])->send(new EmailInvoiceNotif($details));

                // proses send to wa
                $message = "Kepada ".$user->first_name.' '.$user->last_name;
                $message .="\r\n";
                $message .="Kami berharap email ini sampai kepada Anda dengan baik. Silakan rincian faktur Anda:";
                $message .="\r\n";
                $message .="Nomor Invoice : ".$invoice->no_invoice;
                $message .="\r\n";
                $message .="Service : ".$invoice->item;
                $message .="\r\n";
                $message .="Tanggal Invoice : ".$invoice->invoice_date;
                $message .="\r\n";
                $message .="Tanggal Jatuh Tempo : ".$invoice->due_date;
                $message .="\r\n";
                $message .="Total Pembayaran : ".$total_format;
                $message .="\r\n";
                $message .="Harap melakukan pembayaran sebelum tanggal ".$maxdateformat." untuk menghindari biaya keterlambatan dan Order akan di suspend. Anda dapat melihat dan membayar faktur Anda dengan mengklik tombol di bawah ini:";
                $message .="\r\n";
                $message .="Link Invoice ".route('admin.invoice_dinetkan', $invoice->no_invoice);
                $message .="\r\n";
                $message .="Terima Kasih";
                $message .="\r\n";
                $message .="Salam,";
                $message .="\r\n";
                $message .="Dinetkan";

                $nomorhp = $this->gantiformat($user->whatsapp);

                $owner = User::where('role', 'Owner')->first();
                $_id = $owner->whatsapp;
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
                    //     $shortname = $owner->shortname;
                    //     save_wa_log($shortname,$receiver,$message,$status);
                    // }

                } catch (\Exception $e) {

                }
            }
        }
        Log::info('dinetkan invoice cycle has been started at ' . date('Y-m-d H:i:s'));
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
