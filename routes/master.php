<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetReturnedController;
use App\Http\Controllers\AssetUsedController;
use App\Http\Controllers\CorporatePartnerEventController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\PurchaseDetailController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\SchoolEventController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UniversityEventController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\EdufLeadController;
use App\Http\Controllers\EdufLeadSpeakerController;
use App\Http\Controllers\EdufReviewController;
use App\Http\Controllers\EventSpeakerController;
use App\Http\Controllers\SalesTargetController;
use App\Http\Controllers\SeasonalProgramController;
use App\Http\Controllers\SubjectController;
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

Route::get('program/sub_program/{main_program}', [ProgramController::class, 'getSubProgram']);
Route::resource('program', ProgramController::class);

Route::resource('lead', LeadController::class);

Route::resource('position', PositionController::class);

Route::resource('major', MajorController::class);

Route::resource('purchase', PurchaseRequestController::class);
Route::prefix('purchase')->name('purchase.')->group(function () {
    Route::get('download/{file_name}', [PurchaseRequestController::class, 'download'])->name('download');
    Route::get('{purchase}/print', [PurchaseRequestController::class, 'print'])->name('print');
    Route::resource('{purchase}/detail', PurchaseDetailController::class);
});

Route::resource('vendor', VendorController::class);

Route::resource('asset', AssetController::class);
Route::resource('asset/{asset}/used', AssetUsedController::class);
Route::resource('asset/{asset}/used/{used}/returned', AssetReturnedController::class);
// Route::delete('asset/{asset}/detail', [AssetUsedController::class, 'destroy']);
// Route::post('asset/{asset}/detail', [AssetUsedController::class, 'store']);
// Route::delete('asset/{asset}/{user}', [AssetUsedController::class, 'destroy']);

// Route::get('asset/{asset}/{user}/edit', [AssetUsedController::class, 'edit']);
// Route::post('asset/{asset}/{user}', [AssetReturnedController::class, 'store']);
Route::resource('event', EventController::class);
Route::prefix('event')->name('event.')->group(function () {
    Route::resource('{event}/university', UniversityEventController::class);
    Route::resource('{event}/school', SchoolEventController::class);
    Route::resource('{event}/partner', CorporatePartnerEventController::class);
    Route::post('{event}/speaker', [EventSpeakerController::class, 'store'])->name('speaker.store');
    Route::put('{event}/speaker/{speaker}', [EventSpeakerController::class, 'update'])->name('speaker.update');
    Route::delete('{event}/speaker/{speaker}', [EventSpeakerController::class, 'destroy'])->name('speaker.destroy');
});

Route::resource('edufair', EdufLeadController::class);
Route::prefix('edufair')->name('edufair.')->group(function () {

    Route::post('{edufair}/review', [EdufReviewController::class, 'store'])->name('review.store');
    Route::get('{edufair}/review/{review}/edit', [EdufLeadController::class, 'edit'])->name('review.edit');
    Route::get('{edufair}/review/{review}', [EdufReviewController::class, 'show'])->name('review.show');
    Route::put('{edufair}/review/{review}', [EdufReviewController::class, 'update'])->name('review.update');
    Route::delete('{edufair}/review/{review}', [EdufReviewController::class, 'destroy'])->name('review.destroy');

    Route::resource('{edufair}/speaker', EdufLeadSpeakerController::class);
});

Route::resource('sales-target', SalesTargetController::class);

Route::resource('curriculum', CurriculumController::class);

Route::resource('university-tags', TagController::class);

Route::resource('seasonal-program', SeasonalProgramController::class);

Route::resource('subject', SubjectController::class);
