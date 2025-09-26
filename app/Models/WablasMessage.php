<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WablasMessage extends Model
{
    use HasFactory;
    protected $connection = 'db_wablas';
    protected $table = 'message';
    protected $fillable = [
        'group_id',
        'id_message',
        'phone',
        'message',
        'status',
    ];
}
