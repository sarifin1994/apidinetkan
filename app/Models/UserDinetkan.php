<?php

namespace App\Models;

use App\Enums\UserStatusEnum;
use App\Models\Traits\Userstamps;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UserDinetkan extends Model
{
    use HasFactory, Notifiable, Userstamps;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
//    // protected $connection = 'frradius';
    protected $table = 'users';
    protected $fillable = [
        'id_group',
        'shortname',
        'name',
        'email',
        'whatsapp',
        'username',
        'password',
        'role',
        'status',
        'reseller_id',
        'order_license_id',
        'license_id',
        'next_due',
        'vlan',
        'metro',
        'metro_id',
        'vendor',
        'trafic_mrtg',
        'ip_prefix',
        'is_dinetkan',
        'otc_license_dinetkan_id',
        'mrc_license_dinetkan_id',
        'dinetkan_user_id',
        'first_name',
        'last_name',
        'id_card',
        'npwp',
        'latitude',
        'longitude',
        'province_id',
        'regency_id',
        'district_id',
        'village_id',
        'tree',
        'node',
        'graph',
        'group_id',
        'group_name',
        'dinetkan_next_due',
        'is_reguler',
        'address',
        'active_date',
        'remainder_day',
        'payment_date',
        'payment_siklus',
        'payment_method',
        'prorata',
        'p_id',
        'is_import',
        'id_mitra',
        'dinetkan_license_id',
        'id_mitra_sales',
        'created_by',
        'create_at_by','update_at_by'
    ];

//    protected $casts = [
//        'status' => UserStatusEnum::class,
//    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    protected $appends = ['is_active'];

    public function company()
    {
        return $this->belongsTo(Company::class, 'id', 'group_id');
    }

    public function pppoeUsers()
    {
        return $this->hasMany(PppoeUser::class, 'group_id', 'id_group');
    }

    public function hotspotUsers()
    {
        return $this->hasMany(HotspotUser::class, 'group_id', 'id_group');
    }

    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class, 'user_id', 'id');
    }

    public function license()
    {
        return $this->belongsTo(LicenseDinetkan::class);
    }

    public function license_otc()
    {
        return $this->belongsTo(LicenseDinetkan::class,'otc_license_dinetkan_id','id');
    }

    public function license_mrc()
    {
        return $this->belongsTo(LicenseDinetkan::class,'mrc_license_dinetkan_id','id');
    }

    public function tripay()
    {
        return $this->hasOne(Tripay::class, 'group_id', 'id_group');
    }

    public function midtrans()
    {
        return $this->hasOne(Midtrans::class, 'group_id', 'id_group');
    }

    public function duitku()
    {
        return $this->hasOne(Duitku::class, 'group_id', 'id_group');
    }

    public function xendit()
    {
        return $this->hasOne(Xendit::class, 'group_id', 'id_group');
    }

    public function invoices()
    {
        return $this->hasMany(AdminInvoice::class, 'group_id', 'id_group');
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === UserStatusEnum::ACTIVE;
    }

    public function province()
    {
        return $this->belongsTo(Province::class,'province_id','id');
    }

    public function regency()
    {
        return $this->belongsTo(Regencies::class,'regency_id','id');
    }

    public function district()
    {
        return $this->belongsTo(Districts::class,'district_id','id');
    }

    public function village()
    {
        return $this->belongsTo(Villages::class,'village_id','id');
    }
}
