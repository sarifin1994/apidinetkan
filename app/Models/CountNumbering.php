<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountNumbering extends Model
{
    use HasFactory;
    // protected $connection = 'db_skuy';
    protected $table = 'count_numbering';
    protected $fillable = [
        'tipe',
        'count',
        'prefix'
    ];
}
