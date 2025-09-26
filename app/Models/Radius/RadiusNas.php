<?php

namespace App\Models\Radius;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RadiusNas extends Model
{
    use HasFactory;
    protected $connection = 'frradius_auth';
    protected $table = 'nas';
    protected $fillable = [
        'shortname',
        'nasname',
        'type',
        'secret',
        'timezone',
    ];
}
