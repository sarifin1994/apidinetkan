<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BillingSetting extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\BillingSetting::create([
            'group_id' => 0,
            'due_bc' => 20,
            'inv_fd' => 1,
            'suspend_date' => 1,
            'suspend_time' => '06:00:00',
            'notif_bi' => 0,
            'notif_it' => 0,
            'notif_ps' => 0,
            'notif_sm' => 0,
        ]);
    }
}
