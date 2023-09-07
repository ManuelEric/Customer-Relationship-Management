<?php

namespace App\Http\Traits;

use App\Models\ClientEvent;
use App\Models\ClientEventLogMail;
use App\Models\Event;
use App\Models\UserClient;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use AshAllenDesign\ShortURL\Facades\ShortURL;

trait MailingEventOfflineTrait
{
    use CreateReferralCodeTrait;
    use CreateShortUrlTrait;
    
    public function register($email, $event_id, $notes)
    {
        $data = [
            'success' => false,
            'already_join' => false,
        ];
        
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
            
            $data['success'] = true;
            $data['already_join'] = true;

            if (!isset($checkJoined)) {
                if(!$clientEvent = ClientEvent::create($clientEvents))
                    throw new Exception('Store client event', 1);


                UserClient::whereId($client_id)->update(['register_as' => 'parent']);
                if ($client->childrens->count() > 0) {
                    UserClient::whereId($client->childrens[0]->id)->update(['register_as' => 'parent']);
                }


                $this->sendMailReferral($clientEvent, $notes, 'first-send');
                

                $data['success'] = true;
                $data['already_join'] = false;  
            }
            
            
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            Log::error('Register express client event failed : ' . $e->getMessage());

            $data['success'] = false;
        }

        return $data;
    }

    public function sendMailReferral($clientEvent, $notes, $for)
    {
        $client = $clientEvent->client;
        $event = $clientEvent->event;
        $referralCode = $this->createReferralCode($client->first_name, $client->id);

        $data['email'] = $client->mail;
        $data['client'] = [
            'name' => $client->full_name
        ];
        $data['title'] = "Invitation For STEM+ Wonderlab";
        $data['notes'] = $notes;
        $data['referral_link'] = $this->createShortUrl(url('form/event?event_name='.urlencode($event->event_title).'&form_type=cta&event_type=offline&ref='. $referralCode), $referralCode);
                                    
        // $data['referral_link'] = url('form/event?event_name='.urlencode($event->event_name).'&form_type=cta&event_type=offline&ref='. substr($client->fist_name,0,3) . $client->id); 
        $data['event'] = [
            'eventName' => $event->event_title,
            'eventDate' => date('M d, Y', strtotime($event->event_startdate)),
            'eventDate_start' => date('M d, Y', strtotime($event->event_startdate)),
            'eventDate_end' => date('M d, Y', strtotime($event->event_enddate)),
            'eventTime_start' => date('H:i', strtotime($event->event_startdate)),
            'eventTime_end' => date('H:i', strtotime($event->event_enddate)),
            'eventLocation' => $event->event_location,
        ];

        try {
            switch ($notes) {
                case 'VVIP':
                    $isSendMail['status'] = Mail::send('mail-template.thanks-email', $data, function ($message) use ($data) {
                        $message->to($data['email'], $data['client']['name'])
                            ->subject($data['title']);
                    });
                    break;
                
                case 'VIP':
                    $data['title'] = 'You have Successfully registered STEM+ WONDERLAB';
                    $data['url'] = route('link-event-attend', [
                        'event_slug' => urlencode($event->event_title),
                        'clientevent' => $clientEvent->clientevent_id
                    ]);
    
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


        if($for == 'first-send'){
            $logDetails = [
                'clientevent_id' => $clientEvent->clientevent_id,
                'sent_status' => $sent_mail,
                'category' => $notes == 'VVIP' ? 'thanks-mail-referral' : 'qrcode-mail-referral'
            ];
    
            return ClientEventLogMail::create($logDetails);
        }

    }

    public function sendMailInvitation($data, $client, $for)
    {

        try {

            Mail::send('mail-template.invitation-email', $data, function ($message) use ($data) {
                $message->to($data['email'], $data['recipient'])
                    ->subject($data['title']);
            });
            $sent_mail = 1;

        } catch (Exception $e) {

            $sent_mail = 0;
            Log::info('Failed to send invitation mail : ' . $e->getMessage());

        }

        if($for == 'first-send'){
            $logDetails = [
                'client_id' => $client['id'],
                'event_id' => $data['event_id'],
                'sent_status' => $sent_mail,
                'category' => 'invitation-mail'
            ];
    
            ClientEventLogMail::create($logDetails);
        }


    }
}
