<?php

namespace App\Http\Traits;

// use App\Interfaces\ClientEventRepositoryInterface;
use App\Models\ClientEvent;
use App\Models\ClientEventLogMail;
use App\Models\Event;
use App\Models\UserClient;
// use App\Repositories\ClientEventRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

trait RegisterExpressTrait
{
    // private ClientEventRepositoryInterface $clientEventRepository;

    // public function _construct(ClientEventRepository $clientEventRepository){
    //     $this->clientEventRepository = $clientEventRepository;
    // }

    public function register($email, $event_id, $notes)
    {
        $data = [
            'success' => false,
            'already_join' => false,
        ];

        // $isSendMail = [
        //     'status' => false,
        //     'category' => ''
        // ];
        
        DB::beginTransaction();

        try {
            $client = UserClient::where('mail', $email)->first();
            $client_id = $client->id;

            $checkJoined = ClientEvent::where('client_id', $client_id)->where('event_id', $event_id)->first();
            $event = Event::where('event_id', $event_id)->first();

            $clientEvents = [
                'client_id' => $client_id,
                'child_id' => $client->childrens->count() > 0 ? $client->childrens[0]->id : null,
                'event_id' => $event_id,
                'lead_id' => 'LS012',
                'status' => $notes == 'VVIP' ? 1 : 0,
                'notes' => $notes,
                'joined_date' => Carbon::now(),
            ];

            $data['email'] = $client->mail;
            $data['client'] = [
                'name' => $client->full_name
            ];
            $data['title'] = "You have Successfully registered STEM+ WONDERLAB";
            $data['notes'] = $notes;
            $data['referral_link'] = url('form/event?event_name='.urlencode($event->event_name).'&form_type=cta&event_type=offline&ref='. substr($client->fist_name,0,3) . $client->id); 
            $data['event'] = [
                'eventName' => $event->event_title,
                'eventDate' => date('M d, Y', strtotime($event->event_startdate)),
                'eventDate_start' => date('M d, Y', strtotime($event->event_startdate)),
                'eventDate_end' => date('M d, Y', strtotime($event->event_enddate)),
                'eventTime_start' => date('H:i', strtotime($event->event_startdate)),
                'eventTime_end' => date('H:i', strtotime($event->event_enddate)),
                'eventLocation' => $event->event_location,
            ];
            
            $data['success'] = true;
            $data['already_join'] = true;

            if (!isset($checkJoined)) {
                if(!$clientEvent = ClientEvent::create($clientEvents))
                    throw new Exception('Store client event', 1);


                UserClient::whereId($client_id)->update(['register_as' => 'parent']);
                if ($client->childrens->count() > 0) {
                    UserClient::whereId($client->childrens[0]->id)->update(['register_as' => 'parent']);
                }

                $data['url'] = route('link-event-attend', [
                    'event_slug' => urlencode($data['event']['eventName']),
                    'clientevent' => $clientEvent->clientevent_id
                ]);

                // switch ($notes) {
                //     case 'VVIP':
                //         $isSendMail['status'] = Mail::send('mail-template.thanks-email', $data, function ($message) use ($data) {
                //             $message->to($data['email'], $data['client']['name'])
                //                 ->subject($data['title']);
                //         });
                //         $isSendMail['category'] = 'thanks-mail';
                //         break;
                    
                //     case 'VIP':
                //         $isSendMail = Mail::send('mail-template.event-registration-success', $data, function ($message) use ($data) {
                //             $message->to($data['email'], $data['client']['name'])
                //                 ->subject($data['title']);
                //         });   
                //         $isSendMail['category'] = 'qrcode-mail';                     
                //         break;
                // }

                $this->sendMail($clientEvent->clientevent_id, $data, $notes);
                

                $data['success'] = true;
                $data['already_join'] = false;  
            }
            
            // if(!$isSendMail['status']){
            //     Log::info('Failed to send mail' . $notes);
            //     throw new Exception('Failed to send mail' . $notes, 2);
            // }else{
            //     Log::info('Client ' . $data['email'] . ' successfully register express');
            // }
            
            
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            // switch ($e->getCode()) {
            //     case 1:
            //         Log::error('Store client event failed : ' . $e->getMessage());
            //         break;

            //     case 2:
            //         Log::error('Send mail : ' . $e->getMessage());
            //         break;

            // }

            Log::error('Register express client event failed : ' . $e->getMessage());

            $data['success'] = false;
        }

        return $data;
    }

    private function sendMail($clientEventId, $data, $notes)
    {
        try {
            switch ($notes) {
                case 'VVIP':
                    $isSendMail['status'] = Mail::send('mail-template.thanks-email', $data, function ($message) use ($data) {
                        $message->to($data['email'], $data['client']['name'])
                            ->subject($data['title']);
                    });
                    break;
                
                case 'VIP':
                    $isSendMail = Mail::send('mail-template.event-registration-success', $data, function ($message) use ($data) {
                        $message->to($data['email'], $data['client']['name'])
                            ->subject($data['title']);
                    });   
                    break;
            }
            $sent_mail = 1;
            
        } catch (Exception $e) {
            
            $sent_mail = 0;
            Log::error('Failed send email '.$notes.' | error : '.$e->getMessage().' | Line '.$e->getLine());

        }

        $logDetails = [
            'clientevent_id' => $clientEventId,
            'sent_status' => $sent_mail,
            'category' => $notes == 'VVIP' ? 'thanks-mail' : 'qrcode-mail'
        ];

        return ClientEventLogMail::create($logDetails);

    }
}
