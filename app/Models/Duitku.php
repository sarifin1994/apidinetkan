<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Duitku extends Model
{
    // protected $connection = 'frradius';
    protected $table = 'duitkus';
    protected $fillable = [
        'group_id',
        'id_merchant',
        'api_key',
        'admin_fee',
        'status',
    ];
}
