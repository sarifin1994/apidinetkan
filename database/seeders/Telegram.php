<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Telegram extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\TelegramBot::create([
            'group_id' => 0,
            'chatid' => '-479835828623',
            'tipe' => 1,
        ]);
        \App\Models\TelegramBot::create([
            'group_id' => 1,
            'chatid' => '-479835828623',
            'tipe' => 2,
        ]);
    }
}
