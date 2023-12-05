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

            foreach ($clients as $student) {

                ## Update to verified

                # Case 1: have joined the program with success status
                $successProg = false;
                if($student->clientProgs->count() > 0){
                    foreach ($student->clientProgs as $clientProg) {
                        if($clientProg->status == 1){
                            $successProg = true;
                        }
                    }
                }
                // $successProg == true ? $this->info(json_encode($student)) : null;
                $successProg == true ?  $this->clientRepository->updateClient($student->id, ['is_verified' => 'Y']) : null;


                # Case 2: Email and phone is complete && school verified
                if($student->mail != null && $student->phone != null && isset($student->school)){
                    if($student->school->is_verified == 'Y'){
                        // $this->info(json_encode($student));
                        $this->clientRepository->updateClient($student->id, ['is_verified' => 'Y']);
                    }
                }

            }
            DB::commit();
            
        } catch (Exception $e) {

            DB::rollBack();
            Log::info('Failed to verified client from school : ' . $e->getMessage() . ' on line ' . $e->getLine());
        }

    }
}
