<?php

use App\Http\Controllers\Api\v1\ExtClientController;
use App\Http\Controllers\ClientEventController;
use App\Http\Controllers\ClientParentController;
use App\Http\Controllers\ClientStudentController;
use App\Http\Controllers\ClientTeacherCounselorController;
use App\Http\Controllers\CurrencyRateController;
use App\Http\Controllers\ExcelTemplateController;
use App\Http\Controllers\InvoiceProgramController;
use App\Http\Controllers\Module\ClientController;
use App\Http\Controllers\Api\v1\SchoolController as APISchoolController;
use App\Http\Controllers\Api\v1\ProgramController as APIProgramController;
use App\Http\Controllers\Api\v1\TagController as APITagController;
use App\Http\Controllers\Api\v1\ClientEventController as APIClientEventController;
use App\Http\Controllers\Api\v1\EventController as APIEventController;
use App\Http\Controllers\Api\v1\ExtClientProgramController;
use App\Http\Controllers\Api\v1\ExtCorporateController;
use App\Http\Controllers\Api\v1\ExtEventController;
use App\Http\Controllers\Api\v1\ExtPartnerController;
use App\Http\Controllers\Api\v1\ExtUniversityController;
use App\Http\Controllers\ClientProgramController;
use App\Http\Controllers\GoogleSheetController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolEventController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProgramPhaseController as APIProgramPhaseController;
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



Route::post('/upload', [InvoiceProgramController::class, 'upload']);

# import student, # import client event
Route::get('download/excel-template/{type}', [ExcelTemplateController::class, 'generateTemplate']);

# master / event
Route::get('master/event/{event}/school/{school}', [SchoolEventController::class]);

# client student menu

Route::get('client/{client}/programs', [ClientStudentController::class, 'getClientProgramByStudentId']);
Route::get('client/{client}/events', [ClientStudentController::class, 'getClientEventByStudentId']);
Route::get('client/{client}/logs', [ClientStudentController::class, 'getLogsClient']);

# Client teacher menu
Route::get('teacher/{teacher}/events', [ClientTeacherCounselorController::class, 'getClientEventByTeacherId']);


# create QR Code
// Route::get('create-qr/{size}', [ClientEventController::class, 'createQrCode']);



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




Route::group(['middleware' => 'crm.key'], function () {
    Route::post('assessment/update', [ExtClientController::class, 'updateTookIA']);
});


# Program Phase
//Route::group(['middleware' => 'auth:api'], function () {
    Route::delete('program-phase/{clientprog}/phase-detail/{phase_detail}/phase-lib/{phase_lib?}', [APIProgramPhaseController::class, 'fnRemoveProgramPhase']);
    Route::post('program-phase/{clientprog}/phase-detail/{phase_detail}/phase-lib/{phase_lib?}', [APIProgramPhaseController::class, 'fnStoreProgramPhase']);
    
    # Update quota for program phase
    Route::patch('program-phase/{clientprog}/phase-detail/{phase_detail}/phase-lib/{phase_lib?}/quota', [APIProgramPhaseController::class, 'fnUpdateQuotaProgramPhase']);

//});
