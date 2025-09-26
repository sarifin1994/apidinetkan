<?php

use App\Http\Controllers\Kemitraan\KemitraanUsersController;
use App\Models\Keuangan\KategoriKeuangan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Mapping\AreaController;
use App\Http\Controllers\Mapping\OdpController;
use App\Http\Controllers\Partnership\MitraController;
use App\Http\Controllers\Partnership\ResellerController;
use App\Http\Controllers\Pppoe\PppoeUserController;
use App\Http\Controllers\Pppoe\PppoeProfileController;
use App\Http\Controllers\Hotspot\HotspotUserController;
use App\Http\Controllers\Hotspot\HotspotProfileController;
use App\Http\Controllers\Mikrotik\VpnController;
use App\Http\Controllers\Mikrotik\NasController;
use App\Http\Controllers\Setting\CompanyController;
use App\Http\Controllers\Setting\IsolirController;
use App\Http\Controllers\Setting\BillingSettingController;
use App\Http\Controllers\Setting\MidtransController;
use App\Http\Controllers\Setting\AccountController;
use App\Http\Controllers\Whatsapp\MpwaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Keuangan\KategoriKeuanganController;
use App\Http\Controllers\Keuangan\TransaksiController;
use App\Http\Controllers\Keuangan\TransaksiMidtransController;
use App\Http\Controllers\Keuangan\TransaksiDuitkuController;
use App\Http\Controllers\Keuangan\TransaksiMitraController;
use App\Http\Controllers\Invoice\UnpaidController;
use App\Http\Controllers\Invoice\PaidController;
use App\Http\Controllers\Invoice\PrintController;
use App\Http\Controllers\Olt\OltController;
use App\Http\Controllers\Olt\ZteController;
use App\Http\Controllers\Olt\HsgqController;
use App\Http\Controllers\Olt\HiosoController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ManualController;
use App\Http\Controllers\Tiket\TiketGangguanController;
use App\Http\Controllers\RadiusLogController;
use App\Http\Controllers\Setting\VpnServerController;
use App\Http\Controllers\Setting\WaServerController;
use App\Http\Controllers\Setting\RoleController;
use App\Http\Controllers\Setting\PaymentController;
use App\Http\Controllers\Setting\DuitkuController;
use App\Http\Controllers\Keuangan\TransaksiOwnerController;
use App\Http\Controllers\Keuangan\TransaksiHotspotController;
use App\Http\Controllers\MapController;

Route::get('/map', [MapController::class, 'index']);
Route::get('/api/routers', [MapController::class, 'getRouters']); // API refresh


// use App\Http\Controllers\Setting\RadiusServerController;

// Route::get('/manual', [ManualController::class, 'manual']);
Route::get('/manual/update_balance', [ManualController::class, 'update_balance']);
Route::get('/manual/test_send/{no_invoice}/{_template}', [ManualController::class, 'test_send']);


// Auth Routes
Route::get('/', fn () => redirect('/auth'));
Route::get('/auth', [LoginController::class, 'login'])->name('login');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
Route::post('auth', [LoginController::class, 'auth'])
    ->name('auth')
    ->middleware(['throttle:5,1']);

Route::get('/register', [RegisterController::class, 'index']);
Route::post('/register', [RegisterController::class, 'register'])
    ->name('register')
    ->middleware(['throttle:5,1']);
Route::get('/verify', [RegisterController::class, 'verify']);
Route::post('/verify', [RegisterController::class, 'verifyOtp'])
    ->name('verify')
    ->middleware(['throttle:5,1']);
Route::post('/resend-otp', [RegisterController::class, 'resendOtp'])->middleware(['throttle:5,1']);

// after regist mitra dinetkan
Route::get('admin/account/after/get_info_dinetkan', [\App\Http\Controllers\Admin\Account\AccountInfoDinetkanController::class, 'get_info_dinetkan'])->name('admin.account.after.get_info_dinetkan');
Route::POST('admin/account/update_info_dinetkan', [\App\Http\Controllers\Admin\Account\AccountInfoDinetkanController::class, 'update_info_dinetkan'])->name('admin.account.info.update_info_dinetkan');
Route::post('admin/account/update_doc_info_dinetkan', [\App\Http\Controllers\Admin\Account\AccountInfoDinetkanController::class, 'update_doc_info_dinetkan'])->name('admin.account.info.update_doc_info_dinetkan');
Route::get('admin/account/show_file/{id}', [\App\Http\Controllers\Admin\Account\AccountInfoDinetkanController::class, 'show_file'])->name('admin.account.info.show_file');
// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware(['multi_auth:admin_dinetkan,web,mitra', 'status:1']);

Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data')->middleware(['multi_auth:admin_dinetkan,web,mitra', 'status:1']);


