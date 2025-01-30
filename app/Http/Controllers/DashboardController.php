<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Module\AlarmController;
use App\Http\Controllers\Module\DigitalDashboardController;
use App\Http\Controllers\Module\SalesDashboardController;
use App\Http\Controllers\Module\FinanceDashboardController;
use App\Http\Controllers\Module\PartnerDashboardController;
use App\Http\Requests\DashboardRequest;
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
use App\Services\Dashboard\DashboardService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
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

    public function index(
        DashboardRequest $request,
        DashboardService $dashboardService,
        )
    {
        # initiate default variables
        $sales = $partnership = $finance = $alarm = $digital = $data = [];
        $time_stored_in_second = 60; // cache requirements
        // Cache::flush();

        # filter for sales dashboard
        $filter = $request->safe()->only(['qdate', 'start', 'end', 'quuid', 'program_id', 'qparam_year1', 'qparam_year2', 'qyear']);
        $division = $request->route('division');

        switch ($division) {
            case 'sales':
                /**
                 * sales data dashboard
                 */
                if (!Cache::has('sales-data-dashboard')) {
                    $sales = $dashboardService->snSalesDashboard($filter);
                    // $sales = (new SalesDashboardController($this))->get($request);
                    Cache::remember('sales-data-dashboard', $time_stored_in_second, function () use ($sales) {
                        return $sales;
                    });
                }
                $sales = Cache::get('sales-data-dashboard');
                break;

            case 'partnership':
                /**
                 * partnership data dashboard
                 */
                if (!Cache::has('partnership-data-dashboard')) {
                    $partnership = $dashboardService->snPartnershipDashboard();
                    // $partnership = (new PartnerDashboardController($this))->get($request);
                    Cache::remember('partnership-data-dashboard', $time_stored_in_second, function () use ($partnership) {
                        return $partnership;
                    });
                }
                $partnership = Cache::get('partnership-data-dashboard');
                break;

            case 'digital':
                /**
                 * digital data dashboard
                 */
                if (!Cache::has('digital-data-dashboard')) {
                    $digital = $dashboardService->snDigitalDashboard();
                    // $digital = (new DigitalDashboardController($this))->get($request);
                    Cache::remember('digital-data-dashboard', $time_stored_in_second, function () use ($digital) {
                        return $digital;
                    });
                }
                $digital = Cache::get('digital-data-dashboard');
                break;

            case 'finance':
                /**
                 * finance data dashboard
                 */
                if (!Cache::has('finance-data-dashboard')) {
                    $finance = $dashboardService->snFinanceDashboard();
                    // $finance = (new FinanceDashboardController($this))->get($request);
                    Cache::remember('finance-data-dashboard', $time_stored_in_second, function () use ($finance) {
                        return $finance;
                    });
                }
                $finance = Cache::get('finance-data-dashboard');
                break;
        }
        
        /**
         * alarm data dashboard
         */
        if (!Cache::has('alarm-data-dashboard')) {
            $alarm = (new AlarmController($this))->get($request);
            Cache::remember('alarm-data-dashboard', $time_stored_in_second, function () use ($alarm) {
                return $alarm;
            });
        }
        $alarm = Cache::get('alarm-data-dashboard');


        # combine data from each division
        $data = array_merge($sales, $partnership, $finance, $alarm, $digital);
        return view('pages.dashboard.index')->with($data);
    }

    public function fnAjaxDataTablesOutstandingPayment()
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
