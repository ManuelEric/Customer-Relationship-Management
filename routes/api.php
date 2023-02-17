<?php

use App\Http\Controllers\Api\v1\SalesDashboardController;
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
Route::get('get/client-status/{month}', [SalesDashboardController::class, 'getClientStatus']);
Route::get('get/follow-up-reminder/{month}', [SalesDashboardController::class, 'getFollowUpReminder']);
Route::get('get/mentee-birthday/{month}', [SalesDashboardController::class, 'getMenteesBirthdayByMonth']);

Route::get('get/client-program/{month}/{user?}', [SalesDashboardController::class, 'getClientProgramByMonth']);
Route::get('get/successful-program/{month}/{user?}', [SalesDashboardController::class, 'getSuccessfulProgramByMonth']);
Route::get('get/admissions-mentoring/{month}/{user?}', [SalesDashboardController::class, 'getAdmissionsProgramByMonth']);
Route::get('get/initial-consultation/{month}/{user?}', [SalesDashboardController::class, 'getInitialConsultationByMonth']);
Route::get('get/academic-prep/{month}/{user?}', [SalesDashboardController::class, 'getAcademicPrepByMonth']);
Route::get('get/career-exploration/{month}/{user?}', [SalesDashboardController::class, 'getCareerExplorationByMonth']);
Route::get('get/conversion-lead/{month}/{user?}', [SalesDashboardController::class, 'getConversionLeadByMonth']);
Route::get('get/lead/admissions-mentoring/{month}/{user?}', [SalesDashboardController::class, 'getLeadAdmissionsProgramByMonth']);
Route::get('get/lead/academic-prep/{month}/{user?}', [SalesDashboardController::class, 'getLeadAcademicPrepByMonth']);
Route::get('get/lead/career-exploration/{month}/{user?}', [SalesDashboardController::class, 'getLeadCareerExplorationByMonth']);



Route::get('get/program-comparison', [SalesDashboardController::class, 'compare_program']);
Route::get('get/conversion-lead/event/{event}', [SalesDashboardController::class, 'getConversionLeadsByEventId']);
