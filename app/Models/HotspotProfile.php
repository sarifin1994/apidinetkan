<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotspotProfile extends Model
{
    use HasFactory;
    protected $connection = 'db_profile';
    protected $table = 'profile_hs';
    protected $fillable = [
        'group_id',
        'name',
        'price',
        'reseller_price',
        'rateLimit',
        'quota',
        'uptime',
        'validity',
        'shared',
        'mac',
        'groupProfile',
    ];
}
