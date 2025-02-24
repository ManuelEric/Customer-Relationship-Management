<?php

namespace App\Http\Controllers\Module;

use App\Enum\LogModule;
use App\Http\Controllers\Controller;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\TagRepositoryInterface;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ClientController extends Controller
{
    protected SchoolRepositoryInterface $schoolRepository;
    protected ClientRepositoryInterface $clientRepository;
    protected TagRepositoryInterface $tagRepository;

    public function __construct(ClientRepositoryInterface $clientRepository, SchoolRepositoryInterface $schoolRepository, TagRepositoryInterface $tagRepository)
    {
        $this->schoolRepository = $schoolRepository;
        $this->clientRepository = $clientRepository;
        $this->tagRepository = $tagRepository;
    }

    private function basicVariables(string $client_type, $request)
    {
        # the difference information
        # depends on client type (student, parent, teacher)
        switch ($client_type) {

            case "parent":
                $parent_details = $request->only([
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
                    'gap_year',
                    // 'st_abrcountry',
                    'st_note',
                    'referral_code'
                ]);

                $parent_details['first_name'] = $request->pr_firstname;
                $parent_details['last_name'] = $request->pr_lastname;
                $parent_details['mail'] = $request->pr_mail;
                $parent_details['phone'] = $this->tnSetPhoneNumber($request->pr_phone);
                $parent_details['dob'] = $request->pr_dob;
                $parent_details['insta'] = $request->pr_insta;
                $parent_details['is_verified'] = 'Y';
                // $parent_details['st_abrcountry'] = json_encode($request->st_abrcountry);

                # set lead_id based on lead_id & kol_lead_id 
                # when lead_id is kol then put kol_lead_id to lead_id
                # otherwise
                # when lead_id is not kol then lead_id is lead_id
                if ($request->lead_id == "kol") {
                    unset($parent_details['lead_id']);
                    $parent_details['lead_id'] = $request->kol_lead_id;
                }

                $student_details = [
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'mail' => $request->mail,
                    'phone' => $this->tnSetPhoneNumber($request->phone),
                    'state' => $parent_details['state'],
                    'city' => $parent_details['city'],
                    'postal_code' => $parent_details['postal_code'],
                    'address' => $parent_details['address'],
                    'lead_id' => $parent_details['lead_id'],
                    'eduf_id' => $parent_details['eduf_id'],
                    'event_id' => $parent_details['event_id'],
                    'st_levelinterest' => $parent_details['st_levelinterest'],
                    'st_grade' => $request->st_grade,
                    'st_abryear' => $request->st_abryear,
                    'graduation_year' => $request->graduation_year,
                    'gap_year' => $request->gap_year,
                    'is_funding' => $request->is_funding ?? 0,
                    'register_by' => $request->register_by,
                    'referral_code' => $request->referral_code,
                    'is_verified' => 'Y'
                ];

                if (isset($request->is_funding))
                    $student_details['is_funding'] = $request->is_funding;

                return compact('student_details', 'parent_details');
                break;

            case "student":
                $student_details = $request->only([
                    'first_name',
                    'last_name',
                    'mail',
                    'dob',
                    'insta',
                    'country',
                    'state',
                    'city',
                    'postal_code',
                    'address',
                    'st_grade',
                    'lead_id',
                    'eduf_id',
                    'kol_lead_id',
                    'event_id',
                    'st_levelinterest',
                    'graduation_year',
                    'gap_year',
                    'st_abryear',
                    // 'st_abrcountry',
                    'st_note',
                    'pr_id',
                    'pr_id_old',
                    // 'is_funding',
                    'register_by',
                    'referral_code'
                ]);

                // update also the gradenow //! not used
                // $student_details['grade_now'] = $student_details['st_grade'];

                if (isset($request->is_funding))
                    $student_details['is_funding'] = $request->is_funding;

                $student_details['phone'] = $this->tnSetPhoneNumber($request->phone);
                $student_details['is_verified'] = "Y";
                // $student_details['st_abrcountry'] = json_encode($request->st_abrcountry);

                # set lead_id based on lead_id & kol_lead_id 
                # when lead_id is kol then put kol_lead_id to lead_id
                # otherwise
                # when lead_id is not kol then lead_id is lead_id
                if ($request->lead_id == "kol") {
                    unset($student_details['lead_id']);
                    $student_details['lead_id'] = $request->kol_lead_id;
                }

                # initiate variable parent details
                // $parent_details = [
                //     'first_name' => $request->pr_firstname,
                //     'last_name' => $request->pr_lastname,
                //     'mail' => $request->pr_mail,
                //     'phone' => $this->tnSetPhoneNumber($request->pr_phone),
                //     'state' => $student_details['state'],
                //     'city' => $student_details['city'],
                //     'postal_code' => $student_details['postal_code'],
                //     'address' => $student_details['address'],
                //     'lead_id' => $student_details['lead_id'],
                //     'eduf_id' => $student_details['eduf_id'],
                //     'event_id' => $student_details['event_id'],
                //     'st_levelinterest' => $student_details['st_levelinterest'],
                //     'st_note' => $student_details['st_note'],
                //     'is_verified' => 'Y',
                //     'referral_code' => $student_details['referral_code'],
                // ];

                // return compact('student_details', 'parent_details');
                return compact('student_details');
                break;

            case "teacher":
                $teacher_details = $request->only([
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
                $teacher_details['phone'] = $this->tnSetPhoneNumber($request->phone);
                $teacher_details['is_verified'] = 'Y';

                # set lead_id based on lead_id & kol_lead_id
                # when lead_id is kol
                # then put kol_lead_id to lead_id
                # otherwise
                # when lead_id is not kol 
                # then lead_id is lead_id
                if ($request->lead_id == "kol") {

                    unset($teacher_details['lead_id']);
                    $teacher_details['lead_id'] = $request->kol_lead_id;
                }
                unset($teacher_details['kol_lead_id']);

                return compact('teacher_details');
                break;
        }
    }

    public function initializeVariablesForStoreAndUpdate(string $client_type, Request $request)
    {
        # initiate variables student details
        $student_details = $this->basicVariables($client_type, $request)['student_details'] ??= [];
        $parent_details = $this->basicVariables($client_type, $request)['parent_details'] ??= [];
        $teacher_details = $this->basicVariables($client_type, $request)['teacher_details'] ??= [];

        # initiate variable school details
        $school_details = $request->only([
            'sch_id',
            'sch_name',
            // 'sch_location',
            'sch_type',
            'sch_score',
            'sch_curriculum',
        ]);

        # initiate variable interest program details
        // $interestPrograms = $request->prog_id ??= [];

        # initiate variable abroad country details
        $abroad_countries = $request->st_abrcountry ??= [];

        # initiate variable abroad university details
        $abroad_universities = $request->st_abruniv ??= [];

        # initiate variable interest major details
        $interest_majors = $request->st_abrmajor ??= [];

        return compact(
            'student_details',
            'parent_details',
            'teacher_details',
            'school_details',
            // 'interestPrograms',
            'abroad_countries',
            'abroad_universities',
            'interest_majors',
        );
    }

    public function createSchoolIfAddNew(array $school_details)
    {
        # when sch_id is "add-new" 
        $choosen_school = $school_details['sch_id'];
        if ($choosen_school == "add-new") {

            $school_curriculums = $school_details['sch_curriculum'];

            # create a new school
            $school = $this->schoolRepository->createSchoolIfNotExists($school_details, $school_curriculums);
            return $school->sch_id;
        }

        return $choosen_school;
    }

    public function createParentsIfAddNew(array $parent_details, array $student_details)
    {
        # when pr_id is "add-new" 
        $choosen_parent = $student_details['pr_id'];
        if (isset($choosen_parent) && $choosen_parent == "add-new") {

            $parent = $this->clientRepository->createClient('Parent', $parent_details);
            return $parent->id;
        }

        return $choosen_parent ?? $student_details['pr_id_old'];
    }

    public function createDestinationCountries(array $abroad_countries, String $newStudentId)
    {
        if (isset($abroad_countries) && count($abroad_countries) > 0) {

            # hari senin lanjutin utk insert destination countries
            # dan hubungin score nya melalui client view
            for ($i = 0; $i < count($abroad_countries); $i++) {
                $destination_country_details[] = $this->tagRepository->getCountryById($abroad_countries[$i])->id;
            }

            $destination_country = $this->clientRepository->createDestinationCountry($newStudentId, $destination_country_details);
            return $destination_country;
        }

        return true;
    }

    public function createInterestedUniversities(array $abroad_universities, String $newStudentId)
    {
        if (isset($abroad_universities) && count($abroad_universities) > 0) {

            for ($i = 0; $i < count($abroad_universities); $i++) {
                $interest_univ_details[] = $abroad_universities[$i];
            }

            $interested_universities = $this->clientRepository->createInterestUniversities($newStudentId, $interest_univ_details);
            return $interested_universities;
        }

        return true;
    }

    public function createInterestedMajor(array $interest_majors, String $newStudentId)
    {
        if (isset($interest_majors) && count($interest_majors) > 0) {

            for ($i = 0; $i < count($interest_majors); $i++) {
                $interestMajor_details[] = $interest_majors[$i];
            }

            return $this->clientRepository->createInterestMajor($newStudentId, $interestMajor_details);
        }

        return true;
    }

    public function getClientSuggestion(Request $request, LogService $log_service)
    {
        $client_ids = $request->get('clientIds');
        $role_name = $request->get('roleName');
        try {
            $client_suggestion = $this->clientRepository->getClientSuggestion($client_ids, $role_name);
        } catch (Exception $e) {
            $log_service->createErrorLog(LogModule::GET_CLIENT_SUGGESTION, $e->getMessage(), $e->getLine(), $e->getFile(), $client_suggestion->toArray());

        }
        return response()->json(
            [
                'success' => true,
                'data' => $client_suggestion
            ]
        );
    }
}
