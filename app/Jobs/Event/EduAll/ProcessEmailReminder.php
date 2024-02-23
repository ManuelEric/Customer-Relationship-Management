<?php

namespace App\Jobs\Event\EduAll;

use App\Interfaces\ClientEventLogMailRepositoryInterface;
use App\Models\ClientEventLogMail;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessEmailReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 600;

    // Priority levels: high, default, low
    public $priority = 'high';

    protected $mailDetails;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $mailDetails)
    {
        $this->mailDetails = $mailDetails;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {


        try {

            Mail::send('mail-template.reminder.event.reminder-mail', $this->mailDetails, function ($message) {
                $message->to($this->mailDetails['email'], $this->mailDetails['recipient'])
                    ->subject($this->mailDetails['title']);
            });
            $sent_status = 1;

        } catch (Exception $e) {
            
            $sent_status = 0;
            Log::error('Failed to send mail reminder: ' . $e->getMessage());

        }

        $keyLog = [
            'client_id' => $this->mailDetails['client_id'],
            'event_id' => $this->mailDetails['event']['eventId'],
            'category' => 'reminder-registration',
            'notes' => $this->mailDetails['notes'],
            'child_id' => $this->mailDetails['child_id']
        ];

        $valueLog = [
            'sent_status' => $sent_status,
        ];

        ClientEventLogMail::updateOrCreate($keyLog, $valueLog);

        Log::debug('Send mail reminder fullname: ' . $this->mailDetails['recipient'] . ' status: ' . $sent_status, ['fullname' => $this->mailDetails['recipient'], 'email' => $this->mailDetails['email'], 'sent_status' => $sent_status]);


        
    }
}