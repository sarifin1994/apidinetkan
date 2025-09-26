<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RadiusNas extends Model
{
    use HasFactory;
    protected $connection = 'db_radius';
    protected $table = 'nas';
    protected $fillable = [
        'group_id',
        'shortname',
        'nasname',
        'secret',
        'timezone',
    ];
}
