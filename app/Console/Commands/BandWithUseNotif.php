<?php

namespace App\Console\Commands;

use App\Enums\UserStatusEnum;
use App\Mail\EmailInvoiceNotif;
use App\Mail\TestEmail;
use App\Models\AdminDinetkanInvoice;
use App\Models\AdminInvoice;
use App\Models\CategoryLicenseDinetkan;
use App\Models\LicenseDinetkan;
use App\Models\MappingUserLicense;
use App\Models\UserDinetkan;
use App\Models\UserDinetkanGraph;
use App\Settings\LicenseDinetkanSettings;
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

class BandWithUseNotif extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bandwith_use_notif:check:cron';

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
        // cari aktif order dedicated nternet
        $dedicated = CategoryLicenseDinetkan::where('id', 1)->first();
        $owner = User::where('role', 'Owner')->first();
        $_id = $owner->whatsapp;
        if($dedicated){
            $services = [];
            $services = LicenseDinetkan::where('category_id', $dedicated->id)->get();
            if($services){
                foreach ($services as $srv){
                    $userInvoice = AdminDinetkanInvoice::where('itemable_id', $srv->id)->first();

                    if($userInvoice){
                        $mapping = MappingUserLicense::where('no_invoice', $userInvoice->no_invoice)->where('license_id', $srv->id)->first();
                        if($mapping){
                            $message = "";
                            $userDinetkan = User::where('is_dinetkan', 1)->where('dinetkan_user_id', $mapping->dinetkan_user_id)->first();
                            if($userDinetkan){
                                $graphUsers = UserDinetkanGraph::where('dinetkan_user_id', $userInvoice->dinetkan_user_id)->get();
                                if($graphUsers){
                                    foreach ($graphUsers as $graphUser){
                                        $graph = $this->get_tree_node_mrtg_summary($graphUser->graph_id);
                                        $labels=[];
                                        $label=[];
                                        if(isset($graph[1])){
                                            foreach($graph[1] as $row2){
                                                foreach($row2 as $key=>$val){
                                                    if($key == "Date"){
                                                        $labels[] = $val;
                                                    }else{
                                                        if(!in_array($key, $label)){
                                                            $label[]=$key;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        $label = array_unique($label);
                                        $date = "";
                                        foreach ($label as $lbl){
                                            if(!Str::contains($lbl, 'col')){
                                                foreach($graph[1] as $row3){
                                                    foreach($row3 as $key=>$val) {
                                                        if($key == "Date"){
                                                            $date = $val;
                                                        }
                                                        if($key==$lbl){
                                                            $floatValue = number_format((float)str_replace(',', '', $val), 2, '.', '');

                                                            if($floatValue > 1000000){
                                                                $valx = $floatValue / 1000000;
                                                                $capacity = 0;
                                                                if (preg_match('/\d+/', $srv->capacity, $matches)) {
                                                                    $capacity = $matches[0];  // Output: 200
                                                                }

                                                                if($valx > $capacity){
                                                                    $message .="Dear ".$userDinetkan->first_name." ".$userDinetkan->last_name;
                                                                    $message .="Paket Anda melebihi Batas segera upgrade paket anda";
                                                                    $nomorhp = $this->gantiformat($userDinetkan->whatsapp);
                                                                    $apiUrl = env('WHATSAPP_URL_NEW')."send-message/".$_id;
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
                                                                    //     $shortname = "Owner";
                                                                    //     save_wa_log($shortname,$receiver,$message,$status);
                                                                    // }
                                                                    Log::info(" Bandwithusenotif running : " . date('Y-m-d H:i:s'));
                                                                    continue;
                                                                }

                                                            }else{
                                                                $valx = $floatValue;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        Log::info(' Notif Bandwith user berhasil dijalankan ' . date('Y-m-d H:i:s'));
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



    protected function cacti_login(){
        $_id = "dinetkanCron";
        $apiUrl = env('CACTI_ENDPOINT').'cacti/login/'.$_id;
        try {
            $params = array(
                "action" =>"login",
                "login_username" => "wijaya",
                "login_password" => "wijaya@2024"
            );
            // Kirim POST request ke API eksternal
            $response = Http::post($apiUrl, $params);
            // Periksa apakah request berhasil
            if ($response->successful()) {
                $data = $response->json();
                return $data['success'] ?? null;
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    protected function get_tree_node_mrtg_summary($graph){
        $this->cacti_login();
    // step 2
        try {

            $now = Carbon::now()->subHours(1)->format('Y-m-d H:i'); // Carbon::now()->format('Y-m-d 00:00');
            $end =  Carbon::now()->subMinutes(5)->format('Y-m-d H:i'); //Carbon::createFromFormat('Y-m-d H:i', $now)->addMonthsWithNoOverflow(1)->toDateString();
            $params = array(
                "local_graph_id" => $graph,
                "rra_id" => "0",
                "format" => "table",
                "graph_start" => $now,
                "graph_end" => $end,
                "graph_height" => "200",
                "graph_width" => "700"
            );
            $_id = "dinetkanCron";
            $apiUrl = env('CACTI_ENDPOINT').'cacti/graph_xport/'.$_id.'?' . urldecode(http_build_query($params)) ;
            // Kirim POST request ke API eksternal
            $response = Http::get($apiUrl);
            Storage::disk('local')->append('cek_mrtg_summary.txt', json_encode($response->json(), JSON_PRETTY_PRINT). "\n\n");
            // Periksa apakah request berhasil
            if ($response->successful()) {
                $data = $response->json();
                return $data ?? [];
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return [];
    }

}
