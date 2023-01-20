<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceSchRequest;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\InvoiceDetailRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Http\Traits\CreateInvoiceIdTrait;
use App\Models\Invb2b;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use PDF;


class InvoiceSchoolController extends Controller
{
    use CreateInvoiceIdTrait;
    protected SchoolRepositoryInterface $schoolRepository;
    protected SchoolProgramRepositoryInterface $schoolProgramRepository;
    protected ProgramRepositoryInterface $programRepository;
    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    protected InvoiceDetailRepositoryInterface $invoiceDetailRepository;
    protected ReceiptRepositoryInterface $receiptRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository, SchoolProgramRepositoryInterface $schoolProgramRepository, ProgramRepositoryInterface $programRepository, InvoiceB2bRepositoryInterface $invoiceB2bRepository, InvoiceDetailRepositoryInterface $invoiceDetailRepository, ReceiptRepositoryInterface $receiptRepository)
    {
        $this->schoolRepository = $schoolRepository;
        $this->schoolProgramRepository = $schoolProgramRepository;
        $this->programRepository = $programRepository;
        $this->invoiceB2bRepository = $invoiceB2bRepository;
        $this->invoiceDetailRepository = $invoiceDetailRepository;
        $this->receiptRepository = $receiptRepository;
    }

    public function index(Request $request)
    {
        $status = $request->route('status');

        if ($request->ajax()) {
            switch ($status) {
                case 'needed':
                    return $this->invoiceB2bRepository->getAllInvoiceNeededSchDataTables();
                    break;
                case 'list':
                    return $this->invoiceB2bRepository->getAllInvoiceSchDataTables();
                    break;
            }
        }

        return view('pages.invoice.school-program.index')->with(['status' => $status]);
    }

    public function create(Request $request)
    {
        $schProgId = $request->route('sch_prog');

        $schoolProgram = $this->schoolProgramRepository->getSchoolProgramById($schProgId);

        $schoolId = $schoolProgram->sch_id;

        # retrieve school data by id
        $school = $this->schoolRepository->getSchoolById($schoolId);

        return view('pages.invoice.school-program.form')->with(
            [
                'schoolProgram' => $schoolProgram,
                'school' => $school,
                'status' => 'create',
            ]
        );
    }

    public function store(StoreInvoiceSchRequest $request)
    {

        $schProgId = $request->route('sch_prog');
        $invoices = $request->only([
            'select_currency',
            'currency',
            'curs_rate',
            'invb2b_priceidr',
            'invb2b_priceidr_other',
            'invb2b_price',
            'invb2b_totpriceidr',
            'invb2b_totpriceidr_other',
            'invb2b_totprice',
            'invb2b_participants',
            'invb2b_participants_other',
            'invb2b_discidr',
            'invb2b_discidr_other',
            'invb2b_disc',
            'invb2b_wordsidr',
            'invb2b_wordsidr_other',
            'invb2b_words',
            'invb2b_pm',
            'invb2b_date',
            'invb2b_duedate',
            'invb2b_notes',
            'invb2b_tnc',
        ]);

        $installments = $request->only(
            [
                'invdtl_installment',
                'invdtl_duedate',
                'invdtl_percentage',
                'invdtl_installment_other',
                'invdtl_duedate_other',
                'invdtl_percentage_other',
                'invdtl_amount',
                'invdtl_amountidr',
                'invdtl_amountidr_other',
            ]
        );

        switch ($invoices['select_currency']) {
            case 'other':
                $invoices['invb2b_priceidr'] = $invoices['invb2b_priceidr_other'];
                $invoices['invb2b_discidr'] = $invoices['invb2b_discidr_other'];
                $invoices['invb2b_participants'] = $invoices['invb2b_participants_other'];
                $invoices['invb2b_totpriceidr'] = $invoices['invb2b_totpriceidr_other'];
                $invoices['invb2b_wordsidr'] = $invoices['invb2b_wordsidr_other'];
                break;

            case 'idr':
                $invoices['currency'] = 'idr';
                unset($invoices['invb2b_price']);
                unset($invoices['invb2b_disc']);
                unset($invoices['invb2b_totprice']);
                unset($invoices['invb2b_words']);
                // unset($invoices['currency']);
                break;
        }



        unset($invoices['invb2b_participants_other']);
        unset($invoices['invb2b_priceidr_other']);
        unset($invoices['invb2b_discidr_other']);
        unset($invoices['invb2b_totpriceidr_other']);
        unset($invoices['invb2b_wordsidr_other']);


        $now = Carbon::now();
        $thisMonth = $now->month;

        $last_id = Invb2b::whereMonth('created_at', $thisMonth)->max(DB::raw('substr(invb2b_id, 1, 4)'));

        $schoolProgram = $this->schoolProgramRepository->getSchoolProgramById($schProgId);
        $prog_id = $schoolProgram->prog_id;

        // Use Trait Create Invoice Id
        $inv_id = $this->getInvoiceId($last_id, $prog_id);

        $invoices['invb2b_id'] = $inv_id;
        $invoices['schprog_id'] = $schProgId;

        if ($invoices['invb2b_pm'] == 'Installment') {
            $installment = $this->extract_installment($inv_id, $invoices['select_currency'], $installments);
        }
        unset($installments);

        DB::beginTransaction();
        try {

            $this->invoiceB2bRepository->createInvoiceB2b($invoices);
            if ($invoices['invb2b_pm'] == 'Installment') {
                $this->invoiceDetailRepository->createInvoiceDetail($installment);
            }
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Create invoice failed : ' . $e->getMessage());

            return $e->getMessage();
            exit;
            return Redirect::to('invoice/school-program/' . $schProgId . '/detail/create')->withError('Failed to create a new invoice');
        }

        return Redirect::to('invoice/school-program/status/list')->withSuccess('Invoice successfully created');
    }

    public function show(Request $request)
    {
        $schProgId = $request->route('sch_prog');
        $invNum = $request->route('detail');

        $schoolProgram = $this->schoolProgramRepository->getSchoolProgramById($schProgId);

        $schoolId = $schoolProgram->sch_id;

        $school = $this->schoolRepository->getSchoolById($schoolId);

        $invoiceSch = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);

        return view('pages.invoice.school-program.form')->with(
            [
                'schoolProgram' => $schoolProgram,
                'school' => $school,
                'invoiceSch' => $invoiceSch,
                'status' => 'show',
            ]
        );
    }

    public function edit(Request $request)
    {
        $invNum = $request->route('detail');
        $schProgId = $request->route('sch_prog');

        $schoolProgram = $this->schoolProgramRepository->getSchoolProgramById($schProgId);

        $schoolId = $schoolProgram->sch_id;

        $school = $this->schoolRepository->getSchoolById($schoolId);

        $invoiceSch = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);

        return view('pages.invoice.school-program.form')->with(
            [
                'status' => 'edit',
                'schoolProgram' => $schoolProgram,
                'school' => $school,
                'invoiceSch' => $invoiceSch,
            ]
        );
    }

    public function update(StoreInvoiceSchRequest $request)
    {

        $schProgId = $request->route('sch_prog');
        $invNum = $request->route('detail');

        $invoices = $request->only([
            'select_currency',
            'currency',
            'curs_rate',
            'invb2b_priceidr',
            'invb2b_priceidr_other',
            'invb2b_price',
            'invb2b_totpriceidr',
            'invb2b_totpriceidr_other',
            'invb2b_totprice',
            'invb2b_participants',
            'invb2b_participants_other',
            'invb2b_discidr',
            'invb2b_discidr_other',
            'invb2b_disc',
            'invb2b_wordsidr',
            'invb2b_wordsidr_other',
            'invb2b_words',
            'invb2b_pm',
            'invb2b_date',
            'invb2b_duedate',
            'invb2b_notes',
            'invb2b_tnc',
        ]);

        $installments = $request->only(
            [
                'invdtl_installment',
                'invdtl_duedate',
                'invdtl_percentage',
                'invdtl_installment_other',
                'invdtl_duedate_other',
                'invdtl_percentage_other',
                'invdtl_amount',
                'invdtl_amountidr',
                'invdtl_amountidr_other',
            ]
        );

        switch ($invoices['select_currency']) {
            case 'other':
                $invoices['invb2b_priceidr'] = $invoices['invb2b_priceidr_other'];
                $invoices['invb2b_discidr'] = $invoices['invb2b_discidr_other'];
                $invoices['invb2b_participants'] = $invoices['invb2b_participants_other'];
                $invoices['invb2b_totpriceidr'] = $invoices['invb2b_totpriceidr_other'];
                $invoices['invb2b_wordsidr'] = $invoices['invb2b_wordsidr_other'];
                break;

            case 'idr':
                unset($invoices['invb2b_price']);
                unset($invoices['invb2b_disc']);
                unset($invoices['invb2b_totprice']);
                unset($invoices['invb2b_words']);
                unset($invoices['currency']);
                break;
        }

        unset($invoices['invb2b_participants_other']);
        unset($invoices['invb2b_priceidr_other']);
        unset($invoices['invb2b_discidr_other']);
        unset($invoices['invb2b_totpriceidr_other']);
        unset($invoices['invb2b_wordsidr_other']);

        $invoices['schprog_id'] = $schProgId;

        $inv_b2b = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);
        $inv_id = $inv_b2b->invb2b_id;
        if ($invoices['invb2b_pm'] == 'Installment') {
            $NewInstallment = $this->extract_installment($inv_id, $invoices['select_currency'], $installments);
        }
        unset($installments);

        // return $installment;
        // exit;

        DB::beginTransaction();
        try {

            $this->invoiceB2bRepository->updateInvoiceB2b($invNum, $invoices);
            if ($invoices['invb2b_pm'] == 'Installment') {
                $this->invoiceDetailRepository->updateInvoiceDetailByInvB2bId($inv_id, $NewInstallment);
                $this->invoiceDetailRepository->createInvoiceDetail($NewInstallment);
            }
            // exit;
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update invoice failed : ' . $e->getMessage());

            return $e->getMessage();
            exit;
            return Redirect::to('invoice/school-program/' . $schProgId . '/detail/' . $invNum)->withError('Failed to update invoice');
        }

        return Redirect::to('invoice/school-program/' . $schProgId . '/detail/' . $invNum)->withSuccess('Invoice successfully created');
    }

    public function destroy(Request $request)
    {
        $invNum = $request->route('detail');
        $schProgId = $request->route('sch_prog');

        DB::beginTransaction();
        try {

            $this->invoiceB2bRepository->deleteInvoiceB2b($invNum);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete invoice failed : ' . $e->getMessage());

            return Redirect::to('invoice/school-program/' . $schProgId . '/detail/' . $invNum)->withError('Failed to delete invoice');
        }

        return Redirect::to('invoice/school-program/status/list')->withSuccess('Invoice successfully deleted');
    }

    public function export(Request $request)
    {
        $invNum = $request->route('invoice');
        $currency = $request->route('currency');

        $invoiceSch = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);
        $invoice_id = $invoiceSch->invb2b_id;


        return view('pages.invoice.school-program.export.invoice-pdf')->with([
            'invoiceSch' => $invoiceSch,
            'currency' => $currency,
        ]);

        // $companyDetail = [
        //     'name' => env('ALLIN_COMPANY'),
        //     'address' => env('ALLIN_ADDRESS'),
        //     'address_dtl' => env('ALLIN_ADDRESS_DTL'),
        //     'city' => env('ALLIN_CITY')
        // ];

        $pdf = PDF::loadView(
            'pages.invoice.school-program.export.invoice-pdf',
            [
                'invoiceSch' => $invoiceSch,
                'currency' => $currency,
                // 'companyDetail' => $companyDetail
            ]
        );
        return $pdf->download($invoice_id . ".pdf");
    }

    protected function extract_installment($inv_id, $currency,  array $installments)
    {
        if ($currency == 'other') {
            for ($i = 0; $i < count($installments['invdtl_installment_other']); $i++) {
                $installment[] = [
                    'invdtl_installment' => $installments['invdtl_installment_other'][$i],
                    'invdtl_duedate' => $installments['invdtl_duedate_other'][$i],
                    'invdtl_percentage' => $installments['invdtl_percentage_other'][$i],
                    'invdtl_amount' => $installments['invdtl_amount'][$i],
                    'invdtl_amountidr' => $installments['invdtl_amountidr_other'][$i],
                    'invb2b_id' => $inv_id,
                ];
            }
        } elseif ($currency == 'idr') {
            for ($i = 0; $i < count($installments['invdtl_installment']); $i++) {
                $installment[] = [
                    'invdtl_installment' => $installments['invdtl_installment'][$i],
                    'invdtl_duedate' => $installments['invdtl_duedate'][$i],
                    'invdtl_percentage' => $installments['invdtl_percentage'][$i],
                    'invdtl_amountidr' => $installments['invdtl_amountidr'][$i],
                    'invb2b_id' => $inv_id,
                ];
            }
        }

        return $installment;
    }
}
