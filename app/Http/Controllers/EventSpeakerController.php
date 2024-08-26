<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpeakerRequest;
use App\Http\Traits\FindAgendaSpeakerPriorityTrait;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
use App\Models\AgendaSpeaker;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class EventSpeakerController extends Controller
{
    use FindAgendaSpeakerPriorityTrait;

    private AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;

    public function __construct(AgendaSpeakerRepositoryInterface $agendaSpeakerRepository)
    {
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
    }

    public function store(StoreSpeakerRequest $request)
    {
        $eventId = $request->route('event');
        
        $agendaDetails = $request->only([
            'speaker_type',
            'allin_speaker',
            'partner_speaker',
            'school_speaker',
            'university_speaker',
            'start_time',
            'end_time',
        ]);

        $agendaDetails['event_id'] = $eventId;
        $agendaDetails['priority'] = (int) $this->maxAgendaSpeakerPriority('Event', $eventId, $agendaDetails)+1;

        DB::beginTransaction();
        try {

            $this->agendaSpeakerRepository->createAgendaSpeaker("Event", $eventId, $agendaDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store event speaker failed : ' . $e->getMessage());
            return Redirect::to('master/event/' . $eventId . '')->withError('Failed to add speaker');

        }

        return Redirect::to('master/event/'.$eventId)->withSuccess('Event speaker successfully added');
    }

    # get request from event controller
    public function update(StoreSpeakerRequest $request)
    {
        $eventId = $request->route('event');
        $agendaId = $request->speaker;
        $status = $request->status;
        $notes = $request->notes;

        DB::beginTransaction();
        try {

            $this->agendaSpeakerRepository->updateAgendaSpeaker($agendaId, ['status' => $status, 'notes' => $notes]);
            $responseMessage = ['status' => true, 'message' => 'Speaker status has successfully changed'];
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('update status speaker failed : ' . $e->getMessage());
            return Redirect::to('master/event/' . $eventId . '')->withError('Failed to update speaker');

        }

        // return response()->json($responseMessage);
        return Redirect::to('master/event/'.$eventId)->withSuccess('Event speaker successfully updated');
    }

    public function destroy(Request $request)
    {
        $eventId = $request->route('event');
        $agendaId = $request->route('speaker');

        DB::beginTransaction();
        try {

            $this->agendaSpeakerRepository->deleteAgendaSpeaker($agendaId);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete event speaker failed : ' . $e->getMessage());
            return Redirect::to('master/event/' . $eventId . '')->withError('Failed to remove speaker');

        }

        return Redirect::to('master/event/'.$eventId)->withSuccess('Event speaker successfully removed');
    }
}
