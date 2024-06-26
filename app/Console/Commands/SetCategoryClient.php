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
        // $mentees = $this->clientRepository->getExistingMentees(false, null, []);
        $progressBar = $this->output->createProgressBar($students->count());
        $progressBar->start();

        DB::beginTransaction();
        try {

            $categories = New Collection;
            foreach ($students->where('st_statusact', 1)->where('is_verified', 'Y') as $student) {
                

                # check if client have clientprogram
                if($student->clientProgram->count() > 0){
                    foreach ($student->clientProgram as $clientProg) {
                     
                        if($clientProg->status == 0){
                            $categories->push(['category' => 'potential', 'id' => $student->id]);

                        }else if($clientProg->status == 2 || $clientProg->status == 3){
                            $categories->push(['category' => 'new_lead', 'id' => $student->id]);
                        }
                        
                        if($clientProg->program->main_prog_id == 1){
                            if($clientProg->status == 1 && $clientProg->prog_running_status != 2){
                                if($clientProg->prog_end_date != null && date('Y-m-d') > $clientProg->prog_end_date){
                                    $categories->push(['category' => 'alumni_mentee', 'id' => $student->id]);
                                }else{
                                    $categories->push(['category' => 'mentee', 'id' => $student->id]);
                                }
                            }else if($clientProg->status == 1 && $clientProg->prog_running_status == 2){
                                $categories->push(['category' => 'alumni_mentee', 'id' => $student->id]);
                            }else if($clientProg->status == 1){
                                $categories->push(['category' => 'mentee', 'id' => $student->id]);
                            }
                        }else if($clientProg->program->main_prog_id != 1){
                            if($clientProg->status == 1 && $clientProg->prog_running_status != 2){
                                if($clientProg->prog_end_date != null && date('Y-m-d') > $clientProg->prog_end_date){
                                    $categories->push(['category' => 'alumni_non_mentee', 'id' => $student->id]);
                                }else{
                                    $categories->push(['category' => 'non_mentee', 'id' => $student->id]);
                                }
                            }else if($clientProg->status == 1 && $clientProg->prog_running_status == 2){
                                $categories->push(['category' => 'alumni_non_mentee', 'id' => $student->id]);
                            }else if($clientProg->status == 1){
                                $categories->push(['category' => 'non_mentee', 'id' => $student->id]);
                            }
                        }
                    }
                }else{
                    $categories->push(['category' => 'new_lead', 'id' => $student->id]);
                }
                
                $nonMentee = $categories->where('id', $student->id)->where('category', 'non_mentee')->count();
                $mentee = $categories->where('id', $student->id)->where('category', 'mentee')->count();
                $potential = $categories->where('id', $student->id)->where('category', 'potential')->count();
                $newLead = $categories->where('id', $student->id)->where('category', 'new_lead')->count();
                $alumniMentee = $categories->where('id', $student->id)->where('category', 'alumni_mentee')->count();
                $alumniNonMentee = $categories->where('id', $student->id)->where('category', 'alumni_non_mentee')->count();
 
                
                if($mentee > 0){
                    $category = 'mentee';
                }else if($mentee == 0 && $nonMentee > 0){
                    $category = 'non-mentee';
                }else if($mentee == 0 && $nonMentee == 0 && $alumniMentee > 0){
                    $category = 'alumni-mentee';
                }else if($mentee == 0 && $nonMentee == 0 && $alumniMentee == 0 && $alumniNonMentee > 0){
                    $category = 'alumni-non-mentee';
                }else if($mentee == 0 && $nonMentee == 0 && $alumniMentee == 0 && $alumniNonMentee == 0 && $potential > 0){
                    $category = 'potential';
                }else if($mentee == 0 && $nonMentee == 0 && $alumniMentee == 0 && $alumniNonMentee == 0 && $potential == 0 && $newLead > 0){
                    $category = 'new-lead';
                }
                $progressBar->advance();


                $this->clientRepository->updateClient($student->id, ['category' => $category]);
            
            }

            // $a =  $mentees->whereNotIn('id', $categories->where('category', 'mentee')->pluck('id'))->pluck('id', 'first_name');
                
            // Log::debug($a);

            

            DB::commit();
            $progressBar->finish();
        } catch (Exception $e) {

            DB::rollBack();
            Log::info('Failed to set category client : ' . $e->getMessage() . ' on line ' . $e->getLine());
        }

        return Command::SUCCESS;
    }
}
