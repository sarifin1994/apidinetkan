<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;
    protected $connection = 'db_ftth';
    protected $table = 'master_area';
    protected $fillable = [
        'group_id',
        'kode_area',
        'deskripsi',    
    ];
}
