<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    use HasFactory;
    // protected $connection = 'db_skuy';
    protected $table = 'license';
    protected $fillable = [
        'name',
        'price',
        'limit_nas',
        'limit_pppoe',
        'limit_hs',
        'limit_vpn',
        'limit_vpn_remote',
        'limit_user',
        'olt_epon_limit',
        'olt_gpon_limit',
        'payment_gateway',
        'whatsapp',
        'invoice_addon',
        'olt_epon',
        'olt_gpon',
        'olt_models',
        'max_buy',
        'color',
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
