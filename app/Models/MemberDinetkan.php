<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PppoeUser;
use App\Models\PppoeProfile;

class MemberDinetkan extends Model
{
    use HasFactory;
    // // protected $connection = 'frradius';
    protected $table = 'frradius.member_dinetkan';
    protected $fillable = [
        'group_id',
        'id_member',
        'full_name',
        'email',
        'wa',
        'address',
        'status',
        'no_ktp',
        'npwp',
        'latitude',
        'longitude',
        'province_id',
        'regency_id',
        'district_id',
        'village_id',
        'dinetkan_user_id',
        'first_name',
        'last_name',
        'product_dinetkan_id',
        'product_name',
        'product_price',
        'product_ppn',
        'product_bhp',
        'product_uso',
    ];

    public function service(){
        return $this->belongsTo(ProductDInetkan::class,'product_dinetkan_id', 'id');
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
}
