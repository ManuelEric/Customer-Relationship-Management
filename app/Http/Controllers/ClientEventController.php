<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreClientEventRequest;
use App\Http\Requests\StoreImportExcelRequest;
use App\Http\Requests\StoreClientEventEmbedRequest;
use App\Http\Traits\CheckExistingClient;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Imports\ClientEventImport;
use App\Interfaces\ClientEventLogMailRepositoryInterface;
use App\Interfaces\CurriculumRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\EdufLeadRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\SchoolCurriculumRepositoryInterface;
use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\TagRepositoryInterface;
use App\Models\Client;
use App\Models\School;
use App\Models\UserClientAdditionalInfo;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class ClientEventController extends Controller
{
    use CheckExistingClient;
    use CreateCustomPrimaryKeyTrait;
    use StandardizePhoneNumberTrait;
    protected CurriculumRepositoryInterface $curriculumRepository;
    protected ClientRepositoryInterface $clientRepository;
    protected ClientEventRepositoryInterface $clientEventRepository;
    protected CorporateRepositoryInterface $corporateRepository;
    protected EdufLeadRepositoryInterface $edufLeadRepository;
    protected EventRepositoryInterface $eventRepository;
    protected LeadRepositoryInterface $leadRepository;
    protected SchoolRepositoryInterface $schoolRepository;
    protected SchoolCurriculumRepositoryInterface $schoolCurriculumRepository;
    protected RoleRepositoryInterface $roleRepository;
    protected ClientEventLogMailRepositoryInterface $clientEventLogMailRepository;
    protected TagRepositoryInterface $tagRepository;


    public function __construct(
        CurriculumRepositoryInterface $curriculumRepository,
        ClientRepositoryInterface $clientRepository,
        ClientEventRepositoryInterface $clientEventRepository,
        CorporateRepositoryInterface $corporateRepository,
        EdufLeadRepositoryInterface $edufLeadRepository,
        EventRepositoryInterface $eventRepository,
        LeadRepositoryInterface $leadRepository,
        SchoolRepositoryInterface $schoolRepository,
        SchoolCurriculumRepositoryInterface $schoolCurriculumRepository,
        RoleRepositoryInterface $roleRepository,
        ClientEventLogMailRepositoryInterface $clientEventLogMailRepository,
        TagRepositoryInterface $tagRepository
    ) {
        $this->curriculumRepository = $curriculumRepository;
        $this->clientRepository = $clientRepository;
        $this->clientEventRepository = $clientEventRepository;
        $this->corporateRepository = $corporateRepository;
        $this->edufLeadRepository = $edufLeadRepository;
        $this->eventRepository = $eventRepository;
        $this->leadRepository = $leadRepository;
        $this->schoolRepository = $schoolRepository;
        $this->schoolCurriculumRepository = $schoolCurriculumRepository;
        $this->roleRepository = $roleRepository;
        $this->clientEventLogMailRepository = $clientEventLogMailRepository;
        $this->tagRepository = $tagRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) 
        {
            $event_name = $request->get('event_name');
            $filter['event_name'] = $event_name;

            return $this->clientEventRepository->getAllClientEventDataTables($filter);
        }

        $events = $this->eventRepository->getAllEvents();

        return view('pages.program.client-event.index')->with(
            [
                'events' => $events
            ]
        );
    }

    public function create()
    {
        $curriculums = $this->curriculumRepository->getAllCurriculums();
        $clients = $this->clientRepository->getAllClients();
        $events = $this->eventRepository->getAllEvents();
        $leads = $this->leadRepository->getAllLead();
        $ext_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        $schools = $this->schoolRepository->getAllSchools();
        $partners = $this->corporateRepository->getAllCorporate();

        return view('pages.program.client-event.form')->with(
            [
                'curriculums' => $curriculums,
                'clients' => $clients,
                'events' => $events,
                'ext_edufair' => $ext_edufair,
                'kols' => $kols,
                'leads' => $leads,
                'schools' => $schools,
                'partners' => $partners,
            ]
        );
    }

    public function store(StoreClientEventRequest $request)
    {

        // Client existing
        $clientEvents = $request->only([
            'client_id',
            'event_id',
            'lead_id',
            'eduf_id',
            'partner_id',
            'status',
            'joined_date'
        ]);

        // Client not existing
        if ($request->existing_client == 0) {


            // Client as student or teacher
            $clientDetails = $request->only([
                'first_name',
                'last_name',
                'mail',
                'phone',
                'dob',
                'state',
                'sch_id',
                'st_grade',
                'lead_id',
                'eduf_id',
                // 'partner_id',
                'kol_lead_id',
                'event_id',
                'graduation_year',
                'st_levelinterest',
                'st_password'
            ]);
            unset($clientDetails['phone']);
            $clientDetails['phone'] = $this->setPhoneNumber($request->phone);

            // Client as parent
            if ($request->status_client == 'Parent') {
                $clientDetails = $request->only([
                    'first_name',
                    'last_name',
                    'mail',
                    'phone',
                    'dob',
                    'state',
                    'lead_id',
                    'eduf_id',
                    'partner_id',
                    'kol_lead_id',
                    'event_id',
                ]);
            }
        }

        # set lead_id based on lead_id & kol_lead_id
        # when lead_id is kol
        # then put kol_lead_id to lead_id
        # otherwise
        # when lead_id is not kol 
        # then lead_id is lead_id
        if ($request->lead_id == "kol") {

            unset($clientEvents['lead_id']);
            $clientEvents['eduf_id'] = null;
            $clientEvents['partner_id'] = null;
            $clientEvents['lead_id'] = $request->kol_lead_id;

            # LS010 = partner
        } else if ($request->lead_id == 'LS010') {
            $clientEvents['eduf_id'] = null;
        }
        # LS017 = external edufair
        else if ($request->lead_id != 'LS017' && $request->lead_id != 'kol') {

            $clientEvents['eduf_id'] = null;
            $clientEvents['partner_id'] = null;
        }
        # LS017 = external edufair
        else if ($request->lead_id != "kol" && $request->lead_id == 'LS017') {

            $clientEvents['partner_id'] = null;
        }

        DB::beginTransaction();

        try {

            # case 1
            # create new school
            # when sch_id is "add-new" 
            if ($request->sch_id == "add-new") {

                $schoolDetails = $request->only([
                    'sch_name',
                    // 'sch_location',
                    'sch_type',
                    'sch_score',
                ]);

                $last_id = School::max('sch_id');
                $school_id_without_label = $this->remove_primarykey_label($last_id, 4);
                $school_id_with_label = 'SCH-' . $this->add_digit($school_id_without_label + 1, 4);

                if (!$school = $this->schoolRepository->createSchool(['sch_id' => $school_id_with_label] + $schoolDetails))
                    throw new Exception('Failed to store new school', 1);

                # insert school curriculum
                if (!$this->schoolCurriculumRepository->createSchoolCurriculum($school_id_with_label, $request->sch_curriculum))
                    throw new Exception('Failed to store school curriculum', 1);


                # remove field sch_id from student detail if exist
                unset($clientDetails['sch_id']);

                # create index sch_id to student details
                # filled with a new school id that was inserted before
                $clientDetails['sch_id'] = $school->sch_id;
            }

            // Case 2
            // Create new client
            // When client not existing
            if ($request->existing_client == 0) {

                switch ($request->status_client) {
                    case 'Student':
                        if (!$clientCreated = $this->clientRepository->createClient('Student', $clientDetails))
                            throw new Exception('Failed to store new client', 2);
                        break;

                    case 'Parent':
                        if (!$clientCreated = $this->clientRepository->createClient('Parent', $clientDetails))
                            throw new Exception('Failed to store new client', 2);
                        break;

                    case 'Teacher/Counsellor':
                        if (!$clientCreated = $this->clientRepository->createClient('Teacher/Counselor', $clientDetails))
                            throw new Exception('Failed to store new client', 2);
                        break;
                }

                $clientEvents['client_id'] = $clientCreated->id;
            }

            // Case 3
            // Create client event
            # insert into client event
            if (!$this->clientEventRepository->createClientEvent($clientEvents))
                throw new Exception('Failed to store new client event', 3);


            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            switch ($e->getCode()) {
                case 1:
                    Log::error('Store school failed from client event : ' . $e->getMessage());
                    break;

                case 2:
                    Log::error('Store client failed from client event : ' . $e->getMessage());
                    break;

                case 3:
                    Log::error('Store client event failed : ' . $e->getMessage());
                    break;
            }

            Log::error('Store a new client event failed : ' . $e->getMessage());
            // return $e->getMessage();
            // exit;
            return Redirect::to('program/event/create')->withError('Failed to create client event');
        }

        return Redirect::to('program/event')->withSuccess('Client event successfully created');
    }

    public function show(Request $request)
    {
        $clientevent_id = $request->route('event');
        $clientEvent = $this->clientEventRepository->getClientEventById($clientevent_id);

        $curriculums = $this->curriculumRepository->getAllCurriculums();
        $clients = $this->clientRepository->getAllClients();
        $events = $this->eventRepository->getAllEvents();
        $leads = $this->leadRepository->getAllLead();
        $ext_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        $schools = $this->schoolRepository->getAllSchools();
        $partners = $this->corporateRepository->getAllCorporate();

        return view('pages.program.client-event.form')->with(
            [
                'clientEvent' => $clientEvent,
                'curriculums' => $curriculums,
                'clients' => $clients,
                'events' => $events,
                'ext_edufair' => $ext_edufair,
                'kols' => $kols,
                'leads' => $leads,
                'schools' => $schools,
                'partners' => $partners,
            ]
        );
    }

    public function edit(Request $request)
    {
        $clientevent_id = $request->route('event');

        $clientEvent = $this->clientEventRepository->getClientEventById($clientevent_id);

        $curriculums = $this->curriculumRepository->getAllCurriculums();
        $clients = $this->clientRepository->getAllClients();
        $events = $this->eventRepository->getAllEvents();
        $leads = $this->leadRepository->getAllLead();
        $ext_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        $schools = $this->schoolRepository->getAllSchools();
        $partners = $this->corporateRepository->getAllCorporate();

        return view('pages.program.client-event.form')->with(
            [
                'edit' => true,
                'clientEvent' => $clientEvent,
                'curriculums' => $curriculums,
                'clients' => $clients,
                'events' => $events,
                'ext_edufair' => $ext_edufair,
                'kols' => $kols,
                'leads' => $leads,
                'schools' => $schools,
                'partners' => $partners,
            ]
        );
    }

    public function update(StoreClientEventRequest $request)
    {
        $clientevent_id = $request->route('event');

        $clientEvent = $request->only([
            'client_id',
            'event_id',
            'lead_id',
            'eduf_id',
            'partner_id',
            'status',
            'joined_date'
        ]);

        # set lead_id based on lead_id & kol_lead_id
        # when lead_id is kol
        # then put kol_lead_id to lead_id
        # otherwise
        # when lead_id is not kol 
        # then lead_id is lead_id
        if ($request->lead_id == "kol") { # lead = kol

            unset($clientEvent['lead_id']);
            $clientEvent['eduf_id'] = null;
            $clientEvent['partner_id'] = null;
            $clientEvent['lead_id'] = $request->kol_lead_id;
        } else if ($request->lead_id == 'LS010') { # lead = All-In Partners
            $clientEvents['eduf_id'] = null;
        } else if ($request->lead_id != 'LS017' && $request->lead_id != 'kol') {

            $clientEvent['eduf_id'] = null;
            $clientEvent['partner_id'] = null;
        } else if ($request->lead_id == 'LS017' && $request->lead_id != 'kol') { # lead = Edufair

            $clientEvent['partner_id'] = null;
        }

        DB::beginTransaction();
        try {

            $this->clientEventRepository->updateClientEvent($clientevent_id, $clientEvent);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update client event failed : ' . $e->getMessage());

            return Redirect::to('program/event/' . $clientevent_id)->withError('Failed to update client event');
        }

        return Redirect::to('program/event')->withSuccess('Client event successfully updated');
    }

    public function destroy(Request $request)
    {
        $clientevent_id = $request->route('event');

        DB::beginTransaction();
        try {

            $this->clientEventRepository->deleteClientEvent($clientevent_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete client event failed : ' . $e->getMessage());

            return Redirect::to('program/event/' . $clientevent_id)->withError('Failed to delete client event');
        }

        return Redirect::to('program/event')->withSuccess('Client event successfully deleted');
    }

    public function import(StoreImportExcelRequest $request)
    {

        $file = $request->file('file');

        $import = new ClientEventImport;
        $import->import($file);

        return back()->withSuccess('Client event successfully imported');
    }

    public function createFormEmbed(Request $request)
    {        
        if ($request->get('event_name') == null) {
            abort(404);
        }
        $leads = $this->leadRepository->getLeadForFormEmbedEvent();
        $schools = $this->schoolRepository->getAllSchools();

        $requested_event_name = str_replace('&quot;', '"', $request->event_name);
        if (!$event = $this->eventRepository->getEventByName(urldecode($requested_event_name)))
            abort(404);

        $tags = $this->tagRepository->getAllTags();

        return view('form-embed.form-events')->with(
            [
                'leads' => $leads,
                'schools' => $schools,
                'event' => $event,
                'tags' => $tags->where('name', '!=', 'Other'),
            ]
        );
    }


    public function storeFormEmbed(Request $request)
    {
        $clientEvent = [];
        $existClientParent = $existClientStudent = $existClientTeacher = ['isExist' => false];
        $childDetails = [];
        $schoolId = null;
        $childId = null;

        $requested_event_name = urldecode(str_replace('&quot;', '"', $request->event_name));

        $event = $this->eventRepository->getEventByName($requested_event_name);

        # attend status
        # 1 is attending
        # 0 is join the event 
        $attend_status = $request->status == "attend" ? 1 : 0;

        # type of event
        # if the event helds offline then the value will be "offline"
        # otherwise it will be null
        # the difference is if event type is "offline" then system will send barcode via mails
        $event_type = $request->event_type;

        // Check existing client by phone number and email
        $choosen_role = $request->role;
        DB::beginTransaction();
        try {

            # when sch_id is "add-new" 
            // $choosen_school = $request->school;
            if (!$this->schoolRepository->getSchoolById($request->school)) {

                $last_id = School::max('sch_id');
                $school_id_without_label = $last_id ? $this->remove_primarykey_label($last_id, 4) : '0000';
                $school_id_with_label = 'SCH-' . $this->add_digit($school_id_without_label + 1, 4);

                $school = [
                    'sch_id' => $school_id_with_label,
                    'sch_name' => $request->school,
                ];

                # create a new school
                $school = $this->schoolRepository->createSchool($school);
                $schoolId = $school->sch_id;
            }

            # store a new client
            $createdClient = $this->createClient($choosen_role, $schoolId, $request);

            # initialize variable for client event
            $clientEventDetails = [
                'client_id' => $createdClient['clientId'],
                'event_id' => $event->event_id,
                'lead_id' => $request->leadsource,
                'status' => $attend_status,
                'joined_date' => Carbon::now(),
            ];

            # store a new client event
            if ($clientEvent = $this->clientEventRepository->createClientEvent($clientEventDetails)) {

                $storedClientEventId = $clientEvent->clientevent_id;

                # when client event has successfully stored
                # continue to send an email
                # but if the event type is "offline"

                if (isset($event_type) && $event_type == "offline") {

                    $this->sendMailQrCode($storedClientEventId, $requested_event_name, ['clientDetails' => ['mail' => $createdClient['clientMail'], 'name' => $createdClient['clientName']]]);

                }

            }

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store client event embed failed : ' . $e->getMessage() . ' on line '. $e->getLine());

            return Redirect::to('form/event?event_name='.$request->get('event_name'))->withErrors('Something went wrong. Please try again or contact our administrator.');
        }


        return Redirect::to('form/thanks');
    }

    private function createClient($choosen_role, $schoolId, $request)
    {
        # store children information if it is parent that filled the form
        if ($choosen_role == 'parent') {
            $childDetails = [
                'name' => $request->fullname[1],
                'email' => null,
                'phone' => null,
                'register_as' => 'parent',
            ];

            $phoneStudent = $childDetails['phone'];

            $existClientStudent = $this->checkExistingClient($phoneStudent, $childDetails['email']);

            if (!$existClientStudent['isExist']) {
                $fullname = explode(' ', $childDetails['name']);
                $limit = count($fullname);

                $firstname = $lastname = null;
                if ($limit > 1) {
                    $lastname = $fullname[$limit - 1];
                    unset($fullname[$limit - 1]);
                    $firstname = implode(" ", $fullname);
                } else {
                    $firstname = implode(" ", $fullname);
                }

                $st_grade = 12 - ($request->graduation_year - date('Y'));


                $clientDetails = [
                    'first_name' => $firstname,
                    'last_name' => $lastname,
                    'mail' => $childDetails['email'],
                    'phone' => $childDetails['phone'],
                    'register_as' => $childDetails['register_as'],
                    'st_grade' => $st_grade,
                    'graduation_year' => $request->graduation_year,
                    'lead' => $request->leadsource,
                    'sch_id' => $schoolId != null ? $schoolId : $request->school,
                ];
                

                $newClientStudent = $this->clientRepository->createClient('Student', $clientDetails);
            }

            $clientStudentId = $existClientStudent['isExist'] ? $existClientStudent['id'] : $newClientStudent->id;
            
        }

        # initialize raw variable
        $newClientDetails = [
            'name' => $request->fullname[0],
            'email' => $request->email[0],
            'phone' => $request->fullnumber[0],
            'register_as' => $choosen_role,
        ];

        # check if the client exist in our databases
        $existingClient = $this->checkExistingClient($newClientDetails['phone'], $newClientDetails['email']);
        if (!$existingClient['isExist']) {

            # get firstname & lastname from fullname
            $fullname = explode(' ', $newClientDetails['name']);
            $fullname_words = count($fullname);

            $firstname = $lastname = null;
            if ($fullname_words > 1) {
                $lastname = $fullname[$fullname_words - 1];
                unset($fullname[$fullname_words - 1]);
                $firstname = implode(" ", $fullname);
            } else {
                $firstname = implode(" ", $fullname);
            }

            # all client basic info (whatever their role is)
            $clientDetails = [
                'first_name' => $firstname,
                'last_name' => $lastname,
                'mail' => $newClientDetails['email'],
                'phone' => $newClientDetails['phone'],
                'lead' => $request->leadsource,
                'register_as' => $choosen_role,
            ];

            # additional info that should be stored when role is student
            if ($choosen_role == 'student') {

                $additionalInfo = [
                    'st_grade' => 12 - ($request->graduation_year - date('Y')),
                    'graduation_year' => $request->graduation_year,
                    'lead' => $request->leadsource,
                    'sch_id' => $schoolId != null ? $schoolId : $request->school,
                ];

                $clientDetails = array_merge($clientDetails, $additionalInfo);
            }

            # additional info that should be stored when role is teacher
            if ($choosen_role == 'teacher/counsellor') {

                $additionalInfo = [
                    'sch_id' => $schoolId != null ? $schoolId : $request->school,
                ];

                $clientDetails = array_merge($clientDetails, $additionalInfo);
            }
            
            # stored a new client information
            $newClient = $this->clientRepository->createClient($this->getRoleName($choosen_role), $clientDetails);

            
        }

        # store the destination country if registrant either parent or student
        if ($choosen_role == 'parent' || $choosen_role == 'student') {

            $clientStudentId = isset($clientStudentId) ? $clientStudentId : $newClient->id;

            $this->clientRepository->createDestinationCountry($clientStudentId, $request->destination_country);
        }

        $response = [
            'clientId' => $existingClient['isExist'] ? $existingClient['id'] : $newClient->id,
            'clientName' => $newClientDetails['name'],
            'clientMail' => $newClientDetails['email']
        ];

        # attaching parent and student
        if ($choosen_role == 'parent') {

            $this->clientRepository->createManyClientRelation($response['clientId'], $clientStudentId);

        }

        return $response;
    }

    private function getRoleName($roleName)
    {
        switch ($roleName) {

            case "teacher/counsellor":
                $role = "Teacher/Counselor";
                break;

            default:
                $role = ucwords($roleName);

        }

        return $role;
    }

    public function sendMailQrCode($clientEventId, $eventName, $client, $update = false)
    {
        $subject = 'Welcome to the '.$eventName.'!';
        $mail_resources = 'mail-template.event-registration-success';

        $recipientDetails = $client['clientDetails'];
        
        $url = route('link-event-attend', [
                        'event_slug' => urlencode($eventName),
                        'clientevent' => $clientEventId
                    ]);

        $clientEvent = $this->clientEventRepository->getClientEventById($clientEventId);
        
        $event = [
            'eventDate_start' => date('l, d M Y', strtotime($clientEvent->event->event_startdate)),
            'eventDate_end' => date('l, d M Y', strtotime($clientEvent->event->event_enddate)),
            'eventTime_start' => date('H.i', strtotime($clientEvent->event->event_startdate)),
            'eventTime_end' => date('H.i', strtotime($clientEvent->event->event_enddate)),
            'eventLocation' => $clientEvent->event->event_location
        ];

        try {
            Mail::send($mail_resources, ['url' => $url, 'client' => $client['clientDetails'], 'event' => $event], function ($message) use ($subject, $recipientDetails) {
                $message->to($recipientDetails['mail'], $recipientDetails['name'])
                    ->subject($subject);
            });
            $sent_mail = 1;
            
        } catch (Exception $e) {
            
            $sent_mail = 0;
            Log::error('Failed send email to participant of Event '.$eventName.' | error : '.$e->getMessage().' | Line '.$e->getLine());

        }

        # if update is true 
        # meaning that this function being called from scheduler
        # that updating the client event log mail, so the system no longer have to create the client event log mail
        if ($update === true) {
            return true;    
        }

        $logDetails = [
            'clientevent_id' => $clientEventId,
            'sent_status' => $sent_mail
        ];

        return $this->clientEventLogMailRepository->createClientEventLogMail($logDetails);
    }

    public function handlerScanQrCodeForAttend(Request $request)
    {
        # get request
        $event = $request->event;
        $clientEventId = $request->clientevent;

        $clientEvent = $this->clientEventRepository->getClientEventById($clientEventId);
        $clientFullname = $clientEvent->client->full_name;
        $eventName = $clientEvent->event->event_title;

        DB::beginTransaction();
        try {

            $this->clientEventRepository->updateClientEvent($clientEventId, ['status' => 1]);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to process the attending request from '.$clientFullname.' ( '.$eventName.' )');
            return view('form-embed.response.error');

        }

        return view('form-embed.response.success');
    }

    public function updateAttendance($id, $status) {
        $clientEvent = $this->clientEventRepository->getClientEventById($id);

        DB::beginTransaction();
        try {
            $clientEvent['status'] = $status;
            $clientEvent->save();
            $data = [
                'name' => $clientEvent->client->full_name,
                'status' => $clientEvent->status
            ];
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Update attendance client event failed : ' . $e->getMessage());
        }
        return response()->json($data);

    }
}
