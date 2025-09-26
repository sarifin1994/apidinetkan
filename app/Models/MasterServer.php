<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterServer extends Model
{
    use HasFactory;
    // protected $connection = 'db_skuy';
    protected $table = 'master_server';
    protected $fillable = [
        'address',
        'name',
        'group_id',
        'is_notif'
    ];

    public function group()
    {
        return $this->belongsTo(MasterGroupServer::class,'group_id','id');
    }
}
