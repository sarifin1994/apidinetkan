<?php

use App\Http\Controllers\OltDeviceController;
use App\Http\Middleware\StatusMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
});

Auth::routes();

Route::get('register_dinetkan', [\App\Http\Controllers\Auth\RegisterDinetkanController::class, 'showRegistrationForm'])->name('register_dinetkan');
Route::post('register_dinetkan', [\App\Http\Controllers\Auth\RegisterDinetkanController::class, 'register'])->name('register_dinetkan');

Route::get('invoice/pdf/{id}', [\App\Http\Controllers\Billing\UnpaidController::class, 'printInvoice'])->name('invoice.print');
Route::get('invoice/{id}', [\App\Http\Controllers\Payment\MemberInvoiceController::class, 'pay'])->name('invoice.pay');
Route::post('invoice/{id}', [\App\Http\Controllers\Payment\MemberInvoiceController::class, 'processPayment'])->name('invoice.process');
Route::get('admin/invoice/{id}', [\App\Http\Controllers\Admin\Account\LicenseController::class, 'invoice'])->name('admin.invoice');
Route::post('admin/invoice/{license}', [\App\Http\Controllers\Admin\Account\LicenseController::class, 'placeOrder'])->name('admin.invoice.place-order');
Route::post('admin/invoice/{id}/pay', [\App\Http\Controllers\Admin\Account\LicenseController::class, 'pay'])->name('admin.invoice.pay');
Route::get('admin/account/licensing/{id}/thank-you', [\App\Http\Controllers\Admin\Account\LicenseController::class, 'thankYou'])->name('admin.account.licensing.thank-you');

// dinetkan
Route::get('admin/invoice_dinetkan/{id}', [\App\Http\Controllers\Admin\Account\LicenseDinetkanController::class, 'invoice'])->name('admin.invoice_dinetkan');
Route::get('admin/invoice_dinetkan/pdf/{id}', [\App\Http\Controllers\Admin\Account\LicenseDinetkanController::class, 'invoice_pdf'])->name('admin.invoice_dinetkan.pdf');
Route::post('admin/invoice_dinetkan/{license}', [\App\Http\Controllers\Admin\Account\LicenseDinetkanController::class, 'placeOrder'])->name('admin.invoice_dinetkan.place-order');
Route::post('admin/invoice_dinetkan/{id}/pay', [\App\Http\Controllers\Admin\Account\LicenseDinetkanController::class, 'pay'])->name('admin.invoice_dinetkan.pay');
Route::get('admin/invoice_dinetkan_search', [\App\Http\Controllers\Admin\Account\LicenseDinetkanController::class, 'search'])->name('admin.invoice_dinetkan_search');

