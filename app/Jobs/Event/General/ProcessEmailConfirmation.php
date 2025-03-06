<?php

namespace App\Jobs\Event\General;

use App\Interfaces\ClientEventLogMailRepositoryInterface;
use App\Models\ClientEvent;
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

class ProcessEmailConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use IsMonitored;

    public $tries = 3;
    public $timeout = 600;

    // Priority levels: high, default, low
    public $priority = 'high';

    protected $client_event;
    protected ClientEventLogMailRepositoryInterface $clientEventLogMailRepository;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ClientEvent $client_event, ClientEventLogMailRepositoryInterface $clientEventLogMailRepository)
    {
        $this->client_event = $client_event;
        $this->clientEventLogMailRepository = $clientEventLogMailRepository;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {


        $view = 'mail-template.confirmation.email-confirmation-event';
        $subject = 'Confirmation Email';
        $role_name = null;
        
        try {

            $event_name = $this->client_event->event->event_title;

            if($this->client_event->clientMaster->roles->where('role_name', 'Parent')->first()){
                $role_name = 'Parent';
                $subject = 'Pendaftaran '. $event_name .' Kamu Sudah Dikonfirmasi!';
            }else if($this->client_event->clientMaster->roles->where('role_name', 'Student')->first()){
                $subject = 'Your '. $event_name .' Registration is Confirmed!';
                $role_name = 'Student';
            }else if($this->client_event->clientMaster->roles->where('role_name', 'Teacher/Counselor')->first()){
                $subject = 'Don’t miss this important session and get a FREE consultation for your students’ profile building activities.';
                $role_name = 'Teacher/Counselor';
            }

            $mail_details = [
                'role_name' => $role_name,
                'email' => $this->client_event->clientMaster->mail,
                'recipient' => $this->client_event->clientMaster->full_name,
                'event' => $this->client_event->event
            ];
            
            if($mail_details['email'] == null){
                Log::warning('Cannot send mail confirmation, email client is null', ['clientevent_id' => $this->client_event->clientevent_id]);
                return;
            }

            # send email
            Mail::send($view, $mail_details, function ($message) use ($subject, $mail_details) {
                $message->to($mail_details['email'], $mail_details['recipient'])
                    ->subject($subject);
            });
            $sent_status = 1;

        } catch (Exception $e) {
            $sent_status = 0;
            Log::error('Failed to send email confirmation'. $e->getMessage());

        }

        try {

            $logDetails = [
                'clientevent_id' => $this->client_event->clientevent_id,
                'sent_status' => $sent_status,
                'category' => 'email-confirmation-event'
            ];

            # check if log is exists
            # when exists then just update the sent_status
            if ($foundLog = $this->clientEventLogMailRepository->getClientEventLogMailByClientEventIdAndCategory($this->client_event->clientevent_id, 'email-confirmation-event')) {
                Log::info($this->client_event->clientevent_id.' & '.json_encode($foundLog));

                $this->clientEventLogMailRepository->updateClientEventLogMail($foundLog->id, ['sent_status' => 1]);

            } else {

                $this->clientEventLogMailRepository->createClientEventLogMail($logDetails);
            }


        } catch (Exception $e) {

            Log::error('Failed to create log email confirmation' . $e->getMessage());

        }
        
    }
}
