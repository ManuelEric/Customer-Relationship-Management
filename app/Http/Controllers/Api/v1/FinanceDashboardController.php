<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\InvoiceProgramRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;

class FinanceDashboardController extends Controller
{
    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    protected InvoiceProgramRepositoryInterface $invoiceProgramRepository;
    protected ReceiptRepositoryInterface $receiptRepository;


    public function __construct(InvoiceB2bRepositoryInterface $invoiceB2bRepository, InvoiceProgramRepositoryInterface $invoiceProgramRepository, ReceiptRepositoryInterface $receiptRepository)
    {
        $this->invoiceB2bRepository = $invoiceB2bRepository;
        $this->invoiceProgramRepository = $invoiceProgramRepository;
        $this->receiptRepository = $receiptRepository;
    }


    public function getTotalByMonth(Request $request)
    {
        $monthYear = $request->route('month');

        $totalInvoiceNeededB2b = $this->invoiceB2bRepository->getTotalInvoiceNeeded($monthYear);
        $totalInvoiceNeededB2c = $this->invoiceProgramRepository->getTotalInvoiceNeeded($monthYear);

        $totalInvoiceB2b = $this->invoiceB2bRepository->getTotalInvoice($monthYear);
        $totalInvoiceB2c = $this->invoiceProgramRepository->getTotalInvoice($monthYear);

        $totalReceipt = $this->receiptRepository->getTotalReceipt($monthYear);

        $totalInvoiceNeeded = collect($totalInvoiceNeededB2b)->merge($totalInvoiceNeededB2c)->sum('count_invoice_needed');
        $totalInvoice = collect($totalInvoiceB2b)->merge($totalInvoiceB2c);

        $data = [
            'totalInvoiceNeeded' => $totalInvoiceNeeded,
            'totalInvoice' => $totalInvoice,
            'totalReceipt' => $totalReceipt,
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

        $data = [
            'statusSchoolPrograms' => $this->schoolProgramRepository->getStatusSchoolProgramByMonthly($monthYear),
            'statusPartnerPrograms' => $this->partnerProgramRepository->getStatusPartnerProgramByMonthly($monthYear),
            'referralTypes' => $this->referralRepository->getReferralTypeByMonthly($monthYear),
            'totalPartnerProgram' => $totalPartnerProgram,
            'totalSchoolProgram' => $totalSchoolProgram,
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

        $programComparisonMerge = $this->mergeProgramComparison($schoolProgramMerge, $partnerProgramMerge, $referralMerge);

        $programComparisons = $this->mappingProgramComparison($programComparisonMerge);

        $data = [
            'programComparisons' => $programComparisons,
            'partnerPrograms' => $this->partnerProgramRepository->getPartnerProgramComparison($startYear, $endYear),
            'totalSch' => $this->schoolProgramRepository->getTotalSchoolProgramComparison($startYear, $endYear),
            'totalPartner' => $this->partnerProgramRepository->getTotalPartnerProgramComparison($startYear, $endYear),
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
