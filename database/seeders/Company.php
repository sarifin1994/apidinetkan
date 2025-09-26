<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Company extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Company::create([
            'group_id' => 0,
            'name' => 'PT Putra Garsel Interkoneksi',
            'nickname' => 'RADIUSQU',
            'email' => 'support@radiusqu.com',
            'wa' => '081222339257',
            'address' => 'Bandung, Jawa Barat, Indonesia',
            'logo' => 'favicon3.png',
        ]);
    }
}
