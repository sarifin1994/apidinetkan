<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogMonitoringServer extends Model
{
    use HasFactory;
    // protected $connection = 'db_skuy';
    protected $table = 'log_monitoring_server';
    protected $fillable = [
        'id_server',
        'status',
        'response'
    ];

    public function server()
    {
        return $this->belongsTo(MasterServer::class,'id_server','id');
    }
}
