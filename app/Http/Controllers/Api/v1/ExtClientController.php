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

    public function getMentors()
    {
        # get the active mentors
        $existingMentors = $this->clientRepository->getExistingMentorsAPI();
        if ($existingMentors->count() == 0) {
            return response()->json([
                'success' => true,
                'message' => 'No mentor found.'
            ]);
        }

        # map the data that being shown to the user
        $mappedExistingMentors = $existingMentors->map(function ($value) {
            $trimmedFullname = trim($value->full_name);

            return [
                'fullname' => $trimmedFullname,
                'id' => $value->id,
                'extended_id' => $value->extended_id,
                'formatted' => $trimmedFullname.' | '.$value->id
            ];
        });

        return response()->json(
            [
                'success' => true,
                'message' => 'Mentors data found.',
                'data' => $mappedExistingMentors
            ]
        );
    }

    public function getAlumnis()
    {
        $existingAlumnis = $this->clientRepository->getExistingAlumnisAPI();

        return response()->json(
            [
                'success' => true,
                'message' => 'Alumnis data found.',
                'data' => $existingAlumnis
            ]
        );
    }

    public function getClientById(int $id)
    {
        $client = $this->clientRepository->getClientById($id);

        return response()->json(
            [
                'success' => true,
                'message' => 'Client data found.',
                'data' => $client
            ]
        );
    }
}
