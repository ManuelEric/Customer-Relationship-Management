<?php

use App\Http\Controllers\InvoiceProgramController;
use App\Http\Controllers\InvoiceSchoolController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// CLIENT 
// Route::get('client-program/create', function () {
//     return view('pages.invoice.client-program.form', ['status' => 'create']);
// });

// Route::get('client-program/1', function () {
//     return view('pages.invoice.client-program.form', ['status' => 'view']);
// });

// Route::get('client-program/1/edit', function () {
//     return view('pages.invoice.client-program.form', ['status' => 'edit']);
// });

// Route::get('client-program/1/export/pdf', function () {
//     return view('pages.invoice.client-program.export.invoice-pdf', ['is_session' => true]);
// });

// Route::get('client-program/status/{status?}', function ($status = null) {
//     return view('pages.invoice.client-program.index', ['status' => $status]);
// });

Route::resource('client-program', InvoiceProgramController::class, [
    'names' => [
        'index' => 'invoice.program.index',
        'store' => 'invoice.program.store',
        'create' => 'invoice.program.create',
        'show' => 'invoice.program.show',
        'update' => 'invoice.program.update',
        'edit' => 'invoice.program.edit',
        'destroy' => 'invoice.program.destroy',
    ]
]);
Route::prefix('client-program')->name('invoice.program.')->group(function() {
    Route::get('{client_program}/export', [InvoiceProgramController::class, 'export'])->name('export');
});



// PARTNER 
Route::get('corporate-program/create', function () {
    return view('pages.invoice.corporate-program.form', ['status' => 'create']);
});

Route::get('corporate-program/1', function () {
    return view('pages.invoice.corporate-program.form', ['status' => 'view']);
});

Route::get('corporate-program/1/edit', function () {
    return view('pages.invoice.corporate-program.form', ['status' => 'edit']);
});

Route::get('corporate-program/1/export/pdf', function () {
    return view('pages.invoice.corporate-program.export.invoice-pdf');
});

Route::get('corporate-program/status/{status?}', function ($status = null) {
    return view('pages.invoice.corporate-program.index', ['status' => $status]);
});



// SCHOOL 
Route::get('school-program/create', function () {
    return view('pages.invoice.school-program.form', ['status' => 'create']);
});

Route::get('school-program/1', function () {
    return view('pages.invoice.school-program.form', ['status' => 'view']);
});

Route::get('school-program/1/edit', function () {
    return view('pages.invoice.school-program.form', ['status' => 'edit']);
});

Route::get('school-program/1/export/pdf', function () {
    return view('pages.invoice.school-program.export.invoice-pdf');
});

// Route::get('school-program/status/{status?}', function ($status = null) {
//     return view('pages.invoice.school-program.index', ['status' => $status]);
// });


// Referral 
Route::get('referral/create', function () {
    return view('pages.invoice.referral.form', ['status' => 'create']);
});

Route::get('referral/1', function () {
    return view('pages.invoice.referral.form', ['status' => 'view']);
});

Route::get('referral/1/edit', function () {
    return view('pages.invoice.referral.form', ['status' => 'edit']);
});

Route::get('referral/1/export/pdf', function () {
    return view('pages.invoice.referral.export.invoice-pdf');
});

Route::get('referral/status/{status?}', function ($status = null) {
    return view('pages.invoice.referral.index', ['status' => $status]);
});

Route::prefix('school-program')->name('invoice-sch.')->group(function () {
    Route::resource('{sch_prog}/detail', InvoiceSchoolController::class)->except(['index']);
    Route::get('status/{status}', [InvoiceSchoolController::class, 'index'])->name('index');
});