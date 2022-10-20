<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\SchoolController;
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

// Route::get('master/program', function () {
//     return view('pages.program.index');
// });
// Route::get('master/program/create', function () {
//     return view('pages.program.form');
// });

// Route::get('master/lead', function () {
//     return view('pages.lead.index');
// });
// Route::get('master/lead/create', function () {
//     return view('pages.lead.form');
// });

// Route::get('master/department', function () {
//     return view('pages.department.index');
// });

// Route::get('master/major', function () {
//     return view('pages.major.index');
// });

// Route::get('master/purchase', function () {
//     return view('pages.purchase.index');
// });
// Route::get('master/purchase/create', function () {
//     return view('pages.purchase.form');
// });

// Route::resource('master/vendor', VendorController::class);

// Route::resource('master/asset', AssetController::class);

// Route::resource('master/university', UniversityController::class);

// User 
Route::resource('user/volunteer', VolunteerController::class);

Route::prefix('master')->group(function() {

    Route::resource('lead', LeadController::class);
    Route::resource('school', SchoolController::class);
});
