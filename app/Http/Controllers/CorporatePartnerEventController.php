<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCorporatePartnerEventRequest;
use App\Interfaces\CorporatePartnerEventRepositoryInterface;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
use App\Interfaces\CorporatePicRepositoryInterface;
use App\Models\Corporate;
use App\Models\Event;
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

    public function store(StoreCorporatePartnerEventRequest $request)
    {
        $partnerDetails = $request->only([
            'corp_id'
        ]);

        $eventId = $request->route('event');

        DB::beginTransaction();
        try {

            $this->corporatePartnerEventRepository->addPartnerEvent($eventId, $partnerDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Add partner event failed : ' . $e->getMessage());
            return Redirect::to('master/event/' . $eventId . '')->withError('Failed to add new partner to event');
        }

        return Redirect::to('master/event/' . $eventId)->withSuccess('Partner successfully added to event');
    }

    public function destroy(Request $request)
    {
        $eventId = $request->route('event');
        $corporateId = $request->route('partner');


        DB::beginTransaction();
        try {

            $event = Event::whereEventId($eventId);

            if (count($event->partner_speaker()->where('corp_id', $corporateId)->get()) > 0) {
                $this->corporatePicRepository->deleteAgendaSpeaker($corporateId,  $eventId);
            }

            $this->corporatePartnerEventRepository->destroyPartnerEvent($eventId, $corporateId);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Remove partner event failed : ' . $e->getMessage());
            return Redirect::to('master/event/' . $eventId)->withError('Failed to remove partner from event');
        }

        return Redirect::to('master/event/' . $eventId)->withSuccess('Partner successfully removed from event');
    }
}
