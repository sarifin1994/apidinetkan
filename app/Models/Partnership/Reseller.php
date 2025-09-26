<?php

namespace App\Models\Partnership;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Reseller extends Authenticatable
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'partnership_reseller';
    protected $fillable = [
        'shortname',
        'id_reseller',
        'password',
        'login',
        'cetak',
        'name',
        'nomor_wa',  
        'profile',  
        'status',
    ];
}
