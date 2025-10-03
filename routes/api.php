<?php


Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [\App\Http\Controllers\Api\AuthController::class, 'me']);
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::post('/logout-all', [\App\Http\Controllers\Api\AuthController::class, 'logoutAll']);

    Route::group(['as' => 'kemitraan.', 'prefix' => 'kemitraan/'], function () {
        // account
        Route::group(['as' => 'account.', 'prefix' => 'account'], function () {
            Route::get('/info', [\App\Http\Controllers\Api\Kemitraan\AccountInfo::class, 'info']);
            Route::get('/list_document', [\App\Http\Controllers\Api\Kemitraan\AccountInfo::class, 'list_document']);
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
            Route::get('/paid', [\App\Http\Controllers\Api\Kemitraan\InvDinetkan::class, 'paid']);
            Route::get('/unpaid', [\App\Http\Controllers\Api\Kemitraan\InvDinetkan::class, 'unpaid']);
            Route::get('/expired', [\App\Http\Controllers\Api\Kemitraan\InvDinetkan::class, 'expired']);
            Route::get('/get_by_invoice_id', [\App\Http\Controllers\Api\Kemitraan\InvDinetkan::class, 'get_by_invoice_id']);
            Route::get('/get_payment_method', [\App\Http\Controllers\Api\Kemitraan\InvDinetkan::class, 'get_payment_method']);
        });

        // mrtg
        Route::group(['as' => 'mrtg.', 'prefix' => 'mrtg'], function () {
            Route::get('/list_graph', [\App\Http\Controllers\Api\Kemitraan\MrtgController::class, 'list_graph']);
        });

    });
});
