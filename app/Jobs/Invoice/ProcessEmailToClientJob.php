<?php

namespace App\Jobs\Invoice;

use App\Interfaces\InvoiceAttachmentRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ProcessEmailToClientJob implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use IsMonitored;

    protected $mailDetails;
    protected $attachment;
    protected $invoiceAttachmentRepository;
    public $invoiceId;

    public $tries = 3;
    public $timeout = 120;

    // Priority levels: high, default, low
    public $priority = 'high';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $mailDetails, $attachment, InvoiceAttachmentRepositoryInterface $invoiceAttachmentRepository, $invoiceId)
    {
        $this->mailDetails = $mailDetails;
        $this->attachment = $attachment;
        $this->invoiceAttachmentRepository = $invoiceAttachmentRepository;
        $this->invoiceId = $invoiceId;
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->invoiceId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::send('pages.invoice.client-program.mail.client-view', $this->mailDetails, function ($message) {
            $message->to($this->mailDetails['email'], $this->mailDetails['recipient'])
                ->cc($this->mailDetails['cc'])
                ->subject($this->mailDetails['title'])
                ->attach(storage_path('app/public/uploaded_file/invoice/client/' . $this->attachment->attachment));
        });
        
        # update status send to client
        $newDetails['send_to_client'] = 'sent';
        
        $this->invoiceAttachmentRepository->updateInvoiceAttachment($this->attachment->id, $newDetails);
    }
}
