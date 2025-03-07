<?php

use App\Http\Controllers\GoogleSheetController;
use Illuminate\Support\Facades\Route;

# Import From google sheet
Route::middleware(['auth:api', 'throttle:10,1'])->group(function () {
    Route::get('parent', [GoogleSheetController::class, 'storeParent']);
    Route::get('client-event', [GoogleSheetController::class, 'storeClientEvent']);
    Route::get('student', [GoogleSheetController::class, 'storeStudent']);
    Route::get('teacher', [GoogleSheetController::class, 'storeTeacher']);
    Route::get('client-program', [GoogleSheetController::class, 'storeClientProgram']);
});