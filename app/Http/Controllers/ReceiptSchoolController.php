<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceSchRequest;
use App\Http\Requests\StoreReceiptSchRequest;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\InvoiceDetailRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Http\Traits\CreateInvoiceIdTrait;
use App\Models\Invb2b;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class ReceiptSchoolController extends Controller
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
        if ($request->ajax()) {
            return $this->receiptRepository->getAllReceiptSchDataTables();
        }
        return view('pages.receipt.school-program.index');
    }

    public function store(StoreReceiptSchRequest $request)
    {

        $invb2b_num = $request->route('invoice');
        $receipts = $request->only([
            'receipt_amount',
            'receipt_amount_idr',
            'receipt_words',
            'receipt_words_idr',
            'receipt_method',
            'receipt_cheque',
            'select_currency_receipt',
        ]);

        switch ($receipts['select_currency_receipt']) {
            case 'idr':
                unset($receipts['receipt_amount']);
                unset($receipts['receipt_words']);
                break;

            case 'other':
                unset($receipts['receipt_amount_idr']);
                unset($receipts['receipt_words_idr']);
                break;
        }

        $receipts['receipt_cat'] = 'school';

        $invoice = $this->invoiceB2bRepository->getInvoiceB2bById($invb2b_num);
        // return $invoice;
        // exit;
        $schProgId = $invoice->schprog_id;
        $invb2b_id = $invoice->invb2b_id;

        $receipts['invb2b_id'] = $invb2b_id;

        $receipts['receipt_id'] = substr_replace($invb2b_id, 'REC', 5) . substr($invb2b_id, 8, strlen($invb2b_id));


        unset($receipts['select_currency_receipt']);

        DB::beginTransaction();
        try {

            $this->receiptRepository->createReceipt($receipts);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Create receipt failed : ' . $e->getMessage());

            return $e->getMessage();
            exit;
            return Redirect::to('invoice/school-program/' . $schProgId . '/detail/' . $invb2b_num)->withError('Failed to create a new receipt');
        }

        return Redirect::to('invoice/school-program/' . $schProgId . '/detail/' . $invb2b_num)->withSuccess('Receipt successfully created');
    }

    public function show(Request $request)
    {
        $receiptId = $request->route('detail');

        $receiptSch = $this->receiptRepository->getReceiptById($receiptId);


        return view('pages.receipt.school-program.form')->with(
            [

                'receiptSch' => $receiptSch,
                'status' => 'show',
            ]
        );
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
}
