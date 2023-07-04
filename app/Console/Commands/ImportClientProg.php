<?php

namespace App\Console\Commands;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\FollowupRepositoryInterface;
use App\Interfaces\InvoiceDetailRepositoryInterface;
use App\Interfaces\InvoiceProgramRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Interfaces\MainProgRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\ReasonRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Interfaces\RefundRepositoryInterface;
use App\Interfaces\SubProgRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Lead;
use App\Models\Program;
use App\Models\School;
use App\Models\UserClient;
use App\Models\v1\Student;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Terbilang;

class ImportClientProg extends Command
{
    use CreateCustomPrimaryKeyTrait;
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
    protected InvoiceProgramRepositoryInterface $invoiceProgramRepository;
    protected InvoiceDetailRepositoryInterface $invoiceDetailRepository;
    protected ReceiptRepositoryInterface $receiptRepository;
    protected RefundRepositoryInterface $refundRepository;
    protected MainProgRepositoryInterface $mainProgRepository;
    protected SubProgRepositoryInterface $subProgRepository;

    public function __construct(ClientRepositoryInterface $clientRepository, ClientProgramRepositoryInterface $clientProgramRepository, ProgramRepositoryInterface $programRepository, LeadRepositoryInterface $leadRepository, ReasonRepositoryInterface $reasonRepository, UserRepositoryInterface $userRepository, FollowupRepositoryInterface $followupRepository, InvoiceProgramRepositoryInterface $invoiceProgramRepository, InvoiceDetailRepositoryInterface $invoiceDetailRepository, ReceiptRepositoryInterface $receiptRepository, RefundRepositoryInterface $refundRepository, MainProgRepositoryInterface $mainProgRepository, SubProgRepositoryInterface $subProgRepository)
    {
        parent::__construct();
        $this->clientRepository = $clientRepository;
        $this->clientProgramRepository = $clientProgramRepository;
        $this->programRepository = $programRepository;
        $this->leadRepository = $leadRepository;
        $this->reasonRepository = $reasonRepository;
        $this->userRepository = $userRepository;
        $this->followupRepository = $followupRepository;
        $this->invoiceProgramRepository = $invoiceProgramRepository;
        $this->invoiceDetailRepository = $invoiceDetailRepository;
        $this->receiptRepository = $receiptRepository;
        $this->refundRepository = $refundRepository;
        $this->mainProgRepository = $mainProgRepository;
        $this->subProgRepository =  $subProgRepository;

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
            $progressBar = $this->output->createProgressBar($crm_clientprogs->count());
            $progressBar->start();
            foreach ($crm_clientprogs as $crm_clientprog) {

                # get the student id on database v2 using name
                if (!isset($crm_clientprog->student->st_firstname))
                    continue; # st num 146 not found in table students v1 deleted soon

                $crm_clientprog_student = $crm_clientprog->student;
                $crm_student_id = $studentId = $crm_clientprog_student->st_id;
                $crm_student_name = $crm_clientprog_student->st_firstname . ' ' . $crm_clientprog_student->st_lastname;

                # when the student hasn't be registered in the database v2
                # then store as a new student
                if (!$student_v2 = $this->clientRepository->getStudentByStudentName($crm_student_name)) {
                    $this->info('Please run the import:student function');
                }

                $student_v2_id = $student_v2->id;

                $progId = $this->createProgIfNotExists($crm_clientprog);

                $lead_v2 = $this->createLeadIfNotExists($crm_clientprog);

                $reason_v2 = $this->createReasonIfNotExists($crm_clientprog);

                # check if empl id is exists
                if ($crm_clientprog->pic != "") {
                    $crm_clientprog_emplname = $crm_clientprog->pic->empl_firstname . ' ' . $crm_clientprog->pic->empl_lastname;
                    $crm_clientprog_emplmail = $crm_clientprog->pic->empl_email;
                    if (!$employee_v2 = $this->userRepository->getUserByFullNameOrEmail($crm_clientprog_emplname, $crm_clientprog_emplmail))
                        throw new Exception('Could not find employee.');
                }

                $clientprog_v2 = $this->createClientProgramIfNotExists($student_v2_id, $progId, $lead_v2, $crm_clientprog, $employee_v2, $reason_v2);

                $this->createFollowupIfNotExists($crm_clientprog, $clientprog_v2);

                $this->createInvoiceIfNotExists($crm_clientprog, $clientprog_v2);

                $progressBar->advance();
            }

            $progressBar->finish();

            DB::commit();
            Log::info('Import client program works fine');
        } catch (Exception $e) {

            DB::rollBack();
            $this->info($e->getMessage() . ' | line ' . $e->getLine());
            Log::warning('Failed to import client program ' . $e->getMessage() . ' | line ' . $e->getLine() . ' | ' . $e->getTraceAsString());
        }

