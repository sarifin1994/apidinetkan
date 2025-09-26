<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Midtrans extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'setting_midtrans';
    protected $fillable = [
        'shortname',
        'id_merchant',
        'client_key',
        'server_key',
        'webhook_url',
        'admin_fee',
        'status',
    ];
}
