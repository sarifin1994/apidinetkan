<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Models\Whatsapp\Mpwa;

class SuspendUserxx extends Command
{
    protected $signature = 'user:suspendxx';
    protected $description = 'Suspend user if license expired';

    public function handle()
    {
        set_time_limit(0);
        // Ambil tanggal hari ini
        $today = Carbon::today()->toDateString();
        // ambil server pertama
        $wa_server = Mpwa::where('shortname','owner_radiusqu')->first();
        // Ambil user dengan role Admin, status aktif, dan memiliki next_due, beserta relasi license
        $users = User::where('role', 'Admin')
            ->whereNotNull('next_due')
            ->where('status', 1)
            ->with('license')
            ->get();

        foreach ($users as $user) {
            $shortname = $user->shortname;
            try {
                // Target suspend: next_due + 1 hari
                $targetDate = Carbon::createFromFormat('Y-m-d', $user->next_due)
                    ->addDay()
                    ->toDateString();
            } catch (\Exception $e) {
                Log::error("Error parsing next_due for user: {$shortname}. " . $e->getMessage());
                continue;
            }

            if ($user->status !== 3 && $today === $targetDate) {

                // Update status user menjadi 3 (suspended)
                $user->update(['status' => 3]);

                // Pastikan relasi license ada
                if (!$user->license) {
                    Log::warning("User {$shortname} tidak memiliki data license, WA tidak dikirim.");
                    continue;
                }

                // Siapkan pesan WA dengan template (gunakan \n untuk pemisah baris)
                $template = "Hai, *{$user->username}*\nKami informasikan bahwa lisensi {$user->license->name} kamu telah *expired*. Untuk memastikan kelancaran layanan dan akses penuh ke fitur radiusqu, mohon segera lakukan perpanjangan lisensi di dashboard ".env('APP_URL')."\nTerima kasih atas perhatian dan kerjasamanya.\nSalam hormat,\n*Radiusqu*";
                $message = str_replace('<br>', "\n", $template);

                // Kirim pesan WA menggunakan cURL dalam blok try-catch
                try {
                    $curl = curl_init();
                    $data = [
                        'api_key' => $wa_server->api_key,
                        'sender'  => $wa_server->sender,
                        'number'  => $user->whatsapp,
                        'message' => $message,
                    ];
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                    curl_setopt($curl, CURLOPT_URL, 'https://' . $wa_server->mpwa_server . '/send-message');
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                    $response = curl_exec($curl);
                    curl_close($curl);
                    Log::info("WA response untuk user {$shortname}: " . $response);
                    // Jeda sejenak untuk mencegah overload
                    sleep(5);
                } catch (\Exception $e) {
                    Log::error("Gagal mengirim WA untuk user: {$shortname}. Error: " . $e->getMessage());
                    continue;
                }

                Log::info("Suspend berhasil dilakukan untuk user: {$shortname}");
            }
        }
    }
}
