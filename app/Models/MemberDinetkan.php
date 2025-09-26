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
}
