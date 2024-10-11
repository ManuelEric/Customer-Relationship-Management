<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientProgramRequest;
use App\Http\Requests\StoreFormProgramEmbedRequest;
use App\Http\Traits\CheckExistingClient;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\EdufLeadRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\ReasonRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\ClientLeadTrackingRepositoryInterface;
use App\Interfaces\ClientProgramLogMailRepositoryInterface;
use App\Interfaces\TagRepositoryInterface;
use App\Jobs\Client\ProcessDefineCategory;
use App\Models\Bundling;
use App\Models\BundlingDetail;
use App\Models\Program;
use App\Models\School;
use App\Models\UserClient;
use App\Models\ViewClientProgram;
use App\Services\Program\ClientProgramService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ClientProgramController extends Controller
{
    use CheckExistingClient;
    use LoggingTrait;
    private ClientRepositoryInterface $clientRepository;
    private ProgramRepositoryInterface $programRepository;
    private LeadRepositoryInterface $leadRepository;
    private EventRepositoryInterface $eventRepository;
    private EdufLeadRepositoryInterface $edufLeadRepository;
    private UserRepositoryInterface $userRepository;
    private CorporateRepositoryInterface $corporateRepository;
    private ReasonRepositoryInterface $reasonRepository;
    private ClientProgramRepositoryInterface $clientProgramRepository;
    private ClientEventRepositoryInterface $clientEventRepository;
    private SchoolRepositoryInterface $schoolRepository;
    private TagRepositoryInterface $tagRepository;
    private ClientProgramLogMailRepositoryInterface $clientProgramLogMailRepository;
    private ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository;
    private ClientProgramService $clientProgramService;
    private $admission_prog_list;
    private $tutoring_prog_list;
    private $satact_prog_list;

    use CreateCustomPrimaryKeyTrait;

    public function __construct(ClientRepositoryInterface $clientRepository, ProgramRepositoryInterface $programRepository, LeadRepositoryInterface $leadRepository, EventRepositoryInterface $eventRepository, EdufLeadRepositoryInterface $edufLeadRepository, UserRepositoryInterface $userRepository, CorporateRepositoryInterface $corporateRepository, ReasonRepositoryInterface $reasonRepository, ClientProgramRepositoryInterface $clientProgramRepository, ClientEventRepositoryInterface $clientEventRepository, SchoolRepositoryInterface $schoolRepository, TagRepositoryInterface $tagRepository, ClientProgramLogMailRepositoryInterface $clientProgramLogMailRepository, ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository, ClientProgramService $clientProgramService)
    {
        $this->clientRepository = $clientRepository;
        $this->programRepository = $programRepository;
        $this->leadRepository = $leadRepository;
        $this->eventRepository = $eventRepository;
        $this->edufLeadRepository = $edufLeadRepository;
        $this->userRepository = $userRepository;
        $this->corporateRepository = $corporateRepository;
        $this->reasonRepository = $reasonRepository;
        $this->clientProgramRepository = $clientProgramRepository;
        $this->clientEventRepository = $clientEventRepository;
        $this->schoolRepository = $schoolRepository;
        $this->tagRepository = $tagRepository;
        $this->clientProgramLogMailRepository = $clientProgramLogMailRepository;
        $this->clientLeadTrackingRepository = $clientLeadTrackingRepository;
        $this->clientProgramService = $clientProgramService;

        $this->admission_prog_list = Program::whereHas('main_prog', function ($query) {
            $query->where('prog_name', 'Admissions Mentoring');
        })->orWhereHas('sub_prog', function ($query) {
            $query->where('sub_prog_name', 'Admissions Mentoring');
        })->pluck('prog_id')->toArray();

        $this->tutoring_prog_list = Program::whereHas('sub_prog', function ($query) {
            $query->where('sub_prog_name', 'like', '%Tutoring%');
        })->pluck('prog_id')->toArray();

        $this->satact_prog_list = Program::whereHas('sub_prog', function ($query) {
            $query->where('sub_prog_name', 'like', '%SAT%')->orWhere('sub_prog_name', 'like', '%ACT%');
        })->pluck('prog_id')->toArray();
    }

    public function index(Request $request)
    {

        $data = $status = $emplUUID = [];
        $status = $userId = $emplId = NULL;

        $data['clientId'] = NULL;
        $data['programName'] = !empty($request->get('program_name')) ? array_filter($request->get('program_name'), fn ($value) => !is_null($value)) ?? null : null;
        $data['mainProgram'] = !empty($request->get('main_program')) ? array_filter($request->get('main_program'), fn ($value) => !is_null($value)) ?? null : null;
        $data['schoolName'] = $request->get('school_name') ?? null;
        $data['leadId'] = $request->get('conversion_lead') ?? null;
        $data['grade'] = $request->get('grade') ?? null;
        
        if ($raw_program_status = $request->get('program_status')) {
            
            for ($i = 0; $i < count($raw_program_status); $i++) {
                $raw_status = Crypt::decrypt($raw_program_status[$i]);
                $status[] = $raw_status;
            }
            
        }

        $data['status'] = $status;

        if ($request->get('mentor_tutor')) {
            for ($i = 0; $i < count($request->get('mentor_tutor')); $i++) {
                $raw_userId = Crypt::decrypt($request->get('mentor_tutor')[$i]);
                $userId[] = $raw_userId;
            }
        }
        $data['userId'] = $userId;

        if ($request->get('pic')) {
            for ($i = 0; $i < count($request->get('pic')); $i++) {
                $emplUUID[] = $request->get('pic')[$i];
            }
        }
        $data['emplUUID'] = array_filter($emplUUID, fn ($value) => !is_null($value)) ?? null;;
        $data['startDate'] = $request->get('start_date') ?? null;
        $data['endDate'] = $request->get('end_date') ?? null;

        if ($request->ajax()) {
            return $this->clientProgramRepository->getAllClientProgramDataTables($data);
        }

        # advanced filter data
        $programs = $this->clientProgramRepository->getAllProgramOnClientProgram();
        $mainPrograms = $this->clientProgramRepository->getAllMainProgramOnClientProgram();
        $schools = $this->schoolRepository->getAllSchools();
        // $conversion_leads = $this->clientProgramRepository->getAllConversionLeadOnClientProgram();
        $mentor_tutors = $this->clientProgramRepository->getAllMentorTutorOnClientProgram();
        $pics = $this->clientProgramRepository->getAllPICOnClientProgram();
        $main_leads = $this->leadRepository->getAllMainLead();
        $main_leads = $main_leads->map(function ($item) {
            return [
                'lead_id' => $item->lead_id,
                'main_lead' => $item->main_lead
            ];
        });
        $sub_leads = $this->leadRepository->getAllKOLlead();
        $sub_leads = $sub_leads->map(function ($item) {
            return [
                'lead_id' => $item->lead_id,
                'main_lead' => $item->sub_lead
            ];
        });
        $conversion_leads = $main_leads->merge($sub_leads);

        return view('pages.program.client-program.index')->with(
            [
                'programs' => $programs,
                'mainPrograms' => $mainPrograms,
                'schools' => $schools,
                'conversion_leads' => $conversion_leads,
                'mentor_tutors' => $mentor_tutors,
                'pics' => $pics,
                'request' => $request,
                'status_decrypted' => $status,
                'mentor_tutor_decrypted' => $userId,
                'picUUID_arr' => $emplUUID,
            ]
        );
    }

    public function show(Request $request)
    {
        if ($request->route('student') !== null)
            $studentID = $request->route('student');
        elseif ($request->route('client') !== null)
            $studentID = $request->route('client');
        // $studentId = isset($request->route('student')) ? $request->route('student') : isset($request->route('client')) ? $request->route('client') : null;
        $clientProgramId = $request->route('program');

        $student = $this->clientRepository->getClientById($studentID);
        // $viewStudent = $this->clientRepository->getViewClientById($studentID);
        $clientProgram = $this->clientProgramRepository->getClientProgramById($clientProgramId);

        # programs
        $b2cprograms = $this->programRepository->getAllProgramByType("B2C");
        $b2bb2cprograms = $this->programRepository->getAllProgramByType("B2B/B2C");
        $programs = $b2cprograms->merge($b2bb2cprograms);

        # main leads
        $leads = $this->leadRepository->getAllMainLead();
        $clientEvents = $this->clientEventRepository->getAllClientEventByClientId($studentID);
        $external_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        $partners = $this->corporateRepository->getAllCorporate();
        $internalPic = $this->userRepository->getAllUsersByRole('Employee');

        $tutors = $this->userRepository->getAllUsersByRole('Tutor');
        $mentors = $this->userRepository->getAllUsersByRole('Mentor');

        $reasons = $this->reasonRepository->getReasonByType('Program');
        // $reasons = $this->reasonRepository->getAllReasons();

        return view('pages.program.client-program.form')->with(
            [
                'student' => $student,
                // 'viewStudent' => $viewStudent,
                'clientProgram' => $clientProgram,
                'programs' => $programs,
                'leads' => $leads,
                'clientEvents' => $clientEvents,
                'external_edufair' => $external_edufair,
                'kols' => $kols,
                'partners' => $partners,
                'internalPIC' => $internalPic,
                'tutors' => $tutors,
                'mentors' => $mentors,
                'reasons' => $reasons
            ]
        );
    }

    public function create(Request $request)
    {
        # identifier from interested program
        $p = $request->get('p') !== NULL ? $request->get('p') : null;

        $studentId = $request->route('student');
        $student = $this->clientRepository->getClientById($studentId);
        $viewStudent = $this->clientRepository->getViewClientById($studentId);

        # programs
        $b2cprograms = $this->programRepository->getAllProgramByType("B2C");
        $b2bb2cprograms = $this->programRepository->getAllProgramByType("B2B/B2C");
        $programs = $b2cprograms->merge($b2bb2cprograms);

        # main leads
        $leads = $this->leadRepository->getAllMainLead();
        $clientEvents = $this->clientEventRepository->getAllClientEventByClientId($studentId);
        $external_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        $partners = $this->corporateRepository->getAllCorporate();
        $internalPic = $this->userRepository->getAllUsersByDepartmentAndRole('Employee', 'Client Management');

        $tutors = $this->userRepository->getAllUsersByRole('Tutor');
        $mentors = $this->userRepository->getAllUsersByRole('Mentor');

        $reasons = $this->reasonRepository->getReasonByType('Program');
        // $reasons = $this->reasonRepository->getAllReasons();


        return view('pages.program.client-program.form')->with(
            [
                'p' => $p,
                'edit' => true,
                'student' => $student,
                'viewStudent' => $viewStudent,
                'programs' => $programs,
                'leads' => $leads,
                'clientEvents' => $clientEvents,
                'external_edufair' => $external_edufair,
                'kols' => $kols,
                'partners' => $partners,
                'internalPIC' => $internalPic,
                'tutors' => $tutors,
                'mentors' => $mentors,
                'reasons' => $reasons,
            ]
        );
    }

    public function store(StoreClientProgramRequest $request)
    {
        $file_path = null;
        // TODO: Perlu dicek function supervisor mentor

        # p means program from interested program
        $query = $request->queryP !== NULL ? "?p=" . $request->queryP : null;

        $studentId = $request->route('student');
        $student = $this->clientRepository->getClientById($studentId);
        if ($student->st_statusact != 1)
            return Redirect::back()->withError('The student is no longer active');

        $status = $request->status;

        $progId = $request->prog_id;

        # initialize
        $clientProgramDetails = $request->only([
            'lead_id',
            'prog_id',
            'clientevent_id',
            'eduf_lead_id',
            'kol_lead_id',
            'partner_id',
            'first_discuss_date',
            // 'meeting_notes',
            'status',
            'referral_code',
            'empl_id'
        ]);

        $clientProgramDetails = $this->clientProgramService->snSetAttributeLead($clientProgramDetails);
        
        DB::beginTransaction();
        try {

            $additional_attributes = $this->clientProgramService->snSetAdditionalAttributes($request, ['admission' => $this->admission_prog_list, 'tutoring' => $this->tutoring_prog_list, 'satact' => $this->satact_prog_list], $student, $clientProgramDetails);
            $clientProgramDetails = $additional_attributes['client_program_details'];
            $file_path = $additional_attributes['file_path'];

            $newClientProgram = $this->clientProgramRepository->createClientProgram(['client_id' => $studentId] + $clientProgramDetails);
       
            # add or remove role mentee
            # add role mentee when program is mentoring and status success then add role mentee
            # remove role mentee Only for method update
            $this->clientProgramService->snAddOrRemoveRoleMentee($progId, $studentId, $this->admission_prog_list, $clientProgramDetails['status']);

            $leadsTracking = $this->clientLeadTrackingRepository->getCurrentClientLead($studentId);

            //! perlu nunggu 1 menit dlu sampai ada client lead tracking status yg 1
            # update status client lead tracking
            if($leadsTracking->count() > 0){
                foreach($leadsTracking as $leadTracking){
                    $this->clientLeadTrackingRepository->updateClientLeadTrackingById($leadTracking->id, ['status' => 0]);
                }
            }

            # trigger to define category child
            ProcessDefineCategory::dispatch([$studentId])->onQueue('define-category-client');

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Create a student program failed : ' . $e->getMessage().' on '.$e->getFile().' line '.$e->getLine());


            # if failed storing the data into the database
            # remove the uploaded file from storage
            if (Storage::exists('public/uploaded_file/agreement/'.$file_path) && $file_path !== null) {
                Storage::delete('public/uploaded_file/agreement/'.$file_path);
            }

            return Redirect::back()->withError('Failed to store a new program.');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Client Program', Auth::user()->first_name . ' '. Auth::user()->last_name, $newClientProgram);

        return Redirect::to('client/student/' . $studentId)->withSuccess('A new program has been submitted for ' . $student->fullname);
    }

    public function edit(Request $request)
    {
        $studentId = $request->route('student');
        $clientProgramId = $request->route('program');

        $student = $this->clientRepository->getClientById($studentId);
        $viewStudent = $this->clientRepository->getViewClientById($studentId);
        $clientProgram = $this->clientProgramRepository->getClientProgramById($clientProgramId);

        # programs
        $b2cprograms = $this->programRepository->getAllProgramByType("B2C");
        $b2bb2cprograms = $this->programRepository->getAllProgramByType("B2B/B2C");
        $programs = $b2cprograms->merge($b2bb2cprograms);

        # main leads
        $leads = $this->leadRepository->getAllMainLead();
        $clientEvents = $this->clientEventRepository->getAllClientEventByClientId($studentId);
        $external_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        $partners = $this->corporateRepository->getAllCorporate();
        $internalPic = $this->userRepository->getAllUsersByDepartmentAndRole('Employee', 'Client Management');

        $tutors = $this->userRepository->getAllUsersByRole('Tutor');
        $mentors = $this->userRepository->getAllUsersByRole('Mentor');

        $reasons = $this->reasonRepository->getReasonByType('Program');
        // $reasons = $this->reasonRepository->getAllReasons();
      
        return view('pages.program.client-program.form')->with(
            [
                'edit' => true,
                'student' => $student,
                'viewStudent' => $viewStudent,
                'clientProgram' => $clientProgram,
                'programs' => $programs,
                'leads' => $leads,
                'clientEvents' => $clientEvents,
                'external_edufair' => $external_edufair,
                'kols' => $kols,
                'partners' => $partners,
                'internalPIC' => $internalPic,
                'tutors' => $tutors,
                'mentors' => $mentors,
                'reasons' => $reasons
            ]
        );
    }

    public function update(StoreClientProgramRequest $request)
    {
        $clientProgramId = $request->route('program');
        $studentId = $request->route('student');
        $student = $this->clientRepository->getClientById($studentId);
        $oldClientProgram = $this->clientProgramRepository->getClientProgramById($clientProgramId);

        $status = $request->status;
        $progId = $request->prog_id;

        # initialize
        $clientProgramDetails = $request->only([
            'lead_id',
            'prog_id',
            'clientevent_id',
            'eduf_lead_id',
            'kol_lead_id',
            'partner_id',
            'first_discuss_date',
            // 'meeting_notes',
            'status',
            'empl_id',
            'referral_code'
        ]);

        $clientProgramDetails = $this->clientProgramService->snSetAttributeLead($clientProgramDetails);

        $additional_attributes = $this->clientProgramService->snSetAdditionalAttributes($request, ['admission' => $this->admission_prog_list, 'tutoring' => $this->tutoring_prog_list, 'satact' => $this->satact_prog_list], $student, $clientProgramDetails, true);
        $clientProgramDetails = $additional_attributes['client_program_details'];
        $file_path = $additional_attributes['file_path'];

        DB::beginTransaction();
        try {

            $updatedClientProgram = $this->clientProgramRepository->updateClientProgram($clientProgramId, ['client_id' => $studentId] + $clientProgramDetails);
            $updatedClientProgramId = $updatedClientProgram->clientprog_id;
            # update the path into clientprogram table
            $this->clientProgramRepository->updateFewField($updatedClientProgramId, ['agreement' => $file_path]);
            
            $this->clientProgramService->snAddOrRemoveRoleMentee($progId, $studentId, $this->admission_prog_list, $status, true);

            $leadsTracking = $this->clientLeadTrackingRepository->getCurrentClientLead($studentId);

            //! perlu nunggu 1 menit dlu sampai ada client lead tracking status yg 1
            # update status client lead tracking
            if($leadsTracking->count() > 0){
                foreach($leadsTracking as $leadTracking){
                    $this->clientLeadTrackingRepository->updateClientLeadTrackingById($leadTracking->id, ['status' => 0]);
                }
            }

            # trigger to define category child
            ProcessDefineCategory::dispatch([$studentId])->onQueue('define-category-client');


            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update a student program failed : ' . $e->getMessage().' on line '.$e->getLine().' '.$e->getFile());

            return Redirect::back()->withError($e->getMessage());
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Client Program', Auth::user()->first_name . ' '. Auth::user()->last_name, ['client_id' => $studentId] + $clientProgramDetails, $oldClientProgram);

        return Redirect::to('client/student/' . $studentId . '/program/' . $clientProgramId)->withSuccess('A program has been updated for ' . $student->fullname);
    }

    public function destroy(Request $request)
    {
        $studentId = $request->route('student');
        $clientProgramId = $request->route('program');
        $clientProgram = $this->clientProgramRepository->getClientProgramById($clientProgramId);

        DB::beginTransaction();
        try {

            $this->clientProgramRepository->deleteClientProgram($clientProgramId);
            # trigger to define category child
            ProcessDefineCategory::dispatch([$studentId])->onQueue('define-category-client');

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete client program failed : ' . $e->getMessage());
            return Redirect::to('client/student/' . $studentId . '/program/' . $clientProgramId)->withError('Failed to delete client program');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Client Program', Auth::user()->first_name . ' '. Auth::user()->last_name, $clientProgram);

        return Redirect::to('client/student/' . $studentId)->withSuccess('Client program has been deleted');
    }

    public function createFormEmbed(Request $request)
    {
        $programName = $request->get('program_name');
        if ($programName == null)
            abort(404);
        
        if (!$program = $this->programRepository->getProgramByName($programName))
            abort(404);

        $leads = $this->leadRepository->getLeadForFormEmbedEvent();
        $schools = $this->schoolRepository->getAllSchools();
        $tags = $this->tagRepository->getAllTags();

        return view('form-embed.form-programs')->with(
            [
                'program' => $program,
                'leads' => $leads,
                'schools' => $schools,
                'tags' => $tags,
            ]
        );
    }

    public function storeFormEmbed(StoreFormProgramEmbedRequest $request)
    {
        $programId = $request->program;
        $program = $this->programRepository->getProgramById($programId);
        $leadId = $request->leadsource;
        $schoolId = $request->school;
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

            $index = 0;
            while($index < 2) 
            {   
                # initialize raw variable
                # why newClientDetails[$loop] should be array?
                # because to make easier for system to differentiate between parents and students like for example if user registered as a parent 
                # then index 0 is for parent data and index 1 is for children data, otherwise 
                $newClientDetails[$index] = [
                    'name' => $request->fullname[$index],
                    'email' => $request->email[$index],
                    'phone' => $request->fullnumber[$index]
                ];

                # check if the client exist in our databases
                $existingClient = $this->checkExistingClient($newClientDetails[$index]['phone'], $newClientDetails[$index]['email']);
                if (!$existingClient['isExist']) {

                    # get firstname & lastname from fullname
                    $fullname = explode(' ', $newClientDetails[$index]['name']);
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
                        'mail' => $newClientDetails[$index]['email'],
                        'phone' => $newClientDetails[$index]['phone'],
                        'lead_id' => "LS001", # hardcode for lead website
                        'register_by' => $choosen_role
                    ];

                    switch ($choosen_role) {

                        case "parent":
                            $role = $index == 0 ? 'parent' : 'student';
                            break;

                        case "student":
                            $role = $index == 1 ? 'parent' : 'student';
                            break;


                    }

                    # additional info that should be stored when role is student and parent
                    # because all of the additional info are for the student
                    if ($choosen_role == 'parent' && $index == 1) {

                        $additionalInfo = [
                            'st_grade' => 12 - ($request->graduation_year - date('Y')),
                            'graduation_year' => $request->graduation_year,
                            'lead' => $request->leadsource,
                            'sch_id' => $schoolId != null ? $schoolId : $request->school,
                        ];

                        $clientDetails = array_merge($clientDetails, $additionalInfo);
                    
                    } else if ($choosen_role == 'student' && $index == 0) {

                        $additionalInfo = [
                            'st_grade' => 12 - ($request->graduation_year - date('Y')),
                            'graduation_year' => $request->graduation_year,
                            'lead' => $request->leadsource,
                            'sch_id' => $schoolId != null ? $schoolId : $request->school,
                        ];

                        $clientDetails = array_merge($clientDetails, $additionalInfo);

                    }

                    # stored a new client information
                    $newClient[$index] = $this->clientRepository->createClient($role, $clientDetails);
                    
                }

                $clientArrayIds[$index] = $existingClient['isExist'] ? $existingClient['id'] : $newClient[$index]->id;
                $index++;
            }

            switch ($choosen_role) {

                case "parent":
                    $parentId = $newClientDetails[0]['id'] = $clientArrayIds[0];
                    $childId = $clientArrayIds[1];
                    break;

                case "student":
                    $parentId = $clientArrayIds[1];
                    $childId = $newClientDetails[0]['id'] = $clientArrayIds[0];
                    break;

            }

            # store the destination country if registrant either parent or student
            $this->clientRepository->createDestinationCountry($childId, $request->destination_country);
            
            # attaching parent and student
            $this->clientRepository->createManyClientRelation($parentId, $childId);

            # initiate variables for client program
            $clientProgramDetails = [
                'client_id' => $childId,
                'prog_id' => $programId,
                'lead_id' => $leadId,
                'first_discuss_date' => Carbon::now(),
                'status' => 0,
                'registration_type' => 'FE'
            ];
            
            # store to client program
            if ($storedClientProgram = $this->clientProgramRepository->createClientProgram($clientProgramDetails))
            {

                # send thanks mail
                $this->sendMailThanks($storedClientProgram, $parentId, $childId);
            }

            # trigger define category client
            ProcessDefineCategory::dispatch([$childId])->onQueue('define-category-client');

            DB::commit();
        
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to register client from form program embed | error : '.$e->getMessage().' | Line : '.$e->getLine());
            return Redirect::to('form/program?program_name='.$program->prog_program)->withErrors('Something went wrong. Please try again or contact our administrator.');
        
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Embed', 'Client Program', 'Guest', $storedClientProgram);

        return Redirect::to('form/thanks');
    }    

    public function sendMailThanks($clientProgram, $parentId, $childId, $update = false)
    {
        $subject = 'Your registration is confirmed';
        $mail_resources = 'mail-template.thanks-email-program';

        $parent = $this->clientRepository->getClientById($parentId);
        $children = $this->clientRepository->getClientById($childId);
        
        $recipientDetails = [
            'name' => $parent->mail != null ? $parent->full_name : $children->full_name,  
            'mail' => $parent->mail != null ? $parent->mail : $children->mail,
            'children_details' => [
                'name' => $children->full_name
            ]
        ];
        
        $program = [
            'name' => $clientProgram->program->program_name
        ];

        try {
            Mail::send($mail_resources, ['client' => $recipientDetails, 'program' => $program], function ($message) use ($subject, $recipientDetails) {
                $message->to($recipientDetails['mail'], $recipientDetails['name'])
                    ->subject($subject);
            });
            $sent_mail = 1;
            
        } catch (Exception $e) {
            
            $sent_mail = 0;
            Log::error('Failed send email thanks to client that register using form program | error : '.$e->getMessage().' | Line '.$e->getLine());

        }

        # if update is true 
        # meaning that this function being called from scheduler
        # that updating the client event log mail, so the system no longer have to create the client event log mail
        if ($update === true) {
            return true;    
        }

        $logDetails = [
            'clientprog_id' => $clientProgram->clientprog_id,
            'sent_status' => $sent_mail
        ];

        return $this->clientProgramLogMailRepository->createClientProgramLogMail($logDetails);
    }

    public function addBundleProgram(Request $request)
    {
        DB::beginTransaction();

        try {
            $clientProgram = $clientProgramDetails = [];
            $uuid = (string) Str::uuid();
    
            foreach ($request->choosen as $key => $clientprog_id) {
                // fetch data client program
                $clientprog_db = $this->clientProgramRepository->getClientProgramById($clientprog_id);
                
                // check there is an invoice 
                $hasInvoiceStd = isset($clientprog_db->invoice) ? $clientprog_db->invoice()->count() : 0;
                $hasBundling = isset($clientprog_db->bundlingDetail) ? $clientprog_db->bundlingDetail()->count() : 0;
    
                $clientProgram[$request->number[$key]] = [
                    'clientprog_id' => $clientprog_id,
                    'status' => $clientprog_db->status,
                    'program' => $clientprog_db->prog_id,
                    'HasInvoice' => $hasInvoiceStd,
                    'HasBundling' => $hasBundling,
                ];
                
                $clientProgramDetails[] = [
                    'clientprog_id' => $clientprog_id,
                    'bundling_id' => $uuid,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
    
            $rules = [
                '*.clientprog_id' => ['required', 'exists:tbl_client_prog,clientprog_id'],
                '*.status' => ['required', 'in:1'],
                '*.HasInvoice' => function($attribute, $value, $fail) {
                    if((int)$value > 0){
                        $fail('This program already has an invoice');
                    }
                },
                '*.HasBundling' => function($attribute, $value, $fail) {
                    if((int)$value > 0){
                        $fail('This program is already in the bundle package');
                    }
                },
                // '*.program' => ['required', 'distinct']

            ];
    
            $validator = Validator::make($clientProgram, $rules);
    
            # threw error if validation fails
            if ($validator->fails()) {
                Log::warning($validator->errors());
    
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()
                ]);
            }
    
            $bundleProgram = $this->clientProgramRepository->createBundleProgram($uuid, $clientProgramDetails);
    
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Something went wrong. Please try again'
            ], 500);
        }
     
        
        return response()->json([
            'success' => true,
            'data' => $bundleProgram
        ]);

    }

    public function cancelBundleProgram(Request $request){

        DB::beginTransaction();

        try {
            $clientProgram = $clientProgramDetails = [];
            $bundlingId = $request->bundlingId;
    
            foreach ($request->choosen as $key => $clientprog_id) {
                // fetch data client program
                $clientprog_db = $this->clientProgramRepository->getClientProgramById($clientprog_id);
                
                // check there is an invoice 
                $hasInvoiceStd = isset($clientprog_db->invoice) ? $clientprog_db->invoice()->count() : 0;
               
                $hasBundling = isset($clientprog_db->bundlingDetail) ? $clientprog_db->bundlingDetail()->count() : 0;

                $clientProgram[$request->number[$key]] = [
                    'clientprog_id' => $clientprog_id,
                    'status' => $clientprog_db->status,
                    'HasInvoice' => $hasInvoiceStd,
                    'HasBundling' => $hasBundling,
                ];
                
            }
    
            $rules = [
                '*.clientprog_id' => ['required', 'exists:tbl_client_prog,clientprog_id'],
                '*.status' => ['required', 'in:1'],
                '*.HasInvoice' => function($attribute, $value, $fail) {
                    if((int)$value > 0){
                        $fail('This program already has an invoice');
                    }
                },
                '*.HasBundling' => function($attribute, $value, $fail) {
                    if((int)$value == 0){
                        $fail('This is not a bundle program');
                    }
                },
            ];
    
            $validator = Validator::make($clientProgram, $rules);
    
            # threw error if validation fails
            if ($validator->fails()) {
                Log::warning($validator->errors());
    
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()
                ]);
            }
    
            $deletedBundleProgram = $this->clientProgramRepository->deleteBundleProgram($bundlingId);
    
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error($e->getMessage() . 'Line: '. $e->getLine());
            return response()->json([
                'success' => false,
                'error' => 'Something went wrong. Please try again'
            ], 500);
        }
     
        
        return response()->json([
            'success' => true,
            'data' => $deletedBundleProgram
        ]);
    }
}
