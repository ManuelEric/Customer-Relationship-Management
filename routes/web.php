<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\VolunteerController;
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


Route::get('/', function () {
    return view('home');
});

Route::get('login', function () {
    return view('auth.login');
});

Route::get('dashboard', function () {
    return view('layout.main');
});

Route::get('vendor/datatables', [VendorController::class, 'data'])->name('vendor.datatables');
Route::resource('vendor', VendorController::class);

Route::resource('volunteer', VolunteerController::class);

Route::get('asset/datatables', [AssetController::class, 'data'])->name('asset.datatables');
Route::resource('asset', AssetController::class);

Route::get('university/datatables', [UniversityController::class, 'data'])->name('university.datatables');
Route::resource('university', UniversityController::class);

Route::prefix('master')->group(function() {

    Route::resource('lead', LeadController::class);
});