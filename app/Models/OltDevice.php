<?php

namespace App\Models;

use App\Enums\OltDeviceEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OltDevice extends Model
{
    use HasFactory;
    protected $connection = 'db_ftth';
    protected $table = 'olt_device';
    protected $fillable = [
        'group_id',
        'model',
        'type',
        'version',
        'name',
        'host',
        'username',
        'password',
        'token',
        'snmp_read_write',
        'udp_port',
        'user_id'
    ];

    protected $casts = [
        'model' => OltDeviceEnum::class,
    ];
}
