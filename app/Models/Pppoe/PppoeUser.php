<?php

namespace App\Models\Pppoe;

use App\Models\MappingAdons;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Mikrotik\Nas;
use App\Models\Radius\RadiusSession;
use App\Models\Pppoe\PppoeProfile;
use App\Models\Mapping\Pop;
use App\Models\Mapping\Odp;
use App\Models\Partnership\Mitra;
use App\Models\Invoice\Invoice;
use App\Models\Radius\RadiusNas;

class PppoeUser extends Model
{
    use HasFactory;
    protected $connection = 'frradius_auth';
    protected $table = 'user_pppoe';
    protected $fillable = [
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
        'id_pelanggan',
        'profile_id',
        'mitra_id',
        'kode_area', 
        'kode_odp',
        'full_name',
        'address',
        'payment_type',
        'billing_period',
        'ppn',  
        'discount',
        'reg_date',
        'next_due',
        'next_invoice',
        'tgl',
        'wa',
        'latitude',
        'longitude',
        'created_by',
        'email',
        'ktp',
        'npwp',
        'province_id',
        'regency_id',
        'district_id',
        'village_id',
        'pks',
        'sn_modem'
    ];

    public function mnas(){
        return $this->belongsTo(Nas::class, 'nas','ip_router')->where('shortname',multi_auth()->shortname)->withDefault(); 
    }
    public function session(){
        return $this->belongsTo(RadiusSession::class, 'username','username')->where('shortname',multi_auth()->shortname)->withDefault(); 
    }

    // public function rprofile(){
    //     return $this->belongsTo(PppoeProfile::class, 'profile','name')->where('shortname',multi_auth()->shortname)->withDefault(); 
    // }

    public function rprofile(){
        return $this->belongsTo(PppoeProfile::class, 'profile_id','id')->where('shortname',multi_auth()->shortname)->withDefault(); 
    }

    public function rarea(){
        return $this->belongsTo(Pop::class, 'kode_area','kode_area')->where('shortname',multi_auth()->shortname)->withDefault(); 
    }

    public function rmitra(){
        return $this->belongsTo(Mitra::class, 'mitra_id','id')->withDefault(); 
    }
    public function rodp(){
        return $this->belongsTo(Odp::class, 'kode_odp','kode_odp')->where('shortname',multi_auth()->shortname)->withDefault(); 
    }
    public function rinvoice(){
        return $this->hasMany(Invoice::class, 'id_pelanggan', 'id');
    }

    // relasi untuk command
    public function c_nas(){
        return $this->hasOne(RadiusNas::class, 'nasname','nas'); 
    }

    public function c_profile(){
        return $this->hasOne(PppoeProfile::class, 'id','profile_id'); 
    }

    public function addon(){
        return $this->hasMany(MappingAdons::class, 'id_pelanggan_pppoe','id_pelanggan');
    }


}
