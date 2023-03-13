<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolDetailController;
use App\Http\Controllers\AuthController;
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

Route::post('auth/login', [AuthController::class, 'login'])->name('login.action');
Route::get('auth/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('dashboard', [DashboardController::class, 'index'])->name('index');

// User 
Route::resource('user/volunteer', VolunteerController::class);
