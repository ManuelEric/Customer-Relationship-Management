<?php

use App\Http\Controllers\Api\v1\SalesDashboardController;
use App\Http\Controllers\Api\v1\PartnerDashboardController;
use App\Http\Controllers\UniversityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

# dashboard sales
Route::get('mentee/birthday/{month}', [SalesDashboardController::class, 'getMenteesBirthdayByMonth']);

Route::get('partner/total/{month}', [PartnerDashboardController::class, 'getTotalByMonth']);
Route::get('partner/agenda/{date}', [PartnerDashboardController::class, 'getSpeakerByDate']);
Route::get('partner/partnership-program/{month}', [PartnerDashboardController::class, 'getPartnershipProgramByMonth']);
Route::get('partner/partnership-program/detail/{type}/{status}/{month}', [PartnerDashboardController::class, 'getPartnershipProgramDetailByMonth']);
Route::get('partner/partnership-program/program-comparison/{start_year}/{end_year}', [PartnerDashboardController::class, 'getProgramComparison']);
