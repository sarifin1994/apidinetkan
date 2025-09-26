<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PppoeProfile extends Model
{
    use HasFactory;
    protected $connection = 'db_profile';
    protected $table = 'profile_pppoe';
    protected $fillable = [
        'group_id',
        'name',
        'price',
        'rateLimit',
        'groupProfile',
        'validity',
    ];
}
