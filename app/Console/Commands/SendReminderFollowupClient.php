<?php

namespace App\Console\Commands;

use App\Interfaces\FollowupRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReminderFollowupClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:reminder_followup_client';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder to Sales Team regarding the followup schedule per client';

    private FollowupRepositoryInterface $followupRepository;

    public function __construct(FollowupRepositoryInterface $followupRepository)
    {
        parent::__construct();
        $this->followupRepository = $followupRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('Cron reminder followup client working properly');
        
        $requested_date = date('Y-m-d');
        $list_followup_schedule = $this->followupRepository->getAllFollowupClientScheduleByDate($requested_date);
        $progressBar = $this->output->createProgressBar($list_followup_schedule->count());
        
        $params = [];
        
        if ($list_followup_schedule->count() == 0) {
            $this->info('No followup schedules were found.');
            return Command::SUCCESS;
        }
        
        $progressBar->start();
        DB::beginTransaction();
        
        foreach ($list_followup_schedule as $data) {
            
            $pic_email = $data->pic->email;
            $pic_name = $data->pic->full_name;

            $params[$pic_name]['email'] = $pic_email;
            $params[$pic_name]['name'] = $pic_name;
            $params[$pic_name]['schedules'][] = [
                'client' => $data->client,
                'followup' => $data
            ];

            $progressBar->advance();
        }

        $subject = 'Client Follow-up Reminder';
            
        $mail_resources = 'mail-template.followup-client-reminder';

        foreach ($params as $key => $value) {

            try {
                Mail::send($mail_resources, $value, function ($message) use ($value, $subject) {
                    $message->to($value['email'], $value['name'])
                        ->subject($subject);
                });

                foreach ($value['schedules'] as $info) {

                    $followup_id = $info['followup']->id;

                    # update status reminder to 1 
                    # if mail successfully sent
                    $this->followupRepository->update($followup_id, ['reminder_is_sent' => 1]);
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
