<?php

namespace App\Http\Controllers\Module;

use App\Http\Controllers\Controller;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\TagRepositoryInterface;
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

    private function basicVariables(string $clientType, $request)
    {
        # the difference information
        # depends on client type (student, parent, teacher)
        switch ($clientType) {

            case "parent":
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
                $parentDetails['phone'] = $this->setPhoneNumber($request->pr_phone);
                $parentDetails['dob'] = $request->pr_dob;
                $parentDetails['insta'] = $request->pr_insta;
                // $parentDetails['st_abrcountry'] = json_encode($request->st_abrcountry);

                # set lead_id based on lead_id & kol_lead_id 
                # when lead_id is kol then put kol_lead_id to lead_id
                # otherwise
                # when lead_id is not kol then lead_id is lead_id
                if ($request->lead_id == "kol") {
                    unset($parentDetails['lead_id']);
                    $parentDetails['lead_id'] = $request->kol_lead_id;
                }

                $studentDetails = [
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'mail' => $request->mail,
                    'phone' => $this->setPhoneNumber($request->phone),
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
                    'graduation_year' => $request->graduation_year,
                    'is_funding' => $request->is_funding ?? 0,
                    'register_as' => $request->register_as
                ];

                if (isset($request->is_funding))
                    $studentDetails['is_funding'] = $request->is_funding;

                return compact('studentDetails', 'parentDetails');
                break;

            case "student":
                $studentDetails = $request->only([
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
                    'st_abryear',
                    // 'st_abrcountry',
                    'st_note',
                    'pr_id',
                    'pr_id_old',
                    // 'is_funding',
                    'register_as'
                ]);

                if (isset($request->is_funding))
                    $studentDetails['is_funding'] = $request->is_funding;

                $studentDetails['phone'] = $this->setPhoneNumber($request->phone);
                // $studentDetails['st_abrcountry'] = json_encode($request->st_abrcountry);

                # set lead_id based on lead_id & kol_lead_id 
                # when lead_id is kol then put kol_lead_id to lead_id
                # otherwise
                # when lead_id is not kol then lead_id is lead_id
                if ($request->lead_id == "kol") {
                    unset($studentDetails['lead_id']);
                    $studentDetails['lead_id'] = $request->kol_lead_id;
                }

                # initiate variable parent details
                $parentDetails = [
                    'first_name' => $request->pr_firstname,
                    'last_name' => $request->pr_lastname,
                    'mail' => $request->pr_mail,
                    'phone' => $this->setPhoneNumber($request->pr_phone),
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

                return compact('studentDetails', 'parentDetails');
                break;

            case "teacher":
                $teacherDetails = $request->only([
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
                ]);
                $teacherDetails['phone'] = $this->setPhoneNumber($request->phone);

                # set lead_id based on lead_id & kol_lead_id
                # when lead_id is kol
                # then put kol_lead_id to lead_id
                # otherwise
                # when lead_id is not kol 
                # then lead_id is lead_id
                if ($request->lead_id == "kol") {

                    unset($teacherDetails['lead_id']);
                    $teacherDetails['lead_id'] = $request->kol_lead_id;
                }
                unset($teacherDetails['kol_lead_id']);

                return compact('teacherDetails');
                break;
        }
    }

    public function initializeVariablesForStoreAndUpdate(string $clientType, Request $request)
    {
        # initiate variables student details
        $studentDetails = $this->basicVariables($clientType, $request)['studentDetails'] ??= [];
        $parentDetails = $this->basicVariables($clientType, $request)['parentDetails'] ??= [];
        $teacherDetails = $this->basicVariables($clientType, $request)['teacherDetails'] ??= [];

        # initiate variable school details
        $schoolDetails = $request->only([
            'sch_id',
            'sch_name',
            // 'sch_location',
            'sch_type',
            'sch_score',
            'sch_curriculum',
        ]);

        # initiate variable interest program details
        $interestPrograms = $request->prog_id ??= [];

        # initiate variable abroad country details
        $abroadCountries = $request->st_abrcountry ??= [];

        # initiate variable abroad university details
        $abroadUniversities = $request->st_abruniv ??= [];

        # initiate variable interest major details
        $interestMajors = $request->st_abrmajor ??= [];

        return compact(
            'studentDetails',
            'parentDetails',
            'teacherDetails',
            'schoolDetails',
            'interestPrograms',
            'abroadCountries',
            'abroadUniversities',
            'interestMajors',
        );
    }

    public function createSchoolIfAddNew(array $schoolDetails)
    {
        # when sch_id is "add-new" 
        $choosen_school = $schoolDetails['sch_id'];
        if ($choosen_school == "add-new") {

            $schoolCurriculums = $schoolDetails['sch_curriculum'];

            # create a new school
            $school = $this->schoolRepository->createSchoolIfNotExists($schoolDetails, $schoolCurriculums);
            return $school->sch_id;
        }

        return $choosen_school;
    }

    public function createParentsIfAddNew(array $parentDetails, array $studentDetails)
    {
        # when pr_id is "add-new" 
        $choosen_parent = $studentDetails['pr_id'];
        if (isset($choosen_parent) && $choosen_parent == "add-new") {

            $parent = $this->clientRepository->createClient('Parent', $parentDetails);
            return $parent->id;
        }

        return $choosen_parent ?? $studentDetails['pr_id_old'];
    }

    public function createInterestedProgram(array $interestPrograms, int $clientId) # clientId can be studentId & parentId
    {
        $interestProgramDetails = array();
        if (isset($interestPrograms) && count($interestPrograms) > 0) {

            for ($i = 0; $i < count($interestPrograms); $i++) {
                $interestProgramDetails[] = [
                    'prog_id' => $interestPrograms[$i]
                ];
            }

            $interestedProgram = $this->clientRepository->createInterestProgram($clientId, $interestProgramDetails);
            return $interestedProgram;
        }

        return true;
    }

    public function createDestinationCountries(array $abroadCountries, int $newStudentId)
    {
        if (isset($abroadCountries) && count($abroadCountries) > 0) {

            # hari senin lanjutin utk insert destination countries
            # dan hubungin score nya melalui client view
            for ($i = 0; $i < count($abroadCountries); $i++) {
                $destinationCountryDetails[] = $this->tagRepository->getTagById($abroadCountries[$i])->id;
            }

            $destinationCountry = $this->clientRepository->createDestinationCountry($newStudentId, $destinationCountryDetails);
            return $destinationCountry;
        }

        return true;
    }

    public function createInterestedUniversities(array $abroadUniversities, int $newStudentId)
    {
        if (isset($abroadUniversities) && count($abroadUniversities) > 0) {

            for ($i = 0; $i < count($abroadUniversities); $i++) {
                $interestUnivDetails[] = $abroadUniversities[$i];
            }

            $interestedUniversities = $this->clientRepository->createInterestUniversities($newStudentId, $interestUnivDetails);
            return $interestedUniversities;
        }

        return true;
    }

    public function createInterestedMajor(array $interestMajors, int $newStudentId)
    {
        if (isset($interestMajors) && count($interestMajors) > 0) {

            for ($i = 0; $i < count($interestMajors); $i++) {
                $interestMajorDetails[] = $interestMajors[$i];
            }

            return $this->clientRepository->createInterestMajor($newStudentId, $interestMajorDetails);
        }

        return true;
    }
}