// Mapping
Route::resource('mapping/pop', AreaController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::resource('mapping/odp', OdpController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);

// Hotspot
Route::resource('hotspot/user', HotspotUserController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::post('hotspot/user/datatable', [HotspotUserController::class, 'datatable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::post('hotspot/user/generate', [HotspotUserController::class, 'generate'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::post('hotspot/user/print', [HotspotUserController::class, 'print'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::post('hotspot/user/enable/', [HotspotUserController::class, 'enable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::post('hotspot/user/disable/', [HotspotUserController::class, 'disable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::post('hotspot/user/kick/{username}', [HotspotUserController::class, 'kick'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::post('hotspot/user/reactivate/', [HotspotUserController::class, 'reactivate'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::get('hotspot/online', [HotspotUserController::class, 'online'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::post('hotspot/user/import', [HotspotUserController::class, 'import'])
    ->name('import.hotspot')
    ->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::get('hotspot/user/getSession/{id}', [HotspotUserController::class, 'getSession'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);

Route::resource('hotspot/profile', HotspotProfileController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::put('hotspot/profile/disable/{id}', [HotspotProfileController::class, 'disable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::put('hotspot/profile/enable/{id}', [HotspotProfileController::class, 'enable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);


Route::resource('hotspot_user', HotspotUserController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::post('hotspot_user/datatable', [HotspotUserController::class, 'datatable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::post('hotspot_user/generate', [HotspotUserController::class, 'generate'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::post('hotspot_user/print', [HotspotUserController::class, 'print'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::post('hotspot_user/enable/', [HotspotUserController::class, 'enable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::post('hotspot_user/disable/', [HotspotUserController::class, 'disable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::post('hotspot_user/kick/{username}', [HotspotUserController::class, 'kick'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::post('hotspot_user/reactivate/', [HotspotUserController::class, 'reactivate'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::get('hotspot_online', [HotspotUserController::class, 'online'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::post('hotspot_user/import', [HotspotUserController::class, 'import'])
    ->name('import.hotspot')
    ->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::get('hotspot_user/getSession/{id}', [HotspotUserController::class, 'getSession'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);

Route::resource('hotspot_profile', HotspotProfileController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::put('hotspot_profile/disable/{id}', [HotspotProfileController::class, 'disable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::put('hotspot_profile/enable/{id}', [HotspotProfileController::class, 'enable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);

// PPPoE
Route::resource('pppoe/user', PppoeUserController::class)->middleware(['multi_auth:admin_dinetkan,web,mitra', 'role:Admin,Teknisi,Kasir,Mitra', 'status:1']);
Route::get('pppoe/user/getKodeOdp/{id}', [PppoeUserController::class, 'getKodeOdp'])->middleware(['multi_auth:admin_dinetkan,web,mitra', 'role:Admin,Teknisi,Kasir,Mitra', 'status:1']);
Route::get('pppoe/user/getPrice/{id}', [PppoeUserController::class, 'getPrice'])->middleware(['multi_auth:admin_dinetkan,web,mitra', 'role:Admin,Teknisi,Kasir,Mitra', 'status:1']);
Route::post('pppoe/user/enable', [PppoeUserController::class, 'enable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi,Kasir', 'status:1']);
Route::post('pppoe/user/disable', [PppoeUserController::class, 'disable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi,Kasir', 'status:1']);
Route::post('pppoe/user/regist', [PppoeUserController::class, 'regist'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi,Kasir', 'status:1']);
Route::post('pppoe/user/kick/{username}', [PppoeUserController::class, 'kick'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi,Kasir', 'status:1']);
Route::post('pppoe/user/delete', [PppoeUserController::class, 'delete'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi,Kasir', 'status:1']);
Route::post('pppoe/user/export', [PppoeUserController::class, 'export'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi,Kasir', 'status:1']);
Route::post('pppoe/user/import', [PppoeUserController::class, 'import'])
    ->name('import.pppoe')
    ->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi,Kasir', 'status:1']);
Route::get('pppoe/online', [PppoeUserController::class, 'online'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi,Kasir', 'status:1']);
Route::get('pppoe/offline', [PppoeUserController::class, 'offline'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi,Kasir', 'status:1']);
Route::get('pppoe/user/getSession/{id}', [PppoeUserController::class, 'getSession'])->middleware(['multi_auth:admin_dinetkan,web,mitra', 'role:Admin,Teknisi,Kasir,Mitra', 'status:1']);
Route::post('pppoe/user/sync/', [PppoeUserController::class, 'sync'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::post('pppoe/user/offline/delete', [PppoeUserController::class, 'syncOffline'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::post('pppoe/user/session/clear', [PppoeUserController::class, 'clearSession'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);

Route::resource('pppoe/profile', PppoeProfileController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::put('pppoe/profile/disable/{id}', [PppoeProfileController::class, 'disable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::put('pppoe/profile/enable/{id}', [PppoeProfileController::class, 'enable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);



Route::resource('pppoe_user', PppoeUserController::class)->middleware(['multi_auth:admin_dinetkan,web,mitra', 'role:Admin,Teknisi,Kasir,Mitra', 'status:1']);
Route::get('pppoe_user/getKodeOdp/{id}', [PppoeUserController::class, 'getKodeOdp'])->middleware(['multi_auth:admin_dinetkan,web,mitra', 'role:Admin,Teknisi,Kasir,Mitra', 'status:1']);
Route::get('pppoe_user/getPrice/{id}', [PppoeUserController::class, 'getPrice'])->middleware(['multi_auth:admin_dinetkan,web,mitra', 'role:Admin,Teknisi,Kasir,Mitra', 'status:1']);
Route::post('pppoe_user/enable', [PppoeUserController::class, 'enable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi,Kasir', 'status:1']);
Route::post('pppoe_user/disable', [PppoeUserController::class, 'disable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi,Kasir', 'status:1']);
Route::post('pppoe_user/regist', [PppoeUserController::class, 'regist'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi,Kasir', 'status:1']);
Route::post('pppoe_user/kick/{username}', [PppoeUserController::class, 'kick'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi,Kasir', 'status:1']);
Route::post('pppoe_user/delete', [PppoeUserController::class, 'delete'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi,Kasir', 'status:1']);
Route::post('pppoe_user/export', [PppoeUserController::class, 'export'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi,Kasir', 'status:1']);
Route::post('pppoe_user/import', [PppoeUserController::class, 'import'])
    ->name('import.pppoe')
    ->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi,Kasir', 'status:1']);
Route::get('pppoe_online', [PppoeUserController::class, 'online'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi,Kasir', 'status:1']);
Route::get('pppoe_offline', [PppoeUserController::class, 'offline'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi,Kasir', 'status:1']);
Route::get('pppoe_user/getSession/{id}', [PppoeUserController::class, 'getSession'])->middleware(['multi_auth:admin_dinetkan,web,mitra', 'role:Admin,Teknisi,Kasir,Mitra', 'status:1']);
Route::post('pppoe_user/sync/', [PppoeUserController::class, 'sync'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::post('pppoe_user/offline/delete', [PppoeUserController::class, 'syncOffline'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::post('pppoe_user/session/clear', [PppoeUserController::class, 'clearSession'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);

Route::resource('pppoe_profile', PppoeProfileController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::put('pppoe_profile/disable/{id}', [PppoeProfileController::class, 'disable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::put('pppoe_profile/enable/{id}', [PppoeProfileController::class, 'enable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);

// Partnership
Route::resource('partnership/mitra', MitraController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::put('partnership/mitra/enable/{id}', [MitraController::class, 'enable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::put('partnership/mitra/disable/{id}', [MitraController::class, 'disable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::get('partnership/mitra/get_invoice_paid/{id}', [MitraController::class, 'get_invoice_paid'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::post('partnership/mitra/edit_saldo', [MitraController::class, 'edit_saldo'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);

Route::resource('partnership/reseller', ResellerController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::put('partnership/reseller/enable/{id}', [ResellerController::class, 'enable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::put('partnership/reseller/disable/{id}', [ResellerController::class, 'disable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);

// Mikrotik
Route::resource('radius/vpn', VpnController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::resource('radius/mikrotik', NasController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::post('radius/mikrotik/update/getTotalSession', [NasController::class, 'getTotalSession'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);

// Setting
Route::resource('setting/perusahaan', CompanyController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::put('setting/perusahaan/upload/{perusahaan}', [CompanyController::class, 'uploadLogo'])
    ->name('company.upload')
    ->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::resource('setting/billing', BillingSettingController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::resource('setting/isolir', IsolirController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::resource('setting/payment', PaymentController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::resource('setting/role', RoleController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);

// Owner
Route::resource('setting/vpn', VpnServerController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Owner', 'status:1']);
Route::resource('setting/wa', WaServerController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Owner', 'status:1']);
Route::get('setting/payment_owner/duitku_log', [\App\Http\Controllers\Setting\PaymentOwnerController::class, 'duitku_log'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Owner', 'status:1'])->name('setting.payment_owner.duitku_log');
Route::resource('setting/payment_owner', \App\Http\Controllers\Setting\PaymentOwnerController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Owner', 'status:1']);

// Whatsapp
Route::resource('whatsapp', MpwaController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Owner', 'status:1']);
Route::post('whatsapp/device/scan', [MpwaController::class, 'scan'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Owner', 'status:1']);
Route::get('whatsapp/device/scan', [MpwaController::class, 'scan'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Owner', 'status:1']);
Route::get('whatsapp/template/{id}', [MpwaController::class, 'getTemplate'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::put('whatsapp/template/active/{id}', [MpwaController::class, 'updateAccountActive'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::put('whatsapp/template/terbit/{id}', [MpwaController::class, 'updateInvoiceTerbit'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::put('whatsapp/template/reminder/{id}', [MpwaController::class, 'updateInvoiceReminder'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::put('whatsapp/template/overdue/{id}', [MpwaController::class, 'updateInvoiceOverdue'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::put('whatsapp/template/paid/{id}', [MpwaController::class, 'updatePaymentPaid'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::put('whatsapp/template/cancel/{id}', [MpwaController::class, 'updatePaymentCancel'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);

Route::put('whatsapp/template/tiketOpenPelanggan/{id}', [MpwaController::class, 'updateOpenPelanggan'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::put('whatsapp/template/tiketOpenTeknisi/{id}', [MpwaController::class, 'updateOpenTeknisi'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::put('whatsapp/template/tiketClosePelanggan/{id}', [MpwaController::class, 'updateClosedPelanggan'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::put('whatsapp/template/tiketCloseTeknisi/{id}', [MpwaController::class, 'updateClosedTeknisi'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);

Route::get('whatsapp/broadcast/getAllUserActive', [MpwaController::class, 'getAllUserActive'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::get('whatsapp/broadcast/getAllUserSuspend', [MpwaController::class, 'getAllUserSuspend'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::get('whatsapp/broadcast/getAllUserArea', [MpwaController::class, 'getAllUserArea'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::get('whatsapp/broadcast/getAllUserOdp', [MpwaController::class, 'getAllUserOdp'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);

Route::get('whatsapp/broadcast/getAllUserActive_owner', [MpwaController::class, 'getAllUserActive_owner'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Owner', 'status:1']);
Route::get('whatsapp/broadcast/getAllUserTrial_owner', [MpwaController::class, 'getAllUserTrial_owner'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Owner', 'status:1']);
Route::get('whatsapp/broadcast/getAllUserExpired_owner', [MpwaController::class, 'getAllUserExpired_owner'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Owner', 'status:1']);

Route::post('whatsapp/broadcast/send', [MpwaController::class, 'sendBroadcast'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Owner', 'status:1']);
Route::post('whatsapp/daftar/mpwa', [MpwaController::class, 'daftar'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Owner', 'status:1']);
Route::post('whatsapp/message/resend', [MpwaController::class, 'resendMessage'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Owner', 'status:1']);
Route::post('whatsapp/message/delete', [MpwaController::class, 'deleteMessage'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Owner', 'status:1']);

// User
Route::resource('user', UserController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Owner,Admin', 'status:1']);
Route::post('user/getTotalSessionHotspot', [UserController::class, 'getTotalSessionHotspot'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Owner', 'status:1']);
Route::post('user/getTotalSessionPppoe', [UserController::class, 'getTotalSessionPppoe'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Owner', 'status:1']);
Route::put('user/disable/{id}', [UserController::class, 'disable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Owner,Admin', 'status:1']);
Route::put('user/enable/{id}', [UserController::class, 'enable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Owner,Admin', 'status:1']);
Route::put('user/renew/{id}', [UserController::class, 'renew'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Owner,Admin', 'status:1']);
Route::put('user/upgrade/{id}', [UserController::class, 'upgrade'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Owner,Admin', 'status:1']);
Route::post('user/delete', [UserController::class, 'delete'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Owner,Admin', 'status:1']);
Route::post('user/set_allow_register', [UserController::class, 'set_allow_register'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Owner,Admin', 'status:1']);

// Keuangan
Route::resource('keuangan/kategori', KategoriKeuanganController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Kasir', 'status:1']);
Route::put('keuangan/kategori/disable/{id}', [KategoriKeuanganController::class, 'disable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Kasir', 'status:1']);
Route::put('keuangan/kategori/enable/{id}', [KategoriKeuanganController::class, 'enable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Kasir', 'status:1']);
Route::resource('keuangan/transaksi', TransaksiController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Kasir', 'status:1']);
Route::post('keuangan/transaksi/export', [TransaksiController::class, 'export'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Kasir', 'status:1']);

Route::resource('keuangan/hotspot', TransaksiHotspotController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Kasir', 'status:1']);

Route::resource('keuangan/midtrans', TransaksiMidtransController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Kasir', 'status:1']);
Route::post('keuangan/midtrans/pindah', [TransaksiMidtransController::class, 'pindahSaldo'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Kasir', 'status:1']);
Route::post('keuangan/midtrans/withdraw', [TransaksiMidtransController::class, 'withdraw'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Kasir', 'status:1']);

Route::get('keuangan/withdraw/midtrans', [TransaksiMidtransController::class,'index_withdraw'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Kasir', 'status:1']);

Route::resource('withdraw', TransaksiOwnerController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Owner', 'status:1']);
Route::post('withdraw/penarikan/pay', [TransaksiOwnerController::class, 'pay'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Owner', 'status:1']);

Route::resource('keuangan/duitku', TransaksiDuitkuController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Kasir', 'status:1']);
Route::post('keuangan/duitku/pindah', [TransaksiDuitkuController::class, 'pindahSaldo'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Kasir', 'status:1']);

Route::resource('keuangan/mitra', TransaksiMitraController::class)->middleware(['multi_auth:admin_dinetkan,web,mitra', 'role:Admin,Kasir,Mitra', 'status:1']);

// Invoice
Route::resource('invoice/unpaid', UnpaidController::class)->middleware(['multi_auth:admin_dinetkan,web,mitra', 'role:Admin,Kasir,Mitra', 'status:1']);
Route::resource('invoice/paid', PaidController::class)->middleware(['multi_auth:admin_dinetkan,web,mitra', 'role:Admin,Kasir,Mitra', 'status:1']);
Route::get('invoice/unpaid/getPelanggan/{id}', [UnpaidController::class, 'getPelanggan'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Kasir', 'status:1']);
Route::post('invoice/unpaid/generate', [UnpaidController::class, 'generateInvoice'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Kasir', 'status:1']);
Route::put('invoice/unpaid/payInvoice/{invoice}', [UnpaidController::class, 'payInvoice'])->middleware(['multi_auth:admin_dinetkan,web,mitra', 'role:Admin,Kasir,Mitra', 'status:1']);
Route::get('invoice/unpaid/getUnpaid/{id}', [UnpaidController::class, 'getUnpaid'])->middleware(['multi_auth:admin_dinetkan,web,mitra', 'role:Admin,Kasir,Mitra', 'status:1']);
Route::post('invoice/unpaid/payMassal', [UnpaidController::class, 'payMassal'])->middleware(['multi_auth:admin_dinetkan,web,mitra', 'role:Admin,Kasir,Mitra', 'status:1']);
Route::put('invoice/paid/unpayInvoice/{invoice}', [PaidController::class, 'unpayInvoice'])->middleware(['multi_auth:admin_dinetkan,web,mitra', 'role:Admin,Kasir,Mitra', 'status:1']);
Route::post('invoice/paid/unpayMassal', [PaidController::class, 'unpayMassal'])->middleware(['multi_auth:admin_dinetkan,web,mitra', 'role:Admin,Kasir,Mitra', 'status:1']);
// Route::get('inv/pdf/{invoice}', [PrintController::class, 'print'])
//     ->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Kasir', 'status:1']);
Route::post('invoice/unpaid/print', [PrintController::class, 'printUnpaid'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Kasir', 'status:1']);
Route::post('invoice/unpaid/export', [UnpaidController::class, 'exportUnpaid'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Kasir', 'status:1']);
Route::post('invoice/unpaid/resend', [UnpaidController::class, 'resendUnpaid'])->middleware(['multi_auth:admin_dinetkan,web,mitra', 'role:Admin,Kasir,Mitra', 'status:1']);

// Midtrans
Route::resource('midtrans', MidtransController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::post('midtrans/notification', [MidtransController::class, 'notification']);
// Route::get('pay/{id}', [MidtransController::class, 'bayar']);
Route::get('pay/{id}', [PaymentController::class, 'bayar'])->name('bayar.invoice');

// Account
Route::resource('account', AccountController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Kasir,Teknisi']);
Route::resource('password', \App\Http\Controllers\PasswordController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Kasir,Teknisi,Owner']);
Route::post('password', [\App\Http\Controllers\PasswordController::class, 'change'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Kasir,Teknisi,Owner']);

// License
Route::resource('license', LicenseController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Owner', 'status:1']);
Route::put('license/disable/{id}', [LicenseController::class, 'disable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Owner', 'status:1']);
Route::put('license/enable/{id}', [LicenseController::class, 'enable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Owner', 'status:1']);
Route::get('order/license', [LicenseController::class, 'license'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin']);
Route::post('order-license', [LicenseController::class, 'orderLicense'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin']);
Route::get('order/{order_number}', [LicenseController::class, 'status'])->name('order.confirm')->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin']);

// OLT & HSGQ
Route::resource('olt', OltController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::post('olt/hsgq/auth', [HsgqController::class, 'do_auth_device'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::post('olt/hsgq/onu/sync', [HsgqController::class, 'sync'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::post('olt/hsgq/onu/rename', [HsgqController::class, 'rename'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::get('olt/hsgq/dashboard', [HsgqController::class, 'show_olt'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::get('olt/hsgq/pon/all', [HsgqController::class, 'show_pon_all'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::get('olt/hsgq/pon/{id}', [HsgqController::class, 'show_pon'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::get('olt/hsgq/pon/{port}/onu/{onu}', [HsgqController::class, 'show_onu'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::post('olt/hsgq/reboot/pon/{port}/onu/{onu}', [HsgqController::class, 'reboot_onu'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::post('olt/hsgq/reboot/{olt}', [HsgqController::class, 'reboot_olt'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::post('olt/hsgq/save/{olt}', [HsgqController::class, 'save_olt'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::post('olt/hsgq/delete/pon/{port}/onu/{onu}', [HsgqController::class, 'delete_onu'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::get('olt/hsgq/logout', [HsgqController::class, 'deviceLogout'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);

Route::post('olt/hioso/auth', [HiosoController::class, 'do_auth_device'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::get('hioso', [HiosoController::class, 'dashboard']);
// Route::get('olt/hioso/pon/{pon_id}', [HiosoController::class, 'showOnu']);
// Route::get('olt/hioso/pon', [HiosoController::class, 'showAllOnu']);
Route::post('olt/hioso/onu/reboot', [HiosoController::class, 'reboot'])->name('onu.reboot');
Route::post('olt/hioso/onu/rename', [HiosoController::class, 'rename'])->name('onu.rename');
Route::post('olt/hioso/onu/delete', [HiosoController::class, 'delete'])->name('onu.delete');
Route::post('olt/hioso/save', [HiosoController::class, 'saveConfig'])->name('olt.save');

// Activity Log & Log Viewer
Route::resource('log', ActivityLogController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
// ->middleware(['multi_auth:admin_dinetkan,web', 'role:Owner,Admin']);

Route::post('/ping-check', [NasController::class, 'checkPing'])
    ->name('ping.check')
    ->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);

Route::resource('tiket/gangguan', TiketGangguanController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::get('tiket/gangguan/getSession/username', [TiketGangguanController::class, 'getSession'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::put('tiket/gangguan/close/{id}', [TiketGangguanController::class, 'close'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::post('tiket/gangguan/getGroup', [TiketGangguanController::class, 'getGroup'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::post('tiket/gangguan/saveGroup', [TiketGangguanController::class, 'saveGroup'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin,Teknisi', 'status:1']);
Route::get('/grafik-tiket', [TiketGangguanController::class, 'grafikTiket'])->name('grafik.tiket');

Route::get('logr', [RadiusLogController::class, 'show']);
Route::post('logr/clear', [RadiusLogController::class, 'clearLog']);

Route::post('duitku', [DuitkuController::class,'store'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::post('duitku/create', [DuitkuController::class, 'create'])->name('duitku.create');
Route::post('duitku/notification', [DuitkuController::class, 'callback'])->name('duitku.callback')
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
Route::post('duitku_owner/notification', [\App\Http\Controllers\Setting\DuitkuOwnerController::class, 'callback'])
    ->name('owner_duitku.callback')->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);


Route::post('duitku/create_setoran', [DuitkuController::class, 'create_setoran'])->name('duitku.create_setoran');
Route::post('duitku/notification_setoran', [DuitkuController::class, 'callback_setoran'])->name('duitku.callback_setoran')
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
Route::get('duitku/get_payment_method', [DuitkuController::class, 'get_payment_method'])->name('duitku.get_payment_method');


Route::post('dinetkan/duitku', [\App\Http\Controllers\Setting\DuitkuOwnerController::class,'store'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Owner', 'status:1']);
Route::post('dinetkan/duitku/create', [\App\Http\Controllers\Setting\DuitkuOwnerController::class, 'create'])->name('owner.duitku.create');
Route::post('dinetkan/duitku/notification', [\App\Http\Controllers\Setting\DuitkuOwnerController::class, 'callback'])->name('owner.duitku.callback');


// owner


// dinetkan
Route::get('admin/invoice_dinetkan/{id}', [\App\Http\Controllers\Admin\Account\LicenseDinetkanController::class, 'invoice'])->name('admin.invoice_dinetkan');
Route::get('admin/invoice_dinetkan/pdf/{id}', [\App\Http\Controllers\Admin\Account\LicenseDinetkanController::class, 'invoice_pdf'])->name('admin.invoice_dinetkan.pdf');
Route::post('admin/invoice_dinetkan/{license}', [\App\Http\Controllers\Admin\Account\LicenseDinetkanController::class, 'placeOrder'])->name('admin.invoice_dinetkan.place-order');
Route::post('admin/invoice_dinetkan/{id}/pay', [\App\Http\Controllers\Admin\Account\LicenseDinetkanController::class, 'pay'])->name('admin.invoice_dinetkan.pay');
Route::get('admin/invoice_dinetkan_search', [\App\Http\Controllers\Admin\Account\LicenseDinetkanController::class, 'search'])->name('admin.invoice_dinetkan_search');
Route::post('admin/invoice_dinetkan_generate', [\App\Http\Controllers\Admin\Account\LicenseDinetkanController::class, 'generate_va'])->name('admin.invoice_dinetkan.generate');

Route::get('admin/settings/master/geo/provinces', [\App\Http\Controllers\Settings\GeoMasterController::class, 'province']);
Route::get('admin/settings/master/geo/regencies/{province_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'regencies']);
Route::get('admin/settings/master/geo/districts/{regency_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'districts']);
Route::get('admin/settings/master/geo/villages/{district_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'villages']);

Route::get('admin/settings/master/geo/provinces_single/{province_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'province_single']);
Route::get('admin/settings/master/geo/regencies_single/{regency_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'regencies_single']);
Route::get('admin/settings/master/geo/districts_single/{district_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'districts_single']);
Route::get('admin/settings/master/geo/villages_single/{village_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'villages_single']);


Route::get('settings/master/geo/provinces', [\App\Http\Controllers\Settings\GeoMasterController::class, 'province']);
Route::get('settings/master/geo/regencies/{province_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'regencies']);
Route::get('settings/master/geo/districts/{regency_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'districts']);
Route::get('settings/master/geo/villages/{district_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'villages']);

Route::get('settings/master/geo/provinces_single/{province_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'province_single']);
Route::get('settings/master/geo/regencies_single/{regency_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'regencies_single']);
Route::get('settings/master/geo/districts_single/{district_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'districts_single']);
Route::get('settings/master/geo/villages_single/{village_id}', [\App\Http\Controllers\Settings\GeoMasterController::class, 'villages_single']);
// dinetkan

//Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data')->middleware(['multi_auth:admin_dinetkan,web,mitra', 'status:1']);
Route::group(['middleware' => ['multi_auth:admin_dinetkan,web,mitra', 'status:1']], function () {
    Route::group(['middleware' => ['role:Admin,dinetkan'], 'as' => 'dinetkan.', 'prefix' => 'dinetkan'], function () {

        Route::get('/users/{user}/login-as', [UserController::Class, 'loginAsUser'])->name('users.login-as');
        // new user dinetkan
        Route::get('/users_dinetkan', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'index'])->name('users_dinetkan');
        Route::get('/users_dinetkan/create', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'create'])->name('users_dinetkan.create');
        Route::post('/users_dinetkan', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'store'])->name('users_dinetkan.store');
        Route::get('/users_dinetkan/{user}', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'edit'])->name('users_dinetkan.edit');
        Route::put('/users_dinetkan/{user}', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'update'])->name('users_dinetkan.update');
        Route::post('/users_dinetkan/update_new', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'update_new'])->name('users_dinetkan.update_new');
        Route::get('/users_dinetkan/detail_cacti/{dinetkan_user_id}', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'detail_cacti'])->name('users_dinetkan.detail_cacti');
        Route::get('/users_dinetkan/detail/{dinetkan_user_id}', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'detail'])->name('users_dinetkan.detail');
        Route::post('/users_dinetkan/update_cacti/{user}', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'update_cacti'])->name('users_dinetkan.update_cacti');
        Route::post('/users_dinetkan/update_cacti2/{user}', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'update_cacti2'])->name('users_dinetkan.update_cacti2');
        Route::post('/users_dinetkan/update_cacti_service/{user}', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'update_cacti_service'])->name('users_dinetkan.update_cacti_service');
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
        Route::get('/users_dinetkan_service_by_mitra/{dinetkan_user_id}', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'service_by_mitra'])->name('users_dinetkan_service_by_mitra');
        Route::get('/users_dinetkan_get_admin_import', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'get_admin_import'])->name('users_dinetkan_get_admin_import');
        Route::post('/users_dinetkan/import', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'import'])->name('users_dinetkan.import');
        Route::post('/users_dinetkan/delete/{id}', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'delete'])->name('users_dinetkan.delete');
        Route::get('/users_dinetkan/get_single/{id}', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'get_single'])->name('users_dinetkan.get_single');
        Route::post('/users_dinetkan/update_cacti_service', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'update_cacti_service'])->name('users_dinetkan.update_cacti_service');
        Route::post('/users_dinetkan/validate_data', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'validate_data'])->name('users_dinetkan.validate_data');
        Route::post('/users_dinetkan/accept_new', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'accept_new'])->name('users_dinetkan.accept_new');
        Route::get('/users_dinetkan_getdata', [\App\Http\Controllers\Dinetkan\UserDinetkanController::class, 'getdata'])->name('users_dinetkan.getdata');

        Route::get('/license_dinetkan', [\App\Http\Controllers\Dinetkan\LicenseDinetkanController::class, 'index'])->name('license_dinetkan');
        Route::post('/license_dinetkan', [\App\Http\Controllers\Dinetkan\LicenseDinetkanController::class, 'store'])->name('license_dinetkan.store');
        Route::get('/license_dinetkan/{license}', [\App\Http\Controllers\Dinetkan\LicenseDinetkanController::class, 'edit'])->name('license_dinetkan.edit');
        Route::put('/license_dinetkan/{license}', [\App\Http\Controllers\Dinetkan\LicenseDinetkanController::class, 'update'])->name('license_dinetkan.update');
        Route::delete('/license_dinetkan/{license}', [\App\Http\Controllers\Dinetkan\LicenseDinetkanController::class, 'destroy'])->name('license_dinetkan.destroy');
        Route::get('/license_dinetkan/by_category/{category_id}/{type}', [\App\Http\Controllers\Dinetkan\LicenseDinetkanController::class, 'by_category']);
        Route::get('/license_dinetkan/by_license/{license_id}', [\App\Http\Controllers\Dinetkan\LicenseDinetkanController::class, 'by_license']);


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
        Route::get('/invoice_dinetkan/order/create', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'create_order'])->name('invoice_dinetkan.order.create');
        Route::get('/invoice_dinetkan/order/service_detail/{id}', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'get_service_detail'])->name('invoice_dinetkan.order.service_detail');
        Route::post('/invoice_dinetkan/order/update_service_detail', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'update_service_detail'])->name('invoice_dinetkan.order.update_service_detail');
        Route::post('/invoice_dinetkan/order/mikrotik_update_service_detail', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'mikrotik_update_service_detail'])->name('invoice_dinetkan.order.mikrotik_update_service_detail');
        Route::get('/invoice_dinetkan/order/edit/{id}', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'edit_order'])->name('invoice_dinetkan.order.edit');
        Route::POST('/invoice_dinetkan/order/update_active_graph', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'update_active_graph'])->name('invoice_dinetkan.order.update_active_graph');
        Route::post('/invoice_dinetkan/order/libre_update_service_detail', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'libre_update_service_detail'])->name('invoice_dinetkan.order.libre_update_service_detail');
        Route::post('/invoice_dinetkan/order/update_service_detail_2', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'update_service_detail_2'])->name('invoice_dinetkan.order.update_service_detail_2');
        Route::get('/invoice_dinetkan/order/upgrade/{id}', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'upgrade'])->name('invoice_dinetkan.order.upgrade');
        Route::post('/invoice_dinetkan/order/check_price', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'check_price'])->name('invoice_dinetkan.order.check_price');
        Route::post('/invoice_dinetkan/order/create_upgrade', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'create_upgrade'])->name('invoice_dinetkan.order.create_upgrade');

        Route::post('/invoice_dinetkan/create', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'create'])->name('invoice_dinetkan.create');
        Route::post('/invoice_dinetkan/create_new', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'create_new'])->name('invoice_dinetkan.create_new');
        Route::get('/invoice_dinetkan/get_tree_node_mrtg/{id}', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'get_tree_node_mrtg'])->name('invoice_dinetkan.get_tree_node_mrtg');
        Route::get('/invoice_dinetkan/get_graph_mrtg/{node}/{page}', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'get_graph_mrtg'])->name('invoice_dinetkan.get_graph_mrtg');
        Route::get('/invoice_dinetkan/by_license/{license_id}', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'by_license']);
        Route::POST('/invoice_dinetkan/by_id_mitra', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'by_id_mitra']);
        Route::get('/invoice_dinetkan/get_user/{id}', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'get_user']);
        Route::get('/invoice_dinetkan/order/get_ifname/{id}', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'get_ifname'])->name('invoice_dinetkan.order.get_ifname');
        Route::get('/invoice_dinetkan/order/delete_ifname/{id}', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'delete_ifname'])->name('invoice_dinetkan.order.delete_ifname');
        Route::get('/invoice_dinetkan/order/delete_service_doc/{id}', [\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'delete_service_doc'])->name('invoice_dinetkan.order.delete_service_doc');

        Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
            Route::group(['prefix' => 'master', 'as' => 'master.'], function () {
                Route::get('/geo/provinces', [\App\Http\Controllers\Dinetkan\GeoMasterController::class, 'province']);
                Route::get('/geo/regencies/{province_id}', [\App\Http\Controllers\Dinetkan\GeoMasterController::class, 'regencies']);
                Route::get('/geo/districts/{regency_id}', [\App\Http\Controllers\Dinetkan\GeoMasterController::class, 'districts']);
                Route::get('/geo/villages/{district_id}', [\App\Http\Controllers\Dinetkan\GeoMasterController::class, 'villages']);

                Route::get('/geo/provinces_single/{province_id}', [\App\Http\Controllers\Dinetkan\GeoMasterController::class, 'province_single']);
                Route::get('/geo/regencies_single/{regency_id}', [\App\Http\Controllers\Dinetkan\GeoMasterController::class, 'regencies_single']);
                Route::get('/geo/districts_single/{district_id}', [\App\Http\Controllers\Dinetkan\GeoMasterController::class, 'districts_single']);
                Route::get('/geo/villages_single/{village_id}', [\App\Http\Controllers\Dinetkan\GeoMasterController::class, 'villages_single']);
            });
        });

        // master pop
        Route::get('/master_pop', [\App\Http\Controllers\Dinetkan\MasterPopController::class, 'index'])->name('master_pop');
        Route::post('/master_pop', [\App\Http\Controllers\Dinetkan\MasterPopController::class, 'store'])->name('master_pop.store');
        Route::post('/master_pop/{id}', [\App\Http\Controllers\Dinetkan\MasterPopController::class, 'delete'])->name('master_pop.delete');
        Route::put('/master_pop/{id}', [\App\Http\Controllers\Dinetkan\MasterPopController::class, 'update'])->name('master_pop.update');
        Route::get('/master_pop/get_single/{id}', [\App\Http\Controllers\Dinetkan\MasterPopController::class, 'single'])->name('master_pop.single');
        // master pop

        // master mikrotik
        Route::get('/master_mikrotik', [\App\Http\Controllers\Dinetkan\MasterMikrotikController::class, 'index'])->name('master_mikrotik');
        Route::post('/master_mikrotik', [\App\Http\Controllers\Dinetkan\MasterMikrotikController::class, 'store'])->name('master_mikrotik.store');
        Route::post('/master_mikrotik/{id}', [\App\Http\Controllers\Dinetkan\MasterMikrotikController::class, 'delete'])->name('master_mikrotik.delete');
        Route::put('/master_mikrotik/{id}', [\App\Http\Controllers\Dinetkan\MasterMikrotikController::class, 'update'])->name('master_mikrotik.update');
        Route::get('/master_mikrotik/get_single/{id}', [\App\Http\Controllers\Dinetkan\MasterMikrotikController::class, 'single'])->name('master_mikrotik.single');
        Route::get('/master_mikrotik/get_vlan/{id}', [\App\Http\Controllers\Dinetkan\MasterMikrotikController::class, 'get_vlan'])->name('master_mikrotik.get_vlan');
        Route::get('/master_mikrotik/disabled_vlan/{service_id}', [\App\Http\Controllers\Dinetkan\MasterMikrotikController::class, 'disabled_vlan'])->name('master_mikrotik.disabled_vlan');
        Route::get('/master_mikrotik/enabled_vlan/{service_id}', [\App\Http\Controllers\Dinetkan\MasterMikrotikController::class, 'enabled_vlan'])->name('master_mikrotik.enabled_vlan');
        Route::get('/master_mikrotik/get_vlan_single/{service_id}', [\App\Http\Controllers\Dinetkan\MasterMikrotikController::class, 'get_vlan_single'])->name('master_mikrotik.get_vlan_single');
        // master mikrotik

        // keuangan
        Route::get('keuangan/index', [\App\Http\Controllers\Dinetkan\TransaksiDinetkanController::class,'index'])->name('keuangan.index');
        // keuangan

        // master metro
        Route::get('/master_metro', [\App\Http\Controllers\Dinetkan\MasterMetroController::class, 'index'])->name('master_metro');
        Route::post('/master_metro', [\App\Http\Controllers\Dinetkan\MasterMetroController::class, 'store'])->name('master_metro.store');
        Route::post('/master_metro/delete/{id}', [\App\Http\Controllers\Dinetkan\MasterMetroController::class, 'delete'])->name('master_metro.delete');
        Route::post('/master_metro/{id}', [\App\Http\Controllers\Dinetkan\MasterMetroController::class, 'update'])->name('master_metro.update');
        Route::get('/master_metro/get_single/{id}', [\App\Http\Controllers\Dinetkan\MasterMetroController::class, 'single'])->name('master_metro.single');
        // master metro

        // dinetkan
        Route::get('/settings_dinetkan', [\App\Http\Controllers\Dinetkan\SettingsDinetkanController::class, 'index'])->name('settings_dinetkan');
        Route::post('/settings_dinetkan/site', [\App\Http\Controllers\Dinetkan\SettingsDinetkanController::class, 'updateSite'])->name('settings_dinetkan.update.site');
        Route::post('/settings_dinetkan/tripay', [\App\Http\Controllers\Dinetkan\SettingsDinetkanController::class, 'updateTripay'])->name('settings_dinetkan.update.tripay');
        Route::put('/settings_dinetkan/license', [\App\Http\Controllers\Dinetkan\SettingsDinetkanController::class, 'updateLicense'])->name('settings_dinetkan.update.license');
        Route::post('/settings_dinetkan/licensemitra', [\App\Http\Controllers\Dinetkan\SettingsDinetkanController::class, 'updateLicenseMitra'])->name('settings_dinetkan.update.licensemitra');
        // dinetkan

        Route::get('/settings', [\App\Http\Controllers\Dinetkan\SettingsController::class, 'index'])->name('settings');
        Route::put('/settings/site', [\App\Http\Controllers\Dinetkan\SettingsController::class, 'updateSite'])->name('settings.update.site');
        Route::post('/settings/tripay', [\App\Http\Controllers\Dinetkan\SettingsController::class, 'updateTripay'])->name('settings.update.tripay');
        Route::put('/settings/license', [\App\Http\Controllers\Dinetkan\SettingsController::class, 'updateLicense'])->name('settings.update.license');
        Route::put('/settings/updatemonitoringnotif', [\App\Http\Controllers\Dinetkan\SettingsController::class, 'updateMonitoringNotif'])->name('settings.update.updatemonitoringnotif');


        // new user dinetkan

        Route::group(['prefix' => 'whatsapp', 'as' => 'whatsapp.'], function () {
            Route::get('/', [\App\Http\Controllers\Dinetkan\WhatsappController::class, 'index'])->name('index');
            Route::get('/start', [\App\Http\Controllers\Dinetkan\WhatsappController::class, 'start'])->name('start');
            Route::get('/restart', [\App\Http\Controllers\Dinetkan\WhatsappController::class, 'restart'])->name('restart');
            Route::get('/get_group', [\App\Http\Controllers\Dinetkan\WhatsappController::class, 'get_group'])->name('get_group');
            Route::get('/get_qr', [\App\Http\Controllers\Dinetkan\WhatsappController::class, 'get_qr'])->name('get_qr');
            Route::get('/logout', [\App\Http\Controllers\Dinetkan\WhatsappController::class, 'logout'])->name('logout');
            Route::post('/update_group', [\App\Http\Controllers\Dinetkan\WhatsappController::class, 'update_group'])->name('update_group');
            Route::get('/start_whatsapp', [\App\Http\Controllers\Dinetkan\WhatsappController::class, 'start_whatsapp'])->name('start_whatsapp');
            Route::get('/template/{id}', [\App\Http\Controllers\Dinetkan\WhatsappController::class, 'getTemplate'])->name('template');
        });
    });

    Route::group(['middleware' => ['multi_auth:admin_dinetkan,web,mitra','role:Admin'], 'as' => 'admin.', 'prefix' => 'admin'], function () {
        Route::group(['prefix' => 'account', 'as' => 'account.'], function () {
            Route::resource('info_dinetkan', \App\Http\Controllers\Admin\Account\AccountInfoDinetkanController::class);
            Route::get('get_info_dinetkan', [\App\Http\Controllers\Admin\Account\AccountInfoDinetkanController::class, 'get_info_dinetkan'])->name('info.get_info_dinetkan');
            Route::resource('mrtg', \App\Http\Controllers\Admin\Account\MrtgDinetkanController::class);
            Route::get('/get_graph_json/{id}', [\App\Http\Controllers\Admin\Account\MrtgDinetkanController::class, 'get_graph_json'])->name('mrtg.get_graph_json');
            Route::get('/week_get_graph_json/{id}', [\App\Http\Controllers\Admin\Account\MrtgDinetkanController::class, 'week_get_graph_json'])->name('mrtg.week_get_graph_json');
            Route::get('/month_get_graph_json/{id}', [\App\Http\Controllers\Admin\Account\MrtgDinetkanController::class, 'month_get_graph_json'])->name('mrtg.month_get_graph_json');
            Route::get('/year_get_graph_json', [\App\Http\Controllers\Admin\Account\MrtgDinetkanController::class, 'year_get_graph_json'])->name('mrtg.year_get_graph_json');
            Route::get('/mrtg/get_ifname_image/{hostname}/{ifname}', [\App\Http\Controllers\Admin\Account\MrtgDinetkanController::class, 'get_ifname_image'])->name('mrtg.get_ifname_image');

            Route::get('/graph_json_mikrotik/{id}', [\App\Http\Controllers\Admin\Account\MrtgDinetkanController::class, 'graph_json_mikrotik'])->name('mrtg.graph_json_mikrotik');
            Route::get('/graph_json_mikrotik_weekly/{id}', [\App\Http\Controllers\Admin\Account\MrtgDinetkanController::class, 'graph_json_mikrotik_weekly'])->name('mrtg.graph_json_mikrotik_weekly');
            Route::get('/graph_json_mikrotik_monthly/{id}', [\App\Http\Controllers\Admin\Account\MrtgDinetkanController::class, 'graph_json_mikrotik_monthly'])->name('mrtg.graph_json_mikrotik_monthly');


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
                Route::get('/order/detail_service/{id}', [\App\Http\Controllers\Admin\Account\InvoiceDinetkanController::class, 'detail_service'])->name('order.detail_service');
                Route::post('/order/update_service_detail', [\App\Http\Controllers\Admin\Account\InvoiceDinetkanController::class, 'update_service_detail'])->name('order.update_service_detail');

            });
        });
        Route::group(['prefix' => 'billing', 'as' => 'billing.'], function () {
            // dinetkan
            Route::get('/member_dinetkan/index', [\App\Http\Controllers\Admin\Billing\MemberDinetkanController::class, 'index'])->name('member_dinetkan.index');
            Route::post('/member_dinetkan/store', [\App\Http\Controllers\Admin\Billing\MemberDinetkanController::class, 'store'])->name('member_dinetkan.store');
            Route::get('/member_dinetkan/single/{id}', [\App\Http\Controllers\Admin\Billing\MemberDinetkanController::class, 'single'])->name('member_dinetkan.single');
            Route::put('/member_dinetkan/update/{id}', [\App\Http\Controllers\Admin\Billing\MemberDinetkanController::class, 'update'])->name('member_dinetkan.update');
            Route::put('/member_dinetkan/update_product/{id}', [\App\Http\Controllers\Admin\Billing\MemberDinetkanController::class, 'update_product'])->name('member_dinetkan.update_product');
            Route::get('/member_dinetkan/mapping_service', [\App\Http\Controllers\Admin\Billing\MemberDinetkanController::class, 'mapping_service'])->name('member_dinetkan.mapping_service');
            Route::get('/member_dinetkan/mapping_service_paid', [\App\Http\Controllers\Admin\Billing\MemberDinetkanController::class, 'mapping_service_paid'])->name('member_dinetkan.mapping_service_paid');
            Route::get('/member_dinetkan/mapping_service_unpaid', [\App\Http\Controllers\Admin\Billing\MemberDinetkanController::class, 'mapping_service_unpaid'])->name('member_dinetkan.mapping_service_unpaid');
            Route::get('/member_dinetkan/mapping_service_by_id/{id}', [\App\Http\Controllers\Admin\Billing\MemberDinetkanController::class, 'mapping_service_by_id'])->name('member_dinetkan.mapping_service_by_id');
            Route::put('/member_dinetkan/update_mapping_service', [\App\Http\Controllers\Admin\Billing\MemberDinetkanController::class, 'update_mapping_service'])->name('member_dinetkan.update_mapping_service');
            Route::get('/member_dinetkan/mapping_service_pay/{id}', [\App\Http\Controllers\Admin\Billing\MemberDinetkanController::class, 'mapping_service_pay'])->name('member_dinetkan.mapping_service_pay');
            Route::post('/member_dinetkan/mapping_service_pay/generate', [\App\Http\Controllers\Admin\Billing\MemberDinetkanController::class, 'generate_va'])->name('member_dinetkan.mapping_service_pay.generate');
            Route::get('/member_dinetkan/mapping_service_item/{month}/{year}', [\App\Http\Controllers\Admin\Billing\MemberDinetkanController::class, 'mapping_service_item'])->name('member_dinetkan.mapping_service_item');
            Route::post('/member_dinetkan/single_delete/{id}', [\App\Http\Controllers\Admin\Billing\MemberDinetkanController::class, 'single_delete'])->name('member_dinetkan.single_delete');
            Route::get('/member_dinetkan/add', [\App\Http\Controllers\Admin\Billing\MemberDinetkanController::class, 'add'])->name('member_dinetkan.add');
            Route::get('/member_dinetkan/edit_pelanggan/{id}', [\App\Http\Controllers\Admin\Billing\MemberDinetkanController::class, 'edit_pelanggan'])->name('member_dinetkan.edit_pelanggan');
            // dinetkan
        });
        Route::group(['prefix' => 'product_dinetkan', 'as' => 'product_dinetkan.'], function () {
            Route::get('/',[\App\Http\Controllers\Admin\ProductDinetkanController::class,'index'])->name('index');
            Route::post('/store',[\App\Http\Controllers\Admin\ProductDinetkanController::class,'store'])->name('store');
            Route::get('/single/{id}',[\App\Http\Controllers\Admin\ProductDinetkanController::class,'single'])->name('single');
            Route::put('/update/{id}',[\App\Http\Controllers\Admin\ProductDinetkanController::class,'update'])->name('update');
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
    Route::post('/admin/duitku_dinetkan', [\App\Http\Controllers\Admin\Account\LicenseDinetkanController::class, 'duitkuNotification'])->name('admin.duitku_dinetkan')->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);


    // whatsapp api
    Route::group(['prefix' => 'whatsapp', 'as' => 'whatsapp.'], function () {
        Route::post('/receive_qr/{userid}',[\App\Http\Controllers\Callback\WhatsappCallbackController::class,'receive_qr'])
            ->name('receive_qr')
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

        Route::post('/receive_qr_admin/{userid}',[\App\Http\Controllers\Callback\WhatsappCallbackController::class,'receive_qr_admin'])
            ->name('receive_qr_admin')
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
    });
    // whatsapp api
});

Route::get('register_dinetkan', [\App\Http\Controllers\Auth\RegisterDinetkanController::class, 'showRegistrationForm'])->name('register_dinetkan');
Route::post('register_dinetkan', [\App\Http\Controllers\Auth\RegisterDinetkanController::class, 'register'])->name('register_dinetkan');

Route::get('/send-email', [\App\Http\Controllers\TestEmailController::class, 'sendEmail']);

Route::middleware(['multi_auth:admin_dinetkan,web', 'status:1'])->group(function () {
    Route::get('/smtp-setting', [\App\Http\Controllers\Setting\SmtpSettingController::class, 'index'])->name('smtp.index');
    Route::post('/smtp-setting', [\App\Http\Controllers\Setting\SmtpSettingController::class, 'update'])->name('smtp.update');
    Route::post('/smtp-setting/test', [\App\Http\Controllers\Setting\SmtpSettingController::class, 'test'])->name('smtp.test');
});

//Route::get('/api/get_by_pelanggan',[\App\Http\Controllers\Apibilling::class, 'get_by_pelanggan']);
//Route::get('/api/get_payment_method',[\App\Http\Controllers\Apibilling::class, 'get_payment_method']);
//Route::post('/api/generate_va_api', [\App\Http\Controllers\Apibilling::class, 'generate_va_api'])
//    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
//Route::get('/api/get_by_invoice_id',[\App\Http\Controllers\Apibilling::class, 'get_by_invoice_id']);
//Route::get('/api/get_payment_method_duitku',[\App\Http\Controllers\Apibilling::class, 'get_payment_method_duitku']);
//Route::get('/api/get_profile',[\App\Http\Controllers\Apibilling::class, 'get_profile']);
//Route::get('/api/get_profile',[\App\Http\Controllers\Apibilling::class, 'get_profile']);

Route::middleware('check.api.key.ext')->prefix('api')->group(function () {
    Route::get('/get_by_pelanggan', [\App\Http\Controllers\Apibilling::class, 'get_by_pelanggan']);
    Route::get('/get_payment_method', [\App\Http\Controllers\Apibilling::class, 'get_payment_method']);
    Route::post('/generate_va_api', [\App\Http\Controllers\Apibilling::class, 'generate_va_api'])->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
    Route::get('/get_by_invoice_id', [\App\Http\Controllers\Apibilling::class, 'get_by_invoice_id']);
    Route::get('/get_payment_method_duitku', [\App\Http\Controllers\Apibilling::class, 'get_payment_method_duitku']);
    Route::get('/get_profile/{id}', [\App\Http\Controllers\Apibilling::class, 'get_profile']);
    Route::post('/buy_voucher', [\App\Http\Controllers\Apibilling::class, 'buy_voucher'])->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
    Route::post('/generate_va_api_pppoe', [\App\Http\Controllers\Apibilling::class, 'generate_va_api_pppoe'])->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

});
Route::post('api/callback', [\App\Http\Controllers\Apibilling::class, 'callback'])
    ->name('notification.apibilling.callback')
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);


// Kemitraan
Route::resource('kemitraan/users', KemitraanUsersController::class)->middleware(['multi_auth:admin_dinetkan,web,mitra', 'role:Mitra', 'status:1']);
Route::get('/kemitraan/users/create',[KemitraanUsersController::Class, 'create'])->name('kemitraan.users.create');
Route::POST('/kemitraan/users/create_users',[KemitraanUsersController::Class, 'create_users'])->name('kemitraan.users.create_users');
Route::post('/kemitraan/validate_data', [KemitraanUsersController::class, 'validate_data'])->name('kemitraan.validate_data');
Route::get('kemitraan/keuangan_dinetkan', [KemitraanUsersController::class, 'keuangan_dinetkan'])->middleware(['multi_auth:admin_dinetkan,web,mitra', 'role:Mitra', 'status:1']);
//Route::get('update_service_id',[\App\Http\Controllers\Dinetkan\InvoiceDinetkanController::class, 'update_service_id']);
Route::get('/kemitraan/invoice', [KemitraanUsersController::class, 'invoice'])->name('kemitraan.invoice');
Route::get('/kemitraan/invoice/unpaid', [KemitraanUsersController::class, 'unpaid'])->name('kemitraan.invoice.unpaid');
Route::get('/kemitraan/invoice/paid', [KemitraanUsersController::class, 'paid'])->name('kemitraan.invoice.paid');
Route::get('/kemitraan/invoice/expired', [KemitraanUsersController::class, 'expired'])->name('kemitraan.invoice.expired');

// widrawal
Route::get('widrawal', [\App\Http\Controllers\Widrawal\WidrawalController::class, 'index'])->middleware(['multi_auth:admin_dinetkan,web,mitra', 'role:Mitra', 'status:1']);
Route::post('widrawal/inq_account', [\App\Http\Controllers\Widrawal\WidrawalController::class, 'inq_account'])->middleware(['multi_auth:admin_dinetkan,web,mitra', 'role:Mitra', 'status:1']);
Route::post('widrawal/inquiry', [\App\Http\Controllers\Widrawal\WidrawalController::class, 'inquiry'])->middleware(['multi_auth:admin_dinetkan,web,mitra', 'role:Mitra', 'status:1']);
Route::post('widrawal/payment', [\App\Http\Controllers\Widrawal\WidrawalController::class, 'payment'])->middleware(['multi_auth:admin_dinetkan,web,mitra', 'role:Mitra', 'status:1']);
Route::get('widrawal/history', [\App\Http\Controllers\Widrawal\WidrawalController::class, 'history'])->middleware(['multi_auth:admin_dinetkan,web,mitra', 'role:Mitra', 'status:1']);

// Partnership
Route::resource('dinetkan/admin', \App\Http\Controllers\DinetkanAdminController::class)->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::put('dinetkan/admin/enable/{id}', [\App\Http\Controllers\DinetkanAdminController::class, 'enable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
Route::put('dinetkan/admin/disable/{id}', [\App\Http\Controllers\DinetkanAdminController::class, 'disable'])->middleware(['multi_auth:admin_dinetkan,web', 'role:Admin', 'status:1']);
