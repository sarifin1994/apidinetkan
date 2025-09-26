<?php

namespace App\Console\Commands;

use App\Models\AdminInvoice;
use App\Models\Districts;
use App\Models\MasterMetro;
use App\Models\MasterPop;
use App\Models\Province;
use App\Models\Regencies;
use App\Models\User;
use App\Models\UserDinetkan;
use App\Models\UserDinetkanGraph;
use App\Models\UsersWhatsapp;
use App\Models\Villages;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class PingCycle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ping_cycle:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ping IP';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $owner = User::where('role', 'Owner')->first();
        $userwa = UsersWhatsapp::where('user_id', $owner->id)->first();
        $_id = $owner->whatsapp;
//        $userdinetkan = UserDinetkan::where('is_dinetkan', 1)->where('dinetkan_user_id', 8)->with('company')->get();
        $userdinetkan = UserDinetkan::where('is_dinetkan', 1)->with('company')->get();
        foreach ($userdinetkan as $user){
            if($user->ip_prefix != "" && $user->ip_prefix != null){
                $message = "";
                $ipexp = explode('/',$user->ip_prefix);
                // Execute the ping command based on the OS
                $ip = $ipexp[0];// "103.184.122.9";
                $rto = 1;
                $unreachable = 1;
                $pingCommand = PHP_OS_FAMILY === 'Windows' ? "ping -n 5 $ip" : "ping -c 5 $ip";
                $output = shell_exec($pingCommand);
                $pingArrayOrig = explode("\n", trim($output)); // Ubah output ke array

                $pingArray = ['message' => 'The IP $ip is reachable.', 'data' => $pingArrayOrig];
                $json = json_encode($pingArray);
                $data = json_decode($json, true);
                $dataLines = $data['data'];
                // Ambil statistik ping
                foreach ($dataLines as $line) {
                    if (preg_match("/request\s+timed\s+out/i", $line)) {
                        // proses send to wa
                        if($rto >= 5){
                            $pop = "";
                            $grap = UserDinetkanGraph::where('dinetkan_user_id', $user->dinetkan_user_id)->first();
                            if($grap){
                                $masterPOP = MasterPop::where('id', $grap->pop_id)->first();
                                if($masterPOP){
                                    $pop = $masterPOP->name;
                                }
                            }
                            $prov="";
                            $reg="";
                            $dist="";
                            $vill="";
                            $provinsi = Province::where('id', $user->province_id)->first();
                            if($provinsi){
                                $prov = $provinsi->name;
                            }
                            $regency = Regencies::where('id', $user->regency_id)->first();
                            if($regency){
                                $reg = $regency->name;
                            }
                            $district = Districts::where('id', $user->district_id)->first();
                            if($district){
                                $dist = $district->name;
                            }
                            $village = Villages::where('id', $user->village)->first();
                            if($village){
                                $vill = $village->name;
                            }

                            // send buat redakasi untuk group mitra
                            $message = "Hallo";
                            $message .= "\r\nHallo";
                            $message .= "\r\nmohon dicek ".$user->company->name;
                            $message .= "\r\nkami monitoring network status Down";
                            $message .= "\r\ndengan data VLAN: ".$user->vlan;
                            $message .= "\r\nIP Prefix: ".$user->ip_prefix;
                            $message .= "\r\nStatus: Down";
                            $message .= "\r\n";
                            $message .= "\r\nmohon dicek di sisi ".$user->company->name." apakah lagi ada maintenance?";
                            $message .= "\r\n";
                            $message .= "\r\nterimakasih";
                            $apiUrl = env('WHATSAPP_URL_NEW')."send-message/".$_id;
                            if($user->group_id){
                                $params = array(
                                    "jid" => $user->group_id,
                                    "content" => array(
                                        "text" => $message
                                    )
                                );
                                // Kirim POST request ke API eksternal
                                Http::post($apiUrl, $params);
                            }

                            // ini untuk group Internal
                            $message = "Hallo";
                            $message .= "\r\nmonitoring network status Down";
                            $message .= "\r\ndengan data";
                            $message .= "\r\nID Pelanggan : ".$user->dinetkan_user_id;
                            $message .= "\r\nNama Mitra : ".$user->first_name." ".$user->last_name;
                            $message .= "\r\nAlamat Mitra : ".$user->address.", ".$prov.", ".$reg.", ".$dist.", ".$vill;
                            $message .= "\r\nNo Tlp Mitra : ".$user->whatsapp;
                            $message .= "\r\nPOP : ".$pop;
                            $message .= "\r\nVendor Metro : ".$user->metro;
                            $message .= "\r\nCID/SO/SOA Vendor Metro: ".$user->vendor;
                            $message .= "\r\nVLAN : ".$user->vlan;
                            $message .= "\r\nIP Prefix : ".$user->ip_prefix;
                            $message .= "\r\nStatus : RTO";
                            $message .= "\r\n";
                            $message .= "\r\nmohon di cek Tim !!!!";
                            $apiUrl = env('WHATSAPP_URL_NEW')."send-message/".$_id;
                            if($userwa->group_id){
                                $params = array(
                                    "jid" => $userwa->group_id,
                                    "content" => array(
                                        "text" => $message
                                    )
                                );
                                // Kirim POST request ke API eksternal
                                Http::post($apiUrl, $params);
                            }

                            // untuk ke group Ventor Metro
                            $masterMetro = MasterMetro::where('id', $user->metro_id)->first();
                            if($masterMetro){
                                $message = "Hallo";
                                $message .= "\r\nmohon dicek ".$masterMetro->name;
                                $message .= "\r\nkami monitoring network status Down";
                                $message .= "\r\ndengan data";
                                $message .= "\r\nMetro : ".$masterMetro->name;
                                $message .= "\r\nCID/SID/SO : ".$user->vendor;
                                $message .= "\r\nStatus: Down";
                                $message .= "\r\nAlamat Mitra : ".$user->address.", ".$prov.", ".$reg.", ".$dist.", ".$vill;
                                $message .= "\r\n";
                                $message .= "\r\nmohon dicek di sisi ".$masterMetro->name." apakah ada issue?";
                                $message .= "\r\n";
                                $message .= "\r\nterimakasih";
                                $apiUrl = env('WHATSAPP_URL_NEW')."send-message/".$_id;
                                if($masterMetro->id_wag){
                                    $params = array(
                                        "jid" => $masterMetro->id_wag,
                                        "content" => array(
                                            "text" => $message
                                        )
                                    );
                                    // Kirim POST request ke API eksternal
                                    Http::post($apiUrl, $params);
                                }
                            }
                        }
                        $rto++;
                    }
                    if (preg_match("/Destination\s+host\s+unreachable/i", $line)) {
                        // proses send to wa
                        if($unreachable >= 5){
                            $pop = "";
                            $grap = UserDinetkanGraph::where('dinetkan_user_id', $user->dinetkan_user_id)->first();
                            if($grap){
                                $masterPOP = MasterPop::where('id', $grap->pop_id)->first();
                                if($masterPOP){
                                    $pop = $masterPOP->name;
                                }
                            }
                            $prov="";
                            $reg="";
                            $dist="";
                            $vill="";
                            $provinsi = Province::where('id', $user->province_id)->first();
                            if($provinsi){
                                $prov = $provinsi->name;
                            }
                            $regency = Regencies::where('id', $user->regency_id)->first();
                            if($regency){
                                $reg = $regency->name;
                            }
                            $district = Districts::where('id', $user->district_id)->first();
                            if($district){
                                $dist = $district->name;
                            }
                            $village = Villages::where('id', $user->village)->first();
                            if($village){
                                $vill = $village->name;
                            }

                            // send buat redakasi untuk group mitra
                            $message = "Hallo";
                            $message .= "\r\nHallo";
                            $message .= "\r\nmohon dicek ".$user->company->name;
                            $message .= "\r\nkami monitoring network status Down";
                            $message .= "\r\ndengan data VLAN: ".$user->vlan;
                            $message .= "\r\nIP Prefix: ".$user->ip_prefix;
                            $message .= "\r\nStatus: Down";
                            $message .= "\r\n";
                            $message .= "\r\nmohon dicek di sisi ".$user->company->name." apakah lagi ada maintenance?";
                            $message .= "\r\n";
                            $message .= "\r\nterimakasih";
                            $apiUrl = env('WHATSAPP_URL_NEW')."send-message/".$_id;
                            if($user->group_id){
                                $params = array(
                                    "jid" => $user->group_id,
                                    "content" => array(
                                        "text" => $message
                                    )
                                );
                                // Kirim POST request ke API eksternal
                                Http::post($apiUrl, $params);
                            }


                            // ini untuk group Internal
                            $message = "Hallo";
                            $message .= "\r\nmonitoring network status Down";
                            $message .= "\r\ndengan data";
                            $message .= "\r\nID Pelanggan : ".$user->dinetkan_user_id;
                            $message .= "\r\nNama Mitra : ".$user->first_name." ".$user->last_name;
                            $message .= "\r\nAlamat Mitra : ".$user->address.", ".$prov.", ".$reg.", ".$dist.", ".$vill;
                            $message .= "\r\nNo Tlp Mitra : ".$user->whatsapp;
                            $message .= "\r\nPOP : ".$pop;
                            $message .= "\r\nVendor Metro : ".$user->metro;
                            $message .= "\r\nCID/SO/SOA Vendor Metro: ".$user->vendor;
                            $message .= "\r\nVLAN : ".$user->vlan;
                            $message .= "\r\nIP Prefix : ".$user->ip_prefix;
                            $message .= "\r\nStatus : unreachable";
                            $message .= "\r\n";
                            $message .= "\r\nmohon di cek Tim !!!!";
                            $apiUrl = env('WHATSAPP_URL_NEW')."send-message/".$_id;
                            if($userwa->group_id){
                                $params = array(
                                    "jid" => $userwa->group_id,
                                    "content" => array(
                                        "text" => $message
                                    )
                                );
                                // Kirim POST request ke API eksternal
                                Http::post($apiUrl, $params);
                            }

                            // untuk ke group Ventor Metro
                            $masterMetro = MasterMetro::where('id', $user->metro_id)->first();
                            if($masterMetro){
                                $message = "Hallo";
                                $message .= "\r\nmohon dicek ".$masterMetro->name;
                                $message .= "\r\nkami monitoring network status Down";
                                $message .= "\r\ndengan data";
                                $message .= "\r\nMetro : ".$masterMetro->name;
                                $message .= "\r\nCID/SID/SO : ".$user->vendor;
                                $message .= "\r\nStatus: Down";
                                $message .= "\r\nAlamat Mitra : ".$user->address.", ".$prov.", ".$reg.", ".$dist.", ".$vill;
                                $message .= "\r\n";
                                $message .= "\r\nmohon dicek di sisi ".$masterMetro->name." apakah ada issue?";
                                $message .= "\r\n";
                                $message .= "\r\nterimakasih";
                                $apiUrl = env('WHATSAPP_URL_NEW')."send-message/".$_id;
                                if( $masterMetro->id_wag){
                                    $params = array(
                                        "jid" => $masterMetro->id_wag,
                                        "content" => array(
                                            "text" => $message
                                        )
                                    );
                                    // Kirim POST request ke API eksternal
                                    Http::post($apiUrl, $params);
                                }
                            }


                        }
                        $unreachable++;
                    }

                }
            }
        }
        // Log::info(' PING SERVER berhasil di jalankan pada ' . date('Y-m-d H:i:s'));
    }
}
