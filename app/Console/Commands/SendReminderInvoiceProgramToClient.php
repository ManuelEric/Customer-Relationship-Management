<?php

namespace App\Console\Commands;

use App\Interfaces\GeneralMailLogRepositoryInterface;
use App\Interfaces\InvoiceDetailRepositoryInterface;
use App\Interfaces\InvoiceProgramRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReminderInvoiceProgramToClient extends Command
{

    private InvoiceProgramRepositoryInterface $invoiceProgramRepository;
    private InvoiceDetailRepositoryInterface $invoiceDetailRepository;
    private GeneralMailLogRepositoryInterface $generalMailLogRepository;

    public function __construct(InvoiceProgramRepositoryInterface $invoiceProgramRepository, InvoiceDetailRepositoryInterface $invoiceDetailRepository, GeneralMailLogRepositoryInterface $generalMailLogRepository)
    {
        parent::__construct();
        $this->invoiceProgramRepository = $invoiceProgramRepository;
        $this->invoiceDetailRepository = $invoiceDetailRepository;
        $this->generalMailLogRepository = $generalMailLogRepository;
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
        $this->handleReminderEmail();

        return Command::SUCCESS;
    }

    public function handleReminderEmail()
    {
        Log::info('Send reminder invoice program to client works fine');
        $parents_have_no_email = [];
        $invoice_master = $this->invoiceProgramRepository->getAllDueDateInvoiceProgram(7);

        if (count($invoice_master) > 0) {

            $progressBar = $this->output->createProgressBar($invoice_master->count());
            $progressBar->start();

            foreach ($invoice_master as $data) {

                $invoiceId = $data->inv_id;
                $logExist = $this->generalMailLogRepository->getStatus($invoiceId);
                $clientprogId = $data->clientprog_id;
                
                $identifier = $data->identifier;
                $payment_method = $data->master_paymentmethod;

                $pic_email = $data->pic_mail;

                $program_name = ucwords(strtolower($data->program_name));

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

                $subject = '7 Days Left until the Payment Deadline for ' . $program_name;

                $params = [
                    'parent_fullname' => $parent_fullname,
                    'parent_mail' => $parent_mail,
                    'program_name' => $program_name,
                    'due_date' => date('d/m/Y', strtotime($data->inv_duedate)),
                    'child_fullname' => $data->fullname,
                    'inv_paymentmethod' => $data->inv_paymentmethod,
                    'total_payment' => $data->invoice_totalprice_idr,
                    'pic_email' => $pic_email
                ];

                $mail_resources = 'pages.invoice.client-program.mail.reminder-payment';

                try {
                    Mail::send($mail_resources, $params, function ($message) use ($params, $subject) {
                        $message->to($params['parent_mail'], $params['parent_fullname'])
                            ->cc([env('FINANCE_CC'), $params['pic_email']])
                            ->subject($subject);
                    });
                } catch (Exception $e) {

                    Log::error('Failed to send invoice reminder to ' . $parent_mail . ' caused by : ' . $e->getMessage() . ' | Line ' . $e->getLine());
                    return $this->error($e->getMessage() . ' | Line ' . $e->getLine());
                }

                $this->info('Invoice reminder has been sent to ' . $parent_mail);

                switch ($payment_method) {

                    case "Full Payment":
                        # update reminded count to 1
                        $updated_invoice = $this->invoiceProgramRepository->getInvoiceByClientProgId($clientprogId);
                        $updated_invoice->reminded = 1;
                        $updated_invoice->save();
                        break;

                    case "Installment":
                        # update reminded count to 1
                        $updated_invoice_installment = $this->invoiceDetailRepository->getInvoiceDetailById($identifier);
                        $updated_invoice_installment->reminded = 1;
                        $updated_invoice_installment->save();
                        break;

                }

                # remove from mail log if the identifier mail has been successfully sent
                if ($logExist)
                    $this->generalMailLogRepository->removeLog($invoiceId);
                

                $progressBar->advance();
            }

            if (count($parents_have_no_email) > 0 && !$logExist) {
                $params = [
                    'finance_name' => env('FINANCE_NAME'),
                    'parents_have_no_email' => $parents_have_no_email,
                ];

                $mail_resources = 'pages.invoice.client-program.mail.reminder-finance';
                try {

                    Mail::send($mail_resources, $params, function ($message) {
                        $message->to(env('FINANCE_CC'), env('FINANCE_NAME'))
                            ->subject('There are Some Client that can\'t be reminded');
                    });

                    # create mail log
                    $logDetails = [
                        'identifier' => $invoiceId,
                        'category' => 'invoice',
                        'target' => 'client',
                        'description' => json_encode($params)
                    ];

                    $this->generalMailLogRepository->createLog($logDetails);

                } catch (Exception $e) {
                    Log::error('Failed to send info to finance team cause by : ' . $e->getMessage() . ' | Line ' . $e->getLine());
                    return $this->error($e->getMessage() . ' | Line ' . $e->getLine());
                }
            }

            $progressBar->finish();
        }
        
        return Command::SUCCESS;
    }
}
