<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vpn extends Model
{
    use HasFactory;
    protected $connection = 'db_profile';
    protected $table = 'vpn';
    protected $fillable = [
        'group_id',
        'name',
        'user',
        'password',
        'ip_address',
        // 'ip_radius'
    ];
}
