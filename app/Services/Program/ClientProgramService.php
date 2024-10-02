<?php

namespace App\Services\Program;

use App\Interfaces\ClientProgramLogMailRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ClientProgramService 
{
    protected ClientRepositoryInterface $clientRepository;
    protected ClientProgramLogMailRepositoryInterface $clientProgramLogMailRepository;

    public function __construct(ClientProgramLogMailRepositoryInterface $clientProgramLogMailRepository, ClientRepositoryInterface $clientRepository)
    {
        $this->clientProgramLogMailRepository = $clientProgramLogMailRepository;
        $this->clientRepository = $clientRepository;
    }

    # Purpose:
    # set mail data (recipient: name, email and children details)
    # send thanks mail registration
    # insert log mail
    public function snSendMailThanks(Collection $clientProgram, int $parentId, int $childId, bool $update = false)
    {
        $subject_mail = 'Your registration is confirmed';
        $mail_resources = 'mail-template.thanks-email-program';

        $parent = $this->clientRepository->getClientById($parentId);
        $children = $this->clientRepository->getClientById($childId);
        
        $recipient_details = [
            'name' => $parent->mail != null ? $parent->full_name : $children->full_name,  
            'mail' => $parent->mail != null ? $parent->mail : $children->mail,
            'children_details' => [
                'name' => $children->full_name
            ]
        ];

        $program = [
            'name' => $clientProgram->program->program_name
        ];

        try {
            Mail::send($mail_resources, ['client' => $recipient_details, 'program' => $program], function ($message) use ($subject_mail, $recipient_details) {
                $message->to($recipient_details['mail'], $recipient_details['name'])
                    ->subject($subject_mail);
            });
            $sent_mail = 1;
            
        } catch (Exception $e) {
            
            $sent_mail = 0;
            Log::error('Failed send email thanks to client that register using form program | error : '.$e->getMessage().' | Line '.$e->getLine());

        }

        # if update is true 
        # meaning that this function being called from scheduler
        # that updating the client event log mail, so the system no longer have to create the client event log mail
        if ($update === true) {
            return true;    
        }

        $log_details = [
            'clientprog_id' => $clientProgram->clientprog_id,
            'sent_status' => $sent_mail
        ];

        return $this->clientProgramLogMailRepository->createClientProgramLogMail($log_details);
    }
}