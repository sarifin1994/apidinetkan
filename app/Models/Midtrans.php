<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Midtrans extends Model
{
    use HasFactory;
    // protected $connection = 'frradius';
    protected $table = 'midtrans';
    protected $fillable = [
        'group_id',
        'id_merchant',
        'server_key',
        'client_key',
        'webhook_url',
        'admin_fee',
        'status',
    ];
}
