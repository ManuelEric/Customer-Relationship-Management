<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolProgramRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
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
use App\Models\Reason;
use App\Models\SchoolProgram;
use Exception;
use Faker\Calculator\Inn;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class SchoolProgramController extends Controller
{
    use CreateCustomPrimaryKeyTrait;

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
        UniversityRepositoryInterface $universityRepository
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
        $programsB2B = $this->programRepository->getAllProgramByType('B2B');
        $programsB2BB2C = $this->programRepository->getAllProgramByType('B2B/B2C');
        $programs = $programsB2B->merge($programsB2BB2C);

        # retrieve employee data
        $employees = $this->userRepository->getAllUsersByRole('Employee');

        // dd($request->all());

        return view('pages.program.school-program.index')->with(
            [
                'schools' => $schools,
                'programs' => $programs,
                'employees' => $employees,
            ]
        );
    }

    public function store(StoreSchoolProgramRequest $request)
    {

        $schoolId = strtoupper($request->route('school'));


        $schoolPrograms = $request->all();
        if ($request->input('status') == '2') {
            if ($request->input('reason_id') == 'other') {
                $reason['reason_name'] = $request->input('other_reason');
                $reason['type'] = 'Program';
            }

            unset($schoolPrograms['other_reason_refund']);
            unset($schoolPrograms['reason_refund_id']);
        }

        if ($request->input('status') == '3') {
            if ($request->input('reason_refund_id') == 'other'){
                $reason['reason_name'] = $request->input('other_reason_refund');
                $reason['type'] = 'Program';
            } else {
                $schoolPrograms['reason_id'] = $request->input('reason_refund_id');
            }
            unset($schoolPrograms['other_reason']);
        }

        DB::beginTransaction();
        $schoolPrograms['sch_id'] = $schoolId;

        try {
            # insert into reason
            if ($request->input('reason_id') == 'other' || $request->input('reason_refund_id') == 'other') {
                // $this->reasonRepository->createReason($reason);
                $reason_created = $this->reasonRepository->createReason($reason);
                $reason_id = $reason_created->reason_id;
                $schoolPrograms['reason_id'] = $reason_id;
            }

            # insert into school program
            $sch_prog_created = $this->schoolProgramRepository->createSchoolProgram($schoolPrograms);
            $sch_progId = $sch_prog_created->id;

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store school failed : ' . $e->getMessage());
            return Redirect::to('program/school/' . strtolower($schoolId) . '/detail/create')->withError('Failed to create school program' . $e->getMessage());
        }

        return Redirect::to('program/school/' . strtolower($schoolId) . '/detail/' . $sch_progId)->withSuccess('School program successfully created');
    }

    public function create(Request $request)
    {
        $schoolId = strtoupper($request->route('school'));

        # retrieve school data by id
        $school = $this->schoolRepository->getSchoolById($schoolId);

        # retrieve program data
        $programsB2B = $this->programRepository->getAllProgramByType('B2B');
        $programsB2BB2C = $this->programRepository->getAllProgramByType('B2B/B2C');
        $programs = $programsB2B->merge($programsB2BB2C);

        # retrieve reason data
        $reasons = $this->reasonRepository->getReasonByType('Program');
        // $reasons = $this->reasonRepository->getAllReasons();

        # retrieve employee data
        $employees = $this->userRepository->getAllUsersByDepartmentAndRole('Employee', 'Business Development');


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

        $schoolId = strtoupper($request->route('school'));
        $sch_progId = $request->route('detail');

        # retrieve school data by id
        $school = $this->schoolRepository->getSchoolById($schoolId);

        # retrieve all school data
        $schools = $this->schoolRepository->getAllSchools();

        # retrieve all school detail by school id
        $schoolDetail = $this->schoolDetailRepository->getAllSchoolDetailsById($schoolId);

        # retrieve program data
        $programsB2B = $this->programRepository->getAllProgramByType('B2B');
        $programsB2BB2C = $this->programRepository->getAllProgramByType('B2B/B2C');
        $programs = $programsB2B->merge($programsB2BB2C);

        # retrieve reason data
        $reasons = $this->reasonRepository->getReasonByType('Program');
        // $reasons = $this->reasonRepository->getAllReasons();

        # retrieve School Program data by schoolId
        $schoolProgram = $this->schoolProgramRepository->getSchoolProgramById($sch_progId);

        # retrieve School Program Attach data by schoolId
        $schoolProgramAttachs = $this->schoolProgramAttachRepository->getAllSchoolProgramAttachsBySchprogId($sch_progId);

        # retrieve employee data
        $employees = $this->userRepository->getAllUsersByRole('Employee');

        # retrieve corporate / partner
        $partners = $this->corporateRepository->getAllCorporate();

        # retrieve speaker data
        $speakers = $this->agendaSpeakerRepository->getAllSpeakerBySchoolProgram($sch_progId);

        # retrieve university master
        $universities = $this->universityRepository->getAllUniversities();

        # retrieve collaborators
        $collaborators_school = $this->schoolProgramCollaboratorsRepository->getSchoolCollaboratorsBySchoolProgId($sch_progId);
        $collaborators_univ = $this->schoolProgramCollaboratorsRepository->getUnivCollaboratorsBySchoolProgId($sch_progId);
        $colaborators_partner = $this->schoolProgramCollaboratorsRepository->getPartnerCollaboratorsBySchoolProgId($sch_progId);

        return view('pages.program.school-program.form')->with(
            [
                'schId' => $schoolId,
                'sch_ProgId' => $sch_progId,
                'employees' => $employees,
                'programs' => $programs,
                'reasons' => $reasons,
                'schoolProgram' => $schoolProgram,
                'schoolProgramAttachs' => $schoolProgramAttachs,
                'school' => $school,
                'schoolDetail' => $schoolDetail,
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

        $schoolId = strtoupper($request->route('school'));
        $sch_progId = $request->route('detail');

        # retrieve school data by id
        $school = $this->schoolRepository->getSchoolById($schoolId);

        # retrieve all school data
        $schools = $this->schoolRepository->getAllSchools();

        # retrieve all school detail by school id
        $schoolDetail = $this->schoolDetailRepository->getAllSchoolDetailsById($schoolId);

        # retrieve program data
        $programsB2B = $this->programRepository->getAllProgramByType('B2B');
        $programsB2BB2C = $this->programRepository->getAllProgramByType('B2B/B2C');
        $programs = $programsB2B->merge($programsB2BB2C);


        # retrieve reason data
        $reasons = $this->reasonRepository->getReasonByType('Program');
        // $reasons = $this->reasonRepository->getAllReasons();

        # retrieve School Program data by id
        $schoolProgram = $this->schoolProgramRepository->getSchoolProgramById($sch_progId);

        # retrieve employee data
        $employees = $this->userRepository->getAllUsersByDepartmentAndRole('Employee', 'Business Development');

        # retrieve corporate / partner
        $partners = $this->corporateRepository->getAllCorporate();

        return view('pages.program.school-program.form')->with(
            [
                'edit' => true,
                'employees' => $employees,
                'programs' => $programs,
                'reasons' => $reasons,
                'schoolProgram' => $schoolProgram,
                'school' => $school,
                'schools' => $schools,
                'schoolDetail' => $schoolDetail,
                'partners' => $partners,
            ]
        );
    }

    public function update(StoreSchoolProgramRequest $request)
    {

        $schoolId = strtoupper($request->route('school'));
        $sch_progId = $request->route('detail');
        $schoolPrograms = $request->all();
        if ($request->input('status') == '2') {
            if ($request->input('reason_id') == 'other') {
                $reason['reason_name'] = $request->input('other_reason');
                $reason['type'] = 'Program';
            }

            unset($schoolPrograms['other_reason_refund']);
            unset($schoolPrograms['other_reason']);
            unset($schoolPrograms['reason_refund_id']);
            unset($schoolPrograms['reason_refund_id']);
        }

        if ($request->input('status') == '3') {
            if ($request->input('reason_refund_id') == 'other_reason_refund'){
                $reason['reason_name'] = $request->input('other_reason_refund');
                $reason['type'] = 'Program';
            } else {
                $schoolPrograms['reason_id'] = $request->input('reason_refund_id');
            }
            unset($schoolPrograms['other_reason_refund']);
            unset($schoolPrograms['reason_refund_id']);
            unset($schoolPrograms['other_reason']);
            unset($schoolPrograms['reason_refund_id']);
        }

        DB::beginTransaction();
        $schoolPrograms['sch_id'] = $schoolId;
        $schoolPrograms['updated_at'] = Carbon::now();
        try {

            # update reason school program
            if ($schoolPrograms['status'] == 2 || $schoolPrograms['status'] == 3) {
                // return $request->input('reason_refund_id');
                // exit;
                if ($request->input('reason_id') == 'other' || $request->input('reason_refund_id') == 'other_reason_refund') {
                    $reason_created = $this->reasonRepository->createReason($reason);
                    $reason_id = $reason_created->reason_id;
                    $schoolPrograms['reason_id'] = $reason_id;
                }
            }

            # update school program
            $this->schoolProgramRepository->updateSchoolProgram($sch_progId, $schoolPrograms);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update school program failed : ' . $e->getMessage());
            return Redirect::to('program/school/' . strtolower($schoolId) . '/detail/' . $sch_progId . '/edit')->withError('Failed to update school program' . $e->getMessage());
        }

        return Redirect::to('program/school/' . strtolower($schoolId) . '/detail/' . $sch_progId)->withSuccess('School program successfully updated');
    }

    public function destroy(Request $request)
    {
        $schoolId = strtoupper($request->route('school'));
        $sch_progId = $request->route('detail');

        DB::beginTransaction();
        try {

            $this->schoolProgramRepository->deleteSchoolProgram($sch_progId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete school program failed : ' . $e->getMessage());
            return Redirect::to('program/school/' . strtolower($schoolId) . '/detail/' . $sch_progId)->withError('Failed to delete school program');
        }

        return Redirect::to('program/school/')->withSuccess('School program successfully deleted');
    }
}
