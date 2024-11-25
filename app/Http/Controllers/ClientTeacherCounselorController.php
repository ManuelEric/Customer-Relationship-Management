<?php

namespace App\Http\Controllers;

use App\Enum\LogModule;
use App\Http\Controllers\Module\ClientController;
use App\Http\Requests\StoreClientRawTeacherRequest;
use App\Http\Requests\StoreClientTeacherCounselorRequest;
use App\Http\Requests\StoreImportExcelRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\CurriculumRepositoryInterface;
use App\Interfaces\EdufLeadRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Interfaces\SchoolCurriculumRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Http\Traits\SyncClientTrait;
use App\Imports\TeacherImport;
use App\Models\School;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;

class ClientTeacherCounselorController extends ClientController
{
    use CreateCustomPrimaryKeyTrait;
    use StandardizePhoneNumberTrait;
    use LoggingTrait;
    use SyncClientTrait;
    protected SchoolRepositoryInterface $schoolRepository;
    protected CurriculumRepositoryInterface $curriculumRepository;
    protected LeadRepositoryInterface $leadRepository;
    protected EventRepositoryInterface $eventRepository;
    protected EdufLeadRepositoryInterface $edufLeadRepository;
    protected ClientRepositoryInterface $clientRepository;
    protected SchoolCurriculumRepositoryInterface $schoolCurriculumRepository;
    protected ClientEventRepositoryInterface $clientEventRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository, CurriculumRepositoryInterface $curriculumRepository, LeadRepositoryInterface $leadRepository, EventRepositoryInterface $eventRepository, EdufLeadRepositoryInterface $edufLeadRepository, ClientRepositoryInterface $clientRepository, SchoolCurriculumRepositoryInterface $schoolCurriculumRepository, ClientEventRepositoryInterface $clientEventRepository)
    {
        $this->schoolRepository = $schoolRepository;
        $this->curriculumRepository = $curriculumRepository;
        $this->leadRepository = $leadRepository;
        $this->eventRepository = $eventRepository;
        $this->edufLeadRepository = $edufLeadRepository;
        $this->schoolCurriculumRepository = $schoolCurriculumRepository;
        $this->clientRepository = $clientRepository;
        $this->clientEventRepository = $clientEventRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $status_client = $request->get('st');

            switch ($status_client) {

                case "inactive":
                    $model = $this->clientRepository->getInactiveTeacher(true);
                    break;

                default:
                    $model = $this->clientRepository->getTeachers(true);

            }

            return $this->clientRepository->getDataTables($model);
        }

        return view('pages.client.teacher.index');
    }

    public function indexRaw(Request $request)
    {
        if ($request->ajax()) {
            $model = $this->clientRepository->getAllRawClientDataTables('teacher/counselor', true, []);
            return $this->clientRepository->getDataTables($model, true);
        }

        return view('pages.client.teacher.raw.index');
    }

    public function create()
    {
        $schools = $this->schoolRepository->getAllSchools();
        $curriculums = $this->curriculumRepository->getAllCurriculums();
        $leads = $this->leadRepository->getAllMainLead();
        $events = $this->eventRepository->getAllEvents();
        $ext_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        // $listReferral = $this->clientRepository->getAllClients();

        return view('pages.client.teacher.form')->with(
            [
                'schools' => $schools,
                'curriculums' => $curriculums,
                'leads' => $leads,
                'events' => $events,
                'ext_edufair' => $ext_edufair,
                'kols' => $kols,
                // 'listReferral' => $listReferral
            ]
        );
    }

    public function store(StoreClientTeacherCounselorRequest $request, LogService $log_service)
    {
        $data = $this->initializeVariablesForStoreAndUpdate('teacher', $request);

        DB::beginTransaction();
        try {

            # case 1
            # create new school
            # when sch_id is "add-new" 
            if (!$data['teacher_details']['sch_id'] = $this->createSchoolIfAddNew($data['school_details']))
                throw new Exception('Failed to store new school', 1);


            # case 2
            # create new user client as teacher / counselor
            if (!$new_teacher = $this->clientRepository->createClient('Teacher/Counselor', $data['teacher_details']))
                throw new Exception('Failed to store new teacher / counselor', 2);

            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();

            switch ($e->getCode()) {
                case 1:
                    $log_service->createErrorLog(LogModule::STORE_SCHOOL_FROM_TEACHER, $e->getMessage(), $e->getLine(), $e->getFile(), $data['school_details']);
                    break;

                case 2:
                    $log_service->createErrorLog(LogModule::STORE_TEACHER, $e->getMessage(), $e->getLine(), $e->getFile(), $data['teacher_details']);
                    break;
            }

            $log_service->createErrorLog(LogModule::STORE_TEACHER, $e->getMessage(), $e->getLine(), $e->getFile(), $data['teacher_details']);
            return Redirect::to('client/teacher-counselor/create')->withError($e->getMessage());
        }

        # store Success
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_TEACHER, 'Teacher has been added', $data['teacher_details']);

        return Redirect::to('client/teacher-counselor')->withSuccess('A new teacher / counselor has been registered.');
    }

    public function show(Request $request)
    {
        $teacher_counselor_id = $request->route('teacher_counselor');
        $teacher_counselor = $this->clientRepository->getClientById($teacher_counselor_id);

        return view('pages.client.teacher.view')->with(
            [
                'teacher_counselor' => $teacher_counselor
            ]
        );
    }

    public function edit(Request $request)
    {
        $teacher_counselor_id = $request->route('teacher_counselor');
        $teacher_counselor = $this->clientRepository->getClientById($teacher_counselor_id);
        $schools = $this->schoolRepository->getAllSchools();
        $curriculums = $this->curriculumRepository->getAllCurriculums();
        $leads = $this->leadRepository->getAllMainLead();
        $events = $this->eventRepository->getAllEvents();
        $ext_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        // $listReferral = $this->clientRepository->getAllClients();

        return view('pages.client.teacher.form')->with(
            [
                'teacher_counselor' => $teacher_counselor,
                'schools' => $schools,
                'curriculums' => $curriculums,
                'leads' => $leads,
                'events' => $events,
                'ext_edufair' => $ext_edufair,
                'kols' => $kols,
                // 'listReferral' => $listReferral
            ]
        );
    }

    public function update(StoreClientTeacherCounselorRequest $request, LogService $log_service)
    {
        $new_teacher_counselor_details = $request->only([
            'first_name',
            'last_name',
            'mail',
            'phone',
            'dob',
            'insta',
            'state',
            'city',
            'postal_code',
            'address',
            'sch_id',
            'lead_id',
            'eduf_id',
            'kol_lead_id',
            'event_id',
            'st_levelinterest',
            'referral_code'
        ]);

        $new_teacher_counselor_details['phone'] = $this->tnSetPhoneNumber($request->phone);

        # set lead_id based on lead_id & kol_lead_id
        # when lead_id is kol
        # then put kol_lead_id to lead_id
        # otherwise
        # when lead_id is not kol 
        # then lead_id is lead_id
        if ($request->lead_id == "kol") {

            unset($new_teacher_counselor_details['lead_id']);
            $new_teacher_counselor_details['lead_id'] = $request->kol_lead_id;
        }
        unset($new_teacher_counselor_details['kol_lead_id']);
        // return $new_teacher_counselor_details;
        // exit;

        DB::beginTransaction();
        try {

            # set referral code null if lead != referral
            if ($new_teacher_counselor_details['lead_id'] != 'LS005'){
                $new_teacher_counselor_details['referral_code'] = null;
            }

            # case 1
            # create new school
            # when sch_id is "add-new" 
            if ($request->sch_id == "add-new") {

                $school_details = $request->only([
                    'sch_name',
                    // 'sch_location',
                    'sch_type',
                    'sch_score',
                ]);

                $last_id = School::max('sch_id');
                $school_id_without_label = $this->remove_primarykey_label($last_id, 4);
                $school_id_with_label = 'SCH-' . $this->add_digit($school_id_without_label + 1, 4);

                if (!$school = $this->schoolRepository->createSchool(['sch_id' => $school_id_with_label] + $school_details))
                    throw new Exception('Failed to store new school', 1);

                # insert school curriculum
                if (!$this->schoolCurriculumRepository->createSchoolCurriculum($school_id_with_label, $request->sch_curriculum))
                    throw new Exception('Failed to store school curriculum', 1);


                # remove field sch_id from student detail if exist
                unset($new_teacher_counselor_details['sch_id']);

                # create index sch_id to student details
                # filled with a new school id that was inserted before
                $new_teacher_counselor_details['sch_id'] = $school->sch_id;
            }


            # case 2
            # create new user client as teacher / counselor
            $teacher_counselor_id = $request->route('teacher_counselor');
            $old_teacher = $this->clientRepository->getClientById($teacher_counselor_id);
            if (!$this->clientRepository->updateClient($teacher_counselor_id, $new_teacher_counselor_details))
                throw new Exception('Failed to store new teacher / counselor', 2);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            switch ($e->getCode()) {
                case 1:
                    $log_service->createErrorLog(LogModule::UPDATE_SCHOOL_FROM_TEACHER, $e->getMessage(), $e->getLine(), $e->getFile(), $school_details);
                    break;

                case 2:
                    $log_service->createErrorLog(LogModule::UPDATE_TEACHER, $e->getMessage(), $e->getLine(), $e->getFile(), $new_teacher_counselor_details);
                    break;
            }

            $log_service->createErrorLog(LogModule::UPDATE_TEACHER, $e->getMessage(), $e->getLine(), $e->getFile(), $new_teacher_counselor_details);
            return Redirect::to('client/teacher-counselor/')->withError($e->getMessage());
        }

        # Update success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_TEACHER, 'Teacher has been updated', $new_teacher_counselor_details);

        return Redirect::to('client/teacher-counselor/' . $teacher_counselor_id)->withSuccess('A teacher / counselor\'s profile has been updated.');
    }

    public function updateStatus(Request $request, LogService $log_service)
    {
        $teacher_id = $request->route('teacher');
        $new_status = $request->route('status');

        # validate status
        if (!in_array($new_status, [0, 1])) {

            return response()->json(
                [
                    'success' => false,
                    'message' => "Status is invalid"
                ]
            );
        }

        DB::beginTransaction();
        try {

            $this->clientRepository->updateActiveStatus($teacher_id, $new_status);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_STATUS_TEACHER, $e->getMessage(), $e->getLine(), $e->getFile(), ['teacher_id' => $teacher_id, 'status' => $new_status]);

            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ]
            );
        }

        $log_service->createSuccessLog(LogModule::UPDATE_STATUS_TEACHER, 'Status teacher has been updated', ['teacher_id' => $teacher_id, 'status' => $new_status]);

        return response()->json(
            [
                'success' => true,
                'message' => "Status has been updated",
            ]
        );
    }

    public function getClientEventByTeacherId(Request $request)
    {
        $teacher_id = $request->route('teacher');
        return $this->clientEventRepository->getAllClientEventByClientIdDataTables($teacher_id);
    }

    public function cleaningData(Request $request, LogService $log_service)
    {
        $type = $request->route('type');
        $raw_client_id = $request->route('rawclient_id');
        $client_id = $request->route('client_id');

        DB::beginTransaction();
        try {

            $schools = $this->schoolRepository->getVerifiedSchools();

            $raw_client = $this->clientRepository->getViewRawClientById($raw_client_id);
            if (!isset($raw_client))
                return Redirect::to('client/teacher-counselor/raw')->withError('Data does not exist');

            if ($client_id != null){
                $client = $this->clientRepository->getViewClientById($client_id);
                if (!isset($client))
                    return Redirect::to('client/teacher-counselor/raw')->withError('Data does not exist');
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            $log_service->createErrorLog(LogModule::SELECT_RAW_TEACHER, $e->getMessage(), $e->getLine(), $e->getFile(), $raw_client->toArray());

            return Redirect::to('client/teacher-counselor/raw')->withError('Something went wrong. Please try again or contact the administrator.');
        }

        $log_service->createSuccessLog(LogModule::SELECT_RAW_TEACHER, 'Successfully fetch data raw teacher', $raw_client->toArray());

        switch ($type) {
            case 'comparison':
                return view('pages.client.teacher.raw.form-comparison')->with([
                    'rawClient' => $raw_client,
                    'client' => $client,
                    'schools' => $schools,
                ]);
                break;

            case 'new':
                return view('pages.client.teacher.raw.form-new')->with([
                    'rawClient' => $raw_client,
                    'schools' => $schools,
                ]);
                break;
        }
    }

    public function convertData(StoreClientRawTeacherRequest $request, LogService $log_service)
    {

        $type = $request->route('type');
        $client_id = $request->route('client_id');
        $raw_client_id = $request->route('rawclient_id');

        $name = $this->explodeName($request->nameFinal);

        $client_details = [
            'first_name' => $name['firstname'],
            'last_name' => isset($name['lastname']) ? $name['lastname'] : null,
            'mail' => $request->emailFinal,
            'phone' => $this->tnSetPhoneNumber($request->phoneFinal),
            'sch_id' => $request->schoolFinal,
            'is_verified' => 'Y'
        ];

        DB::beginTransaction();
        try {
            switch ($type) {
                case 'merge':

                    $this->clientRepository->updateClient($client_id, $client_details);

                    $raw_teacher = $this->clientRepository->getViewRawClientById($raw_client_id);

                    # delete parent from raw client
                    $this->clientRepository->deleteClient($raw_client_id);

                    break;

                case 'new':
                    $raw_teacher = $this->clientRepository->getViewRawClientById($raw_client_id);
                    $lead_id = $raw_teacher->lead_id;
                    $register_by = $raw_teacher->register_by;

                    $client_details['lead_id'] = $lead_id;
                    $client_details['register_by'] = $register_by;

                    $new_teacher = $this->clientRepository->updateClient($raw_client_id, $client_details);

                    break;
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Convert client failed : ' . $e->getMessage() . ' ' . $e->getLine());
            return Redirect::to('client/teacher-counselor/raw')->withError('Something went wrong. Please try again or contact the administrator.');
        }

        $log_service->createSuccessLog(LogModule::VERIFIED_RAW_TEACHER, 'Raw teacher has been verified', $client_details);

        return Redirect::to('client/teacher-counselor/'. (isset($client_id) ? $client_id : $raw_client_id))->withSuccess('Convert client successfully.');
    }

    public function destroy(Request $request, LogService $log_service)
    {
        $client_id = $request->route('teacher_counselor');
        $client = $this->clientRepository->getClientById($client_id);

        DB::beginTransaction();
        try {

            if (!isset($client))
                return Redirect::back()->withError('Data does not exist');
    
            $this->clientRepository->deleteClient($client_id);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_TEACHER, $e->getMessage(), $e->getLine(), $e->getFile(), $client->toArray());

            return Redirect::back()->withError('Failed to delete teacher');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_TEACHER, 'Teacher has been deleted', $client->toArray());

        return Redirect::back()->withSuccess('Teacher/Counselor successfully deleted');
    }

    public function destroyRaw(Request $request, LogService $log_service)
    {
        $raw_client_id = $request->route('rawclient_id');
        $raw_teacher = $this->clientRepository->getViewRawClientById($raw_client_id);

        DB::beginTransaction();
        try {

            if (!isset($raw_teacher))
                return Redirect::to('client/teacher-counselor/raw')->withError('Data does not exist');


            $this->clientRepository->deleteClient($raw_client_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_RAW_TEACHER, $e->getMessage(), $e->getLine(), $e->getFile(), $raw_teacher->toArray());

            return Redirect::to('client/teacher-counselor/raw')->withError('Failed to delete raw teacher');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_RAW_TEACHER, 'Raw teacher has been deleted', $raw_teacher->toArray());

        return Redirect::to('client/teacher-counselor/raw')->withSuccess('Raw teacher successfully deleted');
    }
}
