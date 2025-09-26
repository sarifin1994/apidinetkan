<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WaServer extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'wa_server';
    protected $fillable = [
        'wa_api',
        'wa_sender',
        'wa_url',
        'status',
        'wa_server'
    ];
}
