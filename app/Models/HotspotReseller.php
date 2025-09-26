<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotspotReseller extends Model
{
    use HasFactory;
    protected $connection = 'db_profile';
    protected $table = 'reseller';
    protected $fillable = [
        'group_id',
        'name',
        'wa',   
        'kode_area', 
        'status', 
        'nas', 
        'profile', 
        'cetak',
    ];
}
