<?php

namespace App\Http\Controllers\Api\v1;

use App\Enum\LogModule;
use App\Http\Controllers\Controller;
use App\Interfaces\SubSectorRepositoryInterface;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\Request;

class ExtCorporateController extends Controller
{
    protected SubSectorRepositoryInterface $subSectorRepository;

    public function __construct(SubSectorRepositoryInterface $subSectorRepository)
    {
        $this->subSectorRepository = $subSectorRepository;
    }

    public function cnGetSubSectorByIndustry(Request $request, LogService $log_service)
    {
        $industry_id = (int) $request->route('industry');
        try {
            $sub_sectors = $this->subSectorRepository->rnGetSubSectorByIndustryId($industry_id);

            if (! $sub_sectors) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sub sector not found.',
                ]);
            }
        } catch (Exception $e) {
            $log_service->createErrorLog(LogModule::GET_SUB_SECTOR_BY_INDUSTRY, $e->getMessage(), $e->getLine(), $e->getFile(), ['industry_id' => $industry_id]);
        }

        return response()->json([
            'success' => true,
            'message' => 'There are subsectors found.',
            'data' => $sub_sectors,
        ]);

    }
}
