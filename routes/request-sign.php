<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\RequestSignController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Menus Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [RequestSignController::class, 'index']);