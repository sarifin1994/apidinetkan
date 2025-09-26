<?php

namespace App\Console\Commands;

use App\Enums\DinetkanInvoiceStatusEnum;
use App\Models\AdminInvoice;
use App\Models\BillingService;
use App\Models\BillingServiceItem;
use App\Models\MemberDinetkan;
use App\Models\User;
use App\Models\Whatsapp\Mpwa;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class GenerateBillingService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing_service:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate billing service';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        App::setLocale('id');
        config(['app.timezone' => 'Asia/Jakarta']);
        date_default_timezone_set('Asia/Jakarta');

        $today = Carbon::today();
        $waConfig = Mpwa::where('shortname', 'owner_radiusqu')->first();

        // Process in chunks to reduce memory usage
        User::where('role', 'Admin')
            ->where('is_dinetkan',1)
            ->whereNull('ext_role')
            ->chunkById(100, function ($users) use ($today, $waConfig) {
                foreach ($users as $user) {
                    $this->processSingleUser($user, $today, $waConfig);
                }
            });

        return 0;
    }


    protected function processSingleUser($user, Carbon $today, Mpwa $waConfig)
    {
        $today = Carbon::today();
        $lastmont = $today->subMonthNoOverflow(1);
        $members = MemberDinetkan::where('dinetkan_user_id', $user->dinetkan_user_id)->get();
        $price= 0;
        $ppn = 0;
        $bhp = 0;
        $uso = 0;
        $check =  BillingService::where('dinetkan_user_id', $user->dinetkan_user_id)
                    ->where('month', $lastmont->translatedFormat('F'))
                    ->where('year', $lastmont->year)->first();

        if(!$check){
            foreach ($members as $member){
                $price = $price + $member->product_price;
                $ppn = $ppn + $member->product_price * $member->product_ppn / 100;
                $bhp = $bhp + $member->product_price * $member->product_bhp / 100;
                $uso = $uso + $member->product_price * $member->product_uso / 100;

                $cekmember = BillingServiceItem::where('dinetkan_user_id', $user->dinetkan_user_id)
                                                ->where('id_member', $member->id_member)
                                                ->where('month', $lastmont->translatedFormat('F'))
                                                ->where('year', $lastmont->year)->first();
                if(!$cekmember){
                    $dataItem = [
                        'dinetkan_user_id' => $user->dinetkan_user_id,
                        'id_member' => $member->id_member,
                        'total_price' => $member->product_price,
                        'total_ppn' => $member->product_price * $member->product_ppn / 100,
                        'total_bhp' => $member->product_price * $member->product_bhp / 100,
                        'total_uso' => $member->product_price * $member->product_uso / 100,
                        'month' => $lastmont->translatedFormat('F'),
                        'year' => $lastmont->year
                    ];
                    BillingServiceItem::create($dataItem);
                }
            }

            if($price > 0 || $ppn > 0 || $bhp > 0 || $uso > 0){
                $data = [
                    'dinetkan_user_id' => $user->dinetkan_user_id,
                    'total_price' => $price,
                    'total_ppn' => $ppn,
                    'total_bhp' => $bhp,
                    'total_uso' => $uso,
                    'total_member' => count($members),
                    'month' => $lastmont->translatedFormat('F'),
                    'year' => $lastmont->year
                ];
                BillingService::create($data);
            }
        }
        $message = "Data pembayaran PPN, BHP, USO anda sudah waktunya disetorkan";
//        if($waConfig->mpwa_server_server == 'mpwa'){
//            try {
//                $response = Http::asForm()->post("https://{$waConfig->mpwa_server}/send-message", [
//                    'api_key' => $waConfig->api_key,
//                    'sender' => $waConfig->sender,
//                    'number' => $user->whatsapp,
//                    'message' => $message,
//                ]);
//
//                if ($response->successful()) {
//                    Log::info("[user:suspend] WA sent to {$user->username}");
//                } else {
//                    Log::error("[user:suspend] WA failed ({$response->status()}) for {$user->username}: {$response->body()}");
//                }
//            } catch (\Exception $e) {
//                Log::error("[user:suspend] Exception sending WA to {$user->username}: " . $e->getMessage());
//            }
//        }
//        if($waConfig->mpwa_server_server == 'radiusqu'){
//            $nomorhp = gantiformat_hp($user->whatsapp);
//            $user_wa = User::where('shortname', $waConfig->shortname)->first();
//            $_id = $user_wa->whatsapp."_".env('APP_ENV');
//            $apiUrl = env('WHATSAPP_URL_NEW')."send-message/".$_id; //env('CACTI_ENDPOINT').'cacti/logout/'.$_id;
//            try {
//                $params = array(
//                    "jid" => $nomorhp."@s.whatsapp.net",
//                    "content" => array(
//                        "text" => $message
//                    )
//                );
//                // Kirim POST request ke API eksternal
//                Http::post($apiUrl, $params);
//
//            } catch (\Exception $e) {
//                Log::error("[user:suspend] Exception sending WA to {$user->username}: " . $e->getMessage());
//            }
//        }
    }
}
