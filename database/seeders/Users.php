<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Users extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'id_group' => 0,
            'shortname' => 'Radiusqu',
            'name' => 'Putra Garsel Interkoneksi',
            'username' => 'owner',
            'email' => 'support@radiusqu.com',
            'whatsapp' => '081222339257',
            'password' => bcrypt('Lancar2020'),
            'role' => 'Owner',
            'status' => 1,
            'license_id' => 999,
        ]);
    }
}
