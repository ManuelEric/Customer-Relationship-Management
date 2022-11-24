<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\CorporatePartnerEventRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\SchoolEventRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\UniversityEventRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Event;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class EventController extends Controller
{
    use CreateCustomPrimaryKeyTrait;
    
    private EventRepositoryInterface $eventRepository;
    private UserRepositoryInterface $userRepository;
    private UniversityRepositoryInterface $universityRepository;
    private UniversityEventRepositoryInterface $universityEventRepository;
    private SchoolRepositoryInterface $schoolRepository;
    private SchoolEventRepositoryInterface $schoolEventRepository;
    private CorporateRepositoryInterface $corporateRepository;
    private CorporatePartnerEventRepositoryInterface $corporatePartnerRepository;

    public function __construct(EventRepositoryInterface $eventRepository, UserRepositoryInterface $userRepository, UniversityRepositoryInterface $universityRepository, UniversityEventRepositoryInterface $universityEventRepository, SchoolRepositoryInterface $schoolRepository, SchoolEventRepositoryInterface $schoolEventRepository, CorporateRepositoryInterface $corporateRepository, CorporatePartnerEventRepositoryInterface $corporatePartnerRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->userRepository = $userRepository;
        $this->universityRepository = $universityRepository;
        $this->universityEventRepository = $universityEventRepository;
        $this->schoolRepository = $schoolRepository;
        $this->schoolEventRepository = $schoolEventRepository;
        $this->corporateRepository = $corporateRepository;
        $this->corporatePartnerRepository = $corporatePartnerRepository;
    }
    
    public function index(Request $request)
    {
        if ($request->ajax())
        return $this->eventRepository->getAllEventDataTables();

        return view('pages.master.event.index');
    }

    public function show(Request $request)
    {
        $eventId = $request->route('event');
        $event = $this->eventRepository->getEventById($eventId);
        $eventPic = $event->eventPic->pluck('id')->toArray();
        $employees = $this->userRepository->getAllUsersByRole('employee');
        # universities
        $universities = $this->universityRepository->getAllUniversities();
        $universityEvent = $this->universityEventRepository->getUniversityByEventId($eventId);
        # schools
        $schools = $this->schoolRepository->getAllSchools();
        $schoolEvent = $this->schoolEventRepository->getSchoolByEventId($eventId);
        # corporate / partner
        $partners = $this->corporateRepository->getAllCorporate();
        $partnerEvent = $this->corporatePartnerRepository->getPartnerByEventId($eventId);
        

        return view('pages.master.event.form')->with(
            [
                'event' => $event,
                'eventPic' => $eventPic,
                'employees' => $employees,
                'universities' => $universities,
                'universityEvent' => $universityEvent,
                'schools' => $schools,
                'schoolEvent' => $schoolEvent,
                'partners' => $partners,
                'partnerEvent' => $partnerEvent,
            ]
        );
    }

    public function store(StoreEventRequest $request)
    {
        $eventDetails = $request->only([
            'event_title',
            'event_description',
            'event_location',
            'event_startdate',
            'event_enddate',
        ]);

        $employee_id = $request->user_id;

        $last_id = Event::max('event_id');
        $event_id_without_label = $this->remove_primarykey_label($last_id, 4);
        $event_id_with_label = 'EVT-' . $this->add_digit((int)$event_id_without_label + 1, 4);
        $eventDetails['event_id'] = $event_id_with_label;

        DB::beginTransaction();
        try {

            $this->eventRepository->createEvent($eventDetails);

            $this->eventRepository->addEventPic($event_id_with_label, $employee_id);

            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store event failed : ' . $e->getMessage());
            return Redirect::to('master/event/' . $event_id_with_label . '')->withError('Failed to create new event');

        }

        return Redirect::to('master/event/'.$event_id_with_label)->withSuccess('Event successfully created');
    }

    public function create()
    {
        $employees = $this->userRepository->getAllUsersByRole('employee');

        return view('pages.master.event.form')->with(
            [
                'edit' => true,
                'employees' => $employees
            ]
        );
    }

    public function update(StoreEventRequest $request)
    {
        $newDetails = $request->only([
            'event_title',
            'event_description',
            'event_location',
            'event_startdate',
            'event_enddate',
        ]);

        $eventId = $request->route('event');
        $newPic = $request->user_id;

        DB::beginTransaction();
        try {

            $this->eventRepository->updateEvent($eventId, $newDetails);

            $this->eventRepository->updateEventPic($eventId, $newPic);

            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update event failed : ' . $e->getMessage());
            return Redirect::to('master/event/' . $eventId . '')->withError('Failed to update new event');

        }

        return Redirect::to('master/event/'.$eventId)->withSuccess('Event successfully updated');
    }

    public function edit(Request $request)
    {
        $eventId = $request->route('event');
        $event = $this->eventRepository->getEventById($eventId);
        $eventPic = $event->eventPic->pluck('id')->toArray();
        $employees = $this->userRepository->getAllUsersByRole('employee');

        return view('pages.master.event.form')->with(
            [
                'edit' => true,
                'event' => $event,
                'eventPic' => $eventPic,
                'employees' => $employees,
            ]
        );
    }

    public function destroy(Request $request)
    {
        $eventId = $request->route('event');

        DB::beginTransaction();
        try {

            $this->eventRepository->deleteEvent($eventId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete event failed : ' . $e->getMessage());
            return Redirect::to('master/event/'.$eventId)->withError('Failed to delete event');
        }

        return Redirect::to('master/event')->withSuccess('Event successfully deleted');
    }
}
