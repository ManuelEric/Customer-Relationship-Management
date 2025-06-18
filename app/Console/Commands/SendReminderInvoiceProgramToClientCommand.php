<?php

namespace App\Console\Commands;

use App\Enum\LogModule;
use App\Http\Traits\CurrencyTrait;
use App\Interfaces\GeneralMailLogRepositoryInterface;
use App\Interfaces\InvoiceDetailRepositoryInterface;
use App\Interfaces\InvoiceProgramRepositoryInterface;
use App\Mail\Invoice\ReminderToClient as InvoiceReminderToClient;
use App\Mail\Invoice\ReportToFinanceTeam;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReminderInvoiceProgramToClientCommand extends Command
{
    use CurrencyTrait;
    private InvoiceProgramRepositoryInterface $invoiceProgramRepository;
    private InvoiceDetailRepositoryInterface $invoiceDetailRepository;
    private GeneralMailLogRepositoryInterface $generalMailLogRepository;
    private LogService $log_service;

    public function __construct(
        InvoiceProgramRepositoryInterface $invoiceProgramRepository, 
        InvoiceDetailRepositoryInterface $invoiceDetailRepository, 
        GeneralMailLogRepositoryInterface $generalMailLogRepository,
        LogService $log_service
        )
    {
        parent::__construct();
        $this->invoiceProgramRepository = $invoiceProgramRepository;
        $this->invoiceDetailRepository = $invoiceDetailRepository;
        $this->generalMailLogRepository = $generalMailLogRepository;
        $this->log_service = $log_service;
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:reminder_invoiceprogram';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder invoice to client. To remind the client to pay the invoice.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->cnHandleReminderEmail();

        return Command::SUCCESS;
    }

    public function cnHandleReminderEmail()
    {
        $timer_start = Carbon::now();
        $parents_have_no_email = [];
        $invoice_master = $this->invoiceProgramRepository->getAllDueDateInvoiceProgram(7);

        if (count($invoice_master) == 0) 
            return Command::SUCCESS;

        $progress_bar = $this->output->createProgressBar($invoice_master->count());
        $progress_bar->start();

        DB::beginTransaction();
        try {

            foreach ($invoice_master as $data) {
    
                // meaning that the document has already verified by manager and mark as `signed`
                // email will exclude those documents has not been signed 
                if ( $data->sign_status != 'signed')
                {
                    $this->newLine();
                    $this->info("Email not sent to {$data->parent_mail} because the sign status is null");
                    continue;
                }
    
                $invoice_id = $data->inv_id;
                $clientprog_id = $data->clientprog_id;
                $identifier = $data->identifier;
                $log_exist = $this->generalMailLogRepository->getStatus($invoice_id);
                $payment_method = $data->master_paymentmethod;
                
                $pic_email = $data->internalPic->email;
    
                $parent_fullname = $data->parent_fullname;
                $parent_mail = $data->parent_mail;
                $parent_phone = $data->parent_phone;
                if ($parent_mail === null) {
                    # collect data parents that have no email
                    $parents_have_no_email[] = [
                        'fullname' => $parent_fullname,
                        'mail' => $parent_mail,
                        'phone' => $parent_phone,
                    ];
                    continue;
                }
    
                try {
                    Mail::to($parent_mail, $parent_fullname)->cc([env('FINANCE_CC'), $pic_email])->queue(new InvoiceReminderToClient([
                        'invoice_id' => $invoice_id,
                        'parent_fullname' => $parent_fullname,
                        'parent_mail' => $parent_mail,
                        'program_name' => $data->invoice_program_name,
                        'due_date' => date('d/m/Y', strtotime($data->inv_duedate)),
                        'child_fullname' => $data->fullname,
                        'inv_paymentmethod' => $data->inv_paymentmethod,
                        'total_payment_other' => $data->currency != 'idr' ? $this->formatCurrency($data->currency, $data->inv_totalprice_idr, $data->inv_totalprice ?? 0) : 0,
                        'total_payment_idr' => $this->formatCurrency('idr', $data->inv_totalprice_idr, $data->inv_totalprice ?? 0),
                        'pic_email' => $data->internalPic->email,
                        'currency' => $data->currency,
                    ]));
                } catch (Exception $e) {
    
                    $this->log_service->createErrorLog(LogModule::REMINDER_INVOICE_PROGRAM_TO_CLIENT, $e->getMessage(), $e->getLine(), $e->getFile(), []);
                    Log::error("[CRON - SEND REMINDER INVOICE PROGRAM TO CLIENT] Email to {$parent_mail} failed.");
                    $this->error($e->getMessage() . ' | Line ' . $e->getLine());
                    return Command::FAILURE;
                }
                Log::info("[CRON - SEND REMINDER INVOICE PROGRAM TO CLIENT] Email has been sent to {$parent_mail}.");
    
                $this->newLine();
                $this->info('Invoice reminder has been sent to ' . $parent_mail);
    
                # remove from mail log if the identifier mail has been successfully sent
                if ($log_exist)
                    $this->generalMailLogRepository->removeLog($invoice_id);
                
    
                $progress_bar->advance();
            }
    
            if (count($parents_have_no_email) > 0 && !$log_exist) {
    
                try {
    
                    Mail::to(env('FINANCE_CC'), env('FINANCE_NAME'))->queue(new ReportToFinanceTeam([
                        'view' => 'pages.invoice.client-program.mail.reminder-finance',
                        'with' => [
                            'finance_name' => env('FINANCE_NAME'),
                            'parents_have_no_email' => $parents_have_no_email,
                        ]
                    ]));
    
                    # create mail log
                    $this->generalMailLogRepository->createLog([
                        'identifier' => $invoice_id,
                        'category' => 'invoice',
                        'target' => 'client',
                        'description' => json_encode([
                            'finance_name' => env('FINANCE_NAME'),
                            'parents_have_no_email' => $parents_have_no_email,
                        ])
                    ]);
    
                } catch (Exception $e) {
                    
                    $this->log_service->createErrorLog(LogModule::REPORT_INVOICE_TO_FINANCE_TEAM, $e->getMessage(), $e->getLine(), $e->getFile(), []);
                    $this->error($e->getMessage() . ' | Line ' . $e->getLine());
                    return Command::FAILURE;
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("[CRON - SEND REMINDER INVOICE PROGRAM TO CLIENT] Email reminder has failure. Error: {$e->getMessage()} on file {$e->getFile()} line {$e->getLine()}");
            return Command::FAILURE;
        }


        $progress_bar->finish();
    
        
        $timer_end = Carbon::now();
        $progress_time = $timer_start->diffInSeconds($timer_end);
        
        Log::info("[CRON - SEND REMINDER INVOICE PROGRAM TO CLIENT] works. It tooks {$progress_time} seconds");
        return Command::SUCCESS;
    }
}
