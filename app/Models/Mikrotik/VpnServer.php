<?php

namespace App\Models\Mikrotik;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VpnServer extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'vpn_server';
}
