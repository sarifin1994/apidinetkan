<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PppoeUser;

class RadiusSession extends Model
{
    use HasFactory;

    protected $connection = 'db_radius';
    protected $table = 'user_session';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int'; // Use 'int' for bigints in Laravel
    public $timestamps = false;

    protected $fillable = [
        'shortname',
        'session_id',
        'username',
        'start',
        'stop',
        'update',
        'nas_address',
        'ip',
        'mac',
        'input',
        'output',
        'uptime',
        'type',
        'status',
        'AcctUniqueId'
    ];

    public function ppp()
    {
        return $this->belongsTo(PppoeUser::class, 'username', 'username')->withDefault();
    }
}
