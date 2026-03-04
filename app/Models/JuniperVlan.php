<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JuniperVlan extends Model
{
    protected $fillable = [
        'interface',
        'unit',
        'vlan_id',
        'description',
        'admin_status'
    ];

    public function trafficLogs()
    {
        return $this->hasMany(JuniperVlanTrafficLog::class);
    }
}
