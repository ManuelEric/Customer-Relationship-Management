<?php

namespace App\Http\Controllers;

use App\Exceptions\StoreNewSchoolException;
use App\Exports\StudentTemplate;
use App\Http\Controllers\Module\ClientController;
use App\Http\Requests\StoreClientStudentRequest;
use App\Http\Requests\StoreImportExcelRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\FindStatusClientTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
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
use App\Models\ClientLeadTracking;
use App\Models\Lead;
use App\Models\School;
use App\Models\UserClient;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class ClientStudentController extends ClientController
{
    use CreateCustomPrimaryKeyTrait;
    use FindStatusClientTrait;
    use StandardizePhoneNumberTrait;

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

    public function __construct(ClientRepositoryInterface $clientRepository, SchoolRepositoryInterface $schoolRepository, LeadRepositoryInterface $leadRepository, EventRepositoryInterface $eventRepository, EdufLeadRepositoryInterface $edufLeadRepository, ProgramRepositoryInterface $programRepository, UniversityRepositoryInterface $universityRepository, MajorRepositoryInterface $majorRepository, CurriculumRepositoryInterface $curriculumRepository, TagRepositoryInterface $tagRepository, SchoolCurriculumRepositoryInterface $schoolCurriculumRepository, ClientProgramRepositoryInterface $clientProgramRepository, CountryRepositoryInterface $countryRepository, ClientEventRepositoryInterface $clientEventRepository, InitialProgramRepositoryInterface $initialProgramRepository, ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository, ReasonRepositoryInterface $reasonRepository)
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

            # array for advanced filter request
            $advanced_filter = [
                'school_name' => $school_name,
                'graduation_year' => $graduation_year,
                'leads' => $leads,
                'initial_programs' => $initial_programs,
                'status_lead' => $status_lead,
                'active_status' => $active_status
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

                default:
                    $model = $this->clientRepository->getAllClientStudent($advanced_filter);
            }
            
            return $this->clientRepository->getDataTables($model);
        }

        $reasons = $this->reasonRepository->getReasonByType('Hot Lead');

        # for advance filter purpose
        $schools = $this->schoolRepository->getAllSchools();
        $parents = $this->clientRepository->getAllClientByRole('Parent');
        $max_graduation_year = $this->clientRepository->getMaxGraduationYearFromClient();
        $main_leads = $this->leadRepository->getAllMainLead();
        $main_leads = $main_leads->map(function ($item) {
            return [
                'main_lead' => $item->main_lead
            ];
        });
        $sub_leads = $this->leadRepository->getAllKOLlead();
        $sub_leads = $sub_leads->map(function ($item) {
            return [
                'main_lead' => $item->sub_lead
            ];
        });
        $leads = $main_leads->merge($sub_leads);
        $initial_programs = $this->initialProgramRepository->getAllInitProg();        

        return view('pages.client.student.index')->with(
            [
                'reasons' => $reasons,
                'advanced_filter' => [
                    'schools' => $schools,
                    'parents' => $parents,
                    'leads' => $leads,
                    'max_graduation_year' => $max_graduation_year,
                    'initial_programs' => $initial_programs
                ]
            ]
        );
    }

    public function show(Request $request)
    {
        $studentId = $request->route('student');
        $student = $this->clientRepository->getClientById($studentId);

        $initialPrograms = $this->initialProgramRepository->getAllInitProg();
        // $historyLeads = $this->clientLeadTrackingRepository->getHistoryClientLead($studentId);
        $viewStudent = $this->clientRepository->getViewClientById($studentId);

        $initialPrograms = $this->initialProgramRepository->getAllInitProg();
        $historyLeads = $this->clientLeadTrackingRepository->getHistoryClientLead($studentId);

        if (!$student)
            abort(404);

        return view('pages.client.student.view')->with(
            [
                'student' => $student,
                'initialPrograms' => $initialPrograms,
                'historyLeads' => $historyLeads,
                'viewStudent' => $viewStudent
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
            if ($data['studentDetails']['pr_id'] !== NULL) {

                if (!$parentId = $this->createParentsIfAddNew($data['parentDetails'], $data['studentDetails']))
                    throw new Exception('Failed to store new parent', 2);
            }

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
            if ($parentId !== NULL && $data['studentDetails']['pr_id'] !== NULL) {

                if (!$this->clientRepository->createClientRelation($parentId, $newStudentId))
                    throw new Exception('Failed to store relation between student and parent', 4);
            }

            # case 5
            # create interested program
            # if they didn't insert interested program 
            # then skip this case
            if (!$this->createInterestedProgram($data['interestPrograms'], $newStudentId))
                throw new Exception('Failed to store interest program', 5);

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

                case 5:
                    Log::error('Store interest programs failed : ' . $e->getMessage());
                    break;

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
                'regions' => $regions
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

        $leadsTracking = $this->clientLeadTrackingRepository->getCurrentClientLead($studentId);

        DB::beginTransaction();
        try {

            //! perlu nunggu 1 menit dlu sampai ada client lead tracking status yg 1
            # update status client lead tracking
            if($leadsTracking->count() > 0){
                foreach($leadsTracking as $leadTracking){
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

            if ($data['studentDetails']['pr_id'] !== NULL) {
                if (!$parentId = $this->createParentsIfAddNew($data['parentDetails'], $data['studentDetails']))
                    throw new Exception('Failed to store new parent', 2);
            }
            

            # removing the kol_lead_id & pr_id from studentDetails array
            # if the data still exists it will error because there are no field with kol_lead_id & pr_id
            unset($data['studentDetails']['kol_lead_id']);
            $newParentId = $data['studentDetails']['pr_id'];
            $oldParentId = $data['studentDetails']['pr_id_old']; 
            unset($data['studentDetails']['pr_id']);
            unset($data['studentDetails']['pr_id_old']);

            # case 3
            # create new user client as student
            if (!$student = $this->clientRepository->updateClient($studentId, $data['studentDetails']))
                throw new Exception('Failed to update student information', 3);


            # case 4
            # add relation between parent and student
            # if they didn't insert parents which parentId = NULL
            # then assumed that register for student only
            # so no need to create parent children relation
            if ($newParentId !== NULL) {

                if (!in_array($parentId, $this->clientRepository->getParentsByStudentId($studentId))) {

                    if (!$this->clientRepository->createClientRelation($parentId, $studentId))
                        throw new Exception('Failed to store relation between student and parent', 4);
                }

            } else {

                # when pr_id is null it means they remove the parent from the child
                if (in_array($oldParentId, $this->clientRepository->getParentsByStudentId($studentId))) {

                    if (!$this->clientRepository->removeClientRelation($oldParentId, $studentId))
                        throw new Exception('Failed to remove relation between student and parent', 4);
                }
            }

            # case 5
            # create interested program
            # if they didn't insert interested program 
            # then skip this case
            if (!$this->createInterestedProgram($data['interestPrograms'], $studentId))
                throw new Exception('Failed to store interest program', 5);

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

                case 5:
                    Log::error('Update interest programs failed : ' . $e->getMessage());
                    break;

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

        if($reason == 'other'){
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

        $file = $request->file('file');

        $import = new StudentImport();
        $import->import($file);

        return back()->withSuccess('Student successfully imported');
    }

    public function siblings(Request $request)
    {
        $clients = $this->clientRepository->getAlumniMenteesSiblings();
        return $clients;
    }
}
