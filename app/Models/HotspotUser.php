<?php

namespace App\Models;

use App\Enums\HotspotUserStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RadiusSession;
use App\Models\Nas;
use App\Models\HotspotReseller;
use App\Models\HotspotProfile;

class HotspotUser extends Model
{
    use HasFactory;
    protected $connection = 'db_radius';
    protected $table = 'user_hs';
    protected $fillable = [
        'group_id',
        'shortname',
        'username',
        'attribute',
        'op',
        'value',
        'profile',
        'nas',
        'server',
        'remark',
        'status',
        'reseller_id',
        'start_time',
        'end_time',
        'admin',
        'statusPayment',
    ];

    protected $casts = [
        'status' => HotspotUserStatusEnum::class,
    ];

    public function radius()
    {
        return $this->belongsTo(Nas::class, 'nas', 'ip_router')->withDefault();
    }
    public function reseller()
    {
        return $this->belongsTo(HotspotReseller::class, 'reseller_id', 'id')->withDefault();
    }
    public function rprofile()
    {
        return $this->belongsTo(HotspotProfile::class, 'profile', 'name')->withDefault();
    }
    public function session()
    {
        return $this->belongsTo(RadiusSession::class, 'username', 'username')->withDefault();
    }
}
