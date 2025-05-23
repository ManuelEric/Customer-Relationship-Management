<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Embed\PublicRegistrationController;
use App\Http\Controllers\ClientEventController;
use App\Http\Controllers\ClientProgramController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VolunteerController;
use App\Jobs\JobCoba;
use App\Models\UserClient;
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

# AUTH START --------------------------------

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('auth.login');
    });
    
    Route::get('404', function () {
        return view('auth.404');
    })->name('auth.404');
    
    Route::get('login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('auth/login', [AuthController::class, 'login'])->name('login.action');
    Route::get('login/expired', [AuthController::class, 'logoutFromExpirationTime'])->name('logout.expiration');
});



Route::group(['middleware' => ['auth', 'auth.department']], function () {
    Route::get('auth/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('dashboard/{division}', [DashboardController::class, 'index'])->name('index');

    Route::get('import', function () {
        return view('pages.import.index');
    });
});

# AUTH END ------------------------------------


# FORM EVENT EMBED START ------------------------

Route::get('form/event', [ClientEventController::class, 'fnCreateFormEmbed'])->name('form.event.create');
Route::post('form/events', [ClientEventController::class, 'storeFormEmbed'])->name('form.event.store');

// Route::get('form/event/{event_slug}/client/attend/{clientevent}', [ClientEventController::class, 'handlerScanQrCodeForAttend'])->name('link-event-attend');
Route::put('form/event/attend/{clientevent}', [ClientEventController::class, 'handlerScanQrCodeForAttend'])->name('link-event-attend')->withoutMiddleware(['auth', 'auth.department']);

Route::get('form/program', [ClientProgramController::class, 'fnCreateFormEmbed']);
Route::post('form/program', [ClientProgramController::class, 'storeFormEmbed'])->withoutMiddleware(['auth', 'auth.department']);

Route::get('form/registration', [PublicRegistrationController::class, 'register']);
Route::post('form/registrations', [PublicRegistrationController::class, 'store'])->name('submit.registration');

Route::get('form/thanks', function () {
    return view('form-embed.thanks');
})->name('form.event.registration.success');

Route::get('form/registration/success', [ClientEventController::class, 'successPage']);

Route::get('form/already-join', [ClientEventController::class, 'alreadyJoinPage']);

Route::get('registration', function () {
    return view('stem-wonderlab.registration');
});

Route::get('onsite', function () {
    return view('stem-wonderlab.onsite');
});

Route::get('scan', function () {
    return view('stem-wonderlab.scan-qrcode.index');
});

Route::get('client-detail/{identifier}/{screening_type}', [ClientEventController::class, 'previewClientInformation']);

Route::get('mailing', function () {
    return view('mailing.mailing-event');
});

Route::get('sample/form', function () {
    return view('form-embed.form-sample');
});

# FORM EVENT EMBED END --------------------------------


// User 
Route::resource('user/volunteer', VolunteerController::class);

# PROFILE START ---------------------------------------

Route::resource('profile', ProfileController::class);

# PROFILE END -----------------------------------------
