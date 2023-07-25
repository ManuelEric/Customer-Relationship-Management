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
use App\Imports\MasterStudentImport;
use App\Imports\StudentImport;
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

    public function __construct(ClientRepositoryInterface $clientRepository, SchoolRepositoryInterface $schoolRepository, LeadRepositoryInterface $leadRepository, EventRepositoryInterface $eventRepository, EdufLeadRepositoryInterface $edufLeadRepository, ProgramRepositoryInterface $programRepository, UniversityRepositoryInterface $universityRepository, MajorRepositoryInterface $majorRepository, CurriculumRepositoryInterface $curriculumRepository, TagRepositoryInterface $tagRepository, SchoolCurriculumRepositoryInterface $schoolCurriculumRepository, ClientProgramRepositoryInterface $clientProgramRepository, CountryRepositoryInterface $countryRepository, ClientEventRepositoryInterface $clientEventRepository)
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
            switch ($statusClient) {

                    // client/student
                case "new-leads":
                    $model = $this->clientRepository->getNewLeads($asDatatables);
                    break;

                case "potential":
                    $model = $this->clientRepository->getPotentialClients($asDatatables);
                    break;

                case "mentee":
                    $model = $this->clientRepository->getExistingMentees($asDatatables);
                    break;

                case "non-mentee":
                    $model = $this->clientRepository->getExistingNonMentees($asDatatables);
                    break;

                default:
                    $statusClientCode = $this->getStatusClientCode($statusClient);
                    return $this->clientRepository->getAllClientByRoleAndStatusDataTables('Student', $statusClientCode);
            }
            
            return $this->clientRepository->getDataTables($model);
        }
        
        $schools = $this->schoolRepository->getAllSchools();
        $parents = $this->clientRepository->getAllClientByRole('Parent');
        $leads = $this->leadRepository->getAllMainLead();


        return view('pages.client.student.index')->with(
            [
                'schools' => $schools,
                'parents' => $parents,
                'leads' => $leads,
            ]
        );
    }

    public function show(Request $request)
    {
        $studentId = $request->route('student');
        $student = $this->clientRepository->getClientById($studentId);
        if (!$student)
            abort(404);

        return view('pages.client.student.view')->with(
            [
                'student' => $student
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
        DB::beginTransaction();
        try {

            # case 1
            # create new school
            # when sch_id is "add-new" 
            if (!$data['studentDetails']['sch_id'] = $this->createSchoolIfAddNew($data['schoolDetails']))
                throw new Exception('Failed to store new school', 1);

            # case 2
            # create new user client as parents
            # when pr_id is "add-new" 

            if (!$parentId = $this->createParentsIfAddNew($data['parentDetails'], $data['studentDetails']))
                throw new Exception('Failed to store new parent', 2);
            

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

    public function import(StoreImportExcelRequest $request)
    {

        $file = $request->file('file');

        $import = new MasterStudentImport();
        $import->onlySheets('Student');
        // $import->import($file);
        Excel::import($import, $file);

        return back()->withSuccess('Student successfully imported');
    }
}
