<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Report Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('sales', function () {
    return view('pages.report.sales-tracking.index');
});

Route::get('event', function () {
    return view('pages.report.event-tracking.index');
});

Route::get('invoice', function () {
    return view('pages.report.invoice.index');
});

Route::get('partnership', function () {
    return view('pages.report.partnership.index');
});

Route::get('unpaid', function () {
    return view('pages.report.unpaid-payment.index');
});