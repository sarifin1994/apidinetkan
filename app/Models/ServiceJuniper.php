<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ServiceStatusEnum;

class ServiceJuniper extends Model
{
    use HasFactory;
    protected $table = 'service_juniper';
    protected $fillable = [
        'service_id',
        'vlan_name',
        'vlan_id',
        'juniper_graph_name'
    ];
}
