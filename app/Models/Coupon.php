<?php

namespace App\Models;

use App\Enums\UserStatusEnum;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Coupon extends Model
{
    use HasFactory;
    // protected $connection = 'db_skuy';
    protected $table = 'coupon';
    protected $fillable = [
        'coupon_name',
        'used',
        'start_date',
        'end_date',
        'type',
        'percent',
        'nominal'
    ];

    public function user()
    {
        return $this->hasMany(Coupon_user::class, 'coupon_id', 'id');
    }

    public function license()
    {
        return $this->hasMany(Coupon_license::class, 'coupon_id', 'id');
    }
}
