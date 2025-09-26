<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wablas extends Model
{
    use HasFactory;
    protected $connection = 'db_wablas';
    protected $table = 'setting';
    protected $fillable = [
        'group_id',
        'sender',
        'token',
    ];
}
