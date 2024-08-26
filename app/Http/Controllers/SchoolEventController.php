<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolEventRequest;
use App\Interfaces\SchoolEventRepositoryInterface;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
use App\Interfaces\SchoolDetailRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Models\Event;
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

    public function store(StoreSchoolEventRequest $request)
    {
        $schoolDetails = $request->only([
            'sch_id'
        ]);

        $eventId = $request->route('event');

        DB::beginTransaction();
        try {

            $this->schoolEventRepository->addSchoolEvent($eventId, $schoolDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Add school event failed : ' . $e->getMessage());
            return Redirect::to('master/event/' . $eventId . '')->withError('Failed to add new school to event');
        }

        return Redirect::to('master/event/' . $eventId)->withSuccess('School successfully added to event');
    }

    public function destroy(Request $request)
    {
        $eventId = $request->route('event');
        $schoolId = $request->route('school');

        if ($this->agendaSpeakerRepository->getAllSpeakersByEventAndSchool($eventId, $schoolId))
        {
            $schoolInfo = $this->schoolRepository->getSchoolById($schoolId);
            return Redirect::back()->withError('You cannot remove the "'.$schoolInfo->sch_name.'" because there are speakers from the school. Do double check the agenda.');
        }

        DB::beginTransaction();
        try {

            $event = Event::whereEventId($eventId);

            if (count($event->school_speaker()->where('sch_id', $schoolId)->get()) > 0) {
                $this->schoolDetailRepository->deleteAgendaSpeaker($schoolId, $eventId);
            }

            $this->schoolEventRepository->destroySchoolEvent($eventId, $schoolId);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Remove school event failed : ' . $e->getMessage());
            return Redirect::to('master/event/' . $eventId)->withError('Failed to remove school from event');
        }

        return Redirect::to('master/event/' . $eventId)->withSuccess('School successfully removed from event');
    }
}
