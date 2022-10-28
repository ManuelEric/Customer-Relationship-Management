<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetReturnedController;
use App\Http\Controllers\AssetUsedController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\UniversityController;
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


Route::get('program', function () {
    return view('pages.program.index');
});
Route::get('program/create', function () {
    return view('pages.program.form');
});

Route::resource('lead', LeadController::class);

Route::resource('department', DepartmentController::class);

Route::resource('major', MajorController::class);

Route::get('purchase', function () {
    return view('pages.purchase.index');
});
Route::get('purchase/create', function () {
    return view('pages.purchase.form');
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

Route::resource('university', UniversityController::class);