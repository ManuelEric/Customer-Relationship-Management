<?php

namespace App\Http\Controllers;

use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\FollowupRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\PartnerAgreementRepositoryInterface;
use App\Interfaces\AgendaSpeakerRepositoryInterface;


use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected ClientRepositoryInterface $clientRepository;
    protected FollowupRepositoryInterface $followupRepository;
    protected CorporateRepositoryInterface $corporateRepository;
    protected SchoolRepositoryInterface $schoolRepository;
    protected UniversityRepositoryInterface $universityRepository;
    protected PartnerAgreementRepositoryInterface $partnerAgreementRepository;
    protected AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;


    public function __construct(ClientRepositoryInterface $clientRepository, FollowupRepositoryInterface $followupRepository, CorporateRepositoryInterface $corporateRepository, SchoolRepositoryInterface $schoolRepository, UniversityRepositoryInterface $universityRepository, PartnerAgreementRepositoryInterface $partnerAgreementRepository, AgendaSpeakerRepositoryInterface $agendaSpeakerRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->followupRepository = $followupRepository;
        $this->corporateRepository = $corporateRepository;
        $this->schoolRepository = $schoolRepository;
        $this->universityRepository = $universityRepository;
        $this->partnerAgreementRepository = $partnerAgreementRepository;
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
    }

    public function index(Request $request)
    {
        // return $this->indexSales($request);
        return $this->indexPartnership($request);
    }

    # sales dashboard

    public function indexSales($request)
    {

        $totalClientByStatus = [
            'prospective' => $this->clientRepository->getCountTotalClientByStatus(0), # prospective
            'potential' => $this->clientRepository->getCountTotalClientByStatus(1), # potential
            'current' => $this->clientRepository->getCountTotalClientByStatus(2), # current
            'completed' => $this->clientRepository->getCountTotalClientByStatus(3), # current
            'mentee' => $this->clientRepository->getAllClientByRole('mentee')->count(),
            'alumni' => $this->clientRepository->getAllClientByRole('alumni')->count(),
            'parent' => $this->clientRepository->getAllClientByRole('parent')->count(),
            'teacher_counselor' => $this->clientRepository->getAllClientByRole('Teacher/Counselor')->count()
        ];

        $followUpReminder = $this->followupRepository->getAllFollowupWithin(7);
        $menteesBirthday = $this->clientRepository->getMenteesBirthdayMonthly(date('m'));

        return view('pages.dashboard.index')->with(
            [
                'totalClientInformation' => $totalClientByStatus,
                'followUpReminder' => $followUpReminder,
                'menteesBirthday' => $menteesBirthday
            ]
        );
    }

    public function indexPartnership($request)
    {
        $date = null;

        $totalPartner = $this->corporateRepository->getAllCorporate()->count();
        $totalSchool = $this->schoolRepository->getAllSchools()->count();
        $totalUniversity = $this->universityRepository->getAllUniversities()->count();
        $totalAgreement = $this->partnerAgreementRepository->getCountTotalPartnerAgreementByMonthly(date('Y-m'));
        $newPartner = $this->corporateRepository->getCountTotalCorporateByMonthly(date('Y-m'));
        $newSchool = $this->schoolRepository->getCountTotalSchoolByMonthly(date('Y-m'));
        $newUniversity = $this->universityRepository->getCountTotalUniversityByMonthly(date('Y-m'));
        $speakers = $this->agendaSpeakerRepository->getAllSpeakerDashboard('all', $date);

        return view('pages.dashboard.index')->with(
            [
                'totalPartner' => $totalPartner,
                'totalSchool' => $totalSchool,
                'totalUniversity' => $totalUniversity,
                'totalAgreement' => $totalAgreement,
                'newPartner' => $newPartner,
                'newSchool' => $newSchool,
                'newUniversity' => $newUniversity,
                'speakers' => $speakers
            ]
        );
    }
}
