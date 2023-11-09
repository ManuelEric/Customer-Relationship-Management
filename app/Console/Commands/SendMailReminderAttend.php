<?php

namespace App\Console\Commands;

use App\Http\Controllers\ClientEventController;
use App\Http\Traits\MailingEventOfflineTrait;
use App\Interfaces\ClientEventLogMailRepositoryInterface;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Models\ClientEvent;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendMailReminderAttend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    use MailingEventOfflineTrait;

    protected $signature = 'automate:send_mail_reminder_attend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the mail reminder attend STEM+ Wonderlab.';

    private ClientEventLogMailRepositoryInterface $clientEventLogMailRepository;
    private ClientEventRepositoryInterface $clientEventRepository;

    public function __construct(ClientEventLogMailRepositoryInterface $clientEventLogMailRepository, ClientEventRepositoryInterface $clientEventRepository)
    {
        parent::__construct();
        $this->clientEventLogMailRepository = $clientEventLogMailRepository;
        $this->clientEventRepository = $clientEventRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $clientEvents = $this->clientEventRepository->getClientEventByEventId('EVT-0008');
        $clientEvents = ClientEvent::where('clientevent_id', 3860)->get();

        $full_name = '';
        $eventName = 'STEM+ Wonderlab';
        $progressBar = $this->output->createProgressBar($clientEvents->count());
        $progressBar->start();

        DB::beginTransaction();

        foreach ($clientEvents as $detail) {

            try {

                $this->sendMailReminderAttend($detail, 'first-send');

                $full_name = $detail->client->full_name;

                $progressBar->advance();
                
                DB::commit();

            } catch (Exception $e) {

                DB::rollBack();
                Log::error('Failed to send mail reminder attend for : '.$full_name.' on the event : '.$eventName.' | Error '.$e->getMessage().' Line '.$e->getLine());
                
            }
        }


        $progressBar->finish();
        return Command::SUCCESS;
    }
}
