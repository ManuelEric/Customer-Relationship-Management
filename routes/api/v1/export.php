<?php

use App\Http\Controllers\GoogleSheetController;
use Illuminate\Support\Facades\Route;

# Export data to google sheet
Route::middleware(['auth:api'])->group( function () {

    // {from} meaning type of data = {collection or model}
    Route::get('{type}/{from}', [GoogleSheetController::class, 'exportData']);
});