<?php

namespace App\Models\Mapping;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Mapping\Pop;

class Odp extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'mapping_odp';
    protected $fillable = [
        'shortname',
        'kode_area_id',
        'kode_odp',
        'deskripsi',   
        'port_odp',
        'latitude',
        'longitude',
    ];
    public function area(){
        return $this->belongsTo(Pop::class,'kode_area_id')->withDefault();
    }
}
