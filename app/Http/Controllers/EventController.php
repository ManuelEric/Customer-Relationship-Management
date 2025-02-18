<?php

namespace App\Http\Controllers;

use App\Actions\Events\CreateEventAction;
use App\Actions\Events\DeleteEventAction;
use App\Actions\Events\UpdateEventAction;
use App\Enum\LogModule;
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
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        // if ($request->ajax())
            return $this->eventRepository->getAllEventDataTables();

        return view('pages.master.event.index');
    }

    public function show(Request $request)
    {
        $event_id = $request->route('event');
        $event = $this->eventRepository->getEventById($event_id);
        $event_pic = $event->eventPic->pluck('id')->toArray();
        $employees = $this->userRepository->rnGetAllUsersByRole('employee');
        # universities
        $universities = $this->universityRepository->getAllUniversities();
        $university_event = $this->universityEventRepository->getUniversityByEventId($event_id);
        # schools
        $schools = $this->schoolRepository->getAllSchools();
        $school_event = $this->schoolEventRepository->getSchoolByEventId($event_id);
        # corporate / partner
        $partners = $this->corporateRepository->getAllCorporate();
        $partner_event = $this->corporatePartnerRepository->getPartnerByEventId($event_id);

        $event_speakers = $this->agendaSpeakerRepository->getAllSpeakerByEvent($event_id);

        # retrieve program data
        $programs = $this->programRepository->getAllPrograms();

        return view('pages.master.event.form')->with(
            [
                'event' => $event,
                'eventPic' => $event_pic,
                'employees' => $employees,
                'universities' => $universities,
                'universityEvent' => $university_event,
                'schools' => $schools,
                'schoolEvent' => $school_event,
                'partners' => $partners,
                'partnerEvent' => $partner_event,
                'eventSpeakers' => $event_speakers,
                'programs' => $programs
            ]
        );
    }

    public function store(StoreEventRequest $request, CreateEventAction $createEventAction, LogService $log_service)
    {
        $new_event_details = $request->safe()->only([
            'event_title',
            'event_description',
            'event_location',
            'event_startdate',
            'event_enddate',
            'event_target',
            'category',
            'type'
        ]);

        DB::beginTransaction();
        try {

            $new_event = $createEventAction->execute($request, $new_event_details);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_EVENT, $e->getMessage(), $e->getLine(), $e->getFile(), $new_event_details);

            return Redirect::to('master/event/')->withError('Failed to create new event');
        }

        # store Success
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_EVENT, 'New event has been added', $new_event->toArray());

        return Redirect::to('master/event/' . $new_event->event_id)->withSuccess('Event successfully created');
    }

    public function create()
    {
        $partnership = $this->userRepository->rnGetAllUsersByDepartmentAndRole('Employee', 'Business Development');
        $sales = $this->userRepository->rnGetAllUsersByDepartmentAndRole('Employee', 'Client Management');
        $digital = $this->userRepository->rnGetAllUsersByDepartmentAndRole('Employee', 'Digital');
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

    public function update(StoreEventRequest $request, UpdateEventAction $updateEventAction, LogService $log_service)
    {
        $new_event_details = $request->only([
            'event_title',
            'event_description',
            'event_location',
            'event_startdate',
            'event_enddate',
            'event_target',
            'category',
            'type'
        ]);

        DB::beginTransaction();
        try {

            $updated_event = $updateEventAction->execute($request, $new_event_details);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_EVENT, $e->getMessage(), $e->getLine(), $e->getFile(), $new_event_details);

            return Redirect::to('master/event/' . $updated_event->event_id)->withError('Failed to update new event');
        }

        # Update success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_EVENT, 'New event has been updated', $updated_event->toArray());

        return Redirect::to('master/event/' . $updated_event->event_id)->withSuccess('Event successfully updated');
    }

    public function edit(Request $request)
    {

        $event_id = $request->route('event');
        $event = $this->eventRepository->getEventById($event_id);
        $event_pic = $event->eventPic->pluck('id')->toArray();

        $partnership = $this->userRepository->rnGetAllUsersByDepartmentAndRole('Employee', 'Business Development');
        $sales = $this->userRepository->rnGetAllUsersByDepartmentAndRole('Employee', 'Client Management');
        $digital = $this->userRepository->rnGetAllUsersByDepartmentAndRole('Employee', 'Digital');
        $employees = $partnership->merge($sales)->merge($digital);

        # universities
        $universities = $this->universityRepository->getAllUniversities();
        $university_event = $this->universityEventRepository->getUniversityByEventId($event_id);
        # schools
        $schools = $this->schoolRepository->getAllSchools();
        $school_event = $this->schoolEventRepository->getSchoolByEventId($event_id);
        # corporate / partner
        $partners = $this->corporateRepository->getAllCorporate();
        $partner_event = $this->corporatePartnerRepository->getPartnerByEventId($event_id);

        $event_speakers = $this->agendaSpeakerRepository->getAllSpeakerByEvent($event_id);

        # retrieve program data
        $programs = $this->programRepository->getAllPrograms();

        return view('pages.master.event.form')->with(
            [
                'edit' => true,
                'event' => $event,
                'eventPic' => $event_pic,
                'employees' => $employees,
                'universities' => $universities,
                'universityEvent' => $university_event,
                'schools' => $schools,
                'schoolEvent' => $school_event,
                'partners' => $partners,
                'partnerEvent' => $partner_event,
                'eventSpeakers' => $event_speakers,
                'programs' => $programs
            ]
        );
    }

    public function destroy(Request $request, DeleteEventAction $deleteEventAction, LogService $log_service)
    {
        $event_id = $request->route('event');

        $event = $this->eventRepository->getEventById($event_id);

        DB::beginTransaction();
        try {

            $deleteEventAction->execute($event_id);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_EVENT, $e->getMessage(), $e->getLine(), $e->getFile(), $event->toArray());

            Log::error('Delete event failed : ' . $e->getMessage());
            return Redirect::to('master/event/' . $event_id)->withError('Failed to delete event');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_EVENT, 'Event has been deleted', $event->toArray());

        return Redirect::to('master/event')->withSuccess('Event successfully deleted');
    }
}
