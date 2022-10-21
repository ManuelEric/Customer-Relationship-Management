<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolDetailController;
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

// User 
Route::resource('user/volunteer', VolunteerController::class);

Route::prefix('master')->group(function() {

    Route::resource('school', SchoolController::class);
    Route::resource('school/{school}/detail', SchoolDetailController::class);
    // Route::get('school/{school}/detail/create', [SchoolDetailController::class, 'create']);
});
