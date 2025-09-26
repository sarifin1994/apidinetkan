<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SiteSettings extends Settings
{
    public string $name;

    public string $address;

    public string $active_gateway;

    public string $tripay_merchant_code;

    public string $tripay_api_key;

    public string $tripay_private_key;

    public bool $tripay_sandbox;

    public string $duitku_merchant_code;

    public string $duitku_api_key;

    public bool $duitku_sandbox;

    public int $ppn;

    public int $admin_fee;

    public string $monitoring_notif_email;

    public string $monitoring_notif_whatsapp;

    public static function group(): string
    {
        return 'site';
    }
}
