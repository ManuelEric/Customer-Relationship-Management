<?php

namespace App\Console\Commands;

use App\Http\Traits\CurrencyTrait;
use App\Interfaces\GeneralMailLogRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Mail\Invoice\ReminderToPartner as InvoiceReminderToPartner;
use App\Mail\Invoice\ReportToFinanceTeam;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReminderInvoiceProgramToPartnerCommand extends Command
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
        $timer_start = Carbon::now();
        $partner_have_no_pic = [];
        $partner_pic_name = null;
        $partner_pic_mail = null;
        $invoiceB2bId = null;

        $invoice_master = $this->invoiceB2bRepository->getAllDueDateInvoicePartnerProgram(7);

        if (count($invoice_master) == 0) {
            return Command::SUCCESS;
        }

        $progressBar = $this->output->createProgressBar($invoice_master->count());
        $progressBar->start();

        DB::beginTransaction();
        try {

            foreach ($invoice_master as $data) {

                // meaning, invoices need to be signed before processed to partner
                if ($data->sign_status != 'signed') {
                    continue;
                }

                $invoiceB2bId = $data->invb2b_id;
                $logExist = $this->generalMailLogRepository->getStatus($invoiceB2bId);
                $pic_email = $data->pic_mail;

                $partner_name = $data->partnership_name;
                $partner_pics = $data->partner_prog->corp->pic;

                if ($partner_pics->count() == 0) {
                    // collect data parents that have no email
                    $partner_have_no_pic[] = [
                        'partner_name' => $partner_name,
                    ];

                    continue;
                }

                foreach ($partner_pics as $partner_pic) {
                    if ($partner_pic->is_pic == 1) {
                        $partner_pic_name = $partner_pic->pic_name;
                        $partner_pic_mail = $partner_pic->pic_mail;
                    }
                }

                $cc = [env('FINANCE_CC')];

                if ($pic_email !== null) {
                    array_push($cc, $pic_email);
                }

                try {

                    Mail::to($partner_pic_mail, $partner_pic_name)->cc($cc)->queue(new InvoiceReminderToPartner([
                        'invoiceb2b_id' => $invoiceB2bId,
                        'partner_pic' => $partner_pic_name,
                        'partner_mail' => $partner_pic_mail,
                        'program_name' => $data->program_name,
                        'due_date' => date('d/m/Y', strtotime($data->invb2b_duedate)),
                        'partner_name' => $partner_name,
                        'total_payment_other' => $data->currency != 'idr' ? $this->formatCurrency($data->currency, $data->invb2b_totpriceidr, $data->invb2b_totprice ?? 0) : 0,
                        'total_payment_idr' => $this->formatCurrency('idr', $data->invb2b_totpriceidr, $data->invb2b_totprice ?? 0),
                        'pic_email' => $data->pic_mail,
                        'currency' => $data->currency,
                        'invoiceb2b' => $data,
                    ]));

                } catch (Exception $e) {

                    Log::error("[CRON - SEND REMINDER INVOICE PROGRAM TO PARTNER] Email to {$partner_pic_mail} failed.");
                    $this->error($e->getMessage().' | Line '.$e->getLine());

                    return Command::FAILURE;
                }

                $this->info('Invoice reminder has been sent to '.$partner_pic_mail);

                // update reminded count to 1
                $data->reminded = 1;
                $data->save();

                // remove from mail log if the identifier mail has been successfully sent
                if ($logExist) {
                    $this->generalMailLogRepository->removeLog($invoiceB2bId);
                }

                $progressBar->advance();
            }

            // will send email also to finance team
            // when some recipient/clients cannot received the email
            if (count($partner_have_no_pic) > 0 && ! $logExist) {

                try {

                    Mail::to(env('FINANCE_CC'), env('FINANCE_NAME'))->cc(env('PARTNERSHIP_MAIL'))->queue(new ReportToFinanceTeam([
                        'view' => 'pages.invoice.corporate-program.mail.reminder-finance',
                        'with' => [
                            'finance_name' => env('FINANCE_NAME'),
                            'partner_have_no_pic' => $partner_have_no_pic,
                        ],
                    ]));

                    // create mail log
                    $this->generalMailLogRepository->createLog([
                        'identifier' => $invoiceB2bId,
                        'category' => 'invoice',
                        'target' => 'partner',
                        'description' => json_encode([
                            'finance_name' => env('FINANCE_NAME'),
                            'partner_have_no_pic' => $partner_have_no_pic,
                        ]),
                    ]);
                    $this->info('report sent to finance & partnership team');

                } catch (Exception $e) {
                    Log::error('Failed to send info to finance team cause by : '.$e->getMessage().' | Line '.$e->getLine());
                    $this->error($e->getMessage().' | Line '.$e->getLine());

                    return Command::FAILURE;
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("[CRON - SEND REMINDER INVOICE PROGRAM TO PARTNER] Email reminder has failure. Error: {$e->getMessage()} on file {$e->getFile()} line {$e->getLine()}");

            return Command::FAILURE;
        }

        $progressBar->finish();

        $timer_end = Carbon::now();
        $progress_time = $timer_start->diffInSeconds($timer_end);
        Log::info("[CRON - SEND REMINDER INVOICE TO PARTNER] works. It tooks {$progress_time} seconds.");

        return Command::SUCCESS;
    }
}
