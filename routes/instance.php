<?php

use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolDetailController;
use App\Http\Controllers\CorporateController;
use App\Http\Controllers\EdufLeadController;
use Illuminate\Support\Facades\Route;


Route::resource('school', SchoolController::class);
Route::resource('school/{school}/detail', SchoolDetailController::class);

Route::resource('corporate', CorporateController::class);
Route::resource('edufair', EdufLeadController::class);