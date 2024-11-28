<?php

namespace App\Console\Commands;

use App\Interfaces\ClientProgramLogMailRepositoryInterface;
use App\Services\Program\ClientProgramService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResendThanksMailForRegistrantThroughFormProgramEmbedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'automate:resend_thanks_mail_program';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resend a thanks mail if failed for newly registrant that registered through form program embed. ';

    private ClientProgramLogMailRepositoryInterface $clientProgramLogMailRepository;
    private ClientProgramService $clientProgramService;

    public function __construct(ClientProgramLogMailRepositoryInterface $clientProgramLogMailRepository, ClientProgramService $clientProgramService)
    {
        parent::__construct();
        $this->clientProgramLogMailRepository = $clientProgramLogMailRepository;
        $this->clientProgramService = $clientProgramService;
    }

    # Purpose:
    # Get tbl_client_prog_log_mail when sent_status 0 is mean failed sent or not sent
    # Resend mail if success update sent_status from tbl_client_prog_log_mail to 1 ELSE update sent_status from tbl_client_prog_log_mail to 0
    public function handle()
    {
        Log::info('Cron for resend thanks mail form program works fine');
        
        $logs = $this->clientProgramLogMailRepository->getClientProgramLogMail();
        $progress_bar = $this->output->createProgressBar($logs->count());
        $progress_bar->start();

        foreach ($logs as $log) {

            $client_program = $log->clientProgram;
            $student = $client_program->client;
            $parent = $student->parents[0]; # get the first parent if there are more than one parent attached

            $this->clientProgramService->snSendMailThanks($client_program, $parent->id, $student->id, true);

            $progress_bar->advance();
        }
            
        $progress_bar->finish();

        return Command::SUCCESS;
    }
}
