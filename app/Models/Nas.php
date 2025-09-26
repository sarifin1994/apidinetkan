<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PppoeUser;

class Nas extends Model
{
    use HasFactory;
    protected $connection = 'db_profile';
    protected $table = 'nas';
    protected $fillable = [
        'group_id',
        'name',
        'ip_router',
        // 'ip_radius',
        'secret',
        'timezone',
    ];
    public function users(){
        return $this->hasMany(PppoeUser::class);
    }
}