        return Command::SUCCESS;
    }

    private function createProgIfNotExists($crm_clientprog)
    {
        # check if prog id is exists
        $crm_clientprog_progid = $crm_clientprog->prog_id;

        if (!$program_v2 = $this->programRepository->getProgramById($crm_clientprog_progid)) {

            if (!$main_prog = $this->mainProgRepository->getMainProgByName($crm_clientprog->program->prog_main)) {
                $mainProgDetails = [
                    'prog_name' => $crm_clientprog->program->prog_main,
                    'prog_status' => 1
                ];

                $main_prog = $this->mainProgRepository->createMainProg($mainProgDetails);
            }

            $programDetails = [
                'prog_id' => $crm_clientprog_progid,
                'main_prog_id' => $main_prog->id, //!
                'sub_prog_id' => null, //!
                'prog_main' => $crm_clientprog->program->prog_main,
                'main_number' => $crm_clientprog->program->main_number,
                'prog_sub' => $crm_clientprog->program->prog_sub,
                'prog_program' => $crm_clientprog->program->prog_program,
                'prog_type' => $crm_clientprog->program->prog_type,
                'prog_mentor' => $crm_clientprog->program->prog_mentor,
                'prog_payment' => $crm_clientprog->program->prog_payment,
            ];

            if ($crm_clientprog->program->prog_sub != "" && $crm_clientprog->program->prog_sub != NULL) {
                if (!$sub_prog = $this->subProgRepository->getSubProgBySubProgName($crm_clientprog->program->prog_sub)) {
                    $subProgDetails = [
                        'main_prog_id' => $main_prog->id,
                        'sub_prog_name' => $crm_clientprog->program->prog_sub,
                        'sub_prog_status' => 1,
                    ];

                    $sub_prog = $this->subProgRepository->createSubProg($subProgDetails);

                    $programDetails['sub_prog_id'] = $sub_prog->id;
                }
            }

            $program_v2 = $this->programRepository->createProgramFromV1($programDetails);
        }

        return $program_v2->prog_id;
    }

    private function createLeadIfNotExists($crm_clientprog)
    {
        # check if lead id is exists
        $crm_clientprog_leadname = $crm_clientprog->lead->lead_name;
        if (!$lead_v2 = $this->leadRepository->getLeadByName($crm_clientprog_leadname)) {
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

        return $lead_v2;
    }

    private function createReasonIfNotExists($crm_clientprog)
    {
        # check if reason id is exists
        if ($crm_clientprog->reason != NULL) {
            $crm_clientprog_reasonname = $crm_clientprog->reason->reason_name;
            if (!$reason_v2 = $this->reasonRepository->getReasonByReasonName($crm_clientprog_reasonname)) {
                $reasonDetails = [
                    'reason_name' => $crm_clientprog_reasonname

                ];

                $reason_v2 = $this->reasonRepository->createReason($reasonDetails);
            }

            return $reason_v2;
        }
    }

    private function createClientProgramIfNotExists($student_v2_id, $progId, $lead_v2, $crm_clientprog, $employee_v2, $reason_v2)
    {
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
            'created_at' => $crm_clientprog->stprog_firstdisdate,
            'updated_at' => $crm_clientprog->stprog_firstdisdate
        ];

        # additional
        $clientProgramDetails['total_uni'] = $crm_clientprog->stprog_tot_uni;
        $clientProgramDetails['total_foreign_currency'] = $crm_clientprog->stprog_tot_dollar;
        $clientProgramDetails['foreign_currency'] = 'usd';
        $clientProgramDetails['foreign_currency_exchange'] = $crm_clientprog->stprog_kurs;
        $clientProgramDetails['total_idr'] = $crm_clientprog->stprog_tot_idr;
        $clientProgramDetails['installment_notes'] = $crm_clientprog->stprog_install_plan;

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

                # check if he/she already mentee
                if (!$this->clientRepository->checkIfClientIsMentee($student_v2_id)) {
                    # if he/she join admission mentoring program
                    # add role mentee
                    if (in_array($progId, $this->admission_prog_list)) {
                        $this->clientRepository->addRole($student_v2_id, 'Mentee');
                    }
                }

                # declare default variable
                $clientProgramDetails['prog_running_status'] = $crm_clientprog->stprog_runningstatus;
                $clientProgramDetails['success_date'] = $crm_clientprog->stprog_statusprogdate;
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
                        $crm_clientprog_mentor1name = $crm_clientprog->hasMainMentor[0]->mt_firstn . ' ' . $crm_clientprog->hasMainMentor[0]->mt_lastn;
                        $crm_clientprog_mentor1email = $crm_clientprog->hasMainMentor[0]->mt_email;
                        # check if main mentor is exists
                        if ($main_mentor_v2 = $this->userRepository->getUserByFullNameOrEmail($crm_clientprog_mentor1name, $crm_clientprog_mentor1email))
                            $clientProgramDetails['main_mentor'] = $main_mentor_v2->id;
                    }

                    if (isset($crm_clientprog->hasBackupMentor) && count($crm_clientprog->hasBackupMentor) > 0) {
                        $crm_clientprog_mentor2name = $crm_clientprog->hasBackupMentor[0]->mt_firstn . ' ' . $crm_clientprog->hasBackupMentor[0]->mt_lastn;
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

        # check the st id 
        $client = $this->clientRepository->getClientById($student_v2_id);

        if ($this->clientRepository->getStudentByStudentId($client->st_id) || $client->st_id == NULL) {
            # initialize
            $last_id = Student::max('st_id');
            $student_id_without_label = $this->remove_primarykey_label($last_id, 3);
            $studentId = 'ST-' . $this->add_digit((int) $student_id_without_label + 1, 4);

            if ($this->clientRepository->getStudentByStudentId($studentId)) {
                # initialize
                $last_id = UserClient::max('st_id');
                $student_id_without_label = $this->remove_primarykey_label($last_id, 3);
                $studentId = 'ST-' . $this->add_digit((int) $student_id_without_label + 1, 4);
            }
            
            $this->clientRepository->updateClient($student_v2_id, ['st_id' => $studentId]);
        }


        # import client program to v2
        $clientprog_v2 = $this->clientProgramRepository->createClientProgram($clientProgramDetails);

        return $clientprog_v2;
    }

    private function createFollowupIfNotExists($crm_clientprog, $clientprog_v2)
    {
        # import followup 
        if (isset($crm_clientprog->followUp) && count($crm_clientprog->followUp) > 0) {
            $crm_followup = $crm_clientprog->followUp;
            foreach ($crm_followup as $detail_flw) {
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

    private function createInvoiceIfNotExists($crm_clientprog, $clientprog_v2)
    {
        # import invoice program to v2
        $crm_clientprog_invoice = $crm_clientprog->invoice;
        if ($crm_clientprog_invoice) {
            $crm_invoiceId = $crm_clientprog_invoice->inv_id;
            if (!$invoice_v2 = $this->invoiceProgramRepository->getInvoiceByInvoiceId($crm_invoiceId)) {
                $inv_words = $crm_clientprog_invoice->inv_wordsusd;
                if ($crm_clientprog_invoice->inv_wordsusd == "") {
                    $inv_words = Terbilang::make($crm_clientprog_invoice->inv_totprusd, ' dollars');
                }

                # it supposed to be : one hundred and fifty-three thousand, two hundred
                # so we removed the and & commas
                $inv_words = str_replace(' and', '', $inv_words);
                $inv_words = str_replace(',', '', $inv_words);

                $curs_rate = $crm_clientprog->stprog_kurs;
                # when curs rate from v1 is 0
                # then create using totprice_idr/totprice_usd
                if ($crm_clientprog->stprog_kurs == 0 || $crm_clientprog->stprog_kurs == "" || $crm_clientprog->stprog_kurs == NULL) {
                    if ($crm_clientprog_invoice->inv_totprusd != 0)
                        $curs_rate = $crm_clientprog_invoice->inv_totpridr / $crm_clientprog_invoice->inv_totprusd;
                    else
                        $curs_rate = $curs_rate;
                }

                $invoiceDetails = [
                    'inv_id' => $crm_clientprog_invoice->inv_id,
                    'clientprog_id' => $clientprog_v2->clientprog_id,
                    'inv_category' => $crm_clientprog_invoice->inv_category == "usd" ? "Other" : $crm_clientprog_invoice->inv_category,
                    'inv_price' => $crm_clientprog_invoice->inv_priceusd,
                    'inv_earlybird' => $crm_clientprog_invoice->inv_earlybirdusd,
                    'inv_discount' => $crm_clientprog_invoice->inv_discusd,
                    'inv_totalprice' => $crm_clientprog_invoice->inv_totprusd,
                    'inv_words' => $inv_words,
                    'inv_price_idr' => $crm_clientprog_invoice->inv_priceidr,
                    'inv_earlybird_idr' => $crm_clientprog_invoice->inv_earlybirdidr,
                    'inv_discount_idr' => $crm_clientprog_invoice->inv_discidr,
                    'inv_totalprice_idr' => $crm_clientprog_invoice->inv_totpridr,
                    'inv_words_idr' => $crm_clientprog_invoice->inv_words,
                    'session' => $crm_clientprog_invoice->inv_session,
                    'duration' => $crm_clientprog_invoice->inv_duration,
                    'inv_paymentmethod' => $crm_clientprog_invoice->inv_paymentmethod,
                    'inv_duedate' => $crm_clientprog_invoice->inv_duedate,
                    'inv_notes' => $crm_clientprog_invoice->inv_notes == "" ? NULL : $crm_clientprog_invoice->inv_notes,
                    'inv_tnc' => $crm_clientprog_invoice->inv_tnc == "" ? NULL : $crm_clientprog_invoice->inv_tnc,
                    'inv_status' => $crm_clientprog_invoice->inv_status,
                    'curs_rate' => $curs_rate, # take the value from stprog
                    'currency' => $crm_clientprog_invoice->inv_category == "session" ? $this->checkCurrencyBasedOnPrice($crm_clientprog_invoice->inv_totprusd) : $crm_clientprog_invoice->inv_category,
                    'created_at' => $crm_clientprog_invoice->inv_date,
                    'updated_at' => $crm_clientprog_invoice->inv_date
                ];

                $invoice_v2 = $this->invoiceProgramRepository->createInvoice($invoiceDetails);
                // $this->info('Invoice stored : '.$invoice_v2->inv_id);

                # if the invoice has some installments
                // $this->info('Ini installment : '.$crm_clientprog_invoice->installment);
                if (count($crm_clientprog_invoice->installment) > 0) {
                    $crm_clientprog_installment = $crm_clientprog_invoice->installment;
                    $installmentDetails = [];
                    foreach ($crm_clientprog_installment as $installment) {
                        $installmentDetails = [
                            'inv_id' => $invoice_v2->inv_id,
                            'invdtl_installment' => $installment->invdtl_statusname,
                            'invdtl_duedate' => $installment->invdtl_duedate,
                            'invdtl_percentage' => $installment->invdtl_percentage,
                            'invdtl_amount' => $installment->invdtl_amountusd,
                            'invdtl_amountidr' => $installment->invdtl_amountidr,
                            'invdtl_status' => $installment->invdtl_status == "" ? 0 : $installment->invdtl_status,
                            'invdtl_cursrate' => $crm_clientprog->stprog_kurs, # take the value from stprog
                            'invdtl_currency' => $crm_clientprog_invoice->inv_category,
                        ];

                        # import invoice installment to v2
                        $installment_v2 = $this->invoiceDetailRepository->createOneInvoiceDetail($installmentDetails);
                        // $this->info('Installment stored : '.json_encode($installment_v2));

                        if (isset($installment->receipt)) {
                            $receiptDetails = [
                                'receipt_id' => $installment->receipt->receipt_id,
                                'receipt_cat' => 'student',
                                'inv_id' => $invoice_v2->inv_id,
                                'receipt_method' => $installment->receipt->receipt_mtd,
                                'receipt_cheque' => $installment->receipt->receipt_cheque == "" ? NULL : $installment->receipt->receipt_cheque,
                                'receipt_amount' => $installment->receipt->receipt_amountusd,
                                'receipt_words' => $installment->receipt->receipt_wordsusd,
                                'receipt_amount_idr' => $installment->receipt->receipt_amount,
                                'receipt_words_idr' => $installment->receipt->receipt_word,
                                'receipt_notes' => $installment->receipt->receipt_notes == "" ? NULL : $installment->receipt->receipt_notes,
                                'receipt_status' => $installment->receipt->receipt_status,
                                'invdtl_id' => $installment_v2->invdtl_id,
                                'created_at' => $installment->receipt->receipt_date,
                                'updated_at' => $installment->receipt->receipt_date
                            ];

                            # import receipt installment to v2
                            if ($this->receiptRepository->getReceiptByReceiptId($installment->receipt->receipt_id)) {
                                $receiptDetails['receipt_id'] = $installment->receipt->receipt_id . '-1';
                            }

                            $receipt_v2 = $this->receiptRepository->createReceipt($receiptDetails);
                            // $this->info('Receipt installment stored : '.$receipt_v2->receipt_id);

                            # check if the receipt has refund
                            if ($installment->receipt->receipt_status == 2 && $installment->receipt->receipt_refund != 0) {

                                $total_paid = $installment->receipt->receipt_amount;
                                $refund_amount = $installment->receipt->receipt_refund;
                                $percentage_refund = ($refund_amount / $total_paid) * 100;

                                $refundDetails = [
                                    'inv_id' => $invoice_v2->inv_id,
                                    'total_payment' => $invoice_v2->inv_totalprice_idr,
                                    'total_paid' => $total_paid,
                                    'refund_amount' => $refund_amount,
                                    'percentage_refund' => $percentage_refund,
                                    'tax_amount' => 0,
                                    'tax_percentage' => 0,
                                    'total_refunded' => $refund_amount,
                                    'status' => 1, # //?
                                ];

                                $refund_v2 = $this->refundRepository->createRefund($refundDetails);
                                // $this->info('Refund installment stored : '.$refund_v2->id);

                            }
                        }
                    }
                }

                # if the invoice has receipt
                if ($crm_clientprog_invoice->receipt && count($crm_clientprog_invoice->installment) == 0) {
                    $crm_clientprog_receipt = $crm_clientprog_invoice->receipt;
                    foreach ($crm_clientprog_receipt as $crm_receipt) {
                        // $this->info('ini Receipt master : '.$crm_receipt);
                        # store into receipt v2
                        $receiptDetails = [
                            'receipt_id' => $crm_receipt->receipt_id,
                            'receipt_cat' => 'student',
                            'inv_id' => $invoice_v2->inv_id,
                            'receipt_method' => $crm_receipt->receipt_mtd,
                            'receipt_cheque' => $crm_receipt->receipt_cheque == "" ? NULL : $crm_receipt->receipt_cheque,
                            'receipt_amount' => $crm_receipt->receipt_amountusd,
                            'receipt_words' => $crm_receipt->receipt_wordsusd,
                            'receipt_amount_idr' => $crm_receipt->receipt_amount,
                            'receipt_words_idr' => $crm_receipt->receipt_word,
                            'receipt_notes' => $crm_receipt->receipt_notes == "" ? NULL : $crm_receipt->receipt_notes,
                            'receipt_status' => $crm_receipt->receipt_status,
                            'created_at' => $crm_receipt->receipt_date,
                            'updated_at' => $crm_receipt->receipt_date
                        ];

                        $receipt_v2 = $this->receiptRepository->createReceipt($receiptDetails);

                        # check if the receipt has refund
                        if ($crm_receipt->receipt_status == 2 && $crm_receipt->receipt_refund != 0) {

                            $total_paid = $crm_receipt->receipt_amount;
                            $refund_amount = $crm_receipt->receipt_refund;
                            $percentage_refund = ($refund_amount / $total_paid) * 100;

                            $refundDetails = [
                                'inv_id' => $receipt_v2->inv_id,
                                'total_payment' => $invoice_v2->inv_totalprice_idr,
                                'total_paid' => $total_paid,
                                'refund_amount' => $refund_amount,
                                'percentage_refund' => $percentage_refund,
                                'tax_amount' => 0,
                                'tax_percentage' => 0,
                                'total_refunded' => $refund_amount,
                                'status' => 1, # //?
                            ];

                            $this->refundRepository->createRefund($refundDetails);
                        }
                    }
                }
            }
        }
    }

    private function checkCurrencyBasedOnPrice($totprice_other)
    {
        if ($totprice_other == NULL)
            $currency = "usd";
        else
            $currency = "idr";

        return $currency;
    }
}
