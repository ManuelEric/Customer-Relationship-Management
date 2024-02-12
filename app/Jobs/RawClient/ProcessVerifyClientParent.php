<?php

namespace App\Jobs\RawClient;

use App\Interfaces\ClientRepositoryInterface;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessVerifyClientParent implements ShouldQueue
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

        if ($this->clientIds != NULL) 
        {
            # if some functions has called this queue and send parameters like client Id
            # then fetch the client only
            $parents = $clientRepository->getClientsById($this->clientIds);

        } else {

            # if the client id not declared
            # then fetch all the client parents

            # assuming this function being called from cron in purpose to change every unverified parent into verified
            $parents = $clientRepository->getUnverifiedParent();

        }


        DB::beginTransaction();
        try {

            # declare default variables
            $updatedParents = [];

            foreach ($parents as $parent) {

                ## Update to verified

                # declare default variables
                $isVerified = false;

                # Case 1: have joined the program with success status
                $model = $parent->childrens()->whereHas('clientProgram', function ($query) {
                            $query->where('tbl_client_prog.status', 1);
                        })->exists();
                if ($model)
                    $isVerified = true;

                # Case 2: Email, phone, and name are valid
                if ($parent->mail != null && $parent->phone != null && !preg_match('/[^\x{80}-\x{F7} a-z0-9@_.\'-]/iu', $parent->full_name)) 
                    $isVerified = true;
                
                
                if ($isVerified === true) {

                    $clientRepository->updateClient($parent->id, ['is_verified' => 'Y']);

                    # push into an array updatedParents
                    array_push($updatedParents, $parent->id);

                }

            }
            DB::commit();
            
        } catch (Exception $e) {

            DB::rollBack();
            Log::info('Failed to verified client parent : ' . $e->getMessage() . ' on line ' . $e->getLine());
        }

        Log::notice('Parents that have been verified : ('.json_encode($updatedParents).')');

    }
}
