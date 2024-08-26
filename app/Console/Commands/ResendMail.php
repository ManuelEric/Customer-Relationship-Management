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

    protected $signature = 'mailing:resend_unsend_mail';

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

            // echo json_encode($detail->event);
            try {

                $logId = $detail->id;
                $category = $detail->category;

                if($detail->clientevent_id != null){
                    # basic info
                    $clientEventId = $detail->clientevent_id;
                    $clientEvent = $this->clientEventRepository->getClientEventById($clientEventId);
                    $eventName = $clientEvent->event->event_title;
                    $client = $clientEvent->client;
                    $full_name = $client->full_name;
                }

                
                switch ($category) {
                    case 'qrcode-mail':

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

                    # not used for edu ALL  
                    case 'thanks-mail-referral':
                        $clientEventId = $detail->clientevent_id;
                        $eventName = $detail->clientEvent->event->event_title;

                        $this->sendMailReferral($detail->clientEvent, 'VVIP', 'automate');
                        break;

                    # not used for edu ALL
                    case 'qrcode-mail-referral':
                        $clientEventId = $detail->clientevent_id;
                        $eventName = $detail->clientEvent->event->event_title;

                        $this->sendMailReferral($detail->clientEvent, $detail->clientEvent->notes, 'automate');
                        break;
                    
                    # VIP
                    case 'invitation-mail':
                        if($detail->event->event_enddate > Carbon::now()){
                            $this->sendMailInvitation($detail->client_id, $detail->event_id, $detail->child_id, $detail->notes);
                        }
                        break;

                    # VIP
                    case 'reminder-registration':
                        if($detail->event->event_enddate > Carbon::now()){
                            $this->sendMailReminder($detail->client_id, $detail->event_id, 'automate', 'registration', $detail->child_id, $detail->notes);
                        }
                        break;

                    # not used for edu ALL
                    case 'reminder-referral':
                        if($detail->event->event_enddate > Carbon::now()){
                            $this->sendMailReminder($detail->client->mail, $detail->event->event_id, 'automate', 'referral', $detail->index_child, $detail->notes);
                        }
                        break;

                    # not used for edu ALL
                    case 'reminder-attend':
                        if($detail->clientEvent->event->event_enddate > Carbon::now()){
                            $this->sendMailReminderAttend($detail->clientEvent, 'automate');
                        }
                        break;

                    # not used
                    # utk VIP yg general
                    case 'invitation-info':
                        if($detail->event->event_enddate > Carbon::now()){
                            $data = [
                                'client' => [
                                    'client_id' => $detail->client_id,
                                    'email' => $detail->client->mail,
                                    'recipient' => $detail->client->full_name,
                                ],
                                'event_id' => $detail->event_id,
                                'notes' => 'WxSFs0LGh',
                            ];

                            $this->sendMailInvitationInfo($data, 'automate');
                        }
                        break;

                    case 'registration-event-mail':

                        # create the request that being used in send email registration
                        $request = [
                            'registration_type' => $clientEvent->registration_type,
                            'fullname' => $clientEvent->client->full_name,
                            'mail' => $clientEvent->client->mail,
                            'notes' => $clientEvent->notes
                        ];

                        # repeat the function to send the email 
                        # the purpose is to re-send the email that failed to sent
                        app('App\Http\Controllers\Api\v1\ExtClientController')->sendEmailRegistrationSuccess($request, $clientEvent);

                        break;

                    case 'verification-registration-event-mail':

                        # create the request that being used in send email registration
                        $request = [
                            'registration_type' => $clientEvent->registration_type,
                            'fullname' => $clientEvent->client->full_name,
                            'mail' => $clientEvent->client->mail,
                            'notes' => $clientEvent->notes
                        ];

                        # repeat the function to send the email 
                        # the purpose is to re-send the email that failed to sent
                        app('App\Http\Controllers\Api\v1\ExtClientController')->sendEmailVerificationSuccess($request, $clientEvent);
                        break;
                }
                    

                $progressBar->advance();
        
                
                DB::commit();
                $sent_mail = 1;

            } catch (Exception $e) {
                
                Log::error("Failed to send mail {$category} for : {$full_name} on the event : {$eventName} | Error {$e->getMessage()} Line {$e->getLine()}");
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
