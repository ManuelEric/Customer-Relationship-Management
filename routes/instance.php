<?php

use App\Http\Controllers\CorporateController;
use App\Http\Controllers\EdufLeadController;
use Illuminate\Support\Facades\Route;


Route::resource('corporate', CorporateController::class);
Route::resource('edufair', EdufLeadController::class);