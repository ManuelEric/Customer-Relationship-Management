<?php

namespace App\Console\Commands;

use App\Interfaces\ClientRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerifiedTeacher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verified:teacher';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checking verified teacher';


    private ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        parent::__construct();
        $this->clientRepository = $clientRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $teachers = $this->clientRepository->getAllClientByRole('Teacher/Counselor');
        $progressBar = $this->output->createProgressBar($teachers->count());
        $progressBar->start();

        DB::beginTransaction();
        try {

            foreach ($teachers as $teacher) {
                $progressBar->advance();

                ## Update to verified

                # Case 1: Email and phone is complete && school verified
                 if($teacher->mail != null && $teacher->phone != null && isset($teacher->school) && !preg_match('/[^\x{80}-\x{F7} a-z0-9@_.\'-]/iu', $teacher->full_name)){
                    if($teacher->school->is_verified == 'Y'){
                        $this->clientRepository->updateClient($teacher->id, ['is_verified' => 'Y']);
                    }
                }

                $progressBar->advance();
            }
            DB::commit();
            $progressBar->finish();
        } catch (Exception $e) {

            DB::rollBack();
            Log::info('Failed to check verified teacher : ' . $e->getMessage() . ' on line ' . $e->getLine());
        }

        return Command::SUCCESS;
    }
}