<?php

use App\Http\Controllers\ReceiptController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReceiptSchoolController;
use App\Http\Controllers\ReceiptPartnerController;
use App\Http\Controllers\RefundSchoolController;

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
// Route::get('client-program/', function () {
//     return view('pages.receipt.client-program.index');
// });

// Route::get('client-program/1', function () {
//     return view('pages.receipt.client-program.form');
// });

// Route::get('client-program/1/export/pdf', function () {
//     return view('pages.receipt.client-program.export.receipt-pdf');
// });

Route::resource('client-program', ReceiptController::class, [
    'names' => [
        'index' => 'receipt.client-program',
        'store' => 'receipt.client-program.store',
        'create' => 'receipt.client-program.create',
        'show' => 'receipt.client-program.show',
        'edit' => 'receipt.client-program.edit',
        'update' => 'receipt.client-program.update',
        'destroy' => 'receipt.client-program.destroy'
    ]
])->parameters(['client-program' => 'receipt']);

Route::prefix('client-program')->name('receipt.client-program.')->group(function () {
    Route::get('{receipt}/print/{currency}', [ReceiptController::class, 'print'])->name('print');
    Route::get('{receipt}/export', [ReceiptController::class, 'export'])->name('export');
    Route::post('{receipt}/upload', [ReceiptController::class, 'upload'])->name('upload');
    Route::get('{receipt}/request_sign', [ReceiptController::class, 'requestSign'])->name('request_sign');
    Route::get('{receipt}/preview/{currency}', [ReceiptController::class, 'preview'])->name('preview'); # new 
    Route::post('{receipt}/preview/{currency}', [ReceiptController::class, 'uploadSigned'])->name('upload-signed'); # new
    Route::get('{receipt}/send/{currency}', [ReceiptController::class, 'sendToClient'])->name('send_to_client'); # new
});

// CORPORATE 
Route::get('corporate-program/', function () {
    return view('pages.receipt.corporate-program.index');
});

Route::get('corporate-program/1', function () {
    return view('pages.receipt.corporate-program.form');
});

Route::get('corporate-program/1/export/pdf', function () {
    return view('pages.receipt.corporate-program.export.receipt-pdf');
});

// school 

Route::prefix('school-program')->name('receipt.school.')->group(function () {
    Route::get('/', [ReceiptSchoolController::class, 'index'])->name('index');
    Route::get('{detail}', [ReceiptSchoolController::class, 'show'])->name('show');
    Route::delete('{detail}', [ReceiptSchoolController::class, 'destroy'])->name('destroy');
    Route::post('/{invoice}', [ReceiptSchoolController::class, 'store'])->name('store');
    Route::get('{receipt}/export/{currency}', [ReceiptSchoolController::class, 'export'])->name('export');
    Route::post('{receipt}/upload', [ReceiptSchoolController::class, 'upload'])->name('upload');
    Route::get('{receipt}/request_sign/{currency}', [ReceiptSchoolController::class, 'requestSign'])->name('request_sign');
    Route::get('{receipt}/sign/{currency}', [ReceiptSchoolController::class, 'signAttachment'])->name('sign_document');
    Route::get('{receipt}/print/{currency}', [ReceiptSchoolController::class, 'print'])->name('print');
    Route::get('{receipt}/send/{currency}', [ReceiptSchoolController::class, 'sendToClient'])->name('send_to_client');
    Route::get('{receipt}/refund', [RefundSchoolController::class, 'refund'])->name('refund');
});

Route::prefix('corporate-program')->name('receipt.corporate.')->group(function () {
    Route::get('/', [ReceiptPartnerController::class, 'index'])->name('index');
    Route::get('{detail}', [ReceiptPartnerController::class, 'show'])->name('show');
    Route::delete('{detail}', [ReceiptPartnerController::class, 'destroy'])->name('destroy');
    Route::post('/{invoice}', [ReceiptPartnerController::class, 'store'])->name('store');
    Route::get('{receipt}/export/{currency}', [ReceiptPartnerController::class, 'export'])->name('export');
    // Route::get('{receipt}/refund', [RefundSchoolController::class, 'refund'])->name('refund');
});

// Route::get('school-program/', function () {
//     return view('pages.receipt.school-program.index');
// });

Route::get('school-program/1', function () {
    return view('pages.receipt.school-program.form');
});

Route::get('school-program/1/export/pdf', function () {
    return view('pages.receipt.school-program.export.receipt-pdf');
});

// referral 
Route::get('referral/', function () {
    return view('pages.receipt.referral.index');
});

Route::get('referral/1', function () {
    return view('pages.receipt.referral.form');
});

Route::get('referral/1/export/pdf', function () {
    return view('pages.receipt.referral.export.receipt-pdf');
});
