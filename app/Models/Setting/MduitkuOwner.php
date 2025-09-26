<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MduitkuOwner extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'setting_duitku_owner';
    protected $fillable = [
        'shortname',
        'id_merchant',
        'api_key',
        'callback_url',
        'return_url',
        'admin_fee',
        'status',
        'environment',
        'url_production',
        'url_development'
    ];
}
