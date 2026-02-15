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
        'shortname_created',
        'nomor_tiket',
        'pelanggan_id',
        'nama_pelanggan',
        'email',
        'jenis_gangguan',
        'penyelesaian',
        'closed_at',
        'prioritas',
        'note',
        'subject',
        'created_by',
        'teknisi',
        'status',
        'service_id',
        'metro_id',
        'whatsapp_group_id',
        'img_path',
        'group_tiket',
        'rootcause',
        'action'
    ];
}
