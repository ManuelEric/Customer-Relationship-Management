<?php

namespace App\Console\Commands;

use App\Interfaces\ClientRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerifiedParent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verified:parent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checking verified parent';


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
        $parents = $this->clientRepository->getAllClientByRole('Parent');
        $progressBar = $this->output->createProgressBar($parents->count());
        $progressBar->start();

        DB::beginTransaction();
        try {

            foreach ($parents as $parent) {
                $progressBar->advance();

                ## Update to verified

                # Case 1: have joined the program with success status
                $isVerified = false;
                if ($parent->childrens->count() > 0) {
                    foreach ($parent->childrens as $child) {
                        if ($child->clientProgs->count() > 0) {
                            foreach ($child->clientProgs as $clientProg) {
                                if ($clientProg->status == 1) {
                                    $isVerified = true;
                                }
                            }
                        }
                    }
                }else{
                    # Case 2: Email and phone is complete
                    if ($parent->mail != null && $parent->phone != null && !preg_match('/[^\x{80}-\x{F7} a-z0-9@_.\'-]/iu', $parent->full_name)) {
                        $isVerified = true;
                    }
                }
                $isVerified == true ?  $this->clientRepository->updateClient($parent->id, ['is_verified' => 'Y']) : null;


                

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