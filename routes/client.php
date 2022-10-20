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

// User 
Route::get('mentee/create', function () {
    return view('pages.client.mentee.form');
});

Route::get('mentee/1', function () {
    return view('pages.client.mentee.view');
});

Route::get('mentee/{status}', function ($status) {
    return view('pages.client.mentee.index', ['status' => $status]);
});



Route::get('parent', function () {
    return view('pages.client.parent.index');
});

Route::get('parent/create', function () {
    return view('pages.client.parent.create');
});