<?php

use App\Http\Controllers\AcceptanceController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\ClientHotLeadsController;
use App\Http\Controllers\ClientMenteeController;
use App\Http\Controllers\ClientParentController;
use App\Http\Controllers\ClientProgramController;
use App\Http\Controllers\ClientStudentController;
use App\Http\Controllers\ClientTeacherCounselorController;
use App\Http\Controllers\FollowupController;
use App\Models\Axis;
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

Route::get('student/raw',  [ClientStudentController::class, 'indexRaw']);
Route::get('student/raw/{rawclient_id}/{type}/{client_id?}', [ClientStudentController::class, 'cleaningData']);
Route::post('student/raw/{rawclient_id}/{type}/{client_id?}', [ClientStudentController::class, 'convertData'])->name('client.convert.student');
Route::delete('student/raw/{rawclient_id}', [ClientStudentController::class, 'destroyRaw']);
Route::post('delete/bulk/student/raw', [ClientStudentController::class ,'destroyRaw'])->name('client.raw.bulk.destroy');
Route::post('assign/bulk/student/', [ClientStudentController::class ,'assign'])->name('client.bulk.assign');
Route::post('update/pic', [ClientStudentController::class ,'updatePic'])->name('client.update.pic');

Route::resource('student', ClientStudentController::class);
Route::prefix('student')->name('student.')->group(function () {
    Route::post('import', [ClientStudentController::class, 'import'])->name('import');
    Route::get('{student}/status/{status}', [ClientStudentController::class, 'updateStatus'])->name('update.status');
    Route::post('{student}/lead_status', [ClientStudentController::class, 'updateLeadStatus'])->name('update.lead.status');
    Route::post('{student}/interest_program', [ClientStudentController::class, 'addInterestProgram'])->name('add.interest.program');
    Route::delete('{student}/interest_program/{interest_program}/{prog}', [ClientStudentController::class, 'removeInterestProgram'])->name('remove.interest.program');
    Route::post('{student}/parent', [ClientStudentController::class, 'addParent'])->name('add.parent');
    Route::delete('{student}/parent/{parent}', [ClientStudentController::class, 'disconnectParent'])->name('disconnect.parent');

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

// Route::get('teacher-counselor/raw', function () {
//     return view('pages.client.teacher.raw.index');
// });
// Route::get('teacher-counselor/raw/1/comparison/2', function () {
//     return view('pages.client.teacher.raw.form-comparison');
// });
// Route::get('teacher-counselor/raw/1/new', function () {
//     return view('pages.client.teacher.raw.form-new');
// });

Route::get('teacher-counselor/raw',  [ClientTeacherCounselorController::class, 'indexRaw']);
Route::get('teacher-counselor/raw/{rawclient_id}/{type}/{client_id?}', [ClientTeacherCounselorController::class, 'cleaningData']);
Route::post('teacher-counselor/raw/{rawclient_id}/{type}/{client_id?}', [ClientTeacherCounselorController::class, 'convertData'])->name('client.convert.teacher');
Route::delete('teacher-counselor/raw/{rawclient_id}', [ClientTeacherCounselorController::class, 'destroyRaw']);

Route::resource('teacher-counselor', ClientTeacherCounselorController::class);
Route::prefix('teacher-counselor')->name('teacher-counselor.')->group(function () {
    Route::post('import', [ClientTeacherCounselorController::class, 'import'])->name('import');
    Route::get('{teacher}/status/{status}', [ClientTeacherCounselorController::class, 'updateStatus'])->name('update.status');
});


Route::get('parent/raw',  [ClientParentController::class, 'indexRaw']);
Route::get('parent/raw/{rawclient_id}/{type}/{client_id?}', [ClientParentController::class, 'cleaningData']);
Route::post('parent/raw/{rawclient_id}/{type}/{client_id?}', [ClientParentController::class, 'convertData'])->name('client.convert.parent');
Route::delete('parent/raw/{rawclient_id}', [ClientParentController::class, 'destroyRaw']);
Route::delete('parent/{parent}/student/{student}', [ClientParentController::class, 'disconnectStudent'])->name('disconnect.student');

Route::get('parent/inactive', [ClientParentController::class, 'index']);

Route::resource('parent', ClientParentController::class);

Route::post('parent/import', [ClientParentController::class, 'import'])->name('parent.import');

Route::resource('acceptance', AcceptanceController::class)->parameters(['acceptance' => 'client']);

Route::get('hot-leads', [ClientHotLeadsController::class, 'index']);