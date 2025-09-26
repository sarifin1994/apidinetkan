<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tripay extends Model
{
    use HasFactory;
    // protected $connection = 'frradius';
    protected $fillable = [
        'group_id',
        'merchant_code',
        'api_key',
        'private_key',
        'admin_fee',
        'status',
    ];
}
