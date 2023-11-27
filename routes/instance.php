<?php

use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolDetailController;
use App\Http\Controllers\CorporateController;
use App\Http\Controllers\CorporatePicController;
use App\Http\Controllers\SchoolAliasController;
// use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PartnerAgreementController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\SchoolProgramController;
use App\Http\Controllers\SchoolVisitController;
use App\Http\Controllers\UniversityPicController;
use Illuminate\Support\Facades\Route;

Route::resource('university', UniversityController::class);
Route::prefix('university')->name('university.')->group(function () {
    Route::resource('{university}/detail', UniversityPicController::class);
});

Route::get('school/raw', function () {
    return view('pages.instance.school.raw.index');
});
Route::get('school/raw/1/new', function () {
    return view('pages.instance.school.raw.form-new');
});
Route::resource('school', SchoolController::class);
Route::prefix('school')->name('school.')->group(function() {
    Route::resource('{school}/detail', SchoolDetailController::class);
    Route::resource('{school}/program', SchoolDetailController::class);
    Route::post('{school}/visit', [SchoolVisitController::class, 'store'])->name('visit.store');
    Route::put('{school}/visit/{visit}', [SchoolVisitController::class, 'update'])->name('visit.update');
    Route::delete('{school}/visit/{visit}', [SchoolVisitController::class, 'destroy'])->name('visit.destroy');

    Route::resource('{school}/alias', SchoolAliasController::class);
});

Route::resource('corporate', CorporateController::class);
Route::prefix('corporate')->name('corporate.')->group(function () {

    Route::resource('{corporate}/detail', CorporatePicController::class);
    Route::resource('{corporate}/agreement', PartnerAgreementController::class);
});

// Route::resource('partner', PartnerController::class);