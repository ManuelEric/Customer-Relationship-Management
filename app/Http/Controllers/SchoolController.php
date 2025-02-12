<?php

namespace App\Http\Controllers;

use App\Actions\Schools\CreateSchoolAction;
use App\Actions\Schools\DeleteSchoolAction;
use App\Actions\Schools\UpdateSchoolAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreImportExcelRequest;
use App\Http\Requests\StoreSchoolRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Imports\School\UsersImport;
use App\Imports\SchoolImport;
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
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
        $duplicates_schools_string = $this->fnConvertDuplicatesSchoolAsString($duplicates_schools);

        return view('pages.instance.school.index')->with(
            [
                'duplicates_schools_string' => $duplicates_schools_string,
                'duplicates_schools' => $duplicates_schools->pluck('sch_name')->toArray()
            ]
        );
    }

    private function fnConvertDuplicatesSchoolAsString($schools)
    {
        $response = '';
        foreach ($schools as $school) {

            $response .= ', '.$school->sch_name;

        }

        return $response;
    }

    public function store(StoreSchoolRequest $request, CreateSchoolAction $createSchoolAction, LogService $log_service)
    {

        $school_details = $request->safe()->only([
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

        DB::beginTransaction();
        try {

            # insert into school
            $created_school = $createSchoolAction->execute($request, $school_details);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_SCHOOL, $e->getMessage(), $e->getLine(), $e->getFile(), $school_details);

            return Redirect::to('instance/school')->withError('Failed to create school');
        }

        # store Success
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_SCHOOL, 'New school has been added', $created_school->toArray());

        return Redirect::to('instance/school/' . $created_school->sch_id)->withSuccess('School successfully created');
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
        $school_id = $request->route('school');
        $sch_progId = $request->route('detail');

        # retrieve curriculum data
        $curriculums = $this->curriculumRepository->getAllCurriculums();

        # retrieve school data by id
        $school = $this->schoolRepository->getSchoolById($school_id);

        # retrieve program data
        $programs = $this->programRepository->getAllPrograms();

        # retrieve lead data
        $leads = $this->leadRepository->getAllLead();

        # retrieve employee data
        $employees = $this->userRepository->rnGetAllUsersByDepartmentAndRole('Employee', 'Business Development');

        # retrieve school detail data by school Id
        $school_details = $this->schoolDetailRepository->getAllSchoolDetailsById($school_id);

        # retrieve School Program data by school_id
        $schoolPrograms = $this->schoolProgramRepository->getAllSchoolProgramsBySchoolId($school_id);

        # school visit data
        $schoolVisits = $this->schoolVisitRepository->getSchoolVisitBySchoolId($school_id);

        # aliases
        $aliases = $this->schoolRepository->getAliasBySchool($school_id);

        return view('pages.instance.school.form')->with(
            [
                'school' => $school,
                'curriculums' => $curriculums,
                'programs' => $programs,
                'schoolPrograms' => $schoolPrograms,
                'schoolVisits' => $schoolVisits,
                'leads' => $leads,
                'employees' => $employees,
                'details' => $school_details,
                'aliases' => $aliases
            ]
        );
    }

    public function edit(Request $request)
    {
        $school_id = $request->route('school');

        # retrieve curriculum data
        $curriculums = $this->curriculumRepository->getAllCurriculums();

        # retrieve program data
        $programs = $this->programRepository->getAllPrograms();

        # retrieve lead data
        $leads = $this->leadRepository->getAllLead();

        # retrieve employee data
        $employees = $this->userRepository->rnGetAllUsersByRole('Employee');

        # retrieve school data by id
        $school = $this->schoolRepository->getSchoolById($school_id);
        
        # aliases
        $aliases = $this->schoolRepository->getAliasBySchool($school_id);

        return view('pages.instance.school.form')->with(
            [
                'edit' => true,
                'school' => $school,
                'programs' => $programs,
                'leads' => $leads,
                'employees' => $employees,
                'curriculums' => $curriculums,
                'aliases' => $aliases
            ]
        );
    }

    public function update(StoreSchoolRequest $request, UpdateSchoolAction $updateSchoolAction, LogService $log_service)
    {
        $school_details = $request->safe()->only([
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

        $school_id = $request->route('school');
        dd($school_details);

        DB::beginTransaction();
        try {

            # update school
            $updated_school = $updateSchoolAction->execute($request, $school_id, $school_details);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_SCHOOL, $e->getMessage(), $e->getLine(), $e->getFile(), $school_details);
            return Redirect::to('instance/school')->withError('Failed to update school');
        }

        # Update success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_SCHOOL, 'School has been updated', $updated_school->toArray());

        return Redirect::to('instance/school/' . $school_id)->withSuccess('School successfully updated');
    }

    public function destroy(Request $request, DeleteSchoolAction $deleteSchoolAction, LogService $log_service)
    {
        $school_id = $request->route('school');
        $school = $this->schoolRepository->getSchoolById($school_id);

        DB::beginTransaction();
        try {

            if (!isset($school))
                return Redirect::to('instance/school')->withError('Data does not exist');
            
            $deleteSchoolAction->execute($school_id);
            
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_SCHOOL, $e->getMessage(), $e->getLine(), $e->getFile(), $school->toArray());

            return Redirect::to('instance/school')->withError('Failed to delete school');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_SCHOOL, 'School has been deleted', $school->toArray());

        return Redirect::to('instance/school')->withSuccess('School successfully deleted');
    }

    public function updateStatus(Request $request, LogService $log_service)
    {
        $school_id = $request->route('school');
        $new_status = $request->route('status');

        # validate status
        if (!in_array($new_status, [0, 1])) {

            return response()->json(
                [
                    'success' => false,
                    'message' => "Status is invalid"
                ]
            );
        }

        DB::beginTransaction();
        try {

            $this->schoolRepository->updateActiveStatus($school_id, $new_status);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_STATUS_SCHOOL, $e->getMessage(), $e->getLine(), $e->getFile(), ['sch_id' => $school_id, 'new_status' => $new_status]);

            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ]
            );
        }

        # Upload success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_STATUS_SCHOOL, 'Status school has been updated', ['sch_id' => $school_id, 'new_status' => $new_status]);

        return response()->json(
            [
                'success' => true,
                'message' => "Status has been updated",
            ]
        );
    }

    function getSchoolData()
    {
        $schools = $this->schoolRepository->getVerifiedSchools();
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
