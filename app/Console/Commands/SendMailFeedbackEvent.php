<?php

namespace App\Console\Commands;

use App\Interfaces\ClientEventLogMailRepositoryInterface;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Jobs\Event\Stem\ProcessEmailFeedback;
use App\Jobs\Event\Stem\ProcessEmailThanks;
use Illuminate\Console\Command;

class SendMailFeedbackEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:feedback_mail_event';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a feedback mail after attending the event';

    private EventRepositoryInterface $eventRepository;
    private ClientEventRepositoryInterface $clientEventRepository;
    private ClientEventLogMailRepositoryInterface $clientEventLogMailRepository;

    public function __construct(EventRepositoryInterface $eventRepository, ClientEventRepositoryInterface $clientEventRepository, ClientEventLogMailRepositoryInterface $clientEventLogMailRepository)
    {
        parent::__construct();
        $this->eventRepository = $eventRepository;
        $this->clientEventRepository = $clientEventRepository;
        $this->clientEventLogMailRepository = $clientEventLogMailRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        # change the event name to whatever you want as long as it match to event_title on tbl_events
        $event_name = 'STEM Wonderlab Registration Form';
        $chosen_event = $this->eventRepository->getEventByName($event_name);

        $eventId = $chosen_event->event_id;
        $getParticipants = $this->clientEventRepository->getJoinedClientByEventId($eventId);
        

        if ($getParticipants->count() == 0) {
            $this->info('No participant found for event');
            return Command::SUCCESS;
        }


        foreach ($getParticipants as $participant) {

            $clientevent_id = $participant->clientevent_id;

            # get full information of client
            $master_client = $participant->client;

            $role = strtolower($master_client->roles->first()->role_name);

            $client = [
                'recipient' => $master_client->full_name,
                'email' => $master_client->mail,
                'role' => $role
            ];

  
            ProcessEmailFeedback::dispatch($client, $clientevent_id, $this->clientEventLogMailRepository)->onQueue('feedback-email_'.$eventId);


        }        

        return Command::SUCCESS;
    }
}
