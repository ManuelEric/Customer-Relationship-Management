<?php

namespace App\Jobs\Client;

use App\Interfaces\ClientRepositoryInterface;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessDefineCategory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ClientRepositoryInterface $clientRepository;
    protected $clientIds;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($clients)
    {
        $this->clientIds = $clients;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ClientRepositoryInterface $clientRepository)
    {
        $clients = $clientRepository->getClientsById($this->clientIds, ['clientProgram']);
        
        // DB::beginTransaction();
        try {

            # declare default variables
            $updatedClients = [];

            foreach ($clients as $student) {

                # New leads
                /*
                    - Doesnt have clientprogram
                    - Or have clientprogram but status failed (2) or refund (3)
                */

                # Potential
                /*
                    - Have clienprogram and status pending (0)
                */

                # Mentee
                /*
                    - Have clientprogram & (join admission with status success (1) where prog running status != done (2))
                    - Or have clientprogram & (join admission with status success (1) where prog running status == done (2))
                    - Or have clientprogram & (join admission with status pending (1))
                */

                # Non mentee
                /*
                    - Have clientprogram & (Not join admission with status success (1) where prog running status != done (2))
                    - Or have clientprogram & (Not join admission with status success (1) where prog running status == done (2))
                    - Or have clientprogram & (Not join admission with status pending (1))
                */


                $categories = New Collection;

                # check if client have clientprogram
                if($student->clientProgram->count() > 0){
                    foreach ($student->clientProgram as $clientProg) {
                     
                        if($clientProg->status == 0){
                            $categories->push(['category' => 'potential', 'id' => $student->id]);

                        }else if($clientProg->status == 2 || $clientProg->status == 3){
                            $categories->push(['category' => 'new_lead', 'id' => $student->id]);
                        }
                        
                        if($clientProg->program->main_prog_id == 1){
                            if($clientProg->status == 1 && ($clientProg->prog_running_status != 2 || $clientProg->prog_running_status == 2)){
                                $categories->push(['category' => 'mentee', 'id' => $student->id]);
                            }else if($clientProg->status == 1){
                                $categories->push(['category' => 'mentee', 'id' => $student->id]);
                            }
                        }else if($clientProg->program->main_prog_id != 1){
                            if($clientProg->status == 1 && ($clientProg->prog_running_status != 2 || $clientProg->prog_running_status == 2)){
                                $categories->push(['category' => 'non_mentee', 'id' => $student->id]);
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
               
                
                if($mentee > 0){
                    $category = 'mentee';
                }else if($mentee == 0 && $nonMentee > 0){
                    $category = 'non_mentee';
                }else if($mentee == 0 && $nonMentee == 0 && $potential > 0){
                    $category = 'potential';
                }else if($mentee == 0 && $nonMentee == 0 && $potential == 0 && $newLead > 0){
                    $category = 'new_lead';
                }

                $logDetails = [
                    'client_id' => $student->id,
                    'category' => $category,
                ];
                Log::debug($logDetails);

           

            }
            // DB::commit();
            
        } catch (Exception $e) {

            // DB::rollBack();
            Log::error('Failed to define category client : ' . $e->getMessage() . ' on line ' . $e->getLine());

        }

        // Log::notice('Client that have been verified (either student or teacher)  : ('.json_encode($updatedClients).')');

    }
}
