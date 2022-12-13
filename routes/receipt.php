<?php

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
Route::get('client-program/', function () {
    return view('pages.receipt.client-program.index');
});

Route::get('client-program/1', function () {
    return view('pages.receipt.client-program.form');
});

Route::get('client-program/1/export/pdf', function () {
    return view('pages.receipt.client-program.export.invoice-pdf');
});