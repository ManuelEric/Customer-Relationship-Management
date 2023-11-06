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

class SendMailReminderStemH1 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    use MailingEventOfflineTrait;

    protected $signature = 'automate:send_mail_reminder_stem_h1';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the mail reminder stem h1.';

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
        $unsend_mail = $this->clientEventRepository->getClientEventById(3648);
        $full_name = '';
        $eventName = '';
        $progressBar = $this->output->createProgressBar($unsend_mail->count());
        $progressBar->start();
        DB::beginTransaction();
        Log::debug(json_encode($unsend_mail));

        // foreach ($unsend_mail as $detail) {

            try {

                $this->sendMailReminderH1($unsend_mail);


                
                // switch ($category) {
                //     case 'qrcode-mail':

                //         $clientEventId = $detail->clientevent_id;
                //         $clientEvent = $this->clientEventRepository->getClientEventById($clientEventId);
                //         $eventName = $clientEvent->event->event_title;
                //         $client = $clientEvent->client;
                //         $full_name = $client->full_name;

                //         if($clientEvent->event->event_enddate > Carbon::now()){
                //             $clientDetails = ['clientDetails' => 
                //                 [
                //                     'mail' => $client->mail, 
                //                     'name' => $client->full_name
                //                 ]
                //             ];
                            
                //             $con = app('App\Http\Controllers\ClientEventController')->sendMailQrCode($clientEventId, $eventName, $clientDetails, true);
                //         }
                //         break;

                //     case 'thanks-mail':
                //         $clientEventId = $detail->clientevent_id;
                //         $clientEvent = $this->clientEventRepository->getClientEventById($clientEventId);
                        
                //         $eventName = $clientEvent->event->event_title;
                //         $client = $clientEvent->client;
                //         $full_name = $client->full_name;

                //         if($clientEvent->event->event_enddate > Carbon::now()){
                            
                //             $clientDetails = ['clientDetails' => 
                //                 [
                //                     'mail' => $client->mail, 
                //                     'name' => $client->full_name
                //                 ]
                //             ];
                            
                //             $con = app('App\Http\Controllers\ClientEventController')->sendMailThanks($clientEventId, $eventName, $clientDetails, true);
                //         }
                //         break;

                //     case 'thanks-mail-referral':
                //         $clientEventId = $detail->clientevent_id;
                //         $eventName = $detail->clientEvent->event->event_title;

                //         $this->sendMailReferral($detail->clientEvent, 'VVIP', 'automate');
                //         break;

                //     case 'qrcode-mail-referral':
                //         $clientEventId = $detail->clientevent_id;
                //         $eventName = $detail->clientEvent->event->event_title;

                //         $this->sendMailReferral($detail->clientEvent, $detail->clientEvent->notes, 'automate');
                //         break;
                    
                //     case 'invitation-mail':
                //         if($detail->event->event_enddate > Carbon::now()){
                //             $this->sendMailInvitation($detail->client->mail, $detail->event->event_id, 'automate', $detail->index_child, $detail->notes);
                //         }
                //         break;

                //     case 'reminder-registration':
                //         if($detail->event->event_enddate > Carbon::now()){
                //             $this->sendMailReminder($detail->client->mail, $detail->event->event_id, 'automate', 'registration', $detail->index_child, $detail->notes);
                //         }
                //         break;

                //     case 'reminder-referral':
                //         if($detail->event->event_enddate > Carbon::now()){
                //             $this->sendMailReminder($detail->client->mail, $detail->event->event_id, 'automate', 'referral', $detail->index_child, $detail->notes);
                //         }
                //         break;
                // }
                    

                $progressBar->advance();
        
                
                DB::commit();
                $sent_mail = 1;

            } catch (Exception $e) {
                
                Log::error('Failed to send mail QrCode for : '.$full_name.' on the event : '.$eventName.' | Error '.$e->getMessage().' Line '.$e->getLine());
                $sent_mail = 0;
                
            }

            $logDetails['sent_status'] = $sent_mail;
            
            try {

                // $this->clientEventLogMailRepository->updateClientEventLogMail($logId, $logDetails);
                DB::commit();

            } catch (Exception $e) {

                DB::rollBack();
                Log::error('Failed to update client event log mail for Id : | Error '. $e->getMessage().' Line '.$e->getLine());
            }
        // }


        $progressBar->finish();
        return Command::SUCCESS;
    }
}
