<?php

namespace App\Http\Controllers;

use App\Exceptions\StoreNewSchoolException;
use App\Http\Requests\StoreClientStudentRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\FindStatusClientTrait;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
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
use App\Models\Lead;
use App\Models\School;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ClientStudentController extends Controller
{
    use CreateCustomPrimaryKeyTrait;
    use FindStatusClientTrait;

    private ClientRepositoryInterface $clientRepository;
    private SchoolRepositoryInterface $schoolRepository;
    private LeadRepositoryInterface $leadRepository;
    private EventRepositoryInterface $eventRepository;
    private EdufLeadRepositoryInterface $edufLeadRepository;
    private ProgramRepositoryInterface $programRepository;
    private UniversityRepositoryInterface $universityRepository;
    private MajorRepositoryInterface $majorRepository;
    private CurriculumRepositoryInterface $curriculumRepository;
    private TagRepositoryInterface $tagRepository;
    private SchoolCurriculumRepositoryInterface $schoolCurriculumRepository;
    private ClientProgramRepositoryInterface $clientProgramRepository;

    public function __construct(ClientRepositoryInterface $clientRepository, SchoolRepositoryInterface $schoolRepository, LeadRepositoryInterface $leadRepository, EventRepositoryInterface $eventRepository, EdufLeadRepositoryInterface $edufLeadRepository, ProgramRepositoryInterface $programRepository, UniversityRepositoryInterface $universityRepository, MajorRepositoryInterface $majorRepository, CurriculumRepositoryInterface $curriculumRepository, TagRepositoryInterface $tagRepository, SchoolCurriculumRepositoryInterface $schoolCurriculumRepository, ClientProgramRepositoryInterface $clientProgramRepository)
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
    }
    
    public function index(Request $request)
    {
        // $statusClient = $request->get('st');
        // $statusClientCode = $this->getStatusClientCode($statusClient);
        // return $this->clientRepository->getAllClientByRoleAndStatusDataTables('Student', $statusClientCode);
        
        if ($request->ajax()) {

            $statusClient = $request->get('st');
            $statusClientCode = $this->getStatusClientCode($statusClient);
            return $this->clientRepository->getAllClientByRoleAndStatusDataTables('Student', $statusClientCode);

        }

        return view('pages.client.student.index');
    }

    public function show(Request $request)
    {
        $studentId = $request->route('student');
        if ($request->ajax()) 
            return $this->clientProgramRepository->getAllClientProgramDataTables($studentId);

        $student = $this->clientRepository->getClientById($studentId);

        return view('pages.client.student.view')->with(
            [
                'student' => $student
            ]
        );
    }

    public function store(StoreClientStudentRequest $request)
    {

        $studentDetails = $request->only([
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
            'st_grade',
            'lead_id',
            'eduf_id',
            'kol_lead_id',
            'event_id',
            'st_levelinterest',
            'graduation_year',
            'st_abryear',
            'st_abrcountry',
            'st_note',
        ]);

        $studentDetails['st_abrcountry'] = json_encode($request->st_abrcountry);
        $parentId = $request->pr_id;

        # set lead_id based on lead_id & kol_lead_id
        # when lead_id is kol
        # then put kol_lead_id to lead_id
        # otherwise
        # when lead_id is not kol 
        # then lead_id is lead_id
        if ($request->lead_id == "kol") {

            unset($studentDetails['lead_id']);
            $studentDetails['lead_id'] = $request->kol_lead_id;
        }

        DB::beginTransaction();
        try {

            # case 1
            # create new school
            # when sch_id is "add-new" 
            if ($request->sch_id == "add-new") {

                $schoolDetails = $request->only([
                    'sch_name',
                    // 'sch_location',
                    'sch_type',
                    'sch_score',
                ]);
    
                $last_id = School::max('sch_id');
                $school_id_without_label = $this->remove_primarykey_label($last_id, 4);
                $school_id_with_label = 'SCH-' . $this->add_digit($school_id_without_label + 1, 4);

                if (!$school = $this->schoolRepository->createSchool(['sch_id' => $school_id_with_label] + $schoolDetails))
                    throw new Exception('Failed to store new school', 1);
                
                # insert school curriculum
                if (!$this->schoolCurriculumRepository->createSchoolCurriculum($school_id_with_label, $request->sch_curriculum))
                    throw new Exception('Failed to store school curriculum', 1);


                # remove field sch_id from student detail if exist
                unset($studentDetails['sch_id']);

                # create index sch_id to student details
                # filled with a new school id that was inserted before
                $studentDetails['sch_id'] = $school->sch_id;

            }


            # case 2
            # create new user client as parents
            # when pr_id is "add-new" 
            if (isset($request->pr_id) && $request->pr_id == "add-new") {

                $parentDetails = [
                    'first_name' => $request->pr_firstname,
                    'last_name' => $request->pr_lastname,
                    'mail' => $request->pr_mail,
                    'phone' => $request->pr_phone,
                    'state' => $studentDetails['state'],
                    'city' => $studentDetails['city'],
                    'postal_code' => $studentDetails['postal_code'],
                    'address' => $studentDetails['address'],
                    'lead_id' => $studentDetails['lead_id'],
                    'eduf_id' => $studentDetails['eduf_id'],
                    'event_id' => $studentDetails['event_id'],
                    'st_levelinterest' => $studentDetails['st_levelinterest'],
                    'st_note' => $studentDetails['st_note'],
                ];

                if (!$parent = $this->clientRepository->createClient('Parent', $parentDetails))
                    throw new Exception('Failed to store new parent', 2);

                $parentId = $parent->id;
            }


            # case 3
            # create new user client as student
            if (!$newStudent = $this->clientRepository->createClient('Student', $studentDetails))
                throw new Exception('Failed to store new student', 3);

            $newStudentId = $newStudent->id;

            # case 4
            # add relation between parent and student
            # if they didn't insert parents which parentId = NULL
            # then assumed that register for student only
            # so no need to create parent children relation
            if ($parentId !== NULL) {

                if (!$this->clientRepository->createClientRelation($parentId, $newStudentId))
                    throw new Exception('Failed to store relation between student and parent', 4);
            }
            

            # case 5
            # create interested program
            # if they didn't insert interested program 
            # then skip this case
            if (isset($request->prog_id) && count($request->prog_id) > 0) {

                for ($i = 0 ; $i < count($request->prog_id) ; $i++) {
                    $interestProgramDetails[] = [
                        'prog_id' => $request->prog_id[$i],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }
    
                if (!$this->clientRepository->createInterestProgram($newStudentId, $interestProgramDetails))
                    throw new Exception('Failed to store interest program', 5);
            }

            # case 6.1
            # create destination countries
            # if they didn't insert destination countries
            # then skip this case
            if (isset($request->st_abrcountry) && count($request->st_abrcountry) > 0) {

                # hari senin lanjutin utk insert destination countries
                # dan hubungin score nya melalui client view
                for ($i = 0 ; $i < count($request->st_abrcountry) ; $i++) {
                    $destinationCountryDetails[] = [
                        'tag_id' => $this->tagRepository->getTagById($request->st_abrcountry[$i])->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }

                if (!$this->clientRepository->createDestinationCountry($newStudentId, $destinationCountryDetails))
                    throw new Exception('Failed to store destination country', 6);
            }

            # case 6.2
            # create interested universities
            # if they didn't insert universities
            # then skip this case
            if (isset($request->st_abruniv) && count($request->st_abruniv) > 0) {

                for ($i = 0 ; $i < count($request->st_abruniv) ; $i++) {
                    $interestUnivDetails[] = [
                        'univ_id' => $request->st_abruniv[$i],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }
    
                if (!$this->clientRepository->createInterestUniversities($newStudentId, $interestUnivDetails))
                    throw new Exception('Failed to store interest universities', 6);
            }


            # case 7
            # create interested major
            # if they didn't insert major
            # then skip this case
            if (isset($request->st_abrmajor) && count($request->st_abrmajor) > 0) {

                for ($i = 0 ; $i < count($request->st_abrmajor) ; $i++) {
                    $interestMajorDetails[] = [
                        'major_id' => $request->st_abrmajor[$i],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }
    
                if (!$this->clientRepository->createInterestMajor($newStudentId, $interestMajorDetails))
                    throw new Exception('Failed to store interest major', 7);
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

        return Redirect::to('client/student?st=prospective')->withSuccess('A new student has been registered.');
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
        $programsB2BB2C = $this->programRepository->getAllProgramByType('B2B/B2C');
        $programsB2C = $this->programRepository->getAllProgramByType('B2C');
        $programs = $programsB2BB2C->merge($programsB2C);
        $countries = $this->tagRepository->getAllTags();
        $majors = $this->majorRepository->getAllMajors();

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
        $studentDetails = $request->only([
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
            'st_grade',
            'lead_id',
            'eduf_id',
            'kol_lead_id',
            'event_id',
            'st_levelinterest',
            'graduation_year',
            'st_abryear',
            'st_abrcountry',
            'st_note',
        ]);

        $studentDetails['st_abrcountry'] = json_encode($request->st_abrcountry);
        $parentId = $request->pr_id;

        # set lead_id based on lead_id & kol_lead_id
        # when lead_id is kol
        # then put kol_lead_id to lead_id
        # otherwise
        # when lead_id is not kol 
        # then lead_id is lead_id
        if ($request->lead_id == "kol") {

            unset($studentDetails['lead_id']);
            $studentDetails['lead_id'] = $request->kol_lead_id;
        }

        DB::beginTransaction();
        try {

            # case 1
            # create new school
            # when sch_id is "add-new" 
            if ($request->sch_id == "add-new") {

                $schoolDetails = $request->only([
                    'sch_name',
                    // 'sch_location',
                    'sch_type',
                    'sch_score',
                ]);
    
                $last_id = School::max('sch_id');
                $school_id_without_label = $this->remove_primarykey_label($last_id, 4);
                $school_id_with_label = 'SCH-' . $this->add_digit($school_id_without_label + 1, 4);

                if (!$school = $this->schoolRepository->createSchool(['sch_id' => $school_id_with_label] + $schoolDetails))
                    throw new Exception('Failed to store new school', 1);

                # insert school curriculum
                if (!$this->schoolCurriculumRepository->createSchoolCurriculum($school_id_with_label, $request->sch_curriculum))
                    throw new Exception('Failed to store school curriculum', 1);
                

                # remove field sch_id from student detail if exist
                unset($studentDetails['sch_id']);

                # create index sch_id to student details
                # filled with a new school id that was inserted before
                $studentDetails['sch_id'] = $school->sch_id;

            }


            # case 2
            # create new user client as parents
            # when pr_id is "add-new" 
            if (isset($request->pr_id) && $request->pr_id == "add-new") {

                $parentDetails = [
                    'first_name' => $request->pr_firstname,
                    'last_name' => $request->pr_lastname,
                    'mail' => $request->pr_mail,
                    'phone' => $request->pr_phone,
                    'state' => $studentDetails['state'],
                    'city' => $studentDetails['city'],
                    'postal_code' => $studentDetails['postal_code'],
                    'address' => $studentDetails['address'],
                    'lead_id' => $studentDetails['lead_id'],
                    'eduf_id' => $studentDetails['eduf_id'],
                    'event_id' => $studentDetails['event_id'],
                    'st_levelinterest' => $studentDetails['st_levelinterest'],
                    'st_note' => $studentDetails['st_note'],
                ];

                if (!$parent = $this->clientRepository->createClient('Parent', $parentDetails))
                    throw new Exception('Failed to store new parent', 2);

                $parentId = $parent->id;
            }


            # case 3
            # create new user client as student
            $studentId = $request->route('student');
            if (!$this->clientRepository->updateClient($studentId, $studentDetails))
                throw new Exception('Failed to store new student', 3);


            # case 4
            # add relation between parent and student
            # if they didn't insert parents which parentId = NULL
            # then assumed that register for student only
            # so no need to create parent children relation
            if ($parentId !== NULL) {

                if (!in_array($parentId, $this->clientRepository->getParentsByStudentId($studentId))) {

                    if (!$this->clientRepository->createClientRelation($parentId, $studentId))
                        throw new Exception('Failed to store relation between student and parent', 4);
                }
            }
            

            # case 5
            # create interested program
            # if they didn't insert interested program 
            # then skip this case
            if (isset($request->prog_id) && count($request->prog_id) > 0) {

                $interestProgramDetails = $request->prog_id;
    
                if (!$this->clientRepository->createInterestProgram($studentId, $interestProgramDetails))
                    throw new Exception('Failed to store interest program', 5);
            }

            # case 6.1
            # create destination countries
            # if they didn't insert destination countries
            # then skip this case
            if (isset($request->st_abrcountry) && count($request->st_abrcountry) > 0) {

                # hari senin lanjutin utk insert destination countries
                # dan hubungin score nya melalui client view
                $destinationCountryDetails = $request->st_abrcountry;

                if (!$this->clientRepository->createDestinationCountry($studentId, $destinationCountryDetails))
                    throw new Exception('Failed to store destination country', 6);
            }

            # case 6.2
            # create interested universities
            # if they didn't insert universities
            # then skip this case
            if (isset($request->st_abruniv) && count($request->st_abruniv) > 0) {

                $interestUnivDetails = $request->st_abruniv;
    
                if (!$this->clientRepository->createInterestUniversities($studentId, $interestUnivDetails))
                    throw new Exception('Failed to store interest universities', 6);
            }


            # case 7
            # create interested major
            # if they didn't insert major
            # then skip this case
            if (isset($request->st_abrmajor) && count($request->st_abrmajor) > 0) {

                $interestMajorDetails = $request->st_abrmajor;
    
                if (!$this->clientRepository->createInterestMajor($studentId, $interestMajorDetails))
                    throw new Exception('Failed to store interest major', 7);
            }


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
            return Redirect::to('client/student/'.$studentId.'/edit')->withError($e->getMessage());

        }

        return Redirect::to('client/student/'.$studentId)->withSuccess('A student\'s profile has been updated.');
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
                'message' => "Active status has been updated",
            ]
        );
    }
}
