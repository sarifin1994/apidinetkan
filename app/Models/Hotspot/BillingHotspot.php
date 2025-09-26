<?php

namespace App\Models\Hotspot;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class BillingHotspot extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'billing_hotspot';
    protected $fillable = [
        'id_hotspot_profile',
        'name_hotspot_profile',
        'price',
        'virtual_account',
        'bank',
        'bank_name',
        'status',
        'whatsapp',
        'email',
        'username',
        'password',
        'trx_no',
        'reference',
        'callback_url'
    ];
}
