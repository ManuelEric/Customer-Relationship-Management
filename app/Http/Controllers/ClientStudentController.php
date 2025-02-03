<?php

namespace App\Http\Controllers;

use App\Enum\LogModule;
use App\Http\Controllers\Module\ClientController;
use App\Http\Requests\AddParentRequest;
use App\Http\Requests\StoreClientRawRequest;
use App\Http\Requests\StoreClientRawStudentRequest;
use App\Http\Requests\StoreClientStudentRequest;
use App\Http\Requests\StoreImportExcelRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\FindStatusClientTrait;
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Http\Traits\SyncClientTrait;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\CountryRepositoryInterface;
use App\Interfaces\CurriculumRepositoryInterface;
use App\Interfaces\EdufLeadRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Interfaces\MajorRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SchoolCurriculumRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\TagRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\InitialProgramRepositoryInterface;
use App\Interfaces\ClientLeadTrackingRepositoryInterface;
use App\Interfaces\ReasonRepositoryInterface;
use App\Imports\StudentImport;
use App\Interfaces\UserRepositoryInterface;
use App\Jobs\Client\ProcessInsertLogClient;
use App\Jobs\Client\ProcessUpdateClientEventRawStudent;
use App\Jobs\Client\ProcessUpdateClientProgramRawStudent;
use App\Models\ClientLeadTracking;
use App\Services\Log\LogService;
use App\Services\Master\ProgramService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ClientStudentController extends ClientController
{
    use CreateCustomPrimaryKeyTrait;
    use FindStatusClientTrait;
    use StandardizePhoneNumberTrait;
    use LoggingTrait;
    use SyncClientTrait;

    protected ClientRepositoryInterface $clientRepository;
    protected SchoolRepositoryInterface $schoolRepository;
    private LeadRepositoryInterface $leadRepository;
    private EventRepositoryInterface $eventRepository;
    private EdufLeadRepositoryInterface $edufLeadRepository;
    private ProgramRepositoryInterface $programRepository;
    private UniversityRepositoryInterface $universityRepository;
    private MajorRepositoryInterface $majorRepository;
    private CurriculumRepositoryInterface $curriculumRepository;
    protected TagRepositoryInterface $tagRepository;
    private SchoolCurriculumRepositoryInterface $schoolCurriculumRepository;
    private ClientProgramRepositoryInterface $clientProgramRepository;
    private CountryRepositoryInterface $countryRepository;
    private ClientEventRepositoryInterface $clientEventRepository;
    private InitialProgramRepositoryInterface $initialProgramRepository;
    private ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository;
    private ReasonRepositoryInterface $reasonRepository;
    private UserRepositoryInterface $userRepository;
    private ProgramService $programService;

    public function __construct(ClientRepositoryInterface $clientRepository, SchoolRepositoryInterface $schoolRepository, LeadRepositoryInterface $leadRepository, EventRepositoryInterface $eventRepository, EdufLeadRepositoryInterface $edufLeadRepository, ProgramRepositoryInterface $programRepository, UniversityRepositoryInterface $universityRepository, MajorRepositoryInterface $majorRepository, CurriculumRepositoryInterface $curriculumRepository, TagRepositoryInterface $tagRepository, SchoolCurriculumRepositoryInterface $schoolCurriculumRepository, ClientProgramRepositoryInterface $clientProgramRepository, CountryRepositoryInterface $countryRepository, ClientEventRepositoryInterface $clientEventRepository, InitialProgramRepositoryInterface $initialProgramRepository, ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository, ReasonRepositoryInterface $reasonRepository, UserRepositoryInterface $userRepository, ProgramService $programService)
    {
        $this->clientRepository = $clientRepository;
        $this->schoolRepository = $schoolRepository;
        $this->leadRepository = $leadRepository;
        $this->eventRepository = $eventRepository;
        $this->edufLeadRepository = $edufLeadRepository;
        $this->programRepository = $programRepository;
        $this->universityRepository = $universityRepository;
        $this->majorRepository = $majorRepository;
        $this->curriculumRepository = $curriculumRepository;
        $this->tagRepository = $tagRepository;
        $this->schoolCurriculumRepository = $schoolCurriculumRepository;
        $this->clientProgramRepository = $clientProgramRepository;
        $this->countryRepository = $countryRepository;
        $this->clientEventRepository = $clientEventRepository;
        $this->initialProgramRepository = $initialProgramRepository;
        $this->clientLeadTrackingRepository = $clientLeadTrackingRepository;
        $this->reasonRepository = $reasonRepository;
        $this->userRepository = $userRepository;
        $this->programService = $programService;
    }

    # ajax start
    public function getClientProgramByStudentId(Request $request)
    {
        $student_id = $request->route('client');
        return $this->clientProgramRepository->getAllClientProgramDataTables_DetailUser(['clientId' => $student_id]);
    }

    public function getClientEventByStudentId(Request $request)
    {
        $student_id = $request->route('client');
        return $this->clientEventRepository->getAllClientEventByClientIdDataTables($student_id);
    }
    # ajax end

    public function index(Request $request)
    {
        $status_client = $request->get('st');
        if ($request->ajax()) {

            $as_datatables = true;

            # advanced filter purpose
            $school_name = $request->get('school_name');
            $graduation_year = $request->get('graduation_year');
            $leads = $request->get('lead_source');
            $initial_programs = $request->get('program_suggest');
            $status_lead = $request->get('status_lead');
            $active_status = $request->get('active_status');
            $pic = $request->get('pic');
            $start_joined_date = $request->get('start_joined_date');
            $end_joined_date = $request->get('end_joined_date');

            # array for advanced filter request
            $advanced_filter = [
                'school_name' => $school_name,
                'graduation_year' => $graduation_year,
                'leads' => $leads,
                'initial_programs' => $initial_programs,
                'status_lead' => $status_lead,
                'active_status' => $active_status,
                'pic' => $pic,
                'start_joined_date' => $start_joined_date,
                'end_joined_date' => $end_joined_date
            ];

            switch ($status_client) {

                case "new-leads":
                    $model = $this->clientRepository->getNewLeads($as_datatables, null, $advanced_filter);
                    break;

                case "potential":
                    $model = $this->clientRepository->getPotentialClients($as_datatables, null, $advanced_filter);
                    break;

                case "mentee":
                    $model = $this->clientRepository->getExistingMentees($as_datatables, null, $advanced_filter);
                    break;

                case "non-mentee":
                    $model = $this->clientRepository->getExistingNonMentees($as_datatables, null, $advanced_filter);
                    break;

                case "inactive":
                    $model = $this->clientRepository->getInactiveStudent($as_datatables, null, $advanced_filter);
                    break;

                default:
                    $model = $this->clientRepository->getAllClientStudent($advanced_filter, $as_datatables);
            }

            return $this->clientRepository->getDataTables($model);
        }

        $entries = app('App\Services\ClientStudentService')->advancedFilterClient();

        return view('pages.client.student.index')->with($entries + ['st' => $status_client]);
    }

    public function indexRaw(Request $request)
    {
        if ($request->ajax()) {

            # advanced filter purpose
            $school_name = $request->get('school_name');
            $grade = $request->get('grade');
            $graduation_year = $request->get('graduation_year');
            $leads = $request->get('lead_source');
            $initial_programs = $request->get('program_suggest');
            $status_lead = $request->get('status_lead');
            $active_status = $request->get('active_status');
            $roles = $request->get('roles');
            $start_joined_date = $request->get('start_joined_date');
            $end_joined_date = $request->get('end_joined_date');

            # array for advanced filter request
            $advanced_filter = [
                'school_name' => $school_name,
                'grade' => $grade,
                'graduation_year' => $graduation_year,
                'leads' => $leads,
                'initial_programs' => $initial_programs,
                'status_lead' => $status_lead,
                'active_status' => $active_status,
                'roles' => $roles,
                'start_joined_date' => $start_joined_date,
                'end_joined_date' => $end_joined_date
            ];

            $model = $this->clientRepository->getAllRawClientDataTables('student', true, $advanced_filter);
            return $this->clientRepository->getDataTables($model, true);
        }

        $entries = app('App\Services\ClientStudentService')->advancedFilterClient();

        return view('pages.client.student.raw.index')->with($entries);
    }

    public function show(Request $request)
    {
                
        $student_id = $request->route('student');
        $student = $this->clientRepository->getClientById($student_id);

        # validate
        # if user forced to access student that isn't his/her 
        if (!$this->clientRepository->findHandledClient($student_id))
            abort(403);

        $initial_programs = $this->initialProgramRepository->getAllInitProg();
        // $history_leads = $this->clientLeadTrackingRepository->getHistoryClientLead($student_id);
        $view_student = $this->clientRepository->getViewClientById($student_id);

        $programs = $this->programService->snGetProgramsB2c();
        
        $sales_teams = $this->userRepository->rnGetAllUsersByDepartmentAndRole('Employee', 'Client Management');

        $initial_programs = $this->initialProgramRepository->getAllInitProg();
        $history_leads = $this->clientLeadTrackingRepository->getHistoryClientLead($student_id);

        $parents = $this->clientRepository->getAllClientByRole('Parent');

        $pic_active = null;
        if (count($student->picClient) > 0){
            $pic_active = $student->picClient->where('status', 1)->first();
        }

        if (!$student)
            abort(500);

        return view('pages.client.student.view')->with(
            [
                'student' => $student,
                'initialPrograms' => $initial_programs,
                'historyLeads' => $history_leads,
                'viewStudent' => $view_student,
                'programs' => $programs,
                'salesTeams' => $sales_teams,
                'picActive' => $pic_active,
                'parents' => $parents
            ]
        );
    }

    public function store(StoreClientStudentRequest $request, LogService $log_service)
    {
        $data = $this->initializeVariablesForStoreAndUpdate('student', $request);
        $data['student_details']['register_by'] == null ? $data['student_details']['register_by'] = 'student' : $data['student_details']['register_by'];

        DB::beginTransaction();
        try {

            # case 1
            # create new school
            if (!$data['student_details']['sch_id'] = $this->createSchoolIfAddNew($data['school_details']))
                throw new Exception('Failed to store new school', 1);

            # case 2
            # create new user client as parents
            // if ($data['student_details']['pr_id'] !== NULL) {

            //     if (!$parent_id = $this->createParentsIfAddNew($data['parent_details'], $data['student_details']))
            //         throw new Exception('Failed to store new parent', 2);
            // }

            # case 3
            # create new user client as student
            if (!$new_student_details = $this->clientRepository->createClient('Student', $data['student_details']))
                throw new Exception('Failed to store new student', 3);

            $new_student_id = $new_student_details->id;

            # initiate variable for client log
            $clients_data_for_log_client[] = [
                'client_id' => $new_student_id,
                'first_name' => $data['student_details']['first_name'],
                'last_name' => $data['student_details']['last_name'],
                'lead_source' => $data['student_details']['lead_id'],
                'inputted_from' => 'manual',
                'clientprog_id' => null,
                
            ];
            
            # trigger to insert log client
            ProcessInsertLogClient::dispatch($clients_data_for_log_client)->onQueue('insert-log-client');
            
            # case 4 (optional)
            # add relation between parent and student
            # if they didn't insert parents which parentId = NULL
            # then assumed that register for student only
            # so no need to create parent children relation
            // if ($parent_id !== NULL && $data['student_details']['pr_id'] !== NULL) {

            //     if (!$this->clientRepository->createClientRelation($parent_id, $new_student_id))
            //         throw new Exception('Failed to store relation between student and parent', 4);
            // }

            # case 5
            # create interested program
            # if they didn't insert interested program 
            # then skip this case
            // if (!$this->createInterestedProgram($data['interestPrograms'], $new_student_id))
            //     throw new Exception('Failed to store interest program', 5);

            # case 6.1
            # create destination countries
            # if they didn't insert destination countries
            # then skip this case
            if (!$this->createDestinationCountries($data['abroad_countries'], $new_student_id))
                throw new Exception('Failed to store destination country', 6);

            # case 6.2
            # create interested universities
            # if they didn't insert universities
            # then skip this case
            if (!$this->createInterestedUniversities($data['abroad_universities'], $new_student_id))
                throw new Exception('Failed to store interest universities', 6);

            # case 7
            # create interested major
            # if they didn't insert major
            # then skip this case
            if (!$this->createInterestedMajor($data['interest_majors'], $new_student_id))
                throw new Exception('Failed to store interest major', 7);

            # case 8
            # Set default PIC if sales member add student
            if (Session::get('user_role') == 'Employee') {
                $pic_details[] = [
                    'client_id' => $new_student_id,
                    'user_id' => auth()->user()->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                $this->clientRepository->insertPicClient($pic_details);
            }

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            switch ($e->getCode()) {
                case 1:
                    $log_service->createErrorLog(LogModule::STORE_SCHOOL_FROM_STUDENT, $e->getMessage(), $e->getLine(), $e->getFile(), $data['school_details']);
                    break;
                    
                case 2:
                    $log_service->createErrorLog(LogModule::STORE_PARENT_FROM_STUDENT, $e->getMessage(), $e->getLine(), $e->getFile(), $data['parent_details']);
                    break;
                    
                case 3:
                    $log_service->createErrorLog(LogModule::STORE_STUDENT, $e->getMessage(), $e->getLine(), $e->getFile(), $data['student_details']);
                    break;

                case 4:
                    $log_service->createErrorLog(LogModule::STORE_RELATION_FROM_STUDENT, $e->getMessage(), $e->getLine(), $e->getFile(), $data['parent_details']);
                    break;

                    // case 5:
                    //     Log::error('Store interest programs failed : ' . $e->getMessage());
                    //     break;

                case 6:
                    $log_service->createErrorLog(LogModule::STORE_UNIVERSITY_FROM_STUDENT, $e->getMessage(), $e->getLine(), $e->getFile(), $data['abroad_universities']);
                    break;

                case 7:
                    $log_service->createErrorLog(LogModule::STORE_MAJOR_FROM_STUDENT, $e->getMessage(), $e->getLine(), $e->getFile(), $data['interest_majors']);
                    break;
            }

            $log_service->createErrorLog(LogModule::STORE_STUDENT, $e->getMessage(), $e->getLine(), $e->getFile(), $data['student_details']);
            return Redirect::to('client/student/create')->withError($e->getMessage());
        }

        # store Success
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_STUDENT, 'New student has been added', $data['student_details']);

        return Redirect::to('client/student?st=new-leads')->withSuccess('A new student has been registered.');
    }

    public function create(Request $request)
    {
        # ajax
        # to get university by selected country
        if ($request->ajax()) {
            $universities = $this->universityRepository->getAllUniversitiesByTag($request->country);
            return response()->json($universities);
        }

        $schools = $this->schoolRepository->getAllSchools();
        $curriculums = $this->curriculumRepository->getAllCurriculums();
        $parents = $this->clientRepository->getAllClientByRole('Parent');
        $leads = $this->leadRepository->getAllMainLead();
        $events = $this->eventRepository->getAllEvents();
        $ext_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();

        $programs = $this->programService->snGetProgramsB2c();
        $tags = $this->tagRepository->getAllTags();
        $majors = $this->majorRepository->getAllActiveMajors();
        $regions = $this->countryRepository->getAllRegionByLocale('en');

        return view('pages.client.student.form')->with(
            [
                'schools' => $schools,
                'curriculums' => $curriculums,
                'parents' => $parents,
                'leads' => $leads,
                'events' => $events,
                'ext_edufair' => $ext_edufair,
                'kols' => $kols,
                'programs' => $programs,
                'tags' => $tags,
                'majors' => $majors,
                'regions' => $regions,
            ]
        );
    }

    public function edit(Request $request)
    {
        # ajax
        # to get university by selected country
        if ($request->ajax()) {
            $universities = $this->universityRepository->getAllUniversitiesByTag($request->country);
            return response()->json($universities);
        }
        $student_id = $request->route('student');
        $student = $this->clientRepository->getClientById($student_id);
        $view_student = $this->clientRepository->getViewClientById($student_id);

        $schools = $this->schoolRepository->getAllSchools();
        $curriculums = $this->curriculumRepository->getAllCurriculums();
        $parents = $this->clientRepository->getAllClientByRole('Parent');
        $leads = $this->leadRepository->getAllMainLead();
        $events = $this->eventRepository->getAllEvents();
        $ext_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        $programs = $this->programService->snGetProgramsB2c();
        $tags = $this->tagRepository->getAllTags();
        $majors = $this->majorRepository->getAllMajors();

        return view('pages.client.student.form')->with(
            [
                'student' => $student,
                'viewStudent' => $view_student,
                'schools' => $schools,
                'curriculums' => $curriculums,
                'parents' => $parents,
                'leads' => $leads,
                'events' => $events,
                'ext_edufair' => $ext_edufair,
                'kols' => $kols,
                'programs' => $programs,
                'tags' => $tags,
                'majors' => $majors,
            ]
        );
    }

    public function update(StoreClientStudentRequest $request, LogService $log_service)
    {
        $data = $this->initializeVariablesForStoreAndUpdate('student', $request);

        $student_id = $request->route('student');
        $old_Student = $this->clientRepository->getClientById($student_id);

        $leads_tracking = $this->clientLeadTrackingRepository->getCurrentClientLead($student_id);

        DB::beginTransaction();
        try {

            # set referral code null if lead != referral
            if ($data['student_details']['lead_id'] != 'LS005'){
                $data['student_details']['referral_code'] = null;
            }

            //! perlu nunggu 1 menit dlu sampai ada client lead tracking status yg 1
            # update status client lead tracking
            if ($leads_tracking->count() > 0) {
                foreach ($leads_tracking as $lead_tracking) {
                    $this->clientLeadTrackingRepository->updateClientLeadTrackingById($lead_tracking->id, ['status' => 0]);
                }
            }

            # case 1
            # create new school
            # when sch_id is "add-new" 
            if (!$data['student_details']['sch_id'] = $this->createSchoolIfAddNew($data['school_details']))
                throw new Exception('Failed to store new school', 1);

            # case 2
            # create new user client as parents
            # when pr_id is "add-new" 

            // if ($data['student_details']['pr_id'] !== NULL) {
            //     if ($data['student_details']['lead_id'] != 'LS005'){
            //         $data['parent_details']['referral_code'] = null;
            //     }
            //     if (!$parent_id = $this->createParentsIfAddNew($data['parent_details'], $data['student_details']))
            //         throw new Exception('Failed to store new parent', 2);
            // }


            # removing the kol_lead_id & pr_id from student_details array
            # if the data still exists it will error because there are no field with kol_lead_id & pr_id
            unset($data['student_details']['kol_lead_id']);
            // $newParentId = $data['student_details']['pr_id'];
            // $oldParentId = $data['student_details']['pr_id_old'];
            // unset($data['student_details']['pr_id']);
            // unset($data['student_details']['pr_id_old']);

            # case 3
            # create new user client as student
            if (!$student = $this->clientRepository->updateClient($student_id, $data['student_details']))
                throw new Exception('Failed to update student information', 3);


            # case 4
            # add relation between parent and student
            # if they didn't insert parents which parentId = NULL
            # then assumed that register for student only
            # so no need to create parent children relation
            // if ($newParentId !== NULL) {

            //     if (!in_array($parent_id, $this->clientRepository->getParentsByStudentId($student_id))) {

            //         if (!$this->clientRepository->createClientRelation($parent_id, $student_id))
            //             throw new Exception('Failed to store relation between student and parent', 4);
            //     }
            // } else {

            //     # when pr_id is null it means they remove the parent from the child
            //     if (in_array($oldParentId, $this->clientRepository->getParentsByStudentId($student_id))) {

            //         if (!$this->clientRepository->removeClientRelation($oldParentId, $student_id))
            //             throw new Exception('Failed to remove relation between student and parent', 4);
            //     }
            // }

            # case 5
            # create interested program
            # if they didn't insert interested program 
            # then skip this case
            // if (!$this->createInterestedProgram($data['interestPrograms'], $student_id))
            //     throw new Exception('Failed to store interest program', 5);

            # case 6.1
            # create destination countries
            # if they didn't insert destination countries
            # then skip this case
            if (!$this->createDestinationCountries($data['abroad_countries'], $student_id))
                throw new Exception('Failed to store destination country', 6);

            # case 6.2
            # create interested universities
            # if they didn't insert universities
            # then skip this case
            if (!$this->createInterestedUniversities($data['abroad_universities'], $student_id))
                throw new Exception('Failed to store interest universities', 6);


            # case 7
            # create interested major
            # if they didn't insert major
            # then skip this case
            if (!$this->createInterestedMajor($data['interest_majors'], $student_id))
                throw new Exception('Failed to store interest major', 7);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            switch ($e->getCode()) {
                case 1:
                    $log_service->createErrorLog(LogModule::UPDATE_SCHOOL_FROM_STUDENT, $e->getMessage(), $e->getLine(), $e->getFile(), $data['school_details']);
                    break;

                case 2:
                    $log_service->createErrorLog(LogModule::UPDATE_PARENT_FROM_STUDENT, $e->getMessage(), $e->getLine(), $e->getFile(), $data['parent_details']);
                    break;

                case 3:
                    $log_service->createErrorLog(LogModule::UPDATE_STUDENT, $e->getMessage(), $e->getLine(), $e->getFile(), $data['student_details']);
                    break;

                case 4:
                    $log_service->createErrorLog(LogModule::UPDATE_RELATION_FROM_STUDENT, $e->getMessage(), $e->getLine(), $e->getFile(), $data['parent_details']);
                    break;

                    // case 5:
                    //     Log::error('Update interest programs failed : ' . $e->getMessage());
                    //     break;

                case 6:
                    $log_service->createErrorLog(LogModule::UPDATE_UNIVERSITY_FROM_STUDENT, $e->getMessage(), $e->getLine(), $e->getFile(), $data['abroad_universities']);
                    break;

                case 7:
                    $log_service->createErrorLog(LogModule::UPDATE_MAJOR_FROM_STUDENT, $e->getMessage(), $e->getLine(), $e->getFile(), $data['interest_majors']);
                    break;
            }

            $log_service->createErrorLog(LogModule::UPDATE_STUDENT, $e->getMessage(), $e->getLine(), $e->getFile(), $data['student_details']);
            return Redirect::to('client/student/' . $student_id . '/edit')->withError($e->getMessage());
        }

        # Update success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_STUDENT, 'Student has been updated', $data['student_details']);

        return Redirect::to('client/student/' . $student_id)->withSuccess('A student\'s profile has been updated.');
    }

    public function updateStatus(Request $request, LogService $log_service)
    {
        $student_id = $request->route('student');
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

            $this->clientRepository->updateActiveStatus($student_id, $new_status);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_STATUS_STUDENT, $e->getMessage(), $e->getLine(), $e->getFile(), ['student_id' => $student_id, 'status' => $new_status]);

            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ]
            );
        }

        # Upload success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_STATUS_STUDENT, 'Status student has been updated', ['student_id' => $student_id, 'status' => $new_status]);

        return response()->json(
            [
                'success' => true,
                'message' => "Status has been updated",
            ]
        );
    }

    public function updateLeadStatus(Request $request, LogService $log_service)
    {
        $student_id = $request->clientId;
        $init_prog_name = $request->initProg;
        $lead_status = $request->leadStatus;
        
        
        $group_id = $request->groupId;
        $reason = $request->reason_id;
        $program_score = $lead_score = 0;

        $rules = [
            'reason_id' => 'required',
            'leadStatus' => 'required|in:hot,warm,cold',
        ];

        $validator = Validator::make($request->toArray(), $rules);
        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'code' => 400,
                    'message' => $validator->messages()
                ]
            );
        }

        if ($reason == 'other') {
            $other_reason = $this->reasonRepository->createReason(['reason_name' => $request->other_reason, 'type' => 'Hot Lead']);
            $reason = $other_reason->reason_id;
        }

        $init_prog = $this->initialProgramRepository->getInitProgByName($init_prog_name);

        $program_tracking = $this->clientLeadTrackingRepository->getLatestClientLeadTrackingByType('Program', $group_id);
        $lead_tracking = $this->clientLeadTrackingRepository->getLatestClientLeadTrackingByType('Lead', $group_id);

        switch ($lead_status) {
            case 'hot':
                $program_score = 0.99;
                $lead_score = 0.99;
                break;

            case 'warm':
                $program_score = 0.51;
                $lead_score = 0.64;
                break;

            case 'cold':
                $program_score = 0.49;
                $lead_score = 0.34;
                break;
        }

        $last_id = ClientLeadTracking::max('group_id');
        $group_id_without_label = $last_id ? $this->remove_primarykey_label($last_id, 5) : '00000';
        $group_id_with_label = 'CLT-' . $this->add_digit($group_id_without_label + 1, 5);


        $program_details = [
            'group_id' => $group_id_with_label,
            'client_id' => $student_id,
            'initialprogram_id' => $init_prog->id,
            'type' => 'Program',
            'total_result' => $program_score,
            'status' => 1
        ];

        $lead_status_details = [
            'group_id' => $group_id_with_label,
            'client_id' => $student_id,
            'initialprogram_id' => $init_prog->id,
            'type' => 'Lead',
            'total_result' => $lead_score,
            'status' => 1
        ];

        DB::beginTransaction();
        try {

            $this->clientLeadTrackingRepository->updateClientLeadTrackingById($program_tracking->id, ['status' => 0, 'reason_id' => $reason]);
            $this->clientLeadTrackingRepository->updateClientLeadTrackingById($lead_tracking->id, ['status' => 0, 'reason_id' => $reason]);

            $this->clientLeadTrackingRepository->createClientLeadTracking($program_details);
            $this->clientLeadTrackingRepository->createClientLeadTracking($lead_status_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_LEAD_STATUS_STUDENT, $e->getMessage(), $e->getLine(), $e->getFile(), $lead_status_details);

            Log::error('Update lead status client failed : ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'code' => 500,
                    'message' => $e->getMessage()
                ]
            );
        }

        # Upload success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_LEAD_STATUS_STUDENT, 'Lead status has been updated', $lead_status_details);

        return response()->json(
            [
                'success' => true,
                'code' => 200,
                'message' => 'Lead status has been updated',
            ]
        );
    }

    public function siblings(Request $request)
    {
        $clients = $this->clientRepository->getAlumniMenteesSiblings();
        return $clients;
    }

    public function addInterestProgram(Request $request, LogService $log_service)
    {
        $student_id = $request->route('student');

        DB::beginTransaction();
        try {

            $created_interest_program = $this->clientRepository->addInterestProgram($student_id, $request->interest_program);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_INTEREST_PROGRAM_FROM_STUDENT, $e->getMessage(), $e->getLine(), $e->getFile(), ['student_id' => $student_id, 'interest_program' => $request->interest_program]);

            return Redirect::to('client/student/' . $student_id)->withError('Interest program failed to be added.');
        }

        # Add interest program success
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_INTEREST_PROGRAM_FROM_STUDENT, 'Interest program has been added', ['student_id' => $student_id, 'interest_program' => $request->interest_program]);

        return Redirect::to('client/student/' . $student_id)->withSuccess('Interest program successfully added.');
    }

    public function removeInterestProgram(Request $request, LogService $log_service)
    {
        $student_id = $request->route('student');
        $interest_program_id = $request->route('interest_program');
        $prog_id = $request->route('prog');

        DB::beginTransaction();
        try {

            $this->clientRepository->removeInterestProgram($student_id, $interest_program_id, $prog_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_INTEREST_PROGRAM_FROM_STUDENT, $e->getMessage(), $e->getLine(), $e->getFile(), ['interest_program' => $interest_program_id, 'prog_id' => $prog_id]);

            return Redirect::to('client/student/' . $student_id)->withError('Interest program failed to be removed.');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_INTEREST_PROGRAM_FROM_STUDENT, 'Interest program has been removed', ['interest_program' => $interest_program_id, 'prog_id' => $prog_id]);

        return Redirect::to('client/student/' . $student_id)->withSuccess('interest program successfully removed.');
    }

    public function addParent(AddParentRequest $request, LogService $log_service)
    {
        $student_id = $request->route('student');

        $parent_details = $request->only([
            'existing_parent',
            'pr_id',
            'first_name',
            'last_name',
            'mail',
            'phone'
        ]);

        $parent_details['phone'] = $this->tnSetPhoneNumber($request->phone);

        DB::beginTransaction();
        try {

            # Parent Existing
            if($parent_details['existing_parent'] == 1) {

                $this->clientRepository->createManyClientRelation($parent_details['pr_id'], [$student_id]);

            } else { 
                unset($parent_details['existing_parent']);
                unset($parent_details['pr_id']);

                $newParent = $this->clientRepository->createClient('Parent', $parent_details);

                $this->clientRepository->createManyClientRelation($newParent->id, [$student_id]);

            }

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::ADD_PARENT_FROM_STUDENT, $e->getMessage(), $e->getLine(), $e->getFile(), $parent_details);

            return Redirect::to('client/student/' . $student_id)->withError('Parent failed to be added.');
        }

        # Add interest program success
        # create log success
        $log_service->createSuccessLog(LogModule::ADD_PARENT_FROM_STUDENT, 'Parent has been added', $parent_details);

        return Redirect::to('client/student/' . $student_id)->withSuccess('Parent successfully added.');
    }

    public function disconnectParent(Request $request, LogService $log_service)
    {
        $student_id = $request->route('student');
        $parent_id = $request->route('parent');

        DB::beginTransaction();
        try {

            $this->clientRepository->removeClientRelation($parent_id, $student_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DISCONNECT_PARENT_FROM_STUDENT, $e->getMessage(), $e->getLine(), $e->getFile(), ['student_id' => $student_id, 'parent_id' => $parent_id]);

            return Redirect::to('client/student/' . $student_id)->withError('failed to be diconnect parent.');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DISCONNECT_PARENT_FROM_STUDENT, 'Successfully diconnect parent', ['student_id' => $student_id, 'parent_id' => $parent_id]);

        return Redirect::to('client/student/' . $student_id)->withSuccess('Successfully disconnect parent.');
    }

    public function cleaningData(Request $request, LogService $log_service)
    {
        $type = $request->route('type');
        $raw_client_id = $request->route('rawclient_id');
        $client_id = $request->route('client_id');

        DB::beginTransaction();
        try {

            $schools = $this->schoolRepository->getVerifiedSchools();
            $parents = $this->clientRepository->getAllClientByRole('Parent');

            $raw_client = $this->clientRepository->getViewRawClientById($raw_client_id);
            if (!isset($raw_client))
                return Redirect::to('client/student/raw')->withError('Data does not exist');

            if ($client_id != null){
                $client = $this->clientRepository->getViewClientById($client_id);
                if (!isset($client))
                    return Redirect::to('client/student/raw')->withError('Data does not exist');
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            $log_service->createErrorLog(LogModule::SELECT_RAW_PARENT, $e->getMessage(), $e->getLine(), $e->getFile(), ['type' => $type, 'raw_client_id' => $raw_client_id, 'client_id' => $client_id]);

            return Redirect::to('client/student/raw')->withError('Something went wrong. Please try again or contact the administrator.');
        }

        $log_service->createSuccessLog(LogModule::SELECT_RAW_PARENT, 'Successfully fetch raw data student', ['type' => $type, 'raw_client_id' => $raw_client_id, 'client_id' => $client_id]);

        switch ($type) {
            case 'comparison':
                return view('pages.client.student.raw.form-comparison')->with([
                    'rawClient' => $raw_client,
                    'client' => $client,
                    'schools' => $schools,
                    'parents' => $parents,
                ]);
                break;

            case 'new':
                return view('pages.client.student.raw.form-new')->with([
                    'rawClient' => $raw_client,
                    'schools' => $schools,
                    'parents' => $parents,
                ]);
                break;
        }
    }

    public function convertData(StoreClientRawStudentRequest $request, LogService $log_service)
    {

        $type = $request->route('type');
        $client_id = $request->route('client_id');
        $raw_client_id = $request->route('rawclient_id');

        $name = $this->explodeName($request->nameFinal);

        $parent_type = $request->parentType;

        $client_details = [
            'first_name' => $name['firstname'],
            'last_name' => isset($name['lastname']) ? $name['lastname'] : null,
            'mail' => $request->emailFinal,
            'phone' => $this->tnSetPhoneNumber($request->phoneFinal),
            'graduation_year' => $request->graduationFinal,
            'sch_id' => $request->schoolFinal,
            'is_verified' => 'Y',
            'category' => 'new-lead'
        ];

        if ($request->parentName != null) {
            $parent_name = $this->explodeName($request->parentName);
            $parent_details = [
                'first_name' => $parent_name['firstname'],
                'last_name' => isset($parent_name['lastname']) ? $parent_name['lastname'] : null,
                'mail' => $request->parentMail,
                'phone' => isset($request->parentPhone) ? $this->tnSetPhoneNumber($request->parentPhone) : null,
                'is_verified' => 'Y'
            ];
            $parent_id = $request->parentFinal;
        }

        DB::beginTransaction();
        try {
            switch ($type) {
                case 'merge':

                    $student = $this->clientRepository->getClientById($client_id);
                    $this->clientRepository->updateClient($client_id, $client_details);

                    $raw_student = $this->clientRepository->getViewRawClientById($raw_client_id);
                    
                    if ($parent_type == 'new') {
                        if ($request->parentFinal == null) {
                            # Remove relation parent
                            $student->parents()->count() > 0 ? $student->parents()->detach() : null;
                        } else {
                            $parent_details['lead_id'] = $student->lead_id;
                            $parent_details['register_by'] = $student->register_by;

                            # Add relation new parent
                            $parent = $this->clientRepository->updateClient($parent_id, $parent_details);
                            $this->clientRepository->createClientRelation($parent_id, $client_id);
                        }
                    } else if ($parent_type == 'exist') {
                        if ($request->parentFinal != null) {
                            $this->clientRepository->updateClient($parent_id, $parent_details);
                            $this->clientRepository->createClientRelation($parent_id, $client_id);
                        } 
                    } elseif ($parent_type == 'exist_select') {
                        $this->clientRepository->createClientRelation($parent_id, $client_id);
                    }

                    # delete student from raw client
                    $this->clientRepository->deleteClient($raw_client_id);
                    
                    # sync destination country
                    if ($raw_student->destinationCountries->count() > 0)
                        $this->syncDestinationCountry($raw_student->destinationCountries, $student);

                    # insert to client log
                    $client_data_for_log[] = [
                        'client_id' => $student->id,
                        'first_name' => $client_details['first_name'],
                        'last_name' => $client_details['last_name'],
                        'inputted_from' => 'verified',
                        'old_client_id' => $raw_student->id,
                        'select_existing' => true
                    ];
    
                    ProcessInsertLogClient::dispatch($client_data_for_log)->onQueue('insert-log-client');
    
                    break;

                case 'new':
                    $raw_student = $this->clientRepository->getViewRawClientById($raw_client_id);
                    $lead_id = $raw_student->lead_id;
                    $register_by = $raw_student->register_by;

                    $client_details['lead_id'] = $lead_id;
                    $client_details['register_by'] = $register_by;

                    $student = $this->clientRepository->updateClient($raw_client_id, $client_details);

                    if ($parent_type == 'new' && $request->parentFinal != null) {
                        $parent_details['lead_id'] = $lead_id;
                        $parent_details['register_by'] = $register_by;

                        # Add relation new parent
                        $this->clientRepository->updateClient($parent_id, $parent_details);
                        $this->clientRepository->createClientRelation($parent_id, $raw_client_id);
                    } else if ($parent_type == 'exist') {
                        $this->clientRepository->updateClient($parent_id, $parent_details);
                        $this->clientRepository->createClientRelation($parent_id, $raw_client_id);
                    } elseif ($parent_type == 'exist_select') {
                        $this->clientRepository->createClientRelation($parent_id, $raw_client_id);
                    }

                    # insert to client log
                    $client_data_for_log[] = [
                        'client_id' => $raw_student->id,
                        'first_name' => $client_details['first_name'],
                        'last_name' => $client_details['last_name'],
                        'inputted_from' => 'verified',
                        'old_client_id' => null,
                        'select_existing' => false
                    ];

                    ProcessInsertLogClient::dispatch($client_data_for_log)->onQueue('insert-log-client');

                    break;
            }

            
            # Delete raw parent
            // $raw_student->parent_uuid != null ? $this->clientRepository->deleteRawClientByUUID($raw_student->parent_uuid) : null;
          

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            $log_service->createErrorLog(LogModule::VERIFIED_RAW_STUDENT, $e->getMessage(), $e->getLine(), $e->getFile(), $client_details);

            return Redirect::to('client/student/raw')->withError('Something went wrong. Please try again or contact the administrator.');
        }

        $log_service->createSuccessLog(LogModule::VERIFIED_RAW_STUDENT, 'Raw student has been verified', $client_details);

        return Redirect::to('client/student/'. (isset($client_id) ? $client_id : $raw_client_id))->withSuccess('Convert client successfully.');
    }

    public function destroy(Request $request, LogService $log_service)
    {
        $client_id = $request->route('student');
        $client = $this->clientRepository->getClientById($client_id);

        DB::beginTransaction();
        try {

            if (!isset($client))
                return Redirect::to('client/student?st=new-leads')->withError('Data does not exist');

            # insert to client log
            $clients_data_for_log_client[] = [
                'client_id' => $client->id,
                'first_name' => $client->first_name,
                'last_name' => $client->last_name,
                'inputted_from' => 'trash',                    
            ];
    
            # trigger to insert log client
            ProcessInsertLogClient::dispatch($clients_data_for_log_client)->onQueue('insert-log-client');
    
            $this->clientRepository->deleteClient($client_id);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_STUDENT, $e->getMessage(), $e->getLine(), $e->getFile(), $client->toArray());

            return Redirect::to('client/student?st=new-leads')->withError('Failed to delete client student');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_STUDENT, 'Student has been deleted', $client->toArray());

        return Redirect::to('client/student?st=new-leads')->withSuccess('Client student successfully deleted');
    }

    public function destroyRaw(Request $request, LogService $log_service)
    {
        # when is method 'POST' meaning the function come from bulk delete
        $is_bulk = $request->isMethod('POST') ? true : false;
        if ($is_bulk)
            return $this->bulk_destroy($request, $log_service); 
        
        return $this->single_destroy($request);
    }

    private function single_destroy(Request $request)
    {
        $raw_client_id = $request->route('rawclient_id');
        $raw_student = $this->clientRepository->getViewRawClientById($raw_client_id);

        DB::beginTransaction();
        try {

            if (!isset($raw_student))
                return Redirect::to('client/student/raw')->withError('Data does not exist');

            $clients_data_for_log_client[] = [
                'client_id' => $raw_student->id,
                'first_name' => $raw_student->fname,
                'last_name' => $raw_student->lname,
                'inputted_from' => 'trash',                    
            ];
    
            # trigger to insert log client
            ProcessInsertLogClient::dispatch($clients_data_for_log_client)->onQueue('insert-log-client');
    

            $this->clientRepository->deleteClient($raw_client_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete raw client student failed : ' . $e->getMessage());
            return Redirect::to('client/student/raw')->withError('Failed to delete raw student');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Raw Client', Auth::user()->first_name . ' ' . Auth::user()->last_name, $raw_student);

        return Redirect::to('client/student/raw')->withSuccess('Raw student successfully deleted');
    }

    private function bulk_destroy(Request $request, LogService $log_service)
    {
        # raw client id that being choose from list raw data client
        $raw_client_ids = $request->choosen;

        DB::beginTransaction();
        try {

            foreach ($raw_client_ids as $raw_client_id) {
                $client = $this->clientRepository->getClientById($raw_client_id);
                
                if (!isset($client))
                {
                    Log::warning('Failed destroy client: data client with id ('.$raw_client_id.') not found!');
                    continue;
                }

                $clients_data_for_log_client[] = [
                    'client_id' => $client->id,
                    'first_name' => $client->first_name,
                    'last_name' => $client->last_name,
                    'inputted_from' => 'trash',                    
                ];
        
                # trigger to insert log client
                ProcessInsertLogClient::dispatch($clients_data_for_log_client, true)->onQueue('insert-log-client');
            }

            $this->clientRepository->moveBulkToTrash($raw_client_ids);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_BULK_STUDENT_RAW, $e->getMessage(), $e->getLine(), $e->getFile(), ['raw_student_ids' => $raw_client_ids]);

            return response()->json(['success' => false, 'message' => 'Failed to delete raw client'], 500);

        }

        $log_service->createSuccessLog(LogModule::DELETE_BULK_STUDENT_RAW, 'Successfully delete raw student', ['raw_student_ids' => $raw_client_ids]);

        return response()->json(['success' => true, 'message' => 'Delete raw client success']);
    }

    public function assign(Request $request, LogService $log_service)
    {
        # raw client id that being choose from list raw data client
        $client_ids = $request->choosen;
        $pic = $request->pic_id;

        # if pic was null 
        # we don't want to show message "requested failed with status 500" but more to formal
        if (!$pic)
            return response()->json(['success' => false, 'message' => 'We require a selection to proceed. Please review the available options and choose one.'], 500);

        $pic_details = new Collection();

        DB::beginTransaction();
        try {

            foreach ($client_ids as $client_id) {

                if ($pic_details->where('client_id', $client_id)->first())
                    continue;
                
                $pic_details->push([
                    'client_id' => $client_id,
                    'user_id' => $pic,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                if ($client = $this->clientRepository->checkActivePICByClient($client_id)) 
                    $this->clientRepository->inactivePreviousPIC($client);
            }

            # because insert sql need data type as array
            # meaning: collection has to be converted into array
            $this->clientRepository->insertPicClient($pic_details->toArray());
            
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::ASSIGN_PIC_CLIENT, $e->getMessage(), $e->getLine(), $e->getFile(), ['client_ids' => $client_ids, 'pic' => $pic]);

            return response()->json(['success' => false, 'message' => 'Failed to assign client'], 500);

        }

        $log_service->createSuccessLog(LogModule::ASSIGN_PIC_CLIENT, 'Successfully assign pic client', ['client_ids' => $client_ids, 'pic' => $pic]);

        return response()->json(['success' => true, 'message' => 'Assign client success']);
    }

    public function updatePic(Request $request, LogService $log_service)
    {
        $new_pic = $request->new_pic;
        $client_id = $request->client_id;
        $pic_client_id = $request->pic_client_id;

        $pic_detail[] = [
            'client_id' => $client_id,
            'user_id' => $new_pic,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        DB::beginTransaction();
        try {

            $this->clientRepository->updatePicClient($pic_client_id, $pic_detail);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_PIC_CLIENT, $e->getMessage(), $e->getLine(), $e->getFile(), $pic_detail);

            return response()->json(['success' => false, 'message' => 'Failed to update PIC client'], 500);

        }

        $log_service->createSuccessLog(LogModule::UPDATE_PIC_CLIENT, 'PIC client has been updated', $pic_detail);

        return response()->json(['success' => true, 'message' => 'Update PIC client success']);

    }

    public function getLogsClient(Request $request)
    {
        $mapped_client_logs = new Collection;
        $client_uuid = $request->client;

        try {
            $client = $this->clientRepository->getClientByUUID($client_uuid);
    
            if(isset($client->client_log)){
                $mapped_client_logs = $client->client_log->mapToGroups(function ($item, int $key) {
                    return [$item['unique_key'] => [
                        'inputted_from' => ucfirst($item['inputted_from']),
                        'category' => ucfirst($item['category']),
                        'updated_at' => $item['formatted_updated_at']
                    ]];
                });
            }
        } catch (Exception $e) {
            Log::error('Failed to get logs client : ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to get logs client'], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $mapped_client_logs,
            'message' => "Successfully get client logs."
        ]);
 
    }
}
