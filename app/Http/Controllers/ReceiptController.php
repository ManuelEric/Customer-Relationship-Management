<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReceiptRequest;
use App\Http\Traits\CreateReceiptIdTrait;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ReceiptAttachmentRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
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
    private ClientRepositoryInterface $clientRepository;

    public function __construct(ReceiptRepositoryInterface $receiptRepository, ClientProgramRepositoryInterface $clientProgramRepository, ReceiptAttachmentRepositoryInterface $receiptAttachmentRepository, ClientRepositoryInterface $clientRepository)
    {
        $this->receiptRepository = $receiptRepository;
        $this->clientProgramRepository = $clientProgramRepository;
        $this->receiptAttachmentRepository = $receiptAttachmentRepository;
        $this->clientRepository = $clientRepository;
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
            'rec_currency',
            'receipt_amount',
            'receipt_amount_idr',
            'receipt_date',
            'receipt_words',
            'receipt_words_idr',
            'receipt_method',
            'receipt_cheque'
        ]);
        $receiptDetails['receipt_cat'] = 'student';

        $receiptDetails['created_at'] = $receiptDetails['receipt_date'];
        $receiptDetails['updated_at'] = Carbon::now();

        $client_prog = $this->clientProgramRepository->getClientProgramById($request->clientprog_id);
        $invoice = $client_prog->invoice()->first();

        # generate receipt id
        $last_id = Receipt::whereMonth('created_at', isset($request->receipt_date) ? date('m', strtotime($request->receipt_date)) : date('m'))->whereYear('created_at', isset($request->receipt_date) ? date('Y', strtotime($request->receipt_date)) : date('Y'))->max(DB::raw('substr(receipt_id, 1, 4)'));

        # Use Trait Create Invoice Id
        $receiptDetails['receipt_id'] = $this->getLatestReceiptId($last_id, $client_prog->prog_id, $receiptDetails);

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

    public function export(Request $request) # print function
    {
        $receiptId = $request->route('receipt');
        $receipt = $this->receiptRepository->getReceiptById($receiptId);

        $type = $request->get('type');

        $file_name = str_replace('/', '-', $receipt->receipt_id) . '-' . ($type == 'idr' ? $type : 'other') . '.pdf';

        if ($type == "idr")
            $view = 'pages.receipt.client-program.export.receipt-pdf';
        else
            $view = 'pages.receipt.client-program.export.receipt-pdf-foreign';

        try {

            # update download status on tbl_receipt
            if ($type == "idr")
                $receipt->download_idr = 1;
            else
                $receipt->download_other = 1;

            $receipt->save();

            $companyDetail = [
                'name' => env('ALLIN_COMPANY'),
                'address' => env('ALLIN_ADDRESS'),
                'address_dtl' => env('ALLIN_ADDRESS_DTL'),
                'city' => env('ALLIN_CITY')
            ];
            $pdf = PDF::loadView($view, ['receipt' => $receipt, 'companyDetail' => $companyDetail]);
            return $pdf->download($file_name . ".pdf");
        } catch (Exception $e) {

            Log::info('Export receipt failed: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
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
        $file_name = str_replace('/', '-', $receipt->receipt_id) . '-' . ($currency == 'idr' ? $currency : 'other') . '.pdf'; # 0001_REC_JEI_EF_I_23_idr.pdf
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

        return Redirect::to('receipt/client-program/' . $receipt_id)->withSuccess('Receipt has been uploaded.');
    }

    public function requestSign(Request $request)
    {
        $receipt_id = $request->route('receipt');
        $receipt = $this->receiptRepository->getReceiptById($receipt_id);
        $invoice_id = $receipt->invoiceProgram->inv_id;

        $type = $request->get('type');
        $to = $request->get('to');
        $name = $request->get('name');

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

        $data['email'] = $to;
        $data['recipient'] = $name;
        $data['title'] = "Request Sign of Receipt Number : " . $receipt->receipt_id;
        $data['param'] = [
            'receipt' => $receipt,
            'currency' => $type,
            'fullname' => $receipt->invoiceProgram->clientprog->client->full_name,
            'program_name' => $receipt->invoiceProgram->clientprog->program->program_name,
            'receipt_date' => date('d F Y', strtotime($receipt->created_at))
        ];

        DB::beginTransaction();
        try {

            # update request status on receipt attachment
            $attachment = $receipt->receiptAttachment()->where('currency', $type)->first();
            $attachment->recipient = $to;
            $attachment->request_status = 'requested';
            $attachment->save();

            $file_name = str_replace('/', '_', $receipt->receipt_id);
            $pdf = PDF::loadView($view, ['receipt' => $receipt, 'companyDetail' => $companyDetail]);

            Mail::send('pages.receipt.client-program.mail.view', $data, function ($message) use ($data, $pdf, $receipt) {
                $message->to($data['email'], $data['recipient'])
                    ->subject($data['title'])
                    ->attachData($pdf->output(), $receipt->receipt_id . '.pdf');
            });
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::info('Failed to request sign receipt : ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }

        return response()->json(['message' => 'Receipt sent successfully.']);
    }

    public function print(Request $request)
    {
        $receipt_id = $request->route('receipt');
        $currency = $request->route('currency');

        if (!$receipt = $this->receiptRepository->getReceiptById($receipt_id))
            abort(404);


        $attachment = $this->receiptAttachmentRepository->getReceiptAttachmentByReceiptId($receipt->receipt_id, $currency);

        return view('pages.receipt.view-pdf')->with(
            [
                'receipt' => $receipt,
                'attachment' => $attachment
            ]
        );
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

            $data['title'] = 'Receipt No. ' . $receipt->receipt_id . ' has been signed';
            $data['receipt_id'] = $receipt->receipt_id;

            # send mail when document has been signed
            Mail::send('pages.receipt.client-program.mail.signed', $data, function ($message) use ($data, $name) {
                $message->to(env('FINANCE_CC'), env('FINANCE_NAME'))
                    ->subject($data['title'])
                    ->attach(storage_path('app/public/uploaded_file/receipt/client/' . $name));
            });

            DB::commit();
        } catch (Exception $e) {

            Log::error('Failed to update status after being signed : ' . $e->getMessage() . ' | Line ' . $e->getLine());
            return response()->json(['status' => 'success', 'message' => 'Failed to update'], 500);
        }

        return response()->json(['status' => 'success', 'message' => 'Receipt signed successfully']);
    }

    public function sendToClient(Request $request)
    {
        $receipt_id = $request->route('receipt');
        $receipt = $this->receiptRepository->getReceiptById($receipt_id);
        $currency = $request->route('currency');
        $attachment = $receipt->receiptAttachment()->where('currency', $currency)->first();

        $pic_mail = $receipt->invoiceProgram->clientprog->internalPic->email;

        $data['email'] = $receipt->invoiceProgram->clientprog->client->parents[0]->mail;
        // $data['email'] = $receipt->invoiceProgram->clientprog->client->mail;
        $data['cc'] = [
            env('CEO_CC'),
            env('FINANCE_CC'),
            $pic_mail
        ];
        $data['recipient'] = $receipt->invoiceProgram->clientprog->client->parents[0]->full_name;
        // $data['recipient'] = $receipt->invoiceProgram->clientprog->client->full_name;
        $data['program_name'] = $receipt->invoiceProgram->clientprog->program->program_name;
        $data['title'] = "Receipt of program " . $data['program_name'];

        try {

            Mail::send('pages.receipt.client-program.mail.client-view', $data, function ($message) use ($data, $attachment) {
                $message->to($data['email'], $data['recipient'])
                    ->cc($data['cc'])
                    ->subject($data['title'])
                    ->attach(storage_path('app/public/uploaded_file/receipt/client/' . $attachment->attachment));
            });

            # update status send to client
            $newDetails['send_to_client'] = 'sent';
            $this->receiptAttachmentRepository->updateReceiptAttachment($attachment->id, $newDetails);
        } catch (Exception $e) {

            Log::info('Failed to send receipt to client : ' . $e->getMessage());
            return response()->json(['message' => 'Failed to send receipt to client.'], 500);
        }

        return response()->json(['message' => 'Successfully sent receipt to client.']);
    }

    public function updateParentMail(Request $request)
    {

        $client = $this->clientRepository->getClientById($request->parent_id);
        $parent_mail = $request->parent_mail;


        if(isset($client)){
            $client->mail != $parent_mail ? $this->clientRepository->updateClient($client->id, ['mail' => $parent_mail]) : null;
        }

        return response()->json(['status' => 'success', 'message' => 'Success Update Email Parent'], 200);
    }
}
