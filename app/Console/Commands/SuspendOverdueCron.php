<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\RadiusNas;
use App\Models\PppoeMember;
use App\Models\BillingSetting;
use App\Enums\MemberStatusEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;


class SuspendOverdueCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suspend_overdue:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Suspend Overdue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::select('id_group')->where('role', 'Admin')->get();

        foreach ($users as $user) {
            $suspend_date = BillingSetting::where('group_id', $user->id_group)
                ->value('suspend_date');

            if ($suspend_date !== 0) {
                $now      = Carbon::now();
                $next_due = $now->subDays($suspend_date)->format('Y-m-d');

                // Fetch services instead of members
                $services = PppoeMember::where('group_id', $user->id_group)
                    ->where('next_due', '<=', $next_due)
                    ->with([
                        'pppoe:id,username,value,profile,status,nas',
                        'profile:id,price',
                        'member',
                    ])
                    ->get();

                foreach ($services as $service) {
                    $pppoe  = $service->pppoe;
                    $member = $service->member;

                    if ($member->status === MemberStatusEnum::INACTIVE) {
                        continue;
                    }

                    if ($pppoe->status === 1) {
                        $draw = [
                            'username' => $pppoe->username,
                            'nas'      => $pppoe->nas,
                        ];

                        // Update PPPoE user status to suspended
                        $pppoe->update([
                            'status' => 2, // Assuming 2 means suspended
                        ]);

                        if ($pppoe->nas !== null) {
                            $nas_secret = RadiusNas::where('nasname', $pppoe->nas)
                                ->value('secret');

                            $command = Process::path('/usr/bin/')->run("echo User-Name='{$draw['username']}' | radclient -r 1 {$draw['nas']}:3799 disconnect $nas_secret");
                        } else {
                            $nas_list = RadiusNas::where('group_id', $user->id_group)
                                ->select('nasname', 'secret')
                                ->get();

                            foreach ($nas_list as $item) {
                                $command = Process::path('/usr/bin/')->run("echo User-Name='{$draw['username']}' | radclient -r 1 {$item->nasname}:3799 disconnect {$item->secret}");
                            }
                        }
                    }
                }
            }
        }
    }
}
