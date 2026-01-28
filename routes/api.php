<?php


use App\Http\Controllers\Mikrotik\NasController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
Route::middleware('auth:sanctum')->get('/check-token', function (\Illuminate\Http\Request $request) {
    return response()->json([
        'status' => 'valid',
        'user'   => $request->user()
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [\App\Http\Controllers\Api\AuthController::class, 'me']);
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::post('/logout-all', [\App\Http\Controllers\Api\AuthController::class, 'logoutAll']);

    Route::group(['as' => 'kemitraan.', 'prefix' => 'kemitraan/'], function () {

        // dashboard
        Route::group(['as' => 'dashboard.', 'prefix' => 'dashboard'], function () {
            Route::get('/',[\App\Http\Controllers\Api\Kemitraan\DashboardController::class, 'index']);
        });
        // account
        Route::group(['as' => 'account.', 'prefix' => 'account'], function () {
            Route::get('/info', [\App\Http\Controllers\Api\Kemitraan\AccountInfoController::class, 'info']);
            Route::get('/list_document', [\App\Http\Controllers\Api\Kemitraan\AccountInfoController::class, 'list_document']);
            Route::post('/update_info', [\App\Http\Controllers\Api\Kemitraan\AccountInfoController::class, 'update_info']);
        });

        // service
        Route::group(['as' => 'service.', 'prefix' => 'service'], function () {
            Route::get('/active', [\App\Http\Controllers\Api\Kemitraan\ServiceController::class, 'active']);
            Route::get('/inactive', [\App\Http\Controllers\Api\Kemitraan\ServiceController::class, 'inactive']);
            Route::get('/suspend', [\App\Http\Controllers\Api\Kemitraan\ServiceController::class, 'suspend']);
            Route::get('/overdue', [\App\Http\Controllers\Api\Kemitraan\ServiceController::class, 'overdue']);
            Route::get('/detail_service', [\App\Http\Controllers\Api\Kemitraan\ServiceController::class, 'detail_service']);
        });

        // inv
        Route::group(['as' => 'inv.', 'prefix' => 'inv'], function () {
            Route::get('/paid', [\App\Http\Controllers\Api\Kemitraan\InvDinetkanController::class, 'paid']);
            Route::get('/unpaid', [\App\Http\Controllers\Api\Kemitraan\InvDinetkanController::class, 'unpaid']);
            Route::get('/expired', [\App\Http\Controllers\Api\Kemitraan\InvDinetkanController::class, 'expired']);
            Route::get('/get_by_invoice_id', [\App\Http\Controllers\Api\Kemitraan\InvDinetkanController::class, 'get_by_invoice_id']);
            Route::get('/get_payment_method', [\App\Http\Controllers\Api\Kemitraan\InvDinetkanController::class, 'get_payment_method']);
            Route::post('/generate_va', [\App\Http\Controllers\Api\Kemitraan\InvDinetkanController::class, 'generate_va']);
        });

        // mrtg
        Route::group(['as' => 'mrtg.', 'prefix' => 'mrtg'], function () {
            Route::get('/list_graph', [\App\Http\Controllers\Api\Kemitraan\MrtgController::class, 'list_graph']);
            Route::get('/daily_graph_json/{id}', [\App\Http\Controllers\Api\Kemitraan\MrtgController::class, 'daily_graph_json']);
            Route::get('/weekly_graph_json/{id}', [\App\Http\Controllers\Api\Kemitraan\MrtgController::class, 'weekly_graph_json']);
            Route::get('/monthly_graph_json/{id}', [\App\Http\Controllers\Api\Kemitraan\MrtgController::class, 'monthly_graph_json']);


            Route::get('/graph_json_mikrotik_daily/{id}', [\App\Http\Controllers\Api\Kemitraan\MrtgController::class, 'graph_json_mikrotik_daily']);
            Route::get('/graph_json_mikrotik_weekly/{id}', [\App\Http\Controllers\Api\Kemitraan\MrtgController::class, 'graph_json_mikrotik_weekly']);
            Route::get('/graph_json_mikrotik_monthly/{id}', [\App\Http\Controllers\Api\Kemitraan\MrtgController::class, 'graph_json_mikrotik_monthly']);

            Route::get('/get_ifname_image/{hostname}/{ifname}', [\App\Http\Controllers\Api\Kemitraan\MrtgController::class, 'get_ifname_image']);

        });
        Route::group(['as' => 'report_mitra.', 'prefix' => 'report_mitra'], function () {
            Route::group(['as' => 'profile.', 'prefix' => '/profile'], function () {
                Route::get('/', [\App\Http\Controllers\Api\Kemitraan\ProductDinetkanController::class, 'index']);
                Route::post('/store', [\App\Http\Controllers\Api\Kemitraan\ProductDinetkanController::class, 'store']);
                Route::get('/single/{id}', [\App\Http\Controllers\Api\Kemitraan\ProductDinetkanController::class, 'single']);
                Route::put('/update/{id}', [\App\Http\Controllers\Api\Kemitraan\ProductDinetkanController::class, 'update']);
            });
            Route::group(['as' => 'member.', 'prefix' => '/member'], function () {
                // dinetkan
                Route::get('/', [\App\Http\Controllers\Api\Kemitraan\MemberDinetkanController::class, 'index']);
                Route::get('/single/{id}', [\App\Http\Controllers\Api\Kemitraan\MemberDinetkanController::class, 'edit_pelanggan']);
                Route::put('/update', [\App\Http\Controllers\Api\Kemitraan\MemberDinetkanController::class, 'update']);
                Route::post('/single_delete/{id}', [\App\Http\Controllers\Api\Kemitraan\MemberDinetkanController::class, 'single_delete']);
                Route::post('/store', [\App\Http\Controllers\Api\Kemitraan\MemberDinetkanController::class, 'store']);
                Route::get('/report/unpaid', [\App\Http\Controllers\Admin\Billing\MemberDinetkanController::class, 'unpiad']);
                Route::get('/unpaid', [\App\Http\Controllers\Admin\Billing\MemberDinetkanController::class, 'piad']);
            });

            Route::group(['as' => 'report.', 'prefix' => '/report'], function (){
                Route::get('/paid', [\App\Http\Controllers\Api\Kemitraan\MemberDinetkanController::class, 'mapping_service_paid']);
                Route::get('/unpaid', [\App\Http\Controllers\Api\Kemitraan\MemberDinetkanController::class, 'mapping_service_unpaid']);
                Route::get('/detail/{month}/{year}', [\App\Http\Controllers\Api\Kemitraan\MemberDinetkanController::class, 'mapping_service_item']);
                Route::get('/pay/{id}', [\App\Http\Controllers\Api\Kemitraan\MemberDinetkanController::class, 'mapping_service_pay']);
                Route::post('/pay/generate/{id}', [\App\Http\Controllers\Api\Kemitraan\MemberDinetkanController::class, 'generate_va']);
            });
        });

    });

    Route::get('geo/provinces', [\App\Http\Controllers\Api\GeoMasterController::class, 'province']);
    Route::get('geo/regencies/{province_id}', [\App\Http\Controllers\Api\GeoMasterController::class, 'regencies']);
    Route::get('geo/districts/{regency_id}', [\App\Http\Controllers\Api\GeoMasterController::class, 'districts']);
    Route::get('geo/villages/{district_id}', [\App\Http\Controllers\Api\GeoMasterController::class, 'villages']);
    Route::post('ping-check', [NasController::class, 'checkPing']);
});

Route::middleware('auth:mitra')->group(function () {
    Route::group(['as' => 'sales.', 'prefix' => 'sales/'], function () {

        // dashboard
        Route::group(['as' => 'dashboard.', 'prefix' => 'dashboard'], function () {
            Route::get('/',[\App\Http\Controllers\Api\Sales\DashboardController::class, 'index']);
        });

        Route::group(['as' => 'widrawal.', 'prefix' => 'widrawal'], function () {
            Route::get('/', [\App\Http\Controllers\Api\Sales\WidrawalController::class, 'index']);
            Route::post('/check_account', [\App\Http\Controllers\Api\Sales\WidrawalController::class, 'inq_account']);
            Route::post('/inquiry', [\App\Http\Controllers\Api\Sales\WidrawalController::class, 'inquiry']);
            Route::post('/payment', [\App\Http\Controllers\Api\Sales\WidrawalController::class, 'payment']);
            Route::get('/history', [\App\Http\Controllers\Api\Sales\WidrawalController::class, 'history']);
        });

        Route::group(['as' => 'users.', 'prefix' => 'users'], function () {
            Route::get('/', [\App\Http\Controllers\Api\Sales\UserController::class, 'index']);
            Route::post('/create_users', [\App\Http\Controllers\Api\Sales\UserController::class, 'create_users']);
            Route::get('/keuangan_dinetkan', [\App\Http\Controllers\Api\Sales\UserController::class, 'keuangan_dinetkan']);
            Route::get('/invoice/paid', [\App\Http\Controllers\Api\Sales\UserController::class, 'paid']);
            Route::get('/invoice/unpaid', [\App\Http\Controllers\Api\Sales\UserController::class, 'unpaid']);
            Route::get('/invoice/expired', [\App\Http\Controllers\Api\Sales\UserController::class, 'expired']);
        });
        Route::group(['as' => 'pppoe_users', 'prefix' => 'pppoe_users'], function () {
            Route::get('/', [\App\Http\Controllers\Api\Sales\PppoeUserController::class, 'index']);
        });
    });
});
