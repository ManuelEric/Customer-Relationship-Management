<?php

namespace App\Http\Traits;

use App\Jobs\Event\EduAll\ProcessEmailInvitationInfo;
use App\Jobs\Event\EduAll\ProcessEmailInvitationVIP;
use App\Jobs\Event\EduAll\ProcessEmailReminderVIP;
use App\Jobs\Event\Stem\ProcessEmailFeedback;
use App\Jobs\Event\Stem\ProcessEmailQuestCompleter;
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

    public function register($email, $event_id, $notes, $indexChild)
    {
        $data = [
            'success' => false,
            'already_join' => false,
        ];

        switch ($notes) {
            case 'VIP':
            case 'WxSFs0LGh': # Mean VIP
                $notes = 'VIP';
                break;

            case 'VVIP':
            case 'BtSF0x1hK': # Mean VVIP
                $notes = 'VVIP';
                break;

            default:
                abort(404);
                break;
        }


        DB::beginTransaction();

        try {
            $client = UserClient::where('mail', $email)->first();
            $client_id = $client->id;

            $checkJoined = ClientEvent::where('client_id', $client_id)->where('event_id', $event_id)->first();
            $event = Event::where('event_id', $event_id)->first();

            $clientEvents = [
                'client_id' => $client_id,
                'child_id' => $client->childrens->count() > 0 ? $client->childrens[$indexChild]->id : null,
                'event_id' => $event_id,
                'lead_id' => 'LS040',
                'status' => 0,
                'notes' => $notes,
                'joined_date' => Carbon::now(),
            ];

            $data['success'] = true;
            $data['already_join'] = true;

            if (!isset($checkJoined)) {
                if (!$clientEvent = ClientEvent::create($clientEvents))
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
        $noteEncrypt = '';
        switch ($notes) {
            case 'VIP':
            case 'WxSFs0LGh': # Mean VIP
                $notes = 'VIP';
                $noteEncrypt = 'WxSFs0LGh';
                break;

            case 'VVIP':
            case 'BtSF0x1hK': # Mean VVIP
                $notes = 'VVIP';
                $noteEncrypt = 'BtSF0x1hK';
                break;
        }

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

        $data['qr_page'] = route('program.event.qr-page', [
            'event_slug' => str_replace(' ', '-', $event->event_title),
            'clientevent' => $clientEvent->clientevent_id
        ]);
        $data['referral_page'] = route('program.event.referral-page', [
            'event_slug' => str_replace(' ', '-', $event->event_title),
            'refcode' => $referralCode,
            'notes' => $noteEncrypt
        ]);

        $data['event'] = [
            'eventName' => $event->event_title,
            'eventDate' => date('M d, Y', strtotime($event->event_startdate)),
            'eventDate_start' => date('l, d M Y', strtotime($event->event_startdate)),
            'eventDate_end' => date('M d, Y', strtotime($event->event_enddate)),
            'eventTime_start' => date('g A', strtotime($event->event_startdate)),
            'eventTime_end' => date('H:i', strtotime($event->event_enddate)),
            'eventLocation' => $event->event_location,
        ];

        try {
            switch ($notes) {
                case 'VVIP':
                    $data['title'] = 'Thank you for registering as our VVIP guest';
                    // $isSendMail['status'] = Mail::send('mail-template.thanks-email', $data, function ($message) use ($data) {
                    //     $message->to($data['email'], $data['client']['name'])
                    //         ->subject($data['title']);
                    // });
                    $isSendMail = Mail::send('mail-template.thanks-email-vip', $data, function ($message) use ($data) {
                        $message->to($data['email'], $data['client']['name'])
                            ->subject($data['title']);
                    });
                    break;

                case 'VIP':
                    $data['title'] = 'Thank you for registering as our VIP guest';


                    $isSendMail = Mail::send('mail-template.thanks-email-vip', $data, function ($message) use ($data) {
                        $message->to($data['email'], $data['client']['name'])
                            ->subject($data['title']);
                    });
                    break;
            }
            $sent_mail = 1;
        } catch (Exception $e) {

            $sent_mail = 0;
            Log::error('Failed send email ' . $notes . ' | error : ' . $e->getMessage() . ' | Line ' . $e->getLine());
        }


        if ($for == 'first-send') {
            $logDetails = [
                'clientevent_id' => $clientEvent->clientevent_id,
                'sent_status' => $sent_mail,
                'category' => 'qrcode-mail-referral'
            ];

            return ClientEventLogMail::create($logDetails);
        }
    }

    public function sendMailInvitation($client_id, $event_id, $child_id, $notes)
    {

        DB::beginTransaction();

        try {

            $noteEncrypt = '';
            switch ($notes) {
                case 'VIP':
                case 'WxSFs0LGh': # Mean VIP
                    $notes = 'VIP';
                    $noteEncrypt = 'WxSFs0LGh';
                    break;

                case 'VVIP':
                case 'BtSF0x1hK': # Mean VVIP
                    $notes = 'VVIP';
                    $noteEncrypt = 'BtSF0x1hK';
                    break;
            }

            $client = UserClient::where('id', $client_id)->first();
            $event = Event::where('event_id', $event_id)->first();
            if ($child_id != null)
                $child = UserClient::where('id', $child_id)->first();

            $data['client_id'] = $client_id;
            $data['email'] = $client->mail;
            $data['child_id'] = $child_id;
            $data['child_name'] = $child_id != null ? $child->full_name : null;
            $data['event_id'] = $event_id;
            $data['recipient'] = $client->full_name;
            $data['title'] = 'You’re Invited: EduALL Launchpad: Where Your Future Takes Off!';
            $data['param'] = [
                // 'referral_page' => route('program.event.referral-page', [
                //     'event_slug' => str_replace(' ', '-', $event->event_title),
                //     'refcode' => $this->createReferralCode($client->first_name, $client->id),
                //     'notes' => $noteEncrypt
                // ]),
                'link' => route('register-express-event', ['main_client' => $client->id, 'notes' => $noteEncrypt, 'second_client' => $child_id, 'EVT' => $event_id]),
            ];
            $data['event'] = [
                'eventId' => $event->event_id, 
                'eventName' => $event->event_title,
                'eventDate' => date('M d, Y', strtotime($event->event_startdate)),
                'eventDate_start' => date('l, d M Y', strtotime($event->event_startdate)),
                'eventDate_end' => date('M d, Y', strtotime($event->event_enddate)),
                'eventTime_start' => date('g A', strtotime($event->event_startdate)),
                'eventTime_end' => date('H:i', strtotime($event->event_enddate)),
                'eventLocation' => $event->event_location,
            ];
            $data['notes'] = $notes;

            ProcessEmailInvitationVIP::dispatch($data)->onQueue('invitation-vip');

            // Mail::send('mail-template.invitation-email', $data, function ($message) use ($data) {
            //     $message->to($data['email'], $data['recipient'])
            //         ->subject($data['title']);
            // });
            // $sent_mail = 1;

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            // $sent_mail = 0;
            Log::error('Failed to send invitation mail : ' . $e->getMessage());
        }


    }

    public function sendMailReminder($client_id, $event_id, $for, $type, $child_id, $notes)
    {

        try {

            $noteEncrypt = '';
            switch ($notes) {
                case 'VIP':
                case 'WxSFs0LGh': # Mean VIP
                    $notes = 'VIP';
                    $noteEncrypt = 'WxSFs0LGh';
                    break;

                case 'VVIP':
                case 'BtSF0x1hK': # Mean VVIP
                    $notes = 'VVIP';
                    $noteEncrypt = 'BtSF0x1hK';
                    break;
            }

            $client = UserClient::where('id', $client_id)->first();
            if ($child_id != null)
                $child = UserClient::where('id', $child_id)->first();

            $event = Event::where('event_id', $event_id)->first();

            $data = [
                'for' => $for,
                'client_id' => $client->id,
                'child_id' => $child_id,
                'email' => $client->mail,
                'notes' => $notes,
                'recipient' => $client->full_name,
                'child_name' => $child_id != null ? $child->full_name : null,
                'title' => '[Reminder] Let’s come to EduALL Launchpad TOMORROW!',
                'param' => [
                    'link' => route('register-express-event', ['main_client' => $client->id, 'notes' => $noteEncrypt, 'second_client' => $child_id, 'EVT' => $event_id]),
                ],
                'event' => [
                    'eventId' => $event_id,
                    'eventName' => $event->event_title,
                    'eventDate' => date('M d, Y', strtotime($event->event_startdate)),
                    'eventDate_start' => date('l, d M Y', strtotime($event->event_startdate)),
                    'eventDate_end' => date('M d, Y', strtotime($event->event_enddate)),
                    'eventTime_start' => date('g:i A', strtotime($event->event_startdate)),
                    'eventTime_end' => date('H:i', strtotime($event->event_enddate)),
                    'eventLocation' => $event->event_location,
                ]

            ];

            ProcessEmailReminderVIP::dispatch($data)->onQueue('reminder-mail');

        } catch (Exception $e) {

            Log::info('Failed to add queue mail reminder : ' . $e->getMessage());
        }

    }

    public function sendMailReminderAttend($clientEvent, $for)
    {

        try {

            $role = $clientEvent->client->roles->first()->role_name;

            $data['qr'] =  route('link-event-attend', [
                // 'event_slug' => $event_slug,
                'clientevent' => $clientEvent->clientevent_id
            ]);

            $date = Carbon::parse($clientEvent->event->event_startdate)->locale('id');

            if ($role == 'Parent')
                $date->settings(['formatFunction' => 'translatedFormat']);

            $data = [
                'email' => $clientEvent->client->mail,
                'role' => $role,
                'qr' => route('link-event-attend', [
                    'clientevent' => $clientEvent->clientevent_id
                ]),
                // 'notes' => $notes,
                'recipient' => $clientEvent->client->full_name,
                'title' =>  'STEM+ Wonderlab QR Code Entrance',
                'event' => [
                    'eventName' => $clientEvent->event->event_title,
                    'eventDate_start' => $date->format('l, d M Y'),
                    'eventTime_start' => $date->format('g A'),
                    'eventLocation' => $clientEvent->event->event_location,
                ]

            ];


            Mail::send('mail-template.reminder-attend', $data, function ($message) use ($data) {
                $message->to($data['email'], $data['recipient'])
                    ->subject($data['title']);
            });
            $sent_mail = 1;
        } catch (Exception $e) {

            $sent_mail = 0;
            Log::info('Failed to send reminder attend mail : ' . $e->getMessage() . $e->getLine());
        }

        if ($for == 'first-send') {
            $keyLog = [
                'clientevent_id' => $clientEvent->clientevent_id,
                'sent_status' => $sent_mail,
                'category' => 'reminder-attend'
            ];

            $valueLog = [
                'sent_status' => $sent_mail,
            ];

            ClientEventLogMail::updateOrCreate($keyLog, $valueLog);
        }
    }

    public function sendMailCompleterQuest($email, $fullname, $level)
    {

        try {

            $data = [
                'email' => $email,
                'level' => $level,
                'recipient' => $fullname,
                'wa_text_anggie' => 'Hello Anggie, I’m ' . $fullname . ', I have attended STEM+ Wonderlab and would like to claim 100 USD discount for the Innovators-in-Residence program in Singapore. Can you give me further information about this program?',
                'wa_text_derry' => 'Hello Derry, I’m ' . $fullname . ', I have attended STEM+ Wonderlab and would like to claim 100 USD discount for the Innovators-in-Residence program in Singapore. Can you give me further information about this program?'
            ];

            ProcessEmailQuestCompleter::dispatch($data)->onQueue('quest-completer_EVT-0008');

            // $sent_mail = 1;
        } catch (Exception $e) {

            // $sent_mail = 0;
            Log::info('Failed to send quest completer mail : ' . $e->getMessage());
        }

        // Log::debug('Send quest completer mail fullname: ' . $fullname . ' status: ' . $sent_mail, ['fullname' => $fullname, 'email' => $email, 'level' => $level, 'sent_status' => $sent_mail]);
    }

    # Just send mail invitation information only not include register express
    public function sendMailInvitationInfo($details, $for) 
    {
        try {

            $event = Event::whereEventId($details['event_id']);

            $date = Carbon::parse($event->event_startdate)->locale('id');
            
            $details['event'] = [
                'eventName' => $event->event_title,
                'eventDate_start' => $date->format('l, d M Y'),
                'eventTime_start' => $date->format('g A'),
                'eventLocation' => $event->event_location,
            ];
            $details['for'] = $for;
            $details['title'] = 'You’re Invited: EduALL Launchpad: Where Your Future Takes Off!';
            $details['cta'] = 'https://launchpad.edu-all.com';

            ProcessEmailInvitationInfo::dispatch($details)->onQueue('invitation-info');

        } catch (Exception $e) {

            Log::info('Failed to add queue mail invitation info : ' . $e->getMessage());
        }
    }
}