<?php

namespace App\Http\Controllers;

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

    public function store(StoreEdufLeadSpeakerRequest $request)
    {
        $eduf_lead_id = $request->route('edufair');

        $agenda_details = $request->only([
            'speaker',
            'start_time',
            'end_time',
        ]);

        $agenda_details['speaker_type'] = 'internal';
        $agenda_details['eduf_id'] = $eduf_lead_id;
        $agenda_details['priority'] = (int) $this->maxAgendaSpeakerPriority('Edufair', $eduf_lead_id, $agenda_details) + 1;

        DB::beginTransaction();
        try {

            $this->agendaSpeakerRepository->createAgendaSpeaker("Edufair", $eduf_lead_id, $agenda_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store edufair speaker failed : ' . $e->getMessage());
            return Redirect::to('master/edufair/' . $eduf_lead_id)->withError('Failed to add speaker' . $e->getMessage());
        }

        return Redirect::to('master/edufair/' . $eduf_lead_id)->withSuccess('Speaker edufair successfully added');
    }

    # get request from event controller
    public function update(StoreEdufLeadSpeakerRequest $request)
    {

        $eduf_lead_id = $request->route('edufair');
        $agenda_id = $request->speaker;
        $status = $request->status_speaker;
        $notes = $request->notes_reason;

        DB::beginTransaction();
        try {

            $this->agendaSpeakerRepository->updateAgendaSpeaker($agenda_id, ['status' => $status, 'notes' => $notes]);
            $responseMessage = ['status' => true, 'message' => 'Speaker status has successfully changed'];
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('update status speaker failed : ' . $e->getMessage());
            return Redirect::to('master/edufair/' . $eduf_lead_id)->withError('Failed to update speaker');
        }

        // return response()->json($responseMessage);
        return Redirect::to('master/edufair/' . $eduf_lead_id)->withSuccess('Speaker edufair successfully updated');
    }

    public function destroy(Request $request)
    {
        $eduf_lead_id = $request->route('edufair');
        $agenda_id = $request->route('speaker');

        DB::beginTransaction();
        try {

            $this->agendaSpeakerRepository->deleteAgendaSpeaker($agenda_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete edufair speaker failed : ' . $e->getMessage());
            return Redirect::to('master/edufair/' . $eduf_lead_id)->withError('Failed to remove speaker');
        }

        return Redirect::to('master/edufair/' . $eduf_lead_id)->withSuccess('Speaker edufair successfully removed');
    }
}
