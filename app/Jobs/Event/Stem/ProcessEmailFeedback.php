<?php

namespace App\Jobs\Event\Stem;

use App\Interfaces\ClientEventLogMailRepositoryInterface;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ProcessEmailFeedback implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use IsMonitored;

    public $tries = 3;
    public $timeout = 600;

    // Priority levels: high, default, low
    public $priority = 'high';

    protected $mailDetails;
    protected $clientEventId;
    protected ClientEventLogMailRepositoryInterface $clientEventLogMailRepository;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $mailDetails, $clientEventId, ClientEventLogMailRepositoryInterface $clientEventLogMailRepository)
    {
        $this->mailDetails = $mailDetails;
        $this->clientEventId = $clientEventId;
        $this->clientEventLogMailRepository = $clientEventLogMailRepository;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->mailDetails['role'] == 'student' || $this->mailDetails['role'] == 'mentee' || $this->mailDetails['role'] == 'teacher/counselor') {

            $subject = 'Thanks for joining STEM+ Wonderlab, Indonesia’s FIRST Student';


        } elseif ($this->mailDetails['role'] == 'parent') {

            $subject = 'Terima kasih sudah hadir di STEM+ Wonderlab, Indonesia’s FIRST ';            

        }


        try {

            # send email
            Mail::send('mail-template.feedback-email', $this->mailDetails, function ($message) use ($subject) {
                $message->to($this->mailDetails['email'], $this->mailDetails['recipient'])
                    ->subject($subject);
            });
            $sent_status = 1;

        } catch (Exception $e) {
            
            $sent_status = 0;

        }

        try {

            $logDetails = [
                'clientevent_id' => $this->clientEventId,
                'sent_status' => $sent_status,
                'category' => 'feedback-mail'
            ];


            # check if log is exists
            # when exists then just update the sent_status
            if ($foundLog = $this->clientEventLogMailRepository->getClientEventLogMailByClientEventIdAndCategory($this->clientEventId, 'thanks-mail-after')) {
                Log::info($this->clientEventId.' dan '.json_encode($foundLog));

                $this->clientEventLogMailRepository->updateClientEventLogMail($foundLog->id, ['sent_status' => 1]);

            } else {

                $this->clientEventLogMailRepository->createClientEventLogMail($logDetails);
            }


        } catch (Exception $e) {

            Log::error('Failed to create event log email thanks' . $e->getMessage());

        }

        
    }
}
