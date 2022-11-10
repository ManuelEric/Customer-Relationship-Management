<?php

use App\Http\Controllers\CorporateController;
use Illuminate\Support\Facades\Route;


Route::resource('corporate', CorporateController::class);