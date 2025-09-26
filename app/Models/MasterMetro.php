<?php

namespace App\Models;

use App\Models\Traits\Userstamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterMetro extends Model
{
    use HasFactory;
    use Userstamps;
    // protected $connection = 'db_skuy';
    protected $table = 'master_metro';
    protected $fillable = [
        'name',
        'province_id',
        'regency_id',
        'district_id',
        'village_id',
        'address',
        'pic',
        'id_wag',
        'pic_phone',
        'name_wag',
        'create_at_by','update_at_by'
    ];
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
}
