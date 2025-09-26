<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KategoriKeuangan extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'keuangan_kategori';
    protected $fillable = [
        'shortname',
        'category',
        'type',
        'status',
    ];
}
