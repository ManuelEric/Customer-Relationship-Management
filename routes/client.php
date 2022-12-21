<?php

use App\Http\Controllers\ClientParentController;
use App\Http\Controllers\ClientProgramController;
use App\Http\Controllers\ClientStudentController;
use App\Http\Controllers\ClientTeacherCounselorController;
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
// Route::get('student/create', function () {
//     return view('pages.client.student.form');
// });

// Route::get('student/1', function () {
//     return view('pages.client.student.view');
// });

// Route::get('student/1/edit', function () {
//     return view('pages.client.student.form');
// });

// Route::get('student/{status}', function ($status) {
//     return view('pages.client.student.index', ['status' => $status]);
// });

Route::get('mentee/{status}', function ($status) {
    return view('pages.client.student.index-mentee', ['status' => $status]);
});

Route::resource('student', ClientStudentController::class);
Route::prefix('student')->name('student.')->group(function () {
    Route::get('{student}/status/{status}', [ClientStudentController::class, 'updateStatus'])->name('update.status');
    Route::resource('{student}/program', ClientProgramController::class);
});
Route::resource('teacher-counselor', ClientTeacherCounselorController::class);
Route::resource('parent', ClientParentController::class);