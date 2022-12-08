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

// CLIENT 
Route::get('client-program/create', function () {
    return view('pages.invoice.client-program.form', ['status' => 'create']);
});

Route::get('client-program/1', function () {
    return view('pages.invoice.client-program.form', ['status' => 'view']);
});

Route::get('client-program/1/edit', function () {
    return view('pages.invoice.client-program.form', ['status' => 'edit']);
});

Route::get('client-program/status/{status?}', function ($status = null) {
    return view('pages.invoice.client-program.index', ['status' => $status]);
});


// PARTNER 
Route::get('corporate-program/create', function () {
    return view('pages.invoice.corporate-program.form', ['status' => 'create']);
});

Route::get('corporate-program/1', function () {
    return view('pages.invoice.corporate-program.form', ['status' => 'view']);
});

Route::get('corporate-program/1/edit', function () {
    return view('pages.invoice.corporate-program.form', ['status' => 'edit']);
});

Route::get('corporate-program/status/{status?}', function ($status = null) {
    return view('pages.invoice.corporate-program.index', ['status' => $status]);
});


// SCHOOL 
Route::get('school-program/create', function () {
    return view('pages.invoice.school-program.form', ['status' => 'create']);
});

Route::get('school-program/1', function () {
    return view('pages.invoice.school-program.form', ['status' => 'view']);
});

Route::get('school-program/1/edit', function () {
    return view('pages.invoice.school-program.form', ['status' => 'edit']);
});

Route::get('school-program/status/{status?}', function ($status = null) {
    return view('pages.invoice.school-program.index', ['status' => $status]);
});


// Referral 
Route::get('referral/create', function () {
    return view('pages.invoice.referral.form', ['status' => 'create']);
});

Route::get('referral/1', function () {
    return view('pages.invoice.referral.form', ['status' => 'view']);
});

Route::get('referral/1/edit', function () {
    return view('pages.invoice.referral.form', ['status' => 'edit']);
});

Route::get('referral/status/{status?}', function ($status = null) {
    return view('pages.invoice.referral.index', ['status' => $status]);
});