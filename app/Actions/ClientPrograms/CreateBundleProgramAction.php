<?php

namespace App\Actions\ClientPrograms;

use App\Interfaces\ClientLeadTrackingRepositoryInterface;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Services\Program\ClientProgramService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CreateBundleProgramAction
{
    private ClientProgramRepositoryInterface $clientProgramRepository;

    public function __construct(ClientProgramRepositoryInterface $clientProgramRepository, ClientProgramService $clientProgramService, ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository)
    {
        $this->clientProgramRepository = $clientProgramRepository;
    }

    public function execute(
        Request $request,
        Array $client_program,
        Array $client_program_details,
        String $uuid
    ) {
        foreach ($request->choosen as $key => $clientprog_id) {
            // fetch data client program
            $clientprog_db = $this->clientProgramRepository->getClientProgramById($clientprog_id);
            
            // check there is an invoice 
            $has_invoice_std = isset($clientprog_db->invoice) ? $clientprog_db->invoice()->count() : 0;
            $has_bundling = isset($clientprog_db->bundlingDetail) ? $clientprog_db->bundlingDetail()->count() : 0;

            $client_program[$request->number[$key]] = [
                'clientprog_id' => $clientprog_id,
                'status' => $clientprog_db->status,
                'program' => $clientprog_db->prog_id,
                'HasInvoice' => $has_invoice_std,
                'HasBundling' => $has_bundling,
            ];
            
            $client_program_details[] = [
                'clientprog_id' => $clientprog_id,
                'bundling_id' => $uuid,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

        }
        
        return ['client_program' => $client_program, 'client_program_details' => $client_program_details];
    
    }
}
