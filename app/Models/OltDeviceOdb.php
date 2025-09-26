<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OltDeviceOdb extends Model
{
    use HasFactory;
    protected $connection = 'db_ftth';
    protected $table = 'olt_odb';
    protected $fillable = [
        'group_id',
        'id_olt',
        'id_zone',
        'odb_name',
        'port',
        'latitude',
        'longitude'
    ];


    public function zone_olt()
    {
        return $this->belongsTo(OltDeviceZone::class, 'id_zone', 'id'); // Sesuaikan nama kolom 
    }

    public function configOlt() {
        return $this->hasMany(OltConfig::class, 'id_odb', 'id');
    }
}
