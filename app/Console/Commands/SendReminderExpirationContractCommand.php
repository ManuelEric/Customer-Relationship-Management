<?php

namespace App\Console\Commands;

use App\Actions\Contracts\FindExpiringContractByTypeAction;
use App\Events\Contracts\SendingReminderExpiringContractEvent;
use App\Services\User\UserService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

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
    
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        parent::__construct();
        $this->userService = $userService;
    }

    # Purpose:
    # get data contracts user that almost meet the end of the contract
    # send mail to HR Team
    public function handle(
        FindExpiringContractByTypeAction $findExpiringContractByTypeAction
    )
    {
        Log::debug('[CRON] send expiration contract working properly.');

        # type is editor, external_mentor, internship, probation, tutor
        $type = $this->argument('type');
        $list_contracts_expired_soon = [];
        $title_for_mail_data = null;
        [$contracts, $title_for_mail_data] = $findExpiringContractByTypeAction->execute($type);
        $progress_bar = $this->output->createProgressBar($contracts->count());
        $progress_bar->start();

        foreach ($contracts as $contract) 
        {
            if (!$contract->user_type[0])
                throw new Exception("The user [{$contract->full_name}] does not have active contract in the moment.");

            $list_contracts_expired_soon[] = [
                'full_name' => $contract->full_name,
                # there is should be one data of the active employment type so we use index[0]
                'employment_type' => $contract->user_type[0]->type_name,
                'contract_start_date' => $contract->user_type[0]->pivot->start_date,
                'contract_end_date' => $contract->user_type[0]->pivot->end_date,
            ];

            //! might continue to implement event and listener to sending an email
            //! SendingReminderExpiringContractEvent::dispatch($list_contracts_expired_soon, $title_for_mail_data);


            $this->userService->snSendMailExpirationContract($list_contracts_expired_soon, $title_for_mail_data);

            $progress_bar->advance();
        }

        return Command::SUCCESS;
    }
}
