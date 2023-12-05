<?php

namespace App\Console\Commands;

use App\Interfaces\ClientRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerifiedStudent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verified:student';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checking verified student';


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
        $students = $this->clientRepository->getAllClientByRole('Student');
        $progressBar = $this->output->createProgressBar($students->count());
        $progressBar->start();

        DB::beginTransaction();
        try {

            foreach ($students as $student) {
                $progressBar->advance();

                ## Update to verified

                # Case 1: have joined the program with success status
                $isVerified = false;
                if($student->clientProgs->count() > 0){
                    foreach ($student->clientProgs as $clientProg) {
                        if($clientProg->status == 1 || $clientProg->status == 0){
                            $isVerified = true;
                        }
                    }
                }else{

                    # Case 2: Email and phone is complete && school verified
                    if($student->mail != null && $student->phone != null && isset($student->school) && !preg_match('/[^\x{80}-\x{F7} a-z0-9@_.\'-]/iu', $student->full_name)){
                        if($student->school->is_verified == 'Y'){
                            $isVerified = true;
                        }
                    }
                }
                
                $isVerified == true ?  $this->clientRepository->updateClient($student->id, ['is_verified' => 'Y']) : null;

                $progressBar->advance();
            }
            DB::commit();
            $progressBar->finish();
        } catch (Exception $e) {

            DB::rollBack();
            Log::info('Failed to check verified student : ' . $e->getMessage() . ' on line ' . $e->getLine());
        }

        return Command::SUCCESS;
    }
}