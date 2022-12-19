<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientParentRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\FindStatusClientTrait;
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
use App\Models\School;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class ClientParentController extends Controller
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

    public function __construct(ClientRepositoryInterface $clientRepository, SchoolRepositoryInterface $schoolRepository, LeadRepositoryInterface $leadRepository, EventRepositoryInterface $eventRepository, EdufLeadRepositoryInterface $edufLeadRepository, ProgramRepositoryInterface $programRepository, UniversityRepositoryInterface $universityRepository, MajorRepositoryInterface $majorRepository, CurriculumRepositoryInterface $curriculumRepository, TagRepositoryInterface $tagRepository, SchoolCurriculumRepositoryInterface $schoolCurriculumRepository)
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
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            return $this->clientRepository->getAllClientByRoleAndStatusDataTables('Parent');

        }

        return view('pages.client.parent.index');
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
        $curriculums = $this->curriculumRepository->getAllCurriculum();
        $childrens = $this->clientRepository->getAllClientByRole('Student');
        $leads = $this->leadRepository->getAllMainLead();
        $events = $this->eventRepository->getAllEvents();
        $ext_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        $programsB2BB2C = $this->programRepository->getAllProgramByType('B2B/B2C');
        $programsB2C = $this->programRepository->getAllProgramByType('B2C');
        $programs = $programsB2BB2C->merge($programsB2C);
        $countries = $this->tagRepository->getAllTags();
        $majors = $this->majorRepository->getAllMajors();

        return view('pages.client.parent.form')->with(
            [
                'schools' => $schools,
                'curriculums' => $curriculums,
                'childrens' => $childrens,
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

    public function store(StoreClientParentRequest $request)
    {
        $parentDetails = $request->only([
            'pr_firstname',
            'pr_lastname',
            'pr_mail',
            'pr_phone',
            'pr_dob',
            'pr_insta',
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
            'graduation_year',
            // 'st_abrcountry',
            'st_note',
        ]);

        $parentDetails['first_name'] = $request->pr_firstname;
        $parentDetails['last_name'] = $request->pr_lastname;
        $parentDetails['mail'] = $request->pr_mail;
        $parentDetails['phone'] = $request->pr_phone;
        $parentDetails['dob'] = $request->pr_dob;
        $parentDetails['insta'] = $request->pr_insta;

        // $parentDetails['st_abrcountry'] = json_encode($request->st_abrcountry);
        $childrenId = $request->child_id;

        # set lead_id based on lead_id & kol_lead_id
        # when lead_id is kol
        # then put kol_lead_id to lead_id
        # otherwise
        # when lead_id is not kol 
        # then lead_id is lead_id
        if ($request->lead_id == "kol") {

            unset($parentDetails['lead_id']);
            $parentDetails['lead_id'] = $request->kol_lead_id;
        }

        $studentDetails = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'mail' => $request->mail,
            'phone' => $request->phone,
            'state' => $parentDetails['state'],
            'city' => $parentDetails['city'],
            'postal_code' => $parentDetails['postal_code'],
            'address' => $parentDetails['address'],
            'lead_id' => $parentDetails['lead_id'],
            'eduf_id' => $parentDetails['eduf_id'],
            'event_id' => $parentDetails['event_id'],
            'st_levelinterest' => $parentDetails['st_levelinterest'],
            'st_grade' => $request->st_grade,
            'st_abryear' => $request->st_abryear,
            'graduation_year' => $request->graduation_year
        ];

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


                # create index sch_id to student details
                # filled with a new school id that was inserted before
                unset($parentDetails['sch_id']);
                $studentDetails['sch_id'] = $school->sch_id;

            }

            # case 2
            # create new user client as student
            # when child_id is "add-new" 
            if (isset($request->child_id) && $request->child_id == "add-new") {                

                if (!$student = $this->clientRepository->createClient('Student', $studentDetails))
                    throw new Exception('Failed to store new student', 2);

                $newStudentId = $student->id;
            }


            # case 3
            # create new user client as parent
            if (!$parent = $this->clientRepository->createClient('Parent', $parentDetails))
                throw new Exception('Failed to store new parent', 3);

            $parentId = $parent->id;

            # case 4
            # add relation between parent and student
            # if they didn't insert parents which parentId = NULL
            # then assumed that register for student only
            # so no need to create parent children relation
            if ($newStudentId !== NULL) {

                if (!$this->clientRepository->createClientRelation($parentId, $newStudentId))
                    throw new Exception('Failed to store relation between student and parent', 4);
            }
        

            # case 5
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
                    throw new Exception('Failed to store destination country', 5);
            }

            # case 6
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
    
                if (!$this->clientRepository->createInterestProgram($parentId, $interestProgramDetails))
                    throw new Exception('Failed to store interest program', 6);
            }

            # case 7
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
                    throw new Exception('Failed to store interest universities', 7);
            }


            # case 8
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
                    throw new Exception('Failed to store interest major', 8);
            }


            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();

            switch ($e->getCode()) {
                case 1:
                    Log::error('Store school failed from parent : ' . $e->getMessage());
                    break;

                case 2:
                    Log::error('Store student failed from parent : ' . $e->getMessage());
                    break;

                case 3:
                    Log::error('Store parent failed : ' . $e->getMessage());
                    break;

                case 4:
                    Log::error('Store relation between student and parent failed : ' . $e->getMessage());
                    break;

                case 5:
                    Log::error('Store destination country failed : ' . $e->getMessage());
                    break;

                case 6:
                    Log::error('Store interest program failed : ' . $e->getMessage());
                    break;

                case 7:
                    Log::error('Store interest universities failed : ' . $e->getMessage());
                    break;

                case 8:
                    Log::error('Store interest major failed : ' . $e->getMessage());
                    break;
                }
                
            Log::error('Store a new parent failed : ' . $e->getMessage());
            return Redirect::to('client/parent/create')->withError($e->getMessage());

        }

        return Redirect::to('client/parent')->withSuccess('A new parent has been registered.');
    }
}
