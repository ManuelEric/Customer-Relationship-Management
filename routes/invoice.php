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

// Invoice 
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