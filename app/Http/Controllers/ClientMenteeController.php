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
            $status = $request->get('st');
            return $this->clientRepository->getAllClientByRoleAndStatusDataTables('Mentee', $status);

        }

        return view('pages.client.student.index-mentee');
    }

    public function show(Request $request)
    {
        $menteeId = $request->route('mentee');
        $student = $this->clientRepository->getClientById($menteeId);

        return view('pages.client.student.view')->with(
            [
                'student' => $student
            ]
        );
    }
}
