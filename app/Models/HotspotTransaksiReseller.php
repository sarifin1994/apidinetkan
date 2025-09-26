<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotspotTransaksiReseller extends Model
{
    use HasFactory;
    protected $connection = 'db_profile';
    protected $table = 'transaksi_reseller';
    protected $fillable = [
        'group_id',
        'reseller_id',
        'type',
        'nominal', 
        'komisi',
        'item',
    ];
}
