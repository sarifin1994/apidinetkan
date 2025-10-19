<?php

namespace App\Models\Partnership;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Mitra extends Authenticatable
{
    use HasFactory, HasApiTokens;
    protected $connection = 'mysql';
    protected $table = 'partnership_mitra';
    protected $fillable = [
        'shortname',
        'id_mitra',
        'password',
        'login',
        'user',
        'billing',
        'name',
        'nomor_wa',  
        'profile',  
        'status',
        'kemitraan',
        'balance'
    ];
}
