<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ServiceStatusEnum;

class ServiceDetail extends Model
{
    use HasFactory;
    protected $table = 'service_detail';
    protected $fillable = [
        'service_id',
        'email',
        'whatsapp',
        'vlan',
        'metro',
        'metro_id',
        'vendor',
        'trafic_mrtg',
        'first_name',
        'last_name',
        'id_card',
        'npwp',
        'latitude',
        'longitude',
        'province_id',
        'regency_id',
        'district_id',
        'village_id',
        'address',
        'tree',
        'node',
        'graph',
        'group_id',
        'group_name',
        'id_mikrotik',
        'vlan_id',
        'vlan_name',

        'ip_prefix',
        'pop_id',
        'graph_type',
        'graph_name',
        'librenms',
        'sn_modem'
    ];

    public function service()
    {
        return $this->belongsTo(MappingUserLicense::class, 'service_id', 'service_id');
    }
}
