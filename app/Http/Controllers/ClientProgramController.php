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
    private $admission_prog_list;
    private $tutoring_prog_list;
    private $satact_prog_list;

    use CreateCustomPrimaryKeyTrait;

    public function __construct(ClientRepositoryInterface $clientRepository, ProgramRepositoryInterface $programRepository, LeadRepositoryInterface $leadRepository, EventRepositoryInterface $eventRepository, EdufLeadRepositoryInterface $edufLeadRepository, UserRepositoryInterface $userRepository, CorporateRepositoryInterface $corporateRepository, ReasonRepositoryInterface $reasonRepository, ClientProgramRepositoryInterface $clientProgramRepository, ClientEventRepositoryInterface $clientEventRepository, SchoolRepositoryInterface $schoolRepository, TagRepositoryInterface $tagRepository, ClientProgramLogMailRepositoryInterface $clientProgramLogMailRepository, ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository)
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
            'meeting_notes',
            'status',
            'referral_code',
            'empl_id'
        ]);

        # set lead_id based on lead_id & kol_lead_id
        # when lead_id is kol
        # then put kol_lead_id to lead_id
        # otherwise
        # when lead_id is not kol 
        # then lead_id is lead_id
        if ($request->lead_id == "kol") {

            unset($clientProgramDetails['lead_id']);
            $clientProgramDetails['lead_id'] = $request->kol_lead_id;
        }

        switch ($status) {

                # when program status is pending
            case 0:

                # and submitted prog_id is admission mentoring
                if (in_array($progId, $this->admission_prog_list)) {

                    # add additional values
                    $clientProgramDetails['initconsult_date'] = $request->pend_initconsult_date;
                    $clientProgramDetails['assessmentsent_date'] = $request->pend_assessmentsent_date;
                    $clientProgramDetails['mentor_ic'] = $request->pend_mentor_ic;
                } elseif (in_array($progId, $this->tutoring_prog_list)) {

                    # add additional values
                    $clientProgramDetails['trial_date'] = $request->pend_trial_date;
                }

                break;

                # when program status is active
            case 1:
                # declare default variable
                $clientProgramDetails['prog_running_status'] = $request->prog_running_status;
                $clientProgramDetails['success_date'] = $request->success_date;

                # and submitted prog_id is admission mentoring
                if (in_array($progId, $this->admission_prog_list)) {

                    # add additional values
                    $clientProgramDetails['success_date'] = $request->success_date;
                    $clientProgramDetails['initconsult_date'] = $request->initconsult_date;
                    $clientProgramDetails['assessmentsent_date'] = $request->assessmentsent_date;
                    $clientProgramDetails['prog_end_date'] = $request->mentoring_prog_end_date;
                    $clientProgramDetails['total_uni'] = $request->total_uni;
                    $clientProgramDetails['total_foreign_currency'] = $request->total_foreign_currency;
                    $clientProgramDetails['foreign_currency'] = $request->foreign_currency;
                    $clientProgramDetails['foreign_currency_exchange'] = $request->foreign_currency_exchange;
                    $clientProgramDetails['total_idr'] = $request->total_idr;
                    $clientProgramDetails['installment_notes'] = $request->installment_notes;
                    $clientProgramDetails['prog_running_status'] = (int) $request->prog_running_status;

                    
                    # declare the variables for agreement
                    if ($request->hasFile('agreement')) {

                        # setting up the agreement request file
                        $file_name = "agreement_".str_replace(' ', '_', trim($student->full_name))."_".$progId;
                        $file_format = $request->file('agreement')->getClientOriginalExtension();
                        
                        # generate the file path
                        $file_path = $file_name.'.'.$file_format;

                    }

                } elseif (in_array($progId, $this->tutoring_prog_list)) {

                    # add additional values
                    $clientProgramDetails['success_date'] = $request->success_date;
                    $clientProgramDetails['trial_date'] = $request->trial_date;
                    // $clientProgramDetails['first_class'] = $request->first_class;
                    $clientProgramDetails['prog_start_date'] = $request->prog_start_date;
                    $clientProgramDetails['prog_end_date'] = $request->prog_end_date;
                    $clientProgramDetails['timesheet_link'] = $request->timesheet_link;
                    // $clientProgramDetails['tutor_id'] = $request->tutor_id;
                    $clientProgramDetails['prog_running_status'] = (int) $request->prog_running_status;
                } elseif (in_array($progId, $this->satact_prog_list)) {
                    
                    # add additional values
                    $clientProgramDetails['success_date'] = $request->success_date;
                    $clientProgramDetails['test_date'] = $request->test_date;
                    $clientProgramDetails['first_class'] = $request->first_class;
                    $clientProgramDetails['last_class'] = $request->last_class;
                    $clientProgramDetails['diag_score'] = $request->diag_score;
                    $clientProgramDetails['test_score'] = $request->test_score;
                    // $clientProgramDetails['tutor_1'] = $request->tutor_1;
                    // $clientProgramDetails['tutor_2'] = $request->tutor_2;
                    $clientProgramDetails['prog_running_status'] = (int) $request->prog_running_status;
                }

                if (in_array($progId, $this->admission_prog_list)) {

                    $clientProgramDetails['supervising_mentor'] = $request->supervising_mentor;
                    $clientProgramDetails['profile_building_mentor'] = isset($request->profile_building_mentor) ? $request->profile_building_mentor : NULL;
                    $clientProgramDetails['aplication_strategy_mentor'] = isset($request->aplication_strategy_mentor) ? $request->aplication_strategy_mentor : NULL;
                    $clientProgramDetails['writing_mentor'] = isset($request->writing_mentor) ? $request->aplication_strategy_mentor : NULL;
                    $clientProgramDetails['mentor_ic'] = $request->mentor_ic;
                } elseif (in_array($progId, $this->tutoring_prog_list)) {

                    $clientProgramDetails['tutor_id'] = $request->tutor_id;

                    # if session tutor form doesn't exist then don't detail session tutor
                    if (isset($request->session)) {

                        $clientProgramDetails['session_tutor'] = $request->session; // how many session will applied
                        $clientProgramDetails['session_tutor_detail'] = [
                            'datetime' => $request->sessionDetail,
                            'linkmeet' => $request->sessionLinkMeet
                        ];
                    }

                } elseif (in_array($progId, $this->satact_prog_list)) {

                    $clientProgramDetails['tutor_1'] = $request->tutor_1;
                    $clientProgramDetails['tutor_2'] = $request->tutor_2;
                    $clientProgramDetails['timesheet_1'] = $request->timesheet_1;
                    $clientProgramDetails['timesheet_2'] = $request->timesheet_2;
                }
                break;

                # when program status is failed
            case 2:

                $clientProgramDetails['failed_date'] = $request->failed_date;
                $clientProgramDetails['reason_id'] = $request->reason_id;
                $clientProgramDetails['other_reason'] = $request->other_reason;

                break;

                # when program status is refund
            case 3:
                $clientProgramDetails['refund_date'] = $request->failed_date;
                $clientProgramDetails['refund_notes'] = $request->refund_notes;
                $clientProgramDetails['reason_id'] = $request->reason_id;
                $clientProgramDetails['other_reason'] = $request->other_reason;
                break;
        }

        DB::beginTransaction();
        try {

            $newClientProgram = $this->clientProgramRepository->createClientProgram(['client_id' => $studentId] + $clientProgramDetails);
            $newClientProgramId = $newClientProgram->clientprog_id;
            $newClientProgramSuccessDate = $newClientProgram->success_date;

            # check the status of the new client program
            switch ($clientProgramDetails['status']) {

                # if client program has been submitted 
                # then change status client to potential
                case 0: # pending

                    $this->clientRepository->updateClient($studentId, ['st_statuscli' => 1]);
                    break;

                case 1: # success

                    # if he/she join admission mentoring program
                    # add role mentee
                    if (in_array($progId, $this->admission_prog_list)) {
                        
                        $last_id = UserClient::max('st_id');
                        $student_id_without_label = $this->remove_primarykey_label($last_id, 3);
                        $stId = 'ST-' . $this->add_digit((int) $student_id_without_label + 1, 4);
                        $this->clientRepository->updateClient($studentId, ['st_id' => $stId]);

                        $this->clientRepository->addRole($studentId, 'Mentee');

                        # upload the agreement
                        # storing the file
                        if (!$request->file('agreement')->storeAs('public/uploaded_file/agreement', $file_path))
                            throw new Exception('The file cannot be uploaded.');

                        
                        # update the path into clientprogram table
                        $this->clientProgramRepository->updateFewField($newClientProgramId, ['agreement' => $file_path]);

                    }

                    # when program running status was 2 which mean done
                    # then check if client has other program running or done
                    if ($clientProgramDetails['prog_running_status'] != 2)
                        $this->clientRepository->updateClient($studentId, ['st_statuscli' => 2]);


                    # when all program were done
                    # change status client to completed (3)
                    if ($this->clientRepository->checkAllProgramStatus($studentId) == "completed")
                        $this->clientRepository->updateClient($studentId, ['st_statuscli' => 3]);

                    break;
            }

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
            'meeting_notes',
            'status',
            'empl_id',
            'referral_code'
        ]);

        switch ($request->lead_id) {

            case "LS004": #All-In Event
                $clientProgramDetails['eduf_lead_id'] = null;
                // $clientProgramDetails['kol_lead_id'] = null;
                $clientProgramDetails['partner_id'] = null;
                break;

            case "LS010": #ALL-In Partners
                $clientProgramDetails['eduf_lead_id'] = null;
                // $clientProgramDetails['kol_lead_id'] = null;
                $clientProgramDetails['clientevent_id'] = null;
                break;

            case "LS018": #External Edufair
                $clientProgramDetails['partner_id'] = null;
                // $clientProgramDetails['kol_lead_id'] = null;
                $clientProgramDetails['clientevent_id'] = null;
                break;

            case "kol": #External Edufair
                $clientProgramDetails['partner_id'] = null;
                // $clientProgramDetails['eduf_lead_id'] = null;
                $clientProgramDetails['clientevent_id'] = null;
                break;
        }

        # set lead_id based on lead_id & kol_lead_id
        # when lead_id is kol
        # then put kol_lead_id to lead_id
        # otherwise
        # when lead_id is not kol 
        # then lead_id is lead_id
        if ($request->lead_id == "kol") {

            unset($clientProgramDetails['lead_id']);
            $clientProgramDetails['lead_id'] = $request->kol_lead_id;
        }

        if ($request->lead_id != 'LS005') # Referral
        {
            $clientProgramDetails['referral_code'] = null;
        }

        switch ($status) {

                # when program status is pending
            case 0:

                # and submitted prog_id is admission mentoring
                if (in_array($progId, $this->admission_prog_list)) {

                    # add additional values
                    $clientProgramDetails['initconsult_date'] = $request->pend_initconsult_date;
                    $clientProgramDetails['assessmentsent_date'] = $request->pend_assessmentsent_date;
                    $clientProgramDetails['mentor_ic'] = $request->pend_mentor_ic;
                } elseif (in_array($progId, $this->tutoring_prog_list)) {

                    # add additional values
                    $clientProgramDetails['trial_date'] = $request->pend_trial_date;
                }

                break;

                # when program status is active
            case 1:
                $clientProgramDetails['prog_running_status'] = $request->prog_running_status;
                $clientProgramDetails['success_date'] = $request->success_date;

                # and submitted prog_id is admission mentoring
                if (in_array($progId, $this->admission_prog_list)) {

                    # add additional values
                    $clientProgramDetails['success_date'] = $request->success_date;
                    $clientProgramDetails['initconsult_date'] = $request->initconsult_date;
                    $clientProgramDetails['assessmentsent_date'] = $request->assessmentsent_date;
                    $clientProgramDetails['prog_end_date'] = $request->mentoring_prog_end_date;
                    $clientProgramDetails['total_uni'] = $request->total_uni;
                    $clientProgramDetails['total_foreign_currency'] = $request->total_foreign_currency;
                    $clientProgramDetails['foreign_currency'] = $request->foreign_currency;
                    $clientProgramDetails['foreign_currency_exchange'] = $request->foreign_currency_exchange;
                    $clientProgramDetails['total_idr'] = $request->total_idr;
                    // $clientProgramDetails['main_mentor'] = $request->main_mentor;
                    // $clientProgramDetails['backup_mentor'] = isset($request->backup_mentor) ? $request->backup_mentor : NULL;
                    $clientProgramDetails['supervising_mentor'] = $request->supervising_mentor;
                    $clientProgramDetails['profile_building_mentor'] = isset($request->profile_building_mentor) ? $request->profile_building_mentor : NULL;
                    $clientProgramDetails['aplication_strategy_mentor'] = isset($request->aplication_strategy_mentor) ? $request->aplication_strategy_mentor : NULL;
                    $clientProgramDetails['writing_mentor'] = isset($request->writing_mentor) ? $request->writing_mentor : NULL;                    
                    $clientProgramDetails['installment_notes'] = $request->installment_notes;
                    $clientProgramDetails['prog_running_status'] = $request->prog_running_status;

                     # declare the variables for agreement
                     if ($request->hasFile('agreement')) {

                        # setting up the agreement request file
                        $file_name = "agreement_".str_replace(' ', '_', trim($student->full_name))."_".$progId;
                        $file_format = $request->file('agreement')->getClientOriginalExtension();
                        
                        # generate the file path
                        $file_path = $file_name.'.'.$file_format;

                    }

                } elseif (in_array($progId, $this->tutoring_prog_list)) {

                    # add additional values
                    $clientProgramDetails['success_date'] = $request->success_date;
                    $clientProgramDetails['trial_date'] = $request->trial_date;
                    $clientProgramDetails['prog_start_date'] = $request->prog_start_date;
                    $clientProgramDetails['prog_end_date'] = $request->prog_end_date;
                    $clientProgramDetails['timesheet_link'] = $request->timesheet_link;
                    // $clientProgramDetails['tutor_id'] = $request->tutor_id;
                    $clientProgramDetails['prog_running_status'] = $request->prog_running_status;
                } elseif (in_array($progId, $this->satact_prog_list)) {

                    # add additional values
                    $clientProgramDetails['success_date'] = $request->success_date;
                    $clientProgramDetails['test_date'] = $request->test_date;
                    $clientProgramDetails['first_class'] = $request->first_class;
                    $clientProgramDetails['last_class'] = $request->last_class;
                    $clientProgramDetails['diag_score'] = $request->diag_score;
                    $clientProgramDetails['test_score'] = $request->test_score;
                    // $clientProgramDetails['tutor_1'] = $request->tutor_1;
                    // $clientProgramDetails['tutor_2'] = $request->tutor_2;
                    $clientProgramDetails['prog_running_status'] = $request->prog_running_status;
                }

                if (in_array($progId, $this->admission_prog_list)) {

                    // $clientProgramDetails['main_mentor'] = $request->main_mentor;
                    // $clientProgramDetails['backup_mentor'] = isset($request->backup_mentor) ? $request->backup_mentor : NULL;
                    $clientProgramDetails['supervising_mentor'] = $request->supervising_mentor;
                    $clientProgramDetails['profile_building_mentor'] = isset($request->profile_building_mentor) ? $request->profile_building_mentor : NULL;
                    $clientProgramDetails['aplication_strategy_mentor'] = isset($request->aplication_strategy_mentor) ? $request->aplication_strategy_mentor : NULL;
                    $clientProgramDetails['writing_mentor'] = isset($request->writing_mentor) ? $request->writing_mentor : NULL;                    
                    $clientProgramDetails['mentor_ic'] = $request->mentor_ic;
                } elseif (in_array($progId, $this->tutoring_prog_list)) {

                    $clientProgramDetails['tutor_id'] = $request->tutor_id;

                    # if session tutor form doesn't exist then don't detail session tutor
                    if (isset($request->session)) {
                        
                        $clientProgramDetails['session_tutor'] = $request->session; // how many session will applied
                        $clientProgramDetails['session_tutor_detail'] = [
                            'datetime' => $request->sessionDetail,
                            'linkmeet' => $request->sessionLinkMeet
                        ];
                    }
                } elseif (in_array($progId, $this->satact_prog_list)) {

                    $clientProgramDetails['tutor_1'] = $request->tutor_1;
                    $clientProgramDetails['tutor_2'] = $request->tutor_2;
                    $clientProgramDetails['timesheet_1'] = $request->timesheet_1;
                    $clientProgramDetails['timesheet_2'] = $request->timesheet_2;

                }

                break;

                # when program status is failed
            case 2:

                $clientProgramDetails['failed_date'] = $request->failed_date;
                $clientProgramDetails['reason_id'] = $request->reason_id;
                $clientProgramDetails['other_reason'] = $request->other_reason;

                break;

                # when program status is refund
            case 3:
                $clientProgramDetails['refund_date'] = $request->refund_date;
                $clientProgramDetails['refund_notes'] = $request->refund_notes;
                $clientProgramDetails['reason_id'] = $request->reason_id;
                $clientProgramDetails['other_reason'] = $request->other_reason;
                break;
        };

        DB::beginTransaction();
        try {

            $updatedClientProgram = $this->clientProgramRepository->updateClientProgram($clientProgramId, ['client_id' => $studentId] + $clientProgramDetails);
            $updatedClientProgramId = $updatedClientProgram->clientprog_id;

            switch ($clientProgramDetails['status']) {

                    # if client program has been submitted 
                    # then change status client to potential
                case 0: # pending

                    # if he/she has already join admission mentoring program
                    # remove role mentee
                    if (in_array($progId, $this->admission_prog_list)) {
                        $this->clientRepository->removeRole($studentId, 'Mentee');
                    }

                    $this->clientRepository->updateClient($studentId, ['st_statuscli' => 1]);
                    break;

                case 1: # success

                    # if he/she join admission mentoring program
                    # add role mentee
                    if (in_array($progId, $this->admission_prog_list)) {
                        $last_id = UserClient::max('st_id');
                        $student_id_without_label = $this->remove_primarykey_label($last_id, 3);
                        $stId = 'ST-' . $this->add_digit((int) $student_id_without_label + 1, 4);
                        $this->clientRepository->updateClient($studentId, ['st_id' => $stId]);

                        $this->clientRepository->addRole($studentId, 'Mentee');

                        # upload the agreement
                        # storing the file
                        if ($request->hasFile('agreement')) {
                            if (!$request->file('agreement')->storeAs('public/uploaded_file/agreement', $file_path))
                                throw new Exception('The file cannot be uploaded.');
                            
                            if ($old_agreement = $updatedClientProgram->agreement) {

                                if (Storage::exists('public/uploaded_file/agreement/'.$old_agreement) && $file_path !== null) {
                                    Storage::delete('public/uploaded_file/agreement/'.$old_agreement);
                                }

                            }
                            
                            # update the path into clientprogram table
                            $this->clientProgramRepository->updateFewField($updatedClientProgramId, ['agreement' => $file_path]);
                        }
                    }

                    # when program running status was 2 which mean done
                    # then check if client has other program running or done
                    if ((int) $clientProgramDetails['prog_running_status'] != 2) {
                        $this->clientRepository->updateClient($studentId, ['st_statuscli' => 2]);
                    }

                    # when all program were done
                    # change status client to completed (3)

                    if ((int) $clientProgramDetails['prog_running_status'] == 2) {

                        if ($this->clientRepository->checkAllProgramStatus($studentId) == "completed")
                            $this->clientRepository->updateClient($studentId, ['st_statuscli' => 3]);
                    }

                    break;

                case 2: # failed
                case 3: # refund

                    # if he/she has already join admission mentoring program
                    # remove role mentee
                    if (in_array($progId, $this->admission_prog_list)) {
                        $this->clientRepository->removeRole($studentId, 'Mentee');
                    }
                    break;
            }

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

            # if failed storing the data into the database
            # remove the uploaded file from storage
            if (Storage::exists('public/uploaded_file/agreement/'.$file_path) && $file_path !== null) {
                Storage::delete('public/uploaded_file/agreement/'.$file_path);
            }

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
                        'register_as' => $choosen_role
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
