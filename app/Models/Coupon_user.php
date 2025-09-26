<?php

namespace App\Models;

use App\Enums\UserStatusEnum;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Coupon_user extends Model
{
    use HasFactory;
    // protected $connection = 'db_skuy';
    protected $table = 'coupon_user';
    protected $fillable = [
        'user_id',
        'coupon_id'
    ];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id','id');
    }
}
