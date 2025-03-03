<?php

/*
|--------------------------------------------------------------------------
| API/V1/Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\SalesDashboardController as V1SalesDashboardController;
use App\Http\Controllers\Api\v1\PartnerDashboardController as V1PartnershipDashboardController;
use App\Http\Controllers\Api\v1\FinanceDashboardController as V1FinanceDashboardController;
use App\Http\Controllers\Api\v1\DigitalDashboardController as V1DigitalDashboardController;
use App\Http\Controllers\DashboardController;

Route::middleware(['throttle:120,1'])->group(function () {

    /**
     * Sales
     */
    Route::get('client-status/{month}', [V1SalesDashboardController::class, 'fnGetClientStatus']);
    // Route::get('follow-up-reminder/{month}', [V1SalesDashboardController::class, 'fnGetFollowUpReminder']); //! not used
    // Route::get('mentee-birthday/{month}', [V1SalesDashboardController::class, 'fnGetMenteesBirthdayByMonth']); //! not used
    Route::get('client-program/{month}/{user?}', [V1SalesDashboardController::class, 'fnGetClientProgramByMonth']);
    Route::get('successful-program/{month}/{user?}', [V1SalesDashboardController::class, 'fnGetSuccessfulProgramByMonth']);
    Route::get('detail/successful-program/{month}/{program}/{user?}', [V1SalesDashboardController::class, 'fnGetSuccessfulProgramDetailByMonthAndProgram']);
    Route::get('admissions-mentoring/{month}/{user?}', [V1SalesDashboardController::class, 'fnGetAdmissionsProgramByMonth']);
    Route::get('initial-consultation/{month}/{user?}', [V1SalesDashboardController::class, 'fnGetInitialConsultationByMonth']);
    Route::get('detail/initial-consultation/{month}/{user?}', [V1SalesDashboardController::class, 'fnGetDetailInitialConsultByMonth']);
    Route::get('academic-prep/{month}/{user?}', [V1SalesDashboardController::class, 'fnGetAcademicPrepByMonth']);
    Route::get('career-exploration/{month}/{user?}', [V1SalesDashboardController::class, 'fnGetCareerExplorationByMonth']);
    Route::get('detail/client-program/{month}/{type}/{user?}', [V1SalesDashboardController::class, 'fnGetClientProgramByMonthDetail']);
    Route::get('conversion-lead/{month}/{user?}', [V1SalesDashboardController::class, 'fnGetConversionLeadByMonth']);
    Route::get('lead/admissions-mentoring/{month}/{user?}', [V1SalesDashboardController::class, 'fnGetLeadAdmissionsProgramByMonth']);
    Route::get('lead/academic-prep/{month}/{user?}', [V1SalesDashboardController::class, 'fnGetLeadAcademicPrepByMonth']);
    Route::get('lead/career-exploration/{month}/{user?}', [V1SalesDashboardController::class, 'fnGetLeadCareerExplorationByMonth']);
    Route::get('all-program/target/{month}/{user?}', [V1SalesDashboardController::class, 'fnGetAllProgramTargetByMonth']);
    Route::get('client-event/{year}/{user?}', [V1SalesDashboardController::class, 'fnGetClientEventByYear']);
    Route::get('program-comparison', [V1SalesDashboardController::class, 'fnCompareProgram']);
    // Route::get('export/client', [V1SalesDashboardController::class, 'fnExportClient']); //! not used
    Route::get('outstanding-payment', [DashboardController::class, 'fnAjaxDataTablesOutstandingPayment']);


    /**
     * Partnership
     */
    Route::get('partner/detail/{month}/{type}', [V1PartnershipDashboardController::class, 'fnGetPartnerDetailByMonth']);
    Route::get('partner/total/{month}/{type}', [V1PartnershipDashboardController::class, 'fnGetTotalByMonth']);
    Route::get('partner/agenda/{date}', [V1PartnershipDashboardController::class, 'fnGetSpeakerByDate']);
    Route::get('partner/partnership-program/{month}', [V1PartnershipDashboardController::class, 'fnGetPartnershipProgramByMonth']);
    Route::get('partner/partnership-program/detail/{type}/{status}/{month}', [V1PartnershipDashboardController::class, 'fnGetPartnershipProgramDetailByMonth']);
    Route::get('partner/partnership-program/program-comparison/{start_year}/{end_year}', [V1PartnershipDashboardController::class, 'fnGetProgramComparison']);


    /**
     * Finance
     */
    Route::get('finance/detail/{month}/{type}', [V1FinanceDashboardController::class, 'getFinanceDetailByMonth']);
    Route::get('finance/total/{month}', [V1FinanceDashboardController::class, 'getTotalByMonth']);
    Route::get('finance/outstanding/{month}', [V1FinanceDashboardController::class, 'getOutstandingPayment']);
    Route::get('finance/revenue/{year}', [V1FinanceDashboardController::class, 'getRevenueByYear']);
    Route::get('finance/revenue/detail/{year}/{month}', [V1FinanceDashboardController::class, 'getRevenueDetailByMonth']);
    Route::get('finance/outstanding/period/{start_date}/{end_date}', [V1FinanceDashboardController::class, 'getOutstandingPaymentByPeriod']);


    /**
     * Digital
     */
    Route::get('digital/all-leads/{month}', [V1DigitalDashboardController::class, 'getDataLead']);
    Route::get('digital/leads/{month}/{prog?}', [V1DigitalDashboardController::class, 'getLeadDigital']);
    Route::get('digital/detail/{month}/type-lead/{type_lead}/division/{division}', [V1DigitalDashboardController::class, 'getDetailDataLead']);
    Route::get('digital/detail/{month}/lead-source/{lead}/{prog?}', [V1DigitalDashboardController::class, 'getDetailLeadSource']);
    Route::get('digital/detail/{month}/conversion-lead/{lead}/{prog?}', [V1DigitalDashboardController::class, 'getDetailConversionLead']);
});