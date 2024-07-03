<?php

namespace App\Console\Commands;

use App\Interfaces\ClientRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SetCategoryClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:category_client';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically set category client active and verified.';


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

            $categories = New Collection;
            foreach ($students->where('st_statusact', 1)->where('is_verified', 'Y') as $student) {
                $isMentee = false;
                

                # check if client have clientprogram
                if ($student->clientProgram->count() > 0) {
                    foreach ($student->clientProgram as $clientProg) {

                        # status = 0 pending, 1 success, 2 failed, 3 refund
                        

                        if ($clientProg->status == 0) {
                            $categories->push(['category' => 'potential', 'id' => $student->id]);
                        } else if ($clientProg->status == 2 || $clientProg->status == 3) { # jika programnya cuma 1
                            $categories->push(['category' => 'new_lead', 'id' => $student->id]);
                        } else if ($clientProg->status == 1) {
                            if($clientProg->program->main_prog_id == 1){
                                $isMentee = true;
                            }
                            if (($clientProg->prog_end_date != null && date('Y-m-d') > $clientProg->prog_end_date) || $clientProg->prog_running_status == 2) {
                                $categories->push(['category' => 'alumni', 'id' => $student->id]);
                            } else {
                                $categories->push(['category' => 'active', 'id' => $student->id]);
                            }
                        }
                    }
                } else {
                    $categories->push(['category' => 'new_lead', 'id' => $student->id]);
                }
                
                $active = $categories->where('id', $student->id)->where('category', 'active')->count();
                $alumni = $categories->where('id', $student->id)->where('category', 'alumni')->count();
                $potential = $categories->where('id', $student->id)->where('category', 'potential')->count();

                if ($active > 0) {
                    if($isMentee){
                        $category = 'mentee';
                    }else{
                        $category = 'non-mentee';
                    }
                } else if ($potential > 0) {
                    $category = 'potential';
                    // if($alumni > 0 && $isMentee){
                    //     $category = 'mentee';
                    // }else if($alumni > 0 && !$isMentee){
                    //     $category = 'non-mentee';
                    // }
                } else if ($alumni > 0) {
                    if($isMentee){
                        $category = 'alumni-mentee';
                    }else{
                        $category = 'alumni-non-mentee';
                    }
                }else{
                    $category = 'new-lead';
                }
                $progressBar->advance();


                $this->clientRepository->updateClient($student->id, ['category' => $category]);
            
            }
            

            DB::commit();
            $progressBar->finish();
        } catch (Exception $e) {

            DB::rollBack();
            Log::info('Failed to set category client : ' . $e->getMessage() . ' on line ' . $e->getLine());
        }

        return Command::SUCCESS;
    }
}
