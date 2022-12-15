<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StorePartnerProgramSpeakerRequest;
use App\Http\Traits\FindAgendaSpeakerpriorityTrait;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class PartnerProgramSpeakerController extends Controller
{
    use FindAgendaSpeakerpriorityTrait;

    private AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;

    public function __construct(AgendaSpeakerRepositoryInterface $agendaSpeakerRepository)
    {
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
    }

    public function store(StorePartnerProgramSpeakerRequest $request)
    {
        $partnerProgId = $request->route('corp_prog');
        $corpId = $request->route('corp');
        
        $agendaDetails = $request->only([
            'speaker_type',
            'allin_speaker',
            'partner_speaker',
            'school_speaker',
            'start_time',
            'end_time',
        ]);

        $agendaDetails['partner_prog_id'] = $partnerProgId;
        $agendaDetails['priority'] = (int) $this->maxAgendaSpeakerPriority('Partner-Program', $partnerProgId, $agendaDetails)+1;

        DB::beginTransaction();
        try {

            $this->agendaSpeakerRepository->createAgendaSpeaker("Partner-Program", $partnerProgId, $agendaDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store partner program speaker failed : ' . $e->getMessage());
            return Redirect::to('program/corporate/' . strtolower($corpId) . '/detail/' . $partnerProgId)->withError('Failed to add speaker'. $e->getMessage());

        }

        return Redirect::to('program/corporate/' . strtolower($corpId) . '/detail/' . $partnerProgId)->withSuccess('Partner program speaker successfully added');
    }

    public function update(StorePartnerProgramSpeakerRequest $request)
    {

        $partnerProgId = $request->route('corp_prog');
        $corpId = $request->route('corp');
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
            return Redirect::to('program/corporate/' . strtolower($corpId) . '/detail/' . $partnerProgId)->withError('Failed to update speaker' . $e->getMessage());

        }

        // return response()->json($responseMessage);
        return Redirect::to('program/corporate/'. strtolower($corpId) . '/detail/' . $partnerProgId)->withSuccess('School program speaker successfully updated');
    }

    public function destroy(Request $request)
    {
        $corpId = $request->route('corp');
        $partnerProgId = $request->route('corp_prog');
        $agendaId = $request->route('speaker');

        DB::beginTransaction();
        try {

            $this->agendaSpeakerRepository->deleteAgendaSpeaker($agendaId);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete partner program speaker failed : ' . $e->getMessage());
            return Redirect::to('program/corporate/'. strtolower($corpId) .'/detail/'.$partnerProgId)->withError('Failed to remove speaker');

        }

        return Redirect::to('program/corporate/'.strtolower($corpId).'/detail/'.$partnerProgId)->withSuccess('Partner program speaker successfully removed');
    }
}


