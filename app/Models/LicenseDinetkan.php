<?php

namespace App\Models;

use App\Models\Traits\Userstamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenseDinetkan extends Model
{
    use HasFactory;
    use Userstamps;
//    protected $connection = 'db_skuy';
    protected $table = 'license_dinetkan';
    protected $fillable = [
        'name',
        'price',
//        'limit_nas',
//        'limit_pppoe',
//        'limit_hs',
//        'limit_vpn',
//        'limit_vpn_remote',
//        'limit_user',
//        'olt_epon_limit',
//        'olt_gpon_limit',
//        'payment_gateway',
//        'whatsapp',
//        'invoice_addon',
//        'olt_epon',
//        'olt_gpon',
//        'olt_models',
//        'max_buy',
//        'color',
        'descriptions',
        'capacity',
        'category_id',
        'type',
        'ppn',
        'price_otc',
        'ppn_otc',
        'komisi_mitra',
        'create_at_by','update_at_by'
    ];

    protected $casts = [
        'payment_gateway' => 'boolean',
        'whatsapp' => 'boolean',
        'olt_epon' => 'boolean',
        'olt_gpon' => 'boolean',
        'olt_models' => 'array',
    ];

    public function adminInvoice()
    {
        return $this->morphOne(AdminInvoice::class, 'itemable');
    }

    public function category()
    {
        return $this->belongsTo(CategoryLicenseDinetkan::class, 'category_id', 'id');
    }

    public function getPricePlanAttribute()
    {
        $value = $this->price;

        $value = intval($value);

        if ($value < 1000) {
            return $value . 'rb';
        } elseif ($value < 1000000) {
            $value = $value / 1000;
            return intval($value) . 'rb';
        }

        $value = $value / 1000000;
        return intval($value) . 'jt';
    }
}
