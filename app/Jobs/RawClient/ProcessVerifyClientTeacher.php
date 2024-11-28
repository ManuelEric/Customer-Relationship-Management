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
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ProcessVerifyClientTeacher implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use IsMonitored;

    protected ClientRepositoryInterface $clientRepository;
    protected $clientIds;
    protected $is_many_request;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($clients, $is_many_request = false)
    {
        $this->clientIds = $clients;
        $this->is_many_request = $is_many_request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ClientRepositoryInterface $clientRepository)
    {
        $teachers = $clientRepository->getClientsById($this->clientIds);
        DB::beginTransaction();
        try {

            foreach ($teachers as $teacher) {

                ## Update to verified

                # Case 1: Email and phone is complete && school verified
                if($teacher->mail != null && $teacher->phone != null && isset($teacher->school) && !preg_match('/[^\x{80}-\x{F7} a-z0-9@_.\'-]/iu', $teacher->full_name)){
                    if($teacher->school->is_verified == 'Y'){
                        Log::debug(['client_id' => $teacher->id, 'is_verified' => true]);
                        $clientRepository->updateClient($teacher->id, ['is_verified' => 'Y', 'is_many_request' => true]);
                    }
                }
            }
            DB::commit();
            
        } catch (Exception $e) {

            DB::rollBack();
            Log::info('Failed to verified client teacher : ' . $e->getMessage() . ' on line ' . $e->getLine());
        }

    }
}
