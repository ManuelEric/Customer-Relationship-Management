<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\CurriculumRepositoryInterface;
use App\Interfaces\SchoolDetailRepositoryInterface;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\School;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class SchoolController extends Controller
{
    use CreateCustomPrimaryKeyTrait;

    protected SchoolRepositoryInterface $schoolRepository;
    protected SchoolDetailRepositoryInterface $schoolDetailRepository;
    protected ProgramRepositoryInterface $programRepository;
    protected SchoolProgramRepositoryInterface $schoolProgramRepository;
    protected LeadRepositoryInterface $leadRepository;
    protected UserRepositoryInterface $userRepository;
    protected CurriculumRepositoryInterface $curriculumRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository, CurriculumRepositoryInterface $curriculumRepository, ProgramRepositoryInterface $programRepository, LeadRepositoryInterface $leadRepository, UserRepositoryInterface $userRepository, SchoolDetailRepositoryInterface $schoolDetailRepository, SchoolProgramRepositoryInterface $schoolProgramRepository)
    {
        $this->schoolRepository = $schoolRepository;
        $this->curriculumRepository = $curriculumRepository;
        $this->programRepository = $programRepository;
        $this->leadRepository = $leadRepository;
        $this->userRepository = $userRepository;
        $this->schoolDetailRepository = $schoolDetailRepository;
        $this->schoolProgramRepository = $schoolProgramRepository;
    }

    public function index(Request $request)
    {
        // echo $this->schoolRepository->getAllSchoolDataTables();
        // exit;
        if ($request->ajax()) {
            return $this->schoolRepository->getAllSchoolDataTables();
        }
        return view('pages.instance.school.index');
    }

    public function store(StoreSchoolRequest $request)
    {

        $schoolDetails = $request->only([
            'sch_name',
            'sch_type',
            'sch_curriculum',
            'sch_insta',
            'sch_mail',
            'sch_phone',
            'sch_city',
            'sch_location',
            'sch_score',
        ]);

        $last_id = School::max('sch_id');
        $school_id_without_label = $this->remove_primarykey_label($last_id, 4);
        $school_id_with_label = 'SCH-' . $this->add_digit($school_id_without_label + 1, 4);

        DB::beginTransaction();
        try {

            # insert into school
            $this->schoolRepository->createSchool(['sch_id' => $school_id_with_label] + $schoolDetails);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store school failed : ' . $e->getMessage());
            return Redirect::to('instance/school')->withError('Failed to create school');
        }

        return Redirect::to('instance/school/' . $school_id_with_label)->withSuccess('School successfully created');
    }

    public function create()
    {
        $curriculums = $this->curriculumRepository->getAllCurriculum();
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
        $curriculums = $this->curriculumRepository->getAllCurriculum();

        # retrieve school data by id
        $school = $this->schoolRepository->getSchoolById($schoolId);

        # retrieve program data
        $programs = $this->programRepository->getAllPrograms();
        
        # retrieve lead data
        $leads = $this->leadRepository->getAllLead();

        # retrieve employee data
        $employees = $this->userRepository->getAllUsersByRole('Employee');

        # retrieve school detail data by school Id
        $schoolDetails = $this->schoolDetailRepository->getAllSchoolDetailsById($schoolId);
        
        # retrieve School Program data by schoolId
        $schoolPrograms = $this->schoolProgramRepository->getAllSchoolProgramsBySchoolId($schoolId);

        return view('pages.instance.school.form')->with(
            [
                'school' => $school,
                'curriculums' => $curriculums,
                'programs' => $programs,
                'schoolPrograms' => $schoolPrograms,
                'leads' => $leads,
                'employees' => $employees,
                'details' => $schoolDetails
            ]
        );
    }

    public function edit(Request $request)
    {
        $schoolId = $request->route('school');

        # retrieve curriculum data
        $curriculums = $this->curriculumRepository->getAllCurriculum();

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
            'sch_curriculum',
            'sch_insta',
            'sch_mail',
            'sch_phone',
            'sch_city',
            'sch_location',
            'sch_score',
        ]);

        $schoolId = $request->route('school');

        DB::beginTransaction();
        try {

            # insert into school
            $this->schoolRepository->updateSchool($schoolId, $schoolDetails);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update school failed : ' . $e->getMessage());
            return Redirect::to('instance/school')->withError('Failed to update school');
        }

        return Redirect::to('instance/school')->withSuccess('School successfully updated');
    }

    public function destroy(Request $request)
    {
        $schoolId = $request->route('school');

        DB::beginTransaction();
        try {

            $this->schoolRepository->deleteSchool($schoolId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete school failed : ' . $e->getMessage());
            return Redirect::to('instance/school')->withError('Failed to delete school');
        }

        return Redirect::to('instance/school')->withSuccess('School successfully deleted');
    }
}