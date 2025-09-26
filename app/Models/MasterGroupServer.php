<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterGroupServer extends Model
{
    use HasFactory;
    // protected $connection = 'db_skuy';
    protected $table = 'master_group_server';
    protected $fillable = [
        'name'
    ];
}
