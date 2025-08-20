<?php

use App\Http\Controllers\RecycleClientController;
use App\Http\Controllers\RecycleInstanceController;
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

/* RESTORE CLIENT */
Route::put('client/{target}/{client}', [RecycleClientController::class, 'restore']);

/* RESTORE INSTANCE */
Route::put('instance/{target}/{instance}', [RecycleInstanceController::class, 'restore']);
