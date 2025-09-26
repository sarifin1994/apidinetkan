<?php

namespace App\Models\Mikrotik;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vpn extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'radius_vpn';
    protected $fillable = [
        'shortname',
        'name',
        'user',    
        'password',  
        'ip_address',  
        'vpn_server',
    ];
}
