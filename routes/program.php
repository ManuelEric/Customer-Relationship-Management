<?php

use App\Http\Controllers\ReferralController;
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
Route::resource('referral', ReferralController::class);

Route::get('event', function () {
    return view('pages.program.client-event.index');
});
Route::get('event/create', function () {
    return view('pages.program.client-event.form');
});

//  
Route::get('client', function () {
    return view('pages.program.client-program.index');
});
Route::get('client/create', function () {
    return view('pages.program.client-program.form');
});
Route::get('client/1', function () {
    return view('pages.program.client-program.form');
});
Route::get('client/1/edit', function () {
    return view('pages.program.client-program.form');
});

Route::get('corporate', function () {
    return view('pages.program.corporate-program.index');
});
Route::get('corporate/1', function () {
    return view('pages.program.corporate-program.form');
});

Route::get('school', function () {
    return view('pages.program.school-program.index');
});

Route::get('school/create', function () {
    return view('pages.program.school-program.form');
});

Route::get('school/1', function () {
    return view('pages.program.school-program.form');
});