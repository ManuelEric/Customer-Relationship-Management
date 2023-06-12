<?php

namespace App\Console\Commands;

use App\Interfaces\AcadTutorRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\SentMessage;

class SendReminderTutorH1 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:reminder_tutor_h1';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder H-1 days. Running per day';

    private AcadTutorRepositoryInterface $acadTutorRepository;

    public function __construct(AcadTutorRepositoryInterface $acadTutorRepository)
    {
        parent::__construct();

        $this->acadTutorRepository = $acadTutorRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        
        $acadTutors = $this->acadTutorRepository->getAllScheduleAcadTutorH1Day();
        $progressBar = $this->output->createProgressBar($acadTutors->count());
        $progressBar->start();
        foreach ($acadTutors as $data) {

            $tutor_date = date('d M Y', strtotime($data->date));
            $tutor_time = date('H:i', strtotime($data->time));
            $tutor_link = $data->link;

            $master_pic = $data->clientProgram->internalPic;
            $pic_name = $master_pic->first_name.' '.$master_pic->last_name;
            $pic_email[] = $master_pic->email; // must be an array 

            // $master_tutor_allData = $data->clientProgram->clientMentor;
            $master_tutor = $tutor_email = $data->clientProgram->clientMentor()->pluck('email')->toArray();

            $master_client = $data->clientProgram->client;
            $client_name = $master_client->first_name.' '.$master_client->last_name;
            if (!$client_email = $master_client->mail)
                continue;

            $cc = array_merge($pic_email, $master_tutor);

            $params = [
                'recipient' => [
                    'name' => $client_name,
                    'email' => $client_email
                ],
                'cc' => $cc, // pic dari acad tutor
                'tutoring_detail' => [
                    'date' => $tutor_date,
                    'time' => $tutor_time,
                    'link' => $tutor_link
                ] 
            ];

            $subject = 'Reminder for Academic Tutoring';

            Mail::send('pages.reminder.acad_tutor.index-h1', $params, function ($message) use ($params, $subject) {
                $message->to($params['recipient']['email'], $params['recipient']['name'])
                    ->cc($params['cc'])
                    ->subject($subject);
            });
            
            $sent_status = 1;

            if (Mail::flushMacros()) {
                $sent_status = 0;
            }


            $sentDetail = [
                'foreign_identifier' => $data->id,
                'content' => 'Academic Tutoring H1',
                'sent_status' => $sent_status
            ];

            // mark acad tutor id as sent
            $this->acadTutorRepository->markAsSent($sentDetail);

            $progressBar->advance();
        }
        $progressBar->finish();

        return Command::SUCCESS;
    }
}
