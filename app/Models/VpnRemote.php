<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VpnRemote extends Model
{
    protected $connection = 'db_profile';
    protected $fillable = [
        'group_id',
        'name',
        'dst_port',
        'to_addresses',
        'to_ports',
        'comment',
        'protocol',
    ];
}
