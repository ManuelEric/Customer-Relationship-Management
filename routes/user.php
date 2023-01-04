<?php

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

// User 

Route::get('employee', function () {
    return view('pages.user.employee.index');
});
Route::get('mentor', function () {
    return view('pages.user.employee.index');
});
Route::get('editor', function () {
    return view('pages.user.employee.index');
});
Route::get('tutor', function () {
    return view('pages.user.employee.index');
});

Route::get('employee/create', function () {
    return view('pages.user.employee.form');
});

Route::resource('volunteer', VolunteerController::class);