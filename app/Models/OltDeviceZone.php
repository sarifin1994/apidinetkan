<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OltDeviceZone extends Model
{
    use HasFactory;
    protected $connection = 'db_ftth';
    protected $table = 'olt_zone';
    protected $fillable = [
        'group_id',
        'id_olt',
        'zone_name',
    ];

    public function odb()
    {
        return $this->hasMany(OltDeviceOdb::class, 'id_zone', 'id'); // Sesuaikan nama kolom
    }

    public function configOlt() {
        return $this->hasMany(OltConfig::class, 'id_zone', 'id');
    }
}
