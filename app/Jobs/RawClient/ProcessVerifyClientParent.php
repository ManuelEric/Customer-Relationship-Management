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
        $parents = $this->clientRepository->getAllClientByRole('Parent');
        DB::beginTransaction();
        try {

            foreach ($parents as $parent) {

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

            }
            DB::commit();
            
        } catch (Exception $e) {

            DB::rollBack();
            Log::info('Failed to verified client parent : ' . $e->getMessage() . ' on line ' . $e->getLine());
        }

    }
}
