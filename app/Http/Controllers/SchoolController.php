<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\CurriculumRepositoryInterface;
use App\Interfaces\SchoolDetailRepositoryInterface;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Interfaces\SchoolCurriculumRepositoryInterface;
use App\Interfaces\SchoolVisitRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\School;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class SchoolController extends Controller
{
    use CreateCustomPrimaryKeyTrait;
    use LoggingTrait;

    protected SchoolRepositoryInterface $schoolRepository;
    protected SchoolDetailRepositoryInterface $schoolDetailRepository;
    protected ProgramRepositoryInterface $programRepository;
    protected SchoolProgramRepositoryInterface $schoolProgramRepository;
    protected LeadRepositoryInterface $leadRepository;
    protected UserRepositoryInterface $userRepository;
    protected CurriculumRepositoryInterface $curriculumRepository;
    protected SchoolCurriculumRepositoryInterface $schoolCurriculumRepository;
    protected SchoolVisitRepositoryInterface $schoolVisitRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository, CurriculumRepositoryInterface $curriculumRepository, ProgramRepositoryInterface $programRepository, LeadRepositoryInterface $leadRepository, UserRepositoryInterface $userRepository, SchoolDetailRepositoryInterface $schoolDetailRepository, SchoolProgramRepositoryInterface $schoolProgramRepository, SchoolCurriculumRepositoryInterface $schoolCurriculumRepository, SchoolVisitRepositoryInterface $schoolVisitRepository)
    {
        $this->schoolRepository = $schoolRepository;
        $this->curriculumRepository = $curriculumRepository;
        $this->programRepository = $programRepository;
        $this->leadRepository = $leadRepository;
        $this->userRepository = $userRepository;
        $this->schoolDetailRepository = $schoolDetailRepository;
        $this->schoolProgramRepository = $schoolProgramRepository;
        $this->schoolCurriculumRepository = $schoolCurriculumRepository;
        $this->schoolVisitRepository = $schoolVisitRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax())
            return $this->schoolRepository->getAllSchoolDataTables();
        
        
        $duplicates_schools = $this->schoolRepository->getDuplicateSchools();
        $duplicates_schools_string = $this->convertDuplicatesSchoolAsString($duplicates_schools);

        return view('pages.instance.school.index')->with(
            [
                'duplicates_schools_string' => $duplicates_schools_string,
                'duplicates_schools' => $duplicates_schools->pluck('sch_name')->toArray()
            ]
        );
    }

    private function convertDuplicatesSchoolAsString($schools)
    {
        $response = '';
        foreach ($schools as $school) {

            $response .= ', '.$school->sch_name;

        }

        return $response;
    }

    public function store(StoreSchoolRequest $request)
    {

        $schoolDetails = $request->only([
            'sch_name',
            'sch_type',
            'sch_insta',
            'sch_mail',
            'sch_phone',
            'sch_city',
            'sch_location',
            'sch_score',
            'status'
        ]);

        $last_id = School::max('sch_id');
        $school_id_without_label = $last_id ? $this->remove_primarykey_label($last_id, 4) : '0000';
        $school_id_with_label = 'SCH-' . $this->add_digit($school_id_without_label + 1, 4);
        

        DB::beginTransaction();
        try {

            # insert into school
            $this->schoolRepository->createSchool(['sch_id' => $school_id_with_label] + $schoolDetails);

            # insert into sch curriculum
            $schoolCurriculumDetails = $request->sch_curriculum;

            $schoolCreated = $this->schoolCurriculumRepository->createSchoolCurriculum($school_id_with_label, $schoolCurriculumDetails);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store school failed : ' . $e->getMessage());
            return Redirect::to('instance/school')->withError('Failed to create school');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'School', Auth::user()->first_name . ' ' . Auth::user()->last_name, $schoolCreated);

        return Redirect::to('instance/school/' . $school_id_with_label)->withSuccess('School successfully created');
    }

    public function create()
    {
        $curriculums = $this->curriculumRepository->getAllCurriculums();

        return view('pages.instance.school.form')->with(
            [
                'curriculums' => $curriculums
            ]
        );
    }

    public function show(Request $request)
    {
        $schoolId = $request->route('school');
        $sch_progId = $request->route('detail');

        # retrieve curriculum data
        $curriculums = $this->curriculumRepository->getAllCurriculums();

        # retrieve school data by id
        $school = $this->schoolRepository->getSchoolById($schoolId);

        # retrieve program data
        $programs = $this->programRepository->getAllPrograms();

        # retrieve lead data
        $leads = $this->leadRepository->getAllLead();

        # retrieve employee data
        $employees = $this->userRepository->getAllUsersByDepartmentAndRole('Employee', 'Business Development');

        # retrieve school detail data by school Id
        $schoolDetails = $this->schoolDetailRepository->getAllSchoolDetailsById($schoolId);

        # retrieve School Program data by schoolId
        $schoolPrograms = $this->schoolProgramRepository->getAllSchoolProgramsBySchoolId($schoolId);

        # school visit data
        $schoolVisits = $this->schoolVisitRepository->getSchoolVisitBySchoolId($schoolId);

        # aliases
        $aliases = $this->schoolRepository->getAliasBySchool($schoolId);

        return view('pages.instance.school.form')->with(
            [
                'school' => $school,
                'curriculums' => $curriculums,
                'programs' => $programs,
                'schoolPrograms' => $schoolPrograms,
                'schoolVisits' => $schoolVisits,
                'leads' => $leads,
                'employees' => $employees,
                'details' => $schoolDetails,
                'aliases' => $aliases
            ]
        );
    }

    public function edit(Request $request)
    {
        $schoolId = $request->route('school');

        # retrieve curriculum data
        $curriculums = $this->curriculumRepository->getAllCurriculums();

        # retrieve program data
        $programs = $this->programRepository->getAllPrograms();

        # retrieve lead data
        $leads = $this->leadRepository->getAllLead();

        # retrieve employee data
        $employees = $this->userRepository->getAllUsersByRole('Employee');

        # retrieve school data by id
        $school = $this->schoolRepository->getSchoolById($schoolId);

        return view('pages.instance.school.form')->with(
            [
                'edit' => true,
                'school' => $school,
                'programs' => $programs,
                'leads' => $leads,
                'employees' => $employees,
                'curriculums' => $curriculums
            ]
        );
    }

    public function update(StoreSchoolRequest $request)
    {
        $schoolDetails = $request->only([
            'sch_name',
            'sch_type',
            'sch_insta',
            'sch_mail',
            'sch_phone',
            'sch_city',
            'sch_location',
            'sch_score',
            'status'
        ]);

        $schoolId = $request->route('school');
        $oldSchool = $this->schoolRepository->getSchoolById($schoolId);

        DB::beginTransaction();
        try {

            # insert into school
            $this->schoolRepository->updateSchool($schoolId, $schoolDetails);

            # insert into sch curriculum
            $newSchoolCurriculumDetails = $request->sch_curriculum;

            $this->schoolCurriculumRepository->updateSchoolCurriculum($schoolId, $newSchoolCurriculumDetails);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update school failed : ' . $e->getMessage());
            return Redirect::to('instance/school')->withError('Failed to update school');
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'School', Auth::user()->first_name . ' ' . Auth::user()->last_name, $schoolDetails, $oldSchool);

        return Redirect::to('instance/school/' . $schoolId)->withSuccess('School successfully updated');
    }

    public function destroy(Request $request)
    {
        $schoolId = $request->route('school');
        $school = $this->schoolRepository->getSchoolById($schoolId);

        DB::beginTransaction();
        try {

            $this->schoolRepository->deleteSchool($schoolId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete school failed : ' . $e->getMessage());
            return Redirect::to('instance/school')->withError('Failed to delete school');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'School', Auth::user()->first_name . ' ' . Auth::user()->last_name, $school);

        return Redirect::to('instance/school')->withSuccess('School successfully deleted');
    }

    function getSchoolData()
    {
        $schools = $this->schoolRepository->getAllSchools();
        return response()->json(
            [
                'success' => true,
                'data' => $schools
            ]
        );
    }

    ################################
    ############## RAW #############
    ################################
    
    public function raw_index()
    {

    }

    ################################
    ########### RAW END ############
    ################################

}
