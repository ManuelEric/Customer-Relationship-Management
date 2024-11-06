<?php

use App\Http\Controllers\ClientEventController;
use App\Http\Controllers\LeadTrackerController;
use App\Http\Controllers\PartnerProgramController;
use App\Http\Controllers\ReportController;
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

Route::get('event', [ReportController::class, 'event'])->name('report.client.event');


// Route::get('invoice', function () {
//     return view('pages.report.invoice.index');
// });

// Route::get('partnership', function () {
//     return view('pages.report.partnership.index');
// });

Route::get('partnership', [ReportController::class, 'partnership'])->name('report.partnership');

Route::get('invoice', [ReportController::class, 'invoice_receipt'])->name('report.invoice');

Route::get('unpaid', [ReportController::class, 'unpaid_payment'])->name('report.unpaid');

Route::get('program', [ReportController::class, 'program_tracking'])->name('report.program.tracking');

Route::get('lead', [LeadTrackerController::class, 'index'])->name('report.lead');
