<?php

namespace App\Models\Owner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class License extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'license';
    protected $fillable = [
        'name',
        'price',
        'limit_pppoe',    
        'limit_hs',
        'custome'
    ];
}
