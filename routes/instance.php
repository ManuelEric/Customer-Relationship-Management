<?php

use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolDetailController;
use App\Http\Controllers\CorporateController;
use App\Http\Controllers\CorporatePicController;
// use App\Http\Controllers\PartnerController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\SchoolProgramController;
use App\Http\Controllers\UniversityPicController;
use Illuminate\Support\Facades\Route;

Route::resource('university', UniversityController::class);
Route::prefix('university')->name('university.')->group(function () {
    Route::resource('{university}/detail', UniversityPicController::class);
});

Route::resource('school', SchoolController::class);
<<<<<<< HEAD
Route::prefix('school')->name('school.')->group(function () {
    Route::resource('{school}/detail', SchoolDetailController::class);
    Route::resource('{school}/program', SchoolProgramController::class);
=======
Route::prefix('school')->name('school.')->group(function() {
    Route::resource('{school}/detail', SchoolDetailController::class);
    Route::resource('{school}/program', SchoolDetailController::class);
    
>>>>>>> 90076580a2188f79ecb715981225c781e65e6d74
});

Route::resource('corporate', CorporateController::class);
Route::prefix('corporate')->name('corporate.')->group(function () {

    Route::resource('{corporate}/detail', CorporatePicController::class);
});

// Route::resource('partner', PartnerController::class);