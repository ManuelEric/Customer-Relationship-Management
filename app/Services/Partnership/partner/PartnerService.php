<?php

namespace App\Services\Partnership\Partner;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PartnerService 
{
    public function __construct()
    {
       
    }

    # Purpose:
    # Send mail reminder expiration agreement to Internal Team
    public function snSendMailExpirationAgreement(String $pic_mail, array $partner_agreement_expired_soon): void
    {
        $subject_mail = 'Reminder: Upcoming Agreement Expiry';
        $mail_resources = 'mail-template.reminder.agreement.partner.expiration-agreement-reminder';
        
        try {
            Mail::send($mail_resources, ['agreement' => $partner_agreement_expired_soon, 'title' => 'Test'], function ($message) use ($subject_mail, $pic_mail) {
                $message->to($pic_mail)
                    ->cc(env('PARTNERSHIP_MAIL'))
                    ->subject($subject_mail);
            });

        } catch (Exception $e) {

            Log::error('Failed to send mail expiration partner agreement to Partnership Team caused by : ' . $e->getMessage() . ' | Line ' . $e->getLine());
        }
    }

   
}