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
        $initial_programs = $this->initialProgramRepository->getAllInitProg();

        if ($request->ajax()) {

            # get the initial program from parameter get
            $selected_initial_program = $request->get('program');
    
            $model = $this->clientRepository->getClientHotLeads($selected_initial_program);
            return $this->clientRepository->getDataTables($model);
        }


        return view('pages.client.hotleads.index')->with(
            [
                'initialPrograms' => $initial_programs
            ]
        );
    }
}
