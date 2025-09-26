<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PppoeUser;
use App\Models\Member;

class OltUser extends Model
{
    use HasFactory;
    protected $connection = 'db_ftth';
    protected $table = 'olt_user';
    protected $fillable = [
        'group_id',
        'pppoe_id',
        'member_id',
        'olt_id',    
        'port_id',    
        'onu_id', 
    ];

    public function ppp(){
        return $this->belongsTo(PppoeUser::class, 'pppoe_id','id')->withDefault(); 
    }
    public function member(){
        return $this->belongsTo(Member::class, 'member_id','id')->withDefault(); 
    }
}
