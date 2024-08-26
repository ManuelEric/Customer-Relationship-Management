<?php

namespace App\Console\Commands;

use App\Http\Traits\CurrencyTrait;
use App\Interfaces\GeneralMailLogRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReminderInvoiceProgramToPartner extends Command
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
    protected $signature = 'send:reminder_invoicepartner_program';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder invoice partner program. To remind the partner to pay the invoice.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('Send reminder invoice program to partner works fine');
        $partner_have_no_pic = [];
        $invoice_master = $this->invoiceB2bRepository->getAllDueDateInvoicePartnerProgram(7);

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

                $partner_name = $data->corp_name;
                $partner_pics = $data->partner_prog->corp->pic;
                if ($partner_pics->count() == 0) {
                    # collect data parents that have no email
                    $partner_have_no_pic[] = [
                        'partner_name' => $partner_name,
                    ];
                    continue;
                }

                # get the first pic
                $partner_pic_name = $partner_pics[0]->pic_name;
                $partner_pic_mail = $partner_pics[0]->pic_mail;

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

                $mail_resources = 'pages.invoice.corporate-program.mail.reminder-payment';

                $cc = array();
                array_push($cc, env('FINANCE_CC'));
                if ($params['pic_email'] !== NULL)
                    array_push($cc, $params['pic_email']);

                try {
                    Mail::send($mail_resources, $params, function ($message) use ($params, $cc, $subject) {
                        $message->to($params['partner_mail'], $params['partner_pic'])
                            ->cc($cc)
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

                $progressBar->advance();
            }


            if (count($partner_have_no_pic) > 0 && !$logExist) {
                $params = [
                    'finance_name' => env('FINANCE_NAME'),
                    'partner_have_no_pic' => $partner_have_no_pic,
                ];

                $mail_resources = 'pages.invoice.corporate-program.mail.reminder-finance';
                try {

                    Mail::send($mail_resources, $params, function ($message) {
                        $message->to(env('FINANCE_CC'), env('FINANCE_NAME'))
                            ->subject('There are some partner that can\'t be reminded');
                    });

                    # create mail log
                    $logDetails = [
                        'identifier' => $invoiceB2bId,
                        'category' => 'invoice',
                        'target' => 'partner',
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
