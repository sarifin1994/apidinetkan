<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Area;
use App\Models\PppoeUser;

class Odp extends Model
{
    use HasFactory;
    protected $connection = 'db_ftth';
    protected $table = 'master_odp';
    protected $fillable = [
        'group_id',
        'kode_odp',
        'port_odp',
        'kode_area_id',
        'latitude',
        'longitude',

    ];
    public function area(){
        return $this->belongsTo(Area::class,'kode_area_id')->withDefault();
    }
}
