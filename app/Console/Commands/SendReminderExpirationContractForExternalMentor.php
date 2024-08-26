<?php

namespace App\Console\Commands;

use App\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReminderExpirationContractForExternalMentor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:reminder_expiration_contracts_external_mentor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending a reminder to HR Team if there are employees (external mentor) contract that must be renewed or almost finished.';

    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('Cron send reminder expiration contract external mentor working properly');

        # the contracts that expired soon.
        $contracts = $this->userRepository->getAllUsersExternalMentorContracts();
        $list_contracts_expired_soon = [];

        $progressBar = $this->output->createProgressBar($contracts->count());
        $progressBar->start();

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

            $progressBar->advance();
        }

        $subject = 'Contract Expiration Notification';
            
        $mail_resources = 'mail-template.expiration-contract-reminder';

        try {
            Mail::send($mail_resources, ['list_contracts' => $list_contracts_expired_soon, 'title' => 'External Mentor'], function ($message) use ($subject) {
                $message->to(env('HR_MAIL'))
                    ->cc(env('HR_CC'))
                    ->subject($subject);
            });
            $progressBar->finish();

        } catch (Exception $e) {

            Log::error('Failed to send expiration contract (external mentor) to HR Team caused by : ' . $e->getMessage() . ' | Line ' . $e->getLine());
            return $this->error($e->getMessage() . ' | Line ' . $e->getLine());
        }
        return Command::SUCCESS;
    }
}
