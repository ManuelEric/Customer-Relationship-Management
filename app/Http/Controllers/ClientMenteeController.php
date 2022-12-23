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
        if ($request->ajax()) {

            return $this->clientRepository->getAllClientByRoleAndStatusDataTables('Mentee', NULL);

        }

        return view('pages.client.student.index-mentee');
    }
}
