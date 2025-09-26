<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDInetkan extends Model
{
    use HasFactory;
    // // protected $connection = 'frradius';
    protected $table = 'product_dinetkan';
    protected $fillable = [
        'product_name',
        'price',
        'ppn',
        'bhp',
        'uso',
        'dinetkan_user_id',
        'kapasitas'

    ];
}
