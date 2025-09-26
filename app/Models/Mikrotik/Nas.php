<?php

namespace App\Models\Mikrotik;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Radius\RadiusSession;

class Nas extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'radius_mikrotik';
    protected $fillable = [
        'shortname',
        'name',
        'ip_router',    
        'secret',  
        'timezone', 
        'user',
        'password',
        'port_api' 
    ];
}
