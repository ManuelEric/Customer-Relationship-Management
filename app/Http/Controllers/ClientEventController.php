<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreClientEventRequest;
use App\Http\Requests\StoreImportExcelRequest;
use App\Http\Requests\StoreClientEventEmbedRequest;
use App\Http\Requests\StoreFormEventEmbedRequest;
use App\Http\Traits\CheckExistingClient;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\MailingEventOfflineTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Imports\ClientEventImport;
use App\Imports\InvitaionMailImport;
use App\Imports\InvitationMailImport;
use App\Imports\ThankMailImport;
use App\Imports\ReminderEventImport;
use App\Imports\ReminderReferralImport;
use App\Imports\ReminderRegisrationImport;
use App\Imports\ReminderRegistrationImport;
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
use AshAllenDesign\ShortURL\Models\ShortURL;
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
    use MailingEventOfflineTrait;
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

    public function mailing(StoreImportExcelRequest $request)
    {

        $type = $request->route('type');
        $file = $request->file('file');

        $import = '';
        switch ($type) {
            case 'VVIP':
                $import = new ThankMailImport;
                break;

            case 'VIP':
                $import = new InvitationMailImport;
                break;

            case 'reminder_registration':
                $import = new ReminderRegistrationImport;
                break;

            case 'reminder_referral':
                $import = new ReminderReferralImport;
                break;
            
        }
        $import->import($file);

        return back()->withSuccess('Successfully send mail');
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


    public function storeFormEmbed(StoreFormEventEmbedRequest $request)
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
        $attend_status = $request->attend_status == "attend" ? 1 : 0;

        # type of event
        # if the event helds offline then the value will be "offline"
        # otherwise it will be null
        # the difference is if event type is "offline" then system will send barcode via mails
        $event_type = $request->event_type;

        # number of attend
        # only for the people that register on the spot
        $number_of_attend = isset($request->attend) ? $request->attend : 1;

        # notes
        # for stem+ wonderlab is for VIP & VVIP
        $notes = $request->client_type;

        # referral code
        $referral_code = $request->referral;

        # registration type 
        # will be "ots" or "pr"
        $registration_type = $request->status;

        // Check existing client by phone number and email
        $choosen_role = $request->role;

        DB::beginTransaction();
        try {

            # when sch_id is "add-new" 
            // $choosen_school = $request->school;
            if (!$this->schoolRepository->getSchoolById($request->school) && $request->school !== NULL) {

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
                'lead_id' => isset($referral_code) ? "LS005" : $request->leadsource, # if using referral code then lead source will be "referral" which is "LS005"
                'number_of_attend' => $number_of_attend,
                'notes' => $notes,
                'referral_code' => $referral_code,
                'status' => $attend_status,
                'joined_date' => Carbon::now(),
            ];

            if ($choosen_role == "parent")
                $clientEventDetails['child_id'] = $createdClient['childId'];

            if ($choosen_role == "student")
                $clientEventDetails['parent_id'] = $createdClient['parentId'];

            # if registration_type is exist 
            # add the registration_type into the clientEventDetails that will be stored
            if (isset($registration_type))
                $clientEventDetails['registration_type'] = $registration_type;

            # store a new client event
            if ($clientEvent = $this->clientEventRepository->createClientEvent($clientEventDetails)) {

                $storedClientEventId = $clientEvent->clientevent_id;

                # when client event has successfully stored
                # continue to send an email
                # but if the event type is "offline"

                if (isset($event_type) && $event_type == "offline") {

                    $this->sendMailQrCode($storedClientEventId, $requested_event_name, ['clientDetails' => ['mail' => $createdClient['clientMail'], 'name' => $createdClient['clientName']]]);

                } else {
                    
                    # send thanks mail
                    // $this->sendMailThanks($storedClientEventId, $requested_event_name, ['clientDetails' => ['mail' => $createdClient['clientMail'], 'name' => $createdClient['clientName']]]);

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

        $relation = count(array_filter($request->fullname)); # this is the parameter that has maximum length of the requested client (ex: for parent and student the value would be 2 but teacher the value would be 1
        $loop = 0;

        while ($loop < $relation) {

            # initialize raw variable
            # why newClientDetails[$loop] should be array?
            # because to make easier for system to differentiate between parents and students like for example if user registered as a parent 
            # then index 0 is for parent data and index 1 is for children data, otherwise 
            $newClientDetails[$loop] = [
                'name' => $request->fullname[$loop],
                'email' => $request->email[$loop],
                'phone' => $request->fullnumber[$loop],
                'register_as' => $choosen_role,
            ];

            # check if the client exist in our databases
            $existingClient = $this->checkExistingClient($newClientDetails[$loop]['phone'], $newClientDetails[$loop]['email']);
            if (!$existingClient['isExist']) {

                # get firstname & lastname from fullname
                $fullname = explode(' ', $newClientDetails[$loop]['name']);
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
                    'mail' => $newClientDetails[$loop]['email'],
                    'phone' => $newClientDetails[$loop]['phone'],
                    'lead_id' => "LS001", # hardcode for lead website
                    'register_as' => $choosen_role,
                ];

                # additional info that should be stored when role is student and parent
                # because all of the additional info are for the student
                if ($choosen_role == 'parent' && $loop == 1) {

                    $additionalInfo = [
                        'st_grade' => 12 - ($request->graduation_year - date('Y')),
                        'graduation_year' => $request->graduation_year,
                        'lead' => $request->leadsource,
                        'sch_id' => $schoolId != null ? $schoolId : $request->school,
                    ];

                    $clientDetails = array_merge($clientDetails, $additionalInfo);
                    
                
                } else if ($choosen_role == 'student' && $loop == 0) {

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

                switch ($choosen_role) {

                    case "parent":
                        $role = $loop == 0 ? 'parent' : 'student';
                        break;

                    case "student":
                        $role = $loop == 1 ? 'parent' : 'student';
                        break;

                    case "teacher/counsellor":
                        $role = $choosen_role;
                        break;
                }
                
                # stored a new client information
                $newClient[$loop] = $this->clientRepository->createClient($this->getRoleName($role), $clientDetails);
                
            }

            $clientArrayIds[$loop] = $existingClient['isExist'] ? $existingClient['id'] : $newClient[$loop]->id;

            $loop++;
        }

        # the indexes
        # the idea is assuming the index 0 as the main user that will be added into tbl_client_event
        if ($choosen_role == 'parent') 
        {
            $parentId = $newClientDetails[0]['id'] = $clientArrayIds[0];
            $childId = $clientArrayIds[1];
        } 
        else if ($choosen_role == 'student')
        {
            $parentId = $clientArrayIds[1];
            $childId = $newClientDetails[0]['id'] = $clientArrayIds[0];
        } 
        else 
        {
            $teacherId = $newClientDetails[0]['id'] = $clientArrayIds[0];
        }

        # store the destination country if registrant either parent or student
        if ($choosen_role == 'parent' || $choosen_role == 'student') {

            $this->clientRepository->createDestinationCountry($childId, $request->destination_country);
        }

        $response = [
            'clientId' => $newClientDetails[0]['id'],
            'clientName' => $newClientDetails[0]['name'],
            'clientMail' => $newClientDetails[0]['email'],
        ];

        if ($choosen_role == "parent")
            $response['childId'] = $childId;

        if ($choosen_role == "student")
            $response['parentId'] = $parentId;

        # attaching parent and student
        if ($choosen_role == 'parent' || $choosen_role == 'student') {

            $this->clientRepository->createManyClientRelation($parentId, $childId);

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
        // $mail_resources = 'mail-template.event-registration-success';
        $mail_resources = 'mail-template.thanks-email-reg';

        $recipientDetails = $client['clientDetails'];
        
        $url = route('program.event.qr-page', [
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
            Mail::send($mail_resources, ['qr_page' => $url, 'client' => $client['clientDetails'], 'event' => $event], function ($message) use ($subject, $recipientDetails) {
                $message->to($recipientDetails['mail'], $recipientDetails['name'])
                    ->subject($subject);
            });
            $sent_mail = 1;
            
        } catch (Exception $e) {
            
            $sent_mail = 0;
            Log::error('Failed send email qr code to participant of Event '.$eventName.' | error : '.$e->getMessage().' | Line '.$e->getLine());

        }

        # if update is true 
        # meaning that this function being called from scheduler
        # that updating the client event log mail, so the system no longer have to create the client event log mail
        if ($update === true) {
            return true;    
        }

        $logDetails = [
            'clientevent_id' => $clientEventId,
            'sent_status' => $sent_mail,
            'category' => 'qrcode-mail'
        ];

        return $this->clientEventLogMailRepository->createClientEventLogMail($logDetails);
    }

    public function sendMailThanks($clientEventId, $eventName, $client, $update = false)
    {
        $subject = 'Welcome to the '.$eventName.'!';
        $mail_resources = 'mail-template.thanks-email';

        $recipientDetails = $client['clientDetails'];

        $clientEvent = $this->clientEventRepository->getClientEventById($clientEventId);
        
        $event = [
            'eventName' => $eventName,
            'eventDate' => date('l, d M Y', strtotime($clientEvent->event->event_startdate)),
            'eventLocation' => $clientEvent->event->event_location
        ];

        try {
            Mail::send($mail_resources, ['client' => $client['clientDetails'], 'event' => $event], function ($message) use ($subject, $recipientDetails) {
                $message->to($recipientDetails['mail'], $recipientDetails['name'])
                    ->subject($subject);
            });
            $sent_mail = 1;
            
        } catch (Exception $e) {
            
            $sent_mail = 0;
            Log::error('Failed send email thanks to participant of Event '.$eventName.' | error : '.$e->getMessage().' | Line '.$e->getLine());

        }

        # if update is true 
        # meaning that this function being called from scheduler
        # that updating the client event log mail, so the system no longer have to create the client event log mail
        if ($update === true) {
            return true;    
        }

        $logDetails = [
            'clientevent_id' => $clientEventId,
            'sent_status' => $sent_mail,
            'category' => 'thanks-mail'
        ];

        return $this->clientEventLogMailRepository->createClientEventLogMail($logDetails);
    }

    public function sendMailClaim($clientEventId, $eventName, $client, $update = false)
    {
        $subject = 'Claim Lorem ipsum dolor sit amet';
        $mail_resources = 'mail-template.claim-email';

        $recipientDetails = $client['clientDetails'];

        $clientEvent = $this->clientEventRepository->getClientEventById($clientEventId);
        

        $event = [
            'eventName' => $eventName,
            'eventDate' => date('l, d M Y', strtotime($clientEvent->event->event_startdate)),
            'eventLocation' => $clientEvent->event->event_location
        ];

        try {
            Mail::send($mail_resources, ['client' => $client['clientDetails'], 'event' => $event], function ($message) use ($subject, $recipientDetails) {
                $message->to($recipientDetails['mail'], $recipientDetails['name'])
                    ->subject($subject);
            });
            $sent_mail = 1;
            
        } catch (Exception $e) {
            
            $sent_mail = 0;
            Log::error('Failed send email claim to participant of Event '.$eventName.' | error : '.$e->getMessage().' | Line '.$e->getLine());

        }

        # if update is true 
        # meaning that this function being called from scheduler
        # that updating the client event log mail, so the system no longer have to create the client event log mail
        if ($update === true) {
            return true;    
        }

        $logDetails = [
            'clientevent_id' => $clientEventId,
            'sent_status' => $sent_mail,
            'category' => 'thanks-mail'
        ];

        return $this->clientEventLogMailRepository->createClientEventLogMail($logDetails);
    }

    public function previewClientInformation(Request $request) 
    {
        $clientEventId = $request->clientevent;

        $clientEvent = $this->clientEventRepository->getClientEventById($clientEventId);
        $client = $clientEvent->client;
        $clientFullname = $client->full_name;
        $eventName = $clientEvent->event->event_title;

        $secondaryClientInfo = $responseAdditionalInfo = array();
        switch ($client->register_as) { # this is a choosen role

            case "parent":
                $secondaryClientInfo = $clientEvent->children;
                $responseAdditionalInfo = [
                    'school' => $secondaryClientInfo->school->sch_name,
                    'graduation_year' => $secondaryClientInfo->graduation_year,
                    'abr_country' => str_replace(',', ', ', $secondaryClientInfo->abr_country)
                ];
                break;

            case "student":
                $secondaryClientInfo = $clientEvent->parent;
                $responseAdditionalInfo = [
                    'school' => $client->school->sch_name,
                    'graduation_year' => $client->graduation_year,
                    'abr_country' => str_replace(',', ', ', $client->abr_country)
                ];
                break;

        }

        if (!isset($secondaryClientInfo))
            abort(404);

        $response = [
            'client' => $client,
            'client_event' => $clientEvent,
            'secondary_client' => [
                'personal_info' => $secondaryClientInfo,
            ] + $responseAdditionalInfo
        ];

        return view('scan-qrcode.client-detail')->with($response);
    }

    public function handlerScanQrCodeForAttend(Request $request)
    {
        # get request
        $event = $request->event; # not used for now becuase there is no event slug
        $clientEventId = $request->clientevent;

        $clientEvent = $this->clientEventRepository->getClientEventById($clientEventId);
        $client = $clientEvent->client;

        $clientFullname = $client->full_name;
        $eventName = $clientEvent->event->event_title;

        # initiate variables in order to
        # update student information details
        switch ($client->register_as) { # this is a choosen role

            case "parent":
                $childId = $clientEvent->children->id;
                break;

            case "student":
                $childId = $client->id;
                break;

        }

        # initiate variable in order to update client event
        $newDetails = [
            'number_of_attend' => $request->how_many_people_attended,
            'status' => 1 # they came to the event
        ];

        DB::beginTransaction();
        try {

            # update student information details
            $this->clientRepository->updateClient($childId, [
                'mail' => $request->secondary_mail,
                'phone' => $request->secondary_phone
            ]);

            # update client event
            $this->clientEventRepository->updateClientEvent($clientEventId, $newDetails);
            $this->sendMailClaim($clientEventId, $eventName, ['clientDetails' => ['mail' => $client->mail, 'name' => $client->full_name]], $update = false);
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

    public function registerExpress(Request $request)
    {
       
        $clientId = $request->route('client');
        $client = $this->clientRepository->getClientById($clientId);
        $eventId = $request->route('event');

        $dataRegister = $this->register($client->mail, $eventId, 'VIP'); 

        if($dataRegister['success'] && !$dataRegister['already_join']){
            return Redirect::to('form/thanks');
        }else if($dataRegister['success'] && $dataRegister['already_join']){
            return Redirect::to('form/already-join');
        }
        

    }

    public function referralPage(Request $request)
    {
        $refcode = $request->route('refcode');
        $event_slug = $request->route('event_slug');

        $shortUrl = ShortURL::where('url_key', $refcode)->first();
        $event = $this->eventRepository->getEventByName(urldecode($event_slug));

        if(isset($shortUrl)){
            $link = $shortUrl->default_short_url; 
        }else{
            #insert short url to database
            $link = $this->createShortUrl(url('form/event?event_name='.$event_slug.'&form_type=cta&event_type=offline&ref='. $refcode), $refcode);
        }

        return view('referral-link.index')->with([
            'link' => $link,
            'event' => $event
        ]);
    }

    public function qrPage(Request $request)
    {
        $event_slug = $request->route('event_slug');
        $clientEventId = $request->route('clientevent');

        $url =  route('link-event-attend', [
            // 'event_slug' => $event_slug,
            'clientevent' => $clientEventId
        ]);

        return view('scan-qrcode.qrcode')->with([
            'url' => $url
        ]);
    }
}
