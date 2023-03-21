<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\PartnerAgreementRepositoryInterface;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
use App\Interfaces\PartnerProgramRepositoryInterface;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\ReferralRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;


class PartnerDashboardController extends Controller
{
    protected CorporateRepositoryInterface $corporateRepository;
    protected SchoolRepositoryInterface $schoolRepository;
    protected UniversityRepositoryInterface $universityRepository;
    protected PartnerAgreementRepositoryInterface $partnerAgreementRepository;
    protected AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;
    protected PartnerProgramRepositoryInterface $partnerProgramRepository;
    protected SchoolProgramRepositoryInterface $schoolProgramRepository;
    protected ReferralRepositoryInterface $referralRepository;
    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;


    public function __construct(CorporateRepositoryInterface $corporateRepository, SchoolRepositoryInterface $schoolRepository, UniversityRepositoryInterface $universityRepository, PartnerAgreementRepositoryInterface $partnerAgreementRepository, AgendaSpeakerRepositoryInterface $agendaSpeakerRepository, PartnerProgramRepositoryInterface $partnerProgramRepository, SchoolProgramRepositoryInterface $schoolProgramRepository, ReferralRepositoryInterface $referralRepository, InvoiceB2bRepositoryInterface $invoiceB2bRepository)
    {
        $this->corporateRepository = $corporateRepository;
        $this->schoolRepository = $schoolRepository;
        $this->universityRepository = $universityRepository;
        $this->partnerAgreementRepository = $partnerAgreementRepository;
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
        $this->partnerProgramRepository = $partnerProgramRepository;
        $this->schoolProgramRepository = $schoolProgramRepository;
        $this->referralRepository = $referralRepository;
        $this->invoiceB2bRepository = $invoiceB2bRepository;
    }


    public function getTotalByMonth(Request $request)
    {
        $monthYear = $request->route('month');


        $newPartner = $this->corporateRepository->getCountTotalCorporateByMonthly($monthYear, 'monthly');
        $totalPartner = $this->corporateRepository->getCountTotalCorporateByMonthly($monthYear, 'total');
        $beforeMonthPartner = $this->corporateRepository->getCountTotalCorporateByMonthly($monthYear, 'beforeMonth');

        if ($beforeMonthPartner > 0) {
            $percentage = ($totalPartner - $beforeMonthPartner) * 100 / $beforeMonthPartner;
        } else if ($totalPartner > 0) {
            $percentage = 100;
        }

        $data = [
            'percentagePartner' => $percentage,
            'newPartner' => $newPartner,
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

    public function getSpeakerByDate(Request $request)
    {
        $date = $request->route('date');

        $data = [
            'allSpeaker' => $this->agendaSpeakerRepository->getAllSpeakerDashboard('byDate', $date),
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

    public function getPartnershipProgramByMonth(Request $request)
    {
        $monthYear = $request->route('month');

        $totalPartnership = $this->invoiceB2bRepository->getTotalPartnershipProgram($monthYear);
        $totalPartnerProgram = $totalPartnership->where('type', 'partner_prog')->sum('invb2b_totpriceidr');
        $totalSchoolProgram = $totalPartnership->where('type', 'sch_prog')->sum('invb2b_totpriceidr');
        $schoolPrograms = $this->schoolProgramRepository->getStatusSchoolProgramByMonthly($monthYear);

        $data = [
            'statusSchoolPrograms' => $schoolPrograms,
            'statusPartnerPrograms' => $this->partnerProgramRepository->getStatusPartnerProgramByMonthly($monthYear),
            'referralTypes' => $this->referralRepository->getReferralTypeByMonthly($monthYear),
            'totalPartnerProgram' => $totalPartnerProgram,
            'totalSchoolProgram' => $schoolPrograms->sum('total_fee'),
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

    public function getPartnershipProgramDetailByMonth(Request $request)
    {
        $type = $request->route('type');
        $status = $request->route('status');
        $monthYear = $request->route('month');

        switch ($status) {
            case 'Pending':
                $status = 0;
                break;
            case 'Success':
                $status = 1;
                break;
            case 'Denied':
                $status = 2;
                break;
            case 'Refund':
                $status = 3;
                break;
            case 'Referral IN':
                $status = 'In';
                break;
            case 'Referral Out':
                $status = 'Out';
                break;
        }

        switch ($type) {
            case 'school':
                $data = $this->schoolProgramRepository->getAllSchoolProgramByStatusAndMonth($status, $monthYear);
                break;
            case 'partner':
                $data = $this->partnerProgramRepository->getAllPartnerProgramByStatusAndMonth($status, $monthYear);
                break;
            case 'referral':
                $data = $this->referralRepository->getAllReferralByTypeAndMonth($status, $monthYear);
                break;
        }


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

    public function getProgramComparison(Request $request)
    {
        $startYear = $request->route('start_year');
        $endYear = $request->route('end_year');

        $schoolProgramMerge = $this->schoolProgramRepository->getSchoolProgramComparison($startYear, $endYear);
        $partnerProgramMerge = $this->partnerProgramRepository->getPartnerProgramComparison($startYear, $endYear);
        $referralMerge = $this->referralRepository->getReferralComparison($startYear, $endYear);
        $totReferral = $this->referralRepository->getTotalReferralProgramComparison($startYear, $endYear);

        $programComparisonMerge = $this->mergeProgramComparison($schoolProgramMerge, $partnerProgramMerge, $referralMerge);

        $programComparisons = $this->mappingProgramComparison($programComparisonMerge);

        $data = [
            'programComparisons' => $programComparisons,
            'partnerPrograms' => $this->partnerProgramRepository->getPartnerProgramComparison($startYear, $endYear),
            'totalSch' => $this->schoolProgramRepository->getTotalSchoolProgramComparison($startYear, $endYear),
            'totalPartner' => $this->partnerProgramRepository->getTotalPartnerProgramComparison($startYear, $endYear),
            'totalReferral' => $this->referralRepository->getTotalReferralProgramComparison($startYear, $endYear),
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

    protected function mappingProgramComparison($data)
    {
        return $data->mapToGroups(function ($item, $key) {
            return [
                $item['program_name'] . ' - ' . $item['type'] => [
                    'program_name' => $item['program_name'],
                    'type' => $item['type'],
                    'year' => $item['year'],

                    $item['year'] =>
                    [
                        'participants' => $item['participants'],
                        'total' => $item['total'],
                        'count_program' => $item['count_program']
                    ]
                ],
            ];
        });
    }

    protected function mergeProgramComparison($schoolProgram, $partnerProgram, $referral)
    {
        $collection = collect($schoolProgram);
        return $collection->merge($partnerProgram)->merge($referral);
    }
}
