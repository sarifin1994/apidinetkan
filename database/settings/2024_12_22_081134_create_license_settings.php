<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('license.day_before_due', 0);
        $this->migrator->add('license.invoice_created_template', '');
        $this->migrator->add('license.invoice_reminder_template', '');
        $this->migrator->add('license.invoice_overdue_template', '');
        $this->migrator->add('license.invoice_paid_template', '');
    }
};
