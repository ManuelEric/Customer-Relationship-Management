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

        $this->tutoring_prog_list = Program::whereHas('main_prog', function($query) {
            $query->where('prog_name', 'Academic & Test Preparation');
        })->orWhereHas('sub_prog', function ($query) {
            $query->where('sub_prog_name', 'like', '%Tutoring%');
        })->pluck('prog_id')->toArray();
    }

    public function create(Request $request)
    {
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
                'student' => $student,
                'programs' => $programs,
                'leads' => $leads,
                'events' => $events,
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
        echo $request->pend_trial_date;
        exit;

        $status = $request->status;
        $progId = $request->prog_id;
        switch ($status) {

            # when program status is pending
            # and submitted prog_id is admission mentoring
            case 0 AND in_array($progId, $this->admission_prog_list):
                $clientProgramDetails = $request->only([
                    'lead_id',
                    'prog_id',
                    'event_id',
                    'eduf_id',
                    'kol_lead_id',
                    'partner_id',
                    'first_discuss_date',
                    'meeting_notes',
                    'pend_initconsult_date',
                    'pend_assessmentsent_date',
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
                
                # because the field for initial consult date & assessment date different from the table field
                # rename the index key to match the table field
                $clientProgramDetails['initconsult_date'] = $clientProgramDetails['pend_initconsult_date'];
                $clientProgramDetails['assessmentsent_date'] = $clientProgramDetails['pend_assessmentsent_date'];
                unset($clientProgramDetails['pend_initconsult_date']);
                unset($clientProgramDetails['pend_assessmentsent_date']);
                break;
            
            case 0 AND in_array($progId, $this->tutoring_prog_list):
                $clientProgramDetails = $request->only([
                    'lead_id',
                    'prog_id',
                    'event_id',
                    'eduf_id',
                    'kol_lead_id',
                    'partner_id',
                    'first_discuss_date',
                    'meeting_notes',
                    'pend_trial_date',
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

                # because the field for trial date different from the table field
                # rename the index key to match the table field
                $clientProgramDetails['trial_date'] = $clientProgramDetails['pend_trial_date'];
                unset($clientProgramDetails['pend_trial_date']);
                break;

            default:
                $clientProgramDetails = $request->only([
                    'lead_id',
                    'prog_id',
                    'event_id',
                    'eduf_id',
                    'kol_lead_id',
                    'partner_id',
                    'first_discuss_date',
                    'meeting_notes',
                    'status',
                    'success_date',
                    'failed_date',
                    'refund_date',
                    'reason_id',
                    'other_reason',
                    'pend_initconsult_date',
                    'pend_assessment_date',
                    'initconsult_date',
                    'assessmentsent_date',
                    'total_uni',
                    'total_foreign_currency',
                    'foreign_currency_exchange',
                    'total_idr',
                    'main_mentor',
                    'backup_mentor',
                    'installment_notes',
                    'trial_date',
                    'prog_start_date',
                    'prog_end_date',
                    'timesheet_link',
                    'tutor_id',
                    'test_date',
                    'last_class',
                    'diag_score',
                    'test_score',
                    'tutor_1',
                    'tutor_2',
                    'prog_running_status',
                    'empl_id'
                ]);
        }
        
        
        DB::beginTransaction();
        try {

            $this->clientProgramRepository->createClientProgram(['client_id' => $studentId] + $clientProgramDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update a student failed : ' . $e->getMessage());
            return Redirect::to('client/student/'.$studentId.'/program/create')->withError($e->getMessage());
            
        }

        return Redirect::to('client/student/'.$studentId)->withSuccess('A new program has been submitted for '.$student->fullname);
    }

}
