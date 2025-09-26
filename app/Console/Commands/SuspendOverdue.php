<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Setting\BillingSetting;
use App\Models\Radius\RadiusNas;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;

class SuspendOverdue extends Command
{
    protected $signature = 'pppoe:suspend-overdue';
    protected $description = 'Suspend PPPoE users whose invoices are overdue';

    public function handle()
    {
        // Set timezone
        config(['app.timezone' => 'Asia/Jakarta']);
        date_default_timezone_set('Asia/Jakarta');

        $sshUser = env('IP_RADIUS_USERNAME');
        $sshHost = env('IP_RADIUS_SERVER');
        $radPort = env('RADIUS_DISCONNECT_PORT', 3799);
        $radRetries = env('RADIUS_DISCONNECT_RETRIES', 1);
        $sshOptions = ['-o', 'BatchMode=yes', '-o', 'StrictHostKeyChecking=no'];
        $today = Carbon::today();

        // Process in batches
        User::where('role', 'Admin')
            ->whereIn('status', [1, 3])
            ->with('c_pppoe1')
            ->chunkById(100, function ($users) use ($sshUser, $sshHost, $radPort, $radRetries, $sshOptions, $today) {
                foreach ($users as $user) {
                    $this->processUser($user, $sshUser, $sshHost, $radPort, $radRetries, $sshOptions, $today);
                }
            });
    }

    protected function processUser($user, $sshUser, $sshHost, $radPort, $radRetries, array $sshOptions, Carbon $today)
    {
        $shortname = $user->shortname;
        $billing = BillingSetting::firstWhere('shortname', $shortname);

        if (! $billing) {
            Log::error("[SuspendOverdue][{$shortname}] BillingSetting not found");
            return;
        }

        $sd = (int) $billing->suspend_date;
        if ($sd <= 0) {
            Log::info("[SuspendOverdue][{$shortname}] Suspend date is 0, skipping");
            return;
        }

        // Calculate threshold date: today minus suspend_date days
        $threshold = $today->copy()->subDays($sd)->toDateString();
        // Fetch NAS list once
        $nasList = RadiusNas::where('shortname', $shortname)->get(['nasname', 'secret']);

        foreach ($user->c_pppoe1 as $pppoe) {
            $this->processPppoe($pppoe, $threshold, $sshUser, $sshHost, $radPort, $radRetries, $sshOptions, $nasList, $shortname);
        }
    }

    protected function processPppoe($pppoe, string $threshold, $sshUser, $sshHost, $radPort, $radRetries, array $sshOptions, $nasList, $shortname)
    {
        if (empty($pppoe->next_due)) {
            Log::warning("[SuspendOverdue][{$shortname}] PPPoE {$pppoe->username} has no next_due, skipping");
            return;
        }

        try {
            $dueDate = Carbon::createFromFormat('Y-m-d', $pppoe->next_due)->toDateString();
        } catch (\Exception $e) {
            Log::error("[SuspendOverdue][{$shortname}] Invalid next_due for {$pppoe->username}: {$pppoe->next_due}");
            return;
        }

        // Conditions: overdue, active status, payment_type exists
        if ($dueDate <= $threshold && $pppoe->status === 1 && $pppoe->payment_type) {
            $pppoe->update(['status' => 2]);
            Log::info("[SuspendOverdue][{$shortname}] Status set to suspended for {$pppoe->username}");
            $this->disconnectUser($pppoe->username, $pppoe->nas, $sshUser, $sshHost, $radPort, $radRetries, $sshOptions, $nasList, $shortname);
        }
    }

    protected function disconnectUser($username, $nasName, $sshUser, $sshHost, $port, $retries, array $sshOptions, $nasList, $shortname)
    {
        $targets = $nasName
            ? collect($nasList)->where('nasname', $nasName)
            : $nasList;

        if ($targets->isEmpty()) {
            Log::error("[SuspendOverdue][{$shortname}] No NAS targets found for {$username}");
            return;
        }

        foreach ($targets as $nas) {
            $cmdString = sprintf(
                'printf "User-Name=%s\n" | radclient -r %d %s:%d disconnect %s',
                $username,
                $retries,
                $nas->nasname,
                $port,
                $nas->secret
            );

            $sshCommand = array_merge($sshOptions, ["{$sshUser}@{$sshHost}", $cmdString]);

            try {
                $process = Process::run(array_merge(['ssh'], $sshCommand));

                if ($process->successful()) {
                    Log::info("[SuspendOverdue][{$shortname}] Disconnected {$username}@{$nas->nasname}");
                } else {
                    $stderr = trim($process->errorOutput());
                    Log::error(
                        "[SuspendOverdue][{$shortname}] Disconnect failed for {$username}@{$nas->nasname}",
                        ['exit' => $process->exitCode(), 'stderr' => $stderr]
                    );

                    if (str_contains(strtolower($stderr), 'permission denied')) {
                        Log::warning(
                            "[SuspendOverdue][{$shortname}] SSH permission denied for {$sshUser}@{$sshHost}. " .
                            "Please verify SSH keys or credentials."
                        );
                    }
                }
            } catch (\Exception $e) {
                Log::error("[SuspendOverdue][{$shortname}] Exception disconnecting {$username}@{$nas->nasname}: " . $e->getMessage());
            }
        }
    }
}
