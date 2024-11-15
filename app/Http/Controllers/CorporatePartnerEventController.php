<?php

namespace App\Http\Controllers;

use App\Actions\Corporates\Event\CreateCorporateEventAction;
use App\Actions\Corporates\Event\DeleteCorporateEventAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreCorporatePartnerEventRequest;
use App\Interfaces\CorporatePartnerEventRepositoryInterface;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
use App\Interfaces\CorporatePicRepositoryInterface;
use App\Models\Corporate;
use App\Models\Event;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class CorporatePartnerEventController extends Controller
{
    private CorporatePartnerEventRepositoryInterface $corporatePartnerEventRepository;
    private AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;
    private CorporatePicRepositoryInterface $corporatePicRepository;

    public function __construct(CorporatePartnerEventRepositoryInterface $corporatePartnerEventRepository, AgendaSpeakerRepositoryInterface $agendaSpeakerRepository, CorporatePicRepositoryInterface $corporatePicRepository)
    {
        $this->corporatePartnerEventRepository = $corporatePartnerEventRepository;
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
        $this->corporatePicRepository = $corporatePicRepository;
    }

    public function store(StoreCorporatePartnerEventRequest $request, CreateCorporateEventAction $createCorporateEventAction, LogService $log_service)
    {
        $partner_details = $request->safe()->only([
            'corp_id'
        ]);

        $event_id = $request->route('event');

        DB::beginTransaction();
        try {

            $createCorporateEventAction->execute($event_id, $partner_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_CORPORATE_EVENT, $e->getMessage(), $e->getLine(), $e->getFile(), $partner_details);

            return Redirect::to('master/event/' . $event_id . '')->withError('Failed to add new partner to event');
        }

        # create log success
        $log_service->createSuccessLog(LogModule::STORE_CORPORATE_EVENT, 'New corporate event has been added', $partner_details);

        return Redirect::to('master/event/' . $event_id)->withSuccess('Partner successfully added to event');
    }

    public function destroy(Request $request, DeleteCorporateEventAction $deleteCorporateEventAction, LogService $log_service)
    {
        $event_id = $request->route('event');
        $corporate_id = $request->route('partner');


        DB::beginTransaction();
        try {

            $deleteCorporateEventAction->execute($event_id, $corporate_id);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_CORPORATE_EVENT, $e->getMessage(), $e->getLine(), $e->getFile(), ['event_id' => $event_id, 'corporate_id' => $corporate_id]);

            return Redirect::to('master/event/' . $event_id)->withError('Failed to remove partner from event');
        }

        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_CORPORATE_EVENT, 'Corporate event has been deleted', ['event_id' => $event_id, 'corporate_id' => $corporate_id]);

        return Redirect::to('master/event/' . $event_id)->withSuccess('Partner successfully removed from event');
    }
}
