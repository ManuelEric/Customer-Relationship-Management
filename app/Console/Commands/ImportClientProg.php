<?php

namespace App\Console\Commands;

use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\FollowupRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\ReasonRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Lead;
use App\Models\Program;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportClientProg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:clientprog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import client program data known as stprog from crm bigdata v1';

    protected ClientRepositoryInterface $clientRepository;
    protected ClientProgramRepositoryInterface $clientProgramRepository;
    protected ProgramRepositoryInterface $programRepository;
    protected LeadRepositoryInterface $leadRepository;
    protected ReasonRepositoryInterface $reasonRepository;
    protected UserRepositoryInterface $userRepository;
    protected FollowupRepositoryInterface $followupRepository;

    public function __construct(ClientRepositoryInterface $clientRepository, ClientProgramRepositoryInterface $clientProgramRepository, ProgramRepositoryInterface $programRepository, LeadRepositoryInterface $leadRepository, ReasonRepositoryInterface $reasonRepository, UserRepositoryInterface $userRepository, FollowupRepositoryInterface $followupRepository)
    {
        parent::__construct();
        $this->clientRepository = $clientRepository;
        $this->clientProgramRepository = $clientProgramRepository;
        $this->programRepository = $programRepository;
        $this->leadRepository = $leadRepository;
        $this->reasonRepository = $reasonRepository;
        $this->userRepository = $userRepository;
        $this->followupRepository = $followupRepository;
        
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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::beginTransaction();
        try {

            $crm_clientprogs = $this->clientProgramRepository->getClientProgramFromV1();
            foreach ($crm_clientprogs as $crm_clientprog)
            {

                # get the student id on database v2 using name
                if (!isset($crm_clientprog->student->st_firstname)) 
                    continue; # st num 146 not found in table students v1
                

                $crm_student_name = $crm_clientprog->student->st_firstname.' '.$crm_clientprog->student->st_lastname;
                
                if (!$student_v2 = $this->clientRepository->getStudentByStudentName($crm_student_name))
                {
                    echo 'tidak menemukan student '.$crm_student_name;
                }
    
                $student_v2_id = $student_v2->id;
    
                # check if prog id is exists
                $crm_clientprog_progid = $crm_clientprog->prog_id;
                if (!$program_v2 = $this->programRepository->getProgramById($crm_clientprog_progid))
                {
                    $main_prog = $this->mainProgRepository->getMainProgByName($crm_clientprog->prog_main);
                    $sub_prog = $this->subProgRepository->getSubProgBySubProgName($crm_clientprog->prog_sub);
    
                    $programDetails = [
                        'prog_id' => $crm_clientprog_progid,
                        'main_prog_id' => $main_prog->id, //!
                        'sub_prog_id'=> $sub_prog->id, //!
                        'prog_main' => $crm_clientprog->prog_main,
                        'main_number' => $crm_clientprog->main_number,
                        'prog_sub' => $crm_clientprog->prog_sub,
                        'prog_program' => $crm_clientprog->prog_program,
                        'prog_type' => $crm_clientprog->prog_type,
                        'prog_mentor' => $crm_clientprog->prog_mentor,
                        'prog_payment' => $crm_clientprog->prog_payment,
                    ];
    
                    $program_v2 = $this->programRepository->createProgram($programDetails);
                }
    
                $progId = $program_v2->prog_id;
    
                # check if lead id is exists
                $crm_clientprog_leadname = $crm_clientprog->lead->lead_name;
                if (!$lead_v2 = $this->leadRepository->getLeadByName($crm_clientprog_leadname))
                {
                    # initialize
                    $last_id = Lead::max('lead_id');
                    $lead_id_without_label = $this->remove_primarykey_label($last_id, 2);
                    $lead_id_with_label = 'LS' . $this->add_digit($lead_id_without_label + 1, 3);
    
                    $leadDetails = [
                        'lead_id' => $lead_id_with_label,
                        'main_lead' => $crm_clientprog_leadname
                    ];
    
                    $lead_v2 = $this->leadRepository->createLead($leadDetails);
                }
    
                # check if reason id is exists
                if ($crm_clientprog->reason != NULL)
                {
                    $crm_clientprog_reasonname = $crm_clientprog->reason->reason_name;
                    if ($reason_v2 = $this->reasonRepository->getReasonByReasonName($crm_clientprog_reasonname))
                    {
                        $reasonDetails = [
                            'reason_name' => $crm_clientprog_reasonname
                            
                        ];
        
                        $reason_v2 = $this->reasonRepository->createReason($reasonDetails);
                    }
                }
    
                # check if empl id is exists
                if ($crm_clientprog->pic != "") 
                {
                    $crm_clientprog_emplname = $crm_clientprog->pic->empl_firstname.' '.$crm_clientprog->pic->empl_lastname;
                    $crm_clientprog_emplmail = $crm_clientprog->pic->empl_email;
                    if (!$employee_v2 = $this->userRepository->getUserByFullNameOrEmail($crm_clientprog_emplname, $crm_clientprog_emplmail))
                        throw new Exception('Could not find employee.');
                }
                
    
                $clientProgramDetails = [
                    'client_id' => $student_v2_id,
                    'prog_id' => $progId,
                    'lead_id' => $lead_v2->lead_id,
                    'eduf_lead_id' => null,
                    'partner_id' => null,
                    'clientevent_id' => null,
                    'first_discuss_date' => $crm_clientprog->stprog_firstdisdate,
                    'meeting_notes' => $crm_clientprog->stprog_meetingnote,
                    'status' => $crm_clientprog->stprog_status,
                    'empl_id' => $employee_v2->id ?? null,
                    'last_discuss_date' => $crm_clientprog->stprog_lastdisdate,
                    'followup_date' => $crm_clientprog->stprog_followupdate,
                    'meeting_date' => $crm_clientprog->stprog_meetingdate,
                    'statusprog_date' => $crm_clientprog->stprog_statusprogdate,
                    'negotiation_date' => $crm_clientprog->stprog_nego,
                    'price_from_tutor' => $crm_clientprog->stprog_price_from_tutor,
                    'our_price_tutor' => $crm_clientprog->stprog_our_price_tutor,
                    'total_price_tutor' => $crm_clientprog->stprog_total_price_tutor,
                    'duration_notes' => $crm_clientprog->stprog_duration,
                ];
    
                $success_date = $failed_date = $created_at = null;
                switch ($crm_clientprog->stprog_status) {
                    case 0: # pending
    
                        # and submitted prog_id is admission mentoring
                        if (in_array($progId, $this->admission_prog_list)) {
    
                            # add additional values
                            $clientProgramDetails['initconsult_date'] = $crm_clientprog->stprog_init_consult;
                            $clientProgramDetails['assessmentsent_date'] = $crm_clientprog->stprog_ass_sent;
                        } elseif (in_array($progId, $this->tutoring_prog_list)) {
    
                            # add additional values
                            $clientProgramDetails['trial_date'] = null;
                            $clientProgramDetails['created_at'] =  $crm_clientprog->stprog_statusprogdate;
                        }
                        break;
    
                    case 1: # success
    
                        # declare default variable
                        $clientProgramDetails['prog_running_status'] = $crm_clientprog->stprog_runningstatus;
    
                        # and submitted prog_id is admission mentoring
                        if (in_array($progId, $this->admission_prog_list)) {
    
                            # add additional values
                            $clientProgramDetails['success_date'] = $crm_clientprog->stprog_statusprogdate;
                            $clientProgramDetails['initconsult_date'] = $crm_clientprog->stprog_init_consult;
                            $clientProgramDetails['assessmentsent_date'] = $crm_clientprog->stprog_ass_sent;
                            $clientProgramDetails['prog_end_date'] = $crm_clientprog->stprog_end_date;
                            $clientProgramDetails['total_uni'] = $crm_clientprog->stprog_tot_uni;
                            $clientProgramDetails['total_foreign_currency'] = $crm_clientprog->stprog_tot_dollar;
                            $clientProgramDetails['foreign_currency'] = 'usd';
                            $clientProgramDetails['foreign_currency_exchange'] = $crm_clientprog->stprog_kurs;
                            $clientProgramDetails['total_idr'] = $crm_clientprog->stprog_tot_idr;
                            // $clientProgramDetails['main_mentor'] = $request->main_mentor;
                            // $clientProgramDetails['backup_mentor'] = $request->backup_mentor;
                            $clientProgramDetails['installment_notes'] = $crm_clientprog->stprog_install_plan;
                            $clientProgramDetails['prog_running_status'] = (int) $crm_clientprog->stprog_runningstatus;
                        } elseif (in_array($progId, $this->tutoring_prog_list)) {
    
                            # add additional values
                            $clientProgramDetails['success_date'] = $crm_clientprog->stprog_statusprogdate;
                            $clientProgramDetails['trial_date'] = null;
                            $clientProgramDetails['prog_start_date'] = $crm_clientprog->stprog_start_date;
                            $clientProgramDetails['prog_end_date'] = $crm_clientprog->stprog_end_date;
                            $clientProgramDetails['timesheet_link'] = null;
                            // $clientProgramDetails['tutor_id'] = $request->tutor_id;
                            $clientProgramDetails['prog_running_status'] = (int) $crm_clientprog->stprog_runningstatus;
                        } elseif (in_array($progId, $this->satact_prog_list)) {
    
                            # add additional values
                            $clientProgramDetails['success_date'] = $crm_clientprog->stprog_statusprogdate;
                            $clientProgramDetails['test_date'] = $crm_clientprog->stprog_test_date;
                            $clientProgramDetails['last_class'] = $crm_clientprog->stprog_last_class;
                            $clientProgramDetails['diag_score'] = $crm_clientprog->stprog_diag_score;
                            $clientProgramDetails['test_score'] = $crm_clientprog->stprog_test_score;
                            // $clientProgramDetails['tutor_1'] = $request->tutor_1;
                            // $clientProgramDetails['tutor_2'] = $request->tutor_2;
                            $clientProgramDetails['prog_running_status'] = (int) $crm_clientprog->stprog_runningstatus;
                        }
    
                        if (in_array($progId, $this->admission_prog_list)) {
                            
                            if (isset($crm_clientprog->hasMainMentor) && count($crm_clientprog->hasMainMentor) > 0) {
                                $crm_clientprog_mentor1name = $crm_clientprog->hasMainMentor[0]->mt_firstn.' '.$crm_clientprog->hasMainMentor[0]->mt_lastn;
                                $crm_clientprog_mentor1email = $crm_clientprog->hasMainMentor[0]->mt_email;
                                # check if main mentor is exists
                                if ($main_mentor_v2 = $this->userRepository->getUserByFullNameOrEmail($crm_clientprog_mentor1name, $crm_clientprog_mentor1email)) 
                                    $clientProgramDetails['main_mentor'] = $main_mentor_v2->id;
                            }

                            if (isset($crm_clientprog->hasBackupMentor) && count($crm_clientprog->hasBackupMentor) > 0) {
                                $crm_clientprog_mentor2name = $crm_clientprog->hasBackupMentor[0]->mt_firstn.' '.$crm_clientprog->hasBackupMentor[0]->mt_lastn;
                                $crm_clientprog_mentor2email = $crm_clientprog->hasBackupMentor[0]->mt_email;
                                # check if backup mentor is exists
                                if ($backup_mentor_v2 = $this->userRepository->getUserByFullNameOrEmail($crm_clientprog_mentor2name, $crm_clientprog_mentor2email)) 
                                    $clientProgramDetails['backup_mentor'] = $backup_mentor_v2->id;
                            }
                                
    
                        } elseif (in_array($progId, $this->tutoring_prog_list)) {
    
                            // $clientProgramDetails['tutor_id'] = null;
                        } elseif (in_array($progId, $this->satact_prog_list)) {
    
                            // $clientProgramDetails['tutor_1'] = null;
                            // $clientProgramDetails['tutor_2'] = null;
                        }
                        break;
    
                    case 2: # failed
                        
                        $clientProgramDetails['failed_date'] = $crm_clientprog->stprog_statusprogdate;
                        $clientProgramDetails['reason_id'] = $reason_v2->id ?? null;
                        $failed_date = $crm_clientprog->stprog_statusprogdate;
                        break;
                }
    
                # import client program to v2
                $clientprog_v2 = $this->clientProgramRepository->createClientProgram($clientProgramDetails);
    
                # import followup 
                if (isset($crm_clientprog->followUp) && count($crm_clientprog->followUp) > 0)
                {
                    $crm_followup = $crm_clientprog->followUp;
                    foreach ($crm_followup as $detail_flw)
                    {
                        $followupDetails = [
                            'clientprog_id' => $clientprog_v2->clientprog_id,
                            'followup_date' => $detail_flw->flw_date,
                            'status' => $detail_flw->flw_mark,
                            'notes' => $detail_flw->flw_notes == "" ? null : $detail_flw->flw_notes
                        ];
                        $followup_v2 = $this->followupRepository->createFollowup($followupDetails);
                    }
        
                }
            }

            DB::commit();
            Log::info('Import client program works fine');
            
        } catch (Exception $e) {
            
            DB::rollBack();
            $this->info($e->getMessage().' | line '.$e->getLine());
            Log::warning('Failed to import client program '. $e->getMessage());

        }

        return Command::SUCCESS;
    }
}
