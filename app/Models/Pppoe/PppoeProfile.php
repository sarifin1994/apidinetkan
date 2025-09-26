<?php

namespace App\Models\Pppoe;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class PppoeProfile extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'pppoe_profile';
    protected $fillable = [
        'shortname',
        'name',
        'price', 
        'fee_mitra',
        'rateLimit', 
        'groupProfile',
        'validity',
        'status',  
    ];
}
