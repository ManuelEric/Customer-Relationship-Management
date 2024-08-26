<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreClientEventRequest;
use App\Http\Requests\StoreImportExcelRequest;
use App\Http\Requests\StoreClientEventEmbedRequest;
use App\Http\Requests\StoreFormEventEmbedRequest;
use App\Http\Traits\CalculateGradeTrait;
use App\Http\Traits\CheckExistingClient;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\MailingEventOfflineTrait;
use App\Http\Traits\SplitNameTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Http\Traits\SyncClientTrait;
use App\Imports\CheckListInvitation;
use App\Imports\ClientEventImport;
use App\Imports\InvitationMailInfoImport;
use App\Imports\InvitationMailVIPImport;
use App\Imports\InvitationMailVVIPImport;
use App\Imports\QuestCompleterMailImport;
use App\Imports\ThankMailImport;
use App\Imports\ReminderEventImport;
use App\Imports\ReminderReferralImport;
use App\Imports\ReminderRegisrationImport;
use App\Imports\ReminderRegistrationImport;
use App\Interfaces\ClientEventLogMailRepositoryInterface;
use App\Interfaces\CurriculumRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\EdufLeadRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\SchoolCurriculumRepositoryInterface;
use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\TagRepositoryInterface;
use App\Jobs\Client\ProcessDefineCategory;
use App\Jobs\RawClient\ProcessVerifyClient;
use App\Jobs\RawClient\ProcessVerifyClientParent;
use App\Models\Client;
use App\Models\School;
use App\Models\UserClientAdditionalInfo;
use App\Models\ViewClientRefCode;
use AshAllenDesign\ShortURL\Models\ShortURL;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class ClientEventController extends Controller
{
    use CalculateGradeTrait;
    use SplitNameTrait;
    use CheckExistingClient;
    use CreateCustomPrimaryKeyTrait;
    use StandardizePhoneNumberTrait;
    use MailingEventOfflineTrait;
    use LoggingTrait;
    use SyncClientTrait;
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
    protected ClientProgramRepositoryInterface $clientProgramRepository;


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
        TagRepositoryInterface $tagRepository, 
        ClientProgramRepositoryInterface $clientProgramRepository, 
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
        $this->clientProgramRepository = $clientProgramRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $event_name = $request->get('event_name');
            $audience = $request->get('audience');
            $school_name = $request->get('school_name');
            $graduation_year = $request->get('graduation_year');
            $conversion_lead = $request->get('conversion_lead');
            $attendance = $request->get('attendance');
            $registration = $request->get('registration');
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            $filter = [
                'event_name' => $event_name,
                'audience' => $audience,
                'school_name' => $school_name,
                'graduation_year' => $graduation_year,
                'conversion_lead' => $conversion_lead,
                'attendance' => $attendance,
                'registration' => $registration,
                'start_date' => $start_date,
                'end_date' => $end_date
            ];

            return $this->clientEventRepository->getAllClientEventDataTables($filter);
        }

        $events = $this->eventRepository->getAllEvents();
        $schools = $this->schoolRepository->getAllSchools();
        // $conversion_leads = $this->clientProgramRepository->getAllConversionLeadOnClientProgram();
        $main_leads = $this->leadRepository->getAllMainLead();
        $main_leads = $main_leads->map(function ($item) {
            return [
                'lead_id' => $item->lead_id,
                'conversion_lead' => $item->main_lead
            ];
        });
        $sub_leads = $this->leadRepository->getAllKOLlead();
        $sub_leads = $sub_leads->map(function ($item) {
            return [
                'lead_id' => $item->lead_id,
                'conversion_lead' => $item->sub_lead
            ];
        });
        $conversion_leads = $main_leads->merge($sub_leads);

        return view('pages.program.client-event.index')->with(
            [
                'events' => $events,
                'schools' => $schools,
                'conversion_leads' => $conversion_leads
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
            'joined_date',
            'notes'
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
            if (!$storedClientEvent = $this->clientEventRepository->createClientEvent($clientEvents))
                throw new Exception('Failed to store new client event', 3);


            # Case 4
            # Generate ticket ID when the event is offline or hybrid
            # Updated generate ticket ID for all events 

            // if (in_array($storedClientEvent->event->type, ['offline', 'hybrid'])) {

                $ticketID = app('App\Http\Controllers\Api\v1\ExtClientController')->generateTicketID();
                $this->clientEventRepository->updateClientEvent($storedClientEvent->clientevent_id, ['ticket_id' => $ticketID]);
            // }
            

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

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Client Event', Auth::user()->first_name . ' ' . Auth::user()->last_name, $clientEvents);

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
        $oldClientEvent = $this->clientEventRepository->getClientEventById($clientevent_id);

        $clientEvent = $request->only([
            'client_id',
            'event_id',
            'lead_id',
            'eduf_id',
            'partner_id',
            'status',
            'notes',
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

            $newClientEvent = $this->clientEventRepository->updateClientEvent($clientevent_id, $clientEvent);

            # Generate ticket ID when the event is offline or hybrid
            # Updated generate ticket ID for all events 
            
            // if (in_array($newClientEvent->event->type, ['offline', 'hybrid'])) {

                $ticketID = app('App\Http\Controllers\Api\v1\ExtClientController')->generateTicketID();
                $this->clientEventRepository->updateClientEvent($newClientEvent->clientevent_id, ['ticket_id' => $ticketID]);
            // }
            
            //! it supposed to be a function to remove the ticket ID when the event was changed into online (yet to be developed)

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update client event failed : ' . $e->getMessage());

            return Redirect::to('program/event/' . $clientevent_id)->withError('Failed to update client event');
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Client Event', Auth::user()->first_name . ' ' . Auth::user()->last_name, $clientEvent, $oldClientEvent);

        return Redirect::to('program/event')->withSuccess('Client event successfully updated');
    }

    public function destroy(Request $request)
    {
        $clientevent_id = $request->route('event');
        $clientEvent = $this->clientEventRepository->getClientEventById($clientevent_id);

        DB::beginTransaction();
        try {

            $this->clientEventRepository->deleteClientEvent($clientevent_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete client event failed : ' . $e->getMessage());

            return Redirect::to('program/event/' . $clientevent_id)->withError('Failed to delete client event');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Client Program', Auth::user()->first_name . ' ' . Auth::user()->last_name, $clientEvent);

        return Redirect::to('program/event')->withSuccess('Client event successfully deleted');
    }

    public function import(StoreImportExcelRequest $request)
    {
        Cache::put('auth', Auth::user());
        Cache::put('import_id', Carbon::now()->timestamp . '-import-client-event');

        $file = $request->file('file');

        (new ClientEventImport())->queue($file)->allOnQueue('imports-client-event');

        // try {
        // Excel::queueImport(new ClientEventImport($this->clientRepository, Auth::user()), $file);
        // $import = new ClientEventImport($this->clientRepository);
        // $import->import($file);


        // } catch (Exception $e) {

        //     return back()->withError('Something went wrong while processing the data. Please try again or contact the administrator.');

        // }

        return back()->withSuccess('Import client events start progress');
    }

    public function mailing(StoreImportExcelRequest $request)
    {

        $type = $request->route('type');
        $file = $request->file('file');

        $import = '';
        switch ($type) {
            case 'check_list_invitation':
                $import = new CheckListInvitation;
                break;

            case 'VVIP':
                $import = new InvitationMailVVIPImport;
                break;

            case 'VIP':
                $import = new InvitationMailVIPImport;
                break;

            case 'reminder_registration':
                $import = new ReminderRegistrationImport;
                break;

            case 'reminder_referral':
                $import = new ReminderReferralImport;
                break;

            case 'quest_completer':
                $import = new QuestCompleterMailImport;
                break;

            case 'invitation_info':
                $import = new InvitationMailInfoImport;
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
        $requested_event_name = str_replace('&amp;', '&', $requested_event_name);
        if (!$event = $this->eventRepository->getEventByName(urldecode($requested_event_name)))
            abort(404);

        $tags = $this->tagRepository->getAllTags();

        return view('form-embed.form-events')->with(
            [
                'leads' => $leads,
                'schools' => $schools,
                'event' => $event,
                'tags' => $tags,
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
        $requested_event_name = str_replace('&amp;', '&', $requested_event_name);

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

        # prevent data be stored if
        # choosen role is student and parent name is null or -
        $parentNameIsNull = false;
        if ($choosen_role == "student" && ($request->fullname[1] == "-" || $request->fullname[1] === NULL))
            $parentNameIsNull = true;

        # scholarship eligibility
        $scholarship_eligibility = $request->scholarship_eligibility;

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
                'scholarship' => $scholarship_eligibility,
                'joined_date' => Carbon::now(),
            ];

            $newly_registrant = $createdClient['clientId'];

            if ($choosen_role == "parent")
                $clientEventDetails['child_id'] = $createdClient['childId'];

            if ($choosen_role == "student" && !$parentNameIsNull)
                $clientEventDetails['parent_id'] = $createdClient['parentId'];

            # if registration_type is exist
            # add the registration_type into the clientEventDetails that will be stored
            if (isset($registration_type))
                $clientEventDetails['registration_type'] = $registration_type;

            # get data created user
            $newly_registrant_user = $this->clientRepository->getClientById($newly_registrant);

            # check if client has already join the event
            if ($this->clientEventRepository->getClientEventByClientIdAndEventId($createdClient['clientId'], $event->event_id))
                return Redirect::to('form/already-join?role=' . $choosen_role . '&name=' . $newly_registrant_user->full_name);

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
            Log::error('Store client event embed failed : ' . $e->getMessage() . ' on line ' . $e->getLine());

            return Redirect::to('form/event?event_name=' . $request->get('event_name'))->withErrors('Something went wrong. Please try again or contact our administrator.');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Embed', 'Client Event', 'Guest', $clientEvent);


        # if they regist on the spot then should return view success
        if (isset($registration_type) && $registration_type == "ots")
            return Redirect::to('form/registration/success?role=' . $choosen_role . '&name=' . $newly_registrant_user->full_name);


        return Redirect::to('form/thanks');
    }

    private function createClient($choosen_role, $schoolId, $request)
    {

        $relation = count(array_filter($request->fullname)); # this is the parameter that has maximum length of the requested client (ex: for parent and student the value would be 2 but teacher the value would be 1
        $loop = 0;
        $parentNameIsNull = false;

        # prevent data be stored if
        # choosen role is student and parent name is null or -
        if ($choosen_role == "student" && ($request->fullname[1] == "-" || $request->fullname[1] === NULL)) {

            $relation = 1;
            $parentNameIsNull = true;
        }

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
                $splitName = $this->split($newClientDetails[$loop]['name']);
                $firstname = $splitName['first_name'];
                $lastname = $splitName['last_name'];

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
                        'st_grade' => $this->getGradeByGraduationYear($request->graduation_year),
                        'graduation_year' => $request->graduation_year,
                        'lead' => $request->leadsource,
                        'sch_id' => $schoolId != null ? $schoolId : $request->school,
                    ];

                    $clientDetails = array_merge($clientDetails, $additionalInfo);
                } else if ($choosen_role == 'student' && $loop == 0) {

                    $additionalInfo = [
                        'st_grade' => $this->getGradeByGraduationYear($request->graduation_year),
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
        if ($choosen_role == 'parent') {
            $parentId = $newClientDetails[0]['id'] = $clientArrayIds[0];
            $childId = $clientArrayIds[1];
            # trigger to verifying parent
            ProcessVerifyClientParent::dispatch([$parentId])->onQueue('verifying-client-parent');
            # trigger to verifying children
            ProcessVerifyClient::dispatch([$childId])->onQueue('verifying-client');
            # trigger define category client
            ProcessDefineCategory::dispatch([$childId])->onQueue('define-category-client');

            
        } else if ($choosen_role == 'student') {
            # to prevent empty parent name being stored into database
            if (!$parentNameIsNull)
                $parentId = $clientArrayIds[1];
                # trigger to verifying parent
                ProcessVerifyClientParent::dispatch([$parentId])->onQueue('verifying-client-parent');

            $childId = $newClientDetails[0]['id'] = $clientArrayIds[0];
            # trigger to verifying children
            ProcessVerifyClient::dispatch([$childId])->onQueue('verifying-client');
            # trigger define category client
            ProcessDefineCategory::dispatch([$childId])->onQueue('define-category-client');


        } else {
            $teacherId = $newClientDetails[0]['id'] = $clientArrayIds[0];
            # trigger to verifying teacher
            ProcessVerifyClient::dispatch([$teacherId])->onQueue('verifying-client-teacher');

        }

        # store the destination country if registrant either parent or student
        if ($choosen_role == 'parent' || $choosen_role == 'student') {

            $client = $this->clientRepository->getClientById($childId);
            isset($request->category) ? $client->interestPrograms()->syncWithoutDetaching(['prog_id' => $request->category]) : null;
            isset($request->destination_country) ? $this->clientRepository->createDestinationCountry($childId, $request->destination_country) : null;
        }

        $response = [
            'clientId' => $newClientDetails[0]['id'],
            'clientName' => $newClientDetails[0]['name'],
            'clientMail' => $newClientDetails[0]['email'],
        ];

        if ($choosen_role == "parent")
            $response['childId'] = $childId;

        if ($choosen_role == "student" && !$parentNameIsNull)
            $response['parentId'] = $parentId;

        # attaching parent and student
        if (($choosen_role == 'parent' || $choosen_role == 'student') && !$parentNameIsNull) {

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

    public function successPage(Request $request)
    {
        $choosen_role = $request->get('role');
        $name = $request->get('name');

        return view('form-embed.response.success')->with([
            'choosen_role' => $choosen_role,
            'name' => $name
        ]);
    }

    public function alreadyJoinPage(Request $request)
    {
        $choosen_role = $request->get('role');
        $name = $request->get('name');

        return view('form-embed.response.already-join')->with([
            'choosen_role' => $choosen_role,
            'name' => $name
        ]);
    }

    public function sendMailQrCode($clientEventId, $eventName, $client, $update = false)
    {
        # initiate variables
        
        $clientEvent = $this->clientEventRepository->getClientEventById($clientEventId);

        $clientEvent->event_id == 'EVT-0008' ? $eventName = "STEM+ Wonderlab" : null;

        $subject = 'Welcome to the ' . $eventName . '!';
        // $mail_resources = 'mail-template.event-registration-success';
        $mail_resources = 'mail-template.thanks-email-reg';

        $recipientDetails = $client['clientDetails'];

        $url = route('program.event.qr-page', [
            'event_slug' => urlencode($eventName),
            'clientevent' => $clientEventId
        ]);

        # for eduall 2024 the system has changed
        # because of that, the url has changed as well into the api route
        $url = url("/api/v1/client-event/CE/{$clientEventId}");


        $event = [
            
            'eventDate_start' => date('l, d M Y', strtotime($clientEvent->event->event_startdate)),
            'eventDate_end' => date('M d, Y', strtotime($clientEvent->event->event_enddate)),
            'eventTime_start' => date('g A', strtotime($clientEvent->event->event_startdate)),
            'eventTime_end' => date('H:i', strtotime($clientEvent->event->event_enddate)),
            'eventLocation' => $clientEvent->event->event_location,
        ];

        try {
            Mail::send($mail_resources, 
                    [
                        'qr' => $url, 
                        'client' => $client['clientDetails'], 
                        'event' => $event
                    ],
                    function ($message) use ($subject, $recipientDetails) {
                        $message->to($recipientDetails['mail'], $recipientDetails['name'])
                            ->subject($subject);
                    }
            );
            $sent_mail = 1;
        } catch (Exception $e) {

            $sent_mail = 0;
            Log::error('Failed send email qr code to participant of Event ' . $eventName . ' | error : ' . $e->getMessage() . ' on file '.$e->getFile().' | Line ' . $e->getLine());
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
        $subject = 'Welcome to the ' . $eventName . '!';
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
            Log::error('Failed send email thanks to participant of Event ' . $eventName . ' | error : ' . $e->getMessage() . ' | Line ' . $e->getLine());
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
            Log::error('Failed send email claim to participant of Event ' . $eventName . ' | error : ' . $e->getMessage() . ' | Line ' . $e->getLine());
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
        $screening_type = $request->route('screening_type');
        switch ($screening_type) {

            case "qr":
                $clientEventId = $request->route('identifier');
                $clientEvent = $this->clientEventRepository->getClientEventById($clientEventId);
                $client = $clientEvent->client;
                break;

            case "phone":
                $phoneNumber = $request->route('identifier');
                if (!$client = $this->clientRepository->getClientByPhoneNumber($phoneNumber))
                    return view('stem-wonderlab.scan-qrcode.error')->with(['message' => "We're sorry, but your data was not found"]);

                # for now
                # event id is hardcoded for STEM+ Wonderlab
                $clientEvent = $client->clientEvent()->where('event_id', "EVT-0008")->first();
                if (!$clientEvent)
                    return view('stem-wonderlab.scan-qrcode.error')->with(['message' => 'We\'re sorry, but you haven\'t joined our event.']);

                break;
        }

        $clientFullname = $client->full_name;
        $eventName = $clientEvent->event->event_title;

        $secondaryClientInfo = $responseAdditionalInfo = array();
        switch ($client->roles->first()->role_name) { # this is a choosen role

            case "Parent":
                $secondaryClientInfo = $clientEvent->children;
                $responseAdditionalInfo = [
                    'sch_id' => isset($secondaryClientInfo->school) ? $secondaryClientInfo->school->sch_id : null,
                    'school' => isset($secondaryClientInfo->school->sch_name) ? $secondaryClientInfo->school->sch_name : null,
                    'graduation_year' => isset($secondaryClientInfo->graduation_year) ? $secondaryClientInfo->graduation_year : null,
                    'abr_country' => isset($secondaryClientInfo->destinationCountries) ? $secondaryClientInfo->destinationCountries()->pluck('tbl_tag.id')->toArray() : null
                ];
                break;

            case "Student":
                $secondaryClientInfo = $clientEvent->parent;
                $responseAdditionalInfo = [
                    'sch_id' => isset($client->school) ? $client->school->sch_id : null,
                    'school' => isset($client->school->sch_name) ? $client->school->sch_name : null,
                    'graduation_year' => isset($client->graduation_year) ? $client->graduation_year : null,
                    'abr_country' => isset($client->destinationCountries) ? $client->destinationCountries()->pluck('tbl_tag.id')->toArray() : null
                ];
                break;

            case "Teacher/Counselor":
                $secondaryClientInfo = $clientEvent->client;
                $responseAdditionalInfo = [
                    'sch_id' => isset($client->school) ? $client->school->sch_id : null,
                    'school' => isset($client->school->sch_name) ? $client->school->sch_name : null,
                ];
                break;
        }

        // if (!isset($secondaryClientInfo))
        //     return view('stem-wonderlab.scan-qrcode.error')->with(['message' => 'Something went wrong. <br>Please contact our staff to help you scan the QR.']);

        $tags = $this->tagRepository->getAllTags();

        $response = [
            'leadsource' => $clientEvent->lead_id,
            'client' => $client,
            'client_event' => $clientEvent,
            'secondary_client' => [
                'personal_info' => $secondaryClientInfo,
            ] + $responseAdditionalInfo,
            'schools' => $this->schoolRepository->getAllSchools(),
            'tags' => $tags->where('name', '!=', 'Other')
        ];

        return view('stem-wonderlab.scan-qrcode.client-detail')->with($response);
    }

    public function handlerScanQrCodeForAttend(StoreFormEventEmbedRequest $request)
    {
        # nambahin validasi number of attend tidak boleh 0
        $request->validate([
            'how_many_people_attended' => 'required|min:1'
        ], $request->all(), ['how_many_people_attended' => 'number of party field']);

        # get request
        $event = $request->event; # not used for now because there is no event slug
        $clientEventId = $request->route('clientevent');

        $clientEvent = $this->clientEventRepository->getClientEventById($clientEventId);
        $client = $clientEvent->client;

        $clientFullname = $client->full_name;

        $eventName = $clientEvent->event->event_title;

        # initiate variables in order to
        # update student information details
        $isParent = $isStudent = $isTeacher = false;

        $schoolId = $request->school; # can be id when they pick existing school / string when they write a new one

        # when sch_id is "add-new"
        if (!$this->schoolRepository->getSchoolById($schoolId) && $schoolId !== NULL) {

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

        switch ($client->register_as) { # this is a choosen role

            case "parent":
                $childId = $clientEvent->children->id;
                $isParent = true;

                $splitParentName = $this->split($request->fullname[0]);
                $splitChildName = $this->split($request->fullname[1]);

                $newParentInformation = [
                    'first_name' => $splitParentName['first_name'],
                    'last_name' => $splitParentName['last_name'],
                    'mail' => $request->email[0],
                    'phone' => $request->fullnumber[0]
                ];

                $newChildInformation = [
                    'first_name' => $splitChildName['first_name'],
                    'last_name' => $splitChildName['last_name'],
                    'mail' => $request->email[1],
                    'phone' => $request->fullnumber[1],
                    'sch_id' => $schoolId,
                    'graduation_year' => $request->graduation_year
                ];

                $child = $this->clientRepository->getClientById($childId);
                $parent = $this->clientRepository->getClientById($client->id);

                break;

            case "student":
                $childId = $client->id;
                $isStudent = true;

                $splitChildName = $this->split($request->fullname[0]);
                $splitParentName = $this->split($request->fullname[1]);

                $newChildInformation = [
                    'first_name' => $splitChildName['first_name'],
                    'last_name' => $splitChildName['last_name'],
                    'mail' => $request->email[0],
                    'phone' => $request->fullnumber[0],
                    'sch_id' => $schoolId,
                    'graduation_year' => $request->graduation_year
                ];

                $newParentInformation = [
                    'first_name' => $splitParentName['first_name'],
                    'last_name' => $splitParentName['last_name'],
                    'mail' => $request->email[1],
                    'phone' => $request->fullnumber[1]
                ];

                $child = $this->clientRepository->getClientById($client->id);
                if (isset($clientEvent->parent)) {
                    $parent = $this->clientRepository->getClientById($clientEvent->parent->id);
                } else {
                    $parent = null;
                }

                break;

            case "teacher/counsellor":
                $isTeacher = true;

                $splitTeacherName = $this->split($request->fullname[0]);

                $newTeacherInformation = [
                    'first_name' => $splitTeacherName['first_name'],
                    'last_name' => $splitTeacherName['last_name'],
                    'mail' => $request->email[0],
                    'phone' => $request->fullnumber[0]
                ];

                $teacher = $this->clientRepository->getClientById($client->id);

                break;
        }

        # initiate variable in order to update client event
        $newDetails = [
            'number_of_attend' => $request->how_many_people_attended,
            'status' => 1 # they came to the event
        ];

        DB::beginTransaction();
        try {

            # when the client parent or student 
            # they need to complete the email or phone for index 1 which is secondary data
            if ($isParent || $isStudent) {

                # update master client information
                if ($parent == null) {
                    $parent = $this->clientRepository->createClient('Parent', $newParentInformation);
                    $this->clientRepository->createManyClientRelation($parent->id, [$child->id]);
                    $this->clientEventRepository->updateClientEvent($clientEventId, ['parent_id' => $parent->id]);
                } else {
                    $parent->update($newParentInformation);
                }
                $child->update($newChildInformation);

                # update childs school information
                $destination_countries = $request->destination_country;
                $child->destinationCountries()->sync($destination_countries);
            }

            if ($isTeacher)
                $teacher->update($newTeacherInformation);

            # update client event
            $this->clientEventRepository->updateClientEvent($clientEventId, $newDetails);

            // if ($clientEvent->status == 0)
            //     $this->sendMailClaim($clientEventId, $eventName, ['clientDetails' => ['mail' => $client->mail, 'name' => $client->full_name]], $update = false);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to process the attending request from ' . $clientFullname . ' ( ' . $eventName . ' ) | error : ' . $e->getMessage() . ' ' . $e->getLine());
            return view('form-embed.response.error');
        }

        return Redirect::to('form/registration/success?role=' . $client->register_as . '&name=' . $request->fullname[0]);
    }

    public function updateAttendance($id, $status)
    {
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

    public function updateNumberOfParty(int $id, int $number_of_party)
    {
        $clientEvent = $this->clientEventRepository->getClientEventById($id);

        DB::beginTransaction();
        try {
            $clientEvent->number_of_attend = $number_of_party;
            $clientEvent->save();
            DB::commit();

            $success = true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Update number of party client event failed : ' . $e->getMessage());
            $success = false;
        }

        return response()->json([
            'success' => $success,
            'message' => 'Information updated'
        ]);
    }

    public function registerExpress(Request $request)
    {

        $clientId = $request->route('client');
        $client = $this->clientRepository->getClientById($clientId);
        $eventId = $request->route('event');
        $notes = $request->route('notes');
        $indexChild = $request->route('index_child');

        $dataRegister = $this->register($client->mail, $eventId, $notes, $indexChild);

        if ($dataRegister['success'] && !$dataRegister['already_join']) {
            # store Success
            # create log success
            $this->logSuccess('store', 'Register Express', 'Client Event', 'Guest', ['client_id' => $clientId, 'event_id' => $eventId, 'notes' => $notes]);

            return Redirect::to('form/thanks');
        } else if ($dataRegister['success'] && $dataRegister['already_join']) {
            return Redirect::to('form/already-join?role=' . $client->register_as . '&name=' . $client->full_name);
        }
    }

    public function referralPage(Request $request)
    {
        $refcode = $request->route('refcode');
        $event_slug = $request->route('event_slug');
        $noteEncrypt = $request->route('notes');
        switch ($noteEncrypt) {
            case 'VIP':
            case 'WxSFs0LGh': # Mean VIP
                $notes = 'VIP';
                break;

            case 'VVIP':
            case 'BtSF0x1hK': # Mean VVIP
                $notes = 'VVIP';
                break;
        }
        $event_slug = $request->route('event_slug');

        $shortUrl = ShortURL::where('url_key', $refcode)->first();

        $slug = str_replace('-', ' ', $event_slug);
        if (!$event = $this->eventRepository->getEventByName($slug))
            abort(404);

        $link = 'https://makerspace.all-inedu.com';
        $query = '?ref=' . $refcode;

        return view('stem-wonderlab.referral-link.index')->with([
            'link' => $link . $query,
            'event' => $event,
            'notes' => $notes
        ]);
    }

    # for API User
    public function trackReferralURL(Request $request)
    {
        $refcode = $request->route('referral');
        $shortURL = ShortURL::findByKey($refcode);
        $shortURL->trackingEnabled();

        return response()->json(
            [
                'success' => true,
                'data' => $shortURL
            ]
        );
    }
    # end

    public function qrPage(Request $request)
    {
        $event_slug = $request->route('event_slug');
        $clientEventId = $request->route('clientevent');

        $url =  route('link-event-attend', [
            // 'event_slug' => $event_slug,
            'clientevent' => $clientEventId
        ]);

        return view('stem-wonderlab.scan-qrcode.qrcode')->with([
            'url' => $url
        ]);
    }
}
