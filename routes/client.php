<?php

use App\Http\Controllers\ClientStudentController;
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
    
});

// Parent 
Route::get('parent', function () {
    return view('pages.client.parent.index');
});

Route::get('parent/1', function () {
    return view('pages.client.parent.view');
});

Route::get('parent/create', function () {
    return view('pages.client.parent.form');
});

Route::get('parent/1/edit', function () {
    return view('pages.client.parent.form');
});


// Teacher
Route::get('teacher', function () {
    return view('pages.client.teacher.index');
});

Route::get('teacher/1', function () {
    return view('pages.client.teacher.view');
});

Route::get('teacher/create', function () {
    return view('pages.client.teacher.form');
});

Route::get('teacher/1/edit', function () {
    return view('pages.client.teacher.form');
});