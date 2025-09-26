<?php

namespace App\Models\Tiket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TiketGangguan extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'tiket_gangguan';
    protected $fillable = [
        'shortname',
        'nomor_tiket',
        'pelanggan_id',
        'nama_pelanggan',
        'jenis_gangguan',
        'penyelesaian',
        'closed_at',
        'prioritas',
        'note',
        'created_by',
        'teknisi',
        'status',
    ];
}
