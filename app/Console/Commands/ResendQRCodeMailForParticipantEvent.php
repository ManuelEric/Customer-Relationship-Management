<?php

namespace App\Console\Commands;

use App\Http\Controllers\ClientEventController;
use App\Interfaces\ClientEventLogMailRepositoryInterface;
use App\Interfaces\ClientEventRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResendQRCodeMailForParticipantEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'automate:resend_qrcode_mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resend the mail (qrcode) for participant event.';

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
        $unsend_qrcode = $this->clientEventLogMailRepository->getClientEventLogMail('qrcode-mail');
        $progressBar = $this->output->createProgressBar($unsend_qrcode->count());
        $progressBar->start();
        DB::beginTransaction();

        foreach ($unsend_qrcode as $detail) {

            try {

                $logId = $detail->id;
    
                $clientEventId = $detail->clientevent_id;
                $clientEvent = $this->clientEventRepository->getClientEventById($clientEventId);
                $eventName = $clientEvent->event->event_title;
                $client = $clientEvent->client;
    
                $clientDetails = ['clientDetails' => 
                    [
                        'mail' => $client->mail, 
                        'name' => $client->full_name
                    ]
                ];
                
    
                $con = app('App\Http\Controllers\ClientEventController')->sendMailQrCode($clientEventId, $eventName, $clientDetails, true);

                $progressBar->advance();
        
                
                DB::commit();
                $sent_mail = 1;

            } catch (Exception $e) {
                
                Log::error('Failed to send mail QrCode for : '.$client->full_name.' on the event : '.$eventName.' | Error '.$e->getMessage().' Line '.$e->getLine());
                $sent_mail = 0;
                
            }

            $logDetails = [
                'clientevent_id' => $clientEventId,
                'sent_status' => $sent_mail
            ];
            
            $this->clientEventLogMailRepository->updateClientEventLogMail($logId, $logDetails);
        }


        $progressBar->finish();
        return Command::SUCCESS;
    }
}
