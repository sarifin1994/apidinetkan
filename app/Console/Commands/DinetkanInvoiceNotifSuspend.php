<?php

namespace App\Console\Commands;

use App\Enums\UserStatusEnum;
use App\Enums\ServiceStatusEnum;
use App\Enums\DinetkanInvoiceStatusEnum;
use App\Mail\EmailInvoiceNotif;
use App\Models\AdminDinetkanInvoice;
use App\Models\MappingUserLicense;
use App\Models\UserDinetkan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;

class DinetkanInvoiceNotifSuspend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dinetkan_invoice_notif_suspend:check:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Admin License Prabayar and Send Notification';



    /**
     * Execute the console command.
     */
    public function handle() {
        $dinetkanInvoice = AdminDinetkanInvoice::where('status',1)->get();
        $now   = Carbon::now();

        foreach ($dinetkanInvoice as $invoice) {
            $maxDate = Carbon::parse($invoice->due_date)->addDays(5);
            $maxDateFormat = Carbon::parse($maxDate)->format('d-m-Y');
            $user = UserDinetkan::where('dinetkan_user_id', $invoice->dinetkan_user_id)->first();
            if($now > $maxDate){

                // update service jadi suspend
                $mappingUserLicense = MappingUserLicense::where('no_invoice', $invoice->no_invoice)->first();
                if($mappingUserLicense){
                    $mappingUserLicense->status = ServiceStatusEnum::SUSPEND;
                    $mappingUserLicense->save();
                }

                // update invoice jadi cancel / expired
                $invoice->status = DinetkanInvoiceStatusEnum::EXPIRED;
                $invoice->save();
                $totalppn = 0;
                if($invoice->ppn > 0){
                    $totalppn = $invoice->price * $invoice->ppn / 100;
                }
                $total = $invoice->price + $totalppn;
                $total_format = number_format($total, 0, '.', '.');
                $details = [
                    'email' => $user->email,
                    'subject' => 'Invoice Remider Suspend',
                    'fullname' => $user->first_name.' '.$user->last_name,
                    'no_invoice' => $invoice->no_invoice,
                    'invoice_date' => $invoice->invoice_date,
                    'due_date' => $invoice->due_date,
                    'total' => $total_format,
                    'max_date' => $maxDateFormat,
                    'url' => route('admin.invoice_dinetkan', $invoice->no_invoice),
                    'item' => $invoice->item,
                    'view' => 'notif_invoice_suspend'
                ];
                Mail::to($details['email'])->send(new EmailInvoiceNotif($details));

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
                $message .="Service anda telah di suspend karena melewati batas maksimal pembayaran di tanggal $maxDateFormat";
                $message .="\r\n";
                $message .="Terima Kasih";
                $message .="\r\n";
                $message .="Salam,";
                $message .="\r\n";
                $message .="Dinetkan";

                $nomorhp = $this->gantiformat($user->whatsapp);
                $apiUrl = "http://103.184.122.170/api/whatsapp/send-message/session1"; //env('CACTI_ENDPOINT').'cacti/logout/'.$_id;
                try {
                    $params = array(
                        "jid" => $nomorhp."@s.whatsapp.net",
                        "content" => array(
                            "text" => $message
                        )
                    );
                    // Kirim POST request ke API eksternal
                    Http::post($apiUrl, $params);

                } catch (\Exception $e) {

                }
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
