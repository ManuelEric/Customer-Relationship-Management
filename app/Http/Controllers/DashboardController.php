<?php

namespace App\Http\Controllers;

use App\Actions\FetchClientStatus;
use App\Http\Controllers\Module\SalesDashboardController;
use App\Http\Controllers\Module\FinanceDashboardController;
use App\Http\Controllers\Module\testController;
use App\Http\Traits\Modules\GetClientStatusTrait;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\FollowupRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SalesTargetRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\PartnerAgreementRepositoryInterface;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
use App\Interfaces\PartnerProgramRepositoryInterface;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\ReferralRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\InvoiceProgramRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\RefundRepositoryInterface;
use App\Repositories\ClientRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DashboardController extends SalesDashboardController
{

    use GetClientStatusTrait;
    public ClientRepositoryInterface $clientRepository;
    public FollowupRepositoryInterface $followupRepository;
    public CorporateRepositoryInterface $corporateRepository;
    public SchoolRepositoryInterface $schoolRepository;
    public UniversityRepositoryInterface $universityRepository;
    public PartnerAgreementRepositoryInterface $partnerAgreementRepository;
    public AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;
    public PartnerProgramRepositoryInterface $partnerProgramRepository;
    public SchoolProgramRepositoryInterface $schoolProgramRepository;
    public ReferralRepositoryInterface $referralRepository;
    public ClientProgramRepositoryInterface $clientProgramRepository;
    public UserRepositoryInterface $userRepository;
    public SalesTargetRepositoryInterface $salesTargetRepository;
    public ProgramRepositoryInterface $programRepository;
    public ClientEventRepositoryInterface $clientEventRepository;
    public EventRepositoryInterface $eventRepository;
    public InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    public InvoiceProgramRepositoryInterface $invoiceProgramRepository;
    public ReceiptRepositoryInterface $receiptRepository;
    public RefundRepositoryInterface $refundRepository;

    public function __construct(ClientRepositoryInterface $clientRepository, FollowupRepositoryInterface $followupRepository, CorporateRepositoryInterface $corporateRepository, SchoolRepositoryInterface $schoolRepository, UniversityRepositoryInterface $universityRepository, PartnerAgreementRepositoryInterface $partnerAgreementRepository, AgendaSpeakerRepositoryInterface $agendaSpeakerRepository, PartnerProgramRepositoryInterface $partnerProgramRepository, SchoolProgramRepositoryInterface $schoolProgramRepository, ReferralRepositoryInterface $referralRepository, UserRepositoryInterface $userRepository, ClientProgramRepositoryInterface $clientProgramRepository, InvoiceB2bRepositoryInterface $invoiceB2bRepository, InvoiceProgramRepositoryInterface $invoiceProgramRepository, ReceiptRepositoryInterface $receiptRepository, SalesTargetRepositoryInterface $salesTargetRepository, ProgramRepositoryInterface $programRepository, ClientEventRepositoryInterface $clientEventRepository, EventRepositoryInterface $eventRepository, RefundRepositoryInterface $refundRepository)
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
        $this->salesTargetRepository = $salesTargetRepository;
        $this->programRepository = $programRepository;
        $this->clientEventRepository = $clientEventRepository;
        $this->eventRepository = $eventRepository;

        $this->invoiceB2bRepository = $invoiceB2bRepository;
        $this->invoiceProgramRepository = $invoiceProgramRepository;
        $this->receiptRepository = $receiptRepository;
        $this->refundRepository = $refundRepository;
    }

    public function index(Request $request)
    {
        $data = (new SalesDashboardController($this))->get($request);
        $data = array_merge($data, $this->indexPartnership($request));
        $data = array_merge($data, (new FinanceDashboardController($this))->get($request));

        return view('pages.dashboard.index')->with($data);
    }

    public function indexPartnership($request)
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
            'conversion_lead_of_event' => $conversion_lead_of_event
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
