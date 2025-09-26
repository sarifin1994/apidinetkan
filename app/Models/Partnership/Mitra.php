<?php

namespace App\Models\Partnership;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Mitra extends Authenticatable
{
    use HasFactory;
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
