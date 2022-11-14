<?php

use App\Http\Controllers\CorporateController;
use App\Http\Controllers\EdufLeadController;
use App\Http\Controllers\EdufReviewController;
use Illuminate\Support\Facades\Route;


Route::resource('corporate', CorporateController::class);
Route::resource('edufair', EdufLeadController::class);
Route::post('edufair/{edufair}/review', [EdufReviewController::class, 'store'])->name('edufair.review.store');
Route::get('edufair/{edufair}/review/{review}/edit', [EdufLeadController::class, 'edit'])->name('edufair.review.edit');
Route::put('edufair/{edufair}/review/{review}', [EdufReviewController::class, 'update'])->name('edufair.review.update');
Route::delete('edufair/{edufair}/review/{review}', [EdufReviewController::class, 'destroy'])->name('edufair.review.destroy');