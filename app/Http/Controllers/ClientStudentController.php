<?php

namespace App\Http\Controllers;

use App\Exceptions\StoreNewSchoolException;
use App\Exports\StudentTemplate;
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
use App\Imports\MasterStudentImport;
use App\Imports\StudentImport;
use App\Interfaces\UserRepositoryInterface;
use App\Models\ClientLeadTracking;
use App\Models\Lead;
use App\Models\School;
use App\Models\UserClient;
use App\Services\ClientStudentService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

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

    public function __construct(ClientRepositoryInterface $clientRepository, SchoolRepositoryInterface $schoolRepository, LeadRepositoryInterface $leadRepository, EventRepositoryInterface $eventRepository, EdufLeadRepositoryInterface $edufLeadRepository, ProgramRepositoryInterface $programRepository, UniversityRepositoryInterface $universityRepository, MajorRepositoryInterface $majorRepository, CurriculumRepositoryInterface $curriculumRepository, TagRepositoryInterface $tagRepository, SchoolCurriculumRepositoryInterface $schoolCurriculumRepository, ClientProgramRepositoryInterface $clientProgramRepository, CountryRepositoryInterface $countryRepository, ClientEventRepositoryInterface $clientEventRepository, InitialProgramRepositoryInterface $initialProgramRepository, ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository, ReasonRepositoryInterface $reasonRepository, UserRepositoryInterface $userRepository)
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
    }

    # ajax start
    public function getClientProgramByStudentId(Request $request)
    {
        $studentId = $request->route('client');
        return $this->clientProgramRepository->getAllClientProgramDataTables(['clientId' => $studentId]);
    }

    public function getClientEventByStudentId(Request $request)
    {
        $studentId = $request->route('client');
        return $this->clientEventRepository->getAllClientEventByClientIdDataTables($studentId);
    }
    # ajax end

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $statusClient = $request->get('st');
            $asDatatables = true;

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

            switch ($statusClient) {

                    // client/student
                case "new-leads":
                    $model = $this->clientRepository->getNewLeads($asDatatables, null, $advanced_filter);
                    break;

                case "potential":
                    $model = $this->clientRepository->getPotentialClients($asDatatables, null, $advanced_filter);
                    break;

                case "mentee":
                    $model = $this->clientRepository->getExistingMentees($asDatatables, null, $advanced_filter);
                    break;

                case "non-mentee":
                    $model = $this->clientRepository->getExistingNonMentees($asDatatables, null, $advanced_filter);
                    break;

                case "inactive":
                    $model = $this->clientRepository->getInactiveStudent($asDatatables, null, $advanced_filter);
                    break;

                default:
                    $model = $this->clientRepository->getAllClientStudent($advanced_filter);
            }

            return $this->clientRepository->getDataTables($model);
        }

        $entries = app('App\Services\ClientStudentService')->advancedFilterClient();

        return view('pages.client.student.index')->with($entries);
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
        
        $studentId = $request->route('student');
        $student = $this->clientRepository->getClientById($studentId);

        # validate
        # if user forced to access student that isn't his/her 
        if (!$this->clientRepository->findHandledClient($studentId))
            abort(403);

        $initialPrograms = $this->initialProgramRepository->getAllInitProg();
        // $historyLeads = $this->clientLeadTrackingRepository->getHistoryClientLead($studentId);
        $viewStudent = $this->clientRepository->getViewClientById($studentId);

        $programsB2BB2C = $this->programRepository->getAllProgramByType('B2B/B2C', true);
        $programsB2C = $this->programRepository->getAllProgramByType('B2C', true);
        $programs = $programsB2BB2C->merge($programsB2C)->sortBy('program_name');
        
        $salesTeams = $this->userRepository->getAllUsersByDepartmentAndRole('Employee', 'Client Management');

        $initialPrograms = $this->initialProgramRepository->getAllInitProg();
        $historyLeads = $this->clientLeadTrackingRepository->getHistoryClientLead($studentId);

        $parents = $this->clientRepository->getAllClientByRole('Parent');

        $picActive = null;
        if (count($student->picClient) > 0){
            $picActive = $student->picClient->where('status', 1)->first();
        }

        if (!$student)
            abort(500);

        return view('pages.client.student.view')->with(
            [
                'student' => $student,
                'initialPrograms' => $initialPrograms,
                'historyLeads' => $historyLeads,
                'viewStudent' => $viewStudent,
                'programs' => $programs,
                'salesTeams' => $salesTeams,
                'picActive' => $picActive,
                'parents' => $parents
            ]
        );
    }

    public function store(StoreClientStudentRequest $request)
    {
        $parentId = NULL;
        $data = $this->initializeVariablesForStoreAndUpdate('student', $request);
        $data['studentDetails']['register_as'] == null ? $data['studentDetails']['register_as'] = 'student' : $data['studentDetails']['register_as'];

        DB::beginTransaction();
        try {

            # case 1
            # create new school
            if (!$data['studentDetails']['sch_id'] = $this->createSchoolIfAddNew($data['schoolDetails']))
                throw new Exception('Failed to store new school', 1);

            # case 2
            # create new user client as parents
            // if ($data['studentDetails']['pr_id'] !== NULL) {

            //     if (!$parentId = $this->createParentsIfAddNew($data['parentDetails'], $data['studentDetails']))
            //         throw new Exception('Failed to store new parent', 2);
            // }

            # case 3
            # create new user client as student
            if (!$newStudentDetails = $this->clientRepository->createClient('Student', $data['studentDetails']))
                throw new Exception('Failed to store new student', 3);

            $newStudentId = $newStudentDetails->id;

            # case 4 (optional)
            # add relation between parent and student
            # if they didn't insert parents which parentId = NULL
            # then assumed that register for student only
            # so no need to create parent children relation
            // if ($parentId !== NULL && $data['studentDetails']['pr_id'] !== NULL) {

            //     if (!$this->clientRepository->createClientRelation($parentId, $newStudentId))
            //         throw new Exception('Failed to store relation between student and parent', 4);
            // }

            # case 5
            # create interested program
            # if they didn't insert interested program 
            # then skip this case
            // if (!$this->createInterestedProgram($data['interestPrograms'], $newStudentId))
            //     throw new Exception('Failed to store interest program', 5);

            # case 6.1
            # create destination countries
            # if they didn't insert destination countries
            # then skip this case
            if (!$this->createDestinationCountries($data['abroadCountries'], $newStudentId))
                throw new Exception('Failed to store destination country', 6);

            # case 6.2
            # create interested universities
            # if they didn't insert universities
            # then skip this case
            if (!$this->createInterestedUniversities($data['abroadUniversities'], $newStudentId))
                throw new Exception('Failed to store interest universities', 6);

            # case 7
            # create interested major
            # if they didn't insert major
            # then skip this case
            if (!$this->createInterestedMajor($data['interestMajors'], $newStudentId))
                throw new Exception('Failed to store interest major', 7);

            # case 8
            # Set default PIC if sales member add student
            if (Session::get('user_role') == 'Employee') {
                $picDetails[] = [
                    'client_id' => $newStudentId,
                    'user_id' => auth()->user()->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                $this->clientRepository->insertPicClient($picDetails);
            }

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            switch ($e->getCode()) {
                case 1:
                    Log::error('Store school failed from student : ' . $e->getMessage());
                    break;

                case 2:
                    Log::error('Store parent failed from student : ' . $e->getMessage());
                    break;

                case 3:
                    Log::error('Store student failed : ' . $e->getMessage());
                    break;

                case 4:
                    Log::error('Store relation between student and parent failed : ' . $e->getMessage());
                    break;

                    // case 5:
                    //     Log::error('Store interest programs failed : ' . $e->getMessage());
                    //     break;

                case 6:
                    Log::error('Store interest universities failed : ' . $e->getMessage());
                    break;

                case 7:
                    Log::error('Store interest major failed : ' . $e->getMessage());
                    break;
            }

            Log::error('Store a new student failed : ' . $e->getMessage());
            return Redirect::to('client/student/create')->withError($e->getMessage());
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Student', Auth::user()->first_name . ' ' . Auth::user()->last_name, $newStudentDetails);

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

        $programsB2BB2C = $this->programRepository->getAllProgramByType('B2B/B2C', true);
        $programsB2C = $this->programRepository->getAllProgramByType('B2C', true);
        $programs = $programsB2BB2C->merge($programsB2C)->sortBy('program_name');
        $countries = $this->tagRepository->getAllTags();
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
                'countries' => $countries,
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
        $studentId = $request->route('student');
        $student = $this->clientRepository->getClientById($studentId);
        $viewStudent = $this->clientRepository->getViewClientById($studentId);

        $schools = $this->schoolRepository->getAllSchools();
        $curriculums = $this->curriculumRepository->getAllCurriculums();
        $parents = $this->clientRepository->getAllClientByRole('Parent');
        $leads = $this->leadRepository->getAllMainLead();
        $events = $this->eventRepository->getAllEvents();
        $ext_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        $programsB2BB2C = $this->programRepository->getAllProgramByType('B2B/B2C');
        $programsB2C = $this->programRepository->getAllProgramByType('B2C');
        $programs = $programsB2BB2C->merge($programsB2C);
        $countries = $this->tagRepository->getAllTags();
        $majors = $this->majorRepository->getAllMajors();

        return view('pages.client.student.form')->with(
            [
                'student' => $student,
                'viewStudent' => $viewStudent,
                'schools' => $schools,
                'curriculums' => $curriculums,
                'parents' => $parents,
                'leads' => $leads,
                'events' => $events,
                'ext_edufair' => $ext_edufair,
                'kols' => $kols,
                'programs' => $programs,
                'countries' => $countries,
                'majors' => $majors,
            ]
        );
    }

    public function update(StoreClientStudentRequest $request)
    {
        $data = $this->initializeVariablesForStoreAndUpdate('student', $request);

        $studentId = $request->route('student');
        $oldStudent = $this->clientRepository->getClientById($studentId);

        $leadsTracking = $this->clientLeadTrackingRepository->getCurrentClientLead($studentId);

        DB::beginTransaction();
        try {

            # set referral code null if lead != referral
            if ($data['studentDetails']['lead_id'] != 'LS005'){
                $data['studentDetails']['referral_code'] = null;
            }

            //! perlu nunggu 1 menit dlu sampai ada client lead tracking status yg 1
            # update status client lead tracking
            if ($leadsTracking->count() > 0) {
                foreach ($leadsTracking as $leadTracking) {
                    $this->clientLeadTrackingRepository->updateClientLeadTrackingById($leadTracking->id, ['status' => 0]);
                }
            }

            # case 1
            # create new school
            # when sch_id is "add-new" 
            if (!$data['studentDetails']['sch_id'] = $this->createSchoolIfAddNew($data['schoolDetails']))
                throw new Exception('Failed to store new school', 1);

            # case 2
            # create new user client as parents
            # when pr_id is "add-new" 

            // if ($data['studentDetails']['pr_id'] !== NULL) {
            //     if ($data['studentDetails']['lead_id'] != 'LS005'){
            //         $data['parentDetails']['referral_code'] = null;
            //     }
            //     if (!$parentId = $this->createParentsIfAddNew($data['parentDetails'], $data['studentDetails']))
            //         throw new Exception('Failed to store new parent', 2);
            // }


            # removing the kol_lead_id & pr_id from studentDetails array
            # if the data still exists it will error because there are no field with kol_lead_id & pr_id
            unset($data['studentDetails']['kol_lead_id']);
            // $newParentId = $data['studentDetails']['pr_id'];
            // $oldParentId = $data['studentDetails']['pr_id_old'];
            // unset($data['studentDetails']['pr_id']);
            // unset($data['studentDetails']['pr_id_old']);

            # case 3
            # create new user client as student
            if (!$student = $this->clientRepository->updateClient($studentId, $data['studentDetails']))
                throw new Exception('Failed to update student information', 3);


            # case 4
            # add relation between parent and student
            # if they didn't insert parents which parentId = NULL
            # then assumed that register for student only
            # so no need to create parent children relation
            // if ($newParentId !== NULL) {

            //     if (!in_array($parentId, $this->clientRepository->getParentsByStudentId($studentId))) {

            //         if (!$this->clientRepository->createClientRelation($parentId, $studentId))
            //             throw new Exception('Failed to store relation between student and parent', 4);
            //     }
            // } else {

            //     # when pr_id is null it means they remove the parent from the child
            //     if (in_array($oldParentId, $this->clientRepository->getParentsByStudentId($studentId))) {

            //         if (!$this->clientRepository->removeClientRelation($oldParentId, $studentId))
            //             throw new Exception('Failed to remove relation between student and parent', 4);
            //     }
            // }

            # case 5
            # create interested program
            # if they didn't insert interested program 
            # then skip this case
            // if (!$this->createInterestedProgram($data['interestPrograms'], $studentId))
            //     throw new Exception('Failed to store interest program', 5);

            # case 6.1
            # create destination countries
            # if they didn't insert destination countries
            # then skip this case
            if (!$this->createDestinationCountries($data['abroadCountries'], $studentId))
                throw new Exception('Failed to store destination country', 6);

            # case 6.2
            # create interested universities
            # if they didn't insert universities
            # then skip this case
            if (!$this->createInterestedUniversities($data['abroadUniversities'], $studentId))
                throw new Exception('Failed to store interest universities', 6);


            # case 7
            # create interested major
            # if they didn't insert major
            # then skip this case
            if (!$this->createInterestedMajor($data['interestMajors'], $studentId))
                throw new Exception('Failed to store interest major', 7);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            switch ($e->getCode()) {
                case 1:
                    Log::error('Update school failed from student : ' . $e->getMessage());
                    break;

                case 2:
                    Log::error('Update parent failed from student : ' . $e->getMessage());
                    break;

                case 3:
                    Log::error('Update student failed : ' . $e->getMessage());
                    break;

                case 4:
                    Log::error('Update relation between student and parent failed : ' . $e->getMessage());
                    break;

                    // case 5:
                    //     Log::error('Update interest programs failed : ' . $e->getMessage());
                    //     break;

                case 6:
                    Log::error('Update interest universities failed : ' . $e->getMessage());
                    break;

                case 7:
                    Log::error('Update interest major failed : ' . $e->getMessage());
                    break;
            }

            Log::error('Update a student failed : ' . $e->getMessage());
            return Redirect::to('client/student/' . $studentId . '/edit')->withError($e->getMessage());
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Student', Auth::user()->first_name . ' ' . Auth::user()->last_name, $data['studentDetails'], $oldStudent);

        return Redirect::to('client/student/' . $studentId)->withSuccess('A student\'s profile has been updated.');
    }

    public function updateStatus(Request $request)
    {
        $studentId = $request->route('student');
        $newStatus = $request->route('status');

        # validate status
        if (!in_array($newStatus, [0, 1])) {

            return response()->json(
                [
                    'success' => false,
                    'message' => "Status is invalid"
                ]
            );
        }

        DB::beginTransaction();
        try {

            $this->clientRepository->updateActiveStatus($studentId, $newStatus);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update active status client failed : ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ]
            );
        }

        # Upload success
        # create log success
        $this->logSuccess('upload', null, 'Status Client', Auth::user()->first_name . ' ' . Auth::user()->last_name, ['status' => $newStatus], ['client_id', $studentId]);


        return response()->json(
            [
                'success' => true,
                'message' => "Status has been updated",
            ]
        );
    }

    public function updateLeadStatus(Request $request)
    {
        $studentId = $request->clientId;
        $initprogName = $request->initProg;
        $leadStatus = $request->leadStatus;
        
        
        $groupId = $request->groupId;
        $reason = $request->reason_id;
        $programScore = $leadScore = 0;

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
            $otherReason = $this->reasonRepository->createReason(['reason_name' => $request->other_reason, 'type' => 'Hot Lead']);
            $reason = $otherReason->reason_id;
        }

        $initProg = $this->initialProgramRepository->getInitProgByName($initprogName);

        $programTracking = $this->clientLeadTrackingRepository->getLatestClientLeadTrackingByType('Program', $groupId);
        $leadTracking = $this->clientLeadTrackingRepository->getLatestClientLeadTrackingByType('Lead', $groupId);

        switch ($leadStatus) {
            case 'hot':
                $programScore = 0.99;
                $leadScore = 0.99;
                break;

            case 'warm':
                $programScore = 0.51;
                $leadScore = 0.64;
                break;

            case 'cold':
                $programScore = 0.49;
                $leadScore = 0.34;
                break;
        }

        $last_id = ClientLeadTracking::max('group_id');
        $group_id_without_label = $last_id ? $this->remove_primarykey_label($last_id, 5) : '00000';
        $group_id_with_label = 'CLT-' . $this->add_digit($group_id_without_label + 1, 5);


        $programDetails = [
            'group_id' => $group_id_with_label,
            'client_id' => $studentId,
            'initialprogram_id' => $initProg->id,
            'type' => 'Program',
            'total_result' => $programScore,
            'status' => 1
        ];

        $leadStatusDetails = [
            'group_id' => $group_id_with_label,
            'client_id' => $studentId,
            'initialprogram_id' => $initProg->id,
            'type' => 'Lead',
            'total_result' => $leadScore,
            'status' => 1
        ];

        DB::beginTransaction();
        try {

            $this->clientLeadTrackingRepository->updateClientLeadTrackingById($programTracking->id, ['status' => 0, 'reason_id' => $reason]);
            $this->clientLeadTrackingRepository->updateClientLeadTrackingById($leadTracking->id, ['status' => 0, 'reason_id' => $reason]);

            $this->clientLeadTrackingRepository->createClientLeadTracking($programDetails);
            $this->clientLeadTrackingRepository->createClientLeadTracking($leadStatusDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
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
        $this->logSuccess('upload', null, 'Status Lead Client', Auth::user()->first_name . ' ' . Auth::user()->last_name, $leadStatusDetails, ['lead_status', $leadStatus]);

        return response()->json(
            [
                'success' => true,
                'code' => 200,
                'message' => 'Lead status has been updated',
            ]
        );
    }

    public function import(StoreImportExcelRequest $request)
    {
        Cache::put('auth', Auth::user());
        Cache::put('import_id', Carbon::now()->timestamp . '-import-student');

        $file = $request->file('file');

        // try {
            (new StudentImport())->queue($file)->allOnQueue('imports-student');
            // Excel::queueImport(new StudentImport(Auth::user()->first_name . ' '. Auth::user()->last_name), $file);
            // $import = new StudentImport();
            // $import->import($file);
        // } catch (Exception $e) {
        //     return back()->withError('Something went wrong while processing the data. Please try again or contact the administrator.');
        // }

        return back()->withSuccess('Import student start progress');
    }

    public function siblings(Request $request)
    {
        $clients = $this->clientRepository->getAlumniMenteesSiblings();
        return $clients;
    }

    public function addInterestProgram(Request $request)
    {
        $studentId = $request->route('student');

     

        DB::beginTransaction();
        try {

            $createdInterestProgram = $this->clientRepository->addInterestProgram($studentId, $request->interest_program);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Add interest program client failed : ' . $e->getMessage() . ' ' . $e->getLine());
            return Redirect::to('client/student/' . $studentId)->withError('Interest program failed to be added.');
        }

        # Add interest program success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Interest Program', Auth::user()->first_name . ' ' . Auth::user()->last_name, $createdInterestProgram);
        return Redirect::to('client/student/' . $studentId)->withSuccess('Interest program successfully added.');
    }

    public function removeInterestProgram(Request $request)
    {
        $studentId = $request->route('student');
        $interestProgramId = $request->route('interest_program');
        $progId = $request->route('prog');

        DB::beginTransaction();
        try {

            $this->clientRepository->removeInterestProgram($studentId, $interestProgramId, $progId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Remove Interest Program failed : ' . $e->getMessage() . ' ' . $e->getLine());
            return Redirect::to('client/student/' . $studentId)->withError('Interest program failed to be removed.');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Interest Program', Auth::user()->first_name . ' ' . Auth::user()->last_name, ['client_id' => $studentId]);

        return Redirect::to('client/student/' . $studentId)->withSuccess('interest program successfully removed.');
    }

    public function addParent(AddParentRequest $request)
    {
        $studentId = $request->route('student');

        $parentDetails = $request->only([
            'existing_parent',
            'pr_id',
            'first_name',
            'last_name',
            'mail',
            'phone'
        ]);

        $parentDetails['phone'] = $this->setPhoneNumber($request->phone);

        DB::beginTransaction();
        try {

            # Parent Existing
            if($parentDetails['existing_parent'] == 1) {

                $this->clientRepository->createManyClientRelation($parentDetails['pr_id'], [$studentId]);

            } else { 
                unset($parentDetails['existing_parent']);
                unset($parentDetails['pr_id']);

                $newParent = $this->clientRepository->createClient('Parent', $parentDetails);

                $this->clientRepository->createManyClientRelation($newParent->id, [$studentId]);

            }

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Add parent failed : ' . $e->getMessage() . ' ' . $e->getLine());
            return Redirect::to('client/student/' . $studentId)->withError('Parent failed to be added.');
        }

        # Add interest program success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Add Parent', Auth::user()->first_name . ' ' . Auth::user()->last_name, $request->all());
        return Redirect::to('client/student/' . $studentId)->withSuccess('Parent successfully added.');
    }

    public function disconnectParent(Request $request)
    {
        $studentId = $request->route('student');
        $parentId = $request->route('parent');

        DB::beginTransaction();
        try {

            $this->clientRepository->removeClientRelation($parentId, $studentId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Disconnect parent failed : ' . $e->getMessage() . ' ' . $e->getLine());
            return Redirect::to('client/student/' . $studentId)->withError('failed to be diconnect parent.');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'relation parent', Auth::user()->first_name . ' ' . Auth::user()->last_name, ['client_id' => $studentId]);

        return Redirect::to('client/student/' . $studentId)->withSuccess('Successfully disconnect parent.');
    }

    public function cleaningData(Request $request)
    {
        $type = $request->route('type');
        $rawClientId = $request->route('rawclient_id');
        $clientId = $request->route('client_id');

        DB::beginTransaction();
        try {

            $schools = $this->schoolRepository->getVerifiedSchools();
            $parents = $this->clientRepository->getAllClientByRole('Parent');

            $rawClient = $this->clientRepository->getViewRawClientById($rawClientId);
            if (!isset($rawClient))
                return Redirect::to('client/student/raw')->withError('Data does not exist');

            if ($clientId != null){
                $client = $this->clientRepository->getViewClientById($clientId);
                if (!isset($client))
                    return Redirect::to('client/student/raw')->withError('Data does not exist');
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Fetch data raw client failed : ' . $e->getMessage() . ' ' . $e->getLine());
            return Redirect::to('client/student/raw')->withError('Something went wrong. Please try again or contact the administrator.');
        }

        switch ($type) {
            case 'comparison':
                return view('pages.client.student.raw.form-comparison')->with([
                    'rawClient' => $rawClient,
                    'client' => $client,
                    'schools' => $schools,
                    'parents' => $parents,
                ]);
                break;

            case 'new':
                return view('pages.client.student.raw.form-new')->with([
                    'rawClient' => $rawClient,
                    'schools' => $schools,
                    'parents' => $parents,
                ]);
                break;
        }
    }

    public function convertData(StoreClientRawStudentRequest $request)
    {

        $type = $request->route('type');
        $clientId = $request->route('client_id');
        $rawclientId = $request->route('rawclient_id');

        $name = $this->explodeName($request->nameFinal);

        $parentType = $request->parentType;

        $clientDetails = [
            'first_name' => $name['firstname'],
            'last_name' => isset($name['lastname']) ? $name['lastname'] : null,
            'mail' => $request->emailFinal,
            'phone' => $this->setPhoneNumber($request->phoneFinal),
            'graduation_year' => $request->graduationFinal,
            'sch_id' => $request->schoolFinal,
            'is_verified' => 'Y'
        ];

        if ($request->parentName != null) {
            $parentName = $this->explodeName($request->parentName);
            $parentDetails = [
                'first_name' => $parentName['firstname'],
                'last_name' => isset($parentName['lastname']) ? $parentName['lastname'] : null,
                'mail' => $request->parentMail,
                'phone' => isset($request->parentPhone) ? $this->setPhoneNumber($request->parentPhone) : null,
                'is_verified' => 'Y'
            ];
            $parentId = $request->parentFinal;
        }

        DB::beginTransaction();
        try {
            switch ($type) {
                case 'merge':

                    $student = $this->clientRepository->getClientById($clientId);
                    $this->clientRepository->updateClient($clientId, $clientDetails);

                    $rawStudent = $this->clientRepository->getViewRawClientById($rawclientId);
                    
                    // return $rawStudent->destinationCountries->count();
                    // exit;

                    if ($parentType == 'new') {
                        if ($request->parentFinal == null) {
                            # Remove relation parent
                            $student->parents()->count() > 0 ? $student->parents()->detach() : null;
                        } else {
                            $parentDetails['lead_id'] = $student->lead_id;
                            $parentDetails['register_as'] = $student->register_as;

                            # Add relation new parent
                            $parent = $this->clientRepository->updateClient($parentId, $parentDetails);
                            $this->clientRepository->createClientRelation($parentId, $clientId);
                        }
                    } else if ($parentType == 'exist') {
                        if ($request->parentFinal != null) {
                            $this->clientRepository->updateClient($parentId, $parentDetails);
                            $this->clientRepository->createClientRelation($parentId, $clientId);
                        } 
                    } elseif ($parentType == 'exist_select') {
                        $this->clientRepository->createClientRelation($parentId, $clientId);
                    }

                    # delete student from raw client
                    $this->clientRepository->deleteClient($rawclientId);
                    
                    # sync destination country
                    if ($rawStudent->destinationCountries->count() > 0)
                        $this->syncDestinationCountry($rawStudent->destinationCountries, $student);

                    break;

                case 'new':
                    $rawStudent = $this->clientRepository->getViewRawClientById($rawclientId);
                    $lead_id = $rawStudent->lead_id;
                    $register_as = $rawStudent->register_as;

                    $clientDetails['lead_id'] = $lead_id;
                    $clientDetails['register_as'] = $register_as;

                    $student = $this->clientRepository->updateClient($rawclientId, $clientDetails);

                    if ($parentType == 'new' && $request->parentFinal != null) {
                        $parentDetails['lead_id'] = $lead_id;
                        $parentDetails['register_as'] = $register_as;

                        # Add relation new parent
                        $this->clientRepository->updateClient($parentId, $parentDetails);
                        $this->clientRepository->createClientRelation($parentId, $rawclientId);
                    } else if ($parentType == 'exist') {
                        $this->clientRepository->updateClient($parentId, $parentDetails);
                        $this->clientRepository->createClientRelation($parentId, $rawclientId);
                    } elseif ($parentType == 'exist_select') {
                        $this->clientRepository->createClientRelation($parentId, $rawclientId);
                    }

                    break;
            }

            
            # Delete raw parent
            // $rawStudent->parent_uuid != null ? $this->clientRepository->deleteRawClientByUUID($rawStudent->parent_uuid) : null;

          

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Convert client failed : ' . $e->getMessage() . ' ' . $e->getLine());
            return Redirect::to('client/student/raw')->withError('Something went wrong. Please try again or contact the administrator.');
        }

        return Redirect::to('client/student/'. (isset($clientId) ? $clientId : $rawclientId))->withSuccess('Convert client successfully.');
    }

    public function destroy(Request $request)
    {
        $client_id = $request->route('student');
        $client = $this->clientRepository->getClientById($client_id);

        DB::beginTransaction();
        try {

            if (!isset($client))
                return Redirect::to('client/student?st=new-leads')->withError('Data does not exist');

            $this->clientRepository->deleteClient($client_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete client student failed : ' . $e->getMessage());
            return Redirect::to('client/student?st=new-leads')->withError('Failed to delete client student');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Client Student', Auth::user()->first_name . ' ' . Auth::user()->last_name, $client);

        return Redirect::to('client/student?st=new-leads')->withSuccess('Client student successfully deleted');

    }

    public function destroyRaw(Request $request)
    {
        # when is method 'POST' meaning the function come from bulk delete
        $isBulk = $request->isMethod('POST') ? true : false;
        if ($isBulk)
            return $this->bulk_destroy($request); 
        
        return $this->single_destroy($request);
    }

    private function single_destroy(Request $request)
    {
        $rawclientId = $request->route('rawclient_id');
        $rawStudent = $this->clientRepository->getViewRawClientById($rawclientId);

        DB::beginTransaction();
        try {

            if (!isset($rawStudent))
                return Redirect::to('client/student/raw')->withError('Data does not exist');

            $this->clientRepository->deleteClient($rawclientId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete raw client student failed : ' . $e->getMessage());
            return Redirect::to('client/student/raw')->withError('Failed to delete raw student');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Raw Client', Auth::user()->first_name . ' ' . Auth::user()->last_name, $rawStudent);

        return Redirect::to('client/student/raw')->withSuccess('Raw student successfully deleted');
    }

    private function bulk_destroy(Request $request)
    {
        # raw client id that being choose from list raw data client
        $rawClientIds = $request->choosen;

        DB::beginTransaction();
        try {

            $this->clientRepository->moveBulkToTrash($rawClientIds);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to bulk delete raw client failed : ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete raw client'], 500);

        }

        return response()->json(['success' => true, 'message' => 'Delete raw client success']);
    }

    public function assign(Request $request)
    {
        # raw client id that being choose from list raw data client
        $clientIds = $request->choosen;
        $pic = $request->pic_id;
        $picDetails = [];

        DB::beginTransaction();
        try {

            foreach ($clientIds as $clientId) {
                $picDetails[] = [
                    'client_id' => $clientId,
                    'user_id' => $pic,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                if ($client = $this->clientRepository->checkActivePICByClient($clientId)) 
                    $this->clientRepository->inactivePreviousPIC($client);
            }

            $this->clientRepository->insertPicClient($picDetails);
            
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to bulk assign client : ' . $e->getMessage(). ' on line '.$e->getLine());
            return response()->json(['success' => false, 'message' => 'Failed to assign client'], 500);

        }

        return response()->json(['success' => true, 'message' => 'Assign client success']);
    }

    public function updatePic(Request $request)
    {
        $new_pic = $request->new_pic;
        $client_id = $request->client_id;
        $pic_client_id = $request->pic_client_id;

        $picDetail[] = [
            'client_id' => $client_id,
            'user_id' => $new_pic,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        DB::beginTransaction();
        try {

            $this->clientRepository->updatePicClient($pic_client_id, $picDetail);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to update PIC client : ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update PIC client'], 500);

        }

        return response()->json(['success' => true, 'message' => 'Update PIC client success']);


    }
}
