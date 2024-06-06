<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReceiptRequest;
use App\Http\Traits\CreateReceiptIdTrait;
use App\Http\Traits\DirectorListTrait;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ReceiptAttachmentRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Jobs\Receipt\ProcessUploadReceiptJob;
use App\Models\Receipt;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use PDF;

class ReceiptController extends Controller
{
    use DirectorListTrait;
    use CreateReceiptIdTrait;
    use LoggingTrait;
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
        $isBundle = $request->get('b') !== NULL ? true : false;

        if ($request->ajax())
 
            return $this->receiptRepository->getAllReceiptByStatusDataTables($isBundle);

        if ($isBundle)
            return view('pages.receipt.client-program.index-bundle');

        return view('pages.receipt.client-program.index');
    }

    public function show(Request $request)
    {
        $receipt_id = $request->route('receipt');
        $receipt = $this->receiptRepository->getReceiptById($receipt_id);
        
        $isBundle = $request->get('b') !== NULL ? true : false;

        if($isBundle){
            return view('pages.receipt.client-program.form-bundle')->with(
                [
                    'bundle' => $receipt->invoiceProgram->bundling,
                    'receipt' => $receipt
                    ]
            );
        }else{
            return view('pages.receipt.client-program.form')->with(
                [
                    'client_prog' => $receipt->invoiceProgram->clientProg,
                    'receipt' => $receipt
                ]
            );
        }

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
        // $receiptDetails['updated_at'] = Carbon::now();

        $client_prog = $this->clientProgramRepository->getClientProgramById($request->clientprog_id);
        
        # validation child receipt bundle
        # master receipt bundle must be created first
        if($request->is_child_program_bundle > 0 && !isset($client_prog->bundlingDetail->bundling->invoice_b2c->receipt)){
            return Redirect::to('invoice/client-program/' . $request->clientprog_id)->withError('Create master receipt bundle first!');
        }

        $invoice = $client_prog->invoice()->first();

        # generate receipt id
        $last_id = Receipt::whereMonth('created_at', isset($request->receipt_date) ? date('m', strtotime($request->receipt_date)) : date('m'))->whereYear('created_at', isset($request->receipt_date) ? date('Y', strtotime($request->receipt_date)) : date('Y'))->max(DB::raw('substr(receipt_id, 1, 4)'));

        # Use Trait Create Invoice Id
        $receiptDetails['receipt_id'] = $this->getLatestReceiptId($last_id, $client_prog->prog_id, $receiptDetails);

        if($request->is_child_program_bundle > 0){
            $last_id = Receipt::whereMonth('created_at', isset($request->receipt_date) ? date('m', strtotime($request->receipt_date)) : date('m'))->whereYear('created_at', isset($request->receipt_date) ? date('Y', strtotime($request->receipt_date)) : date('Y'))->whereRelation('invoiceProgram', 'bundling_id', $client_prog->bundlingDetail->bundling_id)->max(DB::raw('substr(receipt_id, 1, 4)'));
           
            $bundlingDetails = $this->clientProgramRepository->getBundleProgramDetailByBundlingId($client_prog->bundlingDetail->bundling_id);

            $clientIdsBundle = $incrementBundle = [];
            $is_cross_client = false;
            
            foreach ($bundlingDetails as $key => $bundlingDetail) {
                $incrementBundle[$bundlingDetail->client_program->clientprog_id] = $key + 1;
                $clientIdsBundle[] = $bundlingDetail->client_program->client->id;
            }
    
            if(count(array_count_values($clientIdsBundle)) > 1)
                $is_cross_client = true;

            # Use Trait Create Invoice Id
            $receiptDetails['receipt_id'] = $this->getLatestReceiptId($last_id, 'BDL', $receiptDetails, ['is_bundle' => 1, 'is_cross_client' => $is_cross_client, 'increment_bundle' => $incrementBundle[$invoice->clientprog->clientprog_id]]);
        
        }

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

            $receiptCreated = $this->receiptRepository->createReceipt($receiptDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store receipt failed : ' . $e->getMessage());
            return Redirect::back()->withError('Failed to create receipt');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Receipt Client Program', Auth::user()->first_name . ' '. Auth::user()->last_name, $receiptCreated);

        return Redirect::to('invoice/client-program/' . $request->clientprog_id)->withSuccess('A receipt has been made');
    }

    public function destroy(Request $request)
    {
        $receiptId = $request->route('receipt');
        $receipt = $this->receiptRepository->getReceiptById($receiptId);

        DB::beginTransaction();
        try {

            $this->receiptRepository->deleteReceipt($receiptId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete receipt failed : ' . $e->getMessage());
            return Redirect::back()->withError('Failed to delete receipt');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Receipt Client Program', Auth::user()->first_name . ' '. Auth::user()->last_name, $receipt);

        return Redirect::to('receipt/client-program?s=list')->withSuccess('Receipt has been deleted');
    }

    public function export(Request $request) # print / download function
    {
        
        $receiptId = $request->route('receipt');
        $receipt = $this->receiptRepository->getReceiptById($receiptId);      
        
        $isBundle = $request->get('b') !== NULL ? true : false;

        # directors name
        $choosen_director = $request->get('selectedDirectorMail');
        $name = $this->getDirectorByEmail($choosen_director);

        $type = $request->get('type');

        $file_name = str_replace('/', '-', $receipt->receipt_id) . '-' . ($type == 'idr' ? $type : 'other') . '.pdf';

        if ($type == "idr"){
            $view = 'pages.receipt.client-program.export.receipt-pdf';
            if($isBundle){
                $view = 'pages.receipt.client-program.export.receipt-bundle-pdf';
            }
        }else{
            $view = 'pages.receipt.client-program.export.receipt-pdf-foreign';
            if($isBundle){
                $view = 'pages.receipt.client-program.export.receipt-bundle-pdf-foreign';
            }
        }

            
        # store to receipt attachment
        DB::beginTransaction();

        if (!$this->receiptAttachmentRepository->getReceiptAttachmentByReceiptId($receiptId, $type)) {

            try {
                
                $attachmentDetails = [
                    'receipt_id' => $receipt->receipt_id,
                    'currency' => $type,
                    'sign_status' => 'not yet',
                    'recipient' => $choosen_director, # value of choosen director is email
                    'send_to_client' => 'not sent'
                ];
                $this->receiptAttachmentRepository->createReceiptAttachment($attachmentDetails);
    
            } catch (Exception $e) {
                Log::error('Error to store receipt attachment : '.$e->getMessage().' | Line '.$e->getLine());
                DB::rollBack();
                return response()->json(['message' => $e->getMessage()], 500);
            }
        }
        

        # generate file 
        try {
            # update download status on tbl_receipt

            if ($type == "idr"){
                $this->receiptRepository->updateReceipt($receiptId, ['download_idr' => 1]);
            }else{
                $this->receiptRepository->updateReceipt($receiptId, ['download_other' => 1]);
            }
            
            DB::commit();
            
            $companyDetail = [
                'name' => env('ALLIN_COMPANY'),
                'address' => env('ALLIN_ADDRESS'),
                'address_dtl' => env('ALLIN_ADDRESS_DTL'),
                'city' => env('ALLIN_CITY')
            ];

            $pdf = PDF::loadView($view, ['receipt' => $receipt, 'companyDetail' => $companyDetail, 'director' => $name]);

        } catch (Exception $e) {

            Log::error('Export receipt failed: ' . $e->getMessage() . ' Line:' . $e->getLine());
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }

        # Download success
        # create log success
        $this->logSuccess('download', null, 'Receipt Client Program', Auth::user()->first_name . ' '. Auth::user()->last_name, ['receipt_id' => $receiptId]);
        
        $pdf->setPaper('a4' , 'portrait');
        return $pdf->output();

    }

    public function upload(Request $request)
    {
        $receipt_id = $request->route('receipt');
        $receipt = $this->receiptRepository->getReceiptById($receipt_id);
        $currency = $request->currency;

        $isBundle = $request->get('b') !== NULL ? true : false;


        if ($receipt->receiptAttachment()->where('currency', $currency)->whereNotNull('attachment')->where('sign_status', 'not yet')->first())
            return Redirect::back()->withError('You already upload the receipt.');

        $validated = $request->validate([
            'currency' => 'in:idr,other',
            'attachment' => 'required|file|mimes:pdf'
        ]);

        $uploadedFile = $request->file('attachment');
        
        DB::beginTransaction();
        try {

            $file_name = $uploadedFile->getClientOriginalName();
            $file_name = str_replace('/', '-', $receipt->receipt_id) . '-' . ($currency == 'idr' ? $currency : 'other') . '.pdf'; # 0001_REC_JEI_EF_I_23_idr.pdf
            $path = 'public/uploaded_file/receipt/client/';

            # generate invoice as a PDF file
            if ($uploadedFile->storeAs($path, $file_name)) {
                # update request status on receipt attachment
                $attachment = $receipt->receiptAttachment()->where('currency', $currency)->first();
                $attachment->attachment = $file_name;
                $attachment->save();
            }
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::info('Failed to upload sign receipt : ' . $e->getMessage());
            return Redirect::back()->withError('Failed to upload receipt. Please try again.');
        }

        # Upload success
        # create log success
        $this->logSuccess('upload', null, 'Receipt Client Program', Auth::user()->first_name . ' '. Auth::user()->last_name, ['receipt_id' => $receipt_id]);

        if($isBundle){
            return Redirect::to('receipt/client-program/' . $receipt_id . '?b=true')->withSuccess('Receipt has been uploaded.');
        }
        return Redirect::to('receipt/client-program/' . $receipt_id)->withSuccess('Receipt has been uploaded.');
    }

    public function requestSign(Request $request)
    {
        $receipt_id = $request->route('receipt');
        $receipt = $this->receiptRepository->getReceiptById($receipt_id);
        $isBundle = $request->get('b') !== NULL ? true : false;

        $type = $request->get('type');
        $info = $receipt->receiptAttachment()->where('currency', $type)->first();
        $to = $info->recipient;
        $name = $this->getDirectorByEmail($to);

        if ($type == "idr")
            $view = 'pages.receipt.client-program.export.receipt-pdf';
        else
            $view = 'pages.receipt.client-program.export.receipt-bundle-pdf-foreign';

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
            'receipt_date' => date('d F Y', strtotime($receipt->created_at))
        ];

        if($isBundle){
            $data['param']['fullname'] = $receipt->invoiceProgram->bundling->first_detail->client_program->client->full_name;
            $data['param']['program_name'] = $receipt->invoiceProgram->bundling->first_detail->client_program->program->program_name . ' (Bundle Package)';
        }else{
            $data['param']['fullname'] = $receipt->invoiceProgram->clientprog->client->full_name;
            $data['param']['program_name'] = $receipt->invoiceProgram->clientprog->program->program_name;
        }

        DB::beginTransaction();
        try {

            # update request status on receipt attachment
            $attachment = $receipt->receiptAttachment()->where('currency', $type)->first();
            $attachment->request_status = 'requested';
            $attachment->save();

            $file_name = str_replace('/', '-', $receipt->receipt_id);

            Mail::send('pages.receipt.client-program.mail.view', $data, function ($message) use ($data, $file_name, $type, $receipt) {
                $message->to($data['email'], $data['recipient'])
                    ->subject($data['title'])
                    ->attach(storage_path('app/public/uploaded_file/receipt/client/'.$file_name.'-'.$type.'.pdf'));
            });
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::info('Failed to request sign receipt : ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }

        # Request Sign success
        # create log success
        $this->logSuccess('request-sign', null, 'Receipt Client Program', Auth::user()->first_name . ' '. Auth::user()->last_name, ['receipt_id' => $receipt_id]);

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

            DB::rollBack();
            Log::error('Failed to update status after being signed : ' . $e->getMessage() . ' | Line ' . $e->getLine());
            return response()->json(['status' => 'success', 'message' => 'Failed to update'], 500);
        }

        # Signed success
        # create log success
        $this->logSuccess('signed', null, 'Receipt Client Program', 'Director', ['receipt_id' => $receipt_id]);

        return response()->json(['status' => 'success', 'message' => 'Receipt signed successfully']);
    }

    public function sendToClient(Request $request)
    {
        $receipt_id = $request->route('receipt');
        $type_recipient = $request->route('type_recipient');
        $receipt = $this->receiptRepository->getReceiptById($receipt_id);
        $currency = $request->route('currency');
        $attachment = $receipt->receiptAttachment()->where('currency', $currency)->first();
        $isBundle = $request->get('b') !== NULL ? true : false;

        if($isBundle){
            $data['program_name'] = $receipt->invoiceProgram->bundling->first_detail->client_program->program->program_name . ' (Bundle Package)';
            $pic_mail = $receipt->invoiceProgram->bundling->first_detail->client_program->internalPic->email;
        }else{
            $data['program_name'] = $receipt->invoiceProgram->clientprog->program->program_name;
            $pic_mail = $receipt->invoiceProgram->clientprog->internalPic->email;
        }

        switch ($type_recipient) {
            case 'Parent':
                if($isBundle){
                    $data['email'] = $receipt->invoiceProgram->bundling->first_detail->client_program->client->parents[0]->mail;
                    $data['recipient'] = $receipt->invoiceProgram->bundling->first_detail->client_program->client->parents[0]->full_name;
                }else{
                    $data['email'] = $receipt->invoiceProgram->clientprog->client->parents[0]->mail;
                    $data['recipient'] = $receipt->invoiceProgram->clientprog->client->parents[0]->full_name;
                }
                break;

            case 'Client':
                if($isBundle){
                    $data['email'] = $receipt->invoiceProgram->bundling->first_detail->client_program->client->mail;
                    $data['recipient'] = $receipt->invoiceProgram->bundling->first_detail->client_program->client->full_name;
                }
                $data['email'] = $receipt->invoiceProgram->clientprog->client->mail;
                $data['recipient'] = $receipt->invoiceProgram->clientprog->client->full_name;
                break;
        }

        $data['cc'] = [
            env('CEO_CC'),
            env('FINANCE_CC'),
            $pic_mail
        ];
        
        $data['title'] = "Receipt of program " . $data['program_name'];

        # send mail 
        try {
            
            $storagePath = storage_path('app/public/uploaded_file/receipt/client/' . $attachment->attachment);
            if (!File::exists($storagePath)) 
                return response()->json(['message' => "Receipt doesn't exist. Make sure the receipt has already been signed"], 500);

            Mail::send('pages.receipt.client-program.mail.client-view', $data, function ($message) use ($data, $attachment) {
                $message->to($data['email'], $data['recipient'])
                    ->cc($data['cc'])
                    ->subject($data['title'])
                    ->attach(storage_path('app/public/uploaded_file/receipt/client/' . $attachment->attachment));
            });
            $status_mail = 'sent';

        } catch (Exception $e) {

            $status_mail = 'not sent';
            Log::info('Failed to send receipt to client : ' . $e->getMessage().' | Line : '.$e->getLine());

        }

        if ($status_mail == 'not sent')
            return response()->json(['message' => 'Failed to send receipt to client.'], 500);

        DB::beginTransaction();
        try {


            # update status send to client
            $newDetails['send_to_client'] = 'sent';
            $this->receiptAttachmentRepository->updateReceiptAttachment($attachment->id, $newDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::info('Failed to update send status receipt : ' . $e->getMessage().' | Line : '.$e->getLine());
            return response()->json(['message' => 'Failed to send receipt to client.'], 500);

        }

        # Send To Client success
        # create log success
        $this->logSuccess('send-to-client', null, 'Receipt Client Program', Auth::user()->first_name . ' '. Auth::user()->last_name, ['receipt_id' => $receipt_id, 'recipient' => $type_recipient]);

        return response()->json(['message' => 'Successfully sent receipt to client.']);
    }

    public function updateMail(Request $request)
    {

        $client = $this->clientRepository->getClientById($request->client_id);
        $mail = $request->mail;

        if(isset($client)){
            DB::beginTransaction();
            try {

                $client->mail != $mail ? $this->clientRepository->updateClient($client->id, ['mail' => $mail]) : null;
                DB::commit();

            } catch (Exception $e) {

                DB::rollBack();
                Log::error('Failed to update client mail '. $e->getMessage().' | line '.$e->getLine() );
                return response()->json(['status' => 'failed', 'message' => 'Something went wrong. Please try again or contact the administrator.'], 500);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Success Update Email'], 200);
    }


    // ============ Bundling ==============

    public function storeBundle(StoreReceiptRequest $request)
    {
        // return $request->all();
        // exit;
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
        // $receiptDetails['updated_at'] = Carbon::now();

        $bundle = $this->clientProgramRepository->getBundleProgramByUUID($request->bundling_id);
        $invoice = $bundle->invoice_b2c()->first();

        # generate receipt id
        $last_id = Receipt::whereMonth('created_at', isset($request->receipt_date) ? date('m', strtotime($request->receipt_date)) : date('m'))->whereYear('created_at', isset($request->receipt_date) ? date('Y', strtotime($request->receipt_date)) : date('Y'))->max(DB::raw('substr(receipt_id, 1, 4)'));

        # Use Trait Create Invoice Id
        $receiptDetails['receipt_id'] = $this->getLatestReceiptId($last_id, 'BDL', $receiptDetails);

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

            $receiptCreated = $this->receiptRepository->createReceipt($receiptDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store receipt failed : ' . $e->getMessage());
            return Redirect::back()->withError('Failed to create receipt');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Receipt Client Program', Auth::user()->first_name . ' '. Auth::user()->last_name, $receiptCreated);

        return Redirect::to('invoice/client-program/bundle/' . $request->bundling_id)->withSuccess('A receipt has been made');
    }
}
