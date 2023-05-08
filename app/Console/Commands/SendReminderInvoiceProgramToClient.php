<?php

namespace App\Console\Commands;

use App\Interfaces\InvoiceProgramRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReminderInvoiceProgramToClient extends Command
{

    private InvoiceProgramRepositoryInterface $invoiceProgramRepository;

    public function __construct(InvoiceProgramRepositoryInterface $invoiceProgramRepository)
    {
        parent::__construct();
        $this->invoiceProgramRepository = $invoiceProgramRepository;
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
    protected $description = 'Send reminder on invoice to client. To remind the client to pay the invoice.';

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
        $parents_have_no_email = [];
        $invoice_master = $this->invoiceProgramRepository->getAllDueDateInvoiceProgram(7);
        foreach ($invoice_master as $data) {

            $invoiceId = $data->inv_id;
            $program_name = ucwords(strtolower($data->program_name));
            
            $parent_fullname = $data->parent_fullname;
            $parent_mail = $data->parent_mail;
            $parent_phone = $data->parent_phone;
            if ($parent_mail === null)
            {
                # collect data parents that have no email
                $parents_have_no_email[] = [
                    'fullname' => $parent_fullname,
                    'mail' => $parent_mail,
                    'phone' => $parent_phone,
                ];
            }

            $subject = '7 Days Left until the Payment Deadline for '.$program_name;

            $params = [
                'parent_fullname' => $parent_fullname,
                'parent_mail' => $parent_mail,
                'program_name' => $program_name,
                'due_date' => date('d/m/Y', strtotime($data->inv_duedate)),
                'child_fullname' => $data->full_name,
                'installment_notes' => $data->installment_notes,
                'total_payment' => $data->invoice_price_idr,
            ];

            $mail_resources = 'pages.invoice.client-program.mail.reminder-payment';

            try {
                Mail::send($mail_resources, $params, function ($message) use ($params, $subject) {
                    $message->to($params['parent_mail'], $params['parent_fullname'])
                        ->subject($subject);
                });
            } catch (Exception $e) {

                Log::error('Failed to send invoice reminder to '.$parent_mail . ' cause by : '. $e->getMessage().' | Line '.$e->getLine());
                $this->error($e->getMessage(). ' | Line '.$e->getLine());

            }

            $this->info('Invoice reminder has been sent to '.$parent_mail);

        }

        if (count($parents_have_no_email) > 0)
        {
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
            } catch (Exception $e) {
                Log::error('Failed to send invoice reminder to '.$parent_mail . ' cause by : '. $e->getMessage().' | Line '.$e->getLine());
            }

        }
    }
}
