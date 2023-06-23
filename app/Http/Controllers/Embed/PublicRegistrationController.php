<?php

namespace App\Http\Controllers\Embed;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePublicRegistrationRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Models\School;
use Illuminate\Http\Request;

class PublicRegistrationController extends Controller
{
    use CreateCustomPrimaryKeyTrait;

    private SchoolRepositoryInterface $schoolRepository;
    private ClientRepositoryInterface $clientRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository, ClientRepositoryInterface $clientRepository)
    {
        $this->schoolRepository = $schoolRepository;
        $this->clientRepository = $clientRepository;
    }
    
    public function register()
    {
        $schools = $this->schoolRepository->getAllSchools();

        return view('form-embed.form-website')->with([
            'schools' => $schools
        ]);
    }

    public function store(StorePublicRegistrationRequest $request)
    {
        $parent_or_children = $request->parent_or_children;

        # checking if client was a parent
        if ($parent_or_children == "parent")
            $newParent = $this->storeParentIfNotExists($request);
        
        # checking if client was a child
        $newChild = $this->storeChildrenIfNotExists($request);
        

        # create relation between parent & student
        if ($newParent && $newChild) 
            $this->clientRepository->createClientRelation($newParent['id'], $newChild['id']);

    }

    private function storeParentIfNotExists(Request $request) 
    {
        $first_name = $request->parent_name;
        $last_name = null; # set null as default because the embedded registration form only shows full names which there's no first_name and last_name

        # to retrieve first_name and last_name
        # check parent_name if there are multiple words
        $explode = explode(" ", $request->parent_name);
        if (count($explode) > 1 ) {
            $first_name = $explode[0];
            $last_name = $explode(max($explode));
        } 

        # initialize parent details
        $parentDetail = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'mail' => $request->parent_email,
            'phone' => $request->parent_phone,
        ];
    
        # check if parent mail & phone exists
        if ($existingParent = $this->checkExistingClient($parentDetail['phone'], $parentDetail['mail'])) 
            return $existingParent['id'];

        # when not exist then store it
        return $existingParent['id'] = $this->clientRepository->createClient('Parent', $parentDetail)->id;
    }

    private function storeChildrenIfNotExists(Request $request)
    {
        $explode = explode(" ", $request->child_name);
        if (count($explode) > 1 ) {
            $first_name = $explode[0];
            $last_name = $explode(max($explode));
        } 

        $grade = ($request->grade) > date('Y') ? $request->grade - date('Y') : 13;

        $studentDetail = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'mail' => $request->child_email,
            'phone' => $request->child_phone,
            'school' => $request->child_school,
            'graduation_year' => $request->grade,
            'st_grade' => $grade,
            'preferred_program' => $request->program,

        ];

        if ($studentDetail['school'] == "new-school") {

            $last_id = School::max('sch_id');
            $school_id_without_label = $last_id ? $this->remove_primarykey_label($last_id, 4) : '0000';
            $school_id_with_label = 'SCH-' . $this->add_digit($school_id_without_label + 1, 4);

            $schoolDetail = [
                'sch_id' => $school_id_with_label,
                'sch_name' => $request->new_school_box
            ];

            # store school
            $school = $this->schoolRepository->createSchool($schoolDetail);
            $studentDetail['school'] = $school->sch_id;
        }

        # check if student mail & phone exists
        if ($existingChild = $this->checkExistingClient($studentDetail['phone'], $studentDetail['mail'])) 
            return $existingChild['id'];

        # when not exist then store it
        return $existingChild['id'] = $this->clientRepository->createClient('Student', $studentDetail)->id;
        
    }
}
