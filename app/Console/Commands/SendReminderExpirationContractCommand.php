<?php

namespace App\Console\Commands;

use App\Interfaces\UserRepositoryInterface;
use App\Services\User\UserService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReminderExpirationContractCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:reminder_expiration_contracts {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending a reminder to HR Team if there are employees (editor, external mentor, intern, probation, tutor) contract that must be renewed or almost finished.';
    
    private UserRepositoryInterface $userRepository;
    private UserService $userService;

    public function __construct(UserRepositoryInterface $userRepository, UserService $userService)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->userService = $userService;
    }

    # Purpose:
    # get data contracts user almost expired
    # send mail notification expiration contract to HR Team
    public function handle()
    {
        # type is editor, external_mentor, internship, probation, tutor
        $type = $this->argument('type');
        $title_for_mail_data = null;

        Log::info('Cron send reminder expiration contract working properly');

        # the contracts that expired soon.
        switch ($type) {
            case 'editor':
                $contracts = $this->userRepository->getAllUsersEditorContracts();
                $title_for_mail_data = 'Editor';
                break;

            case 'external_mentor':
                $contracts = $this->userRepository->getAllUsersExternalMentorContracts();
                $title_for_mail_data = 'External Mentor';
                break;

            case 'internship':
                $contracts = $this->userRepository->getAllUsersInternshipContracts();
                $title_for_mail_data = 'Internship';
                break;

            case 'probation':
                $contracts = $this->userRepository->getAllUsersProbationContracts();
                $title_for_mail_data = 'Probation';
                break;

            case 'tutor':
                $contracts = $this->userRepository->getAllUsersTutorContracts();
                $title_for_mail_data = 'Tutor';
                break;
            
            default:
                throw new Exception('Type is invalid!');
                break;
        }

        $list_contracts_expired_soon = [];

        $progress_bar = $this->output->createProgressBar($contracts->count());
        $progress_bar->start();

        foreach ($contracts as $contract) {

            if (!$contract->user_type[0])
                throw new Exception('Cannot found active contract.');

            $list_contracts_expired_soon[] = [
                'full_name' => $contract->full_name,
                # there are should be one data of the active employment type so we use index[0]
                'employment_type' => $contract->user_type[0]->type_name,
                'contract_start_date' => $contract->user_type[0]->pivot->start_date,
                'contract_end_date' => $contract->user_type[0]->pivot->end_date,
            ];

            $this->userService->snSendMailExpirationContract($list_contracts_expired_soon, $title_for_mail_data);

            $progress_bar->advance();
        }

        return Command::SUCCESS;
    }
}
