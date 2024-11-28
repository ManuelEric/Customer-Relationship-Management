<?php

namespace App\Http\Controllers;

use App\Actions\PartnerPrograms\Speaker\CreatePartnerProgramSpeakerAction;
use App\Actions\PartnerPrograms\Speaker\UpdatePartnerProgramSpeakerAction;
use App\Actions\PartnerPrograms\Speaker\DeletePartnerProgramSpeakerAction;
use App\Enum\LogModule;
use Illuminate\Http\Request;
use App\Http\Requests\StorePartnerProgramSpeakerRequest;
use App\Http\Traits\FindAgendaSpeakerPriorityTrait;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class PartnerProgramSpeakerController extends Controller
{
    use FindAgendaSpeakerPriorityTrait;

    private AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;

    public function __construct(AgendaSpeakerRepositoryInterface $agendaSpeakerRepository)
    {
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
    }

    public function store(StorePartnerProgramSpeakerRequest $request, CreatePartnerProgramSpeakerAction $createPartnerProgramSpeakerAction, LogService $log_service)
    {
        $partner_program_id = $request->route('corp_prog');
        $corp_id = $request->route('corp');
        
        $agenda_details = $request->safe()->only([
            'speaker_type',
            'allin_speaker',
            'partner_speaker',
            'school_speaker',
            'start_time',
            'end_time',
        ]);

        DB::beginTransaction();
        try {

            $created_partner_program_speaker = $createPartnerProgramSpeakerAction->execute($agenda_details, $partner_program_id);

            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_PARTNER_PROGRAM_SPEAKER, $e->getMessage(), $e->getLine(), $e->getFile(), $agenda_details);

            return Redirect::to('program/corporate/' . strtolower($corp_id) . '/detail/' . $partner_program_id)->withError('Failed to add speaker'. $e->getMessage());

        }

        # create log success
        $log_service->createSuccessLog(LogModule::STORE_PARTNER_PROGRAM_SPEAKER, 'New Partner program speaker has been added', $created_partner_program_speaker->toArray());

        return Redirect::to('program/corporate/' . strtolower($corp_id) . '/detail/' . $partner_program_id)->withSuccess('Partner program speaker successfully added');
    }

    public function update(StorePartnerProgramSpeakerRequest $request, UpdatePartnerProgramSpeakerAction $updatePartnerProgramSpeakerAction, LogService $log_service)
    {

        $partner_program_id = $request->route('corp_prog');
        $corp_id = $request->route('corp');
        $agenda_id = $request->speaker;
        $status = $request->status_speaker;
        $notes = $request->notes_reason;

        DB::beginTransaction();
        try {


            $updated_partner_program_speaker = $updatePartnerProgramSpeakerAction->execute($agenda_id, $status, $notes);
            // $responseMessage = ['status' => true, 'message' => 'Speaker status has successfully changed'];
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_PARTNER_PROGRAM_SPEAKER, $e->getMessage(), $e->getLine(), $e->getFile(), ['agenda_id' => $agenda_id, 'status' => $status, 'notes' => $notes]);

            return Redirect::to('program/corporate/' . strtolower($corp_id) . '/detail/' . $partner_program_id)->withError('Failed to update speaker' . $e->getMessage());

        }

        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_PARTNER_PROGRAM_SPEAKER, 'Partner program speaker has been updated', $updated_partner_program_speaker->toArray());
        
        // return response()->json($responseMessage);
        return Redirect::to('program/corporate/'. strtolower($corp_id) . '/detail/' . $partner_program_id)->withSuccess('School program speaker successfully updated');
    }

    public function destroy(Request $request, DeletePartnerProgramSpeakerAction $deletePartnerProgramSpeakerAction, LogService $log_service)
    {
        $corp_id = $request->route('corp');
        $partner_prog_id = $request->route('corp_prog');
        $agenda_id = $request->route('speaker');

        DB::beginTransaction();
        try {

            $deletePartnerProgramSpeakerAction->execute($agenda_id);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_PARTNER_PROGRAM_SPEAKER, $e->getMessage(), $e->getLine(), $e->getFile(), ['agenda_id' => $agenda_id]);
            return Redirect::to('program/corporate/'. strtolower($corp_id) .'/detail/'.$partner_prog_id)->withError('Failed to remove speaker');

        }

        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_PARTNER_PROGRAM_SPEAKER, 'Partner program speaker has been deleted', ['agenda_id' => $agenda_id]);

        return Redirect::to('program/corporate/'.strtolower($corp_id).'/detail/'.$partner_prog_id)->withSuccess('Partner program speaker successfully removed');
    }
}


