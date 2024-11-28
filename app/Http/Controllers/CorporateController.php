<?php

namespace App\Http\Controllers;

use App\Actions\Corporates\CreateCorporateAction;
use App\Actions\Corporates\DeleteCorporateAction;
use App\Actions\Corporates\UpdateCorporateAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreCorporateRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\CorporatePicRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\PartnerProgramRepositoryInterface;
use App\Interfaces\PartnerAgreementRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Corporate;
use App\Services\Log\LogService;
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

    public function store(StoreCorporateRequest $request, CreateCorporateAction $createCorporateAction, LogService $log_service)
    {
        $corporate_details = $request->safe()->only([
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

        DB::beginTransaction();
        try {

            $created_corporate = $createCorporateAction->execute($request, $corporate_details);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            $log_service->createErrorLog(LogModule::STORE_CORPORATE, $e->getMessage(), $e->getLine(), $e->getFile(), $corporate_details);
            return Redirect::to('instance/corporate')->withError('Failed to create new corporate');
        }

        # create log success
        $log_service->createSuccessLog(LogModule::STORE_CORPORATE, 'New corporate has been added', $created_corporate->toArray());

        return Redirect::to('instance/corporate/' . $created_corporate->corp_id)->withSuccess('Corporate successfully created');
    }

    public function create()
    {
        return view('pages.instance.corporate.form');
    }

    public function update(StoreCorporateRequest $request, UpdateCorporateAction $updateCorporateAction, LogService $log_service)
    {
        $corporate_details = $request->safe()->only([
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
        ]);

        $corporate_id = $request->route('corporate');

        DB::beginTransaction();
        try {

            $updated_corporate = $updateCorporateAction->execute($corporate_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update corporate failed : ' . $e->getMessage());
            $log_service->createErrorLog(LogModule::UPDATE_CORPORATE, $e->getMessage(), $e->getLine(), $e->getFile(), $corporate_details);

            return Redirect::to('instance/corporate/' . $corporate_id)->withError('Failed to update corporate');
        }

        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_CORPORATE, 'Corporate has been updated', $updated_corporate->toArray());

        return Redirect::to('instance/corporate/' . $corporate_id)->withSuccess('Corporate successfully updated');
    }

    public function show(Request $request)
    {
        $corporate_id = $request->route('corporate');
        $corporate = $this->corporateRepository->getCorporateById($corporate_id);


        # retrieve School Program data by schoolId
        $partnerPrograms = $this->partnerProgramRepository->getAllPartnerProgramsByPartnerId($corporate_id);

        $partnerAgreements = $this->partnerAgreementRepository->getAllPartnerAgreementsByPartnerId($corporate_id);

        $pics = $this->corporatePicRepository->getAllCorporatePicByCorporateId($corporate_id);

        # retrieve employee from partnership team data
        # because for now 29/03/2023 there aren't partnership team, so we use client management
        $employees = $this->userRepository->rnGetAllUsersByDepartmentAndRole('Employee', 'Business Development');

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
        $corporate_id = $request->route('corporate');
        $corporate = $this->corporateRepository->getCorporateById($corporate_id);

        return view('pages.instance.corporate.form')->with(
            [
                'edit' => true,
                'corporate' => $corporate
            ]
        );
    }

    public function destroy(Request $request, DeleteCorporateAction $deleteCorporateAction, LogService $log_service)
    {
        $corporate_id = $request->route('corporate');

        DB::beginTransaction();
        try {

            $deleted_corporate = $deleteCorporateAction->execute($corporate_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_CORPORATE, $e->getMessage(), $e->getLine(), $e->getFile(), $deleted_corporate->toArray());
            return Redirect::to('instance/corporate')->withError('Failed to delete corporate');
        }
        
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_CORPORATE, 'Corporate has been deleted', $deleted_corporate->toArray());

        return Redirect::to('instance/corporate')->withSuccess('Corporate successfully deleted');
    }
}
