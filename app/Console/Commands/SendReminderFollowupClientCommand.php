<?php

namespace App\Console\Commands;

use App\Interfaces\FollowupRepositoryInterface;
use App\Services\User\UserService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReminderFollowupClientCommand extends Command
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
    private UserService $userService;

    public function __construct(FollowupRepositoryInterface $followupRepository, UserService $userService)
    {
        parent::__construct();
        $this->followupRepository = $followupRepository;
        $this->userService = $userService;
    }

    # Purpose:
    # Get data followup client schedule by date
    # Send mail reminder followup to pic
    public function handle()
    {
        Log::info('Cron reminder followup client working properly');
        
        $requested_date = date('Y-m-d');
        $list_followup_schedule = $this->followupRepository->getAllFollowupClientScheduleByDate($requested_date);
        $progress_bar = $this->output->createProgressBar($list_followup_schedule->count());
        
        $params = [];
        
        if ($list_followup_schedule->count() == 0) {
            $this->info('No followup schedules were found.');
            return Command::SUCCESS;
        }
        
        $progress_bar->start();
        $this->userService->snSendMailReminderFollowup($list_followup_schedule);

        $progress_bar->finish();


        return Command::SUCCESS;
    }
}
