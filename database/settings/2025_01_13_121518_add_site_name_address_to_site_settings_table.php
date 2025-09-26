<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('site.name', 'Radius');
        $this->migrator->add('site.address', 'Jl. Raya Radius No. 1');
    }
};
