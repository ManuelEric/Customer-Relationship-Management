<?php

namespace App\Http\Controllers;

use App\Actions\FetchClientStatus;
use App\DataTables\OutstandingDataTable;
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
use App\Interfaces\InvoicesRepositoryInterface;
use App\Interfaces\TargetSignalRepositoryInterface;
use App\Interfaces\TargetTrackingRepositoryInterface;
use App\Interfaces\LeadTargetRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Models\Client;
use App\Models\User;
use App\Models\UserClient;
use App\Repositories\ClientRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\v1\Student as CRMStudent;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Passport;
use Mostafaznv\LaraCache\Facades\LaraCache;

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
    public InvoicesRepositoryInterface $invoicesRepository;

    public function __construct(ClientRepositoryInterface $clientRepository, FollowupRepositoryInterface $followupRepository, CorporateRepositoryInterface $corporateRepository, SchoolRepositoryInterface $schoolRepository, UniversityRepositoryInterface $universityRepository, PartnerAgreementRepositoryInterface $partnerAgreementRepository, AgendaSpeakerRepositoryInterface $agendaSpeakerRepository, PartnerProgramRepositoryInterface $partnerProgramRepository, SchoolProgramRepositoryInterface $schoolProgramRepository, ReferralRepositoryInterface $referralRepository, UserRepositoryInterface $userRepository, ClientProgramRepositoryInterface $clientProgramRepository, InvoiceB2bRepositoryInterface $invoiceB2bRepository, InvoiceProgramRepositoryInterface $invoiceProgramRepository, ReceiptRepositoryInterface $receiptRepository, SalesTargetRepositoryInterface $salesTargetRepository, ProgramRepositoryInterface $programRepository, ClientEventRepositoryInterface $clientEventRepository, EventRepositoryInterface $eventRepository, RefundRepositoryInterface $refundRepository, ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository, TargetTrackingRepositoryInterface $targetTrackingRepository, TargetSignalRepositoryInterface $targetSignalRepository, LeadTargetRepositoryInterface $leadTargetRepository, LeadRepositoryInterface $leadRepository, AlarmRepositoryInterface $alarmRepository, InvoicesRepositoryInterface $invoicesRepository)
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
        $this->invoicesRepository = $invoicesRepository;
    }

    public function index(Request $request)
    {
        // Cache::flush();
        $data = array();
        $timeStoredInSecond = 60;

        if (!Cache::has('sales-data-dashboard')) {
            $sales = (new SalesDashboardController($this))->get($request);
            Cache::remember('sales-data-dashboard', $timeStoredInSecond, function () use ($sales) {
                return $sales;
            });
        }
        
        $sales = Cache::get('sales-data-dashboard');

        if (!Cache::has('partnership-data-dashboard')) {
            $partnership = (new PartnerDashboardController($this))->get($request);
            Cache::remember('partnership-data-dashboard', $timeStoredInSecond, function () use ($partnership) {
                return $partnership;
            });
        }

        $partnership = Cache::get('partnership-data-dashboard');
        
        if (!Cache::has('finance-data-dashboard')) {
            $finance = (new FinanceDashboardController($this))->get($request);
            Cache::remember('finance-data-dashboard', $timeStoredInSecond, function () use ($finance) {
                return $finance;
            });
        }

        $finance = Cache::get('finance-data-dashboard');
        
        if (!Cache::has('alarm-data-dashboard')) {
            $alarm = (new AlarmController($this))->get($request);
            Cache::remember('alarm-data-dashboard', $timeStoredInSecond, function () use ($alarm) {
                return $alarm;
            });
        }

        $alarm = Cache::get('alarm-data-dashboard');

        if (!Cache::has('digital-data-dashboard')) {
            $digital = (new DigitalDashboardController($this))->get($request);
            Cache::remember('digital-data-dashboard', $timeStoredInSecond, function () use ($digital) {
                return $digital;
            });
        }

        $digital = Cache::get('digital-data-dashboard');

        $data = array_merge($sales, $partnership, $finance, $alarm, $digital);


        return view('pages.dashboard.index')->with($data);
    }

    public function ajaxDataTablesOutstandingPayment()
    {
        return $this->invoicesRepository->getOustandingPaymentDataTables(date('Y-m'));
    }

    public function listOustandingPayments(Request $request)
    {
        $search = $request->get('q') ?? null;

        try {
            $listOutstanding = $this->invoicesRepository->getOustandingPaymentPaginate(date('Y-m'), $search);
        } catch (Exception $e) {
            Log::error('Failed to get list outstanding payment ' . $e->getMessage() . ' | Line: ' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get list outstanding payment'
            ], 500);
        }

        return $listOutstanding;
    }
}