Route::get('admin/settings/master/geo/provinces', [\App\Http\Controllers\Settings\GeoMasterController::class, 'province']);
Route::get('admin/settings/master/geo/regencies/{province_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'regencies']);
Route::get('admin/settings/master/geo/districts/{regency_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'districts']);
Route::get('admin/settings/master/geo/villages/{district_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'villages']);

Route::get('admin/settings/master/geo/provinces_single/{province_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'province_single']);
Route::get('admin/settings/master/geo/regencies_single/{regency_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'regencies_single']);
Route::get('admin/settings/master/geo/districts_single/{district_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'districts_single']);
Route::get('admin/settings/master/geo/villages_single/{village_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'villages_single']);
// dinetkan

Route::post('users/logout-as', [\App\Http\Controllers\Dinetkan\UserController::class, 'logoutAsUser'])->name('logout-as');
Route::group(['middleware' => ['auth']], function () {
    Route::get('/logout', function () {
        Auth::logout();
        return redirect()->route('login');
    })->name('logout-alt');

    Route::group(['middleware' => ['role:Admin'], 'as' => 'admin.', 'prefix' => 'admin'], function () {
        Route::group(['prefix' => 'account', 'as' => 'account.'], function () {
            Route::group(['prefix' => 'invoice', 'as' => 'invoice.'], function () {
                Route::get('/', [\App\Http\Controllers\Admin\Account\InvoiceController::class, 'index'])->name('index');
                Route::get('/unpaid', [\App\Http\Controllers\Admin\Account\InvoiceController::class, 'unpaid'])->name('unpaid');
                Route::get('/paid', [\App\Http\Controllers\Admin\Account\InvoiceController::class, 'paid'])->name('paid');
                Route::get('/expired', [\App\Http\Controllers\Admin\Account\InvoiceController::class, 'expired'])->name('expired');
            });

            Route::group(['prefix' => 'invoice_dinetkan', 'as' => 'invoice_dinetkan.'], function () {
                Route::get('/', [\App\Http\Controllers\Admin\Account\InvoiceDinetkanController::class, 'index'])->name('index');
                Route::get('/unpaid', [\App\Http\Controllers\Admin\Account\InvoiceDinetkanController::class, 'unpaid'])->name('unpaid');
                Route::get('/paid', [\App\Http\Controllers\Admin\Account\InvoiceDinetkanController::class, 'paid'])->name('paid');
                Route::get('/expired', [\App\Http\Controllers\Admin\Account\InvoiceDinetkanController::class, 'expired'])->name('expired');

                Route::get('/order', [\App\Http\Controllers\Admin\Account\InvoiceDinetkanController::class, 'order'])->name('order');
                Route::get('/order/active', [\App\Http\Controllers\Admin\Account\InvoiceDinetkanController::class, 'active'])->name('order.active');
                Route::get('/order/inactive', [\App\Http\Controllers\Admin\Account\InvoiceDinetkanController::class, 'inactive'])->name('order.inactive');
                Route::get('/order/overdue', [\App\Http\Controllers\Admin\Account\InvoiceDinetkanController::class, 'overdue'])->name('order.overdue');
                Route::get('/order/suspend', [\App\Http\Controllers\Admin\Account\InvoiceDinetkanController::class, 'suspend'])->name('order.suspend');

            });
        });
    });
});

Route::group(['as' => 'notification.', 'prefix' => 'notification'], function () {
    Route::post('/midtrans', [\App\Http\Controllers\Payment\MemberInvoiceController::class, 'midtransNotification'])->name('midtrans');
    Route::post('/tripay', [\App\Http\Controllers\Payment\MemberInvoiceController::class, 'tripayNotification'])->name('tripay');
    Route::post('/duitku', [\App\Http\Controllers\Payment\MemberInvoiceController::class, 'duitkuNotification'])->name('duitku');
    Route::post('/xendit', [\App\Http\Controllers\Payment\MemberInvoiceController::class, 'xenditNotification'])->name('xendit');
    Route::post('/admin/tripay', [\App\Http\Controllers\Admin\Account\LicenseController::class, 'tripayNotification'])->name('admin.tripay');
    Route::post('/admin/duitku', [\App\Http\Controllers\Admin\Account\LicenseController::class, 'duitkuNotification'])->name('admin.duitku');
    Route::post('/admin/duitku_dinetkan', [\App\Http\Controllers\Admin\Account\LicenseDinetkanController::class, 'duitkuNotification'])->name('admin.duitku_dinetkan');


    // whatsapp api
    Route::group(['prefix' => 'whatsapp', 'as' => 'whatsapp.'], function () {
        Route::post('/receive_qr/{userid}',[\App\Http\Controllers\Callback\WhatsappCallbackController::class,'receive_qr'])->name('receive_qr');
    });
    // whatsapp api
});

Route::group(['middleware' => ['auth', 'status:1']], function () {
    Route::group(['middleware' => ['role:Owner'], 'as' => 'owner.', 'prefix' => 'owner'], function () {
        Route::get('/', [\App\Http\Controllers\Dinetkan\DashboardController::class, 'index'])->name('dashboard')->withoutMiddleware(StatusMiddleware::class);
        Route::get('/charts/revenue', [\App\Http\Controllers\Dinetkan\DashboardController::class, 'monthlyRevenueChart'])->name('charts.revenue');
        Route::get('/charts/daily-revenue', [\App\Http\Controllers\Dinetkan\DashboardController::class, 'dailyRevenueChart'])->name('charts.daily-revenue');
        Route::get('/charts/recent-admins', [\App\Http\Controllers\Dinetkan\DashboardController::class, 'recentAdminsChart'])->name('charts.recent-admins');

        Route::get('/license', [\App\Http\Controllers\Dinetkan\LicenseController::class, 'index'])->name('license');
        Route::post('/license', [\App\Http\Controllers\Dinetkan\LicenseController::class, 'store'])->name('license.store');
        Route::get('/license/{license}', [\App\Http\Controllers\Dinetkan\LicenseController::class, 'edit'])->name('license.edit');
        Route::put('/license/{license}', [\App\Http\Controllers\Dinetkan\LicenseController::class, 'update'])->name('license.update');
        Route::delete('/license/{license}', [\App\Http\Controllers\Dinetkan\LicenseController::class, 'destroy'])->name('license.destroy');

        Route::get('/users', [\App\Http\Controllers\Dinetkan\UserController::class, 'index'])->name('users');
        Route::post('/users', [\App\Http\Controllers\Dinetkan\UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [\App\Http\Controllers\Dinetkan\UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [\App\Http\Controllers\Dinetkan\UserController::class, 'update'])->name('users.update');
        Route::put('/users/status/{user}', [\App\Http\Controllers\Dinetkan\UserController::class, 'status'])->name('users.status');
        Route::delete('/users/{user}', [\App\Http\Controllers\Dinetkan\UserController::class, 'destroy'])->name('users.destroy');

        // new user dinetkan
        Route::get('/users_dinetkan', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'index'])->name('users_dinetkan');
        Route::post('/users_dinetkan', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'store'])->name('users_dinetkan.store');
        Route::get('/users_dinetkan/{user}', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'edit'])->name('users_dinetkan.edit');
        Route::put('/users_dinetkan/{user}', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'update'])->name('users_dinetkan.update');
        Route::post('/users_dinetkan/update_new', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'update_new'])->name('users_dinetkan.update_new');
        Route::get('/users_dinetkan/detail_cacti/{dinetkan_user_id}', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'detail_cacti'])->name('users_dinetkan.detail_cacti');
        Route::get('/users_dinetkan/detail/{dinetkan_user_id}', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'detail'])->name('users_dinetkan.detail');
        Route::post('/users_dinetkan/update_cacti/{user}', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'update_cacti'])->name('users_dinetkan.update_cacti');
        Route::post('/users_dinetkan/update_cacti2/{user}', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'update_cacti2'])->name('users_dinetkan.update_cacti2');
        Route::put('/users_dinetkan/status/{user}', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'status'])->name('users_dinetkan.status');
        Route::delete('/users_dinetkan/{user}', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'destroy'])->name('users_dinetkan.destroy');
        Route::get('/users_dinetkan/get_tree_node_mrtg/{id}', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'get_tree_node_mrtg'])->name('users_dinetkan.get_tree_node_mrtg');
        Route::get('/users_dinetkan/get_graph_mrtg/{node}/{page}', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'get_graph_mrtg'])->name('users_dinetkan.get_graph_mrtg');
        Route::get('/users_dinetkan/single_cacti/{id}', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'single_cacti'])->name('users_dinetkan.single_cacti');
        Route::delete('/users_dinetkan/delete_cacti/{id}', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'delete_cacti'])->name('users_dinetkan.delete_cacti');
        Route::get('/users_dinetkan_service', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'service'])->name('users_dinetkan_service');
        Route::post('/users_dinetkan/update_doc_info_dinetkan', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'update_doc_info_dinetkan'])->name('users_dinetkan.update_doc_info_dinetkan');
        Route::get('/users_dinetkan_service/show_file/{id}', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'show_file'])->name('users_dinetkan.show_file');
        Route::get('/users_dinetkan/detail2/{dinetkan_user_id}', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'detail2'])->name('users_dinetkan.detail2');
        Route::post('/users_dinetkan/accept', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'accept'])->name('users_dinetkan.accept');


        Route::get('/license_dinetkan', [\App\Http\Controllers\Dinetkan\LicenseDinetkanController::class, 'index'])->name('license_dinetkan');
        Route::post('/license_dinetkan', [\App\Http\Controllers\Dinetkan\LicenseDinetkanController::class, 'store'])->name('license_dinetkan.store');
        Route::get('/license_dinetkan/{license}', [\App\Http\Controllers\Dinetkan\LicenseDinetkanController::class, 'edit'])->name('license_dinetkan.edit');
        Route::put('/license_dinetkan/{license}', [\App\Http\Controllers\Dinetkan\LicenseDinetkanController::class, 'update'])->name('license_dinetkan.update');
        Route::delete('/license_dinetkan/{license}', [\App\Http\Controllers\Dinetkan\LicenseDinetkanController::class, 'destroy'])->name('license_dinetkan.destroy');
        Route::get('/license_dinetkan/by_category/{category_id}/{type}', [\App\Http\Controllers\Dinetkan\LicenseDinetkanController::class, 'by_category']);


        Route::get('/invoice_dinetkan/', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'index'])->name('invoice_dinetkan.index');
        Route::get('/invoice_dinetkan/unpaid', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'unpaid'])->name('invoice_dinetkan.unpaid');
        Route::get('/invoice_dinetkan/paid', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'paid'])->name('invoice_dinetkan.paid');
        Route::get('/invoice_dinetkan/expired', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'expired'])->name('invoice_dinetkan.expired');
        Route::get('/invoice_dinetkan/detail/{id}', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'detail'])->name('invoice_dinetkan.detail');
        Route::post('/invoice_dinetkan/detail_update', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'detail_update'])->name('invoice_dinetkan.detail_update');
        Route::get('/invoice_dinetkan/cancel', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'cancel'])->name('invoice_dinetkan.cancel');

        Route::get('/invoice_dinetkan/order', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'order'])->name('invoice_dinetkan.order');
        Route::get('/invoice_dinetkan/order/active', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'active'])->name('invoice_dinetkan.order.active');
        Route::get('/invoice_dinetkan/order/inactive', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'inactive'])->name('invoice_dinetkan.order.inactive');
        Route::get('/invoice_dinetkan/order/overdue', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'overdue'])->name('invoice_dinetkan.order.overdue');
        Route::get('/invoice_dinetkan/order/suspend', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'suspend'])->name('invoice_dinetkan.order.suspend');
        Route::get('/invoice_dinetkan/order/progress', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'progress'])->name('invoice_dinetkan.order.progress');
        Route::get('/invoice_dinetkan/order/cancel', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'cancel'])->name('invoice_dinetkan.order.cancel');
        Route::get('/invoice_dinetkan/order/new', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'new'])->name('invoice_dinetkan.order.new');
        Route::get('/invoice_dinetkan/order/single_order/{id}', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'single_order'])->name('invoice_dinetkan.order.single_order');
        Route::put('/invoice_dinetkan/order/update_mapping/{id}', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'update_mapping'])->name('invoice_dinetkan.order.update_mapping');
        Route::put('/invoice_dinetkan/order/cancel_mapping/{id}', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'cancel_mapping'])->name('invoice_dinetkan.order.cancel_mapping');

        Route::post('/invoice_dinetkan/create', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'create'])->name('invoice_dinetkan.create');
        Route::get('/invoice_dinetkan/get_tree_node_mrtg/{id}', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'get_tree_node_mrtg'])->name('invoice_dinetkan.get_tree_node_mrtg');
        Route::get('/invoice_dinetkan/get_graph_mrtg/{node}/{page}', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'get_graph_mrtg'])->name('invoice_dinetkan.get_graph_mrtg');
        // new user dinetkan

        Route::get('/users/{user}/login-histories', [\App\Http\Controllers\Dinetkan\UserController::class, 'loginHistories'])->name('users.login-histories');
        Route::get('/users/{user}/login-as', [\App\Http\Controllers\Dinetkan\UserController::class, 'loginAsUser'])->name('users.login-as');

        Route::get('/billing', [\App\Http\Controllers\Dinetkan\BillingController::class, 'index'])->name('billing');
        Route::get('/billing/renew/{user}', [\App\Http\Controllers\Dinetkan\BillingController::class, 'renew'])->name('billing.renew');

        Route::get('/settings', [\App\Http\Controllers\Dinetkan\SettingsController::class, 'index'])->name('settings');
        Route::put('/settings/site', [\App\Http\Controllers\Dinetkan\SettingsController::class, 'updateSite'])->name('settings.update.site');
        Route::put('/settings/tripay', [\App\Http\Controllers\Dinetkan\SettingsController::class, 'updateTripay'])->name('settings.update.tripay');
        Route::put('/settings/license', [\App\Http\Controllers\Dinetkan\SettingsController::class, 'updateLicense'])->name('settings.update.license');
        Route::put('/settings/updatemonitoringnotif', [\App\Http\Controllers\Dinetkan\SettingsController::class, 'updateMonitoringNotif'])->name('settings.update.updatemonitoringnotif');

        // dinetkan
        Route::get('/settings_dinetkan', [\App\Http\Controllers\Dinetkan\SettingsDinetkanController::class, 'index'])->name('settings_dinetkan');
        Route::put('/settings_dinetkan/site', [\App\Http\Controllers\Dinetkan\SettingsDinetkanController::class, 'updateSite'])->name('settings_dinetkan.update.site');
        Route::put('/settings_dinetkan/tripay', [\App\Http\Controllers\Dinetkan\SettingsDinetkanController::class, 'updateTripay'])->name('settings_dinetkan.update.tripay');
        Route::put('/settings_dinetkan/license', [\App\Http\Controllers\Dinetkan\SettingsDinetkanController::class, 'updateLicense'])->name('settings_dinetkan.update.license');
        Route::put('/settings_dinetkan/licensemitra', [\App\Http\Controllers\Dinetkan\SettingsDinetkanController::class, 'updateLicenseMitra'])->name('settings_dinetkan.update.licensemitra');
        // dinetkan

        Route::group(['prefix' => 'licensing', 'as' => 'licensing.'], function () {
            Route::get('/', [\App\Http\Controllers\Admin\Account\LicenseController::class, 'index'])->name('index');
            Route::get('/{id}/pay', [\App\Http\Controllers\Admin\Account\LicenseController::class, 'pay'])->name('pay');
        });

        Route::group(['prefix' => 'account', 'as' => 'account.'], function () {
            Route::get('/profile', [\App\Http\Controllers\Account\ProfileController::class, 'index'])->name('profile.index');
            Route::patch('/profile', [\App\Http\Controllers\Account\ProfileController::class, 'update'])->name('profile.update');
            Route::patch('/profile/password', [\App\Http\Controllers\Account\ChangePasswordController::class, 'update'])->name('profile.password');
        });

        Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])->name('logs');

        // kupon
        Route::get('/coupon', [\App\Http\Controllers\Dinetkan\CouponController::class, 'index'])->name('coupon');
        Route::post('/coupon', [\App\Http\Controllers\Dinetkan\CouponController::class, 'create_coupon'])->name('coupon.create');
        Route::get('/coupon/get_coupon_single/{coupon}', [\App\Http\Controllers\Dinetkan\CouponController::class, 'get_coupon_single']);
        // kupon

        // master pop
        Route::get('/master_pop', [\App\Http\Controllers\Dinetkan\MasterPopController::class, 'index'])->name('master_pop');
        Route::post('/master_pop', [\App\Http\Controllers\Dinetkan\MasterPopController::class, 'store'])->name('master_pop.store');
        Route::post('/master_pop/{id}', [\App\Http\Controllers\Dinetkan\MasterPopController::class, 'delete'])->name('master_pop.delete');
        Route::put('/master_pop/{id}', [\App\Http\Controllers\Dinetkan\MasterPopController::class, 'update'])->name('master_pop.update');
        Route::get('/master_pop/get_single/{id}', [\App\Http\Controllers\Dinetkan\MasterPopController::class, 'single'])->name('master_pop.single');
        // master pop

        // master metro
        Route::get('/master_metro', [\App\Http\Controllers\Dinetkan\MasterMetroController::class, 'index'])->name('master_metro');
        Route::post('/master_metro', [\App\Http\Controllers\Dinetkan\MasterMetroController::class, 'store'])->name('master_metro.store');
        Route::delete('/master_metro/{id}', [\App\Http\Controllers\Dinetkan\MasterMetroController::class, 'delete'])->name('master_metro.delete');
        Route::post('/master_metro/{id}', [\App\Http\Controllers\Dinetkan\MasterMetroController::class, 'update'])->name('master_metro.update');
        Route::get('/master_metro/get_single/{id}', [\App\Http\Controllers\Dinetkan\MasterMetroController::class, 'single'])->name('master_metro.single');
        // master metro

        Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
            Route::group(['prefix' => 'master', 'as' => 'master.'], function () {
                Route::get('/geo/provinces', [\App\Http\Controllers\Settings\GeoMasterController::class, 'province']);
                Route::get('/geo/regencies/{province_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'regencies']);
                Route::get('/geo/districts/{regency_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'districts']);
                Route::get('/geo/villages/{district_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'villages']);

                Route::get('/geo/provinces_single/{province_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'province_single']);
                Route::get('/geo/regencies_single/{regency_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'regencies_single']);
                Route::get('/geo/districts_single/{district_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'districts_single']);
                Route::get('/geo/villages_single/{village_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'villages_single']);
            });
        });
        Route::group(['prefix' => 'pingip', 'as' => 'pingip.'], function () {
            Route::get('/',[\App\Http\Controllers\Dinetkan\PingIPController::class, 'index']);
            Route::get('/test',[\App\Http\Controllers\Dinetkan\PingIPController::class, 'test']);
        });

        Route::group(['prefix' => 'whatsapp', 'as' => 'whatsapp.'], function () {
            Route::get('/', [\App\Http\Controllers\Dinetkan\WhatsappController::class, 'index'])->name('index');
            Route::get('/start', [\App\Http\Controllers\Dinetkan\WhatsappController::class, 'start'])->name('start');
            Route::get('/restart', [\App\Http\Controllers\Dinetkan\WhatsappController::class, 'restart'])->name('restart');
            Route::get('/get_group', [\App\Http\Controllers\Dinetkan\WhatsappController::class, 'get_group'])->name('get_group');
            Route::get('/get_qr', [\App\Http\Controllers\Dinetkan\WhatsappController::class, 'get_qr'])->name('get_qr');
            Route::get('/logout', [\App\Http\Controllers\Dinetkan\WhatsappController::class, 'logout'])->name('logout');
            Route::post('/update_group', [\App\Http\Controllers\Dinetkan\WhatsappController::class, 'update_group'])->name('update_group');
            Route::get('/start_whatsapp', [\App\Http\Controllers\Dinetkan\WhatsappController::class, 'start_whatsapp'])->name('start_whatsapp');
        });
        Route::group(['prefix' => 'monitoring', 'as' => 'monitoring.'], function () {
            Route::group(['prefix' => 'server', 'as' => 'server.'], function () {
                Route::get('/', [\App\Http\Controllers\Dinetkan\MonitoringServerController::class, 'server'])->name('list');
                Route::post('/store', [\App\Http\Controllers\Dinetkan\MonitoringServerController::class, 'server_store'])->name('store');
                Route::get('/single/{id}', [\App\Http\Controllers\Dinetkan\MonitoringServerController::class, 'server_single'])->name('single');
                Route::put('/update/{id}', [\App\Http\Controllers\Dinetkan\MonitoringServerController::class, 'server_update'])->name('update');
                Route::delete('/delete/{id}', [\App\Http\Controllers\Dinetkan\MonitoringServerController::class, 'server_delete'])->name('delete');

                Route::group(['prefix' => 'group', 'as' => 'group.'], function () {
                    Route::get('/', [\App\Http\Controllers\Dinetkan\MonitoringServerController::class, 'group_server'])->name('list');
                    Route::post('/store', [\App\Http\Controllers\Dinetkan\MonitoringServerController::class, 'group_server_store'])->name('store');
                    Route::get('/single/{id}', [\App\Http\Controllers\Dinetkan\MonitoringServerController::class, 'group_server_single'])->name('single');
                    Route::put('/update/{id}', [\App\Http\Controllers\Dinetkan\MonitoringServerController::class, 'group_server_update'])->name('update');
                    Route::post('/delete/{id}', [\App\Http\Controllers\Dinetkan\MonitoringServerController::class, 'group_server_delete'])->name('delete');
                });

                Route::group(['prefix' => 'log', 'as' => 'log.'], function () {
                    Route::get('/', [\App\Http\Controllers\Dinetkan\MonitoringServerController::class, 'log'])->name('log');
                });
            });
        });
    });

    Route::group(['middleware' => ['role:Admin'], 'as' => 'admin.', 'prefix' => 'admin'], function () {
        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard')->withoutMiddleware(StatusMiddleware::class);
        Route::get('/charts/revenue', [\App\Http\Controllers\Admin\DashboardController::class, 'revenueChart'])->name('charts.revenue');
        Route::get('/charts/new-issues', [\App\Http\Controllers\Admin\DashboardController::class, 'newIssuesChart'])->name('charts.new-issues');
        Route::get('/charts/users-growth', [\App\Http\Controllers\Admin\DashboardController::class, 'usersGrowthChart'])->name('charts.users-growth');
        Route::get('/tables/recent-users', [\App\Http\Controllers\Admin\DashboardController::class, 'recentUsersTable'])->name('tables.recent-users');
        Route::get('olt/device/pon/zte_fiber/{id}',[\App\Http\Controllers\OltDeviceController::class, 'show_pon_zte_fiber']);
        Route::get('olt/device/pon/getDataDetail/{id}',[\App\Http\Controllers\OltDeviceController::class, 'getProfileOnu']);
        Route::post('olt/device/pon/updateonu', [\App\Http\Controllers\OltDeviceController::class, 'updateonu']);
        Route::post('olt/device/pon/updatespeed', [\App\Http\Controllers\OltDeviceController::class, 'updatespeed']);
        Route::post('olt/onu/sync', [\App\Http\Controllers\OltDeviceController::class, 'sync']);
        Route::post('olt/onu/rename', [\App\Http\Controllers\OltDeviceController::class, 'rename']);
        Route::post('olt/auth', [\App\Http\Controllers\OltDeviceController::class, 'do_auth_device']);
        Route::get('olt/device/dashboard', [\App\Http\Controllers\OltDeviceController::class, 'show_olt'])->name('admin.olt.dashboard');
        Route::get('olt/device/pon/all', [\App\Http\Controllers\OltDeviceController::class, 'show_pon_all']);
        Route::get('olt/device/pon/{id}', [\App\Http\Controllers\OltDeviceController::class, 'show_pon']);
        Route::get('olt/device/pon/{port}/onu/{onu}', [\App\Http\Controllers\OltDeviceController::class, 'show_onu']);
        Route::post('olt/device/reboot/pon/{port}/onu/{onu}', [\App\Http\Controllers\OltDeviceController::class, 'reboot_onu']);
        Route::post('olt/device/reboot/{olt}', [\App\Http\Controllers\OltDeviceController::class, 'reboot_olt']);
        Route::post('olt/device/save/{olt}', [\App\Http\Controllers\OltDeviceController::class, 'save_olt']);
        Route::post('olt/device/delete/pon/{port}/onu/{onu}', [\App\Http\Controllers\OltDeviceController::class, 'delete_onu']);
        Route::get('olt/device/logout', [\App\Http\Controllers\OltDeviceController::class, 'deviceLogout']);
        Route::get('olt/device/setting_zone', [\App\Http\Controllers\OltDeviceController::class, 'setting_zone']);
        Route::get('olt/device/setting_odb', [\App\Http\Controllers\OltDeviceController::class, 'setting_odb']);
        Route::get('olt/device/setting_vlan', [\App\Http\Controllers\OltDeviceController::class, 'setting_vlan']);
        Route::post('olt/device/updatezone', [\App\Http\Controllers\OltDeviceController::class, 'updatezone']); 
        Route::delete('olt/device/delete_zone/{id}', [\App\Http\Controllers\OltDeviceController::class, 'delete_zone']);
        Route::post('olt/device/updateodb', [\App\Http\Controllers\OltDeviceController::class, 'updateodb']); 
        Route::post('olt/device/updatevlan', [\App\Http\Controllers\OltDeviceController::class, 'updatevlan']); 
        Route::delete('olt/device/delete_odb/{id}', [\App\Http\Controllers\OltDeviceController::class, 'delete_odb']);
        Route::delete('olt/device/delete_vlan/{id}', [\App\Http\Controllers\OltDeviceController::class, 'delete_vlan']);
        Route::get('olt/device/setting_port_vlan', [\App\Http\Controllers\OltDeviceController::class, 'setting_port_vlan']);
        Route::post('olt/device/updateportvlan', [\App\Http\Controllers\OltDeviceController::class, 'updateportvlan']);
        Route::get('olt/device/setting_bandwidth', [\App\Http\Controllers\OltDeviceController::class, 'setting_bandwidth']);
        Route::get('olt/device/setting_onu_type', [\App\Http\Controllers\OltDeviceController::class, 'setting_onu_type']);
        Route::post('olt/device/updatetypepon', [\App\Http\Controllers\OltDeviceController::class, 'updatetypepon']);
        Route::post('olt/device/updatebandwidth', [\App\Http\Controllers\OltDeviceController::class, 'updatebandwidth']);
        Route::delete('olt/device/delete_bandwidth/{id}', [\App\Http\Controllers\OltDeviceController::class, 'delete_bandwidth']);
        Route::get('olt/device/setting_traffic', [\App\Http\Controllers\OltDeviceController::class, 'setting_traffic']);
        Route::post('olt/device/updatetraffic', [\App\Http\Controllers\OltDeviceController::class, 'updatetraffic']);
        Route::delete('olt/device/delete_traffic/{id}', [\App\Http\Controllers\OltDeviceController::class, 'delete_traffic']);
        Route::get('olt/device/setting_vlan_profile', [\App\Http\Controllers\OltDeviceController::class, 'setting_vlan_profile']);
        Route::post('olt/device/updatevlanprofile', [\App\Http\Controllers\OltDeviceController::class, 'updatevlanprofile']);
        Route::delete('olt/device/delete_vlan_profile/{id}', [\App\Http\Controllers\OltDeviceController::class, 'delete_vlan_profile']);
        Route::delete('olt/device/delete_onu_type/{id}', [\App\Http\Controllers\OltDeviceController::class, 'delete_onu_type']);
        Route::delete('olt/device/deleteONU/{id}', [\App\Http\Controllers\OltDeviceController::class, 'deleteONU']);



        Route::resource('/olt', \App\Http\Controllers\OltDeviceController::class)->except(['show']);

        Route::prefix('services')->name('services.')->group(function () {
            Route::get('/data/tools/sample', [\App\Http\Controllers\Service\DataController::class, 'sample'])->name('sample');
            Route::get('/data/tools/export', [\App\Http\Controllers\Service\DataController::class, 'export'])->name('export');
            Route::post('/data/tools/import', [\App\Http\Controllers\Service\DataController::class, 'import'])->name('import');
            Route::get('/data/stats', [\App\Http\Controllers\Service\DataController::class, 'getServiceStats'])->name('stats');
            Route::get('/data/{id}/kode-odp', [\App\Http\Controllers\Service\DataController::class, 'getKodeOdp']);
            Route::get('/data/{id}/price', [\App\Http\Controllers\Service\DataController::class, 'getPrice']);
            Route::get('/data/{id}/session', [\App\Http\Controllers\Service\DataController::class, 'getSession']);
            Route::get('/data/{id}/ppp', [\App\Http\Controllers\Service\DataController::class, 'getPpp']);
            Route::get('/data/{id}/invoices', [\App\Http\Controllers\Service\DataController::class, 'invoices']);
            Route::delete('/data', [\App\Http\Controllers\Service\DataController::class, 'destroy']);
            Route::resource('/data', \App\Http\Controllers\Service\DataController::class);

            Route::get('/data/{user}/payment', [\App\Http\Controllers\Service\DataController::class, 'getPayment']);
            Route::post('/data/{user}/payment', [\App\Http\Controllers\Service\DataController::class, 'storePayment']);
            Route::put('/data/{user}/payment', [\App\Http\Controllers\Service\DataController::class, 'updatePayment']);

            Route::put('/data/enable/{id}', [\App\Http\Controllers\Service\DataController::class, 'enable']);
            Route::put('/data/disable/{id}', [\App\Http\Controllers\Service\DataController::class, 'disable']);
            Route::put('/data/suspend/{id}', [\App\Http\Controllers\Service\DataController::class, 'suspend']);

            Route::get('/member/stats', [\App\Http\Controllers\Service\DataController::class, 'getMemberStats'])->name('member.stats');
            Route::get('/member/tools/sample', [\App\Http\Controllers\Service\MemberController::class, 'sample'])->name('member.sample');
            Route::get('/member/tools/export', [\App\Http\Controllers\Service\MemberController::class, 'export'])->name('member.export');
            Route::post('/member/tools/import', [\App\Http\Controllers\Service\MemberController::class, 'import'])->name('member.import');

            Route::get('/member/{id}/services', [\App\Http\Controllers\Service\MemberController::class, 'services'])->name('member.services');
            Route::get('/member/{id}/service-list', [\App\Http\Controllers\Service\MemberController::class, 'serviceList'])->name('member.services-list');
            Route::get('/member/{id}/invoices', [\App\Http\Controllers\Service\MemberController::class, 'invoices'])->name('member.invoices');
            Route::get('/member/list', [\App\Http\Controllers\Service\MemberController::class, 'listOptions']);
            Route::delete('/member', [\App\Http\Controllers\Service\MemberController::class, 'destroy']);
            Route::resource('/member', \App\Http\Controllers\Service\MemberController::class);

            Route::get('/online', [\App\Http\Controllers\Service\OnlineController::class, 'index'])->name('online.index');
            Route::get('/offline', [\App\Http\Controllers\Service\OfflineController::class, 'index'])->name('offline.index');

            Route::resource('/profile', \App\Http\Controllers\Admin\Service\ProfileController::class);

            Route::resource('/settings', \App\Http\Controllers\Admin\Service\SettingController::class);
        });

        Route::group(['prefix' => 'hotspot', 'as' => 'hotspot.'], function () {
            Route::get('/user/getProfile', [\App\Http\Controllers\Hotspot\UserController::class, 'getProfile']);
            Route::post('/user/datatable', [\App\Http\Controllers\Hotspot\UserController::class, 'datatable']);
            Route::post('/user/generate', [\App\Http\Controllers\Hotspot\UserController::class, 'generate']);
            Route::post('/user/print', [\App\Http\Controllers\Hotspot\UserController::class, 'print']);
            Route::post('/user/enable/', [\App\Http\Controllers\Hotspot\UserController::class, 'enable']);
            Route::post('/user/disable/', [\App\Http\Controllers\Hotspot\UserController::class, 'disable']);
            Route::post('/user/reactivate/', [\App\Http\Controllers\Hotspot\UserController::class, 'reactivate']);
            Route::resource('/user', \App\Http\Controllers\Hotspot\UserController::class);

            Route::resource('/voucher', \App\Http\Controllers\Hotspot\VoucherController::class);

            Route::get('/online', [\App\Http\Controllers\Hotspot\OnlineController::class, 'index'])->name('online.index');

            Route::resource('/profile', \App\Http\Controllers\Hotspot\ProfileController::class);

            Route::put('/reseller/disable/{id}', [\App\Http\Controllers\Hotspot\ResellerController::class, 'disable']);
            Route::put('/reseller/enable/{id}', [\App\Http\Controllers\Hotspot\ResellerController::class, 'enable']);
            Route::put('/reseller/deposit', [\App\Http\Controllers\Hotspot\ResellerController::class, 'deposit']);
            Route::resource('/reseller', \App\Http\Controllers\Hotspot\ResellerController::class);
        });

        Route::group(['prefix' => 'billing', 'as' => 'billing.'], function () {
            Route::get('/member/getPpp/{id}', [\App\Http\Controllers\Billing\MemberController::class, 'getPpp']);
            Route::get('/member/getPayment/{id}', [\App\Http\Controllers\Billing\MemberController::class, 'getPayment']);
            Route::get('/member/getContact/{id}', [\App\Http\Controllers\Billing\MemberController::class, 'getContact']);
            Route::get('/member/getInvoice/{id}', [\App\Http\Controllers\Billing\MemberController::class, 'getInvoice']);
            Route::get('/member/getListInvoice/{id}', [\App\Http\Controllers\Billing\MemberController::class, 'getListInvoice']);
            Route::put('/member/updateInvoice/{invoice}', [\App\Http\Controllers\Billing\MemberController::class, 'updateInvoice']);
            Route::put('/member/updatePayment/{member}', [\App\Http\Controllers\Billing\MemberController::class, 'updatePayment']);
            Route::put('/member/updateContact/{member}', [\App\Http\Controllers\Billing\MemberController::class, 'updateContact']);
            Route::resource('/member', \App\Http\Controllers\Billing\MemberController::class);

            Route::resource('/unpaid', \App\Http\Controllers\Billing\UnpaidController::class);
            Route::resource('/paid', \App\Http\Controllers\Billing\PaidController::class);
            Route::resource('/setting', \App\Http\Controllers\Admin\Billing\SettingController::class);

            Route::get('/unpaid/{memberId}/services', [\App\Http\Controllers\Billing\UnpaidController::class, 'getServicesByMember'])->name('getServicesByMember');
            Route::get('/unpaid/{serviceId}/detail', [\App\Http\Controllers\Billing\UnpaidController::class, 'getServiceDetails'])->name('getServiceDetails');
            Route::post('/unpaid/generate', [\App\Http\Controllers\Billing\UnpaidController::class, 'generateInvoice'])->name('generateInvoice');
            Route::post('/unpaid/generateWA/{invoice_id}', [\App\Http\Controllers\Billing\UnpaidController::class, 'generateInvoiceWA']);
            Route::get('/unpaid/getInvoice/{id}', [\App\Http\Controllers\Billing\MemberController::class, 'getInvoice']);
            Route::put('/unpaid/updateInvoice/{invoice}', [\App\Http\Controllers\Billing\MemberController::class, 'updateInvoice']);
            Route::put('/unpaid/payInvoice/{invoice}', [\App\Http\Controllers\Billing\UnpaidController::class, 'payInvoice']);
            Route::post('/unpaid/payInvoiceWA/{invoice}', [\App\Http\Controllers\Billing\UnpaidController::class, 'payInvoiceWA']);

            Route::put('/paid/undopayInvoice/{invoice}', [\App\Http\Controllers\Billing\PaidController::class, 'undopayInvoice']);
            Route::post('/paid/undopayInvoiceWA/{invoice}', [\App\Http\Controllers\Billing\PaidController::class, 'undopayInvoiceWA']);
            Route::resource('/paid', \App\Http\Controllers\Billing\PaidController::class);
        });

        Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
            Route::resource('revenue', \App\Http\Controllers\Report\RevenueController::class)->except(['show', 'update']);
            Route::resource('expense', \App\Http\Controllers\Report\ExpenseController::class)->except(['show', 'update']);
            Route::resource('transaction', \App\Http\Controllers\Report\TransactionController::class);
        });

        Route::group(['prefix' => 'ticket', 'as' => 'ticket.'], function () {
            Route::get('/new/member/{member}', [\App\Http\Controllers\Ticket\NewClientController::class, 'getMemberDetails']);
            Route::post('/new/secret', [\App\Http\Controllers\Ticket\NewClientController::class, 'storeSecret']);
            Route::put('/new/confirm/{psb}', [\App\Http\Controllers\Ticket\NewClientController::class, 'confirmPsb']);
            Route::resource('/new', \App\Http\Controllers\Ticket\NewClientController::class);

            Route::get('/outage/getArea/{id}', [\App\Http\Controllers\Ticket\OutageController::class, 'getArea']);
            Route::get('/outage/getSession/{id}', [\App\Http\Controllers\Ticket\OutageController::class, 'getSession']);
            Route::put('/outage/confirm/{ggn}', [\App\Http\Controllers\Ticket\OutageController::class, 'confirmGgn']);
            Route::resource('/outage', \App\Http\Controllers\Ticket\OutageController::class);
        });

        Route::group(['prefix' => 'integration', 'as' => 'integration.'], function () {
            Route::resource('whatsapp', \App\Http\Controllers\Integration\WhatsappController::class);

            Route::post('whatsapp/delete', [\App\Http\Controllers\Integration\WhatsappController::class, 'delete']);
            Route::get('whatsapp/device/scan', [\App\Http\Controllers\Integration\WhatsappController::class, 'scan']);
            Route::post('whatsapp/device/logout', [\App\Http\Controllers\Integration\WhatsappController::class, 'logout']);
            Route::post('whatsapp/message/resend', [\App\Http\Controllers\Integration\WhatsappController::class, 'resend']);
            Route::post('whatsapp/single/resend', [\App\Http\Controllers\Integration\WhatsappController::class, 'resendMessage']);
            Route::get('whatsapp/template/{id}', [\App\Http\Controllers\Integration\WhatsappController::class, 'getTemplate']);
            Route::get('whatsapp/user/getUser', [\App\Http\Controllers\Integration\WhatsappController::class, 'getUser']);
            Route::get('whatsapp/user/getUserAll', [\App\Http\Controllers\Integration\WhatsappController::class, 'getUserAll']);
            Route::post('whatsapp/send/broadcast', [\App\Http\Controllers\Integration\WhatsappController::class, 'sendBroadcast']);

            Route::put('whatsapp/template/active/{id}', [\App\Http\Controllers\Integration\WhatsappController::class, 'updateAccountActive']);
            Route::put('whatsapp/template/terbit/{id}', [\App\Http\Controllers\Integration\WhatsappController::class, 'updateInvoiceTerbit']);
            Route::put('whatsapp/template/reminder/{id}', [\App\Http\Controllers\Integration\WhatsappController::class, 'updateInvoiceReminder']);
            Route::put('whatsapp/template/overdue/{id}', [\App\Http\Controllers\Integration\WhatsappController::class, 'updateInvoiceOverdue']);
            Route::put('whatsapp/template/paid/{id}', [\App\Http\Controllers\Integration\WhatsappController::class, 'updatePaymentPaid']);
            Route::put('whatsapp/template/cancel/{id}', [\App\Http\Controllers\Integration\WhatsappController::class, 'updatePaymentCancel']);

            Route::resource('telegram', \App\Http\Controllers\Admin\Integration\TelegramController::class);

            Route::resource('midtrans', \App\Http\Controllers\Admin\Integration\MidtransController::class);
            Route::resource('tripay', \App\Http\Controllers\Admin\Integration\TripayController::class);
            Route::resource('duitku', \App\Http\Controllers\Admin\Integration\DuitkuController::class);
            Route::resource('xendit', \App\Http\Controllers\Admin\Integration\XenditController::class);
        });

        Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
            Route::group(['prefix' => 'mikrotik', 'as' => 'mikrotik.'], function () {
                Route::resource('vpn', \App\Http\Controllers\Admin\Settings\VpnMikrotikController::class);
                Route::resource('vpn-remote', \App\Http\Controllers\Admin\Settings\VpnRemoteMikrotikController::class);
                Route::resource('nas', \App\Http\Controllers\Admin\Settings\NasMikrotikController::class);
            });

            Route::group(['prefix' => 'master', 'as' => 'master.'], function () {
                Route::resource('/about', \App\Http\Controllers\Admin\Settings\AboutMasterController::class)->parameters(['about' => 'company']);
                Route::put('/about/update/{company}', [\App\Http\Controllers\Admin\Settings\AboutMasterController::class, 'updateCompany']);

                Route::get('/area/list', [\App\Http\Controllers\Settings\AreaMasterController::class, 'listOptions']);
                Route::resource('/area', \App\Http\Controllers\Settings\AreaMasterController::class);

                Route::get('/odp/list', [\App\Http\Controllers\Settings\OdpMasterController::class, 'listOptions']);
                Route::resource('/odp', \App\Http\Controllers\Settings\OdpMasterController::class);

            });

            Route::put('users/disable/{id}', [\App\Http\Controllers\Admin\Settings\UserController::class, 'disable']);
            Route::put('users/enable/{id}', [\App\Http\Controllers\Admin\Settings\UserController::class, 'enable']);
            Route::resource('users', \App\Http\Controllers\Admin\Settings\UserController::class);
            Route::resource('activity', \App\Http\Controllers\Admin\Settings\ActivityLogController::class);
        });
    });

    Route::group(['middleware' => ['role:Teknisi'], 'as' => 'teknisi.', 'prefix' => 'teknisi'], function () {
        Route::get('/', [\App\Http\Controllers\Teknisi\DashboardController::class, 'index'])->name('dashboard')->withoutMiddleware(StatusMiddleware::class);
        Route::get('/charts/new-issues', [\App\Http\Controllers\Teknisi\DashboardController::class, 'newIssuesChart'])->name('charts.new-issues');

        Route::post('olt/onu/sync', [\App\Http\Controllers\OltDeviceController::class, 'sync']);
        Route::post('olt/onu/rename', [\App\Http\Controllers\OltDeviceController::class, 'rename']);
        Route::post('olt/auth', [\App\Http\Controllers\OltDeviceController::class, 'do_auth_device']);
        Route::get('olt/device/dashboard', [\App\Http\Controllers\OltDeviceController::class, 'show_olt']);
        Route::get('olt/device/pon/all', [\App\Http\Controllers\OltDeviceController::class, 'show_pon_all']);
        Route::get('olt/device/pon/{id}', [\App\Http\Controllers\OltDeviceController::class, 'show_pon']);
        Route::get('olt/device/pon/{port}/onu/{onu}', [\App\Http\Controllers\OltDeviceController::class, 'show_onu']);
        Route::post('olt/device/reboot/pon/{port}/onu/{onu}', [\App\Http\Controllers\OltDeviceController::class, 'reboot_onu']);
        Route::post('olt/device/reboot/{olt}', [\App\Http\Controllers\OltDeviceController::class, 'reboot_olt']);
        Route::post('olt/device/save/{olt}', [\App\Http\Controllers\OltDeviceController::class, 'save_olt']);
        Route::post('olt/device/delete/pon/{port}/onu/{onu}', [\App\Http\Controllers\OltDeviceController::class, 'delete_onu']);
        Route::get('olt/device/logout', [\App\Http\Controllers\OltDeviceController::class, 'deviceLogout']);
        Route::resource('/olt', \App\Http\Controllers\OltDeviceController::class)->except(['show']);

        Route::group(['prefix' => 'hotspot', 'as' => 'hotspot.'], function () {
            Route::resource('/user', \App\Http\Controllers\Hotspot\UserController::class);
            Route::get('/user/getProfile', [\App\Http\Controllers\Hotspot\UserController::class, 'getProfile']);
            Route::post('/user/datatable', [\App\Http\Controllers\Hotspot\UserController::class, 'datatable']);

            Route::get('/online', [\App\Http\Controllers\Hotspot\OnlineController::class, 'index'])->name('online.index');
        });

        Route::group(['prefix' => 'services', 'as' => 'services.'], function () {
            Route::get('/data/{id}/kode-odp', [\App\Http\Controllers\Service\DataController::class, 'getKodeOdp']);
            Route::get('/data/{id}/price', [\App\Http\Controllers\Service\DataController::class, 'getPrice']);
            Route::get('/data/{id}/ppp', [\App\Http\Controllers\Service\DataController::class, 'getPpp']);
            Route::get('/data/{id}/session', [\App\Http\Controllers\Service\DataController::class, 'getSession']);
            Route::get('/member/{id}/service-list', [\App\Http\Controllers\Service\MemberController::class, 'serviceList'])->name('member.services-list');

            Route::resource('/data', \App\Http\Controllers\Service\DataController::class);

            Route::get('/online', [\App\Http\Controllers\Service\OnlineController::class, 'index'])->name('online.index');
        });

        Route::group(['prefix' => 'billing', 'as' => 'billing.'], function () {
            Route::resource('/member', \App\Http\Controllers\Billing\MemberController::class);

            Route::get('/member/getPpp/{id}', [\App\Http\Controllers\Billing\MemberController::class, 'getPpp']);
            Route::get('/member/getPayment/{id}', [\App\Http\Controllers\Billing\MemberController::class, 'getPayment']);
            Route::get('/member/getContact/{id}', [\App\Http\Controllers\Billing\MemberController::class, 'getContact']);
            Route::get('/member/getInvoice/{id}', [\App\Http\Controllers\Billing\MemberController::class, 'getInvoice']);
            Route::get('/member/getListInvoice/{id}', [\App\Http\Controllers\Billing\MemberController::class, 'getListInvoice']);

            Route::get('/unpaid/getMember/{id}', [\App\Http\Controllers\Billing\UnpaidController::class, 'getMember']);
            Route::get('/unpaid/getProfile/{id}', [\App\Http\Controllers\Billing\UnpaidController::class, 'getProfile']);
        });

        Route::group(['prefix' => 'ticket', 'as' => 'ticket.'], function () {
            Route::put('/new/confirm/{psb}', [\App\Http\Controllers\Ticket\NewClientController::class, 'confirmPsb']);
            Route::resource('/new', \App\Http\Controllers\Ticket\NewClientController::class);

            Route::get('/outage/getArea/{id}', [\App\Http\Controllers\Ticket\OutageController::class, 'getArea']);
            Route::get('/outage/getSession/{id}', [\App\Http\Controllers\Ticket\OutageController::class, 'getSession']);
            Route::put('/outage/confirm/{ggn}', [\App\Http\Controllers\Ticket\OutageController::class, 'confirmGgn']);
            Route::resource('/outage', \App\Http\Controllers\Ticket\OutageController::class);
        });

        Route::group(['prefix' => 'account', 'as' => 'account.'], function () {
            Route::get('/profile', [\App\Http\Controllers\Account\ProfileController::class, 'index'])->name('profile.index');
            Route::patch('/profile', [\App\Http\Controllers\Account\ProfileController::class, 'update'])->name('profile.update');
            Route::patch('/profile/password', [\App\Http\Controllers\Account\ChangePasswordController::class, 'update'])->name('profile.password');
        });
    });

    Route::group(['middleware' => ['role:Kasir'], 'as' => 'kasir.', 'prefix' => 'kasir'], function () {
        Route::get('/', [\App\Http\Controllers\Kasir\DashboardController::class, 'index'])->name('dashboard')->withoutMiddleware(StatusMiddleware::class);
        Route::get('/charts/revenue', [\App\Http\Controllers\Admin\DashboardController::class, 'revenueChart'])->name('charts.revenue');

        Route::group(['prefix' => 'billing', 'as' => 'billing.'], function () {
            Route::resource('/member', \App\Http\Controllers\Billing\MemberController::class);
            Route::resource('/unpaid', \App\Http\Controllers\Billing\UnpaidController::class);
            Route::resource('/paid', \App\Http\Controllers\Billing\PaidController::class);

            Route::get('/member/getPpp/{id}', [\App\Http\Controllers\Billing\MemberController::class, 'getPpp']);
            Route::get('/member/getPayment/{id}', [\App\Http\Controllers\Billing\MemberController::class, 'getPayment']);
            Route::get('/member/getContact/{id}', [\App\Http\Controllers\Billing\MemberController::class, 'getContact']);
            Route::get('/member/getInvoice/{id}', [\App\Http\Controllers\Billing\MemberController::class, 'getInvoice']);
            Route::get('/member/getListInvoice/{id}', [\App\Http\Controllers\Billing\MemberController::class, 'getListInvoice']);
            Route::put('/member/updateInvoice/{invoice}', [\App\Http\Controllers\Billing\MemberController::class, 'updateInvoice']);
            Route::put('/member/updatePayment/{member}', [\App\Http\Controllers\Billing\MemberController::class, 'updatePayment']);
            Route::put('/member/updateContact/{member}', [\App\Http\Controllers\Billing\MemberController::class, 'updateContact']);

            Route::get('/unpaid/{memberId}/services', [\App\Http\Controllers\Billing\UnpaidController::class, 'getServicesByMember'])->name('getServicesByMember');
            Route::get('/unpaid/{serviceId}/detail', [\App\Http\Controllers\Billing\UnpaidController::class, 'getServiceDetails'])->name('getServiceDetails');
            Route::post('/unpaid/generate', [\App\Http\Controllers\Billing\UnpaidController::class, 'generateInvoice'])->name('generateInvoice');
            Route::get('/unpaid/getInvoice/{id}', [\App\Http\Controllers\Billing\MemberController::class, 'getInvoice']);
            Route::put('/unpaid/updateInvoice/{invoice}', [\App\Http\Controllers\Billing\MemberController::class, 'updateInvoice']);
            Route::put('/unpaid/payInvoice/{invoice}', [\App\Http\Controllers\Billing\UnpaidController::class, 'payInvoice']);
            Route::post('/unpaid/payInvoiceWA/{invoice}', [\App\Http\Controllers\Billing\UnpaidController::class, 'payInvoiceWA']);

            Route::put('/paid/undopayInvoice/{invoice}', [\App\Http\Controllers\Billing\PaidController::class, 'undopayInvoice']);
            Route::post('/paid/undopayInvoiceWA/{invoice}', [\App\Http\Controllers\Billing\PaidController::class, 'undopayInvoiceWA']);
            Route::resource('/paid', \App\Http\Controllers\Billing\PaidController::class);
        });

        Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
            Route::resource('revenue', \App\Http\Controllers\Report\RevenueController::class)->except(['show', 'update']);
            Route::resource('expense', \App\Http\Controllers\Report\ExpenseController::class)->except(['show', 'update']);
            Route::resource('transaction', \App\Http\Controllers\Report\TransactionController::class);
        });

        Route::group(['prefix' => 'ticket', 'as' => 'ticket.'], function () {
            Route::get('/outage/getArea/{id}', [\App\Http\Controllers\Ticket\OutageController::class, 'getArea']);
            Route::get('/outage/getSession/{id}', [\App\Http\Controllers\Ticket\OutageController::class, 'getSession']);
        });

        Route::group(['prefix' => 'whatsapp', 'as' => 'whatsapp.'], function () {
            Route::put('whatsapp/template/active/{id}', [\App\Http\Controllers\Integration\WhatsappController::class, 'updateAccountActive']);
            Route::put('whatsapp/template/terbit/{id}', [\App\Http\Controllers\Integration\WhatsappController::class, 'updateInvoiceTerbit']);
            Route::put('whatsapp/template/reminder/{id}', [\App\Http\Controllers\Integration\WhatsappController::class, 'updateInvoiceReminder']);
            Route::put('whatsapp/template/overdue/{id}', [\App\Http\Controllers\Integration\WhatsappController::class, 'updateInvoiceOverdue']);
            Route::put('whatsapp/template/paid/{id}', [\App\Http\Controllers\Integration\WhatsappController::class, 'updatePaymentPaid']);
            Route::put('whatsapp/template/cancel/{id}', [\App\Http\Controllers\Integration\WhatsappController::class, 'updatePaymentCancel']);
        });

        Route::group(['prefix' => 'account', 'as' => 'account.'], function () {
            Route::get('/profile', [\App\Http\Controllers\Account\ProfileController::class, 'index'])->name('profile.index');
            Route::patch('/profile', [\App\Http\Controllers\Account\ProfileController::class, 'update'])->name('profile.update');
            Route::patch('/profile/password', [\App\Http\Controllers\Account\ChangePasswordController::class, 'update'])->name('profile.password');
        });
    });

    Route::group(['middleware' => ['role:Helpdesk'], 'as' => 'helpdesk.', 'prefix' => 'helpdesk'], function () {
        Route::get('/', [\App\Http\Controllers\Helpdesk\DashboardController::class, 'index'])->name('dashboard')->withoutMiddleware(StatusMiddleware::class);
        Route::get('/charts/new-issues', [\App\Http\Controllers\Teknisi\DashboardController::class, 'newIssuesChart'])->name('charts.new-issues');
        Route::post('olt/onu/sync', [\App\Http\Controllers\OltDeviceController::class, 'sync']);
        Route::post('olt/onu/rename', [\App\Http\Controllers\OltDeviceController::class, 'rename']);
        Route::post('olt/auth', [\App\Http\Controllers\OltDeviceController::class, 'do_auth_device']);
        Route::get('olt/device/dashboard', [\App\Http\Controllers\OltDeviceController::class, 'show_olt']);
        Route::get('olt/device/pon/all', [\App\Http\Controllers\OltDeviceController::class, 'show_pon_all']);
        Route::get('olt/device/pon/{id}', [\App\Http\Controllers\OltDeviceController::class, 'show_pon']);
        Route::get('olt/device/pon/{port}/onu/{onu}', [\App\Http\Controllers\OltDeviceController::class, 'show_onu']);
        Route::post('olt/device/reboot/pon/{port}/onu/{onu}', [\App\Http\Controllers\OltDeviceController::class, 'reboot_onu']);
        Route::post('olt/device/reboot/{olt}', [\App\Http\Controllers\OltDeviceController::class, 'reboot_olt']);
        Route::post('olt/device/save/{olt}', [\App\Http\Controllers\OltDeviceController::class, 'save_olt']);
        Route::post('olt/device/delete/pon/{port}/onu/{onu}', [\App\Http\Controllers\OltDeviceController::class, 'delete_onu']);
        Route::get('olt/device/logout', [\App\Http\Controllers\OltDeviceController::class, 'deviceLogout']);
        Route::resource('/olt', \App\Http\Controllers\OltDeviceController::class)->except(['show']);

        Route::group(['prefix' => 'hotspot', 'as' => 'hotspot.'], function () {
            Route::resource('/user', \App\Http\Controllers\Hotspot\UserController::class);
            Route::get('/user/getProfile', [\App\Http\Controllers\Hotspot\UserController::class, 'getProfile']);
            Route::post('/user/datatable', [\App\Http\Controllers\Hotspot\UserController::class, 'datatable']);

            Route::get('/online', [\App\Http\Controllers\Hotspot\OnlineController::class, 'index'])->name('online.index');
        });

        Route::group(['prefix' => 'services', 'as' => 'services.'], function () {
            Route::get('/data/{id}/kode-odp', [\App\Http\Controllers\Service\DataController::class, 'getKodeOdp']);
            Route::get('/data/{id}/price', [\App\Http\Controllers\Service\DataController::class, 'getPrice']);
            Route::get('/data/{id}/ppp', [\App\Http\Controllers\Service\DataController::class, 'getPpp']);
            Route::get('/data/{id}/session', [\App\Http\Controllers\Service\DataController::class, 'getSession']);
            Route::get('/member/{id}/service-list', [\App\Http\Controllers\Service\MemberController::class, 'serviceList'])->name('member.services-list');

            Route::put('/data/enable/{id}', [\App\Http\Controllers\Service\DataController::class, 'enable']);
            Route::put('/data/disable/{id}', [\App\Http\Controllers\Service\DataController::class, 'disable']);
            Route::put('/data/suspend/{id}', [\App\Http\Controllers\Service\DataController::class, 'suspend']);
            Route::resource('/data', \App\Http\Controllers\Service\DataController::class);

            Route::resource('/member', \App\Http\Controllers\Service\MemberController::class);

            Route::get('/online', [\App\Http\Controllers\Service\OnlineController::class, 'index'])->name('online.index');
        });

        Route::group(['prefix' => 'billing', 'as' => 'billing.'], function () {
            Route::resource('/member', \App\Http\Controllers\Billing\MemberController::class);

            Route::get('/member/getPpp/{id}', [\App\Http\Controllers\Billing\MemberController::class, 'getPpp']);
            Route::get('/member/getPayment/{id}', [\App\Http\Controllers\Billing\MemberController::class, 'getPayment']);
            Route::get('/member/getContact/{id}', [\App\Http\Controllers\Billing\MemberController::class, 'getContact']);
            Route::get('/member/getInvoice/{id}', [\App\Http\Controllers\Billing\MemberController::class, 'getInvoice']);
            Route::get('/member/getListInvoice/{id}', [\App\Http\Controllers\Billing\MemberController::class, 'getListInvoice']);
            Route::get('/unpaid/getMember/{id}', [\App\Http\Controllers\Billing\UnpaidController::class, 'getMember']);
            Route::get('/unpaid/getProfile/{id}', [\App\Http\Controllers\Billing\UnpaidController::class, 'getProfile']);
        });

        Route::group(['prefix' => 'ticket', 'as' => 'ticket.'], function () {
            Route::get('/new/{user}/service', [\App\Http\Controllers\Ticket\NewClientController::class, 'getService']);
            Route::post('/new/secret', [\App\Http\Controllers\Ticket\NewClientController::class, 'storeSecret']);
            Route::put('/new/confirm/{psb}', [\App\Http\Controllers\Ticket\NewClientController::class, 'confirmPsb']);
            Route::resource('/new', \App\Http\Controllers\Ticket\NewClientController::class);

            Route::get('/outage/getArea/{id}', [\App\Http\Controllers\Ticket\OutageController::class, 'getArea']);
            Route::get('/outage/getSession/{id}', [\App\Http\Controllers\Ticket\OutageController::class, 'getSession']);
            Route::put('/outage/confirm/{ggn}', [\App\Http\Controllers\Ticket\OutageController::class, 'confirmGgn']);
            Route::resource('/outage', \App\Http\Controllers\Ticket\OutageController::class);
        });

        Route::group(['prefix' => 'whatsapp', 'as' => 'whatsapp.'], function () {
            Route::post('whatsapp/delete', [\App\Http\Controllers\Integration\WhatsappController::class, 'delete']);
            Route::get('whatsapp/device/scan', [\App\Http\Controllers\Integration\WhatsappController::class, 'scan']);
            Route::post('whatsapp/device/logout', [\App\Http\Controllers\Integration\WhatsappController::class, 'logout']);
            Route::post('whatsapp/message/resend', [\App\Http\Controllers\Integration\WhatsappController::class, 'resend']);
            Route::post('whatsapp/single/resend', [\App\Http\Controllers\Integration\WhatsappController::class, 'resendMessage']);
            Route::get('whatsapp/template/{id}', [\App\Http\Controllers\Integration\WhatsappController::class, 'getTemplate']);
            Route::get('whatsapp/user/getUser', [\App\Http\Controllers\Integration\WhatsappController::class, 'getUser']);
            Route::get('whatsapp/user/getUserAll', [\App\Http\Controllers\Integration\WhatsappController::class, 'getUserAll']);
            Route::post('whatsapp/send/broadcast', [\App\Http\Controllers\Integration\WhatsappController::class, 'sendBroadcast']);
        });

        Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
            Route::group(['prefix' => 'master', 'as' => 'master.'], function () {
                Route::resource('area', \App\Http\Controllers\Settings\AreaMasterController::class);
                Route::resource('odp', \App\Http\Controllers\Settings\OdpMasterController::class);
            });
        });

        Route::group(['prefix' => 'account', 'as' => 'account.'], function () {
            Route::get('/profile', [\App\Http\Controllers\Account\ProfileController::class, 'index'])->name('profile.index');
            Route::patch('/profile', [\App\Http\Controllers\Account\ProfileController::class, 'update'])->name('profile.update');
            Route::patch('/profile/password', [\App\Http\Controllers\Account\ChangePasswordController::class, 'update'])->name('profile.password');
        });
    });

    Route::group(['middleware' => ['role:Reseller'], 'as' => 'reseller.', 'prefix' => 'reseller'], function () {
        Route::get('/', [\App\Http\Controllers\Reseller\DashboardController::class, 'index'])->name('dashboard')->withoutMiddleware(StatusMiddleware::class);

        Route::group(['prefix' => 'hotspot', 'as' => 'hotspot.'], function () {
            Route::get('/user', [\App\Http\Controllers\Reseller\Hotspot\UserController::class, 'index'])->name('user.index');
            Route::resource('/user', \App\Http\Controllers\Hotspot\UserController::class)->except('index');
            Route::post('/user/datatable', [\App\Http\Controllers\Reseller\Hotspot\UserController::class, 'datatable']);
            Route::post('/user/generate', [\App\Http\Controllers\Reseller\Hotspot\UserController::class, 'generate']);
            Route::get('/user/getProfile', [\App\Http\Controllers\Hotspot\UserController::class, 'getProfile']);
            Route::post('/user/print', [\App\Http\Controllers\Hotspot\UserController::class, 'print']);
            Route::post('/user/enable/', [\App\Http\Controllers\Hotspot\UserController::class, 'enable']);
            Route::post('/user/disable/', [\App\Http\Controllers\Hotspot\UserController::class, 'disable']);
            Route::post('/user/reactivate/', [\App\Http\Controllers\Hotspot\UserController::class, 'reactivate']);
        });

        Route::group(['prefix' => 'account', 'as' => 'account.'], function () {
            Route::get('/profile', [\App\Http\Controllers\Account\ProfileController::class, 'index'])->name('profile.index');
            Route::patch('/profile', [\App\Http\Controllers\Account\ProfileController::class, 'update'])->name('profile.update');
            Route::patch('/profile/password', [\App\Http\Controllers\Account\ChangePasswordController::class, 'update'])->name('profile.password');
        });
    });

    Route::group(['middleware' => ['role:Pelanggan'], 'as' => 'pelanggan.', 'prefix' => 'pelanggan'], function () {
        Route::get('/', [\App\Http\Controllers\Pelanggan\DashboardController::class, 'index'])->name('dashboard')->withoutMiddleware(StatusMiddleware::class);
    });
});

