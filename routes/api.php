<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes Main
|--------------------------------------------------------------------------
|
| Main unchangeable API routes.
|
*/

Route::get('/', [App\Http\Controllers\Api\InfoController::class, 'info'])->name('info');
Route::get('/server/queue', [App\Http\Controllers\Api\ServerController::class, 'queue'])->name('server.queue');




# Sample:

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});