<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ServiceStatusEnum;

class MappingUserLicense extends Model
{
    use HasFactory;
    // // protected $connection = 'frradius';
    protected $table = 'mapping_user_license';
    protected $fillable = [
        'dinetkan_user_id',
        'license_id',
        'status',
        'no_invoice',
        'due_date',
        'category_id',
        'active_date',
        'remainder_day',
        'payment_date',
        'payment_siklus',
        'payment_method',
        'prorata',
        'id_mitra',
        'service_id',
        'notes',

    ];

    protected $casts = [
        'status' => ServiceStatusEnum::class,
    ];

    public function user()
    {
        return $this->belongsTo(UserDinetkan::class, 'dinetkan_user_id', 'dinetkan_user_id');
    }

    public function service()
    {
        return $this->belongsTo(LicenseDinetkan::class, 'license_id', 'id');
    }

    public function adon()
    {
        return $this->belongsTo(MappingAdons::class, 'id_mapping', 'id');
    }

    public function service_detail()
    {
        return $this->belongsTo(ServiceDetail::class, 'service_id', 'service_id');
    }

    public function service_libre()
    {
        return $this->hasMany(ServiceLibre::class, 'service_id', 'service_id');
    }

    public function graph()
    {
        return $this->belongsTo(UserDinetkanGraph::class, 'service_id', 'service_id');
    }
}
