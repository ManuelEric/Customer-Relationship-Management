<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SalesDashboardController extends Controller
{
    protected ClientRepositoryInterface $clientRepository;
    protected ClientProgramRepositoryInterface $clientProgramRepository;

    public function __construct(ClientRepositoryInterface $clientRepository, ClientProgramRepositoryInterface $clientProgramRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->clientProgramRepository = $clientProgramRepository;
    }

    public function getMenteesBirthdayByMonth(Request $request)
    {
        $month = $request->route('month');
        if ($data = $this->clientRepository->getMenteesBirthdayMonthly($month)) {
            $response = [
                'success' => true,
                'data' => $data
            ];
        } else {
            $response = [
                'success' => false,
                'data' => null
            ];
        }

        return response()->json($response);
    }

    public function compare_program(Request $request)
    {
        $query_programs_array = explode(',', $request->prog);
        $query_year_1 = $request->first_year;
        $query_year_2 = $request->second_year;
        $user = $request->u;
        
        $cp_filter = [
            'qprogs' => $query_programs_array,
            'queryParams_year1' => $query_year_1,
            'queryParams_year2' => $query_year_2,
            'quuid' => $user == 'all' ? null : $user,
        ];

        try {

            $comparisons = $this->clientProgramRepository->getComparisonBetweenYears($cp_filter);

        } catch (Exception $e) {

            Log::error($e->getMessage());

            return response()->json(['success' => false, 'data' => null]);
        }
        
        return response()->json(['success' => true, 'data' => $comparisons]);




    }
}
