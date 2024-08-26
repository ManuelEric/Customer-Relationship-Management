<?php

use App\Http\Controllers\ClientProgramController;
use App\Http\Controllers\ReferralController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientEventController;
use App\Http\Controllers\SchoolProgramController;
use App\Http\Controllers\SchoolProgramSpeakerController;
use App\Http\Controllers\SchoolProgramAttachController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\PartnerProgramController;
use App\Http\Controllers\PartnerProgramAttachController;
use App\Http\Controllers\PartnerProgramCollaboratorsController;
use App\Http\Controllers\PartnerProgramSpeakerController;
use App\Http\Controllers\SchoolDetailController;
use App\Http\Controllers\SchoolProgramCollaboratorsController;

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

Route::resource('referral', ReferralController::class);

// Route::get('event', function () {
//     return view('pages.program.client-event.index');
// });
Route::get('event/create', function () {
    return view('pages.program.client-event.form');
});

//  
// Route::get('client', function () {
//     return view('pages.program.client-program.index');
// });
// Route::get('client/create', function () {
//     return view('pages.program.client-program.form');
// });
// Route::get('client/1', function () {
//     return view('pages.program.client-program.form');
// });
// Route::get('client/1/edit', function () {
//     return view('pages.program.client-program.form');
// });

Route::resource('client', ClientProgramController::class);
Route::post('client/bundle', [ClientProgramController::class ,'addBundleProgram'])->name('program.client.bundle');
Route::post('client/deleteBundle', [ClientProgramController::class ,'cancelBundleProgram'])->name('program.client.bundle.destroy');


Route::get('corporate', function () {
    return view('pages.program.corporate-program.index');
});

Route::resource('event', ClientEventController::class, [
    'names' => [
        'index' => 'program.event.index',
        'store' => 'program.event.store',
        'create' => 'program.event.create',
        'show' => 'program.event.show',
        'update' => 'program.event.update',
        'edit' => 'program.event.edit',
        'destroy' => 'program.event.destroy',
    ]
]);

Route::post('event/import', [ClientEventController::class, 'import'])->name('program.event.import');
Route::post('event/{type}/import', [ClientEventController::class, 'mailing'])->name('program.event.mailing');
Route::get('event/reg-exp/{client}/{event}/{notes}/{index_child}', [ClientEventController::class, 'registerExpress'])->name('program.event.register-express')->withoutMiddleware(['auth', 'auth.department']);
Route::get('event/referral/{refcode}/{event_slug}/{notes}', [ClientEventController::class, 'referralPage'])->name('program.event.referral-page')->withoutMiddleware(['auth', 'auth.department']);
Route::get('event/qr/{clientevent}/{event_slug}', [ClientEventController::class, 'qrPage'])->name('program.event.qr-page')->withoutMiddleware(['auth', 'auth.department']);


Route::get('corporate', [PartnerProgramController::class, 'index'])->name('program.corporate.index');
Route::prefix('corporate')->name('corporate_prog.')->group(function () {
    Route::resource('{corp}/detail', PartnerProgramController::class);
    Route::resource('{corp}/detail/{corp_prog}/speaker', PartnerProgramSpeakerController::class);
    Route::resource('{corp}/detail/{corp_prog}/attach', PartnerProgramAttachController::class);
    Route::post('{corp}/detail/{corp_prog}/collaborators/{collaborators}', [PartnerProgramCollaboratorsController::class, 'store'])->name('collaborators.store');
    Route::delete('{corp}/detail/{corp_prog}/collaborators/{collaborators}/{collaborators_id}', [PartnerProgramCollaboratorsController::class, 'destroy'])->name('collaborators.destroy');
});

Route::get('school', [SchoolProgramController::class, 'index'])->name('program.school.index');
Route::post('school', [SchoolProgramController::class, 'index']);
Route::prefix('school')->name('school.')->group(function () {
    Route::resource('{school}/detail', SchoolProgramController::class, [
        'names' => [
            'index' => 'school.program.detail.index',
            'create' => 'school.program.detail.create',
            'store' => 'school.program.detail.store',
            'show' => 'school.program.detail.show',
            'edit' => 'school.program.detail.edit',
            'update' => 'school.program.detail.update',
            'destroy' => 'school.program.detail.destroy',
        ]
    ]);
    Route::resource('{school}/detail/{sch_prog}/speaker', SchoolProgramSpeakerController::class);
    Route::resource('{school}/detail/{sch_prog}/attach', SchoolProgramAttachController::class);
});
Route::post('school/{school}/detail/{sch_prog}/collaborators/{collaborators}', [SchoolProgramCollaboratorsController::class, 'store'])->name('school_prog.collaborators.store');
Route::delete('school/{school}/detail/{sch_prog}/collaborators/{collaborators}/{collaborators_id}', [SchoolProgramCollaboratorsController::class, 'destroy'])->name('school_prog.collaborators.destroy');

