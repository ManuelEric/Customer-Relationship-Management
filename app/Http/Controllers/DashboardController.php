<?php

namespace App\Http\Controllers;

use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\FollowupRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\PartnerAgreementRepositoryInterface;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
use App\Interfaces\PartnerProgramRepositoryInterface;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\ReferralRepositoryInterface;



use App\Interfaces\UserRepositoryInterface;
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
    protected PartnerProgramRepositoryInterface $partnerProgramRepository;
    protected SchoolProgramRepositoryInterface $schoolProgramRepository;
    protected ReferralRepositoryInterface $referralRepository;
    protected ClientProgramRepositoryInterface $clientProgramRepository;
    protected UserRepositoryInterface $userRepository;


    public function __construct(ClientRepositoryInterface $clientRepository, FollowupRepositoryInterface $followupRepository, CorporateRepositoryInterface $corporateRepository, SchoolRepositoryInterface $schoolRepository, UniversityRepositoryInterface $universityRepository, PartnerAgreementRepositoryInterface $partnerAgreementRepository, AgendaSpeakerRepositoryInterface $agendaSpeakerRepository, PartnerProgramRepositoryInterface $partnerProgramRepository, SchoolProgramRepositoryInterface $schoolProgramRepository, ReferralRepositoryInterface $referralRepository, UserRepositoryInterface $userRepository, ClientProgramRepositoryInterface $clientProgramRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->followupRepository = $followupRepository;
        $this->corporateRepository = $corporateRepository;
        $this->schoolRepository = $schoolRepository;
        $this->universityRepository = $universityRepository;
        $this->partnerAgreementRepository = $partnerAgreementRepository;
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
        $this->partnerProgramRepository = $partnerProgramRepository;
        $this->schoolProgramRepository = $schoolProgramRepository;
        $this->referralRepository = $referralRepository;
        $this->clientProgramRepository = $clientProgramRepository;
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        // return $this->indexSales($request);
        return $this->indexPartnership($request);
    }

    # sales dashboard

    public function indexSales($request)
    {
        # data at the top of dashboard
        $month = date('Y-m');
        $filter = null;
        if ($request->get('month')) {
            $filter = $month = $request->get('month');
        }

        $totalClientByStatus = [
            'prospective' => $this->clientRepository->getCountTotalClientByStatus(0, $filter), # prospective
            'potential' => $this->clientRepository->getCountTotalClientByStatus(1, $filter), # potential
            'current' => $this->clientRepository->getCountTotalClientByStatus(2, $filter), # current
            'completed' => $this->clientRepository->getCountTotalClientByStatus(3, $filter), # current
            'mentee' => $this->clientRepository->getAllClientByRole('mentee', $filter)->count(),
            'alumni' => $this->clientRepository->getAllClientByRole('alumni', $filter)->count(),
            'parent' => $this->clientRepository->getAllClientByRole('parent', $filter)->count(),
            'teacher_counselor' => $this->clientRepository->getAllClientByRole('Teacher/Counselor', $filter)->count()
        ];
        $followUpReminder = $this->followupRepository->getAllFollowupWithin(7, $filter);
        $menteesBirthday = $this->clientRepository->getMenteesBirthdayMonthly($month);

        # data at the body of dashboard
        $employees = $this->userRepository->getAllUsersByRole('employee');

        # on client program tab
        $cp_filter['qdate'] = date('Y-m');
        if ($request->get('cp-month')) { # format Y-m
            $cp_filter['qdate'] = $request->get('cp-month');
        }

        $dateDetails = [
            'startDate' => $cp_filter['qdate'] . '-01',
            'endDate' => $cp_filter['qdate'] . '-31'
        ];

        # if (admin)
        # then null
        # elif not admin
        # their uuid
        $cp_filter['quuid'] = null;
        if ($request->get('quser')) {
            $cp_filter['quuid'] = $request->get('quser');
        }

        # client program status
        $totalAllClientProgramByStatus = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => null] + $cp_filter);

        # admissions mentoring
        $admissionsMentoring = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => 'Admissions Mentoring'] + $cp_filter);

        # initial consultation
        $initialConsultation = $this->clientProgramRepository->getInitialConsultationInformation($cp_filter);
        $totalInitialConsultation = array_sum($initialConsultation);
        $successProgram = $admissionsMentoring[2];

        $initialAssessmentMaking = $this->clientProgramRepository->getInitialMaking($dateDetails);
        $conversionTimeProgress = $this->clientProgramRepository->getConversionTimeProgress($dateDetails);
        $successPercentage = ($successProgram / $totalInitialConsultation) * 100;
        $allSuccessProgramByMonth = $this->clientProgramRepository->getSuccessProgramByMonth($cp_filter);
        $totalRevenueAdmMentoringByProgramAndMonth = $this->clientProgramRepository->getTotalRevenueByProgramAndMonth(['program' => 'Admissions Mentoring'] + $cp_filter);
        $academicTestPrep = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => 'Academic & Test Preparation'] + $cp_filter);
        $totalRevenueAcadTestPrepByMonth = $this->clientProgramRepository->getTotalRevenueByProgramAndMonth(['program' => 'Academic & Test Preparation'] + $cp_filter);
        $careerExploration = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => 'Career Exploration'] + $cp_filter);
        $totalRevenueCareerExplorationByMonth = $this->clientProgramRepository->getTotalRevenueByProgramAndMonth(['program' => 'Career Exploration'] + $cp_filter);

        $leadSource = $this->clientProgramRepository->getLeadSource($dateDetails);
        $conversionLeads = $this->clientProgramRepository->getConversionLead($dateDetails);

        $adminssionMentoringConvLead = $this->clientProgramRepository->getConversionLead($dateDetails, 'Admissions Mentoring');
        $academicTestPrepConvLead = $this->clientProgramRepository->getConversionLead($dateDetails, 'Academic & Test Preparation');
        $careerExplorationConvLead = $this->clientProgramRepository->getConversionLead($dateDetails, 'Career Exploration');

        # on sales target tab

        # on program comparison tab

        # on client event tab

        return view('pages.dashboard.index')->with(
            [
                'totalClientInformation' => $totalClientByStatus,
                'followUpReminder' => $followUpReminder,
                'menteesBirthday' => $menteesBirthday,
                'filter_bymonth' => $filter,

                # client program tab
                'employees' => $employees,
                'clientProgramGroupByStatus' => $totalAllClientProgramByStatus,
                'admissionsMentoring' => $admissionsMentoring,
                'initialConsultation' => $initialConsultation,
                'totalInitialConsultation' => $totalInitialConsultation,
                'successProgram' => $successProgram,
                'initialAssessmentMaking' => $initialAssessmentMaking,
                'conversionTimeProgress' => $conversionTimeProgress,
                'successPercentage' => $successPercentage,
                'allSuccessProgramByMonth' => $allSuccessProgramByMonth,
                'totalRevenueAdmissionMentoring' => $totalRevenueAdmMentoringByProgramAndMonth,
                'academicTestPrep' => $academicTestPrep,
                'totalRevenueAcadTestPrepByMonth' => $totalRevenueAcadTestPrepByMonth,
                'careerExploration' => $careerExploration,
                'totalRevenueCareerExplorationByMonth' => $totalRevenueCareerExplorationByMonth,
                'leadSource' => $leadSource,
                'conversionLeads' => $conversionLeads,
                'adminssionMentoringConvLead' => $adminssionMentoringConvLead,
                'academicTestPrepConvLead' => $academicTestPrepConvLead,
                'careerExplorationConvLead' => $careerExplorationConvLead,
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
        $partnerPrograms = $this->partnerProgramRepository->getAllPartnerProgramByStatusAndMonth(0, date('Y-m'));


        return view('pages.dashboard.index')->with(
            [
                'totalPartner' => $totalPartner,
                'totalSchool' => $totalSchool,
                'totalUniversity' => $totalUniversity,
                'totalAgreement' => $totalAgreement,
                'newPartner' => $newPartner,
                'newSchool' => $newSchool,
                'newUniversity' => $newUniversity,
                'speakers' => $speakers,
                'partnerPrograms' => $partnerPrograms,
            ]
        );
    }
}
