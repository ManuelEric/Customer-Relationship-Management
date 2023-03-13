<?php

use App\Http\Controllers\MenuController;
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

Route::get('/', [MenuController::class, 'index']);
Route::post('manage/department/access', [MenuController::class, 'updateDepartmentAccess']);
Route::post('manage/user/access', [MenuController::class, 'updateUserAccess']);