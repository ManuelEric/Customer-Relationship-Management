<?php

use App\Http\Controllers\InvoiceProgramController;
use App\Http\Controllers\InvoiceSchoolController;
use App\Http\Controllers\RefundSchoolController;
use App\Http\Controllers\RefundPartnerController;
use App\Http\Controllers\InvoicePartnerController;
use App\Http\Controllers\RefundController;
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

Route::get('view/b2c/1', function () {
    return view('pages.invoice.view-pdf');
});

Route::get('sign/b2c/1', function () {
    return view('pages.invoice.sign-pdf');
});

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

Route::prefix('client-program')->name('invoice.program.')->group(function () {
    Route::get('{client_program}/preview/{currency}', [InvoiceProgramController::class, 'preview'])->name('preview'); # new 
    Route::post('{client_program}/preview/{currency}', [InvoiceProgramController::class, 'upload'])->name('upload-signed'); # new
    Route::get('{client_program}/export', [InvoiceProgramController::class, 'export'])->name('export');
    Route::post('{client_program}/refund', [RefundController::class, 'store'])->name('refund');
    Route::delete('{client_program}/refund', [RefundController::class, 'destroy'])->name('destroy');
    Route::get('{client_program}/request_sign', [InvoiceProgramController::class, 'requestSign'])->name('request_sign');
    Route::get('{client_program}/upload', [InvoiceProgramController::class, 'createSignedAttachment'])->name('create_signed_document');
    Route::post('{client_program}/upload', [InvoiceProgramController::class, 'storeSignedAttachment'])->name('upload_signed_document');
    Route::get('{client_program}/send', [InvoiceProgramController::class, 'sendToClient'])->name('send_to_client');
    Route::get('{client_program}/attachment/download', [InvoiceProgramController::class, 'download'])->name('download');
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

// Route::get('corporate-program/status/{status?}', function ($status = null) {
//     return view('pages.invoice.corporate-program.index', ['status' => $status]);
// });



// SCHOOL 
// Route::get('school-program/create', function () {
//     return view('pages.invoice.school-program.form', ['status' => 'create']);
// });

// Route::get('school-program/1', function () {
//     return view('pages.invoice.school-program.form', ['status' => 'view']);
// });

// Route::get('school-program/1/edit', function () {
//     return view('pages.invoice.school-program.form', ['status' => 'edit']);
// });

// Route::get('school-program/1/export/pdf', function () {
//     return view('pages.invoice.school-program.export.invoice-pdf');
// });

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
    Route::get('{invoice}/export/{currency}', [InvoiceSchoolController::class, 'export'])->name('export');
    Route::get('{invoice}/request_sign/{currency}', [InvoiceSchoolController::class, 'requestSign'])->name('request_sign');
    Route::get('{invoice}/sign/{currency}', [InvoiceSchoolController::class, 'signAttachment'])->name('sign_document');
    Route::get('{invoice}/send/{currency}', [InvoiceSchoolController::class, 'sendToClient'])->name('send_to_client');
    Route::post('{invoice}/refund', [RefundSchoolController::class, 'store'])->name('refund');
    Route::delete('{invoice}/refund/{refund}', [RefundSchoolController::class, 'destroy'])->name('refund.destroy');
});

// Route::get('sign/b2c/1', function () {
//     return view('pages.invoice.sign-pdf');
// });

Route::prefix('corporate-program')->name('invoice-corp.')->group(function () {
    Route::resource('{corp_prog}/detail', InvoicePartnerController::class)->except(['index']);
    Route::get('status/{status}', [InvoicePartnerController::class, 'index'])->name('index');
    // Route::get('{invoice}/export/{currency}', [InvoiceSchoolController::class, 'export'])->name('export');
    // Route::get('{invoice}/request_sign/{currency}', [InvoiceSchoolController::class, 'requestSign'])->name('request_sign');
    // Route::get('{invoice}/upload', [InvoiceSchoolController::class, 'createSignedAttachment'])->name('create_signed_document');
    // Route::post('{invoice}/upload', [InvoiceSchoolController::class, 'storeSignedAttachment'])->name('upload_signed_document');
    // Route::get('{invoice}/send', [InvoiceSchoolController::class, 'sendToClient'])->name('send_to_client');
    // Route::get('{invoice}/attachment/download', [InvoiceSchoolController::class, 'download'])->name('download');
    Route::post('{invoice}/refund', [RefundPartnerController::class, 'store'])->name('refund');
    Route::delete('{invoice}/refund/{refund}', [RefundPartnerController::class, 'destroy'])->name('refund.destroy');
});

Route::get('refund/status/{status}', [RefundController::class, 'index'])->name('refund.index');
