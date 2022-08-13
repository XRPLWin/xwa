<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Version 1
|--------------------------------------------------------------------------
|
| Versioned API routes, definition is located in RouteServiceProvider.
|
*/

#Account routes
Route::get('/account/info/{address}', [App\Http\Controllers\Api\AccountController::class, 'info'])/*->middleware('varnish5min')*/->name('account.info');
//Route::get('/account/trustlines/{account}', [App\Http\Controllers\Api\AccountController::class, 'trustlines'])/*->middleware('varnish5min')*/->name('account.trustlines');
//Route::get('/account/chart/spending/{account}', [App\Http\Controllers\Api\AccountController::class, 'chart_spending'])/*->middleware('varnish5min')*/->name('account.chart.spending');