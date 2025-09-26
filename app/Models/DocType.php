<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocType extends Model
{
    use HasFactory;
    // protected $connection = 'db_skuy';
    protected $table = 'doc_type';
    protected $fillable = [
        'name'
    ];
}
