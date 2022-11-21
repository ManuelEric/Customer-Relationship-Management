<?php

use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolDetailController;
use App\Http\Controllers\CorporateController;
use App\Http\Controllers\CorporatePicController;
use App\Http\Controllers\EdufLeadController;
use App\Http\Controllers\EdufReviewController;
use App\Http\Controllers\UniversityController;
use Illuminate\Support\Facades\Route;

Route::resource('university', UniversityController::class);

Route::resource('school', SchoolController::class);
Route::resource('school/{school}/detail', SchoolDetailController::class);

Route::resource('corporate', CorporateController::class);
Route::prefix('corporate')->name('corporate.')->group(function () {
    Route::resource('{corporate}/detail', CorporatePicController::class);
});

Route::resource('edufair', EdufLeadController::class);
Route::post('edufair/{edufair}/review', [EdufReviewController::class, 'store'])->name('edufair.review.store');
Route::get('edufair/{edufair}/review/{review}/edit', [EdufLeadController::class, 'edit'])->name('edufair.review.edit');
Route::get('edufair/{edufair}/review/{review}', [EdufReviewController::class, 'show'])->name('edufair.review.show');
Route::put('edufair/{edufair}/review/{review}', [EdufReviewController::class, 'update'])->name('edufair.review.update');
Route::delete('edufair/{edufair}/review/{review}', [EdufReviewController::class, 'destroy'])->name('edufair.review.destroy');

Route::get('referral', function () {
    return view('pages.instance.referral.index');
});
Route::get('referral/create', function () {
    return view('pages.instance.referral.form');
});