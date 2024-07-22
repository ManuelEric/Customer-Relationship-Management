<?php

use App\Http\Controllers\UserController;
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

Route::resource('{user_role}', UserController::class, [
    'names' => [
        'index' => 'user.index',
        'store' => 'user.store',
        'create' => 'user.create',
        'show' => 'user.show',
        'update' => 'user.update',
        'edit' => 'user.edit',
        'destroy' => 'user.destroy',
    ]
])->parameters([
    '{user_role}' => 'user'
]);
Route::prefix('{user_role}/{user}')->name('user.')->group(function () {
    Route::get('download/{filetype}', [UserController::class, 'download'])->name('file.download');
    Route::get('download_agreement/{user_subject}', [UserController::class, 'downloadAgreement'])->name('file.download.agreement');
    Route::get('set_password', [UserController::class, 'setPassword'])->name('set.password');
    Route::post('update/status', [UserController::class, 'changeStatus'])->name('update.status');
    Route::delete('{user_type}', [UserController::class, 'destroyUserType'])->name('type.destroy');
});

Route::resource('volunteer', VolunteerController::class);
Route::prefix('volunteer')->name('volunteer.')->group(function () {
    Route::get('{volunteer}/download/file/{filetype}', [VolunteerController::class, 'download'])->name('file.download');
    Route::post('{volunteer}/update/volunteer/status', [VolunteerController::class, 'changeStatus'])->name('update.status');
});
