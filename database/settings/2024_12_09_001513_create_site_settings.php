<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('site.tripay_merchant_code', '');
        $this->migrator->add('site.tripay_api_key', '');
        $this->migrator->add('site.tripay_private_key', '');
        $this->migrator->add('site.tripay_sandbox', false);
    }
};
