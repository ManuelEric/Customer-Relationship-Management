<?php

use App\Http\Controllers\Api\v1\DigitalDashboardController;
use App\Http\Controllers\Api\v2\DigitalDashboardController as DigitalDashboardControllerV2;
use App\Http\Controllers\Api\v1\ExtClientController;
use App\Http\Controllers\Api\v1\ExtLeadController;
use App\Http\Controllers\Api\v1\ExtProgramController;
use App\Http\Controllers\Api\v1\ExtSalesTrackingController;
use App\Http\Controllers\Api\v1\ExtUserController;
use App\Http\Controllers\Api\v1\SalesDashboardController;
use App\Http\Controllers\Api\v2\SalesDashboardController as SalesDashboardControllerV2;
use App\Http\Controllers\Api\v1\PartnerDashboardController;
use App\Http\Controllers\Api\v2\PartnerDashboardController as PartnerDashboardControllerV2;
use App\Http\Controllers\Api\v1\FinanceDashboardController;
use App\Http\Controllers\Api\v2\FinanceDashboardController as FinanceDashboardControllerV2;
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
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\Api\v1\SchoolController as APISchoolController;
use App\Http\Controllers\Api\v1\ProgramController as APIProgramController;
use App\Http\Controllers\Api\v1\TagController as APITagController;
use App\Http\Controllers\Api\v1\ClientEventController as APIClientEventController;
use App\Http\Controllers\Api\v1\EventController as APIEventController;
use App\Http\Controllers\Api\v1\ExtEventController;
use App\Http\Controllers\Api\v1\ExtPartnerController;
use App\Http\Controllers\Api\v1\ExtUniversityController;
use App\Http\Controllers\GoogleSheetController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ReceiptSchoolController;
use App\Http\Controllers\ReceiptPartnerController;
use App\Http\Controllers\ReceiptReferralController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolEventController;
use App\Http\Controllers\UserController;
use App\Models\JobBatches;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
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

Route::get('get/detail/initial-consultation/{month}/{user?}', [SalesDashboardController::class, 'getDetailInitialConsultByMonth']);

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
    Route::get('get/parent-mentees', [ExtClientController::class, 'getParentMentee']);
    Route::get('get/alumni-mentees', [ExtClientController::class, 'getAlumniMentees']);
    Route::get('get/mentees', [ExtClientController::class, 'getClientFromAdmissionMentoring']);
    Route::get('get/mentors', [ExtClientController::class, 'getMentors']);
    Route::get('get/alumnis', [ExtClientController::class, 'getAlumnis']);
    Route::get('get/detail/lead-source', [ExtSalesTrackingController::class, 'getLeadSourceDetail']);
    Route::get('get/detail/conversion-lead', [ExtSalesTrackingController::class, 'getConversionLeadDetail']);

    # used for spreadsheets syncing data
    Route::get('get/{department}/member', [ExtUserController::class, 'getMemberOfDepartments']);
    Route::get('get/employees', [ExtUserController::class, 'getEmployees']);
    Route::get('get/programs', [ExtProgramController::class, 'getPrograms']);
    Route::get('get/programs/{main_program}', [ExtProgramController::class, 'getProgramsByMainProg']);
    Route::get('get/programs/type/{type}', [ExtProgramController::class, 'getProgramsByType']);
    Route::get('get/leads', [ExtLeadController::class, 'getLeadSources']);
    Route::get('get/partners', [ExtPartnerController::class, 'getPartners']);
    Route::get('get/universities', [ExtUniversityController::class, 'getUniversities']);
    Route::get('get/events', [ExtEventController::class, 'getEvents']);

    # used for creating form registration
    Route::get('get/destination-country', [APITagController::class, 'getTags']);


    # used for storing user client data / from registation form
    Route::post('register/event', [ExtClientController::class, 'store']);
    Route::get('register/event/express/{main_client}/{notes}/{second_client?}', [ExtClientController::class, 'store_express'])->name('register-express-event');
    Route::get('event/{event_id}', [APIEventController::class, 'findEvent']);
    Route::get('client-event/{screening_type}/{identifier}', [APIClientEventController::class, 'findClientEvent']);
    Route::patch('registration/verify/{clientevent_id}', [ExtClientController::class, 'update']);
    Route::get('school', [APISchoolController::class, 'alt_search']);

    # Form embed public registration
    Route::post('register/public', [ExtClientController::class, 'storePublicRegistration']);

    # ----------------------
    # used in other platform
    # ----------------------
    Route::get('get/user/by/TKT/{ticket_no}', [ExtClientController::class, 'getUserByTicket']);
    Route::get('get/user/by/UUID/{uuid}', [ExtClientController::class, 'getUserByUUID']);

    # timesheet
    Route::middleware('throttle:10,1')->group(function () {
        Route::get('auth/email/check', [ExtClientController::class, 'checkUserEmail']);
        Route::post('auth/token', [ExtClientController::class, 'validateCredentials']);
        Route::get('user/mentor-tutors', [ExtClientController::class, 'getMentorTutors']);
        Route::post('user/update', [ExtClientController::class, 'updateUser']);
    });
});

