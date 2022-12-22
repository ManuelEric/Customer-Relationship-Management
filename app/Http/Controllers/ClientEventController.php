<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreClientEventRequest;
use App\Interfaces\CurriculumRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\EdufLeadRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Models\v1\School;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;



// TODO: Use request, repo, interface
// TODO: Ambil data untuk form
// TODO: Store, destroy data
// TODO: Buat form untuk create client
// TODO: Store client

class ClientEventController extends Controller
{

    protected CurriculumRepositoryInterface $curriculumRepository;
    protected ClientRepositoryInterface $clientRepository;
    protected ClientEventRepositoryInterface $clientEventRepository;
    protected EdufLeadRepositoryInterface $edufLeadRepository;
    protected EventRepositoryInterface $eventRepository;
    protected LeadRepositoryInterface $leadRepository;
    protected SchoolRepositoryInterface $schoolRepository;

    public function __construct(
        CurriculumRepositoryInterface $curriculumRepository,
        ClientRepositoryInterface $clientRepository, 
        ClientEventRepositoryInterface $clientEventRepository,
        EdufLeadRepositoryInterface $edufLeadRepository, 
        EventRepositoryInterface $eventRepository,
        LeadRepositoryInterface $leadRepository,
        SchoolRepositoryInterface $schoolRepository,
        )
    {
        $this->curriculumRepository = $curriculumRepository;
        $this->clientRepository = $clientRepository;
        $this->clientEventRepository = $clientEventRepository;
        $this->edufLeadRepository = $edufLeadRepository;
        $this->eventRepository = $eventRepository;
        $this->leadRepository = $leadRepository;
        $this->schoolRepository = $schoolRepository;
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
        $curriculums = $this->curriculumRepository->getAllCurriculum();
        $clients = $this->clientRepository->getAllClients();
        $events = $this->eventRepository->getAllEvents();
        $leads = $this->leadRepository->getAllLead();
        $ext_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        $schools = $this->schoolRepository->getAllSchools();

        return view('pages.program.client-event.form')->with(
            [
                'curriculums' => $curriculums,
                'clients' => $clients,
                'events' => $events,
                'ext_edufair' => $ext_edufair,
                'kols' => $kols,
                'leads' => $leads,
                'schools' => $schools,
            ]
        );
    }

    public function store(StoreClientEventRequest $request)
    {

        $clientEvents = $request->only([
            'client_id',
            'event_id',
            'lead_id',
            'eduf_id',
            'status',
            'joined_date'
        ]);

        if($request->existing_client == 0){
            
            if($request->status_client == 'Parent'){
                $clientDetails = $request->only([
                    'first_name',
                    'last_name',
                    'mail',
                    'phone',
                    'dob',
                    'state',
                    'lead_id',
                    'eduf_id',
                    'kol_lead_id',
                    'event_id',
                ]);
            }

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
                'kol_lead_id',
                'event_id',
                'graduation_year',
                'st_levelinterest',
                'st_password'
            ]);

        }

        # set lead_id based on lead_id & kol_lead_id
        # when lead_id is kol
        # then put kol_lead_id to lead_id
        # otherwise
        # when lead_id is not kol 
        # then lead_id is lead_id
        if ($request->lead_id == "kol") {

            unset($clientEvents['lead_id']);
            $clientEvents['lead_id'] = $request->kol_lead_id;
        }

      

        DB::beginTransaction();
        
        try {
            
            // unset($clientEvents['existing_client']);
            // return $clientEvents;
            // exit;

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

            if($request->existing_client == 0){

                switch ($request->status_client) {
                    case 'Mentee':
                        $clientCreated = $this->clientRepository->createClient('Mentee', $clientDetails);
                        break;
                    
                    case 'Parent':
                        $clientCreated = $this->clientRepository->createClient('Parent', $clientDetails);
                        break;
                        
                    case 'Teacher/Counselor':
                        $clientCreated = $this->clientRepository->createClient('Teacher/Counselor', $clientDetails);
                        break;
                }
                
                $clientEvents['client_id'] = $clientCreated->id;
                
            }
            
            // dd($clientEvents);

            # insert into client event
            $this->clientEventRepository->createClientEvent($clientEvents);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store client event failed : ' . $e->getMessage());
            return $e->getMessage();
            exit;
            return Redirect::to('program/event/create')->withError('Failed to create client event');
        }
        
        return Redirect::to('program/event')->withSuccess('Client event successfully created');

    }

    public function show(Request $request)
    {
        $clientevent_id = $request->route('event');
        $clientEvent = $this->clientEventRepository->getClientEventById($clientevent_id);

        $curriculums = $this->curriculumRepository->getAllCurriculum();
        $clients = $this->clientRepository->getAllClients();
        $events = $this->eventRepository->getAllEvents();
        $leads = $this->leadRepository->getAllLead();
        $ext_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        $schools = $this->schoolRepository->getAllSchools();

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
            ]
        );
    }

    public function edit(Request $request)
    {
        $clientevent_id = $request->route('event');

        $clientEvent = $this->clientEventRepository->getClientEventById($clientevent_id);

        $curriculums = $this->curriculumRepository->getAllCurriculum();
        $clients = $this->clientRepository->getAllClients();
        $events = $this->eventRepository->getAllEvents();
        $leads = $this->leadRepository->getAllLead();
        $ext_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        $schools = $this->schoolRepository->getAllSchools();

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
            'status',
            'joined_date'
        ]);

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
}
