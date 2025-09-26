<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Whatsapp\Mpwa;

class SuspendUser extends Command
{
    protected $signature = 'user:suspend';
    protected $description = 'Suspend users when license expired and notify via WhatsApp';

    public function handle()
    {
        // Set timezone to Asia/Jakarta for date comparisons
        config(['app.timezone' => 'Asia/Jakarta']);
        date_default_timezone_set('Asia/Jakarta');

        $today = Carbon::today();
        $waConfig = Mpwa::where('shortname', 'owner_radiusqu')->first();

//        if (!$waConfig) {
//            Log::error('[user:suspend] WA configuration not found for shortname owner_radiusqu');
//            return 1;
//        }

        // Process in chunks to reduce memory usage
        User::where('role', 'Admin')
            ->where('status', 1)
            ->where('is_dinetkan',0)
            ->whereNull('ext_role')
            ->with('license')
            ->chunkById(100, function ($users) use ($today, $waConfig) {
                foreach ($users as $user) {
                    $this->processSingleUser($user, $today, $waConfig);
                }
            });

        return 0;
    }

    protected function processSingleUser($user, Carbon $today, Mpwa $waConfig)
    {
        try {
            if($user->next_due == null){
                $nextDue = Carbon::now()->format('Y-m-d');
            }
            if($user->next_due != null){
                $nextDue = Carbon::createFromFormat('Y-m-d', $user->next_due);
            }
        } catch (\Exception $e) {
            Log::error("[user:suspend] Invalid next_due for user {$user->username}: {$user->next_due}");
            return;
        }

        // Suspend date is the next_due date

        if (!$today->greaterThanOrEqualTo($nextDue)) {
            return;
        }

        // Update status to suspended (3)
        $user->update(['status' => 3]);

        if (!$user->license) {
            Log::warning("[user:suspend] User {$user->username} has no license record");
            return;
        }

        $message = $this->buildMessage($user);
        Log::info($message);

        if($waConfig->mpwa_server_server == 'mpwa'){
            try {
                $response = Http::asForm()->post("https://{$waConfig->mpwa_server}/send-message", [
                    'api_key' => $waConfig->api_key,
                    'sender' => $waConfig->sender,
                    'number' => $user->whatsapp,
                    'message' => $message,
                ]);

                if ($response->successful()) {
                    Log::info("[user:suspend] WA sent to {$user->username}");
                } else {
                    Log::error("[user:suspend] WA failed ({$response->status()}) for {$user->username}: {$response->body()}");
                }
            } catch (\Exception $e) {
                Log::error("[user:suspend] Exception sending WA to {$user->username}: " . $e->getMessage());
            }
        }
        if($waConfig->mpwa_server_server == 'radiusqu'){
            $nomorhp = gantiformat_hp($user->whatsapp);
            $user_wa = User::where('shortname', $waConfig->shortname)->first();
            $_id = $user_wa->whatsapp."_".env('APP_ENV');
            $apiUrl = env('WHATSAPP_URL_NEW')."send-message/".$_id; //env('CACTI_ENDPOINT').'cacti/logout/'.$_id;
            try {
                $params = array(
                    "jid" => $nomorhp."@s.whatsapp.net",
                    "content" => array(
                        "text" => $message
                    )
                );
                // Kirim POST request ke API eksternal
                // Http::post($apiUrl, $params);
                $response = Http::post($apiUrl, $params);
                // if($response->successful()){
                //     $json = $response->json();
                //     $status = $json->status;
                //     $receiver = $nomorhp;
                //     $shortname = $user_wa->shortname;
                //     save_wa_log($shortname,$receiver,$message,$status);
                // }

            } catch (\Exception $e) {
                Log::error("[user:suspend] Exception sending WA to {$user->username}: " . $e->getMessage());
            }
        }
    }

    protected function buildMessage($user): string
    {
        $licenseName = $user->license->name;
        $username = $user->username;
        $app_url = env('APP_URL');

        return <<<MSG
        👋 Hai, *{$username}*
        
        Lisensi `{$licenseName}` Anda telah *expired*.

        Untuk memastikan kelancaran layanan dan akses penuh ke fitur Radiusqu, mohon segera perpanjangan lisensi di dashboard `{$app_url}`.

        Terima kasih atas perhatian dan kerjasamanya.
        Salam,
        *Radiusqu*
        MSG;
    }
}
