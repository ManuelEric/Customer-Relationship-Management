<?php

use App\Http\Controllers\AlumniController;
use App\Http\Controllers\ClientMenteeController;
use App\Http\Controllers\ClientParentController;
use App\Http\Controllers\ClientProgramController;
use App\Http\Controllers\ClientStudentController;
use App\Http\Controllers\ClientTeacherCounselorController;
use App\Http\Controllers\FollowupController;
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

// Route::get('mentee/{status}', function ($status) {
//     return view('pages.client.student.index-mentee', ['status' => $status]);
// });

Route::resource('student', ClientStudentController::class);
Route::prefix('student')->name('student.')->group(function () {
    Route::post('import', [ClientStudentController::class, 'import'])->name('import');
    Route::get('{student}/status/{status}', [ClientStudentController::class, 'updateStatus'])->name('update.status');
    Route::post('{student}/lead_status', [ClientStudentController::class, 'updateLeadStatus'])->name('update.lead.status');

    Route::resource('{student}/program', ClientProgramController::class);
    Route::prefix('{student}/program')->name('program.')->group(function () {

        Route::resource('{program}/followup', FollowupController::class);
    });
});
Route::prefix('alumni')->group(function() {

    Route::resource('mentee', ClientMenteeController::class);
    Route::resource('non-mentee', ClientMenteeController::class);
});
Route::resource('alumni', ClientMenteeController::class);

Route::resource('teacher-counselor', ClientTeacherCounselorController::class);
Route::prefix('teacher-counselor')->name('teacher-counselor.')->group(function () {
    Route::post('import', [ClientTeacherCounselorController::class, 'import'])->name('import');
    Route::get('{teacher}/status/{status}', [ClientTeacherCounselorController::class, 'updateStatus'])->name('update.status');
});

Route::resource('parent', ClientParentController::class);
Route::post('parent/import', [ClientParentController::class, 'import'])->name('parent.import');
