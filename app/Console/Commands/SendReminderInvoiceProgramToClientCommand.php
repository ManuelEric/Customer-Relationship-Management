<?php

namespace App\Console\Commands;

use App\Http\Traits\CurrencyTrait;
use App\Interfaces\GeneralMailLogRepositoryInterface;
use App\Interfaces\InvoiceDetailRepositoryInterface;
use App\Interfaces\InvoiceProgramRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReminderInvoiceProgramToClientCommand extends Command
{
    use CurrencyTrait;
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
        $this->cnHandleReminderEmail();

        return Command::SUCCESS;
    }

    public function cnHandleReminderEmail()
    {
        Log::info('Send reminder invoice program to client works fine');
        $parents_have_no_email = [];
        $invoice_master = $this->invoiceProgramRepository->getAllDueDateInvoiceProgram(7);

        if (count($invoice_master) > 0) {

            $progress_bar = $this->output->createProgressBar($invoice_master->count());
            $progress_bar->start();

            foreach ($invoice_master as $data) {

                if($data->sign_status != 'signed')
                {
                    $this->newLine();
                    $this->info("Email not sent to {$data->parent_mail} because the sign status is null");
                    continue;
                }

                $invoice_id = $data->inv_id;
                $log_exist = $this->generalMailLogRepository->getStatus($invoice_id);
                $clientprog_id = $data->clientprog_id;
                
                $identifier = $data->identifier;
                $payment_method = $data->master_paymentmethod;

                $pic_email = $data->internalPic->email;

                $program_name = ucwords(strtolower($data->program->program_name));

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
                    'total_payment_other' => $data->currency != 'idr' ? $this->formatCurrency($data->currency, $data->inv_totalprice_idr, $data->inv_totalprice ?? 0) : 0,
                    'total_payment_idr' => $this->formatCurrency('idr', $data->inv_totalprice_idr, $data->inv_totalprice ?? 0),
                    'pic_email' => $pic_email,
                    'currency' => $data->currency
                ];

                $mail_resources = 'pages.invoice.client-program.mail.reminder-payment';

                try {
                    Mail::send($mail_resources, $params, function ($message) use ($params, $subject) {
                        $message->to($params['parent_mail'], $params['parent_fullname'])
                            ->cc([env(key: 'FINANCE_CC'), env('FINANCE_CC_2'), $params['pic_email']])
                            ->subject($subject);
                    });
                } catch (Exception $e) {

                    Log::error('Failed to send invoice client reminder to ' . $parent_mail . ' caused by : ' . $e->getMessage() . ' | Line ' . $e->getLine() . ' | File ' . $e->getFile());
                    return $this->error($e->getMessage() . ' | Line ' . $e->getLine());
                }

                $this->newLine();
                $this->info('Invoice reminder has been sent to ' . $parent_mail);

                switch ($payment_method) {

                    case "Full Payment":
                        # update reminded count to 1
                        $updated_invoice = $this->invoiceProgramRepository->getInvoiceByClientProgId($clientprog_id);
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
                if ($log_exist)
                    $this->generalMailLogRepository->removeLog($invoice_id);
                

                $progress_bar->advance();
            }

            if (count($parents_have_no_email) > 0 && !$log_exist) {
                $params = [
                    'finance_name' => env('FINANCE_NAME'),
                    'parents_have_no_email' => $parents_have_no_email,
                ];

                $mail_resources = 'pages.invoice.client-program.mail.reminder-finance';
                try {

                    Mail::send($mail_resources, $params, function ($message) {
                        $message->to(env('FINANCE_CC'), env('FINANCE_NAME'))
                            ->cc([env('FINANCE_CC_2')])
                            ->subject('There are Some Client that can\'t be reminded');
                    });

                    # create mail log
                    $log_details = [
                        'identifier' => $invoice_id,
                        'category' => 'invoice',
                        'target' => 'client',
                        'description' => json_encode($params)
                    ];

                    $this->generalMailLogRepository->createLog($log_details);

                } catch (Exception $e) {
                    Log::error('Failed to send info to finance team cause by : ' . $e->getMessage() . ' | Line ' . $e->getLine());
                    return $this->error($e->getMessage() . ' | Line ' . $e->getLine());
                }
            }

            $progress_bar->finish();
        }
        
        return Command::SUCCESS;
    }
}
