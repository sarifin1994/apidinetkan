<?php


namespace App\Models\Tiket;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvidencePhotos extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'tiket_evidence_photos';
    protected $fillable = [
        'evidence_id',
        'file_path',
    ];

}
