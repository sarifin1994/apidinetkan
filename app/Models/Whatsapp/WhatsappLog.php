<?php

namespace App\Models\Whatsapp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WhatsappLog extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'whatsapp_log';
    protected $fillable = [
        'shortname',
        'send_at',
        'receiver',
        'message',
        'status'
    ];
}