# Client Event Attendance
Route::get('event/attendance/{id}/{status}', [ClientEventController::class, 'updateAttendance']);

# Client Event Number of Party
Route::get('event/party/{id}/{party}', [ClientEventController::class, 'updateNumberOfParty']);

# Get URL from short URL
Route::get('track/referral/{referral}', [ClientEventController::class, 'trackReferralURL']);

# Instance School API
# being called from CRM store/update school
# why it's different from api/v1/school? -> because api/v1/school being called from vue frontend meaning they have javascript to automatically create new
Route::get('school', [APISchoolController::class, 'search']);

# Get Active School Data
Route::get('instance/school/', [SchoolController::class, 'getSchoolData']);

# Get Parents Data
Route::get('client/parent/', [ClientParentController::class, 'getDataParents']);

# Get Client Suggestion
Route::get('client/suggestion/', [ClientController::class, 'getClientSuggestion']);

# Get Sales Team
Route::get('user/sales-team/', [UserController::class, 'getSalesTeam']); # basically same with route on line 155

# Get Prog Program based on Main Program Id
Route::get('get/program/main/{mainProgId}', [APIProgramController::class, 'getProgramNameByMainProgramId']);

# Get List referral / sub lead referral (All Client)
Route::get('get/referral/list', [LeadController::class, 'getListReferral']);

# Import From google sheet
Route::group(['middleware' => 'auth:api', 'prefix' => 'import'], function () {

    Route::get('parent', [GoogleSheetController::class, 'storeParent']);
    Route::get('client-event', [GoogleSheetController::class, 'storeClientEvent']);
    Route::get('student', [GoogleSheetController::class, 'storeStudent']);
    Route::get('teacher', [GoogleSheetController::class, 'storeTeacher']);
    Route::get('client-program', [GoogleSheetController::class, 'storeClientProgram']);
});

