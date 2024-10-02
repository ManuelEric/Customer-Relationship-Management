<?php

namespace App\Console\Commands;

use App\Interfaces\AcadTutorRepositoryInterface;
use App\Services\User\UserService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\SentMessage;

class SendReminderTutorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:reminder_tutor {time}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder tutor';

    private AcadTutorRepositoryInterface $acadTutorRepository;
    private UserService $userService;

    public function __construct(AcadTutorRepositoryInterface $acadTutorRepository, UserService $userService)
    {
        parent::__construct();

        $this->acadTutorRepository = $acadTutorRepository;
        $this->userService = $userService;
    }

    # Purpose:
    # Get schedule tutor H-1 or -3 hours
    # Reminder email tutor 
    public function handle()
    {
        $time = $this->argument('time');
        
        switch ($time) {
            case 'H1':
                $acad_tutors = $this->acadTutorRepository->getAllScheduleAcadTutorH1Day();
                break;

            case 'T3':
                $acad_tutors = $this->acadTutorRepository->getAllScheduleAcadTutorT3Hours();
                break;
            
            default:
                throw new Exception('Time is invalid!');
                break;
        }

        $progress_bar = $this->output->createProgressBar($acad_tutors->count());
        $progress_bar->start();
        $this->userService->snSendReminderTutor($acad_tutors, $time);
        $progress_bar->finish();

        return Command::SUCCESS;
    }
}
