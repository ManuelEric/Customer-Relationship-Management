<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCorporateRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\CorporatePicRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\PartnerProgramRepositoryInterface;
use App\Interfaces\PartnerAgreementRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Corporate;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class CorporateController extends Controller
{
    use CreateCustomPrimaryKeyTrait;

    private CorporateRepositoryInterface $corporateRepository;
    private CorporatePicRepositoryInterface $corporatePicRepository;
    private PartnerProgramRepositoryInterface $partnerProgramRepository;
    private PartnerAgreementRepositoryInterface $partnerAgreementRepository;
    protected UserRepositoryInterface $userRepository;


    public function __construct(CorporateRepositoryInterface $corporateRepository, CorporatePicRepositoryInterface $corporatePicRepository, PartnerProgramRepositoryInterface $partnerProgramRepository, PartnerAgreementRepositoryInterface $partnerAgreementRepository, UserRepositoryInterface $userRepository)
    {
        $this->corporateRepository = $corporateRepository;
        $this->corporatePicRepository = $corporatePicRepository;
        $this->partnerProgramRepository = $partnerProgramRepository;
        $this->partnerAgreementRepository = $partnerAgreementRepository;
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->corporateRepository->getAllCorporateDataTables();
        }

        return view('pages.instance.corporate.index');
    }

    public function store(StoreCorporateRequest $request)
    {
        $corporateDetails = $request->only([
            'corp_name',
            'corp_industry',
            'corp_mail',
            'corp_phone',
            'corp_insta',
            'corp_site',
            'corp_region',
            'corp_address',
            'corp_note',
            'corp_password',
            'country_type',
            'type',
            'partnership_type',
        ]);

        $last_id = Corporate::max('corp_id');
        $corp_id_without_label = $this->remove_primarykey_label($last_id, 5);
        $corp_id_with_label = 'CORP-' . $this->add_digit($corp_id_without_label + 1, 4);
        $corporateDetails['corp_password'] = Hash::make($request->corp_password);

        DB::beginTransaction();
        try {

            $this->corporateRepository->createCorporate(['corp_id' => $corp_id_with_label] + $corporateDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store corporate failed : ' . $e->getMessage());
            return Redirect::to('instance/corporate/' . $corp_id_with_label)->withError('Failed to create new corporate');
        }

        return Redirect::to('instance/corporate/' . $corp_id_with_label)->withSuccess('Corporate successfully created');
    }

    public function create()
    {
        return view('pages.instance.corporate.form');
    }

    public function update(StoreCorporateRequest $request)
    {
        $newDetails = $request->only([
            'corp_id',
            'corp_name',
            'corp_industry',
            'corp_mail',
            'corp_phone',
            'corp_insta',
            'corp_site',
            'corp_region',
            'corp_address',
            'corp_note',
            'country_type',
            'type',
            'partnership_type',
            // 'corp_password',
        ]);

        // if ($corp_password = $newDetails['corp_password']) 
        //     $newDetails['corp_password'] = Hash::make($corp_password);
        $corporateId = $request->route('corporate');

        DB::beginTransaction();
        try {

            $this->corporateRepository->updateCorporate($newDetails['corp_id'], $newDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update corporate failed : ' . $e->getMessage());
            return Redirect::to('instance/corporate/' . $corporateId)->withError('Failed to update corporate');
        }

        return Redirect::to('instance/corporate/' . $corporateId)->withSuccess('Corporate successfully updated');
    }

    public function show(Request $request)
    {
        $corporateId = $request->route('corporate');
        $corporate = $this->corporateRepository->getCorporateById($corporateId);
        
        
        # retrieve School Program data by schoolId
        $partnerPrograms = $this->partnerProgramRepository->getAllPartnerProgramsByPartnerId($corporateId);
        
        $partnerAgreements = $this->partnerAgreementRepository->getAllPartnerAgreementsByPartnerId($corporateId);
        
        $pics = $this->corporatePicRepository->getAllCorporatePicByCorporateId($corporateId);

        # retrieve employee data
        $employees = $this->userRepository->getAllUsersByRole('Employee');


        return view('pages.instance.corporate.form')->with(
            [
                'corporate' => $corporate,
                'partnerPrograms' => $partnerPrograms,
                'partnerAgreements' => $partnerAgreements,
                'pics' => $pics,
                'employees' => $employees
            ]
        );
    }

    public function edit(Request $request)
    {
        $corporateId = $request->route('corporate');
        $corporate = $this->corporateRepository->getCorporateById($corporateId);

        return view('pages.instance.corporate.form')->with(
            [
                'edit' => true,
                'corporate' => $corporate
            ]
        );
    }

    public function destroy(Request $request)
    {
        $corporateId = $request->route('corporate');

        DB::beginTransaction();
        try {

            $this->corporateRepository->deleteCorporate($corporateId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete corporate failed : ' . $e->getMessage());
            return Redirect::to('instance/corporate')->withError('Failed to delete corporate');
        }

        return Redirect::to('instance/corporate')->withSuccess('Corporate successfully deleted');
    }
}