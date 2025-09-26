<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WablasSetting extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Wablas::create([
            'group_id' => 0,
            'sender' => 'jkt',
            'token' => 'zmaCOPy5WxayF70QecPbneXbOwf28GHxkDRIn4KRV43ug4QeumM5MRktpxofn6y9',
        ]);
    }
}
