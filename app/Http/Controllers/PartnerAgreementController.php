<?php

namespace App\Http\Controllers;

use App\Actions\Partners\Agreement\CreatePartnerAgreementAction;
use App\Actions\Partners\Agreement\DeletePartnerAgreementAction;
use App\Enum\LogModule;
use App\Http\Requests\StorePartnerAgreementRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\CorporatePicRepositoryInterface;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
use App\Interfaces\PartnerAgreementRepositoryInterface;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PartnerAgreementController extends Controller
{
    use CreateCustomPrimaryKeyTrait;
    use LoggingTrait;

    protected UserRepositoryInterface $userRepository;
    protected CorporateRepositoryInterface $corporateRepository;
    protected CorporatePicRepositoryInterface $corporatePicRepository;
    protected AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;
    protected PartnerAgreementRepositoryInterface $partnerAgreementRepository;

    public function __construct(
        UserRepositoryInterface $userRepository, 
        CorporateRepositoryInterface $corporateRepository,
        CorporatePicRepositoryInterface $corporatePicRepository,
        AgendaSpeakerRepositoryInterface $agendaSpeakerRepository,
        PartnerAgreementRepositoryInterface $partnerAgreementRepository,
        )
    {
        $this->userRepository = $userRepository;
        $this->corporateRepository = $corporateRepository;
        $this->corporatePicRepository = $corporatePicRepository;
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
        $this->partnerAgreementRepository = $partnerAgreementRepository;
    }

  

    public function store(StorePartnerAgreementRequest $request, CreatePartnerAgreementAction $createPartnerAgreementAction, LogService $log_service)
    {        
        $corp_id = $request->route('corporate');

        $partner_agreement_details = $request->all();
     
       
        DB::beginTransaction();
        try {
            
            $created_partner_agreement = $createPartnerAgreementAction->execute($request, $corp_id, $partner_agreement_details);
            
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_PARTNER_AGREEMENT, $e->getMessage(), $e->getLine(), $e->getFile(), $partner_agreement_details);

            // NOTE: Notif error ga muncul
            return Redirect::to('instance/corporate/'.strtolower($corp_id))->withError('Failed to create partner agreement');
        }
        
        # store Success
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_PARTNER_AGREEMENT, 'New partner agreement has been added', $created_partner_agreement->toArray());

        // NOTE: Notif success ga muncul
        return Redirect::to('instance/corporate/'.strtolower($corp_id))->withSuccess('Partner agreement successfully created');
    }

    public function destroy(Request $request, DeletePartnerAgreementAction $deletePartnerAgreementAction, LogService $log_service)
    {
        $corp_id = $request->route('corporate');
        $partner_agreement_id = $request->route('agreement');
        
        DB::beginTransaction();
        try {

            $deleted_partner_agreement = $deletePartnerAgreementAction->execute($partner_agreement_id, $corp_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_PARTNER_AGREEMENT, $e->getMessage(), $e->getLine(), $e->getFile(), $deleted_partner_agreement->toArray());

            return Redirect::to('instance/corporate/' . strtolower($corp_id))->withError('Failed to delete partner agreement');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_PARTNER_AGREEMENT, 'Partner agreement has been deleted', $deleted_partner_agreement->toArray());

        return Redirect::to('instance/corporate/'. strtolower($corp_id))->withSuccess('Partner Agreement successfully deleted');
    }
}