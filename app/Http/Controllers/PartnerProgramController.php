<?php

namespace App\Http\Controllers;

use App\Actions\PartnerPrograms\CreatePartnerProgramAction;
use App\Actions\PartnerPrograms\DeletePartnerProgramAction;
use App\Actions\PartnerPrograms\UpdatePartnerProgramAction;
use App\Enum\LogModule;
use App\Http\Requests\StorePartnerProgramRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\PartnerProgramRepositoryInterface;
use App\Interfaces\PartnerProgramAttachRepositoryInterface;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\SchoolProgramAttachRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\SchoolDetailRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\ReasonRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\CorporatePicRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\UniversityPicRepositoryInterface;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
use App\Interfaces\PartnerProgramCollaboratorsRepositoryInterface;
use App\Services\Log\LogService;
use App\Services\Master\ProgramService;
use App\Services\Master\ReasonService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class PartnerProgramController extends Controller
{
    use CreateCustomPrimaryKeyTrait;
    use LoggingTrait;

    protected SchoolRepositoryInterface $schoolRepository;
    protected SchoolProgramRepositoryInterface $schoolProgramRepository;
    protected PartnerProgramRepositoryInterface $partnerProgramRepository;
    protected PartnerProgramAttachRepositoryInterface $partnerProgramAttachRepository;
    protected SchoolProgramAttachRepositoryInterface $schoolProgramAttachRepository;
    protected ProgramRepositoryInterface $programRepository;
    protected UserRepositoryInterface $userRepository;
    protected ReasonRepositoryInterface $reasonRepository;
    protected CorporateRepositoryInterface $corporateRepository;
    protected CorporatePicRepositoryInterface $corporatePicRepository;
    protected UniversityRepositoryInterface $universityRepository;
    protected UniversityPicRepositoryInterface $universityPicRepository;
    protected SchoolDetailRepositoryInterface $schoolDetailRepository;
    protected AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;
    protected PartnerProgramCollaboratorsRepositoryInterface $partnerProgramCollaboratorsRepository;
    protected ProgramService $programService;
    protected ReasonService $reasonService;

    public function __construct(
        SchoolRepositoryInterface $schoolRepository,
        UserRepositoryInterface $userRepository,
        SchoolProgramRepositoryInterface $schoolProgramRepository,
        PartnerProgramRepositoryInterface $partnerProgramRepository,
        PartnerProgramAttachRepositoryInterface $partnerProgramAttachRepository,
        SchoolProgramAttachRepositoryInterface $schoolProgramAttachRepository,
        ProgramRepositoryInterface $programRepository,
        ReasonRepositoryInterface $reasonRepository,
        CorporateRepositoryInterface $corporateRepository,
        CorporatePicRepositoryInterface $corporatePicRepository,
        UniversityRepositoryInterface $universityRepository,
        UniversityPicRepositoryInterface $universityPicRepository,
        SchoolDetailRepositoryInterface $schoolDetailRepository,
        AgendaSpeakerRepositoryInterface $agendaSpeakerRepository,
        PartnerProgramCollaboratorsRepositoryInterface $partnerProgramCollaboratorsRepository,
        ProgramService $programService,
        ReasonService $reasonService,
    ) {
        $this->schoolRepository = $schoolRepository;
        $this->schoolProgramRepository = $schoolProgramRepository;
        $this->partnerProgramRepository = $partnerProgramRepository;
        $this->partnerProgramAttachRepository = $partnerProgramAttachRepository;
        $this->schoolProgramAttachRepository = $schoolProgramAttachRepository;
        $this->programRepository = $programRepository;
        $this->userRepository = $userRepository;
        $this->reasonRepository = $reasonRepository;
        $this->corporateRepository = $corporateRepository;
        $this->corporatePicRepository = $corporatePicRepository;
        $this->universityRepository = $universityRepository;
        $this->universityPicRepository = $universityPicRepository;
        $this->schoolDetailRepository = $schoolDetailRepository;
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
        $this->partnerProgramCollaboratorsRepository = $partnerProgramCollaboratorsRepository;
        $this->programService = $programService;
        $this->reasonService = $reasonService;
    }



    public function index(Request $request)
    {

        if ($request->ajax()) {
            $filter = null;

            if ($request->all() != null) {
                $filter = $request->only([
                    'partner_name',
                    'program_name',
                    'status',
                    'pic',
                    'start_date',
                    'end_date',
                ]);
            }
            return $this->partnerProgramRepository->getAllPartnerProgramsDataTables($filter);
        }

        $partners = $this->corporateRepository->getAllCorporate();

        # retrieve program data
        $programs = $this->programService->snGetAllPrograms();

        # retrieve employee data
        $employees = $this->userRepository->getAllUsersByRole('Employee');

        return view('pages.program.corporate-program.index')->with(
            [
                'partners' => $partners,
                'programs' => $programs,
                'employees' => $employees,
            ]
        );
    }

    public function store(StorePartnerProgramRequest $request, CreatePartnerProgramAction $createPartnerProgramAction, LogService $log_service)
    {

        $corp_id = strtoupper($request->route('corp'));

        $partner_program_details = $request->all();
    
        DB::beginTransaction();
        try {

            $new_partner_program = $createPartnerProgramAction->execute($corp_id, $partner_program_details);
            $partner_prog_id = $new_partner_program->id;

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_PARTNER_PROGRAM, $e->getMessage(), $e->getLine(), $e->getFile(), $partner_program_details);

            return Redirect::to('program/corporate/' . strtolower($corp_id) . '/detail/create')->withError('Failed to create partner program' . $e->getMessage());
        }

        # create log success
        $log_service->createSuccessLog(LogModule::STORE_PARTNER_PROGRAM, 'New partner program has been added', $new_partner_program->toArray());

        return Redirect::to('program/corporate/' . strtolower($corp_id) . '/detail/' . $partner_prog_id)->withSuccess('Partner program successfully created');
    }

    public function create(Request $request)
    {
        $corp_id = strtoupper($request->route('corp'));

        $programs = $this->programService->snGetAllPrograms();

        # retrieve partner data
        $partner = $this->corporateRepository->getCorporateById($corp_id);
        $partners = $this->corporateRepository->getAllCorporate();

        # retrieve reason data
        $reasons = $this->reasonRepository->getReasonByType('Program');
        // $reasons = $this->reasonRepository->getAllReasons();

        # retrieve employee data
        # because for now 29/03/2023 there aren't partnership team, so we use client management
        $employees = $this->userRepository->getAllUsersByDepartmentAndRole('Employee', 'Business Development');

        return view('pages.program.corporate-program.form')->with(
            [
                'employees' => $employees,
                'programs' => $programs,
                'reasons' => $reasons,
                'partner' => $partner,
                'partners' => $partners
            ]
        );
    }

    public function show(Request $request)
    {
        $corp_id = strtoupper($request->route('corp'));
        $corp_prog_id = $request->route('detail');

        // # retrieve school data by id
        // $school = $this->schoolRepository->getSchoolById($schoolId);

        # retrieve all school data
        $schools = $this->schoolRepository->getAllSchools();

        # retrieve all univ data
        $universities = $this->universityRepository->getAllUniversities();

        // # retrieve all school detail by school id
        // $schoolDetail = $this->schoolDetailRepository->getAllSchoolDetailsById($schoolId);

        $programs = $this->programService->snGetAllPrograms();

        # retrieve reason data
        $reasons = $this->reasonRepository->getReasonByType('Program');
        // $reasons = $this->reasonRepository->getAllReasons();

        # retrieve partner data
        $partner = $this->corporateRepository->getCorporateById($corp_id);
        $partners = $this->corporateRepository->getAllCorporate();

        # retrieve partner program data
        $partner_program = $this->partnerProgramRepository->getPartnerProgramById($corp_prog_id);

        # retrieve partner Program Attach data by corpProgId
        $partner_program_attachs = $this->partnerProgramAttachRepository->getAllPartnerProgramAttachsByPartnerProgId($corp_prog_id);

        # retrieve employee data
        $employees = $this->userRepository->getAllUsersByRole('Employee');

        # retrieve speaker data
        $speakers = $this->agendaSpeakerRepository->getAllSpeakerByPartnerProgram($corp_prog_id);

        # retrieve collaborators
        $collaborators_school = $this->partnerProgramCollaboratorsRepository->getSchoolCollaboratorsByPartnerProgId($corp_prog_id);
        $collaborators_univ = $this->partnerProgramCollaboratorsRepository->getUnivCollaboratorsByPartnerProgId($corp_prog_id);
        $colaborators_partner = $this->partnerProgramCollaboratorsRepository->getPartnerCollaboratorsByPartnerProgId($corp_prog_id);

        return view('pages.program.corporate-program.form')->with(
            [
                'corpId' => $corp_id,
                'corp_ProgId' => $corp_prog_id,
                'employees' => $employees,
                'programs' => $programs,
                'reasons' => $reasons,
                'partner' => $partner,
                'partnerProgram' => $partner_program,
                'partnerProgramAttachs' => $partner_program_attachs,
                'partners' => $partners,
                'speakers' => $speakers,
                'schools' => $schools,
                'universities' => $universities,
                'attach' => true,
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

        $corp_id = strtoupper($request->route('corp'));
        $partner_prog_id = $request->route('detail');

        # retrieve school data by id
        // $school = $this->schoolRepository->getSchoolById($schoolId);

        # retrieve all school data
        $schools = $this->schoolRepository->getAllSchools();

        # retrieve all school detail by school id
        // $schoolDetail = $this->schoolDetailRepository->getAllSchoolDetailsById($schoolId);

        $programs = $this->programService->snGetAllPrograms();

        # retrieve reason data
        $reasons = $this->reasonRepository->getReasonByType('Program');
        // $reasons = $this->reasonRepository->getAllReasons();

        # retrieve Partner Program data by id
        // $schoolProgram = $this->schoolProgramRepository->getSchoolProgramById($sch_progId);
        $partner_program = $this->partnerProgramRepository->getPartnerProgramById($partner_prog_id);

        # retrieve employee data
        $employees = $this->userRepository->getAllUsersByDepartmentAndRole('Employee', 'Business Development');

        # retrieve corporate / partner
        $partners = $this->corporateRepository->getAllCorporate();
        $partner = $this->corporateRepository->getCorporateById($corp_id);

        return view('pages.program.corporate-program.form')->with(
            [
                'edit' => true,
                'employees' => $employees,
                'programs' => $programs,
                'reasons' => $reasons,
                'schools' => $schools,
                'partners' => $partners,
                'partner' => $partner,
                'partnerProgram' => $partner_program,
            ]
        );
    }

    public function update(StorePartnerProgramRequest $request, UpdatePartnerProgramAction $updatePartnerProgramAction, LogService $log_service)
    {

        $corp_id = strtoupper($request->route('corp'));
        $partner_prog_id = $request->route('detail');

        $partner_program_details = $request->all();

        DB::beginTransaction();
        try {   

            $updated_partner_program = $updatePartnerProgramAction->execute($partner_prog_id, $partner_program_details);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_CLIENT_PROGRAM, $e->getMessage(), $e->getLine(), $e->getFile(), $partner_program_details);
            
            return Redirect::to('program/corporate/' . strtolower($corp_id) . '/detail/' . $partner_prog_id . '/edit')->withError('Failed to update partner program' . $e->getMessage());
        }

        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_PARTNER_PROGRAM, 'Partner program has been updated', $updated_partner_program->toArray());

        return Redirect::to('program/corporate/' . strtolower($corp_id) . '/detail/' . $partner_prog_id)->withSuccess('Partner program successfully updated');
    }

    public function destroy(Request $request, DeletePartnerProgramAction $deletePartnerProgramAction, LogService $log_service)
    {
        $corp_id = strtoupper($request->route('corp'));
        $partner_prog_id = $request->route('detail');
        $partner_prog = $this->partnerProgramRepository->getPartnerProgramById($partner_prog_id);

        DB::beginTransaction();
        try {

            $deletePartnerProgramAction->execute($partner_prog_id);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_PARTNER_PROGRAM, $e->getMessage(), $e->getLine(), $e->getFile(), $partner_prog->toArray());

            return Redirect::to('program/corporate/' . strtolower($corp_id) . '/detail/' . $partner_prog_id)->withError('Failed to delete partner program');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_PARTNER_PROGRAM, 'Partner program has been deleted', $partner_prog->toArray());

        return Redirect::to('program/corporate/')->withSuccess('Partner program successfully deleted');
    }
}
