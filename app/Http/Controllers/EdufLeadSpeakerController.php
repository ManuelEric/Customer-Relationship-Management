<?php

namespace App\Http\Controllers;

use App\Actions\EdufLeads\Speaker\CreateEdufLeadSpeakerAction;
use App\Actions\EdufLeads\Speaker\DeleteEdufLeadSpeakerAction;
use App\Actions\EdufLeads\Speaker\UpdateEdufLeadSpeakerAction;
use App\Enum\LogModule;
use Illuminate\Http\Request;
use App\Http\Requests\StoreEdufLeadSpeakerRequest;
use App\Http\Traits\FindAgendaSpeakerPriorityTrait;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class EdufLeadSpeakerController extends Controller
{
    use FindAgendaSpeakerPriorityTrait;

    private AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;

    public function __construct(AgendaSpeakerRepositoryInterface $agendaSpeakerRepository)
    {
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
    }

    public function store(StoreEdufLeadSpeakerRequest $request, CreateEdufLeadSpeakerAction $createEdufLeadSpeakerAction, LogService $log_service)
    {
        $eduf_lead_id = $request->route('edufair');

        $new_agenda_speaker_details = $request->safe()->only([
            'speaker',
            'start_time',
            'end_time',
        ]);

        DB::beginTransaction();
        try {

            $eduf_lead_speaker = $createEdufLeadSpeakerAction->execute($eduf_lead_id, $new_agenda_speaker_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_EDUF_LEAD_SPEAKER, $e->getMessage(), $e->getLine(), $e->getFile(), $new_agenda_speaker_details);

            return Redirect::to('master/edufair/' . $eduf_lead_id)->withError('Failed to add speaker' . $e->getMessage());
        }

        $log_service->createSuccessLog(LogModule::STORE_EDUF_LEAD_SPEAKER, 'New eduf lead speaker has been added', $eduf_lead_speaker->toArray());

        return Redirect::to('master/edufair/' . $eduf_lead_id)->withSuccess('Speaker edufair successfully added');
    }

    # get request from event controller
    public function update(StoreEdufLeadSpeakerRequest $request, UpdateEdufLeadSpeakerAction $updateEdufLeadSpeakerAction, LogService $log_service)
    {
        $eduf_lead_id = $request->route('edufair');
        $new_agenda_speaker_details = $request->safe()->only([
            'speaker',
            'status_speaker',
            'notes_reason',
        ]);
        
        DB::beginTransaction();
        try {

            $updated_eduf_lead_speaker = $updateEdufLeadSpeakerAction->execute($new_agenda_speaker_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_EDUF_LEAD_SPEAKER, $e->getMessage(), $e->getLine(), $e->getFile(), $new_agenda_speaker_details);

            return Redirect::to('master/edufair/' . $eduf_lead_id)->withError('Failed to update speaker');
        }

        $log_service->createSuccessLog(LogModule::UPDATE_EDUF_LEAD_SPEAKER, 'New eduf lead speaker has been updated', $updated_eduf_lead_speaker->toArray());

        return Redirect::to('master/edufair/' . $eduf_lead_id)->withSuccess('Speaker edufair successfully updated');
    }

    public function destroy(Request $request, DeleteEdufLeadSpeakerAction $deleteEdufLeadSpeakerAction, LogService $log_service)
    {
        $eduf_lead_id = $request->route('edufair');
        $agenda_id = $request->route('speaker');

        DB::beginTransaction();
        try {

            $deleteEdufLeadSpeakerAction->execute($agenda_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_EDUF_LEAD_SPEAKER, $e->getMessage(), $e->getLine(), $e->getFile(), ['agenda_id' => $agenda_id]);

            return Redirect::to('master/edufair/' . $eduf_lead_id)->withError('Failed to remove speaker');
        }

        $log_service->createSuccessLog(LogModule::DELETE_EDUF_LEAD_SPEAKER, 'Eduf lead speaker has been deleted', ['agenda_id' => $agenda_id]);

        return Redirect::to('master/edufair/' . $eduf_lead_id)->withSuccess('Speaker edufair successfully removed');
    }
}
