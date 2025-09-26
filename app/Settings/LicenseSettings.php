<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class LicenseSettings extends Settings
{
    public int $day_before_due;

    public string $invoice_created_template;

    public string $invoice_reminder_template;

    public string $invoice_overdue_template;

    public string $invoice_paid_template;

    public static function group(): string
    {
        return 'license';
    }
}
