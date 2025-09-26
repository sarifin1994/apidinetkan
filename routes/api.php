<?php


Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [\App\Http\Controllers\Api\AuthController::class, 'me']);
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::post('/logout-all', [\App\Http\Controllers\Api\AuthController::class, 'logoutAll']);

    // account
    Route::group(['as' => 'account.', 'prefix' => 'account'], function () {
        Route::get('/info', [\App\Http\Controllers\Api\AccountInfo::class, 'info']);
        Route::get('/list_document', [\App\Http\Controllers\Api\AccountInfo::class, 'list_document']);
    });

    // service
    Route::group(['as' => 'service.', 'prefix' => 'service'], function () {
        Route::get('/active', [\App\Http\Controllers\Api\ServiceController::class, 'active']);
        Route::get('/inactive', [\App\Http\Controllers\Api\ServiceController::class, 'inactive']);
        Route::get('/suspend', [\App\Http\Controllers\Api\ServiceController::class, 'suspend']);
        Route::get('/overdue', [\App\Http\Controllers\Api\ServiceController::class, 'overdue']);
        Route::get('/detail_service/{id}', [\App\Http\Controllers\Api\ServiceController::class, 'detail_service']);
    });

    // inv
    Route::group(['as' => 'inv.', 'prefix' => 'inv'], function () {
        Route::get('/paid', [\App\Http\Controllers\Api\InvDinetkan::class, 'paid']);
        Route::get('/unpaid', [\App\Http\Controllers\Api\InvDinetkan::class, 'unpaid']);
        Route::get('/expired', [\App\Http\Controllers\Api\InvDinetkan::class, 'expired']);
        Route::get('/get_by_invoice_id', [\App\Http\Controllers\Api\InvDinetkan::class, 'get_by_invoice_id']);
        Route::get('/get_payment_method', [\App\Http\Controllers\Api\InvDinetkan::class, 'get_payment_method']);
    });
});
