<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUniversityRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\UniversityRepositoryInterface;
use App\Models\University;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class UniversityController extends Controller
{
    use CreateCustomPrimaryKeyTrait;

    private UniversityRepositoryInterface $universityRepository;

    public function __construct(UniversityRepositoryInterface $universityRepository)
    {
        $this->universityRepository = $universityRepository;
    }
    
    public function index(): JsonResponse
    {
        return response()->json(
            [
                'data' => $this->universityRepository->getAllUniversities()
            ]
        );
    }

    public function store(StoreUniversityRequest $request)
    {
        $universityDetails = $request->only([
            'univ_name',
            'univ_country',
            'univ_address',
        ]);
        
        $last_id = University::max('univ_id');
        $univ_id_without_label = $this->remove_primarykey_label($last_id, 5);
        $univ_id_with_label = 'UNIV-' . $this->add_digit($univ_id_without_label+1);

        DB::beginTransaction();
        try {

            $this->universityRepository->createUniversity(['univ_id' => $univ_id_with_label] + $universityDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store vendor failed : ' . $e->getMessage());

        }

        return Redirect::to('university');
    }

    public function create()
    {
        return view('form-university');
    }
}
