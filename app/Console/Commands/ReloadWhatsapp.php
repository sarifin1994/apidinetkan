<?php

namespace App\Console\Commands;

use App\Enums\InvoiceStatusEnum;
use App\Models\AdminInvoice;
use App\Models\User;
use App\Models\UsersWhatsapp;
use App\Models\Whatsapp\Mpwa;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class ReloadWhatsapp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reload_whatsapp:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reload Whatsapps';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->start_whatsapp();
    }
    protected function start_whatsapp(){
        $owner = User::where('role', 'Owner')->first();
        $_id = $owner->whatsapp;
        $url = env('WHATSAPP_URL_NEW')."start/".$_id;
        $response = $this->makeRequest($url, "POST");

        $list_mpwa = Mpwa::where('mpwa_server_server','radiusqu')->where('is_login',1)->get();
        foreach ($list_mpwa as $mpwa){
            $user = User::where('shortname',$mpwa->shortname)->first();
            if($user){
                $_id = $user->whatsapp."_".env('APP_ENV');;
                $url = env('WHATSAPP_URL_NEW')."start/".$_id;
                $response = $this->makeRequest($url, "POST");
            }
        }

        $mpwa = UsersWhatsapp::query()->get();
        if($mpwa){
            foreach ($mpwa as $mp){
                $user_dinetkan = User::where('id', $mp->user_id)->first();
                $_id_dinetkan = $user_dinetkan->whatsapp."_".env('APP_ENV');;
                $url = env('WHATSAPP_URL_NEW')."start/".$_id_dinetkan;
                $response = $this->makeRequest($url, "POST");
            }
        }
        return $response;
    }
    protected function makeRequest($url, $method="GET", $params = []){
        try {
            $data = null;
            $response = Http::get($url, $params);
            if($method == "POST"){
                $response = Http::post($url, $params);
            }
            if($method == "DELETE"){
                $response = Http::delete($url);
            }
            if($method == "PATCH"){
                $response = Http::patch($url, $params);
            }
            if($method == "PUT"){
                $response = Http::put($url, $params);
            }
            if ($response->successful()) {
                $data = $response->json();
            }
            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
