<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PppoeSetting extends Model
{
    use HasFactory;
    protected $connection = 'db_radius';
    protected $table = 'isolir';
    protected $fillable = [
        'group_id',
        'shortname',
        'isolir',
        'type',
    ];
}
