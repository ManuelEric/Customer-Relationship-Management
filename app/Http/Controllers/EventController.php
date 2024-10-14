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
use App\Services\FileUploadService;
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
        $event_id = $request->route('event');
        $event = $this->eventRepository->getEventById($event_id);
        $event_pic = $event->eventPic->pluck('id')->toArray();
        $employees = $this->userRepository->getAllUsersByRole('employee');
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

    public function store(StoreEventRequest $request, FileUploadService $file_upload_service)
    {
        $event_details = $request->only([
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
        $event_details['event_id'] = $event_id_with_label;
        $file_name = null;

        DB::beginTransaction();
        try {

            # upload banner 
            if ($request->file('event_banner')) {
                $file_name = time() . '-' . $event_id_with_label . '.' . $request->event_banner->extension();
                $file_upload_service->snUploadFile($request->file('event_banner'), null, $file_name);
                // $request->event_banner->storeAs(null, $file_name, 'uploaded_file_event');
            }

            $event_details['event_banner'] = $file_name;

            $new_event = $this->eventRepository->createEvent($event_details);

            $this->eventRepository->addEventPic($event_id_with_label, $employee_id);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store event failed : ' . $e->getMessage());
            return Redirect::to('master/event/' . $event_id_with_label . '')->withError('Failed to create new event');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Event', Auth::user()->first_name . ' ' . Auth::user()->last_name, $new_event);

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

    public function update(StoreEventRequest $request, FileUploadService $file_upload_service)
    {
        $new_details = $request->only([
            'event_title',
            'event_description',
            'event_location',
            'event_startdate',
            'event_enddate',
            'event_target',
            'category',
            'type'
        ]);

        $event_id = $request->route('event');
        $new_pic = $request->user_id;

        $old_event = $this->eventRepository->getEventById($event_id);

        DB::beginTransaction();
        try {

            // return $request->all();

            # check if the banner event is changed or not
            // if (isset($request->change_banner) && $request->change_banner == "yes") {
            if (isset($request->change_banner)) {

                # get existing banner as a file
                if ($existing_banner_name = $request->old_event_banner) {
                    $existing_image_path = storage_path('app/public/uploaded_file/events') . '/' . $existing_banner_name;
                    if (File::exists($existing_image_path))
                        File::delete($existing_image_path);
                }

                # upload banner 
                if ($request->file('event_banner')) {
                    $file_name = time() . '-' . $event_id . '.' . $request->event_banner->extension();
                    $file_upload_service->snUploadFile($request->file('event_banner'), null, $file_name);
                    // $request->event_banner->storeAs(null, $file_name, 'uploaded_file_event');
                    $new_details['event_banner'] = $file_name;
                }
            }

            $this->eventRepository->updateEvent($event_id, $new_details);

            $this->eventRepository->updateEventPic($event_id, $new_pic);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update event failed : ' . $e->getMessage());
            return Redirect::to('master/event/' . $event_id)->withError('Failed to update new event');
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Event', Auth::user()->first_name . ' ' . Auth::user()->last_name, $new_details, $old_event);

        return Redirect::to('master/event/' . $event_id)->withSuccess('Event successfully updated');
    }

    public function edit(Request $request)
    {

        $event_id = $request->route('event');
        $event = $this->eventRepository->getEventById($event_id);
        $event_pic = $event->eventPic->pluck('id')->toArray();

        $partnership = $this->userRepository->getAllUsersByDepartmentAndRole('Employee', 'Business Development');
        $sales = $this->userRepository->getAllUsersByDepartmentAndRole('Employee', 'Client Management');
        $digital = $this->userRepository->getAllUsersByDepartmentAndRole('Employee', 'Digital');
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

    public function destroy(Request $request)
    {
        $event_id = $request->route('event');

        $event = $this->eventRepository->getEventById($event_id);

        DB::beginTransaction();
        try {

            $this->eventRepository->deleteEvent($event_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete event failed : ' . $e->getMessage());
            return Redirect::to('master/event/' . $event_id)->withError('Failed to delete event');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Event', Auth::user()->first_name . ' ' . Auth::user()->last_name, $event);

        return Redirect::to('master/event')->withSuccess('Event successfully deleted');
    }
}
