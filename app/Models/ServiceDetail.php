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

    public function pop()
    {
        return $this->belongsTo(MasterPop::class, 'pop_id', 'id');
    }

    public function metro()
    {
        return $this->belongsTo(MasterMetro::class, 'metro_id', 'id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class,'province_id','id');
    }

    public function regency()
    {
        return $this->belongsTo(Regencies::class,'regency_id','id');
    }

    public function district()
    {
        return $this->belongsTo(Districts::class,'district_id','id');
    }

    public function village()
    {
        return $this->belongsTo(Villages::class,'village_id','id');
    }

    public function service_active()
    {
        return $this->belongsTo(MappingUserLicense::class, 'service_id', 'service_id')->where('status',1);
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            trim((string) $this->address),
            trim((string) ($this->village?->name)),
            trim((string) ($this->district?->name)),
            trim((string) ($this->regency?->name)),
            trim((string) ($this->province?->name)),
        ], fn ($value) => $value !== '');

        return $parts ? implode(', ', $parts) : '';
    }

}
