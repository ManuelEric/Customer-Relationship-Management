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


    public function __construct(CorporateRepositoryInterface $corporateRepository, SchoolRepositoryInterface $schoolRepository, UniversityRepositoryInterface $universityRepository, PartnerAgreementRepositoryInterface $partnerAgreementRepository, AgendaSpeakerRepositoryInterface $agendaSpeakerRepository, PartnerProgramRepositoryInterface $partnerProgramRepository, SchoolProgramRepositoryInterface $schoolProgramRepository, ReferralRepositoryInterface $referralRepository)
    {
        $this->corporateRepository = $corporateRepository;
        $this->schoolRepository = $schoolRepository;
        $this->universityRepository = $universityRepository;
        $this->partnerAgreementRepository = $partnerAgreementRepository;
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
        $this->partnerProgramRepository = $partnerProgramRepository;
        $this->schoolProgramRepository = $schoolProgramRepository;
        $this->referralRepository = $referralRepository;
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

        $data = [
            'statusSchoolPrograms' => $this->schoolProgramRepository->getStatusSchoolProgramByMonthly($monthYear),
            'statusPartnerPrograms' => $this->partnerProgramRepository->getStatusPartnerProgramByMonthly($monthYear),
            'referralTypes' => $this->referralRepository->getReferralTypeByMonthly($monthYear)
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
}
