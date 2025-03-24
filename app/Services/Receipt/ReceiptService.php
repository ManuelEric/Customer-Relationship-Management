<?php

namespace App\Services\Receipt;

use App\Http\Traits\CreateReceiptIdTrait;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Models\ClientProgram;
use App\Models\Receipt;
use Illuminate\Support\Facades\DB;

class ReceiptService
{
    use CreateReceiptIdTrait;

    protected ClientProgramRepositoryInterface $clientProgramRepository;

    public function __construct(ClientProgramRepositoryInterface $clientProgramRepository)
    {
        $this->clientProgramRepository = $clientProgramRepository;
    }

    public function generateReceiptId(array $receipt_details, ClientProgram $client_prog, int $is_child_program_bundle)
    {
        # get the latest number of receipt
        $latest_number = Receipt::query()->
                    whereMonth('created_at', isset($receipt_details['receipt_date']) 
                    ? date('m', strtotime($receipt_details['receipt_date'])) 
                    : date('m'))->whereYear('created_at', isset($receipt_details['receipt_date']) 
                        ? date('Y', strtotime($receipt_details['receipt_date'])) 
                        : date('Y'))->max(DB::raw('substr(receipt_id, 1, 4)'));

        # Create Receipt Id
        $receipt_id = $this->getLatestReceiptId($latest_number, $client_prog->prog_id, $receipt_details);

        
        # if unique program bundled more than one 
        if ( $is_child_program_bundle > 0 ) 
        {
            # get the latest number of receipt bundling
            $latest_number = Receipt::
                    whereMonth('created_at', isset($request->receipt_date) ? date('m', strtotime($receipt_details['receipt_date'])) : date('m'))->
                    whereYear('created_at', isset($request->receipt_date) ? date('Y', strtotime($receipt_details['receipt_date'])) : date('Y'))->
                    whereRelation('invoiceProgram', 'bundling_id', $client_prog->bundlingDetail->bundling_id)->
                    max(DB::raw('substr(receipt_id, 1, 4)'));
            
            $bundlingDetails = $this->clientProgramRepository->getBundleProgramDetailByBundlingId($client_prog->bundlingDetail->bundling_id);

            $clientIdsBundle = $incrementBundle = [];
            $is_cross_client = false;
            foreach ($bundlingDetails as $key => $bundlingDetail) {
                $incrementBundle[$bundlingDetail->client_program->clientprog_id] = $key + 1;
                $clientIdsBundle[] = $bundlingDetail->client_program->client->id;
            }
    
            # array_count_values returns values of clientIdsBundle without duplicates
            # meaning, if there are 2 different client, the value should be higher than 1
            if(count(array_count_values($clientIdsBundle)) > 1)
                $is_cross_client = true;

            # Create Receipt Id
            $receipt_id = $this->getLatestReceiptId($latest_number, $client_prog->prog_id, $receipt_details, ['is_bundle' => 1, 'is_cross_client' => $is_cross_client, 'increment_bundle' => $incrementBundle[$client_prog->clientprog_id]]);
        }

        return $receipt_id;
    }
}