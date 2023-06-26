<?php

namespace App\Console\Commands;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportStudent extends Command
{
    use CreateCustomPrimaryKeyTrait;
    use StandardizePhoneNumberTrait;
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
            $progressBar = $this->output->createProgressBar($crm_students->count());
            $progressBar->start();
            foreach ($crm_students as $student) {

                $school = $this->createSchoolIfNotExists($student);

                $lead = $this->createLeadIfNotExists($student);

                $edufId = $this->createExternalEdufairIfNotExists($student); # already returned id, that's why the name is edufId

                $studentNumPrimaryKey = $this->createStudentIfNotExists($student, $school, $lead, $edufId);

                $this->attachInterestedProgramIfNotExists($student, $studentNumPrimaryKey);

                $this->createParentsIfNotExists($student, $studentNumPrimaryKey);

                $this->createAbroadCountryIfNotExists($student, $studentNumPrimaryKey);

                $this->attachDreamUniversities($student, $studentNumPrimaryKey);

                $this->attachDreamMajors($student, $studentNumPrimaryKey);
                $progressBar->advance();
            }
            $progressBar->finish();
            DB::commit();
            Log::info('Import Student works fine');
        } catch (Exception $e) {

            DB::rollBack();
            Log::warning('Failed to import student : ' . $e->getMessage() . ' | Line : ' . $e->getLine());
        }

        return Command::SUCCESS;
    }

    private function createSchoolIfNotExists($student)
    {
        $studentHasSchool = $school = $student->school;
        # check if school id is exists on database v2
        if ($studentHasSchool) {
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
                $lead_id_with_label = 'LS' . $this->add_digit((int) $lead_id_without_label + 1, 3);

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
                $extEdufairDetails['ext_pic_phone'] = $this->getValueWithoutSpace($studentHasEduf->eduf_picphone) != NULL ? $this->setPhoneNumber($this->getValueWithoutSpace($studentHasEduf->eduf_picphone)) : NULL;
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
        $studentName = $student->st_firstname . ' ' . $student->st_lastname;

        # if the student does not exist in database v2
        # check using name because there are some students that doesn't have st_id
        if (!$selectedStudent = $this->clientRepository->getStudentByStudentName($studentName)) {

            # check the st id 
            if ($this->clientRepository->getStudentByStudentId($studentId))
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

            $sec_phone_info = $sec_mail_info = null;
            $student_phone = $phone1 = $this->getValueWithoutSpace($student->st_phone);
            if ($student_phone != NULL) {
                # check the phone number
                # if it's more than one phone number 
                # then split into 2 variables
                if (stripos($student_phone, ',')) {
                    $exp_phone = explode(',', $student_phone);
                    $combination1 = $phone1 = trim($exp_phone[0]);
                    $combination2 = $phone2 = trim($exp_phone[1]);

                    if (preg_match('#(?<=\d) (?=[a-z]|\()#i', $combination1)) {
                        [$phone1, $phone1_desc] = preg_split('#(?<=\d) (?=[a-z]|\()#i', $combination1);
                    }

                    if (preg_match('#(?<=\d) (?=[a-z]|\()#i', $combination2) && $combination2 != NULL && $combination2 != '') {
                        [$phone2, $phone2_desc] = preg_split('#(?<=\d) (?=[a-z]|\()#i', $combination2);
                    }
                } elseif (stripos($student_phone, ';')) {

                    $exp_phone = explode(';', $student_phone);
                    $combination1 = $phone1 = $exp_phone[0];
                    $combination2 = $phone2 = $exp_phone[1];

                    if (preg_match('#(?<=\d) (?=[a-z]|\()#i', $combination1)) {
                        [$phone1, $phone1_desc] = preg_split('#(?<=\d) (?=[a-z]|\()#i', $combination1);
                    }

                    if (preg_match('#(?<=\d) (?=[a-z]|\()#i', $combination2) && $combination2 != NULL && $combination2 != '') {
                        [$phone2, $phone2_desc] = preg_split('#(?<=\d) (?=[a-z]|\()#i', $combination2);
                    }
                } else {
                    if (preg_match('#(?<=\d) (?=[a-z]|\()#i', $student_phone)) {
                        if (count(preg_split('#(?<=\d) (?=[a-z]|\()#i', $student_phone)) > 2) {
                            [$phone1, $combination, $phone2_desc] = preg_split('#(?<=\d) (?=[a-z]|\()#i', $student_phone);
                            [$phone1_desc, $phone_2] = preg_split('#(?<=) (?=[0-9])#i', $combination);
                        } else {
                            [$phone1, $phone1_desc] = preg_split('#(?<=\d) (?=[a-z]|\()#i', $student_phone);
                        }
                    }
                }

                # remove , from phone number
                $phone1 = str_replace(',', '', $phone1);

                # remove - from phone number
                $phone1 = str_replace('-', '', $phone1);

                # remove space from phone number
                $phone1 = str_replace(' ', '', $phone1);

                # add the normalization indonesian number +62
                switch (substr($phone1, 0, 1)) {

                        # check if the first character is 0
                    case 0:
                        $phone1 = "+62" . substr($phone1, 1);
                        break;

                        # check if the first character is 6 like 62
                    case 6:
                        $phone1 = "+" . $phone1;
                        break;

                    case "+":
                        $phone1 = $phone1;
                        break;

                    default:
                        $phone1 = "+62" . $phone1;
                }

                if (isset($phone2) && $phone2 != "" && $phone2 != NULL) {
                    # remove , from phone number
                    $phone2 = str_replace(',', '', $phone2);

                    # remove - from phone number
                    $phone2 = str_replace('-', '', $phone2);

                    # remove space from phone number
                    $phone2 = str_replace(' ', '', $phone2);

                    # check if the first character is 0
                    switch (substr($phone2, 0, 1)) {

                            # check if the first character is 0
                        case 0:
                            $phone2 = "+62" . substr($phone2, 1);
                            break;

                            # check if the first character is 6 like 62
                        case 6:
                            $phone2 = "+" . $phone2;
                            break;

                        case "+":
                            $phone2 = $phone2;
                            break;

                        default:
                            $phone2 = "+62" . $phone2;
                    }

                    $sec_phone_info = [
                        'category' => 'phone',
                        'value' => $phone2,
                        'description' => $phone2_desc ?? NULL,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }
            }

            # check the email
            # if he/she has more than one email address
            if ($mail1 = $this->getValueWithoutSpace($student->st_mail)) {
                $student_mail = $mail1;
                if (stripos($student_mail, ',')) {
                    $exp_mail = explode(',', $student_mail);
                    $mail1 = trim($exp_mail[0]);
                    $mail2 = trim($exp_mail[1]);

                    if ($mail2 != NULL && $mail2 != "" && !empty($mail2)) {
                        $sec_mail_info = [
                            'category' => 'mail',
                            'value' => $mail2,
                            'description' => NULL,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];
                    }
                } else if (stripos($student_mail, ';')) {
                    $exp_mail = explode(';', $student_mail);
                    $mail1 = trim($exp_mail[0]);
                    $mail2 = trim($exp_mail[1]);

                    if ($mail2 != NULL && $mail2 != "") {
                        $sec_mail_info = [
                            'category' => 'mail',
                            'value' => $mail2,
                            'description' => NULL,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];
                    }
                }
            }

            if (isset($phone1_desc)) {
                $phone1_desc = preg_match('#([a-z])#i', $phone1_desc) ? $phone1_desc : NULL;
            }

            # import a new student
            $studentDetails = [
                'st_id' => $studentId ?? null,
                'first_name' => $this->getValueWithoutSpace($student->st_firstname),
                'last_name' => $this->getValueWithoutSpace($student->st_lastname),
                'mail' => $mail1,
                'phone' => $phone1,
                'phone_desc' => $phone1_desc ?? null,
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
                // 'st_password' => $this->setPassword($student->st_password),
                'created_at' => $student->st_datecreate,
                'updated_at' => $student->st_datelastedit,
            ];

            $selectedStudent = $this->clientRepository->createClient('Student', $studentDetails);

            if ($sec_mail_info || $sec_phone_info) {
                $additionalInfo = [];
                if ($sec_mail_info) {
                    $sec_mail_info['client_id'] = $selectedStudent->id;
                    array_push($additionalInfo, $sec_mail_info);
                }

                if ($sec_phone_info) {
                    $sec_phone_info['client_id'] = $selectedStudent->id;
                    array_push($additionalInfo, $sec_phone_info);
                }

                # insert secondary email or phone number 
                # into student additional info
                $this->clientRepository->createClientAdditionalInfo($additionalInfo);
            }
        }
        // $this->info('Child Name : '.$selectedStudent->first_name.' '.$selectedStudent->last_name.'\n');
        return $selectedStudent->id;
    }

    private function attachInterestedProgramIfNotExists($student, $studentNumPrimaryKey)
    {
        # check student interested program
        $studentHasInterestedProgram = $student->interestedProgram;
        if ($studentHasInterestedProgram != NULL) {
            $interestedProgramId = $studentHasInterestedProgram->prog_id;
            # check if program does not exists in database v2
            if (!$program = $this->programRepository->getProgramById($interestedProgramId)) {
                # special case
                # because career exploration has changed to experiential learning

                $mainProgName = $studentHasInterestedProgram->prog_main;
                if ($mainProgName == 'Career Exploration')
                    $mainProgName = 'Experiential Learning';

                $main_prog = $this->mainProgRepository->getMainProgByName($mainProgName);
                // $this->info('nama sub prog : '.$studentHasInterestedProgram->prog_sub);

                if ($studentHasInterestedProgram->prog_sub != NULL || empty($studentHasInterestedProgram)) {
                    $sub_prog = $this->subProgRepository->getSubProgBySubProgName($studentHasInterestedProgram->prog_sub);
                    // $this->info('sub program yg keinsert : '.json_encode($sub_prog));
                    $sub_prog_id = $sub_prog->id;
                }

                $programDetails = [
                    'prog_id' => $studentHasInterestedProgram->prog_id,
                    'main_prog_id' => $main_prog->id, //!
                    'sub_prog_id' => $sub_prog_id ??= null, //!
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
            if ($getAllInterestedProgramAlreadySavedOnV2 = $this->clientRepository->getInterestedProgram($studentNumPrimaryKey)) {
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
        if ($studentHasParent = $student->parent) {
            $parentName = $studentHasParent->pr_firstname . ' ' . $studentHasParent->pr_lastname;

            # if the parent does not exist in database v2
            if (!$parent = $this->clientRepository->getParentByParentName($parentName)) {
                $parents_phone = $this->getValueWithoutSpace($studentHasParent->pr_phone);
                if ($parents_phone != NULL) {
                    $parents_phone = str_replace('-', '', $parents_phone);
                    $parents_phone = str_replace(' ', '', $parents_phone);

                    switch (substr($parents_phone, 0, 1)) {

                        case 0:
                            $parents_phone = "+62" . substr($parents_phone, 1);
                            break;

                        case 6:
                            $parents_phone = "+" . $parents_phone;
                            break;
                    }
                }

                $parentDetails = [
                    'first_name' => $this->getValueWithoutSpace($studentHasParent->pr_firstname),
                    'last_name' => $this->getValueWithoutSpace($studentHasParent->pr_lastname),
                    'mail' => $this->getValueWithoutSpace($studentHasParent->pr_mail),
                    'phone' => $parents_phone,
                    'dob' => $this->getValueWithoutSpace($studentHasParent->pr_dob),
                    'insta' => $this->getValueWithoutSpace($studentHasParent->pr_insta),
                    'state' => $this->getValueWithoutSpace($studentHasParent->pr_state),
                    'address' => $this->getValueWithoutSpace($studentHasParent->pr_address),
                    'st_password' => $this->getValueWithoutSpace($studentHasParent->pr_password),
                    'created_at' => $student->st_datecreate,
                    'updated_at' => $student->st_datelastedit
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
        if ($studentHasInterestedCountry = $student->st_abrcountry) {
            $arrayStudentInterestedCountry = array_unique(array_map('trim', explode(",", $studentHasInterestedCountry)));
            foreach ($arrayStudentInterestedCountry as $key => $value) {

                $countryName = trim($value);
                if ($countryTranslations = $this->countryRepository->getCountryNameByUnivCountry($countryName)) {

                    $regionId = $countryTranslations->has_country->lc_region_id;
                    $region = $this->countryRepository->getRegionByRegionId($regionId);
                    $iso_alpha_2 = $countryTranslations->has_country->iso_alpha_2; # US 
                    $regionName = $region->name;


                    switch ($countryName) {

                        case preg_match('/australia/i', $countryName) == 1:
                            $regionName = "Australia";
                            break;

                        case preg_match("/United State|State|US/i", $countryName) == 1:
                            $regionName = "US";
                            break;

                        case preg_match('/United Kingdom|Kingdom|UK/i', $countryName) == 1:
                            $regionName = "UK";
                            break;

                        case preg_match('/canada/i', $countryName) == 1:
                            $regionName = "Canada";
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
            // $this->info('Ini milik : '.$studentNumPrimaryKey.' dengan nama '.$student->st_firstname);
            // $this->info(json_encode($destinationCountryDetails));

            if (isset($destinationCountryDetails))
                $this->clientRepository->createDestinationCountry($studentNumPrimaryKey, $destinationCountryDetails);
        }
    }

    private function attachDreamUniversities($student, $studentNumPrimaryKey)
    {
        $studentHasAbroadUniv = $student->st_abruniv;
        if ($studentHasAbroadUniv) {

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
                    $univ_id_with_label = 'UNIV-' . $this->add_digit((int)$univ_id_without_label + 1, 3);

                    if ($university_v2 = $this->universityRepository->getUniversityByUnivId($univ_id_with_label))
                        $univ_id_with_label = $university_v2->univ_id;

                    $new_universityId = $univ_id_with_label;

                    $tag = null;
                    if ($countryTranslations = $this->countryRepository->getCountryNameByUnivCountry($crm_university->univ_country)) {

                        $countryName = strtolower($countryTranslations->name);

                        $regionId = $countryTranslations->has_country->lc_region_id;
                        $region = $this->countryRepository->getRegionByRegionId($regionId);
                        $iso_alpha_2 = $countryTranslations->has_country->iso_alpha_2; # US 
                        $regionName = $region->name;

                        switch ($countryName) {

                            case preg_match('/australia/i', $countryName) == 1:
                                $regionName = "Australia";
                                break;

                            case preg_match("/United State|State|US/i", $countryName) == 1:
                                $regionName = "US";
                                break;

                            case preg_match('/United Kingdom|Kingdom|UK/i', $countryName) == 1:
                                $regionName = "UK";
                                break;

                            case preg_match('/canada/i', $countryName) == 1:
                                $regionName = "Canada";
                                break;

                            default:
                                $regionName = "Other";
                        }

                        $tag = $this->tagRepository->getTagByName($regionName);
                    }

                    # if not exists, create a new university
                    $universityDetails = [
                        'univ_id' => $new_universityId,
                        'univ_name' => $crm_universityName,
                        'univ_address' => $crm_university->univ_address,
                        'univ_country' => $crm_university->univ_country,
                        'tag' => isset($tag) ? $tag->id : 7, # 7 means Tag : Other
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
        if ($studentHasDreamMajors = $student->st_abrmajor) {
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
        return $value == "" || $value == "-" || $value == "0000-00-00" || $value == 'N/A' ? NULL : $value;
    }

    private function setPassword($value)
    {
        return $value == "" || $value == null ? NULL : $value;
    }
}
