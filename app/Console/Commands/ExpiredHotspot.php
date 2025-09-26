<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Hotspot\HotspotUser;

class ExpiredHotspot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hotspot:expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status hotspot user yang expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        set_time_limit(0);
        $users = User::where('role', 'Admin')->whereIn('status', [1, 3])->with('c_hotspot.c_profile')->get();
        foreach ($users as $user) {
            foreach ($user->c_hotspot as $hotspot) {
                if ($hotspot->c_profile && $hotspot->c_profile->validity !== 'Unlimited') {
                    $validitySeconds = (int) $hotspot->c_profile->validity;
                    $endTime = Carbon::parse($hotspot->start_time)->addSeconds($validitySeconds)->toDateTimeString();
                    $hotspot->update(['end_time' => $endTime]);
                    // \Log::info("End time diperbarui untuk user {$user->shortname} HotspotUser {$hotspot->username} menjadi {$endTime}");
                } else {
                    // \Log::info("Validity is Unlimited for HotspotUser {$hotspot->username}, skipping update.");
                }
            }
        }
        // Update status menjadi 3 untuk hotspot yang end_time-nya sudah lewat waktu (expired)
        $now = Carbon::now()->toDateTimeString();
        $expiredCount = HotspotUser::where('status', 2)
            ->whereNotNull('end_time')
            ->where('end_time', '<', $now)
            ->update(['status' => 3]);
        if ($expiredCount) {
            // \Log::info("Status HotspotUser diperbarui menjadi expired untuk {$expiredCount} record.");
        }

        // // Ambil hotspot user dengan status aktif (2) dan belum memiliki end_time
        // $hotspotUsers = HotspotUser::where('status', 2)
        //     ->whereNull('end_time')
        //     ->with('c_profile:name,validity')
        //     ->get(['id', 'start_time', 'profile']);

        // foreach ($hotspotUsers as $user) {
        //     // Jika validitas bukan Unlimited, maka hitung end_time berdasarkan start_time
        //     if ($user->c_profile->validity !== 'Unlimited') {
        //         $validitySeconds = (int) $user->c_profile->validity;
        //         $endTime = Carbon::parse($user->start_time)->addSeconds($validitySeconds)->toDateTimeString();

        //         $user->update(['end_time' => $endTime]);
        //         $this->info("End time diperbarui untuk HotspotUser ID {$user->id} menjadi {$endTime}");
        //     }
        // }

        // Update status menjadi 3 untuk user yang end_time-nya sudah lewat waktu (expired)
        // $now = Carbon::now()->toDateTimeString();
        // $expiredCount = HotspotUser::where('status', 2)
        //     ->whereNotNull('end_time')
        //     ->where('end_time', '<', $now)
        //     ->update(['status' => 3]);

        // if ($expiredCount) {
        //     \Log::info("Status HotspotUser diperbarui menjadi expired untuk {$expiredCount} record.");
        // }
    }
}
