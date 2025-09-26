<?php

namespace App\Models;

use App\Enums\UserStatusEnum;
use App\Models\Traits\Userstamps;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class MasterMikrotik extends Model
{
    use HasFactory, Notifiable;
    use Userstamps;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $connection = 'db_skuy';
    protected $table = 'master_mikrotik';
    protected $fillable = [
        'name',
        'ip',
        'port',
        'username',
        'password',
        'time_out',
        'create_at_by','update_at_by'
    ];
}
