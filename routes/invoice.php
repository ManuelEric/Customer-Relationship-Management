<?php

use App\Http\Controllers\InvoiceProgramController;
use App\Http\Controllers\InvoiceSchoolController;
use App\Http\Controllers\RefundSchoolController;
use App\Http\Controllers\RefundPartnerController;
use App\Http\Controllers\InvoicePartnerController;
use App\Http\Controllers\InvoiceProgramBundleController;
use App\Http\Controllers\InvoiceReferralController;
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

// Route::get('view/b2c/1', function () {
//     return view('pages.invoice.view-pdf');
// });

// Route::get('sign/b2c/1', function () {
//     return view('pages.invoice.sign-pdf');
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

Route::prefix('client-program')->name('invoice.client-program.')->group(function () {
    Route::post('{client_program}/remind/by/email', [InvoiceProgramController::class, 'remindParentsByEmail']);
    Route::post('{client_program}/remind/by/whatsapp', [InvoiceProgramController::class, 'remindParentsByWhatsapp']);

    Route::get('{client_program}/print/{currency}', [InvoiceProgramController::class, 'print'])->name('print');
    Route::get('{client_program}/preview/{currency}', [InvoiceProgramController::class, 'preview'])->name('preview'); # new 
    Route::post('{client_program}/preview/{currency}', [InvoiceProgramController::class, 'upload'])->name('upload-signed'); # new
    Route::get('{client_program}/export/{currency?}', [InvoiceProgramController::class, 'export'])->name('export');
    Route::post('{client_program}/refund', [RefundController::class, 'store'])->name('refund');
    Route::delete('{client_program}/refund', [RefundController::class, 'destroy'])->name('refund.destroy');
    Route::get('{client_program}/request_sign', [InvoiceProgramController::class, 'requestSign'])->name('request_sign');
    Route::get('{client_program}/upload', [InvoiceProgramController::class, 'createSignedAttachment'])->name('create_signed_document');
    Route::post('{client_program}/upload', [InvoiceProgramController::class, 'storeSignedAttachment'])->name('upload_signed_document');
    Route::get('{client_program}/send/{currency}/{type_recipient}', [InvoiceProgramController::class, 'sendToClient'])->name('send_to_client');
    Route::get('{client_program}/attachment/download', [InvoiceProgramController::class, 'download'])->name('download');
    Route::post('{client_program}/update/mail', [InvoiceProgramController::class, 'updateMail']);

    // ========== Bundling =============
    Route::post('bundle/{bundle}', [InvoiceProgramBundleController::class, 'storeBundle'])->name('store_bundle');
    Route::put('bundle/{bundle}', [InvoiceProgramBundleController::class, 'updateBundle'])->name('update_bundle');
    Route::delete('bundle/{bundle}', [InvoiceProgramBundleController::class, 'destroyBundle'])->name('destroy_bundle');
    Route::get('bundle/{bundle}', [InvoiceProgramBundleController::class, 'showBundle'])->name('show_bundle');
    Route::get('bundle/{bundle}/edit', [InvoiceProgramBundleController::class, 'editBundle'])->name('edit_bundle');
    Route::get('bundle/{bundle}/preview/{currency}', [InvoiceProgramBundleController::class, 'previewBundle'])->name('preview_bundle'); # new 
    Route::get('bundle/{bundle}/request_sign', [InvoiceProgramBundleController::class, 'requestSignBundle'])->name('request_sign_bundle'); 
    Route::post('bundle/{bundle}/preview/{currency}', [InvoiceProgramBundleController::class, 'upload'])->name('upload-signed-bundle'); # new
    Route::get('bundle/{bundle}/print/{currency}', [InvoiceProgramBundleController::class, 'printBundle'])->name('print_bundle');
    Route::get('bundle/{bundle}/send/{currency}/{type_recipient}', [InvoiceProgramBundleController::class, 'sendToClientBundle'])->name('send_to_client_bundle');
    Route::post('bundle/{bundle}/remind/by/whatsapp', [InvoiceProgramBundleController::class, 'remindParentsByWhatsapp']);

});