Route::group(['middleware' => ['role:Admin'], 'as' => 'admin.', 'prefix' => 'admin'], function () {
    Route::group(['prefix' => 'account', 'as' => 'account.'], function () {
        Route::resource('info', \App\Http\Controllers\Admin\Account\AccountInfoController::class);
        Route::resource('info_dinetkan', \App\Http\Controllers\Admin\Account\AccountInfoDinetkanController::class);
        Route::get('get_info_dinetkan', [\App\Http\Controllers\Admin\Account\AccountInfoDinetkanController::class, 'get_info_dinetkan'])->name('info.get_info_dinetkan');
        Route::POST('update_info_dinetkan', [\App\Http\Controllers\Admin\Account\AccountInfoDinetkanController::class, 'update_info_dinetkan'])->name('info.update_info_dinetkan');
        Route::post('update_doc_info_dinetkan', [\App\Http\Controllers\Admin\Account\AccountInfoDinetkanController::class, 'update_doc_info_dinetkan'])->name('info.update_doc_info_dinetkan');
        Route::get('/show_file/{id}', [\App\Http\Controllers\Admin\Account\AccountInfoDinetkanController::class, 'show_file'])->name('info.show_file');
        Route::resource('mrtg', \App\Http\Controllers\Admin\Account\MrtgDinetkanController::class);
        Route::get('/get_graph_json', [\App\Http\Controllers\Admin\Account\MrtgDinetkanController::class, 'get_graph_json'])->name('mrtg.get_graph_json');
        Route::get('/week_get_graph_json', [\App\Http\Controllers\Admin\Account\MrtgDinetkanController::class, 'week_get_graph_json'])->name('mrtg.week_get_graph_json');
        Route::get('/month_get_graph_json', [\App\Http\Controllers\Admin\Account\MrtgDinetkanController::class, 'month_get_graph_json'])->name('mrtg.month_get_graph_json');
        Route::get('/year_get_graph_json', [\App\Http\Controllers\Admin\Account\MrtgDinetkanController::class, 'year_get_graph_json'])->name('mrtg.year_get_graph_json');

        Route::get('/profile', [\App\Http\Controllers\Account\ProfileController::class, 'index'])->name('profile.index');
        Route::patch('/profile', [\App\Http\Controllers\Account\ProfileController::class, 'update'])->name('profile.update');
        Route::patch('/profile/password', [\App\Http\Controllers\Account\ChangePasswordController::class, 'update'])->name('profile.password');

        Route::group(['prefix' => 'licensing', 'as' => 'licensing.'], function () {
            Route::get('/', [\App\Http\Controllers\Admin\Account\LicenseController::class, 'index'])->name('index');
            Route::get('/{id}', [\App\Http\Controllers\Admin\Account\LicenseController::class, 'order'])->name('order');
            Route::get('/{id}/{coupon}', [\App\Http\Controllers\Admin\Account\LicenseController::class, 'order'])->name('order.coupon');
            Route::post('/{id}/pay', [\App\Http\Controllers\Admin\Account\LicenseController::class, 'pay'])->name('pay');
            Route::get('/{id}/thank-you', [\App\Http\Controllers\Admin\Account\LicenseController::class, 'thankYou'])->name('thank-you');
        });
    });
    Route::group(['prefix' => 'billing', 'as' => 'billing.'], function () {
        // dinetkan
//        Route::resource('/member_dinetkan', \App\Http\Controllers\Admin\Billing\MemberDinetkanController::class);
        Route::get('/member_dinetkan/index', [\App\Http\Controllers\Admin\Billing\MemberDinetkanController::class, 'index'])->name('member_dinetkan.index');
        Route::post('/member_dinetkan/store', [\App\Http\Controllers\Admin\Billing\MemberDinetkanController::class, 'store'])->name('member_dinetkan.store');
        Route::get('/member_dinetkan/single/{id}', [\App\Http\Controllers\Admin\Billing\MemberDinetkanController::class, 'single'])->name('member_dinetkan.single');
        Route::put('/member_dinetkan/update/{id}', [\App\Http\Controllers\Admin\Billing\MemberDinetkanController::class, 'update'])->name('member_dinetkan.update');
        Route::put('/member_dinetkan/update_product/{id}', [\App\Http\Controllers\Admin\Billing\MemberDinetkanController::class, 'update_product'])->name('member_dinetkan.update_product');
        Route::get('/member_dinetkan/mapping_service', [\App\Http\Controllers\Admin\Billing\MemberDinetkanController::class, 'mapping_service'])->name('member_dinetkan.mapping_service');
        // dinetkan
    });
    Route::group(['prefix' => 'product_dinetkan', 'as' => 'product_dinetkan.'], function () {
        Route::get('/',[\App\Http\Controllers\Admin\ProductDinetkanController::class,'index'])->name('index');
        Route::post('/store',[\App\Http\Controllers\Admin\ProductDinetkanController::class,'store'])->name('store');
        Route::get('/single/{id}',[\App\Http\Controllers\Admin\ProductDinetkanController::class,'single'])->name('single');
        Route::put('/update/{id}',[\App\Http\Controllers\Admin\ProductDinetkanController::class,'update'])->name('update');
    });
});

