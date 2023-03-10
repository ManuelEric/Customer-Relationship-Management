<?php

namespace App\Console\Commands;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\CountryRepositoryInterface;
use App\Interfaces\EdufLeadRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Interfaces\MainProgRepositoryInterface;
use App\Interfaces\MajorRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\SubProgRepositoryInterface;
use App\Interfaces\TagRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Models\Lead;
use App\Models\School;
use App\Models\University;
use App\Models\User;
use App\Models\UserClient;
use App\Models\v1\Student;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportStudent extends Command
{
    use CreateCustomPrimaryKeyTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:student';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import a student from crm v1';

    protected ClientRepositoryInterface $clientRepository;
    protected SchoolRepositoryInterface $schoolRepository;
    protected LeadRepositoryInterface $leadRepository;
    protected CorporateRepositoryInterface $corporateRepository;
    protected EdufLeadRepositoryInterface $edufLeadRepository;
    protected ProgramRepositoryInterface $programRepository;
    protected MainProgRepositoryInterface $mainProgRepository;
    protected SubProgRepositoryInterface $subProgRepository;
    protected CountryRepositoryInterface $countryRepository;
    protected TagRepositoryInterface $tagRepository;
    protected UniversityRepositoryInterface $universityRepository;
    protected MajorRepositoryInterface $majorRepository;

    public function __construct(ClientRepositoryInterface $clientRepository, SchoolRepositoryInterface $schoolRepository, LeadRepositoryInterface $leadRepository, CorporateRepositoryInterface $corporateRepository, EdufLeadRepositoryInterface $edufLeadRepository, ProgramRepositoryInterface $programRepository, MainProgRepositoryInterface $mainProgRepository, SubProgRepositoryInterface $subProgRepository, CountryRepositoryInterface $countryRepository, TagRepositoryInterface $tagRepository, UniversityRepositoryInterface $universityRepository, MajorRepositoryInterface $majorRepository)
    {
        parent::__construct();
        $this->clientRepository = $clientRepository;
        $this->schoolRepository = $schoolRepository;
        $this->leadRepository = $leadRepository;
        $this->corporateRepository = $corporateRepository;
        $this->edufLeadRepository = $edufLeadRepository;
        $this->programRepository = $programRepository;
        $this->mainProgRepository = $mainProgRepository;
        $this->subProgRepository = $subProgRepository;
        $this->countryRepository = $countryRepository;
        $this->tagRepository = $tagRepository;
        $this->universityRepository = $universityRepository;
        $this->majorRepository = $majorRepository;
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::beginTransaction();
        try {

            $crm_students = $this->clientRepository->getStudentFromV1();
            foreach ($crm_students as $student) 
            {

                $school = $this->createSchoolIfNotExists($student);
                    
                $lead = $this->createLeadIfNotExists($student);
                
                $edufId = $this->createExternalEdufairIfNotExists($student); # already returned id, that's why the name is edufId
                
                $studentNumPrimaryKey = $this->createStudentIfNotExists($student, $school, $lead, $edufId);
                
                $this->attachInterestedProgramIfNotExists($student, $studentNumPrimaryKey);
                
                $this->createParentsIfNotExists($student, $studentNumPrimaryKey);

                $this->createAbroadCountryIfNotExists($student, $studentNumPrimaryKey);
                
                $this->attachDreamUniversities($student, $studentNumPrimaryKey);

                $this->attachDreamMajors($student, $studentNumPrimaryKey);

            }
            DB::commit();
            Log::info('Import Student works fine');
            
        } catch (Exception $e) {
            
            DB::rollBack();
            Log::warning('Failed to import student : '. $e->getMessage());

        }

        return Command::SUCCESS;
    }

    private function createSchoolIfNotExists($student)
    {
        $studentHasSchool = $school = $student->school;
        # check if school id is exists on database v2
        if ($studentHasSchool)
        {
            $schoolName = $studentHasSchool->sch_name;
            if (!$school = $this->schoolRepository->getSchoolByName(strtolower($schoolName))) {

                # initialize
                $last_id = School::max('sch_id');
                $sch_id_without_label = $this->remove_primarykey_label($last_id, 4);
                $sch_id_with_label = 'SCH-' . $this->add_digit($sch_id_without_label + 1, 4);

                $schoolDetails = [
                    'sch_id' => $sch_id_with_label,
                    'sch_name' => $this->getValueWithoutSpace($studentHasSchool->sch_name),
                    'sch_type' => $this->getValueWithoutSpace($studentHasSchool->sch_type),
                    'sch_mail' => $this->getValueWithoutSpace($studentHasSchool->sch_mail),
                    'sch_phone' => $this->getValueWithoutSpace($studentHasSchool->sch_phone),
                    'sch_insta' => $this->getValueWithoutSpace($studentHasSchool->sch_insta),
                    'sch_city' => $this->getValueWithoutSpace($studentHasSchool->sch_city),
                    'sch_location' => $this->getValueWithoutSpace($studentHasSchool->sch_location),
                ];

                $school = $this->schoolRepository->createSchool($schoolDetails);

            }
        }
        return $school;
    }

    private function createLeadIfNotExists($student)
    {
        $studentHasLead = $lead = $student->lead;
        $leadName = $studentHasLead->lead_name;

        # check if lead id is exists on database v2
        if ($studentHasLead) {
            if (!$lead = $this->leadRepository->getLeadByName(strtolower($leadName))) {
                
                # initialize
                $last_id = Lead::max('lead_id');
                $lead_id_without_label = $this->remove_primarykey_label($last_id, 2);
                $lead_id_with_label = 'LS' . $this->add_digit($lead_id_without_label + 1, 3);

                $leadDetails = [
                    'lead_id' => $lead_id_with_label,
                    'main_lead' => $studentHasLead->lead_name
                ];

                $lead = $this->leadRepository->createLead($leadDetails);
            }
        }

        return $lead;
    }

    private function createExternalEdufairIfNotExists($student)
    {
        $edufId = null;
        $studentHasEduf = $student->eduf;
        if ($studentHasEduf) {
            $edufId = $studentHasEduf->eduf_id;
            $organizerName = $studentHasEduf->eduf_organizer;
            $extEdufairDetails = [];
            $extEdufairDetails['sch_id'] = $extEdufairDetails['corp_id'] = null;
            # check if eduf id is exists on database v2
            if ($studentHasEduf && $studentHasEduf != 0) {
                if ($organizerName != "SUN Lampung") {
                    if (!$organizer = $this->schoolRepository->getSchoolByName(strtolower($organizerName))) {

                        # initialize
                        $last_id = School::max('sch_id');
                        $sch_id_without_label = $this->remove_primarykey_label($last_id, 4);
                        $sch_id_with_label = 'SCH-' . $this->add_digit($sch_id_without_label + 1, 4);

                        $schoolDetails = [
                            'sch_id' => $sch_id_with_label,
                            'sch_name' => $studentHasEduf->eduf_organizer,
                        ];

                        $organizer = $this->schoolRepository->createSchool($schoolDetails);

                        $extEdufairDetails['sch_id'] = $organizer->sch_id;
                    }
                } else {
                    if (!$organizer = $this->corporateRepository->getCorporateByName(strtolower($organizerName))) {

                        # initialize
                        $last_id = School::max('sch_id');
                        $corp_id_without_label = $this->remove_primarykey_label($last_id, 5);
                        $corp_id_with_label = 'CORP-' . $this->add_digit($corp_id_without_label + 1, 4);

                        $corporateDetails = [
                            'corp_id' => $corp_id_with_label,
                            'corp_name' => $studentHasEduf->eduf_organizer,
                        ];

                        $organizer = $this->corporateRepository->createCorporate($corporateDetails);

                        $extEdufairDetails['corp_id'] = $organizer->corp_id;
                    }
                }

                # find the pic employee id
                $employeeId = NULL;
                if ($employee = $this->clientRepository->getStudentByStudentName($studentHasEduf->eduf_picallin))
                    $employeeId = $employee->id;

                $extEdufairDetails['intr_pic'] = $employeeId;
                $extEdufairDetails['location'] = strip_tags($studentHasEduf->eduf_place);
                $extEdufairDetails['ext_pic_name'] = $this->getValueWithoutSpace($studentHasEduf->eduf_picname);
                $extEdufairDetails['ext_pic_mail'] = $this->getValueWithoutSpace($studentHasEduf->eduf_picmail);
                $extEdufairDetails['ext_pic_phone'] = $this->getValueWithoutSpace($studentHasEduf->eduf_picphone);
                $extEdufairDetails['first_discussion_date'] = $this->getValueWithoutSpace($studentHasEduf->eduf_firstdisdate);
                $extEdufairDetails['last_discussion_date'] = $this->getValueWithoutSpace($studentHasEduf->eduf_lastdisdate);
                $extEdufairDetails['event_start'] = $studentHasEduf->eduf_eventstartdate;
                $extEdufairDetails['event_end'] = $studentHasEduf->eduf_eventenddate;
                $extEdufairDetails['status'] = $studentHasEduf->eduf_status;
                $extEdufairDetails['notes'] = $studentHasEduf->eduf_notes;

                $eduf = $this->edufLeadRepository->createEdufairLead($extEdufairDetails);
                $edufId = $eduf->id;
            }
        }

        return $edufId;
    }

    private function createStudentIfNotExists($student, $school, $lead, $edufId)
    {
        # reset selected student
        $selectedStudent = null;
        # check student data
        // $studentNumPrimaryKey = $student->st_num;
        $studentId = $student->st_id;
        $studentName = $student->st_firstname.' '.$student->st_lastname;
        
        # if the student does not exist in database v2
        # check using name because there are some students that doesn't have st_id
        if (!$selectedStudent = $this->clientRepository->getStudentByStudentName($studentName))
        {
            
            # check the st id 
            if ($this->clientRepository->getStudentByStudentId($studentId) || $studentId == NULL)
            {
                # initialize
                $last_id = Student::max('st_id');
                $student_id_without_label = $this->remove_primarykey_label($last_id, 3);
                $studentId = 'ST-' . $this->add_digit((int) $student_id_without_label + 1, 4);

                if ($this->clientRepository->getStudentByStudentId($studentId))
                {
                    # initialize
                    $last_id = UserClient::max('st_id');
                    $student_id_without_label = $this->remove_primarykey_label($last_id, 3);
                    $studentId = 'ST-' . $this->add_digit((int) $student_id_without_label + 1, 4);
                }
            }

            # import a new student
            $studentDetails = [
                'st_id' => $studentId ?? null,
                'first_name' => $this->getValueWithoutSpace($student->st_firstname),
                'last_name' => $this->getValueWithoutSpace($student->st_lastname),
                'mail' => $this->getValueWithoutSpace($student->st_mail),
                'phone' => $this->getValueWithoutSpace($student->st_phone),
                'dob' => $this->getValueWithoutSpace($student->st_dob),
                'insta' => $this->getValueWithoutSpace($student->st_insta),
                'state' => $this->getValueWithoutSpace($student->st_state),
                'city' => $this->getValueWithoutSpace($student->st_city),
                'address' => $this->getValueWithoutSpace($student->st_address),
                'sch_id' => isset($school->sch_id) ? $school->sch_id : NULL, //!
                'st_grade' => $this->getValueWithoutSpace($student->st_grade),
                'lead_id' => $lead->lead_id, //!
                'eduf_id' => $edufId, //!
                'st_levelinterest' => $student->st_levelinterest,
                'st_abryear' => $student->st_abryear,
                'st_statusact' => $student->st_statusact == NULL ? 0 : $student->st_statusact,
                'st_statuscli' => $student->st_statuscli,
                'st_password' => $student->st_password,
                'created_at' => $student->st_datecreate,
                'updated_at' => $student->st_datelastedit,
            ];

            $selectedStudent = $this->clientRepository->createClient('Student', $studentDetails);
        }  
        $this->info('Child Name : '.$selectedStudent->first_name.' '.$selectedStudent->last_name.'\n');
        return $selectedStudent->id;
        
    }

    private function attachInterestedProgramIfNotExists($student, $studentNumPrimaryKey)
    {
        # check student interested program
        $studentHasInterestedProgram = $student->interestedProgram;
        if ($studentHasInterestedProgram != NULL)
        {
            $interestedProgramId = $studentHasInterestedProgram->prog_id;
            # check if program does not exists in database v2
            if (!$program = $this->programRepository->getProgramById($interestedProgramId))
            {
                $main_prog = $this->mainProgRepository->getMainProgByName($studentHasInterestedProgram->prog_main);
                $sub_prog = $this->subProgRepository->getSubProgBySubProgName($studentHasInterestedProgram->prog_sub);
    
                $programDetails = [
                    'prog_id' => $studentHasInterestedProgram->prog_id,
                    'main_prog_id' => $main_prog->id, //!
                    'sub_prog_id'=> $sub_prog->id, //!
                    'prog_main' => $studentHasInterestedProgram->prog_main,
                    'main_number' => $studentHasInterestedProgram->main_number,
                    'prog_sub' => $studentHasInterestedProgram->prog_sub,
                    'prog_program' => $studentHasInterestedProgram->prog_program,
                    'prog_type' => $studentHasInterestedProgram->prog_type,
                    'prog_mentor' => $studentHasInterestedProgram->prog_mentor,
                    'prog_payment' => $studentHasInterestedProgram->prog_payment,
                ];
    
                $program = $this->programRepository->createProgram($programDetails);
            }
    
            $interestProgramDetails = [];
            if ($getAllInterestedProgramAlreadySavedOnV2 = $this->clientRepository->getInterestedProgram($studentNumPrimaryKey))
            {
                foreach ($getAllInterestedProgramAlreadySavedOnV2 as $interestedProgram) {
        
                    # saved interested program from v2
                    $interestProgramDetails[] = [
                        'prog_id' => $interestedProgram->prog_id,
                    ];
                }
            }
    
            # add interested program from v1 to interest program v2
            $interestProgramDetails[] = [
                'prog_id' => $program->prog_id,
            ];
    
            # store interest program
            $this->clientRepository->createInterestProgram($studentNumPrimaryKey, $interestProgramDetails);
        }
    }

    private function createParentsIfNotExists($student, $studentNumPrimaryKey)
    {
        # check parents data
        if ($studentHasParent = $student->parent)
        {
            $parentName = $studentHasParent->pr_firstname.' '.$studentHasParent->pr_lastname;

            # if the parent does not exist in database v2
            if (!$parent = $this->clientRepository->getParentByParentName($parentName))
            {
                $parentDetails = [
                    'first_name' => $this->getValueWithoutSpace($studentHasParent->pr_firstname),
                    'last_name' => $this->getValueWithoutSpace($studentHasParent->pr_lastname),
                    'mail' => $this->getValueWithoutSpace($studentHasParent->pr_mail),
                    'phone' => $this->getValueWithoutSpace($studentHasParent->pr_phone),
                    'dob' => $this->getValueWithoutSpace($studentHasParent->pr_dob),
                    'insta' => $this->getValueWithoutSpace($studentHasParent->pr_insta),
                    'state' => $this->getValueWithoutSpace($studentHasParent->pr_state),
                    'address' => $this->getValueWithoutSpace($studentHasParent->pr_address),
                    'st_password' => $this->getValueWithoutSpace($studentHasParent->pr_password)
                ];
                
                $parent = $this->clientRepository->createClient('Parent', $parentDetails);
                
            } 

            # create relation between student & parent
            if ($parentNumPrimaryKey = $parent->id)
                $this->clientRepository->createClientRelation($parentNumPrimaryKey, $studentNumPrimaryKey);
        }
    }

    private function createAbroadCountryIfNotExists($student, $studentNumPrimaryKey)
    {
        $destinationCountryDetails = []; # default
        if ($studentHasInterestedCountry = $student->st_abrcountry)
        {
            $arrayStudentInterestedCountry = array_unique(array_map('trim', explode(",", $studentHasInterestedCountry)));
            foreach ($arrayStudentInterestedCountry as $key => $value) {
    
                $countryName = trim($value);
                if ($countryTranslations = $this->countryRepository->getCountryNameByUnivCountry($countryName))
                {

                    $regionId = $countryTranslations->has_country->lc_region_id;
                    $region = $this->countryRepository->getRegionByRegionId($regionId);
                    $iso_alpha_2 = $countryTranslations->has_country->iso_alpha_2; # US 
                    $regionName = $region->name;
                    
        
                    switch ($countryName) {
            
                        case preg_match("/United State|State|US/i", $countryName) == 1:
                            $regionName = "US";
                            break;
        
                        case preg_match('/United Kingdom|Kingdom|UK/i', $countryName) == 1:
                            $regionName = "UK";
                            break;
        
                        case preg_match('/canada/i', $countryName) == 1:
                            $regionName = "Canada";
                            break;
        
                        case preg_match('/australia/i', $countryName) == 1:
                            $regionName = "Australia";
                            break;

                        default: 
                            $regionName = "Other";
        
                    }
        
                    $tag = $this->tagRepository->getTagByName($regionName);
        
                    $destinationCountryDetails[] = [
                        'tag_id' => $tag->id,
                        'country_name' => $countryName,
                    ];
                    
                    
                }
            }
            $this->info('Ini milik : '.$studentNumPrimaryKey.' dengan nama '.$student->st_firstname);
            $this->info(json_encode($destinationCountryDetails));

            if (isset($destinationCountryDetails))
                $this->clientRepository->createDestinationCountry($studentNumPrimaryKey, $destinationCountryDetails);
        }
        
    }

    private function attachDreamUniversities($student, $studentNumPrimaryKey)
    {
        $studentHasAbroadUniv = $student->st_abruniv;
        if ($studentHasAbroadUniv)
        {

            $arrayStudentAbroadUniv = explode(',', $studentHasAbroadUniv);
            foreach ($arrayStudentAbroadUniv as $key => $value) {
                
                $univId = $value;
                $crm_university = $this->universityRepository->getUniversityFromCRMByUnivId($univId);
                
                $crm_universityName = $crm_university->univ_name;
    
                # check if uni exists or not
                if (!$university = $this->universityRepository->getUniversityByName($crm_universityName)) {
    
                    # initialize
                    $last_id = University::max('univ_id');
                    $univ_id_without_label = $this->remove_primarykey_label($last_id, 5);
                    $univ_id_with_label = 'LS' . $this->add_digit($univ_id_without_label + 1, 3);
    
                    # if not exists, create a new university
                    $universityDetails = [
                        'univ_id' => $univ_id_with_label,
                        'univ_name' => $crm_universityName,
                        'univ_address' => $crm_university->univ_address,
                        'univ_country' => $crm_university->univ_country,
                    ];
    
                    $university = $this->universityRepository->createUniversity($universityDetails);
                }
    
                $interestedUnivDetails[] = $university->univ_id;
    
            }
            $this->clientRepository->createInterestUniversities($studentNumPrimaryKey, $interestedUnivDetails);
        }
    }

    private function attachDreamMajors($student, $studentNumPrimaryKey)
    {
        # when student has st_abrmajor
        if ($studentHasDreamMajors = $student->st_abrmajor)
        {
            $arrayStudentDreamMajors = explode(',', $studentHasDreamMajors);
            foreach ($arrayStudentDreamMajors as $key => $value) {

                $crm_majorName = trim($value);
                if (!$major = $this->majorRepository->getMajorByName($crm_majorName)) {

                    $majorDetails = [
                        'name' => $crm_majorName,
                    ];

                    # create new major
                    $major = $this->majorRepository->createMajor($majorDetails);

                }
                
                $dreamsMajor[] = $major->id;
            }

            $this->clientRepository->createInterestMajor($studentNumPrimaryKey, $dreamsMajor);
        }
    }

    private function getValueWithoutSpace($value)
    {
        return $value == "" || $value == "0000-00-00" ? NULL : $value;
    }
}
