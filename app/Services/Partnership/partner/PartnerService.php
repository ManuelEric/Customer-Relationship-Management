<?php

namespace App\Services\Partnership\Partner;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PartnerService 
{

    # Purpose:
    # Send mail reminder expiration agreement to Internal Team
    public function snSendMailExpirationAgreement(array $partner_agreement_expired_soon, $recipient, Array $cc_mail): void
    {
        $subject_mail = 'Reminder: Upcoming Agreement Expiry';
        $mail_resources = 'mail-template.reminder.agreement.partner.expiration-agreement-reminder';
        
        try {
            Mail::send($mail_resources, ['agreement' => $partner_agreement_expired_soon], function ($message) use ($subject_mail, $recipient, $cc_mail) {
                $message->to($recipient)
                    ->cc($cc_mail)
                    ->subject($subject_mail);
            });

        } catch (Exception $e) {

            Log::error('Failed to send mail expiration partner agreement to Partnership Team caused by : ' . $e->getMessage() . ' | Line ' . $e->getLine());
        }
    }

   
}