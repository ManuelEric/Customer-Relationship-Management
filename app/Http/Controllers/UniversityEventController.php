<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUniversityEventRequest;
use App\Interfaces\UniversityEventRepositoryInterface;
use App\Interfaces\UniversityPicRepositoryInterface;
use App\Models\Event;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class UniversityEventController extends Controller
{
    private UniversityEventRepositoryInterface $universityEventRepository;
    private UniversityPicRepositoryInterface $universityPicRepository;

    public function __construct(UniversityEventRepositoryInterface $universityEventRepository, UniversityPicRepositoryInterface $universityPicRepository)
    {
        $this->universityEventRepository = $universityEventRepository;
        $this->universityPicRepository = $universityPicRepository;
    }

    public function store(StoreUniversityEventRequest $request)
    {
        $universityDetails = $request->only([
            'univ_id'
        ]);

        $eventId = $request->route('event');

        DB::beginTransaction();
        try {

            $this->universityEventRepository->addUniversityEvent($eventId, $universityDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Add university event failed : ' . $e->getMessage());
            return Redirect::to('master/event/' . $eventId . '')->withError('Failed to add new university to event');
        }

        return Redirect::to('master/event/' . $eventId)->withSuccess('University successfully added to event');
    }

    public function destroy(Request $request)
    {
        $eventId = $request->route('event');
        $universityId = $request->route('university');

        DB::beginTransaction();
        try {

            $event = Event::whereEventId($eventId);

            if (count($event->university_speaker()->where('univ_id', $universityId)->get()) > 0) {
                $this->universityPicRepository->deleteAgendaSpeaker($universityId, $eventId);
            }


            $this->universityEventRepository->destroyUniversityEvent($eventId, $universityId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Remove university event failed : ' . $e->getMessage());
            return Redirect::to('master/event/' . $eventId)->withError('Failed to remove university from event');
        }

        return Redirect::to('master/event/' . $eventId)->withSuccess('University successfully removed from event');
    }
}
