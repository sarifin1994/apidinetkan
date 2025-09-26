<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PppoeUser;
use App\Models\PppoeProfile;

class Member extends Model
{
    use HasFactory;
    // protected $connection = 'frradius';
    protected $table = 'frradius.member';
    protected $fillable = [
        'group_id',
        'id_member_new',
        'id_member',
        'full_name',
        'email',
        'wa',
        'address',
        'status',
        'no_ktp',
        'npwp',
        'latitude',
        'longitude',
        'province_id',
        'regency_id',
        'district_id',
        'village_id'
    ];

    public function ppp()
    {
        return $this->belongsTo(PppoeUser::class, 'pppoe_id', 'id')->withDefault();
    }

    public function profile()
    {
        return $this->belongsTo(PppoeProfile::class, 'profile_id', 'id')->withDefault();
    }

    public function invoices()
    {
        return $this->hasManyThrough(Invoice::class, PppoeMember::class, 'member_id', 'pppoe_member_id', 'id', 'id')
            ->orderBy('invoice.id', 'desc');
    }

    public function invoiceforsuspend()
    {
        return $this->hasMany(Invoice::class, 'member_id', 'id')->orderBy('id', 'desc')->limit(1);
    }

    public function pppoes()
    {
        return $this->hasManyThrough(PppoeUser::class, PppoeMember::class, 'member_id', 'id', 'id', 'pppoe_id');
    }

    public function services()
    {
        return $this->hasMany(PppoeMember::class, 'member_id', 'id');
    }
}
