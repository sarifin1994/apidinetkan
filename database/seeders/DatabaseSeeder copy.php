<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call(BillingSetting::class);
        // $this->call(Company::class);
        // $this->call(PppoeSetting::class);
        // $this->call(Telegram::class);
        $this->call(Users::class);
        $this->call(License::class);
        // $this->call(WablasSetting::class);
        // $this->call(WablasTemplate::class);        
    }
}
