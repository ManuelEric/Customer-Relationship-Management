<?php

namespace App\Http\Controllers;

use App\Interfaces\ClientRepositoryInterface;
use Illuminate\Http\Request;

class ClientMenteeController extends Controller
{
    
    private ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }
    
    public function index(Request $request)
    {
        // $status = $request->get('st');
        // return $this->clientRepository->getAllClientByRoleAndStatusDataTables('Mentee', $status);
        if ($request->ajax()) {

            $status = $request->get('st');
            return $this->clientRepository->getAllClientByRoleAndStatusDataTables('Mentee', $status);

        }

        return view('pages.client.student.index-mentee');
    }
}
