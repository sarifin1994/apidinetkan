<?php

namespace App\Models\Hotspot;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Mikrotik\Nas;
use App\Models\Radius\RadiusSession;
use App\Models\Pppoe\PppoeProfile;
use App\Models\Hotspot\HotspotProfile;
use App\Models\Mapping\Pop;
use App\Models\Mapping\Odp;
use App\Models\Partnership\Mitra;
use App\Models\Invoice\Invoice;
use App\Models\Partnership\Reseller;

class HotspotUser extends Model
{
    use HasFactory;
    protected $connection = 'frradius_auth';
    protected $table = 'user_hs';
    protected $fillable = ['shortname', 'username', 'attribute', 'op', 'value', 'profile', 'nas', 'server', 'remark', 'status', 'reseller_id', 'start_time', 'end_time', 'statusPayment', 'created_by', 'status_billing'];

    public function radius()
    {
        return $this->belongsTo(Nas::class, 'nas', 'ip_router')->withDefault();
    }
    public function reseller()
    {
        return $this->belongsTo(Reseller::class, 'reseller_id', 'id')->withDefault();
    }
    public function rprofile()
    {
        return $this->belongsTo(HotspotProfile::class, 'profile', 'name')->where('shortname', multi_auth()->shortname)->withDefault();
    }
    public function session()
    {
        return $this->belongsTo(RadiusSession::class, 'username', 'username')->where('shortname', multi_auth()->shortname)->withDefault();
    }

    public function relatedRemarks()
    {
        return $this->hasMany(HotspotUser::class, 'remark', 'remark')->where('shortname', multi_auth()->shortname); // pastikan hanya menghitung remark dari shortname yang sama
    }

    // untuk hotspot expired command
    public function c_profile()
    {
        return $this->hasOne(HotspotProfile::class, 'name', 'profile');
    }
}
