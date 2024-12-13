<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\SubSectorRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ExtCorporateController extends Controller
{
    protected SubSectorRepositoryInterface $subSectorRepository;

    public function __construct(SubSectorRepositoryInterface $subSectorRepository)
    {
        $this->subSectorRepository = $subSectorRepository;
    }

    public function cnGetSubSectorByIndustry(Request $request)
    {
        $industry_id = $request->route('industry');
        try {
            $sub_sectors = $this->subSectorRepository->rnGetSubSectorByIndustryId($industry_id);
            
            if (!$sub_sectors) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sub sector not found.'
                ]);
            }
        } catch (Exception $e) {
            Log::error('Failed get sub sector' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'There are subsectors found.',
            'data' => $sub_sectors
        ]);
        
    }
}
