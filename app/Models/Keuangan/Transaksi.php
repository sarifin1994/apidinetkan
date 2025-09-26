<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaksi extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'keuangan_transaksi';
    protected $fillable = [
        'shortname',
        'id_data',
        'tanggal',
        'reseller',
        'nas',
        'tipe',
        'kategori',
        'deskripsi',
        'nominal',
        'metode',
        'created_by',
    ];
}
