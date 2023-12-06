<?php

use App\Http\Controllers\ReceiptController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReceiptSchoolController;
use App\Http\Controllers\ReceiptReferralController;
use App\Http\Controllers\ReceiptPartnerController;
use App\Http\Controllers\RecycleController;
use App\Http\Controllers\RefundPartnerController;
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

# only for client
Route::resource('client/{target}', RecycleController::class, [
    'parameters' => [
        '{target}' => 'client'
    ]
]);
Route::put('client/restore/{target}/{client}', [RecycleController::class, 'restore']);