<?php

namespace App\Http\Controllers\Embed;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePublicRegistrationRequest;
use App\Http\Traits\CheckExistingClient;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Jobs\Client\ProcessDefineCategory;
use App\Models\School;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class PublicRegistrationController extends Controller
{
    use CreateCustomPrimaryKeyTrait;
    use CheckExistingClient;
    use LoggingTrait;

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

        return view('form-embed.form-general')->with([
            'schools' => $schools
        ]);
    }

    public function store(StorePublicRegistrationRequest $request)
    {
        // return $request->all();
        $role = $request->role;

        if (count(array_filter($request->fullname)) > 1) {

            # there are parent and children 
            $parentDetail = [
                'fullname' => $request->fullname[0],
                'mail' => $request->email[0],
                'phone' => $request->fullnumber[0]
            ];

            $childrenDetail = [
                'fullname' => $request->fullname[1],
                'mail' => null,
                'phone' => null,
                'school' => $request->school,
                'grade' => $request->grade,
                'program' => $request->program,
                'register_as' => 'parent',
            ];
        } else {

            $childrenDetail = [
                'fullname' => $request->fullname[0],
                'mail' => $request->email[0],
                'phone' => $request->fullnumber[0],
                'school' => $request->school,
                'grade' => $request->grade,
                'program' => $request->program,
                'register_as' => 'student',
            ];
        }

        DB::beginTransaction();
        try {
            $newParent = false;
            # checking if client was a parent
            if ($role == "parent")
                $newParent = $this->storeParentIfNotExists($parentDetail);

            # checking if client was a child
            $newChild = $this->storeChildrenIfNotExists($childrenDetail);

            # trigger define category client
            ProcessDefineCategory::dispatch([$newChild])->onQueue('define-category-client');

            # create relation between parent & student
            if ($newParent && $newChild)
                $this->clientRepository->createClientRelation($newParent, $newChild);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Register from embed form website failed : ' . $e->getMessage() . $e->getLine());
            return 'Error when processing, please try again or contact our team.';
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Embed', 'Registration', 'Guest', $childrenDetail);

        return Redirect::to('form/thanks');
    }

    private function storeParentIfNotExists($detail)
    {
        $first_name = $detail['fullname'];
        $last_name = null; # set null as default because the embedded registration form only shows full names which there's no first_name and last_name

        # to retrieve first_name and last_name
        # check parent_name if there are multiple words
        // $explode = explode(" ", $detail['fullname']);
        // if (count($explode) > 1) {
        //     $first_name = $explode[0];
        //     $last_name = $explode[array_keys($explode, max($explode))[0]];
        // }

        $explode = explode(" ", $detail['fullname']);
        $limit = count($explode);
        if ($limit > 1) {
            $last_name = $explode[$limit - 1];
            unset($explode[$limit - 1]);
            $first_name = implode(" ", $explode);
        } else {
            $first_name = implode(" ", $explode);
        }


        # initialize parent details
        $parentDetail = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'mail' => $detail['mail'],
            'phone' => $detail['phone'],
        ];

        # check if parent mail & phone exists
        if ($existingParent = $this->checkExistingClient($parentDetail['phone'], $parentDetail['mail'])) {
            if (isset($existingChild['id']))
                return $existingChild['id'];
        }

        # when not exist then store it
        return $existingParent['id'] = $this->clientRepository->createClient('Parent', $parentDetail)->id;
    }

    private function storeChildrenIfNotExists($detail)
    {
        $first_name = $detail['fullname'];
        $last_name = null; # set null as default because the embedded registration form only shows full names which there's no first_name and last_name

        // $explode = explode(" ", $detail['fullname']);
        // if (count($explode) > 1) {
        //     $first_name = $explode[0];
        //     $last_name = $explode[array_keys($explode, max($explode))[0]];
        // }

        $explode = explode(" ", $detail['fullname']);
        $limit = count($explode);
        if ($limit > 1) {
            $last_name = $explode[$limit - 1];
            unset($explode[$limit - 1]);
            $first_name = implode(" ", $explode);
        } else {
            $first_name = implode(" ", $explode);
        }

        $max_grade = 12;
        $grade = ($detail['grade']) > date('Y') ? $max_grade - ($detail['grade'] - date('Y')) : 13;

        $studentDetail = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'mail' => $detail['mail'],
            'phone' => $detail['phone'],
            'sch_id' => $detail['school'],
            'graduation_year' => $detail['grade'],
            'register_as' => $detail['register_as'],
            'st_grade' => $grade,
            'preferred_program' => $detail['program'],

        ];

        if (!$this->schoolRepository->getSchoolById($studentDetail['sch_id'])) {

            $last_id = School::max('sch_id');
            $school_id_without_label = $last_id ? $this->remove_primarykey_label($last_id, 4) : '0000';
            $school_id_with_label = 'SCH-' . $this->add_digit($school_id_without_label + 1, 4);

            $schoolDetail = [
                'sch_id' => $school_id_with_label,
                'sch_name' => $detail['school']
            ];

            # store school
            $school = $this->schoolRepository->createSchool($schoolDetail);
            $studentDetail['sch_id'] = $school->sch_id;
        }

        # check if student mail & phone exists
        if ($existingChild = $this->checkExistingClient($studentDetail['phone'], $studentDetail['mail'])) {
            if (isset($existingChild['id']))
                return $existingChild['id'];
        }

        # when not exist then store it
        return $existingChild['id'] = $this->clientRepository->createClient('Student', $studentDetail)->id;
    }
}
