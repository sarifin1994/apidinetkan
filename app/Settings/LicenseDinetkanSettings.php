<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class LicenseDinetkanSettings extends Settings
{
    public int $day_before_due;

    public int $day_after_due;

    public string $invoice_created_template;

    public string $invoice_reminder_template;

    public string $invoice_overdue_template;

    public string $invoice_paid_template;

    public string $ppn_product_mitra;

    public string $bhp_product_mitra;

    public string $uso_product_mitra;

    public static function group(): string
    {
        return 'license_dinetkan';
    }
}
