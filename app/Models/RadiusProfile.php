<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RadiusProfile extends Model
{
    use HasFactory;
    protected $connection = 'db_radius';
    protected $table = 'user_profile';
    protected $fillable = [
        'group_id',
        'shortname',
        'mode',
        'profile',
        'attribute',
        'value',
    ];
}
