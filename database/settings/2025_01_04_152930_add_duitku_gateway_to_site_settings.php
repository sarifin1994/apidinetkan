<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('site.active_gateway', 'tripay');
        $this->migrator->add('site.duitku_merchant_code', '');
        $this->migrator->add('site.duitku_api_key', '');
        $this->migrator->add('site.duitku_sandbox', true);
    }
};
