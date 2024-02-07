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

class ProcessVerifyClient implements ShouldQueue
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

                ## Update to verified

                # declare default variables
                $isVerified = false;

                # Case 1: have joined the program with success status
                $model = $student->clientProgram()->whereIn('status', [0, 1])->exists();
                if ($model)
                    $isVerified = true;

                # Case 2: Email, phone, and fullname are valid && school verified
                if($student->mail != null && $student->phone != null && isset($student->school) && !preg_match('/[^\x{80}-\x{F7} a-z0-9@_.\'-]/iu', $student->full_name)){
                    if($student->school->is_verified == 'Y'){
                        $isVerified = true;
                    }
                }
                
                
                if ($isVerified === true) {

                    $clientRepository->updateClient($student->id, ['is_verified' => 'Y']);

                    # push into an array updatedClients
                    array_push($updatedClients, $student->id);
                }

            }
            DB::commit();
            
        } catch (Exception $e) {

            DB::rollBack();
            Log::info('Failed to verified client from school : ' . $e->getMessage() . ' on line ' . $e->getLine());

        }

        Log::notice('Students that have been verified : ('.json_encode($updatedClients).')');

    }
}
