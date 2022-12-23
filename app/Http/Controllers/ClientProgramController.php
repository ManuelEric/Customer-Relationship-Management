<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientProgramRequest;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\EdufLeadRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\ReasonRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Program;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class ClientProgramController extends Controller
{
    private ClientRepositoryInterface $clientRepository;
    private ProgramRepositoryInterface $programRepository;
    private LeadRepositoryInterface $leadRepository;
    private EventRepositoryInterface $eventRepository;
    private EdufLeadRepositoryInterface $edufLeadRepository;
    private UserRepositoryInterface $userRepository;
    private CorporateRepositoryInterface $corporateRepository;
    private ReasonRepositoryInterface $reasonRepository;
    private ClientProgramRepositoryInterface $clientProgramRepository;

    public function __construct(ClientRepositoryInterface $clientRepository, ProgramRepositoryInterface $programRepository, LeadRepositoryInterface $leadRepository, EventRepositoryInterface $eventRepository, EdufLeadRepositoryInterface $edufLeadRepository, UserRepositoryInterface $userRepository, CorporateRepositoryInterface $corporateRepository, ReasonRepositoryInterface $reasonRepository, ClientProgramRepositoryInterface $clientProgramRepository)
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

        $this->admission_prog_list = Program::whereHas('main_prog', function($query) {
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

    public function show(Request $request)
    {
        $studentId = $request->route('student');
        $clientProgramId = $request->route('program');

        $student = $this->clientRepository->getClientById($studentId);
        $clientProgram = $this->clientProgramRepository->getClientProgramById($clientProgramId);

        # programs
        $b2cprograms = $this->programRepository->getAllProgramByType("B2C");
        $b2bb2cprograms = $this->programRepository->getAllProgramByType("B2B/B2C");
        $programs = $b2cprograms->merge($b2bb2cprograms);
        
        # main leads
        $leads = $this->leadRepository->getAllMainLead();
        $clientEvents = $this->eventRepository->getAllEvents();
        $external_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        $partners = $this->corporateRepository->getAllCorporate();
        $internalPic = $this->userRepository->getAllUsersByRole('Employee');

        $tutors = $this->userRepository->getAllUsersByRole('Tutor');
        $mentors = $this->userRepository->getAllUsersByRole('Mentor');

        $reasons = $this->reasonRepository->getAllReasons();

        return view('pages.program.client-program.form')->with(
            [
                'student' => $student,
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

        # programs
        $b2cprograms = $this->programRepository->getAllProgramByType("B2C");
        $b2bb2cprograms = $this->programRepository->getAllProgramByType("B2B/B2C");
        $programs = $b2cprograms->merge($b2bb2cprograms);
        
        # main leads
        $leads = $this->leadRepository->getAllMainLead();
        $events = $this->eventRepository->getAllEvents();
        $external_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        $partners = $this->corporateRepository->getAllCorporate();
        $internalPic = $this->userRepository->getAllUsersByRole('Employee');

        $tutors = $this->userRepository->getAllUsersByRole('Tutor');
        $mentors = $this->userRepository->getAllUsersByRole('Mentor');

        $reasons = $this->reasonRepository->getAllReasons();

        return view('pages.program.client-program.form')->with(
            [
                'p' => $p,
                'edit' => true,
                'student' => $student,
                'programs' => $programs,
                'leads' => $leads,
                'clientEvents' => $events,
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

    public function store(StoreClientProgramRequest $request)
    {
        $studentId = $request->route('student');
        $student = $this->clientRepository->getClientById($studentId);

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

                } elseif (in_array($progId, $this->tutoring_prog_list)) {
    
                    # add additional values
                    $clientProgramDetails['trial_date'] = $request->pend_trial_date;

                }

                break;

            # when program status is active
            case 1:
                # and submitted prog_id is admission mentoring
                if (in_array($progId, $this->admission_prog_list)) {

                    # add additional values
                    $clientProgramDetails['success_date'] = $request->success_date;
                    $clientProgramDetails['initconsult_date'] = $request->initconsult_date;
                    $clientProgramDetails['assessmentsent_date'] = $request->assessmentsent_date;
                    $clientProgramDetails['prog_end_date'] = $request->prog_end_date;
                    $clientProgramDetails['total_uni'] = $request->total_uni;
                    $clientProgramDetails['total_foreign_currency'] = $request->total_foreign_currency;
                    $clientProgramDetails['foreign_currency'] = $request->foreign_currency;
                    $clientProgramDetails['foreign_currency_exchange'] = $request->foreign_currency_exchange;
                    $clientProgramDetails['total_idr'] = $request->total_idr;
                    $clientProgramDetails['main_mentor'] = $request->main_mentor;
                    $clientProgramDetails['backup_mentor'] = $request->backup_mentor;
                    $clientProgramDetails['installment_notes'] = $request->installment_notes;
                    $clientProgramDetails['prog_running_status'] = $request->prog_running_status;
                    

                } elseif (in_array($progId, $this->tutoring_prog_list)) {
    
                    # add additional values
                    $clientProgramDetails['success_date'] = $request->success_date;
                    $clientProgramDetails['trial_date'] = $request->trial_date;
                    $clientProgramDetails['prog_start_date'] = $request->prog_start_date;
                    $clientProgramDetails['prog_end_date'] = $request->prog_end_date;
                    $clientProgramDetails['timesheet_link'] = $request->timesheet_link;
                    $clientProgramDetails['tutor_id'] = $request->tutor_id;
                    $clientProgramDetails['prog_running_status'] = $request->prog_running_status;

                } elseif (in_array($progId, $this->satact_prog_list)) {

                    # add additional values
                    $clientProgramDetails['success_date'] = $request->success_date;
                    $clientProgramDetails['test_date'] = $request->test_date;
                    $clientProgramDetails['last_class'] = $request->last_class;
                    $clientProgramDetails['diag_score'] = $request->diag_score;
                    $clientProgramDetails['test_score'] = $request->test_score;
                    $clientProgramDetails['tutor_1'] = $request->tutor_1;
                    $clientProgramDetails['tutor_2'] = $request->tutor_2;
                    $clientProgramDetails['prog_running_status'] = $request->prog_running_status;

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
                $clientProgramDetails['reason_id'] = $request->reason_id;
                $clientProgramDetails['other_reason'] = $request->other_reason;
                break;
        }

        DB::beginTransaction();
        try {

            $this->clientProgramRepository->createClientProgram(['client_id' => $studentId] + $clientProgramDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Create a student program failed : ' . $e->getMessage());
            return Redirect::to('client/student/'.$studentId.'/program/create')->withError($e->getMessage());
            
        }

        return Redirect::to('client/student/'.$studentId)->withSuccess('A new program has been submitted for '.$student->fullname);
    }

    public function edit(Request $request)
    {
        $studentId = $request->route('student');
        $clientProgramId = $request->route('program');

        $student = $this->clientRepository->getClientById($studentId);
        $clientProgram = $this->clientProgramRepository->getClientProgramById($clientProgramId);

        # programs
        $b2cprograms = $this->programRepository->getAllProgramByType("B2C");
        $b2bb2cprograms = $this->programRepository->getAllProgramByType("B2B/B2C");
        $programs = $b2cprograms->merge($b2bb2cprograms);
        
        # main leads
        $leads = $this->leadRepository->getAllMainLead();
        $clientEvents = $this->eventRepository->getAllEvents();
        $external_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        $partners = $this->corporateRepository->getAllCorporate();
        $internalPic = $this->userRepository->getAllUsersByRole('Employee');

        $tutors = $this->userRepository->getAllUsersByRole('Tutor');
        $mentors = $this->userRepository->getAllUsersByRole('Mentor');

        $reasons = $this->reasonRepository->getAllReasons();

        return view('pages.program.client-program.form')->with(
            [
                'edit' => true,
                'student' => $student,
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
            'empl_id'
        ]);

        switch ($request->lead_id) {

            case "LS004": #All-In Event
                $clientProgramDetails['eduf_lead_id'] = null;
                $clientProgramDetails['kol_lead_id'] = null;
                $clientProgramDetails['partner_id'] = null;
                break;
                
            case "LS015": #ALL-In Partners
                $clientProgramDetails['eduf_lead_id'] = null;
                $clientProgramDetails['kol_lead_id'] = null;
                $clientProgramDetails['clientevent_id'] = null;
                break;

            case "LS018": #External Edufair
                $clientProgramDetails['partner_id'] = null;
                $clientProgramDetails['kol_lead_id'] = null;
                $clientProgramDetails['clientevent_id'] = null;
                break;
                
            case "kol": #External Edufair
                $clientProgramDetails['partner_id'] = null;
                $clientProgramDetails['eduf_lead_id'] = null;
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

        switch ($status) {

            # when program status is pending
            case 0: 

                # and submitted prog_id is admission mentoring
                if (in_array($progId, $this->admission_prog_list)) {
                    
                    # add additional values
                    $clientProgramDetails['initconsult_date'] = $request->pend_initconsult_date;
                    $clientProgramDetails['assessmentsent_date'] = $request->pend_assessmentsent_date;

                } elseif (in_array($progId, $this->tutoring_prog_list)) {
    
                    # add additional values
                    $clientProgramDetails['trial_date'] = $request->pend_trial_date;

                }

                break;

            # when program status is active
            case 1:
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
                    $clientProgramDetails['main_mentor'] = $request->main_mentor;
                    $clientProgramDetails['backup_mentor'] = $request->backup_mentor;
                    $clientProgramDetails['installment_notes'] = $request->installment_notes;
                    $clientProgramDetails['prog_running_status'] = $request->prog_running_status;
                    

                } elseif (in_array($progId, $this->tutoring_prog_list)) {
    
                    # add additional values
                    $clientProgramDetails['success_date'] = $request->success_date;
                    $clientProgramDetails['trial_date'] = $request->trial_date;
                    $clientProgramDetails['prog_start_date'] = $request->prog_start_date;
                    $clientProgramDetails['prog_end_date'] = $request->prog_end_date;
                    $clientProgramDetails['timesheet_link'] = $request->timesheet_link;
                    $clientProgramDetails['tutor_id'] = $request->tutor_id;
                    $clientProgramDetails['prog_running_status'] = $request->prog_running_status;

                } elseif (in_array($progId, $this->satact_prog_list)) {

                    # add additional values
                    $clientProgramDetails['success_date'] = $request->success_date;
                    $clientProgramDetails['test_date'] = $request->test_date;
                    $clientProgramDetails['last_class'] = $request->last_class;
                    $clientProgramDetails['diag_score'] = $request->diag_score;
                    $clientProgramDetails['test_score'] = $request->test_score;
                    $clientProgramDetails['tutor_1'] = $request->tutor_1;
                    $clientProgramDetails['tutor_2'] = $request->tutor_2;
                    $clientProgramDetails['prog_running_status'] = $request->prog_running_status;

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
                $clientProgramDetails['reason_id'] = $request->reason_id;
                $clientProgramDetails['other_reason'] = $request->other_reason;
                break;
        };

        DB::beginTransaction();
        try {

            $this->clientProgramRepository->updateClientProgram($clientProgramId, ['client_id' => $studentId] + $clientProgramDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update a student program failed : ' . $e->getMessage());
            return Redirect::to('client/student/'.$studentId.'/program/'.$clientProgramId.'/edit')->withError($e->getMessage());
            
        }

        return Redirect::to('client/student/'.$studentId.'/program/'.$clientProgramId)->withSuccess('A program has been updated for '.$student->fullname);
    }

}
