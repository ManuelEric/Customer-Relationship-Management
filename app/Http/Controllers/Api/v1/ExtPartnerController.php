<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\PartnerRepositoryInterface;
use Illuminate\Http\Request;

class ExtPartnerController extends Controller
{
    protected PartnerRepositoryInterface $partnerRepository;

    public function __construct(PartnerRepositoryInterface $partnerRepository)
    {
        $this->partnerRepository = $partnerRepository;
    }

    public function getPartners(Request $request)
    {
        $partner = $this->partnerRepository->getAllPartner();
        if (!$partner) {
            return response()->json([
                'success' => true,
                'message' => 'No partner found.'
            ]);
        }

        # map the data that being shown to the user
        $mappedPartner = $partner->map(function ($value) {
            return [
                'partner' => $value->corp_name,
                'id' => $value->corp_id,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'There are partners found.',
            'data' => $mappedPartner
        ]);
    }
}
