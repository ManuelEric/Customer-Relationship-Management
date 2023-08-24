<?php

namespace App\Http\Controllers;

use App\Actions\FetchClientStatus;
use App\Http\Controllers\Module\AlarmController;
use App\Http\Controllers\Module\DigitalDashboardController;
use App\Http\Controllers\Module\SalesDashboardController;
use App\Http\Controllers\Module\FinanceDashboardController;
use App\Http\Controllers\Module\PartnerDashboardController;
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
use App\Interfaces\AlarmRepositoryInterface;
use App\Interfaces\PartnerProgramRepositoryInterface;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\ReferralRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\InvoiceProgramRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\RefundRepositoryInterface;
use App\Interfaces\ClientLeadTrackingRepositoryInterface;
use App\Interfaces\TargetSignalRepositoryInterface;
use App\Interfaces\TargetTrackingRepositoryInterface;
use App\Interfaces\LeadTargetRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Models\Client;
use App\Models\UserClient;
use App\Repositories\ClientRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\v1\Student as CRMStudent;

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
    public ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository;
    public TargetTrackingRepositoryInterface $targetTrackingRepository;
    public TargetSignalRepositoryInterface $targetSignalRepository;
    public LeadTargetRepositoryInterface $leadTargetRepository;
    public LeadRepositoryInterface $leadRepository;
    public AlarmRepositoryInterface $alarmRepository;

    public function __construct(ClientRepositoryInterface $clientRepository, FollowupRepositoryInterface $followupRepository, CorporateRepositoryInterface $corporateRepository, SchoolRepositoryInterface $schoolRepository, UniversityRepositoryInterface $universityRepository, PartnerAgreementRepositoryInterface $partnerAgreementRepository, AgendaSpeakerRepositoryInterface $agendaSpeakerRepository, PartnerProgramRepositoryInterface $partnerProgramRepository, SchoolProgramRepositoryInterface $schoolProgramRepository, ReferralRepositoryInterface $referralRepository, UserRepositoryInterface $userRepository, ClientProgramRepositoryInterface $clientProgramRepository, InvoiceB2bRepositoryInterface $invoiceB2bRepository, InvoiceProgramRepositoryInterface $invoiceProgramRepository, ReceiptRepositoryInterface $receiptRepository, SalesTargetRepositoryInterface $salesTargetRepository, ProgramRepositoryInterface $programRepository, ClientEventRepositoryInterface $clientEventRepository, EventRepositoryInterface $eventRepository, RefundRepositoryInterface $refundRepository, ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository, TargetTrackingRepositoryInterface $targetTrackingRepository, TargetSignalRepositoryInterface $targetSignalRepository, LeadTargetRepositoryInterface $leadTargetRepository, LeadRepositoryInterface $leadRepository, AlarmRepositoryInterface $alarmRepository)
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
        $this->clientLeadTrackingRepository = $clientLeadTrackingRepository;
        $this->targetTrackingRepository = $targetTrackingRepository;

        $this->invoiceB2bRepository = $invoiceB2bRepository;
        $this->invoiceProgramRepository = $invoiceProgramRepository;
        $this->receiptRepository = $receiptRepository;
        $this->refundRepository = $refundRepository;
        $this->targetSignalRepository = $targetSignalRepository;
        $this->leadTargetRepository = $leadTargetRepository;
        $this->leadRepository = $leadRepository;
        $this->alarmRepository = $alarmRepository;
    }

    public function index(Request $request)
    {

        # will delete soon
        // $new_lead = $this->clientRepository->getNewLeads(false, null);
        // $potential = $this->clientRepository->getPotentialClients(false, null);
        // $existing_mentee = $this->clientRepository->getExistingMentees(false, null);
        // $existing_nonmentee = $this->clientRepository->getExistingNonMentees(false, null);
        // $alumni_mentee = $this->clientRepository->getAlumniMentees(false, false, null);
        // $alumni_nonmentee = $this->clientRepository->getAlumniNonMentees(false, false, null);
        // $parents = $this->clientRepository->getParents(false, null);

        // $new_lead = $new_lead->pluck('id')->toArray();
        // $potential = $potential->pluck('id')->toArray();
        // $merge = array_merge($new_lead, $potential);
        // $existing_mentee = $existing_mentee->pluck('id')->toArray();
        // $merge = array_merge($merge, $existing_mentee);
        // $existing_nonmentee = $existing_nonmentee->pluck('id')->toArray();
        // $merge = array_merge($merge, $existing_nonmentee);
        // $alumni_mentee = $alumni_mentee->pluck('id')->toArray();
        // $merge = array_merge($merge, $alumni_mentee);
        // $alumni_nonmentee = $alumni_nonmentee->pluck('id')->toArray();
        // $merge = array_merge($merge, $alumni_nonmentee);
        // $collect = collect($merge);

        // return $collect->duplicates();

        // $merge_1 = $new_lead->merge($potential);
        // $merge_2 = $merge_1->merge($existing_mentee);
        // $merge_3 = $merge_2->merge($existing_nonmentee);
        // $merge_4 = $merge_3->merge($alumni_mentee);
        // $merge_5 = $merge_4->merge($alumni_nonmentee);        
        // $merge_6 = $merge_5->merge($parents);
        // return $merge_6->map(function($item) {
        //     return [
        //     return [
        //         'id' => $item['id'],
        //         'first_name' => $item['first_name'],
        //         'last_name' => $item['last_name'],
        //     ];
        // })->pluck('id')->toArray(); 
        
        // $data = (new DigitalDashboardController($this))->get($request);
        // return $data;
        // exit;
        $data = (new SalesDashboardController($this))->get($request);
        $data = array_merge($data, (new PartnerDashboardController($this))->get($request));
        $data = array_merge($data, (new FinanceDashboardController($this))->get($request));
        $data = array_merge($data, (new AlarmController($this))->get($request));
        $data = array_merge($data, (new DigitalDashboardController($this))->get($request));

        return view('pages.dashboard.index')->with($data);
    }
}
