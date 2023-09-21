<?php

namespace App\Console\Commands;

use App\Http\Controllers\ClientEventController;
use App\Http\Traits\MailingEventOfflineTrait;
use App\Interfaces\ClientEventLogMailRepositoryInterface;
use App\Interfaces\ClientEventRepositoryInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResendMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    use MailingEventOfflineTrait;

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
        $unsend_mail = $this->clientEventLogMailRepository->getClientEventLogMail();
        $full_name = '';
        $eventName = '';
        $progressBar = $this->output->createProgressBar($unsend_mail->count());
        $progressBar->start();
        DB::beginTransaction();

        foreach ($unsend_mail as $detail) {

            try {

                $logId = $detail->id;
                $category = $detail->category;

                
                switch ($category) {
                    case 'qrcode-mail':

                        $clientEventId = $detail->clientevent_id;
                        $clientEvent = $this->clientEventRepository->getClientEventById($clientEventId);
                        $eventName = $clientEvent->event->event_title;
                        $client = $clientEvent->client;
                        $full_name = $client->full_name;

                        if($clientEvent->event->event_enddate > Carbon::now()){
                            $clientDetails = ['clientDetails' => 
                                [
                                    'mail' => $client->mail, 
                                    'name' => $client->full_name
                                ]
                            ];
                            
                            $con = app('App\Http\Controllers\ClientEventController')->sendMailQrCode($clientEventId, $eventName, $clientDetails, true);
                        }
                        break;

                    case 'thanks-mail':
                        $clientEventId = $detail->clientevent_id;
                        $clientEvent = $this->clientEventRepository->getClientEventById($clientEventId);
                        
                        $eventName = $clientEvent->event->event_title;
                        $client = $clientEvent->client;
                        $full_name = $client->full_name;

                        if($clientEvent->event->event_enddate > Carbon::now()){
                            
                            $clientDetails = ['clientDetails' => 
                                [
                                    'mail' => $client->mail, 
                                    'name' => $client->full_name
                                ]
                            ];
                            
                            $con = app('App\Http\Controllers\ClientEventController')->sendMailThanks($clientEventId, $eventName, $clientDetails, true);
                        }
                        break;

                    case 'thanks-mail-referral':
                        $clientEventId = $detail->clientevent_id;
                        $eventName = $detail->clientEvent->event->event_title;

                        $this->sendMailReferral($detail->clientEvent, 'VVIP', 'automate');
                        break;

                    case 'qrcode-mail-referral':
                        $clientEventId = $detail->clientevent_id;
                        $eventName = $detail->clientEvent->event->event_title;

                        $this->sendMailReferral($detail->clientEvent, 'VIP', 'automate');
                        break;
                    
                    case 'invitation-mail':
                        if($detail->event->event_enddate > Carbon::now()){
                            $this->sendMailInvitation($detail->client->mail, $detail->event->event_id, 'automate');
                        }
                        break;

                    case 'reminder-registration':
                        $this->sendMailReminder($detail->client->mail, $detail->event->event_id, 'automate', 'registration');
                        break;

                    case 'reminder-referral':
                        $this->sendMailReminder($detail->client->mail, $detail->event->event_id, 'automate', 'referral');
                        break;
                }
                    

                $progressBar->advance();
        
                
                DB::commit();
                $sent_mail = 1;

            } catch (Exception $e) {
                
                Log::error('Failed to send mail QrCode for : '.$full_name.' on the event : '.$eventName.' | Error '.$e->getMessage().' Line '.$e->getLine());
                $sent_mail = 0;
                
            }

            $logDetails['sent_status'] = $sent_mail;
            
            try {

                $this->clientEventLogMailRepository->updateClientEventLogMail($logId, $logDetails);
                DB::commit();

            } catch (Exception $e) {

                DB::rollBack();
                Log::error('Failed to update client event log mail for Id : '.$logId. ' | Error '. $e->getMessage().' Line '.$e->getLine());
            }
        }


        $progressBar->finish();
        return Command::SUCCESS;
    }
}
