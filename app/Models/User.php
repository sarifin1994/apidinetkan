<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Owner\License;
use App\Models\Pppoe\PppoeUser;
use App\Models\Hotspot\HotspotUser;
use App\Models\Mikrotik\Nas;
use App\Models\Invoice\Invoice;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'shortname',
        'name',
        'email',
        'password',
        'username',
        'whatsapp',
        'role',
        'order',
        'order_number',
        'order_status',
        'license_id',
        'next_due',
        'status',
        'otp',
        'otp_expires_at',
        'discount',
        'is_dinetkan',
        'is_reguler',
        'dinetkan_user_id',
        'first_name',
        'last_name',
        'active_date',
        'remainder_day',
        'payment_date',
        'payment_siklus',
        'payment_method',
        'prorata',
        'p_id',
        'is_import',
        'id_mitra',
        'id_mitra_sales'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function license()
    {
        return $this->belongsTo(License::class, 'license_id', 'id')->withDefault();
    }
    public function order_license()
    {
        return $this->belongsTo(License::class, 'order', 'id')->withDefault();
    }
    public function hs()
    {
        return $this->belongsTo(HotspotUser::class, 'shortname', 'shortname')->withDefault();
    }
    public function pppoe()
    {
        return $this->belongsTo(PppoeUser::class, 'shortname', 'shortname')->withDefault();
    }
    public function nas()
    {
        return $this->belongsTo(Nas::class, 'shortname', 'shortname')->withDefault();
    }

    // untuk commmand

    public function c_pppoe()
    {
        return $this->hasMany(PppoeUser::class, 'shortname', 'shortname');
    }

    public function c_pppoe_fixed()
    {
        return $this->hasMany(PppoeUser::class, 'shortname', 'shortname')->where('billing_period', 'Fixed Date');
    }
    public function c_pppoe_renewable()
    {
        return $this->hasMany(PppoeUser::class, 'shortname', 'shortname')->where('billing_period', 'Renewable');
    }
    public function c_pppoe_cycle()
    {
        return $this->hasMany(PppoeUser::class, 'shortname', 'shortname')->where('billing_period', 'Billing Cycle');
    }
    public function c_pppoe_reminder()
    {
        return $this->hasMany(PppoeUser::class, 'shortname', 'shortname');
    }

    // untuk suspend overdue
    public function c_pppoe1()
    {
        return $this->hasMany(PppoeUser::class, 'shortname', 'shortname')->where('status', 1)->whereNotNull('payment_type');
    }
    public function c_invoice()
    {
        return $this->hasMany(Invoice::class, 'shortname', 'shortname')->where('status', 'unpaid');
    }

    // untuk expired hotspot command
    public function c_hotspot()
    {
        return $this->hasMany(HotspotUser::class, 'shortname', 'shortname')->where('status', 2)->whereNull('end_time');
    }
}
