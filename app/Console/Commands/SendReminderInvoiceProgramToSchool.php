<?php

namespace App\Console\Commands;

use App\Http\Traits\CurrencyTrait;
use App\Interfaces\GeneralMailLogRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReminderInvoiceProgramToSchool extends Command
{
    use CurrencyTrait;
    private InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    private GeneralMailLogRepositoryInterface $generalMailLogRepository;

    public function __construct(InvoiceB2bRepositoryInterface $invoiceB2bRepository, GeneralMailLogRepositoryInterface $generalMailLogRepository)
    {
        parent::__construct();
        $this->invoiceB2bRepository = $invoiceB2bRepository;
        $this->generalMailLogRepository = $generalMailLogRepository;
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:reminder_invoiceschool_program';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder invoice school program. To remind the school to pay the invoice.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $school_have_no_pic = [];
        $invoice_master = $this->invoiceB2bRepository->getAllDueDateInvoiceSchoolProgram(7);

        if (count($invoice_master) > 0) {

            $progressBar = $this->output->createProgressBar($invoice_master->count());
            $progressBar->start();

            foreach ($invoice_master as $data) {
    
                if($data->sign_status != 'signed')
                    continue;

                $invoiceB2bId = $data->invb2b_id;
                $logExist = $this->generalMailLogRepository->getStatus($invoiceB2bId);
                $pic_email = $data->pic_mail;
    
                $program_name = ucwords(strtolower($data->program_name));
    
                $school_name = $data->school_name;
                $school_pics = $data->sch_prog->school->detail;
                if ($school_pics->count() == 0) {
                    # collect data parents that have no email
                    $school_have_no_pic[] = [
                        'school_name' => $school_name,
                    ];
                    continue;
                }
                $school_pic_name = $school_pics[0]->schdetail_fullname;
                $school_pic_mail = $school_pics[0]->schdetail_email;

                if (!$school_pic_mail) {
                    Log::info('Failed to send reminder to school refer to invoice : '.$invoiceB2bId.' because there is no pic email');
                    continue;
                }
    
                $school_pic_phone = $school_pics[0]->schdetail_phone;
    
    
                $subject = '7 Days Left until the Payment Deadline for ' . $program_name;
    
                $params = [
                    'school_pic_name' => $school_pic_name,
                    'school_pic_mail' => $school_pic_mail,
                    'program_name' => $program_name,
                    'due_date' => date('d/m/Y', strtotime($data->invb2b_duedate)),
                    'school_name' => $school_name,
                    'total_payment_other' => $data->currency != 'idr' ? $this->formatCurrency($data->currency, $data->invb2b_totpriceidr, $data->invb2b_totprice ?? 0) : 0,
                    'total_payment_idr' => $this->formatCurrency('idr', $data->invb2b_totpriceidr, $data->invb2b_totprice ?? 0),
                    'pic_email' => $pic_email,
                    'currency' => $data->currency
                ];
    
                $mail_resources = 'pages.invoice.school-program.mail.reminder-payment';
    
                try {
                    Mail::send($mail_resources, $params, function ($message) use ($params, $subject) {
                        $message->to($params['school_pic_mail'], $params['school_pic_name'])
                            ->cc([env('FINANCE_CC'), $params['pic_email']])
                            ->subject($subject);
                    });
                } catch (Exception $e) {
    
                    Log::error('Failed to send invoice reminder to ' . $school_pic_mail . ' caused by : ' . $e->getMessage() . ' | Line ' . $e->getLine());
                    return $this->error($e->getMessage() . ' | Line ' . $e->getLine());
                }
    
                $this->info('Invoice reminder has been sent to ' . $school_pic_mail);
    
                # update reminded count to 1
                $data->reminded = 1;
                $data->save();

                # remove from mail log if the identifier mail has been successfully sent
                if ($logExist)
                    $this->generalMailLogRepository->removeLog($invoiceB2bId);
    
                $progressBar->advance();
            }
    
            if (count($school_have_no_pic) > 0) {
                $params = [
                    'finance_name' => env('FINANCE_NAME'),
                    'school_have_no_pic' => $school_have_no_pic,
                ];
    
                $mail_resources = 'pages.invoice.school-program.mail.reminder-finance';
                try {
    
                    Mail::send($mail_resources, $params, function ($message) {
                        $message->to(env('FINANCE_CC'), env('FINANCE_NAME'))
                            ->subject('There are some school that can\'t be reminded');
                    });

                    # create mail log
                    $logDetails = [
                        'identifier' => $invoiceB2bId,
                        'category' => 'invoice',
                        'target' => 'school',
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
