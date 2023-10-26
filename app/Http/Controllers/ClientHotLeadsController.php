<?php

namespace App\Http\Controllers;

use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\InitialProgramRepositoryInterface;
use Illuminate\Http\Request;

class ClientHotLeadsController extends Controller
{
    protected ClientRepositoryInterface $clientRepository;
    protected InitialProgramRepositoryInterface $initialProgramRepository;

    public function __construct(ClientRepositoryInterface $clientRepository, InitialProgramRepositoryInterface $initialProgramRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->initialProgramRepository = $initialProgramRepository;
    }

    public function index(Request $request)
    {
        # the initial program that we sell
        # in order to automatically added if there's new initial program 
        $initialPrograms = $this->initialProgramRepository->getAllInitProg();

        if ($request->ajax()) {

            # get the initial program from parameter get
            $selectedInitialProgram = $request->get('program');
    
            return $this->clientRepository->getClientHotLeads($selectedInitialProgram);
        }


        return view('pages.client.hotleads.index')->with(
            [
                'initialPrograms' => $initialPrograms
            ]
        );
    }
}
