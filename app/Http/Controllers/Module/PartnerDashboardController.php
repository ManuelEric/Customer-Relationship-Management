<?php

namespace App\Http\Controllers\Module;

use App\Http\Controllers\Controller;

class PartnerDashboardController extends Controller
{
    public function __construct($repositories)
    {
        $this->corporateRepository = $repositories->corporateRepository;
        $this->schoolRepository = $repositories->schoolRepository;
        $this->universityRepository = $repositories->universityRepository;
        $this->partnerAgreementRepository = $repositories->partnerAgreementRepository;
        $this->agendaSpeakerRepository = $repositories->agendaSpeakerRepository;
        $this->partnerProgramRepository = $repositories->partnerProgramRepository;
        $this->schoolProgramRepository = $repositories->schoolProgramRepository;
        $this->referralRepository = $repositories->referralRepository;
        $this->programRepository = $repositories->programRepository;
        $this->clientEventRepository = $repositories->clientEventRepository;
        $this->eventRepository = $repositories->eventRepository;
    }

    public function get($request)
    {
        $date = null;

        $totalPartner = $this->corporateRepository->getAllCorporate()->count();
        $totalSchool = $this->schoolRepository->getAllSchools()->count();
        $totalUniversity = $this->universityRepository->getAllUniversities()->count();
        $totalAgreement = $this->partnerAgreementRepository->getPartnerAgreementByMonthly(date('Y-m'), 'all');
        $newPartner = $this->corporateRepository->getCorporateByMonthly(date('Y-m'), 'monthly');
        $newSchool = $this->schoolRepository->getSchoolByMonthly(date('Y-m'), 'monthly');
        $newUniversity = $this->universityRepository->getUniversityByMonthly(date('Y-m'), 'monthly');

        // Tab Agenda
        $speakers = $this->agendaSpeakerRepository->getAllSpeakerDashboard('all', $date);
        $speakerToday = $this->agendaSpeakerRepository->getAllSpeakerDashboard('byDate', date('Y-m-d'));

        // Tab Partnership
        $partnerPrograms = $this->partnerProgramRepository->getAllPartnerProgramByStatusAndMonth(0, date('Y-m')); # display default partnership program (status pending)

        // Tab Program Comparison
        $startYear = date('Y') - 1;
        $endYear = date('Y');

        $schoolProgramComparison = $this->schoolProgramRepository->getSchoolProgramComparison($startYear, $endYear);
        $partnerProgramComparison = $this->partnerProgramRepository->getPartnerProgramComparison($startYear, $endYear);
        $referralComparison = $this->referralRepository->getReferralComparison($startYear, $endYear);

        $programComparisonMerge = $this->mergeProgramComparison($schoolProgramComparison, $partnerProgramComparison, $referralComparison);

        $programComparisons = $this->mappingProgramComparison($programComparisonMerge);

        # on client event tab
        $cp_filter['qyear'] = 'current';
        $events = [];
        if ($this->eventRepository->getEventsWithParticipants($cp_filter)->count() > 0) {
            $events = $this->eventRepository->getEventsWithParticipants($cp_filter);
            $cp_filter['eventId'] = $events[0]->event_id;
        }

        $conversion_lead_of_event = $this->clientEventRepository->getConversionLead($cp_filter);

        $uncompleteSchools = $this->schoolRepository->getUncompeteSchools();
        return [
            'totalPartner' => $totalPartner,
            'totalSchool' => $totalSchool,
            'totalUniversity' => $totalUniversity,
            'totalAgreement' => $totalAgreement,
            'newPartner' => $newPartner,
            'newSchool' => $newSchool,
            'newUniversity' => $newUniversity,
            'speakers' => $speakers,
            'speakerToday' => $speakerToday,
            'partnerPrograms' => $partnerPrograms,
            'programComparisons' => $programComparisons,
            # client event tab
            'events' => $events,
            'conversion_lead_of_event' => $conversion_lead_of_event,
            'totalUncompleteSchool' => $uncompleteSchools->count()
        ];
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
