<?php

namespace App\Http\Controllers\Module;

use App\Http\Controllers\Controller;
use App\Interfaces\SchoolRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ClientController extends Controller
{
    private SchoolRepositoryInterface $schoolRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository)
    {
        $this->schoolRepository = $schoolRepository;
    }

    public function createSchoolIfAddNew(Request $request)
    {
        # when sch_id is "add-new" 
        if ($request->sch_id == "add-new") {

            $schoolDetails = $request->only([
                'sch_name',
                // 'sch_location',
                'sch_type',
                'sch_score',
            ]);

            $schoolCurriculums = $request->sch_curriculum;

            # create a new school
            $school = $this->schoolRepository->createSchoolIfNotExists($schoolDetails, $schoolCurriculums);            
            return $school->sch_id;
        }
    }

    public function createParentsIfAddNew(Request $request, array $studentDetails)
    {
        # when pr_id is "add-new" 
        if (isset($request->pr_id) && $request->pr_id == "add-new") {

            $parents_phone = $this->setPhoneNumber($request->pr_phone);

            $parentDetails = [
                'first_name' => $request->pr_firstname,
                'last_name' => $request->pr_lastname,
                'mail' => $request->pr_mail,
                'phone' => $parents_phone,
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

            $parent = $this->clientRepository->createClient('Parent', $parentDetails);
            return $parent->id;
        }
    }

    public function createInterestedProgram(Request $request, int $newStudentId)
    {
        if (isset($request->prog_id) && count($request->prog_id) > 0) {

            for ($i = 0; $i < count($request->prog_id); $i++) {
                $interestProgramDetails[] = [
                    'prog_id' => $request->prog_id[$i],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }

            $interestedProgram = $this->clientRepository->createInterestProgram($newStudentId, $interestProgramDetails);
            return $interestedProgram;
        }
    }

    public function createDestinationCountries(Request $request, int $newStudentId)
    {
        if (isset($request->st_abrcountry) && count($request->st_abrcountry) > 0) {

            # hari senin lanjutin utk insert destination countries
            # dan hubungin score nya melalui client view
            for ($i = 0; $i < count($request->st_abrcountry); $i++) {
                $destinationCountryDetails[] = [
                    'tag_id' => $this->tagRepository->getTagById($request->st_abrcountry[$i])->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }

            $destinationCountry = $this->clientRepository->createDestinationCountry($newStudentId, $destinationCountryDetails);
            return $destinationCountry;
        }
    }

    public function createInterestedUniversities(Request $request, int $newStudentId)
    {
        if (isset($request->st_abruniv) && count($request->st_abruniv) > 0) {

            for ($i = 0; $i < count($request->st_abruniv); $i++) {
                $interestUnivDetails[] = [
                    'univ_id' => $request->st_abruniv[$i],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }

            $interestedUniversities = $this->clientRepository->createInterestUniversities($newStudentId, $interestUnivDetails);
            return $interestedUniversities;
                
        }
    }
}
