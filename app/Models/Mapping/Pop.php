<?php

namespace App\Models\Mapping;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pop extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'mapping_area';
    protected $fillable = [
        'shortname',
        'kode_area',
        'deskripsi',    
    ];
}
