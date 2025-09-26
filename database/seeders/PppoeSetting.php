<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PppoeSetting extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\PppoeSetting::create([
            'group_id' => 0,
            'shortname' => 'owner',
            'isolir' => 0,
            'type' => 'pppoe',
        ]);
    }
}
