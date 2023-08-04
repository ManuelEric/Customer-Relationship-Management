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
            $asDatatables = true;
            $groupBy = false;
            switch ($status) {

                case "mentee":
                    $model = $this->clientRepository->getAlumniMentees($groupBy, $asDatatables);
                    break;

                case "non-mentee":
                    $model = $this->clientRepository->getAlumniNonMentees($groupBy, $asDatatables);
                    break;
                 
            }
            return $this->clientRepository->getDataTables($model);
        }

        return view('pages.client.student.index-mentee');
    }

    public function show(Request $request)
    {
        $menteeId = $request->route('mentee');
        $student = $this->clientRepository->getClientById($menteeId);
        $viewStudent = $this->clientRepository->getViewClientById($studentId);

        return view('pages.client.student.view')->with(
            [
                'student' => $student,
                'viewStudent' => $viewStudent
            ]
        );
    }
}
