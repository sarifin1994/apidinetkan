<?php

namespace App\Models\Radius;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RadiusProfile extends Model
{
    use HasFactory;
    protected $connection = 'frradius_auth';
    protected $table = 'user_profile';
    protected $fillable = [
        'shortname',
        'mode',
        'profile',
        'attribute',
        'value',
    ];
}
