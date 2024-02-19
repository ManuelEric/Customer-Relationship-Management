<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\UniversityRepositoryInterface;
use Illuminate\Http\Request;

class ExtUniversityController extends Controller
{
    protected UniversityRepositoryInterface $universityRepository;

    public function __construct(UniversityRepositoryInterface $universityRepository)
    {
        $this->universityRepository = $universityRepository;
    }

    public function getUniversities(Request $request)
    {
        $universities = $this->universityRepository->getAllUniversities();
        if (!$universities) {
            return response()->json([
                'success' => true,
                'message' => 'No universities found.'
            ]);
        }

        # map the data that being shown to the user
        $mappedUniversities = $universities->map(function ($value) {
            return [
                'univ_id' => $value->univ_id,
                'univ_name' => $value->univ_name,
                'univ_country' => $value->univ_country
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'There are universities found.',
            'data' => $mappedUniversities
        ]);
    }
}
