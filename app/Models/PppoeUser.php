<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Nas;
use App\Models\PppoeProfile;
use App\Models\Area;
use App\Models\Odp;
use App\Models\RadiusSession;
use App\Models\Member;

class PppoeUser extends Model
{
    use HasFactory;
    protected $connection = 'db_radius';
    protected $table = 'db_radius.user_pppoe';
    protected $fillable = [
        'group_id',
        'shortname',
        'username',
        'attribute',
        'op',
        'value',
        'profile',
        'nas',
        'service',
        'ip_address',
        'status',
        'type',
        'lock_mac',
        'mac',
        'member_name',
        'kode_area',
        'kode_odp',
    ];

    public function radius()
    {
        return $this->belongsTo(Nas::class, 'nas', 'ip_router')->withDefault();
    }

    public function rprofile()
    {
        return $this->belongsTo(PppoeProfile::class, 'profile', 'name')->where('group_id', auth()->user()->id_group)->withDefault();
    }

    public function rarea()
    {
        return $this->belongsTo(Area::class, 'kode_area', 'kode_area')->withDefault();
    }

    public function rodp()
    {
        return $this->belongsTo(Odp::class, 'kode_odp', 'kode_odp')->withDefault();
    }

    public function session()
    {
        return $this->belongsTo(RadiusSession::class, 'username', 'username')->withDefault();
    }

    public function member()
    {
        return $this->belongsTo(PppoeMember::class, 'id', 'pppoe_id');
    }

    public function data()
    {
        return $this->belongsTo(PppoeMember::class, 'id', 'pppoe_id');
    }

    public function members()
    {
        return $this->hasManyThrough(Member::class, PppoeMember::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'pppoe_id', 'id')->orderBy('id', 'desc');
    }
}
