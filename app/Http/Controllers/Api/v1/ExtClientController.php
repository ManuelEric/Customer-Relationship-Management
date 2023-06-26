<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\ClientRepositoryInterface;
use Illuminate\Http\Request;

class ExtClientController extends Controller
{

    private ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function getClientFromAdmissionMentoring()
    {
        $existingMentees = $this->clientRepository->getExistingMenteesAPI();
        

        return response()->json(
            [
                'success' => true,
                'message' => 'Mentee data found.',
                'data' => $existingMentees
            ]
        );

    }
}
