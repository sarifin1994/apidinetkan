<?php

namespace App\Models\Radius;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Mikrotik\Nas;
use App\Models\Pppoe\PppoeUser;

class RadiusSession extends Model
{
    use HasFactory;
    protected $connection = 'frradius_auth';
    protected $table = 'user_session';
    public function mnas(){
        return $this->belongsTo(Nas::class, 'nas_address','ip_router')->where('shortname',multi_auth()->shortname)->withDefault(); 
    }
    public function ppp(){
        return $this->belongsTo(PppoeUser::class,'username','username')->where('shortname',multi_auth()->shortname)->withDefault();
    }
}

