<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreSchoolProgramSpeakerRequest;
use App\Http\Traits\FindAgendaSpeakerPriorityTrait;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class SchoolProgramSpeakerController extends Controller
{
    use FindAgendaSpeakerPriorityTrait;

    private AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;

    public function __construct(AgendaSpeakerRepositoryInterface $agendaSpeakerRepository)
    {
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
    }

    public function store(StoreSchoolProgramSpeakerRequest $request)
    {
        $schProgId = $request->route('sch_prog');
        $schoolId = $request->route('school');

        $agendaDetails = $request->only([
            'speaker_type',
            'allin_speaker',
            'partner_speaker',
            'school_speaker',
            'start_time',
            'end_time',
        ]);

        $agendaDetails['sch_prog_id'] = $schProgId;
        $agendaDetails['priority'] = (int) $this->maxAgendaSpeakerPriority('School-Program', $schProgId, $agendaDetails) + 1;

        DB::beginTransaction();
        try {

            $this->agendaSpeakerRepository->createAgendaSpeaker("School-Program", $schProgId, $agendaDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store event speaker failed : ' . $e->getMessage());
            return Redirect::to('program/school/' . strtolower($schoolId) . '/detail/' . $schProgId)->withError('Failed to add speaker' . $e->getMessage());
        }

        return Redirect::to('program/school/' . strtolower($schoolId) . '/detail/' . $schProgId)->withSuccess('School program speaker successfully added');
    }

    # get request from event controller
    public function update(StoreSchoolProgramSpeakerRequest $request)
    {

        $schProgId = $request->route('sch_prog');
        $schoolId = $request->route('school');
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
            return Redirect::to('program/school/' . strtolower($schoolId) . '/detail/' . $schProgId)->withError('Failed to update speaker');
        }

        // return response()->json($responseMessage);
        return Redirect::to('program/school/' . strtolower($schoolId) . '/detail/' . $schProgId)->withSuccess('School program speaker successfully updated');
    }

    public function destroy(Request $request)
    {
        $schoolId = $request->route('school');
        $schProgId = $request->route('sch_prog');
        $agendaId = $request->route('speaker');

        DB::beginTransaction();
        try {

            $this->agendaSpeakerRepository->deleteAgendaSpeaker($agendaId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete school program speaker failed : ' . $e->getMessage());
            return Redirect::to('program/school/' . strtolower($schoolId) . '/detail/' . $schProgId)->withError('Failed to remove speaker');
        }

        return Redirect::to('program/school/' . strtolower($schoolId) . '/detail/' . $schProgId)->withSuccess('School program speaker successfully removed');
    }
}
