<?php

namespace App\Models\Olt;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OltDevice extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'olt_device';
    protected $fillable = [
        'shortname',
        'type',
        'version',
        'name',
        'host',
        'username',
        'password',
        'cookies',
    ];
}
