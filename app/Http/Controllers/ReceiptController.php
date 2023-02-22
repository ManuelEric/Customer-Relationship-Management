<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReceiptRequest;
use App\Http\Traits\CreateReceiptIdTrait;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ReceiptAttachmentRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Models\Receipt;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use PDF;

class ReceiptController extends Controller
{
    use CreateReceiptIdTrait;
    private ReceiptRepositoryInterface $receiptRepository;
    private ClientProgramRepositoryInterface $clientProgramRepository;
    private ReceiptAttachmentRepositoryInterface $receiptAttachmentRepository;

    public function __construct(ReceiptRepositoryInterface $receiptRepository, ClientProgramRepositoryInterface $clientProgramRepository, ReceiptAttachmentRepositoryInterface $receiptAttachmentRepository)
    {
        $this->receiptRepository = $receiptRepository;
        $this->clientProgramRepository = $clientProgramRepository;
        $this->receiptAttachmentRepository = $receiptAttachmentRepository;
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

        try {

            $companyDetail = [
                'name' => env('ALLIN_COMPANY'),
                'address' => env('ALLIN_ADDRESS'),
                'address_dtl' => env('ALLIN_ADDRESS_DTL'),
                'city' => env('ALLIN_CITY')
            ];
    
            $pdf = PDF::loadView($view, ['receipt' => $receipt, 'companyDetail' => $companyDetail]);

        } catch (Exception $e) {
            
            Log::info('Export receipt failed: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
       
        return $pdf->download($receipt->receipt_id.".pdf");

        // return view('pages.receipt.client-program.export.receipt-pdf')->with(
        //     [
        //         'receipt' => $receipt,
        //         'companyDetail' => $companyDetail
        //     ]
        // );
    }

    public function upload(Request $request)
    {
        $receipt_id = $request->route('receipt');
        $receipt = $this->receiptRepository->getReceiptById($receipt_id);
        $currency = $request->currency;

        if ($receipt->receiptAttachment()->where('currency', $currency)->where('sign_status', 'not yet')->first())
            return Redirect::back()->withError('You already upload the receipt.');

        $validated = $request->validate([
            'currency' => 'in:idr,other',
            'attachment' => 'required|file|mimes:pdf'
        ]);

        $attachment = $request->file('attachment');
        $file_name = $attachment->getClientOriginalName();
        $file_name = str_replace('/', '_', $receipt->receipt_id) . '_' . ($currency == 'idr' ? $currency : 'other') . '.pdf'; # 0001_REC_JEI_EF_I_23_idr.pdf
        $path = 'public/uploaded_file/receipt/client/';

        DB::beginTransaction();
        try {

             # insert to invoice attachment
             $attachmentDetails = [
                'receipt_id' => $receipt->receipt_id,
                'currency' => $currency,
                'sign_status' => 'not yet',
                'send_to_client' => 'not sent',
                'attachment' => $file_name
            ];

            # generate invoice as a PDF file
            if ($attachment->storeAs($path, $file_name)) {
                $this->receiptAttachmentRepository->createReceiptAttachment($attachmentDetails);
            }
            DB::commit();
            
        } catch (Exception $e) {

            DB::rollBack();
            Log::info('Failed to request sign invoice : ' . $e->getMessage());
            return Redirect::back()->withError('Failed to upload receipt. Please try again.');

        }

        return Redirect::to('receipt/client-program/'.$receipt_id)->withSuccess('Receipt has been uploaded.');

    }

    public function requestSign(Request $request)
    {
        $receipt_id = $request->route('receipt');
        $receipt = $this->receiptRepository->getReceiptById($receipt_id);
        $invoice_id = $receipt->invoiceProgram->inv_id;

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

        $data['email'] = env('DIRECTOR_EMAIL');
        $data['recipient'] = env('DIRECTOR_NAME');
        $data['title'] = "Request Sign of Receipt Number : " . $receipt->receipt_id;
        $data['param'] = [
            'receipt' => $receipt,
            'currency' => $type
        ];
        try {
            
            # validate 
            # if the receipt has already requested to be signed
            
            if ($this->receiptAttachmentRepository->getReceiptAttachmentByReceiptId($receipt->receipt_id, $type)) {

                $file_name = str_replace('/', '_', $receipt->receipt_id);
                $pdf = PDF::loadView($view, ['receipt' => $receipt, 'companyDetail' => $companyDetail]);

                Mail::send('pages.receipt.client-program.mail.view', $data, function ($message) use ($data, $pdf, $receipt) {
                    $message->to($data['email'], $data['recipient'])
                        ->subject($data['title'])
                        ->attachData($pdf->output(), $receipt->receipt_id . '.pdf');
                });

                return response()->json(['message' => 'Receipt has already been requested to be signed.'], 500);
            }
            
            # generate receipt as a PDF file
            $file_name = str_replace('/', '_', $receipt->receipt_id).'_'.$type;
            $pdf = PDF::loadView($view, ['receipt' => $receipt, 'companyDetail' => $companyDetail]);
            Storage::put('public/uploaded_file/receipt/client/'.$file_name.'.pdf', $pdf->output());
            
            # insert to receipt attachment
            $attachmentDetails = [
                'receipt_id' => $receipt->receipt_id,
                'currency' => $type,
                'sign_status' => 'not yet',
                'send_to_client' => 'not sent',
                'attachment' => $file_name.'.pdf'
            ];
            $this->receiptAttachmentRepository->createReceiptAttachment($attachmentDetails);

            # send email to related person that has authority to give a signature
            Mail::send('pages.receipt.client-program.mail.view', $data, function ($message) use ($data, $pdf, $invoice_id) {
                $message->to($data['email'], $data['recipient'])
                    ->subject($data['title'])
                    ->attachData($pdf->output(), $invoice_id . '.pdf');
            });
            
        } catch (Exception $e) {

            Log::info('Failed to request sign receipt : ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }

        return response()->json(['message' => 'Receipt sent successfully.']);
    }

    public function preview(Request $request)
    {
        $receipt_id = $request->route('receipt');
        $currency = $request->route('currency');

        if (!$receipt = $this->receiptRepository->getReceiptById($receipt_id))
            abort(404);
        

        $attachment = $this->receiptAttachmentRepository->getReceiptAttachmentByReceiptId($receipt->receipt_id, $currency);

        return view('pages.receipt.sign-pdf')->with(
            [
                'receipt' => $receipt,
                'attachment' => $attachment
            ]
        );
    }

    public function uploadSigned(Request $request)
    {
        $pdfFile = $request->file('pdfFile');
        $name = $request->file('pdfFile')->getClientOriginalName();

        $receipt_id = $request->route('receipt');
        $receipt = $this->receiptRepository->getReceiptById($receipt_id);
        $currency = $request->route('currency');

        $attachment = $this->receiptAttachmentRepository->getReceiptAttachmentByReceiptId($receipt->receipt_id, $currency);

        $newDetails = [
            'sign_status' => 'signed',
            'approve_date' => Carbon::now()
        ];

        DB::beginTransaction();
        try {

            $this->receiptAttachmentRepository->updateReceiptAttachment($attachment->id, $newDetails);
            if (!$pdfFile->storeAs('public/uploaded_file/receipt/client/', $name))
                throw new Exception('Failed to store signed receipt file');

            DB::commit();

        } catch (Exception $e) {

            Log::error('Failed to update status after being signed : ' . $e->getMessage());
            return response()->json(['status' => 'success', 'message' => 'Failed to update'], 500);

        }

        return response()->json(['status' => 'success', 'message' => 'Receipt signed successfully']);
    }
}
