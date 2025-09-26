<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class License extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\License::create([
            'id' => 1,
            'name' => 'Cloud Bronze',
            'price' => '200000',
            'limit_pppoe' => '200',
            'limit_hs' => '10000',
            'limit_nas' => 0,
            'payment_gateway' => 1,
        ]);
        \App\Models\License::create([
            'id' => 2,
            'name' => 'Cloud Silver',
            'price' => '300000',
            'limit_pppoe' => '300',
            'limit_hs' => '30000',
            'limit_nas' => 0,
            'payment_gateway' => 1,
        ]);
        \App\Models\License::create([
            'id' => 3,
            'name' => 'Cloud Gold',
            'price' => '500000',
            'limit_pppoe' => '1000',
            'limit_hs' => '50000',
            'limit_nas' => 0,
            'payment_gateway' => 1,
        ]);
        \App\Models\License::create([
            'id' => 4,
            'name' => 'VPS Platinum',
            'price' => '500000',
            'limit_pppoe' => '999999999',
            'limit_hs' => '999999999',
            'limit_nas' => 0,
            'payment_gateway' => 1,
        ]);
        \App\Models\License::create([
            'id' => 5,
            'name' => 'VPS Diamond',
            'price' => '1000000',
            'limit_pppoe' => '999999999',
            'limit_hs' => '999999999',
            'limit_nas' => 0,
            'payment_gateway' => 1,
        ]);
        \App\Models\License::create([
            'id' => 6,
            'name' => 'Local Radius',
            'price' => '250000',
            'limit_pppoe' => '999999999',
            'limit_hs' => '999999999',
            'limit_nas' => 0,
            'payment_gateway' => 1,
        ]);
    }
}
