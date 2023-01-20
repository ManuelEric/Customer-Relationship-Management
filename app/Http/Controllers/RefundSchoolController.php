<?php

namespace App\Http\Controllers;

use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\InvoiceDetailRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Interfaces\RefundRepositoryInterface;
use App\Http\Traits\CreateInvoiceIdTrait;
use App\Models\Receipt;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use PDF;



class RefundSchoolController extends Controller
{
    use CreateInvoiceIdTrait;
    protected SchoolRepositoryInterface $schoolRepository;
    protected SchoolProgramRepositoryInterface $schoolProgramRepository;
    protected ProgramRepositoryInterface $programRepository;
    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    protected InvoiceDetailRepositoryInterface $invoiceDetailRepository;
    protected ReceiptRepositoryInterface $receiptRepository;
    protected RefundRepositoryInterface $refundRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository, SchoolProgramRepositoryInterface $schoolProgramRepository, ProgramRepositoryInterface $programRepository, InvoiceB2bRepositoryInterface $invoiceB2bRepository, InvoiceDetailRepositoryInterface $invoiceDetailRepository, ReceiptRepositoryInterface $receiptRepository, RefundRepositoryInterface $refundRepository)
    {
        $this->schoolRepository = $schoolRepository;
        $this->schoolProgramRepository = $schoolProgramRepository;
        $this->programRepository = $programRepository;
        $this->invoiceB2bRepository = $invoiceB2bRepository;
        $this->invoiceDetailRepository = $invoiceDetailRepository;
        $this->receiptRepository = $receiptRepository;
        $this->refundRepository = $refundRepository;
    }

    // public function index(Request $request)
    // {
    //     if ($request->ajax()) {
    //         return $this->receiptRepository->getAllReceiptSchDataTables();
    //     }
    //     return view('pages.receipt.school-program.index');
    // }

    public function store(Request $request)
    {
        #initialize
        $identifier = $request->identifier; #invdtl_id

        $invb2b_num = $request->route('invoice');
        $receipts = $request->only([
            'identifier',
            'currency',
            'receipt_amount',
            'receipt_amount_idr',
            'receipt_date',
            'receipt_words',
            'receipt_words_idr',
            'receipt_method',
            'receipt_cheque',
        ]);


        switch ($receipts['currency']) {
            case 'idr':
                unset($receipts['receipt_amount']);
                unset($receipts['receipt_words']);
                break;
        }

        $receipts['receipt_cat'] = 'school';

        $invoice = $this->invoiceB2bRepository->getInvoiceB2bById($invb2b_num);
        $schProgId = $invoice->schprog_id;
        $sch_prog = $this->schoolProgramRepository->getSchoolProgramById($schProgId);

        $invb2b_id = $invoice->invb2b_id;

        # generate receipt id
        $last_id = Receipt::whereMonth('created_at', date('m'))->max(DB::raw('substr(receipt_id, 1, 4)'));

        # Use Trait Create Invoice Id
        $receipt_id = $this->getInvoiceId($last_id, $sch_prog->prog_id);

        $receipts['receipt_id'] = substr_replace($receipt_id, 'REC', 5) . substr($receipt_id, 8, strlen($receipt_id));

        $receipts['invb2b_id'] = $invb2b_id;
        $invoice_payment_method = $invoice->invb2b_pm;

        // return $receipts;
        // exit;

        if ($invoice_payment_method == "Installment")
            $receipts['invdtl_id'] = $identifier;

        # validation nominal
        # to catch if total invoice not equal to total receipt 
        if ($invoice_payment_method == "Full Payment") {

            $total_invoice = $invoice->invb2b_totpriceidr;
            $total_receipt = $request->receipt_amount_idr;
        } elseif ($invoice_payment_method == "Installment") {

            $total_invoice = $invoice->inv_detail()->where('invdtl_id', $identifier)->first()->invdtl_amountidr;
            $total_receipt = $request->receipt_amount_idr;
        }

        if ($total_receipt < $total_invoice)
            return Redirect::back()->withError('Do double check the amount. Make sure the amount on invoice and the amount on receipt is equal');


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
        if ($request->ajax()) {
            $increment_receipt = $request->get('increment_receipt');

            return $this->receiptRepository->getReceiptById($increment_receipt);
        }
    }


    public function destroy(Request $request)
    {
        $receiptId = $request->route('detail');

        DB::beginTransaction();
        try {

            $this->receiptRepository->deleteReceipt($receiptId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete receipt failed : ' . $e->getMessage());

            return Redirect::to('receipt/school-program/' . $receiptId)->withError('Failed to delete receipt');
        }

        return Redirect::to('receipt/school-program')->withSuccess('Receipt successfully deleted');
    }
}
