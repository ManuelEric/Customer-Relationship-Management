<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePartnerRequest;
use App\Interfaces\PartnerRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class PartnerController extends Controller
{
    private PartnerRepositoryInterface $partnerRepository;

    public function __construct(PartnerRepositoryInterface $partnerRepository)
    {
        $this->partnerRepository = $partnerRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax())
            return $this->partnerRepository->getAllPartnerDataTables();

        return view('pages.instance.referral.index');
    }

    public function store(StorePartnerRequest $request) 
    {  
        $partnerDetails = $request->only([
            'pt_name',
            'pt_email',
            'pt_phone',
            'pt_institution',
            'pt_address',
            'status',
        ]);

        DB::beginTransaction();
        try {

            $this->partnerRepository->createPartner($partnerDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store partner failed : ' . $e->getMessage());
            return Redirect::to('instance/referral')->withError('Failed to create a new partner');

        }

        return Redirect::to('instance/referral')->withSuccess('Partner successfully created');
    }

    public function create()
    {
        return view('pages.instance.referral.form');
    }

    public function edit(Request $request)
    {
        $partnerId = $request->route('partner');
        $partner = $this->partnerRepository->getPartnerById($partnerId);

        return view('pages.instance.referral.form')->with(
            [
                'partner' => $partner
            ]
        );
    }

    public function update(StorePartnerRequest $request)
    {
        $newDetails = $request->only([
            'pt_name',
            'pt_email',
            'pt_phone',
            'pt_institution',
            'pt_address',
            'status',
        ]);
        $partnerId = $request->route('partner');

        DB::beginTransaction();
        try {

            $this->partnerRepository->updatePartner($partnerId, $newDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update partner failed : ' . $e->getMessage());
            return Redirect::to('instance/referral')->withError('Failed to update partner');

        }

        return Redirect::to('instance/referral')->withSuccess('Partner successfully updated');
    }

    public function destroy(Request $request)
    {
        $partnerId = $request->route('partner');
        
        DB::beginTransaction();
        try {

            $this->partnerRepository->deletePartner($partnerId);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete partner failed : ' . $e->getMessage());
            return Redirect::to('instance/referral')->withError('Failed to delete partner');

        }

        return Redirect::to('instance/referral')->withSuccess('Partner successfully deleted');
    }
}
