<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetReturnedController;
use App\Http\Controllers\AssetUsedController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\PurchaseDetailController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\VendorController;
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

Route::get('program/sub_program/{main_program}', [ProgramController::class, 'getSubProgram']);
Route::resource('program', ProgramController::class);

Route::resource('lead', LeadController::class);

Route::resource('position', PositionController::class);

Route::resource('major', MajorController::class);

Route::resource('purchase', PurchaseRequestController::class);
Route::prefix('purchase')->name('purchase.')->group(function () {
    Route::get('download/{file_name}', [PurchaseRequestController::class, 'download'])->name('download');
    Route::get('{purchase}/print', [PurchaseRequestController::class, 'print'])->name('print');
    Route::resource('{purchase}/detail', PurchaseDetailController::class);
});

Route::resource('vendor', VendorController::class);

Route::resource('asset', AssetController::class);
Route::resource('asset/{asset}/used', AssetUsedController::class);
Route::resource('asset/{asset}/used/{used}/returned', AssetReturnedController::class);
// Route::delete('asset/{asset}/detail', [AssetUsedController::class, 'destroy']);
// Route::post('asset/{asset}/detail', [AssetUsedController::class, 'store']);
// Route::delete('asset/{asset}/{user}', [AssetUsedController::class, 'destroy']);

// Route::get('asset/{asset}/{user}/edit', [AssetUsedController::class, 'edit']);
// Route::post('asset/{asset}/{user}', [AssetReturnedController::class, 'store']);