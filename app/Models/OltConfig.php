<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OltConfig extends Model
{
    use HasFactory;
    protected $connection = 'db_ftth';
    protected $table = 'olt_config';
    protected $fillable = [
        'group_id',
        'id_olt',
        'id_onu',
        'id_zone',
        'id_odb',
        'onu_external_id',
        'onu_mode',
        'vlan_id',
        'ifName_new',
        'ifName_old',
        'address',
        'contact'
    ];

    public function zone_olt()
    {
        return $this->belongsTo(OltDeviceZone::class, 'id_zone', 'id'); // Sesuaikan nama kolom 
    }

    public function odb()
    {
        return $this->belongsTo(OltDeviceOdb::class, 'id_odb', 'id'); // Sesuaikan nama kolom
    }
    

}
