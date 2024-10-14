<?php

namespace App\Http\Controllers;

use App\Actions\EdufLeads\Speaker\CreateEdufLeadSpeakerAction;
use App\Actions\EdufLeads\Speaker\DeleteEdufLeadSpeakerAction;
use App\Actions\EdufLeads\Speaker\UpdateEdufLeadSpeakerAction;
use Illuminate\Http\Request;
use App\Http\Requests\StoreEdufLeadSpeakerRequest;
use App\Http\Traits\FindAgendaSpeakerPriorityTrait;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
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

    public function store(StoreEdufLeadSpeakerRequest $request, CreateEdufLeadSpeakerAction $createEdufLeadSpeakerAction)
    {
        $eduf_lead_id = $request->route('edufair');

        $new_agenda_speaker_details = $request->safe()->only([
            'speaker',
            'start_time',
            'end_time',
        ]);

        DB::beginTransaction();
        try {

            $createEdufLeadSpeakerAction->execute($eduf_lead_id, $new_agenda_speaker_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store edufair speaker failed : ' . $e->getMessage());
            return Redirect::to('master/edufair/' . $eduf_lead_id)->withError('Failed to add speaker' . $e->getMessage());
        }

        return Redirect::to('master/edufair/' . $eduf_lead_id)->withSuccess('Speaker edufair successfully added');
    }

    # get request from event controller
    public function update(StoreEdufLeadSpeakerRequest $request, UpdateEdufLeadSpeakerAction $updateEdufLeadSpeakerAction)
    {
        $eduf_lead_id = $request->route('edufair');
        $new_agenda_speaker_details = $request->safe()->only([
            'speaker',
            'status_speaker',
            'notes_reason',
        ]);
        DB::beginTransaction();
        try {

            $updateEdufLeadSpeakerAction->execute($new_agenda_speaker_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('update status speaker failed : ' . $e->getMessage());
            return Redirect::to('master/edufair/' . $eduf_lead_id)->withError('Failed to update speaker');
        }

        // return response()->json($responseMessage);
        return Redirect::to('master/edufair/' . $eduf_lead_id)->withSuccess('Speaker edufair successfully updated');
    }

    public function destroy(Request $request, DeleteEdufLeadSpeakerAction $deleteEdufLeadSpeakerAction)
    {
        $eduf_lead_id = $request->route('edufair');
        $agenda_id = $request->route('speaker');

        DB::beginTransaction();
        try {

            $deleteEdufLeadSpeakerAction->execute($agenda_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete edufair speaker failed : ' . $e->getMessage());
            return Redirect::to('master/edufair/' . $eduf_lead_id)->withError('Failed to remove speaker');
        }

        return Redirect::to('master/edufair/' . $eduf_lead_id)->withSuccess('Speaker edufair successfully removed');
    }
}
