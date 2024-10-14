<?php
namespace App\Jobs\Invoice;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\InvoiceDetailRepositoryInterface;
use App\Repositories\InvoiceProgramRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PDF;
use romanzipp\QueueMonitor\Traits\IsMonitored;
class ProcessEmailHoldProgramJob implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use IsMonitored;
    protected $clientProgramRepository;
    protected $clientRepository;
    protected $invoiceDetailRepository;
    protected $invoiceProgramRepository;
    protected $data;
    protected $clientProgId;
    public $tries = 3;
    public $timeout = 120;
    // Priority levels: high, default, low
    public $priority = 'high';
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ClientProgramRepositoryInterface $clientProgramRepository, ClientRepositoryInterface $clientRepository, InvoiceProgramRepository $invoiceProgramRepository, InvoiceDetailRepositoryInterface $invoiceDetailRepository, array $data, $clientProgId)
    {
        $this->clientProgramRepository = $clientProgramRepository;
        $this->clientRepository = $clientRepository;
        $this->invoiceProgramRepository = $invoiceProgramRepository;
        $this->invoiceDetailRepository = $invoiceDetailRepository;
        $this->data = $data;
        $this->clientProgId = $clientProgId;
    }
    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->clientProgId;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $parent = $this->clientRepository->getClientById($this->data['parent_id']);
            if($parent->mail != $this->data['parent_mail']){
                $this->clientRepository->updateClient($this->data['parent_id'], ['mail' => $this->data['parent_mail']]);
            }
            $clientProg = $this->clientProgramRepository->getClientProgramById($this->clientProgId);
            $invDetail = $invoiceMaster = null;
            if($this->data['invdtl_id'] != null){
                $invDetail = $this->invoiceDetailRepository->getInvoiceDetailById($this->data['invdtl_id']);
            }
            $invoiceMaster = $this->invoiceProgramRepository->getInvoiceByInvoiceId($this->data['inv_id']);
            
            $mailDetails = [
                'parent_fullname' => $parent->full_name,
                'child_name' => $clientProg->client->full_name,
                'program_name' => $clientProg->program->program_name,
                'invDetail' => $invDetail,
                'invoiceMaster' => $invoiceMaster
            ];
            // Get Profile Building Mentor
            $mentor = $invoiceMaster->clientprog->clientMentor->where('pivot.type', 2);
            $ccMail = [
                env('FINANCE_CC'),
                env('STUDENT_SUCCESS_CC'),
                env('HEAD_MENTOR_CC'),
                $invoiceMaster->clientprog->internalPic->email,
            ];
    
            count($mentor) > 0 ? $ccMail [] = $mentor->first()->email : null;
            # send email to related person that has authority to give a signature
            Mail::send('pages.invoice.client-program.mail.hold-program', $mailDetails, function ($message) use($parent, $ccMail) {
                    
                Log::notice('Email hold mentoring has been sent with ClientProg Id : '.$this->clientProgId);
                
                $message->to($this->data['parent_mail'], $parent->full_name)
                        ->cc($ccMail)
                        ->subject('Mentoring Sessions on Hold Due to Missed Payment');
            });
        } catch (Exception $e) {
            Log::error('Failed to send email hold mentoring with ClientProg Id : '.$this->clientProgId . ' The Problem: ' .$e->getMessage());
        }
    }
}