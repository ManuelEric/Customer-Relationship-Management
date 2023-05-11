<?php

namespace App\Console\Commands;

use App\Interfaces\InvoiceB2bRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReminderInvoiceProgramToReferral extends Command
{
    private InvoiceB2bRepositoryInterface $invoiceB2bRepository;

    public function __construct(InvoiceB2bRepositoryInterface $invoiceB2bRepository)
    {
        parent::__construct();
        $this->invoiceB2bRepository = $invoiceB2bRepository;
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
        $invoice_master = $this->invoiceB2bRepository->getAllDueDateInvoiceReferralProgram(7);
        $progressBar = $this->output->createProgressBar($invoice_master->count());
        $progressBar->start();
        foreach ($invoice_master as $data) {

            $invoiceB2bId = $data->invb2b_id;

            $program_name = ucwords(strtolower($data->program_name));
            
            $partner_name = $data->partner_name;
            $partner_pics = $data->referral->partner->pic;
            if ($partner_pics->count() == 0)
            {
                # collect data parents that have no email
                $partner_have_no_pic[] = [
                    'partner_name' => $partner_name,
                ];
                continue;
            }
            $partner_pic_name = $partner_pics[0]->pic_name; #if null then what happens?
            $partner_pic_mail = $partner_pics[0]->pic_mail;


            $subject = '7 Days Left until the Payment Deadline for '.$program_name;

            $params = [
                'partner_pic' => $partner_pic_name,
                'partner_mail' => $partner_pic_mail,
                'program_name' => $program_name,
                'due_date' => date('d/m/Y', strtotime($data->invb2b_duedate)),
                'partner_name' => $partner_name,
                'total_payment' => $data->invoice_totalprice_idr,
            ];

            $mail_resources = 'pages.invoice.referral.mail.reminder-payment';

            try {
                Mail::send($mail_resources, $params, function ($message) use ($params, $subject) {
                    $message->to($params['partner_mail'], $params['partner_pic'])
                        ->subject($subject);
                });
            } catch (Exception $e) {

                Log::error('Failed to send invoice reminder to '.$partner_pic_mail . ' caused by : '. $e->getMessage().' | Line '.$e->getLine());
                return $this->error($e->getMessage(). ' | Line '.$e->getLine());

            }

            $this->info('Invoice reminder has been sent to '.$partner_pic_mail);

            # update reminded count to 1
            $data->reminded = 1;
            $data->save();

            $progressBar->advance();
        }

        if (count($partner_have_no_pic) > 0)
        {
            $params = [
                'finance_name' => env('FINANCE_NAME'),
                'partner_have_no_pic' => $partner_have_no_pic,
            ];

            $mail_resources = 'pages.invoice.referral.mail.reminder-finance';
            try {

                Mail::send($mail_resources, $params, function ($message) {
                    $message->to(env('FINANCE_CC'), env('FINANCE_NAME'))
                        ->subject('There are some partner that can\'t be reminded');
                });
            } catch (Exception $e) {
                Log::error('Failed to send info to finance team cause by : '. $e->getMessage().' | Line '.$e->getLine());
                return $this->error($e->getMessage(). ' | Line '.$e->getLine());
            }

        }
        $progressBar->finish();
        return Command::SUCCESS;
    }
}
