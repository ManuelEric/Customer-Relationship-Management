<?php

namespace App\Console\Commands;

use App\Interfaces\ClientProgramLogMailRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResendThanksMailForRegistrantThroughFormProgramEmbed extends Command
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

    public function __construct(ClientProgramLogMailRepositoryInterface $clientProgramLogMailRepository)
    {
        parent::__construct();
        $this->clientProgramLogMailRepository = $clientProgramLogMailRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('Cron for resend thanks mail form program works fine');
        
        $logs = $this->clientProgramLogMailRepository->getClientProgramLogMail();
        $progressBar = $this->output->createProgressBar($logs->count());
        $progressBar->start();

        DB::beginTransaction();
        foreach ($logs as $log) {

            $clientProgram = $log->clientProgram;
            $student = $clientProgram->client;
            $parent = $student->parents[0]; # get the first parent if there are more than one parent attached

            try {

                app('App\Http\Controllers\ClientProgramController')->sendMailThanks($clientProgram, $parent->id, $student->id, true);
                $sent_mail = 1;

            } catch (Exception $e) {

                Log::error('Failed to send thanks mail program embed for : '.$parent->mail.' that registered : '.$clientProgram->program->program_name.' program | Error '.$e->getMessage().' Line '.$e->getLine());
                $sent_mail = 0;

            }

            $logDetails = [
                'sent_status' => $sent_mail
            ];
            
            try {

                $this->clientProgramLogMailRepository->updateClientProgramLogMail($log->id, $logDetails);
                DB::commit();

            } catch (Exception $e) {

                DB::rollBack();
                Log::error('Failed to update client program log mail for Id : '.$log->id. ' | Error '. $e->getMessage().' Line '.$e->getLine());
            }


            $progressBar->advance();
        }
            
        $progressBar->finish();

        return Command::SUCCESS;
    }
}
