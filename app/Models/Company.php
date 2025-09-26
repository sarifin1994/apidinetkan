<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
//    protected $connection = 'db_skuy';
    protected $table = 'company';
    protected $fillable = [
        'group_id',
        'name',
        'nickname',
        'email',
        'wa',
        'address',
        'logo'
    ];
}
