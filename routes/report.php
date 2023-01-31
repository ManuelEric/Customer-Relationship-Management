<?php

use App\Http\Controllers\ClientEventController;
use App\Http\Controllers\PartnerProgramController;
use App\Http\Controllers\SalesTrackingController;
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



Route::get('sales', [SalesTrackingController::class, 'index'])->name('report.sales.tracking');

// Route::get('event', function () {
//     return view('pages.report.event-tracking.index');
// });

Route::get('event', [ClientEventController::class, 'report'])->name('report.client.event.index');


Route::get('invoice', function () {
    return view('pages.report.invoice.index');
});

// Route::get('partnership', function () {
//     return view('pages.report.partnership.index');
// });

Route::get('partnership', [PartnerProgramController::class, 'report'])->name('report.partnership.index');

Route::get('unpaid', function () {
    return view('pages.report.unpaid-payment.index');
});
