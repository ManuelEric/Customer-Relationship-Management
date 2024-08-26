<?php

namespace App\Jobs\Invoice;

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

class ProcessEmailRequestSignJob implements ShouldQueue, ShouldBeUniqueUntilProcessing
// class ProcessEmailRequestSignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use IsMonitored;

    protected $mailDetails;
    protected $attachmentDetails;
    protected $invoiceId;

    public $tries = 3;
    public $timeout = 120;

    // Priority levels: high, default, low
    public $priority = 'high';
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $mailDetails, array $attachmentDetails, $invoiceId)
    {
        $this->mailDetails = $mailDetails;
        $this->attachmentDetails = $attachmentDetails;
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
        $pdf = PDF::loadView(
                    $this->attachmentDetails['view'], 
                    [
                        'clientProg' => $this->attachmentDetails['client_prog'], 
                        'companyDetail' => $this->attachmentDetails['company_detail'], 
                        'director' => $this->attachmentDetails['director'],
                    ]
                );
        Storage::put('public/uploaded_file/invoice/client/' . $this->attachmentDetails['file_name'] . '.pdf', $pdf->output());

        # send email to related person that has authority to give a signature
        Mail::send('pages.invoice.client-program.mail.view', $this->mailDetails, function ($message) use ($pdf) {
                
            Log::notice('Email request sign has been sent with invoice ID : '.$this->invoiceId);
            
            $message->to($this->mailDetails['email'], $this->mailDetails['recipient'])
                ->subject($this->mailDetails['title'])
                ->attachData($pdf->output(), $this->invoiceId . '.pdf');
        });
    }
}
