<?php

namespace App\Actions\Report\Partnership;

use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\PartnerProgramRepositoryInterface;
use App\Interfaces\ReferralRepositoryInterface;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\SchoolVisitRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;

class PartnershipReportAction
{
    private PartnerProgramRepositoryInterface $partnerProgramRepository;
    private SchoolProgramRepositoryInterface $schoolProgramRepository;
    private SchoolRepositoryInterface $schoolRepository;
    private SchoolVisitRepositoryInterface $schoolVisitRepository;
    private CorporateRepositoryInterface $corporateRepository;
    private UniversityRepositoryInterface $universityRepository;
    private ReferralRepositoryInterface $referralRepository;

    public function __construct(
        PartnerProgramRepositoryInterface $partnerProgramRepository,
        SchoolProgramRepositoryInterface $schoolProgramRepository,
        SchoolRepositoryInterface $schoolRepository,
        SchoolVisitRepositoryInterface $schoolVisitRepository,
        CorporateRepositoryInterface $corporateRepository,
        UniversityRepositoryInterface $universityRepository,
        ReferralRepositoryInterface $referralRepository,
    )
    {
        $this->partnerProgramRepository = $partnerProgramRepository;
        $this->schoolProgramRepository = $schoolProgramRepository;
        $this->schoolRepository = $schoolRepository;
        $this->schoolVisitRepository = $schoolVisitRepository;
        $this->corporateRepository = $corporateRepository;
        $this->universityRepository = $universityRepository;
        $this->referralRepository = $referralRepository;
    }

    public function execute(Array $incoming_requests): Array
    {
        $start_date = $incoming_requests['start_date'];
        $end_date = $incoming_requests['end_date'];

        $partner_programs = $this->partnerProgramRepository->getReportPartnerPrograms($start_date, $end_date);
        $school_programs = $this->schoolProgramRepository->getReportSchoolPrograms($start_date, $end_date);
        $schools = $this->schoolRepository->getReportNewSchool($start_date, $end_date);
        $school_visits = $this->schoolVisitRepository->getReportSchoolVisit($start_date, $end_date);
        $partners = $this->corporateRepository->getReportNewPartner($start_date, $end_date);
        $universities = $this->universityRepository->getReportNewUniversity($start_date, $end_date);
        $referrals_in = $this->referralRepository->getReportNewReferral($start_date, $end_date, 'In');
        $referrals_out = $this->referralRepository->getReportNewReferral($start_date, $end_date, 'Out');

        return compact(
            'partner_programs',
            'school_programs',
            'schools',
            'school_visits',
            'partners',
            'universities',
            'referrals_in',
            'referrals_out',
            'start_date',
            'end_date'
        );
    }
}