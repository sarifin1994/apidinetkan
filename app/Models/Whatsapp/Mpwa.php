<?php

namespace App\Models\Whatsapp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mpwa extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'mpwa';
    protected $fillable = [
        'shortname',
        'mpwa_server',
        'sender',
        'api_key',
        'webhook',
        'user_id',
        'mpwa_server_server',
        'qr_url',
        'is_login',
        'mpwa_server_server'
    ];
}
