<?php

namespace App\Http\Controllers\Dinetkan;

use App\Http\Controllers\Controller;
use App\Settings\LicenseDinetkanSettings;
use App\Settings\LicenseSettings;
use App\Settings\SiteDinetkanSettings;
use Illuminate\Http\Request;

class SettingsDinetkanController extends Controller
{
    public function index(SiteDinetkanSettings $settings, LicenseDinetkanSettings $licenseSettings)
    {
        return view('backend.dinetkan.settings_dinetkan', compact('settings', 'licenseSettings'));
    }

    public function updateSite(Request $request, SiteDinetkanSettings $settings)
    {
        $settings->name = $request->site_name;
        $settings->address = $request->site_address;
        $settings->siklus_pembayaran = $request->siklus_pembayaran;
        $settings->save();

        return back()->with('success', 'Successfully updated settings');
    }

    public function updateLicenseMitra(Request $request, LicenseDinetkanSettings $licenseSettings)
    {
        $licenseSettings->ppn_product_mitra = $request->ppn_product_mitra;
        $licenseSettings->bhp_product_mitra = $request->bhp_product_mitra;
        $licenseSettings->uso_product_mitra = $request->uso_product_mitra;
        $licenseSettings->save();

        return back()->with('success', 'Successfully updated settings');
    }

    public function updateTripay(Request $request, SiteDinetkanSettings $settings)
    {
        $settings->active_gateway = $request->active_gateway;
//        $settings->tripay_merchant_code = $request->tripay_merchant_code;
//        $settings->tripay_api_key = $request->tripay_api_key;
//        $settings->tripay_private_key = $request->tripay_private_key;
//        $settings->tripay_sandbox = $request->tripay_sandbox;
        $settings->duitku_merchant_code = $request->duitku_merchant_code;
        $settings->duitku_api_key = $request->duitku_api_key;
        $settings->duitku_sandbox = $request->duitku_sandbox;
        $settings->ppn = $request->ppn;
        $settings->admin_fee = $request->admin_fee;
        $settings->save();

        return back()->with('success', 'Successfully updated settings');
    }

    public function updateLicense(Request $request, LicenseSettings $settings)
    {
        $settings->day_before_due = $request->day_before_due;
        $settings->day_after_due = $request->day_after_due;
        $settings->invoice_created_template = $request->invoice_created_template;
        $settings->invoice_reminder_template = $request->invoice_reminder_template;
        $settings->invoice_overdue_template = $request->invoice_overdue_template;
        $settings->invoice_paid_template = $request->invoice_paid_template;
        $settings->save();

        return back()->with('success', 'Successfully updated settings');
    }
}
