<?php

namespace App\Jobs\Client;

use App\Interfaces\ClientLogRepositoryInterface;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Models\ClientLog;
use App\Repositories\ClientProgramRepository;
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
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ProcessInsertLogClient implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use IsMonitored;

    protected ClientRepositoryInterface $clientRepository;
    protected ClientProgramRepositoryInterface $clientProgramRepository;
    protected ClientLogRepositoryInterface $clientLogRepository;
    protected $clients_data;
    protected $is_many_request;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    

    # Clients data => [][client_id, first_name(nullable), last_name(nullable), lead_source, inputted_from(nullable), clientprog_id(nullable)]
    public function __construct($clients_data, $is_many_request = false)
    {
        $this->clients_data = $clients_data;
        $this->is_many_request = $is_many_request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ClientRepositoryInterface $clientRepository, ClientProgramRepositoryInterface $clientProgramRepository, ClientLogRepositoryInterface $clientLogRepository)
    {
        DB::beginTransaction();
        try {

            foreach ($this->clients_data as $key => $client_data) {

                /* 
                    1. Input new client from manual crm
                        - Add log category raw-lead
                        - set new group id
                        - Add log category new-lead
                        - Update category from tbl_client to new-lead
                    
                    2. Input from Import or form embed
                        # Check existing all client (include deleted client)
                        - Update is_verified 'N' (no)
                        - Add log category raw-lead
                        - Update category from tbl_client to raw-lead
                    
                    3. Verified raw client
                        - get latest log client where category raw, if null throw exception
                        - if select_existing true then update client_id log client to client_id existing
                        - set unique_key and lead_source from latest log client
                        - insert log client with category new-lead
                        - if inputted_from import-client-program then insert log client with category potential and clientprog_id from the latest log client
                        - Update category from tbl_client with result define_category_from_all_program

                    4. Restore client
                        - Add log category raw-lead
                        - update status all client program to failed and delete deleted_at client

                    5. Trigger Create/Update/Delete client program
                        # create
                            - if status program pending then add log client potential
                            - if status program success then add log client potential and mentee/non-mentee
                            - if status program failed then add log client potential and failed mentee/non-mentee
                            - if status program hold/refund then add log client hold/refund-mentee/non-mentee

                        # update
                            - if old_status_program = new_status_program continue / no action
                            - if status program pending no action
                            - if status program failed 
                                then
                                    if old_status_program is success then add log client failed-mentee/non-mentee
                                    if old_status_program is pending then no action
                            - if status program success 
                                then
                                    if program is adm then add log client with category mentee else non-mentee
                                    define_category_from_all_program
                                    update category from tbl_client with result define_category_from_all_program
                            - if status program hold/refund
                                then
                                    add log client hold/refund-mentee/non-mentee

                        # delete
                            - delete client log sesuai dengan clientprogram_id 

                        - Define category based on all client programs
                        - Update category from tbl_client with result define category

                    6. Delete/trash client
                        - Add log category trash
                        - If Client program status pending then Update status client program to failed
                        
                */ 
                
                
                switch ($client_data['inputted_from']) {

                    case 'manual':
                        # add log client with category raw
                        $client_data['category'] = 'raw';
                        $new_client_log = $clientRepository->createClientLog($client_data);
        
                        # update category from tbl_client to new-lead
                        $clientRepository->updateClient($client_data['client_id'], ['category' => $client_data['category'], 'is_verified' => 'N']);
                        break;
                    
                    case 'import-parent':
                    case 'import-student':
                    case 'import-client-event':
                    case 'import-client-program':
                    case 'form-embed':
                        # add log client with category raw
                        $client_data['category'] = 'raw';
                        $clientRepository->createClientLog($client_data);
                        
                        # update category from tbl_client to new-lead
                        $clientRepository->updateClient($client_data['client_id'], ['category' => $client_data['category'], 'is_verified' => 'N', 'deleted_at' => null, 'is_many_request' => $this->is_many_request]);
                        break;

                    case 'restore':
                        $latest_client_log = $this->fnGetLatestClientLog($clientRepository, $client_data);
                        $client_data = $this->fnSetLeadSourceAndUniqueKey($clientRepository, $client_data, $latest_client_log);

                        # add log client with category raw
                        $client_data['category'] = 'raw';
                        $clientRepository->createClientLog($client_data);

                        # update category from tbl_client to new-lead
                        $clientRepository->updateClient($client_data['client_id'], ['category' => $client_data['category'], 'is_verified' => 'N', 'is_many_request' => $this->is_many_request]);

                        $get_client = $clientRepository->getClientById($client_data['client_id']);

                        # update status all client program to failed 
                        $this->fnSetAllClientProgramToFailed($clientProgramRepository, $get_client);
                        break;
                    
                    case 'verified':                        
                        $latest_client_log = $this->fnGetLatestClientLog($clientRepository, $client_data);
                        $client_data = $this->fnSetLeadSourceAndUniqueKey($clientRepository, $client_data, $latest_client_log);

                        // Log::debug($client_data);
                        # if when verified select existing 
                        # then update client_id log client to client_id existing
                        if($client_data['select_existing']){                            
                            $clientLogRepository->updateClientLogByClientUUID($client_data['old_client_id'], ['client_id' => $client_data['client_id']]);
                        }
                         
                        unset($client_data['select_existing']);
                        unset($client_data['old_client_id']);
                        
                        # add new log client with category new-lead
                        $client_data['category'] = 'new-lead';
                        $clientRepository->createClientLog($client_data);
 
                        # if inputted_from import-client-program add log client with category potential
                        if($latest_client_log != null && $latest_client_log->inputted_from == 'import-client-program')
                        {
                            # add new log client with category potential and insert clientprog_id from category raw
                            $client_data['clientprog_id'] = $latest_client_log->clientprog_id;
                            $client_data['category'] = 'potential';
                            $clientRepository->createClientLog($client_data);
                        }    

                        $define_category_from_all_program = $clientRepository->defineCategoryClient($client_data, $this->is_many_request)['category'];
                      
                        # update category from tbl_client
                        $clientRepository->updateClient($client_data['client_id'], ['category' => $define_category_from_all_program, 'is_verified' => 'Y', 'is_many_request' => $this->is_many_request]);
                        break;

                    # create or client program
                    case 'create-client-program':
                        $latest_client_log = $this->fnGetLatestClientLog($clientRepository, $client_data);
                        $client_data = $this->fnSetLeadSourceAndUniqueKey($clientRepository, $client_data, $latest_client_log);

                        $is_admission = $clientProgramRepository->checkProgramIsAdmission($client_data['clientprog_id']);
                        
                        switch ($client_data['status_program']) {
                            case 0: # pending
                                unset($client_data['status_program']);
                                $client_data['category'] = 'potential';
                                $clientRepository->createClientLog($client_data);
                                break;
                                
                            case 1: # success
                                unset($client_data['status_program']);
                                $client_data['category'] = 'potential';
                                $clientRepository->createClientLog($client_data);
                                
                                $client_data['category'] = $is_admission ? 'mentee' : 'non-mentee';
                                $clientRepository->createClientLog($client_data);
                                break;
                                
                            case 2: # failed
                                unset($client_data['status_program']);
                                $client_data['category'] = 'potential';
                                $clientRepository->createClientLog($client_data);
                                
                                $client_data['category'] = $is_admission ? 'failed-mentee' : 'failed-non-mentee';
                                $clientRepository->createClientLog($client_data);
                                break;
                                
                            case 3: # refund
                                unset($client_data['status_program']);
                                $client_data['category'] = $is_admission ? 'refund-mentee' : 'refund-non-mentee';
                                $clientRepository->createClientLog($client_data);
                                break;
                                
                            case 4: # hold
                                unset($client_data['status_program']);
                                $client_data['category'] = $is_admission ? 'hold-mentee' : 'hold-non-mentee';
                                $clientRepository->createClientLog($client_data);
                                break;
                                
                        }
                        
                        $define_category_from_all_program = $clientRepository->defineCategoryClient($client_data, $this->is_many_request)['category'];
                        # update category from tbl_client
                        $clientRepository->updateClient($client_data['client_id'], ['category' => $define_category_from_all_program, 'is_verified' => 'Y', 'is_many_request' => $this->is_many_request]);
                        break;

                    case 'update-client-program':
                        $latest_client_log = $this->fnGetLatestClientLog($clientRepository, $client_data);
                        $client_data = $this->fnSetLeadSourceAndUniqueKey($clientRepository, $client_data, $latest_client_log);

                        $is_admission = $clientProgramRepository->checkProgramIsAdmission($client_data['clientprog_id']);

                        if($client_data['old_status_program'] == $client_data['status_program'])
                            # no action
                            break;

                        switch ($client_data['status_program']) {
                            case 1: # success
                                unset($client_data['old_status_program']);
                                unset($client_data['status_program']);
                                $client_data['category'] = $is_admission ? 'mentee' : 'non-mentee';
                                $clientRepository->createClientLog($client_data);
                                break;
                                
                            case 2: # failed
                                if($client_data['old_status_program'] == 1) # success
                                {
                                    unset($client_data['old_status_program']);
                                    unset($client_data['status_program']);
                                    $client_data['category'] = $is_admission ? 'failed-mentee' : 'failed-non-mentee';
                                    $clientRepository->createClientLog($client_data);
                                }   
                                break;
                                
                            case 3: # refund
                                unset($client_data['old_status_program']);
                                unset($client_data['status_program']);
                                $client_data['category'] = $is_admission ? 'refund-mentee' : 'refund-non-mentee';
                                $clientRepository->createClientLog($client_data);
                                break;
                            
                            case 4: # hold
                                unset($client_data['old_status_program']);
                                unset($client_data['status_program']);
                                $client_data['category'] = $is_admission ? 'hold-mentee' : 'hold-non-mentee';
                                $clientRepository->createClientLog($client_data);
                                break;
                        }

                        $define_category_from_all_program = $clientRepository->defineCategoryClient($client_data, $this->is_many_request)['category'];
                        # update category from tbl_client
                        $clientRepository->updateClient($client_data['client_id'], ['category' => $define_category_from_all_program, 'is_verified' => 'Y', 'is_many_request' => $this->is_many_request]);
                        
                        break;
                        
                    case 'delete-client-program':
                        $clientLogRepository->deleteClientLogByClientProgIdAndClientUUID($client_data['clientprog_id'], $client_data['client_id']);
                        
                        $define_category_from_all_program = $clientRepository->defineCategoryClient($client_data, $this->is_many_request)['category'];
                        # update category from tbl_client
                        $clientRepository->updateClient($client_data['client_id'], ['category' => $define_category_from_all_program, 'is_verified' => 'Y', 'is_many_request' => $this->is_many_request]);
                        break;

                    case 'trash':
                        $latest_client_log = $this->fnGetLatestClientLog($clientRepository, $client_data);
                        $client_data = $this->fnSetLeadSourceAndUniqueKey($clientRepository, $client_data, $latest_client_log);

                        # add log client with category trash
                        $client_data['category'] = 'trash';
                        $clientRepository->createClientLog($client_data);
                            
                        # update category from tbl_client to new-lead
                        $clientRepository->updateClient($client_data['client_id'], ['category' => $client_data['category'], 'is_many_request' => $this->is_many_request]);
                            
                        # only temporarily and will be removed in crm adjusted
                        $get_client = $clientRepository->getClientById($client_data['client_id']);
                            
                        # update status all client program to failed 
                        $this->fnSetAllClientProgramToFailed($clientProgramRepository, $get_client);                            
                        break;

                }
            }
            
           
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to insert log client : ' . $e->getMessage() . ' on line ' . $e->getLine());
        }

        Log::notice('Successfully insert log client  : (' . json_encode($this->clients_data) . ')');
    }

    protected function fnSetAllClientProgramToFailed(ClientProgramRepository $clientProgramRepository, $get_client)
    {
        $get_client_programs = $clientProgramRepository->getClientProgramByClientId($get_client->id);
       
        # Get client program where status = 0 (pending)
        $clientprog_ids = $get_client_programs->where('status', 0)->pluck('clientprog_id')->toArray();

        # Update stutus client program to failed
        $clientProgramRepository->updateClientPrograms($clientprog_ids, ['status' => 2]);
    }

    protected function fnGetLatestClientLog(ClientRepositoryInterface $clientRepository, $client_data)
    {
        $latest_client_log = null;

        # only temporarily and will be removed in crm adjusted
        $get_client = $clientRepository->getClientById(isset($client_data['select_existing']) && $client_data['select_existing'] ? $client_data['old_client_id'] : $client_data['client_id']);
            
        if(isset($get_client->client_log)){
            $latest_client_log = $get_client->client_log->sortByDesc('updated_at')->first();
            
            if($client_data['inputted_from'] = 'verified')
                $latest_client_log = $get_client->client_log->where('category', 'raw')->sortByDesc('updated_at')->first();
        }

        return $latest_client_log;
    }

    protected function fnSetLeadSourceAndUniqueKey($clientRepository, $client_data, $latest_client_log)
    {
        if($latest_client_log){
            $client_data['unique_key'] = $latest_client_log->unique_key;
            $client_data['lead_source'] = $latest_client_log->lead_source;
        }else{
            $get_client = $clientRepository->getClientById($client_data['client_id']);
            
            # if client no have log then set lead_source from lead_id tbl_client
            $client_data['lead_source'] = $get_client->lead_id;
        }

        return $client_data;
    }
}
