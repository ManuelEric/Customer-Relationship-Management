<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolEventRequest;
use App\Interfaces\SchoolEventRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class SchoolEventController extends Controller
{
    private SchoolEventRepositoryInterface $schoolEventRepository;

    public function __construct(SchoolEventRepositoryInterface $schoolEventRepository)
    {
        $this->schoolEventRepository = $schoolEventRepository;
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

        return Redirect::to('master/event/'.$eventId)->withSuccess('School successfully added to event');
    }

    public function destroy(Request $request)
    {
        $eventId = $request->route('event');
        $schoolId = $request->route('school');

        DB::beginTransaction();
        try {

            $this->schoolEventRepository->destroySchoolEvent($eventId, $schoolId);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Remove school event failed : ' . $e->getMessage());
            return Redirect::to('master/event/'.$eventId)->withError('Failed to remove school from event');
        }

        return Redirect::to('master/event/'.$eventId)->withSuccess('School successfully removed from event');

    }
}
