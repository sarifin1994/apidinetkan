<?php

namespace App\Models;

use App\Models\Traits\Userstamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDoc extends Model
{
    use HasFactory, Userstamps ;
    // protected $connection = 'db_skuy';
    protected $table = 'users_doc';
    protected $fillable = [
        'doc_id',
        'user_id',
        'file_name',
        'file_ext',
        'path',
        'service_id',
        'create_at_by','update_at_by'
    ];

    public function docType()
    {
        return $this->belongsTo(DocType::class,'doc_id','id');
    }
}
