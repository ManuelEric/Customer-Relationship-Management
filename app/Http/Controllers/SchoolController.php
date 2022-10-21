<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\CurriculumRepositoryInterface;
use App\Interfaces\SchoolDetailRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
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
    protected CurriculumRepositoryInterface $curriculumRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository, CurriculumRepositoryInterface $curriculumRepository, SchoolDetailRepositoryInterface $schoolDetailRepository)
    {
        $this->schoolRepository = $schoolRepository;
        $this->curriculumRepository = $curriculumRepository;
        $this->schoolDetailRepository = $schoolDetailRepository;
    }

    public function index(): JsonResponse
    {
        return response()->json($this->schoolRepository->getAllSchools());
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
        }

        return Redirect::to('master/school')->withSuccess('School successfully created');
    }

    public function create()
    {
        $curriculums = $this->curriculumRepository->getAllCurriculum();
        return view('pages.school.form')->with(
            [
                'curriculums' => $curriculums
            ]
        );
    }

    public function show(Request $request)
    {
        $schoolId = $request->route('school');

        # retrieve curriculum data
        $curriculums = $this->curriculumRepository->getAllCurriculum();

        # retrieve school data by id
        $school = $this->schoolRepository->getSchoolById($schoolId);

        # retrieve school detail data by school Id
        $schoolDetails = $this->schoolDetailRepository->getAllSchoolDetailsById($schoolId);

        return view('pages.school.form')->with(
            [
                'school' => $school,
                'curriculums' => $curriculums,
                'details' => $schoolDetails
            ]
        );
    }

    public function edit(Request $request)
    {
        $schoolId = $request->route('school');

        # retrieve curriculum data
        $curriculums = $this->curriculumRepository->getAllCurriculum();

        # retrieve school data by id
        $school = $this->schoolRepository->getSchoolById($schoolId);

        return view('pages.school.form')->with(
            [
                'school' => $school,
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
        }

        return Redirect::to('master/school')->withSuccess('School successfully updated');
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
        }

        return Redirect::to('master/school')->withSuccess('School successfully deleted');
    }
}
