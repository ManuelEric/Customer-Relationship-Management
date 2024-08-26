<?php

namespace App\Http\Controllers;

use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\PartnerProgramRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\InvoiceDetailRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Interfaces\RefundRepositoryInterface;
use App\Http\Traits\CreateInvoiceIdTrait;
use App\Http\Requests\StoreRefundSchoolRequest;
use App\Models\Receipt;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use PDF;



class RefundPartnerController extends Controller
{
    use CreateInvoiceIdTrait;
    protected CorporateRepositoryInterface $corporateRepository;
    protected PartnerProgramRepositoryInterface $partnerProgramRepository;
    protected ProgramRepositoryInterface $programRepository;
    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    protected InvoiceDetailRepositoryInterface $invoiceDetailRepository;
    protected ReceiptRepositoryInterface $receiptRepository;
    protected RefundRepositoryInterface $refundRepository;

    public function __construct(CorporateRepositoryInterface $corporateRepository, PartnerProgramRepositoryInterface $partnerProgramRepository, ProgramRepositoryInterface $programRepository, InvoiceB2bRepositoryInterface $invoiceB2bRepository, InvoiceDetailRepositoryInterface $invoiceDetailRepository, ReceiptRepositoryInterface $receiptRepository, RefundRepositoryInterface $refundRepository)
    {
        $this->corporateRepository = $corporateRepository;
        $this->partnerProgramRepository = $partnerProgramRepository;
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

    public function store(StoreRefundSchoolRequest $request)
    {
        // TODO: validasi

        $invb2b_num = $request->route('invoice');

        $invoice = $this->invoiceB2bRepository->getInvoiceB2bById($invb2b_num);

        $invb2b_id = $invoice->invb2b_id;

        $partnerprog_id = $invoice->partnerprog_id;

        $refunds = $request->only([
            'total_payment',
            'total_paid',
            'percentage_refund',
            'refund_amount',
            'tax_percentage',
            'tax_amount',
            'total_refunded'
        ]);

        $refunds['invb2b_id'] = $invb2b_id;
        $refunds['status'] = 1;

        $updateInvoice['invb2b_status'] = 2;
        $updateReceipt['receipt_status'] = 2;

        // return $refunds;
        // exit;

        DB::beginTransaction();
        try {

            $this->refundRepository->createRefund($refunds);
            $this->invoiceB2bRepository->updateInvoiceB2b($invb2b_num, $updateInvoice);
            $this->receiptRepository->updateReceiptByInvoiceIdentifier('B2B', $invb2b_id, $updateReceipt);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Create refund failed : ' . $e->getMessage());

            return $e->getMessage();
            exit;
            return Redirect::to('invoice/corporate-program/' . $partnerprog_id . '/detail/' . $invb2b_num)->withError('Failed to create a new refund');
        }

        return Redirect::to('invoice/corporate-program/' . $partnerprog_id . '/detail/' . $invb2b_num)->withSuccess('Refund successfully created');
    }


    public function destroy(Request $request)
    {
        $refundId = $request->route('refund');
        $invb2b_num = $request->route('invoice');

        $invoice = $this->invoiceB2bRepository->getInvoiceB2bById($invb2b_num);

        $invb2b_id = $invoice->invb2b_id;

        $partnerprog_id = $invoice->partnerprog_id;

        $updateInvoice['invb2b_status'] = 1;
        $updateReceipt['receipt_status'] = 1;

        DB::beginTransaction();
        try {

            $this->refundRepository->deleteRefundByRefundId($refundId);
            $this->invoiceB2bRepository->updateInvoiceB2b($invb2b_num, $updateInvoice);
            $this->receiptRepository->updateReceiptByInvoiceIdentifier('B2B', $invb2b_id, $updateReceipt);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete refund failed : ' . $e->getMessage());

            return Redirect::to('invoice/corporate-program/' . $partnerprog_id . '/detail/' . $invb2b_num)->withError('Failed to delete a new refund');
        }

        return Redirect::to('invoice/corporate-program/' . $partnerprog_id . '/detail/' . $invb2b_num)->withSuccess('Refund successfully canceled');
    }
}
