<?php

use App\Http\Controllers\ReceiptController;
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

Route::prefix('client-program')->name('receipt.client-program.')->group(function() {
    Route::get('{receipt}/export', [ReceiptController::class, 'export'])->name('export'); 
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
Route::get('school-program/', function () {
    return view('pages.receipt.school-program.index');
});

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