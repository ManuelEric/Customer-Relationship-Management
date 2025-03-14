<?php

use App\Http\Controllers\Api\v1\AcceptanceController as V1APIAcceptanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\ExtLeadController;
use App\Http\Controllers\Api\v1\ExtProgramController;
use App\Http\Controllers\Api\v1\ExtSalesTrackingController;
use App\Http\Controllers\Api\v1\ExtUserController;
use App\Http\Controllers\Api\v1\TagController as APITagController;
use App\Http\Controllers\Api\v1\ClientEventController as APIClientEventController;
use App\Http\Controllers\Api\v1\EventController as APIEventController;
use App\Http\Controllers\Api\v1\ExtClientProgramController;
use App\Http\Controllers\Api\v1\ExtCorporateController;
use App\Http\Controllers\Api\v1\ExtEventController;
use App\Http\Controllers\Api\v1\ExtPartnerController;
use App\Http\Controllers\Api\v1\ExtUniversityController;
use App\Http\Controllers\Api\v1\ExtClientController;
use App\Http\Controllers\Api\v1\SchoolController as APISchoolController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CurrencyRateController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\GoogleSheetController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ReceiptPartnerController;
use App\Http\Controllers\ReceiptReferralController;
use App\Http\Controllers\ReceiptSchoolController;
use App\Http\Controllers\Api\v1\AuthController as V1APIAuthController;
use App\Http\Controllers\Api\v1\CallbackController as V1APICallbackController;
use App\Http\Controllers\Api\v1\MentoringLogController as V1APIMentoringLogController;

Route::middleware(['throttle:120,1'])->group(function () {

    # auth
    Route::post('auth/login', [V1APIAuthController::class, 'login']);

    Route::get('get/parent-mentees', [ExtClientController::class, 'getParentMentee']);
    Route::get('get/alumni-mentees', [ExtClientController::class, 'getAlumniMentees']);
    Route::get('get/mentees', [ExtClientController::class, 'getClientFromAdmissionMentoring']);


    # detail of mentees
    Route::get('get/mentee/{user_client}', [ExtClientController::class, 'fnGetMenteeDetails']);
    Route::get('get/mentee/{user_client}/mentors', [ExtClientController::class, 'fnGetMentorsByMentee']);
    Route::get('get/mentee/{user_client}/programs', [ExtClientController::class, 'fnGetJoinedProgramsByMentee']);


    # try to use header fields for carrying the mentor ID information
    # so that we don't have to use /{mentor_id} or ?id=<mentor_id>
    Route::get('get/graduated/mentees', [ExtClientController::class, 'fnGetGraduatedMentee']);
    Route::get('get/active/mentees', [ExtClientController::class, 'fnGetActiveMentee']);


    Route::get('get/mentors', [ExtClientController::class, 'getMentors']);
    Route::get('get/alumnis', [ExtClientController::class, 'getAlumnis']);
    Route::get('get/detail/lead-source', [ExtSalesTrackingController::class, 'getLeadSourceDetail']);
    Route::get('get/detail/conversion-lead', [ExtSalesTrackingController::class, 'getConversionLeadDetail']);

    # used for form partner (Individual Professional)
    Route::get('get/user/uuid/{UUID}', [ExtUserController::class, 'cnGetUserByUUID']);

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

    # use for select data subsector corporate
    Route::get('get/subsectors/{industry}', [ExtCorporateController::class, 'cnGetSubSectorByIndustry']);

    # use for select data subject user agreement
    Route::get('get/subjects/{role}', [ExtUserController::class, 'cnGetSubjectsByRole']);
   
    # essay editing
    Route::get('essay/program/list', [ExtClientProgramController::class, 'fnGetSuccessEssayProgram']);
    Route::get('user/{role}/list', [ExtClientController::class, 'fnGetUserByRole']);
    Route::get('user/{role}/by/{uuid}', [ExtClientController::class, 'fnGetUserByRoleAndUUID']);

    # Get List referral / sub lead referral (All Client)
    Route::get('get/referral/list', [LeadController::class, 'fnGetListReferral']);

    # Get List school for select2 filter client student
    Route::get('get/school/list', [APISchoolController::class, 'fnGetListSchool']);

    # invoice program menu
    Route::get('current/rate/{base_currency}/{to_currency}', [CurrencyRateController::class, 'getCurrencyRate']);

    # Receipt
    Route::post('receipt-sch/{receipt}/upload/{currency}', [ReceiptSchoolController::class, 'upload_signed']);
    Route::post('receipt-ref/{receipt}/upload/{currency}', [ReceiptReferralController::class, 'upload_signed']);
    Route::post('receipt-corp/{receipt}/upload/{currency}', [ReceiptPartnerController::class, 'upload_signed']);

    # menus
    Route::get('employee/department/{department}', [DepartmentController::class, 'getEmployeeByDepartment']);
    Route::get('department/access/{department}/{user?}', [MenuController::class, 'fnGetMenuAccess']);

    Route::middleware(['auth:api'])->group(function () {
        # load progress for importing data from google sheets
        Route::get('/batch/{batchId}', [GoogleSheetController::class, 'findBatch']);
        
        # sync data to google sheets
        Route::get('sync/{type}', [GoogleSheetController::class, 'sync']);
    });

        # essay editing & timesheet API use
        Route::middleware(['resource:timesheet,editing'])->group(function () {
            Route::get('auth/email/check', [ExtClientController::class, 'checkUserEmail']);
            Route::post('auth/token', [ExtClientController::class, 'validateCredentials']);
        });
    
        # timesheet
        Route::middleware(  ['resource:timesheet'])->group(function () {
            Route::post('user/update', [ExtClientController::class, 'updateUser']);
    
            Route::get('user/mentor-tutors', [ExtClientController::class, 'getMentorTutors']);
            Route::get('user/mentor-tutors/{uuid}', [ExtClientController::class, 'showMentorTutor']);
    
            # main_program_name could be : academic, admissions
            Route::get('program/{main_program_name}/list', [ExtClientProgramController::class, 'getSuccessPrograms']);
            Route::get('program/list/free-trial', [ExtClientProgramController::class, 'fnGetFreeTrialPrograms']);
            Route::get('client/information/{uuid}', [ExtClientController::class, 'getClientInformation']);
        });
    
        # mentoring 
        Route::middleware(['resource:mentoring'])->group(function () {
            Route::get('student/{student}/acceptance', [V1APIAcceptanceController::class, 'fnListOfUniApplication']);
            Route::post('student/{student}/acceptance', [V1APIAcceptanceController::class, 'fnAddUni']);
            Route::put('student/{student}/acceptance/{acceptance}', [V1APIAcceptanceController::class, 'fnUpdateUni']);
            Route::delete('student/{student}/acceptance/{acceptance}', [V1APIAcceptanceController::class, 'fnDeleteUni']);
            
            # add/update google drive mentee
            Route::put('update/mentee/{user_client}/gdrive', [ExtClientController::class, 'fnUpdateMenteeGDriveLink']);
        });

        # meta ads
        // WebHook 
        Route::get('callback/facebook', [V1APICallbackController::class, 'verify']);
        Route::post('callback/facebook', [V1APICallbackController::class, 'read_lead']);

});