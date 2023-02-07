<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\PartnerAgreementRepositoryInterface;


class PartnerDashboardController extends Controller
{
    protected CorporateRepositoryInterface $corporateRepository;
    protected SchoolRepositoryInterface $schoolRepository;
    protected UniversityRepositoryInterface $universityRepository;
    protected PartnerAgreementRepositoryInterface $partnerAgreementRepository;



    public function __construct(CorporateRepositoryInterface $corporateRepository, SchoolRepositoryInterface $schoolRepository, UniversityRepositoryInterface $universityRepository, PartnerAgreementRepositoryInterface $partnerAgreementRepository)
    {
        $this->corporateRepository = $corporateRepository;
        $this->schoolRepository = $schoolRepository;
        $this->universityRepository = $universityRepository;
        $this->partnerAgreementRepository = $partnerAgreementRepository;

    }


    public function getTotalByMonth(Request $request)
    {
        $monthYear = $request->route('month');

        $data = [
            'newPartner' => $this->corporateRepository->getCountTotalCorporateByMonthly($monthYear),
            'newSchool' => $this->schoolRepository->getCountTotalSchoolByMonthly($monthYear),
            'newUniversity' => $this->universityRepository->getCountTotalUniversityByMonthly($monthYear),
            'totalAgreement' => $this->partnerAgreementRepository->getCountTotalPartnerAgreementByMonthly($monthYear)

        ];

        if ($data) {
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
