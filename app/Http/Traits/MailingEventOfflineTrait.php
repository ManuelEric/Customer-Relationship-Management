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
        // $data['referral_link'] = $this->createShortUrl(url('form/event?event_name='.urlencode($event->event_title).'&form_type=cta&event_type=offline&ref='. $referralCode), $referralCode);
                                    
        // $data['referral_link'] = url('form/event?event_name='.urlencode($event->event_name).'&form_type=cta&event_type=offline&ref='. substr($client->fist_name,0,3) . $client->id); 

        try {
            switch ($notes) {
                case 'VVIP':
                    // $isSendMail['status'] = Mail::send('mail-template.thanks-email', $data, function ($message) use ($data) {
                    //     $message->to($data['email'], $data['client']['name'])
                    //         ->subject($data['title']);
                    // });
                    break;
                
                case 'VIP':
                    $data['title'] = 'Thank you for registering as our VIP guest';
                    $data['qr_page'] = route('program.event.qr-page', [
                        'event_slug' => urlencode($event->event_title),
                        'clientevent' => $clientEvent->clientevent_id
                    ]);
                    $data['referral_page'] = route('program.event.referral-page', [
                        'event_slug' => urlencode($event->event_title),
                        'refcode' => $referralCode
                    ]);

                    $data['event'] = [
                        'eventName' => $event->event_title,
                        'eventDate' => date('M d, Y', strtotime($event->event_startdate)),
                        'eventDate_start' => date('l, M d Y', strtotime($event->event_startdate)),
                        'eventDate_end' => date('M d, Y', strtotime($event->event_enddate)),
                        'eventTime_start' => date('g A', strtotime($event->event_startdate)),
                        'eventTime_end' => date('H:i', strtotime($event->event_enddate)),
                        'eventLocation' => $event->event_location,
                    ];
    
                    $isSendMail = Mail::send('mail-template.thanks-email-vip', $data, function ($message) use ($data) {
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


        if($for == 'first-send' && $notes == 'VIP'){
            $logDetails = [
                'clientevent_id' => $clientEvent->clientevent_id,
                'sent_status' => $sent_mail,
                'category' => $notes == 'VVIP' ? 'thanks-mail-referral' : 'qrcode-mail-referral'
            ];
    
            return ClientEventLogMail::create($logDetails);
        }

    }

    public function sendMailInvitation($email, $event_id, $for)
    {

        try {

            $client = UserClient::where('mail', $email)->first();
            $event = Event::where('event_id', $event_id)->first();

            $data['email'] = $email;
            $data['event_id'] = $event_id;
            $data['recipient'] = $client->full_name;
            $data['title'] = "[VIP Special Invitation] STEM+ Wonderlab, Indonesia’s FIRST Student
            Makerspace Expo";
            $data['param'] = [
                'referral_page' => route('program.event.referral-page',[
                    'event_slug' => urlencode($event->event_title),
                    'refcode' => $this->createReferralCode($client->first_name, $client->id)
                ]),                  
                'link' => url('program/event/reg-exp/' . $client['id'] . '/' . $event_id),
            ];
            $data['event'] = [
                'eventName' => $event->event_title,
                'eventDate' => date('M d, Y', strtotime($event->event_startdate)),
                'eventDate_start' => date('l, M d Y', strtotime($event->event_startdate)),
                'eventDate_end' => date('M d, Y', strtotime($event->event_enddate)),
                'eventTime_start' => date('g A', strtotime($event->event_startdate)),
                'eventTime_end' => date('H:i', strtotime($event->event_enddate)),
                'eventLocation' => $event->event_location,
            ];

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
            $keyLog = [
                'client_id' => $client['id'],
                'event_id' => $data['event_id'],
                'sent_status' => $sent_mail,
                'category' => 'invitation-mail'
            ];
            
            $valueLog = [
                'sent_status' => $sent_mail,
            ];
    
            ClientEventLogMail::updateOrCreate($keyLog, $valueLog);
        }


    }

    public function sendMailReminder($email, $event_id, $for, $type)
    {
        
        try {
            $client = UserClient::where('mail', $email)->first();
    
            $event = Event::where('event_id', $event_id)->first();

            $data = [
                'email' => $email,
                'recipient' => $client->full_name,
                'title' => $type == 'registration' ? 'Enjoy special privileges as our VIP guest at STEM+ Wonderlab!' : '🔔 Reminder to our VIP guests of STEM+ Wonderlab',
                'param' => [
                    'referral_page' => route('program.event.referral-page',[
                        'event_slug' => urlencode($event->event_title),
                        'refcode' => $this->createReferralCode($client->first_name, $client->id)
                    ]),                  
                    'link' => url('program/event/reg-exp/' . $client['id'] . '/' . $event_id)
                ],
                'event' => [
                    'eventName' => $event->event_title,
                    'eventDate' => date('M d, Y', strtotime($event->event_startdate)),
                    'eventDate_start' => date('l, M d Y', strtotime($event->event_startdate)),
                    'eventDate_end' => date('M d, Y', strtotime($event->event_enddate)),
                    'eventTime_start' => date('g A', strtotime($event->event_startdate)),
                    'eventTime_end' => date('H:i', strtotime($event->event_enddate)),
                    'eventLocation' => $event->event_location,
                ]
    
            ];
            

            Mail::send('mail-template.reminder-'.$type, $data, function ($message) use ($data) {
                $message->to($data['email'], $data['recipient'])
                    ->subject($data['title']);
            });
            $sent_mail = 1;

        } catch (Exception $e) {

            $sent_mail = 0;
            Log::info('Failed to send reminder registration mail : ' . $e->getMessage());

        }

        if($for == 'first-send'){
            $keyLog = [
                'client_id' => $client['id'],
                'event_id' => $event_id,
                'sent_status' => $sent_mail,
                'category' => 'reminder-'.$type
            ];
            
            $valueLog = [
                'sent_status' => $sent_mail,
            ];
    
            ClientEventLogMail::updateOrCreate($keyLog, $valueLog);
        }


    }
}
