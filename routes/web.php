<?php

use App\Http\Controllers\Kemitraan\KemitraanUsersController;
// Activity Log & Log Viewer
//Route::resource('log', ActivityLogController::class)->middleware(['multi_auth:web', 'role:Admin', 'status:1']);
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
// ->middleware(['multi_auth:admin_dinetkan,web', 'role:Owner,Admin']);
