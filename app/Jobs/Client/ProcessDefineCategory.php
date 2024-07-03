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
        $clients = $clientRepository->getClientsById($this->clientIds);
        DB::beginTransaction();
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
                    - Or have clientprogram & (join admission with status success (1) where prog running status == done (2) and join another program with status pending(0))
               */

                # Non mentee
                /*
                    - Have clientprogram & (Not join admission with status success (1) where prog running status != done (2))
                    - Or have clientprogram & (Not join admission with status success (1) and join another program with status pending(0))
                */

                # Alumni mentee
                /*
                    - Have clientprogram & (join admission with status success (1) where prog running status == done (2))
                */

                # Alumni non mentee
                /*
                    - Have clientprogram & (not join admission with status success (1) where prog running status == done (2))
                */

                if ($student->is_verified == 'N') {
                    Log::warning('Client with id ' . $student->id . ', failed to determine its category because it has not been verified yet');
                    continue;
                }


                $categories = new Collection;
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


                // if ($mentee > 0) {
                //     $category = 'mentee';
                // } else if ($mentee == 0 && $nonMentee > 0) {
                //     $category = 'non-mentee';
                // } else if ($mentee == 0 && $nonMentee == 0 && $potential > 0) {
                //     $category = 'potential';
                // } else if ($mentee == 0 && $nonMentee == 0 && $potential == 0 && $alumniMentee > 0) {
                //     $category = 'alumni-mentee';
                // } else if ($mentee == 0 && $nonMentee == 0 && $potential == 0 && $alumniMentee == 0 && $alumniNonMentee > 0) {
                //     $category = 'alumni-non-mentee';
                // } else if ($mentee == 0 && $nonMentee == 0 && $potential == 0 && $alumniMentee == 0 && $alumniNonMentee == 0 && $newLead > 0) {
                //     $category = 'new-lead';
                // }


                // $logDetails = [
                //     'client_id' => $student->id,
                //     'category' => $category,
                //     'mentee' => $mentee,
                //     'nonMentee' => $nonMentee,
                //     'potential' => $potential,
                //     'newLead' => $newLead,
                //     'alumniMentee' => $alumniMentee,
                //     'alumniNonMentee' => $alumniNonMentee,
                // ];

                $clientRepository->updateClient($student->id, ['category' => $category]);

                $updatedClients[] = [
                    'client_id' => $student->id,
                    'category' => $category,
                ];
            }
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to define category client : ' . $e->getMessage() . ' on line ' . $e->getLine());
        }

        Log::notice('clients that have defined categories  : (' . json_encode($updatedClients) . ')');
    }
}
