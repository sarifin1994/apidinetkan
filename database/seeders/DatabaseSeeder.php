<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Keuangan\KategoriKeuangan;
use App\Models\Owner\License;
use App\Models\Owner\VpnServer;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Owner
        User::factory()->create([
            'shortname' => 'fajareu',
            'name' => 'Radiusqu Team',
            'username' => 'fajareu',
            'password' => bcrypt('fajarani28'),
            'email' => 'fajareu@frradius.com',
            'whatsapp' => '085155112192',
            'role' => 'Owner',
        ]);
        VpnServer::create([
            'lokasi' => 'indonesia',
            'name' => 'id1.frradius.com',
            'host' => 'id1.frradius.com',
            'user' => 'fajareu',
            'password' => 'fajarani28',
            'port' => 2828,
            'status' => 1,
        ]);

        License::create([
            'name' => 'Cloud Trial',
            'price' => 0,
            'deskripsi' => 'Cocok untuk nyobain',
            'limit_pppoe' => 100,
            'limit_hs' => 100,
            'midtrans' => 0,
            'olt' => 0,
        ]);
        License::create([
            'name' => 'Cloud Newbie',
            'price' => 200000,
            'deskripsi' => 'Cocok untuk pemula',
            'limit_pppoe' => 100,
            'limit_hs' => 1000,
            'midtrans' => 0,
            'olt' => 0,
        ]);
        License::create([
            'name' => 'Cloud Suhu',
            'price' => 300000,
            'deskripsi' => 'Cocok untuk perintis',
            'limit_pppoe' => 300,
            'limit_hs' => 3000,
            'midtrans' => 1,
            'olt' => 1,
        ]);
        License::create([
            'name' => 'Cloud Juragan',
            'price' => 500000,
            'deskripsi' => 'Cocok untuk juragan',
            'limit_pppoe' => 1000,
            'limit_hs' => 10000,
            'midtrans' => 1,
            'olt' => 1,
        ]);
        License::create([
            'name' => 'VPS Sultan 1',
            'price' => 700000,
            'deskripsi' => 'Server dedicated, database terpisah',
            'spek' => 'CPU 2 Core / RAM 2GB',
            'limit_pppoe' => 1000000000,
            'limit_hs' => 1000000000,
            'midtrans' => 1,
            'olt' => 1,
        ]);
        License::create([
            'name' => 'VPS Sultan 2',
            'price' => 1000000,
            'deskripsi' => 'Server dedicated, database terpisah',
            'spek' => 'CPU 2 Core / RAM 4GB',
            'limit_pppoe' => 1000000000,
            'limit_hs' => 1000000000,
            'midtrans' => 1,
            'olt' => 1,
        ]);
        License::create([
            'name' => 'VPS Sultan 3',
            'price' => 1500000,
            'deskripsi' => 'Server dedicated, database terpisah',
            'spek' => 'CPU 4 Core / RAM 8GB',
            'limit_pppoe' => 1000000000,
            'limit_hs' => 1000000000,
            'midtrans' => 1,
            'olt' => 1,
        ]);
        License::create([
            'name' => 'Local Server',
            'price' => 20000000,
            'deskripsi' => 'Anti-timeout, diinstal langsung di server kamu',
            'limit_pppoe' => 1000000000,
            'limit_hs' => 1000000000,
            'midtrans' => 1,
            'olt' => 1,
        ]);
    }
}
