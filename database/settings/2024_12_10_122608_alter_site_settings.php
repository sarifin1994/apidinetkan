<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('site.ppn', 0);
        $this->migrator->add('site.admin_fee', 0);
    }
};
