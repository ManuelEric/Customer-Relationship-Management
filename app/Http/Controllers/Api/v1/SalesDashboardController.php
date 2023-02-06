<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\ClientRepositoryInterface;
use Illuminate\Http\Request;

class SalesDashboardController extends Controller
{
    protected ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
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
}
