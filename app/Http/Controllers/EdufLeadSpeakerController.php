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
        $edufLeadId = $request->route('edufair');

        $agendaDetails = $request->only([
            'speaker',
            'start_time',
            'end_time',
        ]);

        $agendaDetails['speaker_type'] = 'internal';
        $agendaDetails['eduf_id'] = $edufLeadId;
        $agendaDetails['priority'] = (int) $this->maxAgendaSpeakerPriority('Edufair', $edufLeadId, $agendaDetails) + 1;

        DB::beginTransaction();
        try {

            $this->agendaSpeakerRepository->createAgendaSpeaker("Edufair", $edufLeadId, $agendaDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store edufair speaker failed : ' . $e->getMessage());
            return Redirect::to('master/edufair/' . $edufLeadId)->withError('Failed to add speaker' . $e->getMessage());
        }

        return Redirect::to('master/edufair/' . $edufLeadId)->withSuccess('Speaker edufair successfully added');
    }

    # get request from event controller
    public function update(StoreEdufLeadSpeakerRequest $request)
    {

        $edufLeadId = $request->route('edufair');
        $agendaId = $request->speaker;
        $status = $request->status_speaker;
        $notes = $request->notes_reason;

        DB::beginTransaction();
        try {

            $this->agendaSpeakerRepository->updateAgendaSpeaker($agendaId, ['status' => $status, 'notes' => $notes]);
            $responseMessage = ['status' => true, 'message' => 'Speaker status has successfully changed'];
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('update status speaker failed : ' . $e->getMessage());
            return Redirect::to('master/edufair/' . $edufLeadId)->withError('Failed to update speaker');
        }

        // return response()->json($responseMessage);
        return Redirect::to('master/edufair/' . $edufLeadId)->withSuccess('Speaker edufair successfully updated');
    }

    public function destroy(Request $request)
    {
        $edufLeadId = $request->route('edufair');
        $agendaId = $request->route('speaker');

        DB::beginTransaction();
        try {

            $this->agendaSpeakerRepository->deleteAgendaSpeaker($agendaId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete edufair speaker failed : ' . $e->getMessage());
            return Redirect::to('master/edufair/' . $edufLeadId)->withError('Failed to remove speaker');
        }

        return Redirect::to('master/edufair/' . $edufLeadId)->withSuccess('Speaker edufair successfully removed');
    }
}
