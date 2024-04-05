<?php

namespace App\Console\Commands\Event;

use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Jobs\Event\EduAll\ProcessEmailReminderReg;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class Reminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:event {event_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reminder of the Event';

    private ClientEventRepositoryInterface $clientEventRepository;
    private EventRepositoryInterface $eventRepository;

    public function __construct(ClientEventRepositoryInterface $clientEventRepository, EventRepositoryInterface $eventRepository)
    {
        parent::__construct();
        $this->clientEventRepository = $clientEventRepository;
        $this->eventRepository = $eventRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $requestedEventID = $this->argument('event_id');

        # get the master event
        $event = $this->eventRepository->getEventById($requestedEventID);
        
        # find all participants of the event
        $clientEvents = $this->clientEventRepository->getClientEventByEventId($requestedEventID);
        $progressBar = $this->output->createProgressBar($clientEvents->count());
        $progressBar->start();

        # check the start date of the event and minus 1 day
        # so the system will send reminder at H-1
        $startDate = Carbon::parse($event->event_startdate)->subDay()->format('Y-m-d');
        if (Carbon::now()->format('Y-m-d') != $startDate) {

            // Log::info('Reminder for eduALL Launchpad works fine but will not be send because it is not the date of H-1');
            return;
        }

        foreach ($clientEvents as $clientEvent)
        {
            # if the client event previously has a log mail
            # then no need to do the reminder
            # because we assumed those client events are already reminded which we don't need to do that anymore
            if ( $clientEvent->logMail()->where('category', 'reminder-mail')->where('sent_status', 1)->exists() )
                continue; 
            
            $ticketID = $clientEvent->ticket_id;
            $client = $clientEvent->client;
            $event = $clientEvent->event;

            # we no need to do the reminder
            # when the event has already happened
            // if (Carbon::now()->gt(Carbon::parse($event->event_startdate)))
            //     continue;

            # the data are passing to the template
            $passedData = [
                'clientevent_id' => $clientEvent->clientevent_id,
                'ticket_id' => $ticketID,
                'client_id' => $client->id,
                'child_id' => $clientEvent->child_id ?? null,
                'email' => $client->mail,
                'notes' => $clientEvent->notes,
                'recipient' => $client->full_name,
                'subject' => "[Reminder] Let's come to EduALL Launchpad TOMORROW!",
                'event' => [
                    'eventId' => $event->event_id,
                    'eventName' => $event->event_title,
                    'eventDate' => date('M d, Y', strtotime($event->event_startdate)),
                    'eventDate_start' => date('l, d M Y', strtotime($event->event_startdate)),
                    'eventDate_end' => date('M d, Y', strtotime($event->event_enddate)),
                    'eventTime_start' => date('g A', strtotime($event->event_startdate)),
                    'eventTime_end' => date('H:i', strtotime($event->event_enddate)),
                    'eventLocation' => $event->event_location,
                ]
            ];

            ProcessEmailReminderReg::dispatch($passedData)->onQueue('reminder-mail');


            $progressBar->advance();
            
        }

        $progressBar->finish();


        return Command::SUCCESS;
    }
}
