<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JuniperVlanTrafficLog extends Model
{
    protected $fillable = [
        'juniper_vlan_id',
        'in_bytes',
        'out_bytes',
        'in_bps',
        'out_bps',
        'in_pps',
        'out_pps',
        'captured_at'
    ];

    public function vlan()
    {
        return $this->belongsTo(JuniperVlan::class,'juniper_vlan_id', 'id');
    }
}
