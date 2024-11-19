<?php

use App\Http\Controllers\LeadTrackerController;
use App\Http\Controllers\ReportController;
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



// Route::get('sales', [SalesTrackingController::class, 'index'])->name('report.sales.tracking');
Route::get('sales', [ReportController::class, 'fnSalesTracking'])->name('report.sales.tracking');

Route::get('event', [ReportController::class, 'fnEventTracking'])->name('report.client.event');

Route::get('partnership', [ReportController::class, 'fnPartnershipReport'])->name('report.partnership');

Route::get('invoice', [ReportController::class, 'fnInvoiceReceiptReport'])->name('report.invoice');

Route::get('unpaid', [ReportController::class, 'fnUnpaidPaymentReport'])->name('report.unpaid');

Route::get('program', [ReportController::class, 'fnProgramTracking'])->name('report.program.tracking');

Route::get('lead', [LeadTrackerController::class, 'index'])->name('report.lead');
