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

class ProcessEmailInvitationInfo implements ShouldQueue
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

            Mail::send('mail-template.invitation.event.invitation-mail', $this->mailDetails, function ($message) {
                $message->to($this->mailDetails['client']['email'], $this->mailDetails['client']['recipient'])
                    ->subject('Invitation VIP ');
            });
            $sent_status = 1;

        } catch (Exception $e) {
            
            $sent_status = 0;
            Log::error('Failed to send mail invitation info: ' . $e->getMessage());

        }

        $keyLog = [
            'client_id' => $this->mailDetails['client']['client_id'],
            'event_id' => $this->mailDetails['event_id'],
            'category' => 'invitation-info'
        ];

        $valueLog = [
            'sent_status' => $sent_status,
        ];

        ClientEventLogMail::updateOrCreate($keyLog, $valueLog);

        Log::debug('Send mail invitation info fullname: ' . $this->mailDetails['client']['recipient'] . ' status: ' . $sent_status, ['fullname' => $this->mailDetails['client']['recipient'], 'email' => $this->mailDetails['client']['email'], 'sent_status' => $sent_status]);


        
    }
}