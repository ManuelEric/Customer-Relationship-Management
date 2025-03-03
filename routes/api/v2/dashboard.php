<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v2\SalesDashboardController as V2SalesDashboardController;
use App\Http\Controllers\Api\v2\PartnerDashboardController as V2PartnerDashboardController;
use App\Http\Controllers\Api\v2\FinanceDashboardController as V2FinanceDashboardController;
use App\Http\Controllers\Api\v2\DigitalDashboardController as V2DigitalDashboardController;
use App\Http\Controllers\DashboardController;

/**
 * New dashboard requirements
 * if dashboard want to be created using API approach
 * for now, not being used
 */
Route::middleware(['throttle:120,1'])->group(function () {
    
    /**
     * Sales
     */
    Route::get('client', [V2SalesDashboardController::class, 'getClientByMonthAndType']);
    Route::get('client-status', [V2SalesDashboardController::class, 'getClientStatus']);
    Route::get('client-program', [V2SalesDashboardController::class, 'getClientProgramByMonth']);
    Route::get('successful-program', [V2SalesDashboardController::class, 'getSuccessfulProgramByMonth']);
    Route::get('detail/successful-program', [V2SalesDashboardController::class, 'getSuccessfulProgramDetailByMonthAndProgram']);
    Route::get('admissions-mentoring', [V2SalesDashboardController::class, 'getAdmissionsProgramByMonth']);
    Route::get('initial-consultation', [V2SalesDashboardController::class, 'getInitialConsultationByMonth']);
    Route::get('academic-prep', [V2SalesDashboardController::class, 'getAcademicPrepByMonth']);
    Route::get('career-exploration', [V2SalesDashboardController::class, 'getCareerExplorationByMonth']);
    Route::get('conversion-lead', [V2SalesDashboardController::class, 'getConversionLeadByMonth']);
    Route::get('lead/admissions-mentoring', [V2SalesDashboardController::class, 'getLeadAdmissionsProgramByMonth']);
    Route::get('lead/academic-prep', [V2SalesDashboardController::class, 'getLeadAcademicPrepByMonth']);
    Route::get('lead/career-exploration', [V2SalesDashboardController::class, 'getLeadCareerExplorationByMonth']);
    Route::get('detail/client-program', [V2SalesDashboardController::class, 'getClientProgramByMonthDetail']);
    Route::get('all-program/target', [V2SalesDashboardController::class, 'getAllProgramTargetByMonth']);
    Route::get('program-comparison', [V2SalesDashboardController::class, 'compare_program']);
    Route::get('client-event', [V2SalesDashboardController::class, 'getClientEventByYear']);
    Route::get('outstanding-payment', [DashboardController::class, 'listOustandingPayments']);

    
    /**
     * Partnership
     */
    Route::get('partner/total', [V2PartnerDashboardController::class, 'getTotalByMonth']);
    Route::get('partner/detail', [V2PartnerDashboardController::class, 'getPartnerDetailByMonth']);
    Route::get('partner/agenda', [V2PartnerDashboardController::class, 'getSpeakerByDate']);
    Route::get('partner/partnership-program', [V2PartnerDashboardController::class, 'getPartnershipProgramByMonth']);
    Route::get('partner/partnership-program/detail', [V2PartnerDashboardController::class, 'getPartnershipProgramDetailByMonth']);
    Route::get('partner/partnership-program/program-comparison', [V2PartnerDashboardController::class, 'getProgramComparison']);


    /**
     * Digital
     */
    Route::get('digital/all-leads', [V2DigitalDashboardController::class, 'getDataLead']);
    Route::get('digital/leads', [V2DigitalDashboardController::class, 'getLeadDigital']);
    Route::get('digital/detail/type-lead', [V2DigitalDashboardController::class, 'getDetailDataLead']);
    Route::get('digital/detail/lead-source', [V2DigitalDashboardController::class, 'getDetailLeadSource']);
    Route::get('digital/detail/conversion-lead', [V2DigitalDashboardController::class, 'getDetailConversionLead']);

    
    /**
     * Finance
     */
    Route::get('finance/detail', [V2FinanceDashboardController::class, 'getFinanceDetailByMonth']);
    Route::get('finance/total', [V2FinanceDashboardController::class, 'getTotalByMonth']);
    Route::get('finance/outstanding', [V2FinanceDashboardController::class, 'getOutstandingPayment']);
    Route::get('finance/revenue', [V2FinanceDashboardController::class, 'getRevenueByYear']);
    Route::get('finance/revenue/detail', [V2FinanceDashboardController::class, 'getRevenueDetailByMonth']);
    Route::get('finance/outstanding/period', [V2FinanceDashboardController::class, 'getOutstandingPaymentByPeriod']);
});