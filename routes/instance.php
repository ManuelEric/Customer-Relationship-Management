<?php

use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolDetailController;
use App\Http\Controllers\CorporateController;
use App\Http\Controllers\CorporatePicController;
use App\Http\Controllers\EdufLeadController;
use App\Http\Controllers\EdufReviewController;
// use App\Http\Controllers\PartnerController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\UniversityPicController;
use Illuminate\Support\Facades\Route;

Route::resource('university', UniversityController::class);
Route::prefix('university')->name('university.')->group(function() {
    Route::resource('{university}/detail', UniversityPicController::class);

});

Route::resource('school', SchoolController::class);
Route::resource('school/{school}/detail', SchoolDetailController::class);

Route::resource('corporate', CorporateController::class);
Route::prefix('corporate')->name('corporate.')->group(function() {

    Route::resource('{corporate}/detail', CorporatePicController::class);
});

Route::resource('edufair', EdufLeadController::class);
Route::prefix('edufair')->name('edufair.')->group(function() {

    Route::post('{edufair}/review', [EdufReviewController::class, 'store'])->name('review.store');
    Route::get('{edufair}/review/{review}/edit', [EdufLeadController::class, 'edit'])->name('review.edit');
    Route::get('{edufair}/review/{review}', [EdufReviewController::class, 'show'])->name('review.show');
    Route::put('{edufair}/review/{review}', [EdufReviewController::class, 'update'])->name('review.update');
    Route::delete('{edufair}/review/{review}', [EdufReviewController::class, 'destroy'])->name('review.destroy');
});

// Route::resource('partner', PartnerController::class);

Route::get('referral', function () {
    return view('pages.instance.referral.index');
});
Route::get('referral/create', function () {
    return view('pages.instance.referral.form');
});
