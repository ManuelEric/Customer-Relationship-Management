<?php

use App\Http\Controllers\Api\v1\DigitalDashboardController;
use App\Http\Controllers\Api\v1\ExtClientController;
use App\Http\Controllers\Api\v1\ExtSalesTrackingController;
use App\Http\Controllers\Api\v1\SalesDashboardController;
use App\Http\Controllers\Api\v1\PartnerDashboardController;
use App\Http\Controllers\Api\v1\FinanceDashboardController;
use App\Http\Controllers\Api\v1\SchoolController as APISchoolController;
use App\Http\Controllers\ClientEventController;
use App\Http\Controllers\ClientParentController;
use App\Http\Controllers\ClientStudentController;
use App\Http\Controllers\ClientTeacherCounselorController;
use App\Http\Controllers\CorporateController;
use App\Http\Controllers\CurrencyRateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ExcelTemplateController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\InvoiceProgramController;
use App\Http\Controllers\InvoiceSchoolController;
use App\Http\Controllers\InvoicePartnerController;
use App\Http\Controllers\InvoiceReferralController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\Module\ClientController;
use App\Http\Controllers\ReceiptSchoolController;
use App\Http\Controllers\ReceiptPartnerController;
use App\Http\Controllers\ReceiptReferralController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolEventController;
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

Route::get('get/client/{id}', [ExtClientController::class, 'getClientById']);

# dashboard sales
Route::get('get/client/{month}/type/{type}', [SalesDashboardController::class, 'getClientByMonthAndType']);
Route::get('get/client-status/{month}', [SalesDashboardController::class, 'getClientStatus']);
Route::get('get/follow-up-reminder/{month}', [SalesDashboardController::class, 'getFollowUpReminder']);
Route::get('get/mentee-birthday/{month}', [SalesDashboardController::class, 'getMenteesBirthdayByMonth']);

Route::get('get/client-program/{month}/{user?}', [SalesDashboardController::class, 'getClientProgramByMonth']);
Route::get('get/successful-program/{month}/{user?}', [SalesDashboardController::class, 'getSuccessfulProgramByMonth']);
Route::get('get/detail/successful-program/{month}/{program}/{user?}', [SalesDashboardController::class, 'getSuccessfulProgramDetailByMonthAndProgram']);
Route::get('get/admissions-mentoring/{month}/{user?}', [SalesDashboardController::class, 'getAdmissionsProgramByMonth']);
Route::get('get/initial-consultation/{month}/{user?}', [SalesDashboardController::class, 'getInitialConsultationByMonth']);
Route::get('get/academic-prep/{month}/{user?}', [SalesDashboardController::class, 'getAcademicPrepByMonth']);
// Route::get('get/detail/academic-prep/{month}/{user?}', [SalesDashboardController::class, 'getAcademicPrepByMonthDetail']);
Route::get('get/career-exploration/{month}/{user?}', [SalesDashboardController::class, 'getCareerExplorationByMonth']);
// Route::get('get/detail/career-exploration/{month}/{user?}', [SalesDashboardController::class, 'getCareerExplorationByMonthDetail']);
Route::get('get/detail/client-program/{month}/{type}/{user?}', [SalesDashboardController::class, 'getClientProgramByMonthDetail']);
Route::get('get/conversion-lead/{month}/{user?}', [SalesDashboardController::class, 'getConversionLeadByMonth']);
Route::get('get/lead/admissions-mentoring/{month}/{user?}', [SalesDashboardController::class, 'getLeadAdmissionsProgramByMonth']);
Route::get('get/lead/academic-prep/{month}/{user?}', [SalesDashboardController::class, 'getLeadAcademicPrepByMonth']);
Route::get('get/lead/career-exploration/{month}/{user?}', [SalesDashboardController::class, 'getLeadCareerExplorationByMonth']);
Route::get('get/all-program/target/{month}/{user?}', [SalesDashboardController::class, 'getAllProgramTargetByMonth']);
Route::get('get/client-event/{year}/{user?}', [SalesDashboardController::class, 'getClientEventByYear']);
Route::get('get/program-comparison', [SalesDashboardController::class, 'compare_program']);
Route::get('get/conversion-lead/event/{event}', [SalesDashboardController::class, 'getConversionLeadsByEventId']);

Route::post('/upload', [InvoiceProgramController::class, 'upload']);
Route::get('mentee/birthday/{month}', [SalesDashboardController::class, 'getMenteesBirthdayByMonth']);

Route::get('export/client', [SalesDashboardController::class, 'exportClient']);
Route::get('get/outstanding-payment', [DashboardController::class, 'ajaxDataTablesOutstandingPayment']);

