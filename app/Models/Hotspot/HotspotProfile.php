<?php

namespace App\Models\Hotspot;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class HotspotProfile extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'hotspot_profile';
    protected $fillable = [
        'shortname',
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
        'status',
        'is_billing'
    ];
}
