<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreClientEventRequest;
use App\Http\Requests\StoreImportExcelRequest;
use App\Http\Requests\StoreClientEventEmbedRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Imports\ClientEventImport;
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

use App\Models\School;
use App\Models\UserClientAdditionalInfo;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class ClientEventController extends Controller
{
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
        RoleRepositoryInterface $roleRepository
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
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            return $this->clientEventRepository->getAllClientEventDataTables();
        }

        return view('pages.program.client-event.index');
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

    public function createFormEmbed()
    {
        $leads = $this->leadRepository->getLeadForFormEmbedEvent();
        $schools = $this->schoolRepository->getAllSchools();

        return view('form-embed.form-event')->with(
            [
                'leads' => $leads,
                'schools' => $schools,
            ]
        );
    }


    public function storeFormEmbed(StoreClientEventEmbedRequest $request)
    {
        $clientEvent = [];
        $existClientParent = ['isExist' => false];
        $existClientStudent = ['isExist' => false];

        $phoneParent = $this->setPhoneNumber($request->phone);
        $phoneStudent = $this->setPhoneNumber($request->phone_child);

        // Check existing client by phone number and email
        if ($request->user_type == 'Parent') {
            $existClientParent = $this->checkExistingClient($phoneParent, $request->email);
        }

        $existClientStudent = $this->checkExistingClient($phoneStudent, $request->email_child);

        DB::beginTransaction();
        try {

            # when sch_id is "add-new" 
            $choosen_school = $request->school;
            if ($choosen_school == "add-new") {

                $last_id = School::max('sch_id');
                $school_id_without_label = $last_id ? $this->remove_primarykey_label($last_id, 4) : '0000';
                $school_id_with_label = 'SCH-' . $this->add_digit($school_id_without_label + 1, 4);

                $school = [
                    'sch_id' => $school_id_with_label,
                    'sch_name' => $request->other_school
                ];

                # create a new school
                $school = $this->schoolRepository->createSchool($school);
            }

            if (!$existClientParent['isExist'] && $request->user_type == 'Parent') {
                $fullname = explode(' ', $request->name);
                $limit = count($fullname);

                $firstname = $lastname = null;
                if ($limit > 1) {
                    $lastname = $fullname[$limit - 1];
                    unset($fullname[$limit - 1]);
                    $firstname = implode(" ", $fullname);
                } else {
                    $firstname = implode(" ", $fullname);
                }

                $clientDetails = [
                    'first_name' => $firstname,
                    'last_name' => $lastname,
                    'mail' => $request->email,
                    'phone' => $phoneParent,
                    // 'graduation_year' => $request->grade,
                    'lead' => $request->leadsource,
                ];

                $newClientParent = $this->clientRepository->createClient($request->user_type, $clientDetails);
            }

            if (!$existClientStudent['isExist']) {
                $fullname = explode(' ', $request->child_name);
                $limit = count($fullname);

                $firstname = $lastname = null;
                if ($limit > 1) {
                    $lastname = $fullname[$limit - 1];
                    unset($fullname[$limit - 1]);
                    $firstname = implode(" ", $fullname);
                } else {
                    $firstname = implode(" ", $fullname);
                }

                $st_grade = 12 - ($request->grade - date('Y'));


                $clientDetails = [
                    'first_name' => $firstname,
                    'last_name' => $lastname,
                    'mail' => $request->email_child,
                    'phone' => $phoneStudent,
                    'st_grade' => $st_grade,
                    'graduation_year' => $request->grade,
                    'lead' => $request->leadsource,
                    'sch_id' => $request->school == 'add-new' ? $school->sch_id : $request->school,
                ];

                $newClientStudent = $this->clientRepository->createClient('Student', $clientDetails);
            }

            if ($request->user_typ == 'Parent') {
                if ($existClientParent['isExist'] && $existClientStudent['isExist']) {
                    $this->clientRepository->createManyClientRelation($existClientParent['id'], $existClientStudent['id']);
                } else if (!$existClientParent['isExist'] && $existClientStudent['isExist']) {
                    $this->clientRepository->createManyClientRelation($newClientParent->id, $existClientStudent['id']);
                } else if ($existClientParent['isExist'] && !$existClientParent['isExist']) {
                    $this->clientRepository->createManyClientRelation($existClientParent['id'], $newClientStudent->id);
                }
            }

            $clientEvent = [
                'client_id' => $existClientStudent['isExist'] ? $existClientStudent['id'] : $newClientStudent->id,
                'event_id' => $request->event,
                'lead_id' => $request->leadsource,
                'status' => 1,
                'joined_date' => Carbon::now(),
            ];

            $this->clientEventRepository->createClientEvent($clientEvent);
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store client event embed failed : ' . $e->getMessage());

            return Redirect::to('form/event/')->withError('Something went wrong!');
        }


        return 'success';
    }

    private function checkExistingClient($phone, $email)
    {
        $existClient = [];

        // Check existing client by phone number and email
        $clientExistPhone = $this->clientRepository->checkExistingByPhoneNumber($phone);
        $clientExistEmail = $this->clientRepository->checkExistingByEmail($email);

        if ($clientExistPhone && $clientExistEmail) {
            $existClient['isExist'] = true;
            $existClient['id'] = $clientExistPhone['id'];
        } else if ($clientExistPhone && !$clientExistEmail) {
            $existClient['isExist'] = true;
            $existClient['id'] = $clientExistPhone['id'];

            // Add email to client addtional info
            $additionalInfo = [
                'client_id' => $clientExistPhone['id'],
                'category' => 'mail',
                'value' => $email,
            ];
            UserClientAdditionalInfo::create($additionalInfo);
        } else if (!$clientExistPhone && $clientExistEmail) {
            $existClient['isExist'] = true;
            $existClient['id'] = $clientExistEmail['id'];

            // Add email to client addtional info
            $additionalInfo = [
                'client_id' => $clientExistEmail['id'],
                'category' => 'phone',
                'value' => $phone,
            ];
            UserClientAdditionalInfo::create($additionalInfo);
        } else {
            $existClient['isExist'] = false;
        }

        return $existClient;
    }
}
