<?php

namespace App\Http\Controllers;

use App\Actions\ReferralPrograms\CreateReferralProgramAction;
use App\Actions\ReferralPrograms\DeleteReferralProgramAction;
use App\Actions\ReferralPrograms\UpdateReferralProgramAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreReferralRequest;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\PartnerRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\ReferralRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Services\Log\LogService;
use App\Services\Program\ReferralProgramService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class ReferralController extends Controller
{
    use LoggingTrait;

    private ReferralRepositoryInterface $referralRepository;
    private CorporateRepositoryInterface $corporateRepository;
    private ProgramRepositoryInterface $programRepository;
    private UserRepositoryInterface $userRepository;
    private ReferralProgramService $referralProgramService;

    public function __construct(ReferralRepositoryInterface $referralRepository, CorporateRepositoryInterface $corporateRepository, ProgramRepositoryInterface $programRepository, UserRepositoryInterface $userRepository, ReferralProgramService $referralProgramService)
    {
        $this->referralRepository = $referralRepository;
        $this->corporateRepository = $corporateRepository;
        $this->programRepository = $programRepository;
        $this->userRepository = $userRepository;
        $this->referralProgramService = $referralProgramService;
    }

    public function index(Request $request)
    {

        if ($request->ajax())
            return $this->referralRepository->getAllReferralDataTables();

        return view('pages.program.referral.index');
    }

    public function store(StoreReferralRequest $request, CreateReferralProgramAction $createReferralProgramAction, LogService $log_service)
    {
        $referral_details = $request->safe()->only([
            'partner_id',
            'prog_id',
            'empl_id',
            'referral_type',
            'additional_prog_name',
            'currency',
            'number_of_student',
            'revenue',
            'revenue_idr',
            'ref_date',
            'notes'
        ]);
        
        DB::beginTransaction();
        try {

            $created_referral_program = $createReferralProgramAction->execute($referral_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_REFERRAL_PROGRAM, $e->getMessage(), $e->getLine(), $e->getFile(), $referral_details);

            return Redirect::to('program/referral/' . $created_referral_program->id)->withError('Failed to create new referral');
        }

        # store Success
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_REFERRAL_PROGRAM, 'New referral program has been deleted', $created_referral_program->toArray());

        return Redirect::to('program/referral/' . $created_referral_program->id)->withSuccess('Referral successfully created');
    }

    public function create()
    {
        $partners = $this->corporateRepository->getAllCorporate();
        $programs = $this->programRepository->getAllPrograms();
        $employees = $this->userRepository->getAllUsersByDepartmentAndRole('Employee', 'Business Development');

        return view('pages.program.referral.form')->with(
            [
                'edit' => true,
                'partners' => $partners,
                'programs' => $programs,
                'employees' => $employees
            ]
        );
    }

    public function show(Request $request)
    {
        $referral_id = $request->route('referral');

        $referral = $this->referralRepository->getReferralById($referral_id);
        $partners = $this->corporateRepository->getAllCorporate();
        $programs = $this->programRepository->getAllPrograms();

        $employees = $this->userRepository->getAllUsersByRole('Employee');

        return view('pages.program.referral.form')->with(
            [
                'referral' => $referral,
                'partners' => $partners,
                'programs' => $programs,
                'employees' => $employees,
            ]
        );
    }

    public function update(StoreReferralRequest $request, UpdateReferralProgramAction $updateReferralProgramAction, LogService $log_service)
    {
        $referral_id = $request->route('referral');

        $referral_details = $request->safe()->only([
            'partner_id',
            'prog_id',
            'empl_id',
            'referral_type',
            'additional_prog_name',
            'currency',
            'number_of_student',
            'revenue',
            'revenue_idr',
            'ref_date',
            'notes',
            'curs_rate',
        ]);
        

        DB::beginTransaction();
        try {

            $updated_referral_program = $updateReferralProgramAction->execute($referral_id, $referral_details);
            
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            $log_service->createErrorLog(LogModule::UPDATE_REFERRAL_PROGRAM, $e->getMessage(), $e->getLine(), $e->getFile(), $referral_details);
            return Redirect::to('program/referral/' . $referral_id)->withError('Failed to update referral');
        }

        # Update success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_REFERRAL_PROGRAM, 'Referral program has been updated', $updated_referral_program->toArray());

        return Redirect::to('program/referral/' . $referral_id)->withSuccess('Referral successfully updated');
    }

    public function edit(Request $request)
    {
        $referral_id = $request->route('referral');

        $referral = $this->referralRepository->getReferralById($referral_id);
        $partners = $this->corporateRepository->getAllCorporate();
        $programs = $this->programRepository->getAllPrograms();
        $employees = $this->userRepository->getAllUsersByDepartmentAndRole('Employee', 'Business Development');

        return view('pages.program.referral.form')->with(
            [
                'edit' => true,
                'referral' => $referral,
                'partners' => $partners,
                'programs' => $programs,
                'employees' => $employees,
            ]
        );
    }

    public function destroy(Request $request, DeleteReferralProgramAction $deleteReferralProgramAction, LogService $log_service)
    {
        $referral_id = $request->route('referral');
        $old_referral = $this->referralRepository->getReferralById($referral_id);

        DB::beginTransaction();
        try {

            $deleteReferralProgramAction->execute($referral_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_REFERRAL_PROGRAM, $e->getMessage(), $e->getLine(), $e->getFile(), $old_referral->toArray());

            return Redirect::to('program/referral')->withError('Failed to delete referral');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_REFERRAL_PROGRAM, 'Referral program has been deleted', $old_referral->toArray());

        return Redirect::to('program/referral')->withSuccess('Referral successfully deleted');
    }
}