Route::get('/api/get-snmp-types', [OltDeviceController::class, 'getSNMPTypes']);
Route::get('/send-email', [\App\Http\Controllers\TestEmailController::class, 'sendEmail']);
Route::get('/api/get-reg-mod', [OltDeviceController::class, 'getRegMod']);
Route::get('/api/get-status-onu', [OltDeviceController::class, 'getStatusOnu']);
Route::get('/api/get-v-port', [OltDeviceController::class, 'getVPort']);
Route::get('/api/get-zone-olt', [OltDeviceController::class, 'getZone']);
Route::get('/api/get-row-status', [OltDeviceController::class, 'getRowStatus']);
Route::get('/api/get-onu-type', [OltDeviceController::class, 'getOnuType']);
Route::get('/api/get-unconfig-onu', [OltDeviceController::class, 'getUnconfigOnu']);
Route::get('olt/device/unconfigured', [\App\Http\Controllers\OltDeviceController::class, 'unconfigured_olt']);
Route::get('olt/device/configured_onu', [\App\Http\Controllers\OltDeviceController::class, 'configured_onu']);
Route::get('olt/device/zone_list', [\App\Http\Controllers\OltDeviceController::class, 'zone_list']);
Route::get('olt/device/vlan_list', [\App\Http\Controllers\OltDeviceController::class, 'vlan_list']);
Route::get('olt/device/vlan_list_bind/{id}', [\App\Http\Controllers\OltDeviceController::class, 'vlan_list_bind']);
Route::get('olt/device/odb_list', [\App\Http\Controllers\OltDeviceController::class, 'odb_list']);
Route::get('olt/device/odb_olt', [\App\Http\Controllers\OltDeviceController::class, 'odb_olt']);
Route::get('olt/device/zone_olt', [\App\Http\Controllers\OltDeviceController::class, 'zone_olt']);
Route::get('olt/device/vlan_port_list', [\App\Http\Controllers\OltDeviceController::class, 'vlan_port_list']);
Route::get('olt/device/onu_type_list', [\App\Http\Controllers\OltDeviceController::class, 'onu_type_list']);
Route::get('olt/device/bandwidth_list', [\App\Http\Controllers\OltDeviceController::class, 'bandwidth_list']);
Route::get('olt/device/traffic_list', [\App\Http\Controllers\OltDeviceController::class, 'traffic_list']);
Route::get('olt/device/vlan_profile_list', [\App\Http\Controllers\OltDeviceController::class, 'vlan_profile_list']);
Route::get('olt/device/speed_table/{id}', [\App\Http\Controllers\OltDeviceController::class, 'speed_table']);
Route::post('/device/authorize', [\App\Http\Controllers\OltDeviceController::class, 'add_unconfig_onu']);
Route::get('/device/get-onu-history/{id}', [\App\Http\Controllers\OltDeviceController::class, 'getHistory']);

