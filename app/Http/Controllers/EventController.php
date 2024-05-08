<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
use App\Interfaces\CorporatePartnerEventRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SchoolEventRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\UniversityEventRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Agenda;
use App\Models\Event;
use App\Models\pivot\AgendaSpeaker;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class EventController extends Controller
{
    use LoggingTrait;
    use CreateCustomPrimaryKeyTrait;

    private EventRepositoryInterface $eventRepository;
    private UserRepositoryInterface $userRepository;
    private UniversityRepositoryInterface $universityRepository;
    private UniversityEventRepositoryInterface $universityEventRepository;
    private SchoolRepositoryInterface $schoolRepository;
    private SchoolEventRepositoryInterface $schoolEventRepository;
    private CorporateRepositoryInterface $corporateRepository;
    private CorporatePartnerEventRepositoryInterface $corporatePartnerRepository;
    private AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;
    private ProgramRepositoryInterface $programRepository;

    public function __construct(EventRepositoryInterface $eventRepository, UserRepositoryInterface $userRepository, UniversityRepositoryInterface $universityRepository, UniversityEventRepositoryInterface $universityEventRepository, SchoolRepositoryInterface $schoolRepository, SchoolEventRepositoryInterface $schoolEventRepository, CorporateRepositoryInterface $corporateRepository, CorporatePartnerEventRepositoryInterface $corporatePartnerRepository, AgendaSpeakerRepositoryInterface $agendaSpeakerRepository, ProgramRepositoryInterface $programRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->userRepository = $userRepository;
        $this->universityRepository = $universityRepository;
        $this->universityEventRepository = $universityEventRepository;
        $this->schoolRepository = $schoolRepository;
        $this->schoolEventRepository = $schoolEventRepository;
        $this->corporateRepository = $corporateRepository;
        $this->corporatePartnerRepository = $corporatePartnerRepository;
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
        $this->programRepository = $programRepository;
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

        $eventSpeakers = $this->agendaSpeakerRepository->getAllSpeakerByEvent($eventId);

        # retrieve program data
        $programs = $this->programRepository->getAllPrograms();

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
                'eventSpeakers' => $eventSpeakers,
                'programs' => $programs
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
            'event_target',
            'category',
            'type'
        ]);

        $employee_id = $request->user_id;

        $last_id = Event::max('event_id');
        $event_id_without_label = $this->remove_primarykey_label($last_id, 4);
        $event_id_with_label = 'EVT-' . $this->add_digit((int)$event_id_without_label + 1, 4);
        $eventDetails['event_id'] = $event_id_with_label;
        $fileName = null;

        DB::beginTransaction();
        try {

            # upload banner 
            if ($request->file('event_banner')) {
                $fileName = time() . '-' . $event_id_with_label . '.' . $request->event_banner->extension();
                $request->event_banner->storeAs(null, $fileName, 'uploaded_file_event');
            }

            $eventDetails['event_banner'] = $fileName;

            $newEvent = $this->eventRepository->createEvent($eventDetails);

            $this->eventRepository->addEventPic($event_id_with_label, $employee_id);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store event failed : ' . $e->getMessage());
            return Redirect::to('master/event/' . $event_id_with_label . '')->withError('Failed to create new event');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Event', Auth::user()->first_name . ' ' . Auth::user()->last_name, $newEvent);

        return Redirect::to('master/event/' . $event_id_with_label)->withSuccess('Event successfully created');
    }

    public function create()
    {
        $partnership = $this->userRepository->getAllUsersByDepartmentAndRole('Employee', 'Business Development');
        $sales = $this->userRepository->getAllUsersByDepartmentAndRole('Employee', 'Client Management');
        $digital = $this->userRepository->getAllUsersByDepartmentAndRole('Employee', 'Digital');
        $employees = $partnership->merge($sales)->merge($digital);

        # retrieve program data
        $programs = $this->programRepository->getAllPrograms();

        return view('pages.master.event.form')->with(
            [
                'edit' => true,
                'employees' => $employees,
                'programs' => $programs
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
            'event_target',
            'category',
            'type'
        ]);

        $eventId = $request->route('event');
        $newPic = $request->user_id;

        $oldEvent = $this->eventRepository->getEventById($eventId);

        DB::beginTransaction();
        try {

            // return $request->all();

            # check if the banner event is changed or not
            // if (isset($request->change_banner) && $request->change_banner == "yes") {
            if (isset($request->change_banner)) {

                # get existing banner as a file
                if ($existingBannerName = $request->old_event_banner) {
                    $existingImagePath = storage_path('app/public/uploaded_file/events') . '/' . $existingBannerName;
                    if (File::exists($existingImagePath))
                        File::delete($existingImagePath);
                }

                # upload banner 
                if ($request->file('event_banner')) {
                    $fileName = time() . '-' . $eventId . '.' . $request->event_banner->extension();
                    $request->event_banner->storeAs(null, $fileName, 'uploaded_file_event');
                    $newDetails['event_banner'] = $fileName;
                }
            }

            $this->eventRepository->updateEvent($eventId, $newDetails);

            $this->eventRepository->updateEventPic($eventId, $newPic);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update event failed : ' . $e->getMessage());
            return Redirect::to('master/event/' . $eventId)->withError('Failed to update new event');
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Event', Auth::user()->first_name . ' ' . Auth::user()->last_name, $newDetails, $oldEvent);

        return Redirect::to('master/event/' . $eventId)->withSuccess('Event successfully updated');
    }

    public function edit(Request $request)
    {

        $eventId = $request->route('event');
        $event = $this->eventRepository->getEventById($eventId);
        $eventPic = $event->eventPic->pluck('id')->toArray();

        $partnership = $this->userRepository->getAllUsersByDepartmentAndRole('Employee', 'Business Development');
        $sales = $this->userRepository->getAllUsersByDepartmentAndRole('Employee', 'Client Management');
        $digital = $this->userRepository->getAllUsersByDepartmentAndRole('Employee', 'Digital');
        $employees = $partnership->merge($sales)->merge($digital);

        # universities
        $universities = $this->universityRepository->getAllUniversities();
        $universityEvent = $this->universityEventRepository->getUniversityByEventId($eventId);
        # schools
        $schools = $this->schoolRepository->getAllSchools();
        $schoolEvent = $this->schoolEventRepository->getSchoolByEventId($eventId);
        # corporate / partner
        $partners = $this->corporateRepository->getAllCorporate();
        $partnerEvent = $this->corporatePartnerRepository->getPartnerByEventId($eventId);

        $eventSpeakers = $this->agendaSpeakerRepository->getAllSpeakerByEvent($eventId);

        # retrieve program data
        $programs = $this->programRepository->getAllPrograms();

        return view('pages.master.event.form')->with(
            [
                'edit' => true,
                'event' => $event,
                'eventPic' => $eventPic,
                'employees' => $employees,
                'universities' => $universities,
                'universityEvent' => $universityEvent,
                'schools' => $schools,
                'schoolEvent' => $schoolEvent,
                'partners' => $partners,
                'partnerEvent' => $partnerEvent,
                'eventSpeakers' => $eventSpeakers,
                'programs' => $programs
            ]
        );
    }

    public function destroy(Request $request)
    {
        $eventId = $request->route('event');

        $event = $this->eventRepository->getEventById($eventId);

        DB::beginTransaction();
        try {

            $this->eventRepository->deleteEvent($eventId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete event failed : ' . $e->getMessage());
            return Redirect::to('master/event/' . $eventId)->withError('Failed to delete event');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Event', Auth::user()->first_name . ' ' . Auth::user()->last_name, $event);

        return Redirect::to('master/event')->withSuccess('Event successfully deleted');
    }
}
