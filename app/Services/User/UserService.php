<?php

namespace App\Services\User;

use App\Enum\LogModule;
use App\Interfaces\AcadTutorRepositoryInterface;
use App\Interfaces\FollowupRepositoryInterface;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UserService
{
    private FollowupRepositoryInterface $followupRepository;

    private AcadTutorRepositoryInterface $acadTutorRepository;

    private LogService $logService;

    public function __construct(FollowupRepositoryInterface $followupRepository, AcadTutorRepositoryInterface $acadTutorRepository, LogService $logService)
    {
        $this->followupRepository = $followupRepository;
        $this->acadTutorRepository = $acadTutorRepository;
        $this->logService = $logService;
    }

    // Purpose:
    // Send mail reminder expiration contract to HR Team
    public function snSendMailExpirationContract(array $list_contracts_expired_soon, string $title_for_mail_data): void
    {
        $subject_mail = 'Contract Expiration Notification';
        $mail_resources = 'mail-template.expiration-contract-reminder';

        try {
            Mail::send($mail_resources, ['list_contracts' => $list_contracts_expired_soon, 'title' => $title_for_mail_data], function ($message) use ($subject_mail) {
                $message->to(config('env.HR_MAIL'))
                    ->cc(config('env.HR_CC'))
                    ->subject($subject_mail);
            });
        } catch (Exception $e) {
            $this->logService->createErrorLog(
                LogModule::SEND_EXPIRATION_CONTRACT,
                $e->getMessage(),
                $e->getLine(),
                $e->getFile(),
                ['list_contracts' => $list_contracts_expired_soon, 'title' => $title_for_mail_data]
            );
        }
    }

    // Purpose:
    // Set data mail (name, email, and schedules)
    // Send mail reminder follow up to pic client
    public function snSendMailReminderFollowup(Collection $list_followup_schedule): void
    {
        $subject_mail = 'Client Follow-up Reminder';
        $mail_resources = 'mail-template.followup-client-reminder';
        $data_mails = [];

        DB::beginTransaction();

        foreach ($list_followup_schedule as $followup_schedule) {

            if (! $followup_schedule->pic()->exists()) {
                continue; // skip if pic is not set
            }

            $pic_email = $followup_schedule->pic->email;
            $pic_name = $followup_schedule->pic->full_name;

            $data_mails[$pic_name]['email'] = $pic_email;
            $data_mails[$pic_name]['name'] = $pic_name;
            $data_mails[$pic_name]['schedules'][] = [
                'client' => $followup_schedule->client,
                'followup' => $followup_schedule,
            ];
        }

        foreach ($data_mails as $key => $data_mail) {

            try {
                Mail::send($mail_resources, $data_mail, function ($message) use ($data_mail, $subject_mail) {
                    $message->to($data_mail['email'], $data_mail['name'])
                        ->subject($subject_mail);
                });

                foreach ($data_mail['schedules'] as $info) {
                    $followup_id = $info['followup']->id;
                    // update status reminder to 1
                    // if mail successfully sent
                    $this->followupRepository->update($followup_id, ['reminder_is_sent' => 1]);
                }
                DB::commit();

            } catch (Exception $e) {

                DB::rollBack();
                $this->logService->createErrorLog(
                    LogModule::SEND_REMINDER_FOLLOWUP,
                    $e->getMessage(),
                    $e->getLine(),
                    $e->getFile(),
                    ['followup_reminder' => $data_mails]
                );
            }
        }
    }

    // Purpose:
    // Set data mail (name, email, and tutoring details)
    // Send mail reminder tutor
    public function snSendReminderTutor(Collection $acad_tutors, string $time): void
    {
        $subject_mail = 'Reminder for Academic Tutoring';
        $mail_resources = 'pages.reminder.acad_tutor.index-'.strtolower($time);

        foreach ($acad_tutors as $acad_tutor) {

            $tutor_date = date('d M Y', strtotime($acad_tutor->date));
            $tutor_date_for_calendar = date('Ymd', strtotime($acad_tutor->date));
            $tutor_time = date('H:i', strtotime($acad_tutor->time));
            $tutor_link = $acad_tutor->link;

            $master_pic = $acad_tutor->clientProgram->internalPic;
            $pic_name = $master_pic->first_name.' '.$master_pic->last_name;
            $pic_email[] = $master_pic->email; // must be an array

            // $master_tutor_allData = $acad_tutor->clientProgram->clientMentor;
            $master_tutor = $tutor_email = $acad_tutor->clientProgram->clientMentor()->pluck('email')->toArray();

            $master_client = $acad_tutor->clientProgram->client;
            $client_name = $master_client->first_name.' '.$master_client->last_name;
            if (! $client_email = $master_client->mail) {
                continue;
            }

            $cc = array_merge($pic_email, $master_tutor);
            $program = 'Academic Tutoring';

            $params = [
                'recipient' => [
                    'name' => $client_name,
                    'email' => $client_email,
                ],
                'cc' => $cc, // pic dari acad tutor
                'tutoring_detail' => [
                    'date' => $tutor_date,
                    'time' => $tutor_time,
                    'link' => $tutor_link,
                ],
            ];

            if ($time == 'H1') {
                $params['calendar'] = 'https://calendar.google.com/calendar/u/0/r/eventedit?dates='.$tutor_date_for_calendar.'/'.$tutor_date_for_calendar.'&text=Academic+Tutoring&details='.str_replace(' ', '+', $program.' on '.$tutor_date).'&location='.urlencode($tutor_link);
            }

            Mail::send($mail_resources, $params, function ($message) use ($params, $subject_mail) {
                $message->to($params['recipient']['email'], $params['recipient']['name'])
                    ->cc($params['cc'])
                    ->subject($subject_mail);
            });

            $sent_status = 1;

            if (Mail::flushMacros()) {
                $sent_status = 0;
            }

            $sent_detail = [
                'foreign_identifier' => $acad_tutor->id,
                'content' => 'Academic Tutoring '.$time,
                'sent_status' => $sent_status,
            ];

            // mark acad tutor id as sent
            $this->acadTutorRepository->markAsSent($sent_detail);
        }
    }
}
