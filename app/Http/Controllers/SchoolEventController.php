<?php

namespace App\Http\Controllers;

use App\Actions\Schools\Event\CreateSchoolEventAction;
use App\Actions\Schools\Event\DeleteSchoolEventAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreSchoolEventRequest;
use App\Interfaces\SchoolEventRepositoryInterface;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
use App\Interfaces\SchoolDetailRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Models\Event;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class SchoolEventController extends Controller
{
    private SchoolRepositoryInterface $schoolRepository;
    private SchoolEventRepositoryInterface $schoolEventRepository;
    private AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;
    private SchoolDetailRepositoryInterface $schoolDetailRepository;

    public function __construct(SchoolEventRepositoryInterface $schoolEventRepository, AgendaSpeakerRepositoryInterface $agendaSpeakerRepository, SchoolDetailRepositoryInterface $schoolDetailRepository, SchoolRepositoryInterface $schoolRepository)
    {
        $this->schoolEventRepository = $schoolEventRepository;
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
        $this->schoolDetailRepository = $schoolDetailRepository;
        $this->schoolRepository = $schoolRepository;
    }

    public function store(StoreSchoolEventRequest $request, CreateSchoolEventAction $createSchoolEventAction, LogService $log_service)
    {
        $school_details = $request->safe()->only([
            'sch_id'
        ]);

        $event_id = $request->route('event');

        DB::beginTransaction();
        try {

            $createSchoolEventAction->execute($event_id, $school_details);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            $log_service->createErrorLog(LogModule::STORE_SCHOOL_EVENT, $e->getMessage(), $e->getLine(), $e->getFile(), $school_details);

            return Redirect::to('master/event/' . $event_id . '')->withError('Failed to add new school to event');
        }

        # create log success
        $log_service->createSuccessLog(LogModule::STORE_SCHOOL_EVENT, 'New school event has been added', $school_details);

        return Redirect::to('master/event/' . $event_id)->withSuccess('School successfully added to event');
    }

    public function destroy(Request $request, DeleteSchoolEventAction $deleteSchoolEventAction, LogService $log_service)
    {
        $event_id = $request->route('event');
        $school_id = $request->route('school');

        if ($this->agendaSpeakerRepository->getAllSpeakersByEventAndSchool($event_id, $school_id))
        {
            $schoolInfo = $this->schoolRepository->getSchoolById($school_id);
            return Redirect::back()->withError('You cannot remove the "'.$schoolInfo->sch_name.'" because there are speakers from the school. Do double check the agenda.');
        }

        DB::beginTransaction();
        try {

            $deleted_school_event = $deleteSchoolEventAction->execute($event_id, $school_id);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_SCHOOL_EVENT, $e->getMessage(), $e->getLine(), $e->getFile(), ['event_id' => $event_id, 'school_id' => $school_id]);

            return Redirect::to('master/event/' . $event_id)->withError('Failed to remove school from event');
        }

        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_SCHOOL_EVENT, 'School event has been deleted', ['event_id' => $event_id, 'school_id' => $school_id]);

        return Redirect::to('master/event/' . $event_id)->withSuccess('School successfully removed from event');
    }
}
