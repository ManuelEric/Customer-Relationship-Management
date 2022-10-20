<?php

namespace App\Http\Controllers;

use App\Interfaces\SchoolRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    protected SchoolRepositoryInterface $schoolRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository)
    {
        $this->schoolRepository = $schoolRepository;
    }

    public function index(): JsonResponse
    {
        return response()->json($this->schoolRepository->getAllSchools());
    }
}
