<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\SchoolRepositoryInterface;
use Illuminate\Http\Request;

class SchoolController extends Controller
{

    protected SchoolRepositoryInterface $schoolRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository)
    {
        $this->schoolRepository = $schoolRepository;
    }
    
    public function search(Request $request)
    {
        $terms = $request->search;

        return $this->schoolRepository->findSchoolByTerms($terms);
    }
}