# dashboard partnership
Route::get('partner/detail/{month}/{type}', [PartnerDashboardController::class, 'getPartnerDetailByMonth']);
Route::get('partner/total/{month}/{type}', [PartnerDashboardController::class, 'getTotalByMonth']);
Route::get('partner/agenda/{date}', [PartnerDashboardController::class, 'getSpeakerByDate']);
Route::get('partner/partnership-program/{month}', [PartnerDashboardController::class, 'getPartnershipProgramByMonth']);
Route::get('partner/partnership-program/detail/{type}/{status}/{month}', [PartnerDashboardController::class, 'getPartnershipProgramDetailByMonth']);
Route::get('partner/partnership-program/program-comparison/{start_year}/{end_year}', [PartnerDashboardController::class, 'getProgramComparison']);

# dashboard finance
Route::get('finance/detail/{month}/{type}', [FinanceDashboardController::class, 'getFinanceDetailByMonth']);
Route::get('finance/total/{month}', [FinanceDashboardController::class, 'getTotalByMonth']);
Route::get('finance/outstanding/{month}', [FinanceDashboardController::class, 'getOutstandingPayment']);
Route::get('finance/revenue/{year}', [FinanceDashboardController::class, 'getRevenueByYear']);
Route::get('finance/revenue/detail/{year}/{month}', [FinanceDashboardController::class, 'getRevenueDetailByMonth']);
Route::get('finance/outstanding/period/{start_date}/{end_date}', [FinanceDashboardController::class, 'getOutstandingPaymentByPeriod']);

# dashboard digital
Route::get('digital/all-leads/{month}', [DigitalDashboardController::class, 'getDataLead']);
Route::get('digital/leads/{month}/{prog?}', [DigitalDashboardController::class, 'getLeadDigital']);
Route::get('digital/detail/{month}/type-lead/{type_lead}/division/{division}', [DigitalDashboardController::class, 'getDetailDataLead']);
Route::get('digital/detail/{month}/lead-source/{lead}/{prog?}', [DigitalDashboardController::class, 'getDetailLeadSource']);
Route::get('digital/detail/{month}/conversion-lead/{lead}/{prog?}', [DigitalDashboardController::class, 'getDetailConversionLead']);


Route::post('/upload', [InvoiceProgramController::class, 'upload']);
Route::post('invoice-sch/{invoice}/upload/{currency}', [InvoiceSchoolController::class, 'upload']);
Route::post('invoice-ref/{invoice}/upload/{currency}', [InvoiceReferralController::class, 'upload']);
Route::post('invoice-corp/{invoice}/upload/{currency}', [InvoicePartnerController::class, 'upload']);

Route::post('receipt-sch/{receipt}/upload/{currency}', [ReceiptSchoolController::class, 'upload_signed']);
Route::post('receipt-ref/{receipt}/upload/{currency}', [ReceiptReferralController::class, 'upload_signed']);
Route::post('receipt-corp/{receipt}/upload/{currency}', [ReceiptPartnerController::class, 'upload_signed']);

# menus
Route::get('employee/department/{department}', [DepartmentController::class, 'getEmployeeByDepartment']);
Route::get('department/access/{department}/{user?}', [MenuController::class, 'getDepartmentAccess']);

# import student, # import client event
Route::get('download/excel-template/{type}', [ExcelTemplateController::class, 'generateTemplate']);

# master / event
Route::get('master/event/{event}/school/{school}', [SchoolEventController::class]);

# client student menu
Route::get('client/{client}/programs', [ClientStudentController::class, 'getClientProgramByStudentId']);
Route::get('client/{client}/events', [ClientStudentController::class, 'getClientEventByStudentId']);

# Client teacher menu
Route::get('teacher/{teacher}/events', [ClientTeacherCounselorController::class, 'getClientEventByTeacherId']);

# invoice program menu
Route::get('current/rate/{base_currency}/{to_currency}', [CurrencyRateController::class, 'getCurrencyRate']);

# create QR Code
// Route::get('create-qr/{size}', [ClientEventController::class, 'createQrCode']);

# external API
Route::prefix('v1')->group(function () {
    Route::get('get/mentees', [ExtClientController::class, 'getClientFromAdmissionMentoring']);
    Route::get('get/mentors', [ExtClientController::class, 'getMentors']);
    Route::get('get/alumnis', [ExtClientController::class, 'getAlumnis']);
    Route::get('get/detail/lead-source', [ExtSalesTrackingController::class, 'getLeadSourceDetail']);
    Route::get('get/detail/conversion-lead', [ExtSalesTrackingController::class, 'getConversionLeadDetail']);
});

# Client Event Attendance
Route::get('event/attendance/{id}/{status}', [ClientEventController::class, 'updateAttendance']);

# Client Event Number of Party
Route::get('event/party/{id}/{party}', [ClientEventController::class, 'updateNumberOfParty']);

# Get URL from short URL
Route::get('track/referral/{referral}', [ClientEventController::class, 'trackReferralURL']);

# Instance School API
Route::get('school', [APISchoolController::class, 'search']);
# Get Active School Data
Route::get('instance/school/', [SchoolController::class, 'getSchoolData']);

# Get Parents Data
Route::get('client/parent/', [ClientParentController::class, 'getDataParents']);

# Get Client Suggestion
Route::get('client/suggestion/', [ClientController::class, 'getClientSuggestion']);
