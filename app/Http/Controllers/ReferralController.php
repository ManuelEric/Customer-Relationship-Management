<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReferralRequest;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\PartnerRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\ReferralRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
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
            'revenue_idr',
            'ref_date',
            'notes'
        ]);

        if ($referralDetails['currency'] != 'IDR') {
            $referralDetails['revenue_other'] = $referralDetails['revenue'];
            $referralDetails['revenue'] = $referralDetails['revenue_idr'];
            unset($referralDetails['revenue_idr']);
        } else {
            unset($referralDetails['revenue_idr']);
            unset($referralDetails['curs_rate']);
        }

        DB::beginTransaction();
        try {

            $referral = $this->referralRepository->createReferral($referralDetails);
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
        $referralId = $request->route('referral');
        $oldReferralProgram = $this->referralRepository->getReferralById($referralId);

        $newDetails = $request->only([
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

        if ($newDetails['currency'] != 'IDR') {
            $newDetails['revenue_other'] = $newDetails['revenue'];
            $newDetails['revenue'] = $newDetails['revenue_idr'];
            unset($newDetails['revenue_idr']);
        } else {
            unset($newDetails['revenue_idr']);
            unset($newDetails['curs_rate']);
        }

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

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Referral Program', Auth::user()->first_name . ' '. Auth::user()->last_name, $newDetails, $oldReferralProgram);

        return Redirect::to('program/referral/' . $referralId)->withSuccess('Referral successfully updated');
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
