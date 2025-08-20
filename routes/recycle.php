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

// route for client
Route::resource('client/{target}', RecycleClientController::class, [
    'names' => [
        'index' => 'recycle.client.index',
        'store' => 'recycle.client.store',
        'create' => 'recycle.client.create',
        'show' => 'recycle.client.show',
        'update' => 'recycle.client.update',
        'edit' => 'recycle.client.edit',
        'destroy' => 'recycle.client.destroy',
    ],
    'parameters' => [
        '{target}' => 'client',
    ],
])->parameters(['{target}' => 'client']);

// route for instance
Route::resource('instance/{target}', RecycleInstanceController::class, [
    'names' => [
        'index' => 'recycle.instance.index',
        'store' => 'recycle.instance.store',
        'create' => 'recycle.instance.create',
        'show' => 'recycle.instance.show',
        'update' => 'recycle.instance.update',
        'edit' => 'recycle.instance.edit',
        'destroy' => 'recycle.instance.destroy',
    ],
    'parameters' => [
        '{target}' => 'instance',
    ],
])->parameters(['{target}' => 'instance']);
