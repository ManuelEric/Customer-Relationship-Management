<?php

namespace App\Console\Commands;

use App\Http\Traits\CurrencyTrait;
use App\Interfaces\GeneralMailLogRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReminderInvoiceProgramToReferralCommand extends Command
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
    protected $signature = 'send:reminder_invoicereferral_program';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder invoice referral out. To remind the referral to pay the invoice.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $partner_have_no_pic = [];
        $partner_have_no_pic = [];
        $partner_pic_name = null;
        $partner_pic_mail = null;
        $invoiceB2bId = null;

        $invoice_master = $this->invoiceB2bRepository->getAllDueDateInvoiceReferralProgram(7);

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
    
                $partner_name = $data->partner_name;
                $partner_pics = $data->referral->partner->pic;

                # when the partner doesnt have PIC
                if ($partner_pics->count() == 0) {
                    # collect data partner that have no email
                    $partner_have_no_pic[] = [
                        'partner_name' => $partner_name,
                    ];
                    continue;
                }else{
                    foreach ($partner_pics as $partner_pic) {
                        if($partner_pic->is_pic == 1){
                            $partner_pic_name = $partner_pic->pic_name;
                            $partner_pic_mail = $partner_pic->pic_mail;
                        }
                    }
                }    
    
                $subject = '7 Days Left until the Payment Deadline for ' . $program_name;
    
                $params = [
                    'partner_pic' => $partner_pic_name,
                    'partner_mail' => $partner_pic_mail,
                    'program_name' => $program_name,
                    'due_date' => date('d/m/Y', strtotime($data->invb2b_duedate)),
                    'partner_name' => $partner_name,
                    'total_payment_other' => $data->currency != 'idr' ? $this->formatCurrency($data->currency, $data->invb2b_totpriceidr, $data->invb2b_totprice ?? 0) : 0,
                    'total_payment_idr' => $this->formatCurrency('idr', $data->invb2b_totpriceidr, $data->invb2b_totprice ?? 0),
                    'pic_email' => $pic_email,
                    'currency' => $data->currency
                ];
    
                $mail_resources = 'pages.invoice.referral.mail.reminder-payment';
    
                if (isset($partner_pic_name) && isset($partner_pic_mail)) {
                    
                    try {
                        Mail::send($mail_resources, $params, function ($message) use ($params, $subject) {
                            $message->to($params['partner_mail'], $params['partner_pic'])
                                ->cc([env('FINANCE_CC'), env('FINANCE_CC_2'), $params['pic_email']])
                                ->subject($subject);
                        });
                    } catch (Exception $e) {
        
                        Log::error('Failed to send invoice reminder to ' . $partner_pic_mail . ' caused by : ' . $e->getMessage() . ' | Line ' . $e->getLine());
                        return $this->error($e->getMessage() . ' | Line ' . $e->getLine());
                    }
        
                    $this->info('Invoice reminder has been sent to ' . $partner_pic_mail);
        
                    # update reminded count to 1
                    $data->reminded = 1;
                    $data->save();
    
                    # remove from mail log if the identifier mail has been successfully sent
                    if ($logExist)
                        $this->generalMailLogRepository->removeLog($invoiceB2bId);

                } else {
                    
                    Log::error('Invoice Referral cannot be sent because the partner ('.$partner_name.') don\'t have mail');

                }

    
                $progressBar->advance();
            }
    
            # check if variable partner have no pic has value in it
            # meaning that partner doesnt have a pic
            if (count($partner_have_no_pic) > 0 && !$logExist) {
                $params = [
                    'finance_name' => env('FINANCE_NAME'),
                    'partner_have_no_pic' => $partner_have_no_pic,
                ];
    
                $mail_resources = 'pages.invoice.referral.mail.reminder-finance';
                try {
    
                    Mail::send($mail_resources, $params, function ($message) {
                        $message->to(env('FINANCE_CC'), env('FINANCE_NAME'))
                            ->cc([env('FINANCE_CC_2')])
                            ->subject('There are some partner that can\'t be reminded');
                    });

                    # create mail log
                    $logDetails = [
                        'identifier' => $invoiceB2bId,
                        'category' => 'invoice',
                        'target' => 'referral',
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
