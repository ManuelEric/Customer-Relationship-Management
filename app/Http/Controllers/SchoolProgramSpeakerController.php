<?php

namespace App\Http\Controllers;

use App\Actions\SchoolPrograms\Speaker\CreateSchoolProgramSpeakerAction;
use App\Actions\SchoolPrograms\Speaker\DeleteSchoolProgramSpeakerAction;
use App\Actions\SchoolPrograms\Speaker\UpdateSchoolProgramSpeakerAction;
use App\Enum\LogModule;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSchoolProgramSpeakerRequest;
use App\Http\Traits\FindAgendaSpeakerPriorityTrait;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
use App\Services\Log\LogService;
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

    public function store(StoreSchoolProgramSpeakerRequest $request, CreateSchoolProgramSpeakerAction $createSchoolProgramSpeakerAction, LogService $log_service)
    {
        $school_program_id = $request->route('sch_prog');
        $school_id = $request->route('school');

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

            $created_school_program_speaker = $createSchoolProgramSpeakerAction->execute($agenda_details, $school_program_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_SCHOOL_PROGRAM_SPEAKER, $e->getMessage(), $e->getLine(), $e->getFile(), $agenda_details);

            return Redirect::to('program/school/' . strtolower($school_id) . '/detail/' . $school_program_id)->withError('Failed to add speaker' . $e->getMessage());
        }

        # create log success
        $log_service->createSuccessLog(LogModule::STORE_SCHOOL_PROGRAM_SPEAKER, 'New school program speaker has been added', $created_school_program_speaker->toArray());

        return Redirect::to('program/school/' . strtolower($school_id) . '/detail/' . $school_program_id)->withSuccess('School program speaker successfully added');
    }

    # get request from event controller
    public function update(StoreSchoolProgramSpeakerRequest $request, UpdateSchoolProgramSpeakerAction $updateSchoolProgramSpeakerAction, LogService $log_service)
    {

        $sch_prog_id = $request->route('sch_prog');
        $school_id = $request->route('school');
        $agenda_id = $request->speaker;
        $status = $request->status_speaker;
        $notes = $request->notes_reason;

        DB::beginTransaction();
        try {

            $updated_school_program_speaker = $updateSchoolProgramSpeakerAction->execute($agenda_id, $status, $notes);
            // $responseMessage = ['status' => true, 'message' => 'Speaker status has successfully changed'];
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            $log_service->createErrorLog(LogModule::UPDATE_SCHOOL_PROGRAM_SPEAKER, $e->getMessage(), $e->getLine(), $e->getFile(), ['agenda_id' => $agenda_id, 'status' => $status, 'notes' => $notes]);

            return Redirect::to('program/school/' . strtolower($school_id) . '/detail/' . $sch_prog_id)->withError('Failed to update speaker');
        }


        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_SCHOOL_PROGRAM_SPEAKER, 'School program speaker has been updated', $updated_school_program_speaker->toArray());

        return Redirect::to('program/school/' . strtolower($school_id) . '/detail/' . $sch_prog_id)->withSuccess('School program speaker successfully updated');
    }

    public function destroy(Request $request, DeleteSchoolProgramSpeakerAction $deleteSchoolProgramSpeakerAction, LogService $log_service)
    {
        $school_id = $request->route('school');
        $sch_prog_id = $request->route('sch_prog');
        $agenda_id = $request->route('speaker');

        DB::beginTransaction();
        try {

            $deleteSchoolProgramSpeakerAction->execute($agenda_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            $log_service->createErrorLog(LogModule::DELETE_SCHOOL_PROGRAM_SPEAKER, $e->getMessage(), $e->getLine(), $e->getFile(), ['agenda_id' => $agenda_id]);
            return Redirect::to('program/school/' . strtolower($school_id) . '/detail/' . $sch_prog_id)->withError('Failed to remove speaker');
        }

        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_SCHOOL_PROGRAM_SPEAKER, 'School program speaker has been deleted', ['agenda_id' => $agenda_id]);
        
        return Redirect::to('program/school/' . strtolower($school_id) . '/detail/' . $sch_prog_id)->withSuccess('School program speaker successfully removed');
    }
}
