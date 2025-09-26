<?php

namespace App\Console\Commands;

use App\Models\LogMonitoringServer;
use App\Models\MasterServer;
use App\Settings\SiteSettings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ServerMonitoringCycle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server_monitoring_cycle:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ping IP';

    /**
     * Execute the console command.
     */
    public function handle(SiteSettings $settings)
    {
        $servers = MasterServer::get();
        foreach($servers as $server){

            $ip = $server->address;
            $pingCommand = PHP_OS_FAMILY === 'Windows' ? ["ping", "-n", "5", $ip] : ["ping", "-c", "5", $ip];

            $process = new Process($pingCommand);
            $process->run();

            $outputArray = explode("\n", $process->getOutput());
            $rto = 0;
            $unreachable= 0;
            $statusstr = 'UP';
            $statusstr2 = "";
            foreach ($outputArray as $ou){
                if(Str::contains($ou, 'Request timed out')){
                    $rto++;
                }
                if(Str::contains($ou, 'Destination net unreachable')){
                    $unreachable++;
                }
            }

            if($server->is_notif == 1){
                if($rto >= 5 || $unreachable >= 5){
                    $statusstr = "DOWN";
                    if($rto >= 5){
                        $statusstr2 = "RTO";
                    }
                    if($unreachable >= 5){
                        $statusstr2 = "Unreachable";
                    }
                    $emails = explode(";", $settings->monitoring_notif_email); // ['anggifauzi@dinetkan.com','asried@dinetkan.com','monitoring@dinetkan.com','monitoring@wipay.co.id','saeful.arifin150@gmail.com'];
                    foreach($emails as $email){
                        $details = [
                            'email' => $email,
                            'body' => "Hai Admin ada server dengan status ".$statusstr2." dengan rincian sebagai berikut :".PHP_EOL.
                                " Server : ".$server->name.PHP_EOL.
                                ", Address : ".$server->address.PHP_EOL.
                                ", Pada ".Carbon::now()->format('d-m-Y H:i').PHP_EOL,
                            'view' => 'notif_general'
                        ];
                        Mail::to($details['email'])->send(new EmailGeneralNotif($details));
                    }
                    $owner = User::where('role', 'Owner')->first();
                    $_id = $owner->whatsapp;
                    $apiUrl = env('WHATSAPP_URL_NEW')."send-message/".$_id; //env('CACTI_ENDPOINT').'cacti/logout/'.$_id;

                    $whatsapps = explode(";", $settings->monitoring_notif_whatsapp);
                    foreach($whatsapps as $whatsapp){

                        $nomorhp = $this->gantiformat($whatsapp);
                        $params = array(
                            "jid" => $nomorhp."@s.whatsapp.net",
                            "content" => array(
                                "text" => "Hai Admin ada server dengan status ".$statusstr2." dengan rincian sebagai berikut : Server : ".$server->name.", Address : ".$server->address.", Pada ".Carbon::now()->format('d-m-Y H:i')
                            )
                        );
                        // Kirim POST request ke API eksternal
                        // $response = Http::post($apiUrl, $params);
                        $response = Http::post($apiUrl, $params);
                        // if($response->successful()){
                        //     $json = $response->json();
                        //     $status = $json->status;
                        //     $receiver = $nomorhp;
                        //     $shortname = $owner->shortname;
                        //     save_wa_log($shortname,$receiver,$message,$status);
                        // }
                    }
                }
            }
            LogMonitoringServer::create([
                'id_server' => $server->id,
                'status' => $statusstr,
                'response' => json_encode($process->getOutput())
            ]);
        }
        // Log::info($ip." RTO = ".$rto.", Unreach = ".$unreachable);
        // Log::info(' LOG MONITORING SERVER berhasil di jalankan pada ' . date('Y-m-d H:i:s'));
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
