<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Partnership\Mitra;

class TransaksiMitra extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'keuangan_mitra';
    protected $fillable = [
        'shortname',
        'id_data',
        'tanggal',
        'mitra_id',
        'tipe',
        'kategori',
        'deskripsi',
        'nominal',
        'metode',
        'created_by',
        'is_dinetkan'
    ];

    public function mitra(){
        return $this->belongsTo(Mitra::class,'mitra_id','id')->withDefault();
    }
}
