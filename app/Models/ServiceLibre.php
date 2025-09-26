<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ServiceStatusEnum;

class ServiceLibre extends Model
{
    use HasFactory;
    protected $table = 'service_libre';
    protected $fillable = [
        'service_id',
        'vlan_name',
        'vlan_id',
        'hostname',
        'ifName'
    ];
}
