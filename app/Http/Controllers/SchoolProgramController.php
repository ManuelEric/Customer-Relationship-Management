<?php

namespace App\Http\Controllers;

use App\Actions\SchoolPrograms\CreateSchoolProgramAction;
use App\Actions\SchoolPrograms\DeleteSchoolProgramAction;
use App\Actions\SchoolPrograms\UpdateSchoolProgramAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreSchoolProgramRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\SchoolProgramAttachRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\SchoolDetailRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\ReasonRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\CorporatePicRepositoryInterface;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
use App\Interfaces\SchoolProgramCollaboratorsRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Services\Log\LogService;
use App\Services\Master\ProgramService;
use App\Services\Master\ReasonService;
use App\Services\Program\SchoolProgramService;
use App\Services\Program\SchoolService;
use Exception;
use Faker\Calculator\Inn;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class SchoolProgramController extends Controller
{
    use CreateCustomPrimaryKeyTrait;
    use LoggingTrait;

    protected SchoolRepositoryInterface $schoolRepository;
    protected SchoolProgramRepositoryInterface $schoolProgramRepository;
    protected SchoolProgramAttachRepositoryInterface $schoolProgramAttachRepository;
    protected ProgramRepositoryInterface $programRepository;
    protected UserRepositoryInterface $userRepository;
    protected ReasonRepositoryInterface $reasonRepository;
    protected CorporateRepositoryInterface $corporateRepository;
    protected CorporatePicRepositoryInterface $corporatePicRepository;
    protected AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;
    protected SchoolDetailRepositoryInterface $schoolDetailRepository;
    protected SchoolProgramCollaboratorsRepositoryInterface $schoolProgramCollaboratorsRepository;
    protected UniversityRepositoryInterface $universityRepository;
    protected SchoolProgramService $schoolProgramService;
    protected ProgramService $programService;
    protected ReasonService $reasonService;

    public function __construct(
        SchoolRepositoryInterface $schoolRepository,
        UserRepositoryInterface $userRepository,
        SchoolProgramRepositoryInterface $schoolProgramRepository,
        SchoolProgramAttachRepositoryInterface $schoolProgramAttachRepository,
        ProgramRepositoryInterface $programRepository,
        ReasonRepositoryInterface $reasonRepository,
        CorporateRepositoryInterface $corporateRepository,
        CorporatePicRepositoryInterface $corporatePicRepository,
        AgendaSpeakerRepositoryInterface $agendaSpeakerRepository,
        SchoolDetailRepositoryInterface $schoolDetailRepository,
        SchoolProgramCollaboratorsRepositoryInterface $schoolProgramCollaboratorsRepository,
        UniversityRepositoryInterface $universityRepository,
        SchoolProgramService $schoolProgramService,
        ProgramService $programService,
        ReasonService $reasonService
    ) {
        $this->schoolRepository = $schoolRepository;
        $this->schoolProgramRepository = $schoolProgramRepository;
        $this->schoolProgramAttachRepository = $schoolProgramAttachRepository;
        $this->programRepository = $programRepository;
        $this->userRepository = $userRepository;
        $this->reasonRepository = $reasonRepository;
        $this->corporateRepository = $corporateRepository;
        $this->corporatePicRepository = $corporatePicRepository;
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
        $this->schoolDetailRepository = $schoolDetailRepository;
        $this->schoolProgramCollaboratorsRepository = $schoolProgramCollaboratorsRepository;
        $this->universityRepository = $universityRepository;
        $this->schoolProgramService = $schoolProgramService;
        $this->programService = $programService;
        $this->reasonService = $reasonService;
    }

    public function index(Request $request)
    {


        if ($request->ajax()) {
            $filter = null;

            if ($request->all() != null) {
                $filter = $request->only([
                    'school_name',
                    'program_name',
                    'status',
                    'pic',
                    'start_date',
                    'end_date',
                ]);
            }

            return $this->schoolProgramRepository->getAllSchoolProgramsDataTables($filter);
        }

        # retrieve all school data
        $schools = $this->schoolRepository->getAllSchools();

        # retrieve program data
        $programs = $this->programService->snGetAllPrograms();

        # retrieve employee data
        $employees = $this->userRepository->rnGetAllUsersByRole('Employee');


        return view('pages.program.school-program.index')->with(
            [
                'schools' => $schools,
                'programs' => $programs,
                'employees' => $employees,
            ]
        );
    }

    public function store(StoreSchoolProgramRequest $request, CreateSchoolProgramAction $createSchoolProgramAction, LogService $log_service)
    {

        $school_id = strtoupper($request->route('school'));

        $school_program_details = $request->all();
    
        DB::beginTransaction();
        
        try {
            $school_program_created = $createSchoolProgramAction->execute($school_id, $school_program_details);
            $sch_prog_id = $school_program_created->id;

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_SCHOOL_PROGRAM, $e->getMessage(), $e->getLine(), $e->getFile(), $school_program_details);
            return Redirect::to('program/school/' . strtolower($school_id) . '/detail/create')->withError('Failed to create school program' . $e->getMessage());
        }

        # store Success
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_SCHOOL_PROGRAM, 'New school program has been added', $school_program_created->toArray());

        return Redirect::to('program/school/' . strtolower($school_id) . '/detail/' . $sch_prog_id)->withSuccess('School program successfully created');
    }

    public function create(Request $request)
    {
        $school_id = strtoupper($request->route('school'));

        # retrieve school data by id
        $school = $this->schoolRepository->getSchoolById($school_id);

        # retrieve program data
        $programs = $this->programService->snGetAllPrograms();

        # retrieve reason data
        $reasons = $this->reasonRepository->getReasonByType('Program');
        // $reasons = $this->reasonRepository->getAllReasons();

        # retrieve employee data
        $employees = $this->userRepository->rnGetAllUsersByDepartmentAndRole('Employee', 'Business Development');


        return view('pages.program.school-program.form')->with(
            [
                'employees' => $employees,
                'programs' => $programs,
                'reasons' => $reasons,
                'school' => $school
            ]
        );
    }

    public function show(Request $request)
    {

        $school_id = strtoupper($request->route('school'));
        $sch_prog_id = $request->route('detail');

        # retrieve school data by id
        $school = $this->schoolRepository->getSchoolById($school_id);

        # retrieve all school data
        $schools = $this->schoolRepository->getAllSchools();

        # retrieve all school detail by school id
        $school_detail = $this->schoolDetailRepository->getAllSchoolDetailsById($school_id);

        # retrieve program data
        $programs = $this->programService->snGetAllPrograms();

        # retrieve reason data
        $reasons = $this->reasonRepository->getReasonByType('Program');
        // $reasons = $this->reasonRepository->getAllReasons();

        # retrieve School Program data by schoo_id
        $school_program = $this->schoolProgramRepository->getSchoolProgramById($sch_prog_id);

        # retrieve School Program Attach data by schoo_id
        $school_program_attachs = $this->schoolProgramAttachRepository->getAllSchoolProgramAttachsBySchprogId($sch_prog_id);

        # retrieve employee data
        $employees = $this->userRepository->rnGetAllUsersByRole('Employee');

        # retrieve corporate / partner
        $partners = $this->corporateRepository->getAllCorporate();

        # retrieve speaker data
        $speakers = $this->agendaSpeakerRepository->getAllSpeakerBySchoolProgram($sch_prog_id);

        # retrieve university master
        $universities = $this->universityRepository->getAllUniversities();

        # retrieve collaborators
        $collaborators_school = $this->schoolProgramCollaboratorsRepository->getSchoolCollaboratorsBySchoolProgId($sch_prog_id);
        $collaborators_univ = $this->schoolProgramCollaboratorsRepository->getUnivCollaboratorsBySchoolProgId($sch_prog_id);
        $colaborators_partner = $this->schoolProgramCollaboratorsRepository->getPartnerCollaboratorsBySchoolProgId($sch_prog_id);

        return view('pages.program.school-program.form')->with(
            [
                'schId' => $school_id,
                'sch_ProgId' => $sch_prog_id,
                'employees' => $employees,
                'programs' => $programs,
                'reasons' => $reasons,
                'schoolProgram' => $school_program,
                'schoolProgramAttachs' => $school_program_attachs,
                'school' => $school,
                'schoolDetail' => $school_detail,
                'schools' => $schools,
                'partners' => $partners,
                'speakers' => $speakers,
                'attach' => true,
                'universities' => $universities,
                'collaborators_school' => $collaborators_school,
                'collaborators_univ' => $collaborators_univ,
                'colaborators_partner' => $colaborators_partner
            ]
        );
    }


    public function edit(Request $request)
    {

        if ($request->ajax()) {
            $id = $request->get('id');
            $type = $request->get('type');

            switch ($type) {

                case "partner":
                    return $this->corporatePicRepository->getAllCorporatePicByCorporateId($id);
                    break;

                case "school":
                    return $this->schoolDetailRepository->getAllSchoolDetailsById($id);
                    break;
            }
        }

        $school_id = strtoupper($request->route('school'));
        $sch_prog_id = $request->route('detail');

        # retrieve school data by id
        $school = $this->schoolRepository->getSchoolById($school_id);

        # retrieve all school data
        $schools = $this->schoolRepository->getAllSchools();

        # retrieve all school detail by school id
        $school_detail = $this->schoolDetailRepository->getAllSchoolDetailsById($school_id);

        # retrieve program data
        $programs = $this->programService->snGetAllPrograms();

        # retrieve reason data
        $reasons = $this->reasonRepository->getReasonByType('Program');
        // $reasons = $this->reasonRepository->getAllReasons();

        # retrieve School Program data by id
        $school_program = $this->schoolProgramRepository->getSchoolProgramById($sch_prog_id);

        # retrieve employee data
        $employees = $this->userRepository->rnGetAllUsersByDepartmentAndRole('Employee', 'Business Development');

        # retrieve corporate / partner
        $partners = $this->corporateRepository->getAllCorporate();

        return view('pages.program.school-program.form')->with(
            [
                'edit' => true,
                'employees' => $employees,
                'programs' => $programs,
                'reasons' => $reasons,
                'schoolProgram' => $school_program,
                'school' => $school,
                'schools' => $schools,
                'schoolDetail' => $school_detail,
                'partners' => $partners,
            ]
        );
    }

    public function update(StoreSchoolProgramRequest $request, UpdateSchoolProgramAction $updateSchoolProgramAction, LogService $log_service)
    {

        $school_id = strtoupper($request->route('school'));
        $sch_prog_id = $request->route('detail');

        $school_program_details = $request->all();

        DB::beginTransaction();
        try {
            
            $updated_school_program = $updateSchoolProgramAction->execute($school_id, $sch_prog_id, $school_program_details);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_SCHOOL_PROGRAM, $e->getMessage(), $e->getLine(), $e->getFile(), $school_program_details);

            return Redirect::to('program/school/' . strtolower($school_id) . '/detail/' . $sch_prog_id . '/edit')->withError('Failed to update school program' . $e->getMessage());
        }

        # Update success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_SCHOOL_PROGRAM, 'School program has been updated', $updated_school_program->toArray());

        return Redirect::to('program/school/' . strtolower($school_id) . '/detail/' . $sch_prog_id)->withSuccess('School program successfully updated');
    }

    public function destroy(Request $request, DeleteSchoolProgramAction $deleteSchoolProgramAction, LogService $log_service)
    {
        $school_id = strtoupper($request->route('school'));
        $sch_prog_id = $request->route('detail');
        $school_prog = $this->schoolProgramRepository->getSchoolProgramById($sch_prog_id);

        DB::beginTransaction();
        try {

            $deleteSchoolProgramAction->execute($sch_prog_id);
        
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_SCHOOL_PROGRAM, $e->getMessage(), $e->getLine(), $e->getFile(), $school_prog->toArray());
            Log::error('Delete school program failed : ' . $e->getMessage());
            return Redirect::to('program/school/' . strtolower($school_id) . '/detail/' . $sch_prog_id)->withError('Failed to delete school program');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_SCHOOL_PROGRAM, 'School program has been deleted', $school_prog->toArray());

        return Redirect::to('program/school/')->withSuccess('School program successfully deleted');
    }
}
