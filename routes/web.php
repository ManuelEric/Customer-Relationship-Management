<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolDetailController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Embed\PublicRegistrationController;
use App\Http\Controllers\ClientEventController;
use App\Http\Controllers\ClientProgramController;
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


Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');

Route::get('404', function () {
    return view('auth.404');
})->name('auth.404');

Route::get('login', function () {
    return view('auth.login');
})->middleware('guest')->name('login');

Route::post('auth/login', [AuthController::class, 'login'])->name('login.action');
Route::get('login/expired', [AuthController::class, 'logoutFromExpirationTime'])->name('logout.expiration');

Route::group(['middleware' => ['auth', 'auth.department']], function () {
    Route::get('auth/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('index');
});

// User 
Route::resource('user/volunteer', VolunteerController::class);

// Form 
// Route::get('form/event', function() {
//     return view('form-embed.form-event');
// });
Route::get('form/event', [ClientEventController::class, 'createFormEmbed']);
Route::post('form/events', [ClientEventController::class, 'storeFormEmbed']);

// Route::get('form/event/{event_slug}/client/attend/{clientevent}', [ClientEventController::class, 'handlerScanQrCodeForAttend'])->name('link-event-attend');
Route::put('form/event/attend/{clientevent}', [ClientEventController::class, 'handlerScanQrCodeForAttend'])->name('link-event-attend');

Route::get('form/program', [ClientProgramController::class, 'createFormEmbed']);
Route::post('form/program', [ClientProgramController::class, 'storeFormEmbed']);

Route::get('form/registration', [PublicRegistrationController::class, 'register']);
Route::post('form/registrations', [PublicRegistrationController::class, 'store'])->name('submit.registration');

Route::get('form/thanks', function() {
    return view('form-embed.thanks');
});

Route::get('form/already-join', function() {
    return view('form-embed.response.already-join');
});

Route::get('scan', function() {
    return view('scan-qrcode.index');
});

Route::get('client-detail/{clientevent}', [ClientEventController::class, 'previewClientInformation']);

Route::get('mailing', function() {
    return view('mailing.stem-wonderlab');
});