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
        $event_id = $request->route('event');
        
        $agenda_details = $request->only([
            'speaker_type',
            'allin_speaker',
            'partner_speaker',
            'school_speaker',
            'university_speaker',
            'start_time',
            'end_time',
        ]);

        $agenda_details['event_id'] = $event_id;
        $agenda_details['priority'] = (int) $this->maxAgendaSpeakerPriority('Event', $event_id, $agenda_details)+1;

        DB::beginTransaction();
        try {

            $this->agendaSpeakerRepository->createAgendaSpeaker("Event", $event_id, $agenda_details);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store event speaker failed : ' . $e->getMessage());
            return Redirect::to('master/event/' . $event_id . '')->withError('Failed to add speaker');

        }

        return Redirect::to('master/event/'.$event_id)->withSuccess('Event speaker successfully added');
    }

    # get request from event controller
    public function update(StoreSpeakerRequest $request)
    {
        $event_id = $request->route('event');
        $agenda_id = $request->speaker;
        $status = $request->status;
        $notes = $request->notes;

        DB::beginTransaction();
        try {

            $this->agendaSpeakerRepository->updateAgendaSpeaker($agenda_id, ['status' => $status, 'notes' => $notes]);
            $responseMessage = ['status' => true, 'message' => 'Speaker status has successfully changed'];
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('update status speaker failed : ' . $e->getMessage());
            return Redirect::to('master/event/' . $event_id . '')->withError('Failed to update speaker');

        }

        // return response()->json($responseMessage);
        return Redirect::to('master/event/'.$event_id)->withSuccess('Event speaker successfully updated');
    }

    public function destroy(Request $request)
    {
        $event_id = $request->route('event');
        $agenda_id = $request->route('speaker');

        DB::beginTransaction();
        try {

            $this->agendaSpeakerRepository->deleteAgendaSpeaker($agenda_id);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete event speaker failed : ' . $e->getMessage());
            return Redirect::to('master/event/' . $event_id . '')->withError('Failed to remove speaker');

        }

        return Redirect::to('master/event/'.$event_id)->withSuccess('Event speaker successfully removed');
    }
}
