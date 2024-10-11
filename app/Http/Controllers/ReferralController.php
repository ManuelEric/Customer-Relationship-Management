<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReferralRequest;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\PartnerRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\ReferralRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
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

    public function store(StoreReferralRequest $request)
    {
        $referral_details = $request->only([
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
        
        # Update attribute revenue by currency
        $referral_details_update_attribute_revenue = $this->referralProgramService->snUpdateAttributeRevenueByCurrency($referral_details);

        DB::beginTransaction();
        try {

            $referral = $this->referralRepository->createReferral($referral_details_update_attribute_revenue);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store referral failed : ' . $e->getMessage());
            return Redirect::to('program/referral/' . $referral->id)->withError('Failed to create new referral');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Referral Program', Auth::user()->first_name . ' '. Auth::user()->last_name, $referral);

        return Redirect::to('program/referral/' . $referral->id)->withSuccess('Referral successfully created');
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
        $referralId = $request->route('referral');

        $referral = $this->referralRepository->getReferralById($referralId);
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

    public function update(StoreReferralRequest $request)
    {
        $referral_id = $request->route('referral');
        $old_referral_program = $this->referralRepository->getReferralById($referral_id);

        $referral_details = $request->only([
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
        
        # Update attribute revenue by currency
        $referral_details_updated_attribute_revenue = $this->referralProgramService->snUpdateAttributeRevenueByCurrency($referral_details);

        # Update attribute program by referral type
        $referral_details_updated_attribute_program = $this->referralProgramService->snUpdateAttributeProgramByReferralType($referral_details_updated_attribute_revenue);

        DB::beginTransaction();
        try {

            $this->referralRepository->updateReferral($referral_id, $referral_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update referral failed : ' . $e->getMessage());
            return Redirect::to('program/referral/' . $referral_id)->withError('Failed to update referral');
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Referral Program', Auth::user()->first_name . ' '. Auth::user()->last_name, $referral_details_updated_attribute_program, $old_referral_program);

        return Redirect::to('program/referral/' . $referral_id)->withSuccess('Referral successfully updated');
    }

    public function edit(Request $request)
    {
        $referralId = $request->route('referral');

        $referral = $this->referralRepository->getReferralById($referralId);
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

    public function destroy(Request $request)
    {
        $referralId = $request->route('referral');
        $referral = $this->referralRepository->getReferralById($referralId);

        DB::beginTransaction();
        try {

            $this->referralRepository->deleteReferral($referralId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete referral failed : ' . $e->getMessage());
            return Redirect::to('program/referral')->withError('Failed to delete referral');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Client Program', Auth::user()->first_name . ' '. Auth::user()->last_name, $referral);

        return Redirect::to('program/referral')->withSuccess('Referral successfully deleted');
    }
}