# New dashboard
Route::prefix('v2')->group(function () {

    #sales
    Route::get('get/client', [SalesDashboardControllerV2::class, 'getClientByMonthAndType']);
    Route::get('get/client-status', [SalesDashboardControllerV2::class, 'getClientStatus']);
    Route::get('get/client-program', [SalesDashboardControllerV2::class, 'getClientProgramByMonth']);
    Route::get('get/successful-program', [SalesDashboardControllerV2::class, 'getSuccessfulProgramByMonth']);
    Route::get('get/detail/successful-program', [SalesDashboardControllerV2::class, 'getSuccessfulProgramDetailByMonthAndProgram']);
    Route::get('get/admissions-mentoring', [SalesDashboardControllerV2::class, 'getAdmissionsProgramByMonth']);
    Route::get('get/initial-consultation', [SalesDashboardControllerV2::class, 'getInitialConsultationByMonth']);
    Route::get('get/academic-prep', [SalesDashboardControllerV2::class, 'getAcademicPrepByMonth']);
    Route::get('get/career-exploration', [SalesDashboardControllerV2::class, 'getCareerExplorationByMonth']);
    Route::get('get/conversion-lead', [SalesDashboardControllerV2::class, 'getConversionLeadByMonth']);
    Route::get('get/lead/admissions-mentoring', [SalesDashboardControllerV2::class, 'getLeadAdmissionsProgramByMonth']);
    Route::get('get/lead/academic-prep', [SalesDashboardControllerV2::class, 'getLeadAcademicPrepByMonth']);
    Route::get('get/lead/career-exploration', [SalesDashboardControllerV2::class, 'getLeadCareerExplorationByMonth']);
    Route::get('get/detail/client-program', [SalesDashboardControllerV2::class, 'getClientProgramByMonthDetail']);
    Route::get('get/all-program/target', [SalesDashboardControllerV2::class, 'getAllProgramTargetByMonth']);
    Route::get('get/program-comparison', [SalesDashboardControllerV2::class, 'compare_program']);

    Route::get('get/client-event', [SalesDashboardControllerV2::class, 'getClientEventByYear']);
    Route::get('get/outstanding-payment', [DashboardController::class, 'listOustandingPayments']);


    #Partnership
    Route::get('partner/total', [PartnerDashboardControllerV2::class, 'getTotalByMonth']);
    Route::get('partner/detail', [PartnerDashboardControllerV2::class, 'getPartnerDetailByMonth']);
    Route::get('partner/agenda', [PartnerDashboardControllerV2::class, 'getSpeakerByDate']);
    Route::get('partner/partnership-program', [PartnerDashboardControllerV2::class, 'getPartnershipProgramByMonth']);
    Route::get('partner/partnership-program/detail', [PartnerDashboardControllerV2::class, 'getPartnershipProgramDetailByMonth']);
    Route::get('partner/partnership-program/program-comparison', [PartnerDashboardControllerV2::class, 'getProgramComparison']);

    #Digital
    Route::get('digital/all-leads', [DigitalDashboardControllerV2::class, 'getDataLead']);
    Route::get('digital/leads', [DigitalDashboardControllerV2::class, 'getLeadDigital']);
    Route::get('digital/detail/type-lead', [DigitalDashboardControllerV2::class, 'getDetailDataLead']);
    Route::get('digital/detail/lead-source', [DigitalDashboardControllerV2::class, 'getDetailLeadSource']);
    Route::get('digital/detail/conversion-lead', [DigitalDashboardControllerV2::class, 'getDetailConversionLead']);

    #Finance
    Route::get('finance/detail', [FinanceDashboardControllerV2::class, 'getFinanceDetailByMonth']);
    Route::get('finance/total', [FinanceDashboardControllerV2::class, 'getTotalByMonth']);
    Route::get('finance/outstanding', [FinanceDashboardControllerV2::class, 'getOutstandingPayment']);
    Route::get('finance/revenue', [FinanceDashboardControllerV2::class, 'getRevenueByYear']);
    Route::get('finance/revenue/detail', [FinanceDashboardControllerV2::class, 'getRevenueDetailByMonth']);
    Route::get('finance/outstanding/period', [FinanceDashboardControllerV2::class, 'getOutstandingPaymentByPeriod']);

});
# Export data to google sheet
Route::group(['middleware' => 'auth:api', 'prefix' => 'export'], function () {

    // From mean type data {collection or model}
    Route::get('{type}/{from}', [GoogleSheetController::class, 'exportData']);
});

Route::get('/batch/{batchId}', [GoogleSheetController::class, 'findBatch'])->middleware(['auth:api']);

Route::middleware('auth:api')->get('sync/{type}', [GoogleSheetController::class, 'sync']);

Route::group(['middleware' => 'crm.key'], function () {
    Route::post('assessment/update', [ExtClientController::class, 'updateTookIA']);


});
