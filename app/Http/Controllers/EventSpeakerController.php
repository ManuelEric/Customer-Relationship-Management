<?php

namespace App\Http\Controllers;

use App\Actions\Events\Speaker\CreateEventSpeakerAction;
use App\Actions\Events\Speaker\DeleteEventSpeakerAction;
use App\Actions\Events\Speaker\UpdateEventSpeakerAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreSpeakerRequest;
use App\Http\Traits\FindAgendaSpeakerPriorityTrait;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
use App\Models\AgendaSpeaker;
use App\Services\Log\LogService;
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

    public function store(StoreSpeakerRequest $request, CreateEventSpeakerAction $createEventSpeakerAction, LogService $log_service)
    {
        $event_id = $request->route('event');
        
        $new_agenda_details = $request->only([
            'speaker_type',
            'allin_speaker',
            'partner_speaker',
            'school_speaker',
            'university_speaker',
            'start_time',
            'end_time',
        ]);

        DB::beginTransaction();
        try {

            $new_agenda = $createEventSpeakerAction->execute($event_id, $new_agenda_details);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_EVENT_SPEAKER, $e->getMessage(), $e->getLine(), $e->getFile(), $new_agenda_details);

            return Redirect::to('master/event/' . $event_id . '')->withError('Failed to add speaker');

        }

        $log_service->createSuccessLog(LogModule::STORE_EVENT_SPEAKER, 'New event speaker has been added', $new_agenda->toArray());

        return Redirect::to('master/event/'.$event_id)->withSuccess('Event speaker successfully added');
    }

    # get request from event controller
    public function update(StoreSpeakerRequest $request, UpdateEventSpeakerAction $updateEventSpeakerAction, LogService $log_service)
    {
        $event_id = $request->route('event');

        $new_event_speaker_details = $request->safe()->only([
            'status',
            'notes'
        ]);

        DB::beginTransaction();
        try {

            $updated_event_speaker = $updateEventSpeakerAction->execute($request, $new_event_speaker_details);

            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();

            $log_service->createErrorLog(LogModule::UPDATE_EVENT_SPEAKER, $e->getMessage(), $e->getLine(), $e->getFile(), $new_event_speaker_details);

            return Redirect::to('master/event/' . $event_id . '')->withError('Failed to update speaker');

        }

        $log_service->createSuccessLog(LogModule::UPDATE_EVENT_SPEAKER, 'Event speaker has been updated', $updated_event_speaker->toArray());

        return Redirect::to('master/event/'.$event_id)->withSuccess('Event speaker successfully updated');
    }

    public function destroy(Request $request, DeleteEventSpeakerAction $deleteEventSpeakerAction, LogService $log_service)
    {
        $event_id = $request->route('event');
        $agenda_id = $request->route('speaker');

        DB::beginTransaction();
        try {

            $deleteEventSpeakerAction->execute($agenda_id);

            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_EVENT_SPEAKER, $e->getMessage(), $e->getLine(), $e->getFile(), ['agenda_id' => $agenda_id]);

            return Redirect::to('master/event/' . $event_id . '')->withError('Failed to remove speaker');

        }

        $log_service->createSuccessLog(LogModule::DELETE_EVENT_SPEAKER, 'Event speaker has been updated', ['agenda_id' => $agenda_id]);

        return Redirect::to('master/event/'.$event_id)->withSuccess('Event speaker successfully removed');
    }
}
