<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReceiptRequest;
use App\Http\Traits\CreateReceiptIdTrait;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Models\Receipt;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use PDF;

class ReceiptController extends Controller
{
    use CreateReceiptIdTrait;
    private ReceiptRepositoryInterface $receiptRepository;
    private ClientProgramRepositoryInterface $clientProgramRepository;

    public function __construct(ReceiptRepositoryInterface $receiptRepository, ClientProgramRepositoryInterface $clientProgramRepository)
    {
        $this->receiptRepository = $receiptRepository;
        $this->clientProgramRepository = $clientProgramRepository;
    }
    
    public function index(Request $request)
    {
        if ($request->ajax()) 
            return $this->receiptRepository->getAllReceiptByStatusDataTables();
        
        return view('pages.receipt.client-program.index');
    }

    public function show(Request $request)
    {
        $receipt_id = $request->route('receipt');
        $receipt = $this->receiptRepository->getReceiptById($receipt_id);

        return view('pages.receipt.client-program.form')->with(
            [
                'client_prog' => $receipt->invoiceProgram->clientProg,
                'receipt' => $receipt
            ]
        );
    }

    public function store(StoreReceiptRequest $request)
    {
        #initialize
        $identifier = $request->identifier; #invdtl_id
        $paymethod = $request->paymethod;

        $receiptDetails = $request->only([
            'currency',
            'receipt_amount',
            'receipt_amount_idr',
            'receipt_date',
            'receipt_words',
            'receipt_words_idr',
            'receipt_method',
            'receipt_cheque'
        ]);
        $receiptDetails['receipt_cat'] = 'student';

        $client_prog = $this->clientProgramRepository->getClientProgramById($request->clientprog_id);
        $invoice = $client_prog->invoice()->first();

        # generate receipt id
        $last_id = Receipt::whereMonth('created_at', date('m'))->max(DB::raw('substr(receipt_id, 1, 4)'));
    
        # Use Trait Create Invoice Id
        $receiptDetails['receipt_id'] = $this->getInvoiceId($last_id, $client_prog->prog_id);
        
        $receiptDetails['inv_id'] = $invoice->inv_id;
        $invoice_payment_method = $invoice->inv_paymentmethod;
        if ($invoice_payment_method == "Installment")
            $receiptDetails['invdtl_id'] = $identifier;

        # validation nominal
        # to catch if total invoice not equal to total receipt 
        if ($invoice_payment_method == "Full Payment") {

            $total_invoice = $invoice->inv_totalprice_idr;
            $total_receipt = $request->receipt_amount_idr;

        } elseif ($invoice_payment_method == "Installment") {

            $total_invoice = $invoice->invoiceDetail()->where('invdtl_id', $identifier)->first()->invdtl_amountidr;
            $total_receipt = $request->receipt_amount_idr;
        }

        if ($total_receipt < $total_invoice)
            return Redirect::back()->withError('Do double check the amount. Make sure the amount on invoice and the amount on receipt is equal');

        // return $receiptDetails;
        DB::beginTransaction();
        try {

            $this->receiptRepository->createReceipt($receiptDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store receipt failed : ' . $e->getMessage());
            return Redirect::back()->withError('Failed to create receipt');

        }

        return Redirect::to('invoice/client-program/' . $request->clientprog_id)->withSuccess('A receipt has been made');
    }

    public function destroy(Request $request)
    {
        $receiptId = $request->route('receipt');

        DB::beginTransaction();
        try {

            $this->receiptRepository->deleteReceipt($receiptId);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete receipt failed : ' . $e->getMessage());
            return Redirect::back()->withError('Failed to delete receipt');

        }

        return Redirect::to('receipt/client-program?s=list')->withSuccess('Receipt has been deleted');
    }

    public function export(Request $request)
    {
        $receiptId = $request->route('receipt');
        $receipt = $this->receiptRepository->getReceiptById($receiptId);

        $type = $request->get('type');

        if ($type == "idr")
            $view = 'pages.receipt.client-program.export.receipt-pdf';
        else
            $view = 'pages.receipt.client-program.export.receipt-pdf-foreign';

        $companyDetail = [
            'name' => env('ALLIN_COMPANY'),
            'address' => env('ALLIN_ADDRESS'),
            'address_dtl' => env('ALLIN_ADDRESS_DTL'),
            'city' => env('ALLIN_CITY')
        ];

        $pdf = PDF::loadView($view, ['receipt' => $receipt, 'companyDetail' => $companyDetail]);
        return $pdf->download($receipt->receipt_id.".pdf");

        // return view('pages.receipt.client-program.export.receipt-pdf')->with(
        //     [
        //         'receipt' => $receipt,
        //         'companyDetail' => $companyDetail
        //     ]
        // );
    }
}
