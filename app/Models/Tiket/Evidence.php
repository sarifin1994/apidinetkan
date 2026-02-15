<?php


namespace App\Models\Tiket;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evidence extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'tiket_evidences';
    protected $fillable = [
        'tiket_id',
        'nama_teknisi',
        'tanggal_pengerjaan',
        'keterangan'
    ];

    public function photo(){
        return $this->hasMany(EvidencePhotos::class,'evidence_id');
    }

}
