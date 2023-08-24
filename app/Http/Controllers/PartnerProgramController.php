<?php

namespace App\Http\Controllers;


use App\Http\Requests\StorePartnerProgramRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
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
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class PartnerProgramController extends Controller
{
    use CreateCustomPrimaryKeyTrait;

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
        $programsB2B = $this->programRepository->getAllProgramByType('B2B');
        $programsB2BB2C = $this->programRepository->getAllProgramByType('B2B/B2C');
        $programs = $programsB2B->merge($programsB2BB2C);

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

    public function store(StorePartnerProgramRequest $request)
    {

        $corpId = strtoupper($request->route('corp'));

        $partnerPrograms = $request->all();
        if ($request->input('status') == '2') {
            if ($request->input('reason_id') == 'other') {
                $reason['reason_name'] = $request->input('other_reason');
                $reason['type'] = 'Program';
            }

            unset($partnerPrograms['other_reason_refund']);
            unset($partnerPrograms['reason_refund_id']);
        }

        if ($request->input('status') == '3') {
            if ($request->input('reason_refund_id') == 'other'){
                $reason['reason_name'] = $request->input('other_reason_refund');
                $reason['type'] = 'Program';
            } else {
                $partnerPrograms['reason_id'] = $request->input('reason_refund_id');
            }
            unset($partnerPrograms['other_reason']);
        }

        DB::beginTransaction();
        $partnerPrograms['corp_id'] = $corpId;

        try {
            # insert into reason
            if ($request->input('reason_id') == 'other' || $request->input('reason_refund_id') == 'other') {
                $reason_created = $this->reasonRepository->createReason($reason);
                $reason_id = $reason_created->reason_id;
                $partnerPrograms['reason_id'] = $reason_id;
            }


            # insert into partner program
            $partner_prog_created = $this->partnerProgramRepository->createPartnerProgram($partnerPrograms);
            $partner_progId = $partner_prog_created->id;

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store partner program failed : ' . $e->getMessage());
            return Redirect::to('program/corporate/' . strtolower($corpId) . '/detail/create')->withError('Failed to create partner program' . $e->getMessage());
        }

        return Redirect::to('program/corporate/' . strtolower($corpId) . '/detail/' . $partner_progId)->withSuccess('Partner program successfully created');
    }

    public function create(Request $request)
    {
        $corp_id = strtoupper($request->route('corp'));

        # retrieve program data
        $programsB2B = $this->programRepository->getAllProgramByType('B2B');
        $programsB2BB2C = $this->programRepository->getAllProgramByType('B2B/B2C');
        $programs = $programsB2B->merge($programsB2BB2C);

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
        $corpId = strtoupper($request->route('corp'));
        $corp_ProgId = $request->route('detail');

        // # retrieve school data by id
        // $school = $this->schoolRepository->getSchoolById($schoolId);

        # retrieve all school data
        $schools = $this->schoolRepository->getAllSchools();

        # retrieve all univ data
        $universities = $this->universityRepository->getAllUniversities();

        // # retrieve all school detail by school id
        // $schoolDetail = $this->schoolDetailRepository->getAllSchoolDetailsById($schoolId);

        # retrieve program data
        $programsB2B = $this->programRepository->getAllProgramByType('B2B');
        $programsB2BB2C = $this->programRepository->getAllProgramByType('B2B/B2C');
        $programs = $programsB2B->merge($programsB2BB2C);

        # retrieve reason data
        $reasons = $this->reasonRepository->getReasonByType('Program');
        // $reasons = $this->reasonRepository->getAllReasons();

        # retrieve partner data
        $partner = $this->corporateRepository->getCorporateById($corpId);
        $partners = $this->corporateRepository->getAllCorporate();

        # retrieve partner program data
        $partnerProgram = $this->partnerProgramRepository->getPartnerProgramById($corp_ProgId);

        # retrieve partner Program Attach data by corpProgId
        $partnerProgramAttachs = $this->partnerProgramAttachRepository->getAllPartnerProgramAttachsByPartnerProgId($corp_ProgId);

        # retrieve employee data
        $employees = $this->userRepository->getAllUsersByRole('Employee');

        # retrieve speaker data
        $speakers = $this->agendaSpeakerRepository->getAllSpeakerByPartnerProgram($corp_ProgId);

        # retrieve collaborators
        $collaborators_school = $this->partnerProgramCollaboratorsRepository->getSchoolCollaboratorsByPartnerProgId($corp_ProgId);
        $collaborators_univ = $this->partnerProgramCollaboratorsRepository->getUnivCollaboratorsByPartnerProgId($corp_ProgId);
        $colaborators_partner = $this->partnerProgramCollaboratorsRepository->getPartnerCollaboratorsByPartnerProgId($corp_ProgId);

        return view('pages.program.corporate-program.form')->with(
            [
                'corpId' => $corpId,
                'corp_ProgId' => $corp_ProgId,
                'employees' => $employees,
                'programs' => $programs,
                'reasons' => $reasons,
                'partner' => $partner,
                'partnerProgram' => $partnerProgram,
                'partnerProgramAttachs' => $partnerProgramAttachs,
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

        $corpId = strtoupper($request->route('corp'));
        $partner_progId = $request->route('detail');

        # retrieve school data by id
        // $school = $this->schoolRepository->getSchoolById($schoolId);

        # retrieve all school data
        $schools = $this->schoolRepository->getAllSchools();

        # retrieve all school detail by school id
        // $schoolDetail = $this->schoolDetailRepository->getAllSchoolDetailsById($schoolId);

        # retrieve program data
        $programsB2B = $this->programRepository->getAllProgramByType('B2B');
        $programsB2BB2C = $this->programRepository->getAllProgramByType('B2B/B2C');
        $programs = $programsB2B->merge($programsB2BB2C);


        # retrieve reason data
        $reasons = $this->reasonRepository->getReasonByType('Program');
        // $reasons = $this->reasonRepository->getAllReasons();

        # retrieve Partner Program data by id
        // $schoolProgram = $this->schoolProgramRepository->getSchoolProgramById($sch_progId);
        $partnerProgram = $this->partnerProgramRepository->getPartnerProgramById($partner_progId);

        # retrieve employee data
        $employees = $this->userRepository->getAllUsersByDepartmentAndRole('Employee', 'Business Development');

        # retrieve corporate / partner
        $partners = $this->corporateRepository->getAllCorporate();
        $partner = $this->corporateRepository->getCorporateById($corpId);

        return view('pages.program.corporate-program.form')->with(
            [
                'edit' => true,
                'employees' => $employees,
                'programs' => $programs,
                'reasons' => $reasons,
                'schools' => $schools,
                'partners' => $partners,
                'partner' => $partner,
                'partnerProgram' => $partnerProgram,
            ]
        );
    }

    public function update(StorePartnerProgramRequest $request)
    {

        $corpId = strtoupper($request->route('corp'));
        $partner_progId = $request->route('detail');
        $partnerPrograms = $request->all();
        if ($request->input('status') == '2') {
            if ($request->input('reason_id') == 'other') {
                $reason['reason_name'] = $request->input('other_reason');
                $reason['type'] = 'Program';
            }

            unset($partnerPrograms['other_reason_refund']);
            unset($partnerPrograms['other_reason']);
            unset($partnerPrograms['reason_refund_id']);
            unset($partnerPrograms['reason_refund_id']);
        }

        if ($request->input('status') == '3') {
            if ($request->input('reason_refund_id') == 'other_reason_refund')
            {
                $reason['reason_name'] = $request->input('other_reason_refund');
                $reason['type'] = 'Program';
            }else {
                $partnerPrograms['reason_id'] = $request->input('reason_refund_id');
            }
            unset($partnerPrograms['other_reason_refund']);
            unset($partnerPrograms['reason_refund_id']);
            unset($partnerPrograms['other_reason']);
            unset($partnerPrograms['reason_refund_id']);
        }


        DB::beginTransaction();
        $partnerPrograms['corp_id'] = $corpId;
        $partnerPrograms['updated_at'] = Carbon::now();
        try {

            # update reason school program
            if ($partnerPrograms['status'] == 2 || $partnerPrograms['status'] == 3) {
                // return $request->input('reason_refund_id');
                // exit;
                if ($request->input('reason_id') == 'other' || $request->input('reason_refund_id') == 'other_reason_refund') {
                    $reason_created = $this->reasonRepository->createReason($reason);
                    $reason_id = $reason_created->reason_id;
                    $partnerPrograms['reason_id'] = $reason_id;
                }
            }

            # update school program
            $this->partnerProgramRepository->updatePartnerProgram($partner_progId, $partnerPrograms);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update partner program failed : ' . $e->getMessage());
            return Redirect::to('program/corporate/' . strtolower($corpId) . '/detail/' . $partner_progId . '/edit')->withError('Failed to update partner program' . $e->getMessage());
        }

        return Redirect::to('program/corporate/' . strtolower($corpId) . '/detail/' . $partner_progId)->withSuccess('Partner program successfully updated');
    }

    public function destroy(Request $request)
    {
        $corpId = strtoupper($request->route('corp'));
        $corp_progId = $request->route('detail');

        DB::beginTransaction();
        try {

            $this->partnerProgramRepository->deletePartnerProgram($corp_progId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete partner program failed : ' . $e->getMessage());
            return Redirect::to('program/corporate/' . strtolower($corpId) . '/detail/' . $corp_progId)->withError('Failed to delete partner program');
        }

        return Redirect::to('program/corporate/')->withSuccess('Partner program successfully deleted');
    }
}
