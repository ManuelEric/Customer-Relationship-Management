<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReferralRequest;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\PartnerRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\ReferralRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class ReferralController extends Controller
{

    private ReferralRepositoryInterface $referralRepository;
    private CorporateRepositoryInterface $corporateRepository;
    private ProgramRepositoryInterface $programRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(ReferralRepositoryInterface $referralRepository, CorporateRepositoryInterface $corporateRepository, ProgramRepositoryInterface $programRepository, UserRepositoryInterface $userRepository)
    {
        $this->referralRepository = $referralRepository;
        $this->corporateRepository = $corporateRepository;
        $this->programRepository = $programRepository;
        $this->userRepository = $userRepository;
    }
    
    public function index(Request $request)
    {
        
        if ($request->ajax())
            return $this->referralRepository->getAllReferralDataTables();

        return view('pages.program.referral.index');

    }

    public function store(StoreReferralRequest $request)
    {  
        $referralDetails = $request->only([
            'partner_id',
            'prog_id',
            'empl_id',
            'referral_type',
            'additional_prog_name',
            'currency',
            'number_of_student',
            'revenue',
            'ref_date',
            'notes'
        ]);

        DB::beginTransaction();
        try {

            $referral = $this->referralRepository->createReferral($referralDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store referral failed : ' . $e->getMessage());
            return Redirect::to('program/referral/' . $referral->id)->withError('Failed to create new referral');

        }

        return Redirect::to('program/referral/'.$referral->id)->withSuccess('Referral successfully created');
    }

    public function create()
    {
        $partners = $this->corporateRepository->getAllCorporate();
        $B2BPrograms = $this->programRepository->getAllProgramByType("B2B");
        $B2BandB2CPrograms = $B2BPrograms->merge($this->programRepository->getAllProgramByType("B2B/B2C"));
        $employees = $this->userRepository->getAllUsersByRole('Employee');

        return view('pages.program.referral.form')->with(
            [
                'edit' => true,
                'partners' => $partners,
                'programs' => $B2BandB2CPrograms,
                'employees' => $employees
            ]
        );
    }

    public function show(Request $request)
    {
        $referralId = $request->route('referral');

        $referral = $this->referralRepository->getReferralById($referralId);
        $partners = $this->corporateRepository->getAllCorporate();
        $B2BPrograms = $this->programRepository->getAllProgramByType("B2B");
        $B2BandB2CPrograms = $B2BPrograms->merge($this->programRepository->getAllProgramByType("B2B/B2C"));

        $employees = $this->userRepository->getAllUsersByRole('Employee');

        return view('pages.program.referral.form')->with(
            [
                'referral' => $referral,
                'partners' => $partners,
                'programs' => $B2BandB2CPrograms,
                'employees' => $employees,
            ]
        );
    }

    public function update(StoreReferralRequest $request)
    {
        $referralId = $request->route('referral');

        $newDetails = $request->only([
            'partner_id',
            'prog_id',
            'empl_id',
            'referral_type',
            'additional_prog_name',
            'currency',
            'number_of_student',
            'revenue',
            'ref_date',
            'notes'
        ]);

        $referral_type = $request->referral_type;
        if ($referral_type == "In")
            $newDetails['additional_prog_name'] = null;
        elseif ($referral_type == "Out")
            $newDetails['prog_id'] = null;

        DB::beginTransaction();
        try {

            $this->referralRepository->updateReferral($referralId, $newDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update referral failed : ' . $e->getMessage());
            return Redirect::to('program/referral/' . $referralId)->withError('Failed to update referral');

        }

        return Redirect::to('program/referral/'.$referralId)->withSuccess('Referral successfully updated');
    }

    public function edit(Request $request)
    {
        $referralId = $request->route('referral');

        $referral = $this->referralRepository->getReferralById($referralId);
        $partners = $this->corporateRepository->getAllCorporate();
        $B2BPrograms = $this->programRepository->getAllProgramByType("B2B");
        $B2BandB2CPrograms = $B2BPrograms->merge($this->programRepository->getAllProgramByType("B2B/B2C"));
        $employees = $this->userRepository->getAllUsersByRole('Employee');

        return view('pages.program.referral.form')->with(
            [
                'edit' => true,
                'referral' => $referral,
                'partners' => $partners,
                'programs' => $B2BandB2CPrograms,
                'employees' => $employees,
            ]
        );
    }

    public function destroy(Request $request)
    {
        $referralId = $request->route('referral');

        DB::beginTransaction();
        try {

            $this->referralRepository->deleteReferral($referralId);
            DB::commit();
             
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete referral failed : ' . $e->getMessage());
            return Redirect::to('program/referral')->withError('Failed to delete referral');

        }

        return Redirect::to('program/referral')->withSuccess('Referral successfully deleted');
    }
}