// PARTNER 
// Route::get('corporate-program/create', function () {
//     return view('pages.invoice.corporate-program.form', ['status' => 'create']);
// });

// Route::get('corporate-program/1', function () {
//     return view('pages.invoice.corporate-program.form', ['status' => 'view']);
// });

// Route::get('corporate-program/1/edit', function () {
//     return view('pages.invoice.corporate-program.form', ['status' => 'edit']);
// });

// Route::get('corporate-program/1/export/pdf', function () {
//     return view('pages.invoice.corporate-program.export.invoice-pdf');
// });

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


Route::prefix('school-program')->name('invoice-sch.')->group(function () {
    Route::resource('{sch_prog}/detail', InvoiceSchoolController::class)->except(['index']);
    Route::get('status/{status}', [InvoiceSchoolController::class, 'index'])->name('index');
    Route::get('{invoice}/export/{currency}', [InvoiceSchoolController::class, 'export'])->name('export');
    Route::get('{invoice}/request_sign/{currency}', [InvoiceSchoolController::class, 'requestSign'])->name('request_sign');
    Route::get('{invoice}/sign/{currency}', [InvoiceSchoolController::class, 'signAttachment'])->name('sign_document');
    Route::get('{invoice}/send/{currency}', [InvoiceSchoolController::class, 'sendToClient'])->name('send_to_client');
    Route::get('{invoice}/preview/{currency}', [InvoiceSchoolController::class, 'previewPdf'])->name('preview_pdf');
    Route::post('{invoice}/refund', [RefundSchoolController::class, 'store'])->name('refund');
    Route::delete('{invoice}/refund/{refund}', [RefundSchoolController::class, 'destroy'])->name('refund.destroy');
});

Route::prefix('referral')->name('invoice-ref.')->group(function () {
    Route::resource('{referral}/detail', InvoiceReferralController::class)->except(['index']);
    Route::get('status/{status}', [InvoiceReferralController::class, 'index'])->name('index');
    Route::get('{invoice}/export/{currency}', [InvoiceReferralController::class, 'export'])->name('export');
    Route::get('{invoice}/request_sign/{currency}', [InvoiceReferralController::class, 'requestSign'])->name('request_sign');
    Route::get('{invoice}/sign/{currency}', [InvoiceReferralController::class, 'signAttachment'])->name('sign_document');
    Route::get('{invoice}/send/{currency}', [InvoiceReferralController::class, 'sendToClient'])->name('send_to_client');
    Route::get('{invoice}/preview/{currency}', [InvoiceReferralController::class, 'previewPdf'])->name('preview_pdf');
    // Route::post('{invoice}/refund', [RefundSchoolController::class, 'store'])->name('refund');
    // Route::delete('{invoice}/refund/{refund}', [RefundSchoolController::class, 'destroy'])->name('refund.destroy');
});

// Route::get('sign/b2c/1', function () {
//     return view('pages.invoice.sign-pdf');
// });

Route::prefix('corporate-program')->name('invoice-corp.')->group(function () {
    Route::resource('{corp_prog}/detail', InvoicePartnerController::class)->except(['index']);
    Route::get('status/{status}', [InvoicePartnerController::class, 'index'])->name('index');
    Route::get('{invoice}/export/{currency}', [InvoicePartnerController::class, 'export'])->name('export');
    Route::get('{invoice}/request_sign/{currency}', [InvoicePartnerController::class, 'requestSign'])->name('request_sign');
    Route::get('{invoice}/sign/{currency}', [InvoicePartnerController::class, 'signAttachment'])->name('sign_document');
    Route::get('{invoice}/send/{currency}', [InvoicePartnerController::class, 'sendToClient'])->name('send_to_client');
    Route::get('{invoice}/preview/{currency}', [InvoicePartnerController::class, 'previewPdf'])->name('preview_pdf');
    Route::post('{invoice}/refund', [RefundPartnerController::class, 'store'])->name('refund');
    Route::delete('{invoice}/refund/{refund}', [RefundPartnerController::class, 'destroy'])->name('refund.destroy');
});

Route::get('refund/status/{status}', [RefundController::class, 'index'])->name('refund.index');
