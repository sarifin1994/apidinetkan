<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Isolir extends Model
{
    use HasFactory;
    protected $connection = 'frradius_auth';
    protected $table = 'isolir';
    protected $fillable = [
        'shortname',
        'isolir',
        'type',
    ];
}
