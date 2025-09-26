<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramBot extends Model
{
    use HasFactory;
    protected $connection = 'db_profile';
    protected $table = 'telegram';
    protected $fillable = [
        'group_id',
        'chatid',
        'tipe',
    ];
}
