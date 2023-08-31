<?php

namespace App\Console\Commands;

use App\Interfaces\FollowupRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReminderFollowup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:reminder_followup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder to Sales Team regarding the followup schedule';

    private FollowupRepositoryInterface $followupRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(FollowupRepositoryInterface $followupRepository, UserRepositoryInterface $userRepository)
    {
        parent::__construct();
        $this->followupRepository = $followupRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('Cron reminder followup working properly');
        
        $requested_date = date('Y-m-d');
        $list_followup_schedule = $this->followupRepository->getAllFollowupScheduleByDate($requested_date);
        $progressBar = $this->output->createProgressBar($list_followup_schedule->count());
        $progressBar->start();
        $params = [];

        if ($list_followup_schedule->count() == 0) {
            Log::info('No followup schedules were found.');
            return Command::SUCCESS;
        }

        DB::beginTransaction();
        foreach ($list_followup_schedule as $data) {
            
            $pic_email = $data->clientProgram->internalPic->email;
            $pic_name = $data->clientProgram->internalPic->full_name;

            $params[$pic_name]['email'] = $pic_email;
            $params[$pic_name]['name'] = $pic_name;
            $params[$pic_name]['schedules'][] = [
                'client' => $data->clientProgram->client,
                'program' => $data->clientProgram,
                'followup' => $data
            ];

            $progressBar->advance();
        }

        $subject = 'Client Follow-up Reminder';
            
        $mail_resources = 'mail-template.followup-reminder';

        foreach ($params as $key => $value) {

            try {
                Mail::send($mail_resources, $value, function ($message) use ($value, $subject) {
                    $message->to($value['email'], $value['name'])
                        ->subject($subject);
                });

                foreach ($value['schedules'] as $info) {

                    $followup_id = $info['followup']->id;
                    $this->info($followup_id);
        
                    # update status reminder to 1 
                    # if mail successfully sent
                    $this->followupRepository->updateFollowup($followup_id, ['reminder' => 1]);
                }

                DB::commit();
    
            } catch (Exception $e) {
    
                DB::rollBack();
                Log::error('Failed to send followup reminder to ' . $value['name'] . ' caused by : ' . $e->getMessage() . ' | Line ' . $e->getLine());
                return $this->error($e->getMessage() . ' | Line ' . $e->getLine());
            }
        }

        $progressBar->finish();

        return Command::SUCCESS;
    }
}
