<?php

namespace App\Services\Program;

use App\Interfaces\ClientProgramLogMailRepositoryInterface;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ClientProgramService 
{
    protected ClientRepositoryInterface $clientRepository;
    protected ClientProgramLogMailRepositoryInterface $clientProgramLogMailRepository;
    protected ClientProgramRepositoryInterface $clientProgramRepository;

    public function __construct(ClientProgramLogMailRepositoryInterface $clientProgramLogMailRepository, ClientRepositoryInterface $clientRepository, ClientProgramRepositoryInterface $clientProgramRepository)
    {
        $this->clientProgramLogMailRepository = $clientProgramLogMailRepository;
        $this->clientRepository = $clientRepository;
        $this->clientProgramRepository = $clientProgramRepository;
    }

    public function snSetFilterDataIndex(Request $request)
    {
        $data = $status = $empl_uuid = [];
        $status = $user_id = null;

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
                $raw_user_id = Crypt::decrypt($request->get('mentor_tutor')[$i]);
                $user_id[] = $raw_user_id;
            }
        }
        $data['userId'] = $user_id;

        if ($request->get('pic')) {
            for ($i = 0; $i < count($request->get('pic')); $i++) {
                $empl_uuid[] = $request->get('pic')[$i];
            }
        }
        $data['emplUUID'] = array_filter($empl_uuid, fn ($value) => !is_null($value)) ?? null;;
        $data['startDate'] = $request->get('start_date') ?? null;
        $data['endDate'] = $request->get('end_date') ?? null;

        return $data;
    }

    public function snMappingLeads($leads, $type)
    {
        $leads = $leads->map(function ($item) use($type) {
            return [
                'lead_id' => $item->lead_id,
                'main_lead' => $type == 'main_lead' ? $item->main_lead : $item->sub_lead
            ];
        });

        return $leads;
    }

    public function snSetAttributeLead($clientProgramDetails)
    {
        switch ($clientProgramDetails['lead_id']) {

            case "LS004": #All-In Event
                $clientProgramDetails['eduf_lead_id'] = null;
                $clientProgramDetails['partner_id'] = null;
                break;

            case "LS010": #ALL-In Partners
                $clientProgramDetails['eduf_lead_id'] = null;
                $clientProgramDetails['clientevent_id'] = null;
                break;

            case "LS018": #External Edufair
                $clientProgramDetails['partner_id'] = null;
                $clientProgramDetails['clientevent_id'] = null;
                break;

            case "kol": #KOL
                $clientProgramDetails['partner_id'] = null;
                $clientProgramDetails['clientevent_id'] = null;
                break;
        }

        # set lead_id based on lead_id & kol_lead_id
        # when lead_id is kol
        # then put kol_lead_id to lead_id
        # otherwise
        # when lead_id is not kol 
        # then lead_id is lead_id
        if ($clientProgramDetails['lead_id'] == "kol") {

            unset($clientProgramDetails['lead_id']);
            $clientProgramDetails['lead_id'] = $clientProgramDetails['kol_lead_id'];
        }

        if ( !in_array($clientProgramDetails['lead_id'], ['LS005', 'LS058', 'LS060', 'LS061']) ) # Referral
        {
            $clientProgramDetails['referral_code'] = null;
        }

        return $clientProgramDetails;
    }

    public function snSetAdditionalAttributes($request, array $prog_list, $student, $clientProgramDetails, $is_update_method = false)
    {
        $file_path = null;

        switch ($request->status) {

            # when program status is pending
            case 0:

                # and submitted prog_id is admission mentoring
                if (in_array($request->prog_id, $prog_list['admission'])) {

                    # add additional values
                    $clientProgramDetails['initconsult_date'] = $request->pend_initconsult_date;
                    $clientProgramDetails['assessmentsent_date'] = $request->pend_assessmentsent_date;
                    $clientProgramDetails['mentor_ic'] = $request->pend_mentor_ic;
                } elseif (in_array($request->prog_id, $prog_list['tutoring'])) {

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
                if (in_array($request->prog_id, $prog_list['admission'])) {

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

                    # for method update
                    if ($is_update_method)
                        $clientProgramDetails['supervising_mentor'] = $request->supervising_mentor;
                        $clientProgramDetails['profile_building_mentor'] = isset($request->profile_building_mentor) ? $request->profile_building_mentor : NULL;
                        $clientProgramDetails['subject_specialist_mentor'] = isset($request->subject_specialist_mentor) ? $request->subject_specialist_mentor : NULL;
                        $clientProgramDetails['aplication_strategy_mentor'] = isset($request->aplication_strategy_mentor) ? $request->aplication_strategy_mentor : NULL;
                        $clientProgramDetails['writing_mentor'] = isset($request->writing_mentor) ? $request->writing_mentor : NULL;                    
                    
                    # declare the variables for agreement
                    if ($request->hasFile('agreement')) {

                        # setting up the agreement request file
                        $file_name = "agreement_".str_replace(' ', '_', trim($student->full_name))."_".$request->prog_id;
                        $file_format = $request->file('agreement')->getClientOriginalExtension();
                        
                        # generate the file path
                        $file_path = $file_name.'.'.$file_format;

                        if (Storage::exists('public/uploaded_file/agreement/'.$file_path)) {
                            Storage::delete('public/uploaded_file/agreement/'.$file_path);
                        }

                        if (!$request->file('agreement')->storeAs('public/uploaded_file/agreement', $file_path))
                            throw new Exception('The file cannot be uploaded.');

                    }

                } elseif (in_array($request->prog_id, $prog_list['tutoring'])) {

                    # add additional values
                    $clientProgramDetails['success_date'] = $request->success_date;
                    $clientProgramDetails['trial_date'] = $request->trial_date;
                    // $clientProgramDetails['first_class'] = $request->first_class;
                    $clientProgramDetails['prog_start_date'] = $request->prog_start_date;
                    $clientProgramDetails['prog_end_date'] = $request->prog_end_date;
                    $clientProgramDetails['timesheet_link'] = $request->timesheet_link;
                    // $clientProgramDetails['tutor_id'] = $request->tutor_id;
                    $clientProgramDetails['prog_running_status'] = (int) $request->prog_running_status;
                } elseif (in_array($request->prog_id, $prog_list['satact'])) {
                    
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

                if (in_array($request->prog_id, $prog_list['admission'])) {

                    $clientProgramDetails['supervising_mentor'] = $request->supervising_mentor;
                    $clientProgramDetails['profile_building_mentor'] = isset($request->profile_building_mentor) ? $request->profile_building_mentor : NULL;
                    $clientProgramDetails['subject_specialist_mentor'] = isset($request->subject_specialist_mentor) ? $request->subject_specialist_mentor : NULL;
                    $clientProgramDetails['aplication_strategy_mentor'] = isset($request->aplication_strategy_mentor) ? $request->aplication_strategy_mentor : NULL;
                    $clientProgramDetails['writing_mentor'] = isset($request->writing_mentor) ? $request->writing_mentor : NULL;
                    $clientProgramDetails['mentor_ic'] = $request->mentor_ic;
                } elseif (in_array($request->prog_id, $prog_list['tutoring'])) {

                    $clientProgramDetails['tutor_id'] = $request->tutor_id;

                    # if session tutor form doesn't exist then don't detail session tutor
                    if (isset($request->session)) {

                        $clientProgramDetails['session_tutor'] = $request->session; // how many session will applied
                        $clientProgramDetails['session_tutor_detail'] = [
                            'datetime' => $request->sessionDetail,
                            'linkmeet' => $request->sessionLinkMeet
                        ];
                    }

                } elseif (in_array($request->prog_id, $prog_list['satact'])) {

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
                $clientProgramDetails['reason_notes'] = $request->reason_notes;

                break;

                # when program status is refund
            case 3:
                $clientProgramDetails['refund_date'] = $request->refund_date;
                $clientProgramDetails['refund_notes'] = $request->refund_notes;
                $clientProgramDetails['reason_id'] = $request->reason_id;
                $clientProgramDetails['other_reason'] = $request->other_reason;
                $clientProgramDetails['reason_notes'] = $request->reason_notes;
                break;

                # when program status is hold
            case 4:
                $clientProgramDetails['reason_id'] = $request->reason_id;
                $clientProgramDetails['other_reason'] = $request->other_reason;
                $clientProgramDetails['hold_date'] = Carbon::now();
                break;
        }

        return ['file_path' => $file_path, 'client_program_details' => $clientProgramDetails];
    }

    # Purpose:
    # set mail data (recipient: name, email and children details)
    # send thanks mail registration
    # insert log mail
    public function snSendMailThanks(Collection $clientProgram, int $parentId, int $childId, bool $update = false)
    {
        $subject_mail = 'Your registration is confirmed';
        $mail_resources = 'mail-template.thanks-email-program';

        $parent = $this->clientRepository->getClientById($parentId);
        $children = $this->clientRepository->getClientById($childId);
        
        $recipient_details = [
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
            Mail::send($mail_resources, ['client' => $recipient_details, 'program' => $program], function ($message) use ($subject_mail, $recipient_details) {
                $message->to($recipient_details['mail'], $recipient_details['name'])
                    ->subject($subject_mail);
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

        $log_details = [
            'clientprog_id' => $clientProgram->clientprog_id,
            'sent_status' => $sent_mail
        ];

        return $this->clientProgramLogMailRepository->createClientProgramLogMail($log_details);
    }

    public function snAddOrRemoveRoleMentee($prog_id, $student_id, $admission_prog_list, $status, $is_method_update = false)
    {
        if (in_array($prog_id, $admission_prog_list)) {
            switch ($status) {
                case 0: # pending
                case 2: # failed
                case 3: # refund
                    if($is_method_update)
                        $this->clientRepository->removeRole($student_id, 'Mentee');
                    break;
                case 1: # success
                    $this->clientRepository->addRole($student_id, 'Mentee');
                    break;
            }
        }
    }

    public function snSetDataBundleProgramBeforeCreate(Request $request, Array $client_program, Array $client_program_details, String $uuid)
    {
        foreach ($request->choosen as $key => $clientprog_id) {
            // fetch data client program
            $clientprog_db = $this->clientProgramRepository->getClientProgramById($clientprog_id);
            
            // check there is an invoice 
            $has_invoice_std = isset($clientprog_db->invoice) ? $clientprog_db->invoice()->count() : 0;
            $has_bundling = isset($clientprog_db->bundlingDetail) ? $clientprog_db->bundlingDetail()->count() : 0;

            $client_program[$request->number[$key]] = [
                'clientprog_id' => $clientprog_id,
                'status' => $clientprog_db->status,
                'program' => $clientprog_db->prog_id,
                'HasInvoice' => $has_invoice_std,
                'HasBundling' => $has_bundling,
            ];
            
            $client_program_details[] = [
                'clientprog_id' => $clientprog_id,
                'bundling_id' => $uuid,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        
        return ['client_program' => $client_program, 'client_program_details' => $client_program_details];
    }

    public function snSetDataBundleProgramBeforeDelete(Request $request, Array $client_program)
    {
        foreach ($request->choosen as $key => $clientprog_id) {
            // fetch data client program
            $clientprog_db = $this->clientProgramRepository->getClientProgramById($clientprog_id);
            
            // check there is an invoice 
            $has_invoice_std = isset($clientprog_db->invoice) ? $clientprog_db->invoice()->count() : 0;
           
            $has_bundling = isset($clientprog_db->bundlingDetail) ? $clientprog_db->bundlingDetail()->count() : 0;

            $client_program[$request->number[$key]] = [
                'clientprog_id' => $clientprog_id,
                'status' => $clientprog_db->status,
                'HasInvoice' => $has_invoice_std,
                'HasBundling' => $has_bundling,
            ];
            
        }

        return ['client_program' => $client_program];
    }
}