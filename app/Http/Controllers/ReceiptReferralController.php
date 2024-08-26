<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReceiptAttachmentRequest;
use App\Http\Requests\StoreReceiptRequest;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\ReferralRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\InvoiceDetailRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Interfaces\ReceiptAttachmentRepositoryInterface;
use App\Interfaces\RefundRepositoryInterface;
use App\Interfaces\AxisRepositoryInterface;
use App\Http\Traits\CreateReceiptIdTrait;
use App\Http\Traits\DirectorListTrait;
use App\Http\Traits\LoggingTrait;
use App\Models\Receipt;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use PDF;



class ReceiptReferralController extends Controller
{
    use DirectorListTrait;
    use CreateReceiptIdTrait;
    use LoggingTrait;
    protected CorporateRepositoryInterface $corporateRepository;
    protected ReferralRepositoryInterface $referralRepository;
    protected ProgramRepositoryInterface $programRepository;
    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    protected InvoiceDetailRepositoryInterface $invoiceDetailRepository;
    protected ReceiptAttachmentRepositoryInterface $receiptAttachmentRepository;
    protected ReceiptRepositoryInterface $receiptRepository;
    protected RefundRepositoryInterface $refundRepository;
    protected AxisRepositoryInterface $axisRepository;

    public function __construct(CorporateRepositoryInterface $corporateRepository, ReferralRepositoryInterface $referralRepository, ProgramRepositoryInterface $programRepository, InvoiceB2bRepositoryInterface $invoiceB2bRepository, InvoiceDetailRepositoryInterface $invoiceDetailRepository, ReceiptAttachmentRepositoryInterface $receiptAttachmentRepository, ReceiptRepositoryInterface $receiptRepository, RefundRepositoryInterface $refundRepository, AxisRepositoryInterface $axisRepository)
    {
        $this->corporateRepository = $corporateRepository;
        $this->referralRepository = $referralRepository;
        $this->programRepository = $programRepository;
        $this->invoiceB2bRepository = $invoiceB2bRepository;
        $this->invoiceDetailRepository = $invoiceDetailRepository;
        $this->receiptAttachmentRepository = $receiptAttachmentRepository;
        $this->receiptRepository = $receiptRepository;
        $this->refundRepository = $refundRepository;
        $this->axisRepository = $axisRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->receiptRepository->getAllReceiptReferralDataTables();
        }
        return view('pages.receipt.referral.index');
    }

    public function store(StoreReceiptRequest $request)
    {
        #initialize
        $identifier = $request->identifier; #invdtl_id

        $invb2b_num = $request->route('invoice');
        $receipts = $request->only([
            'rec_currency',
            'receipt_amount',
            'receipt_amount_idr',
            'receipt_date',
            'receipt_words',
            'receipt_words_idr',
            'receipt_method',
            'receipt_cheque',
            'pph23'
        ]);
        $receipts['currency'] = $receipts['rec_currency'];
        unset($receipts['rec_currency']);

        switch ($receipts['currency']) {
            case 'idr':
                unset($receipts['receipt_amount']);
                unset($receipts['receipt_words']);
                break;
        }

        $receipts['receipt_cat'] = 'referral';

        $receipts['created_at'] = $receipts['receipt_date'];
        $receipts['updated_at'] = Carbon::now();

        $invoice = $this->invoiceB2bRepository->getInvoiceB2bById($invb2b_num);
        $ref_id = $invoice->ref_id;

        $invb2b_id = $invoice->invb2b_id;

        # generate receipt id
        $last_id = Receipt::whereMonth('created_at', isset($request->receipt_date) ? date('m', strtotime($request->receipt_date)) : date('m'))->whereYear('created_at', isset($request->receipt_date) ? date('Y', strtotime($request->receipt_date)) : date('Y'))->max(DB::raw('substr(receipt_id, 1, 4)'));

        # Use Trait Create Invoice Id
        $receipt_id = $this->getLatestReceiptId($last_id, 'REF-OUT', $receipts);

        $receipts['receipt_id'] = substr_replace($receipt_id, 'REC', 5) . substr($receipt_id, 8, strlen($receipt_id));

        $receipts['invb2b_id'] = $invb2b_id;
        $invoice_payment_method = $invoice->invb2b_pm;

        # validation nominal
        # to catch if total invoice not equal to total receipt 
        if ($invoice_payment_method == "Full Payment") {

            $total_invoice = $invoice->invb2b_totpriceidr;
            $total_receipt = $request->receipt_amount_idr;
        }

        if ($receipts['pph23'] == 0 && $total_receipt < $total_invoice)
            return Redirect::back()->withError('Do double check the amount. Make sure the amount on invoice and the amount on receipt is equal');


        DB::beginTransaction();
        try {

            $receiptCreated = $this->receiptRepository->createReceipt($receipts);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Create receipt failed : ' . $e->getMessage());

            return Redirect::to('invoice/referral/' . $ref_id . '/detail/' . $invb2b_num)->withError('Failed to create a new receipt');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Receipt Referral Program', Auth::user()->first_name . ' '. Auth::user()->last_name, $receiptCreated);

        return Redirect::to('invoice/referral/' . $ref_id . '/detail/' . $invb2b_num)->withSuccess('Receipt successfully created');
    }

    public function show(Request $request)
    {
        $receiptId = $request->route('detail');

        $receiptRef = $this->receiptRepository->getReceiptById($receiptId);
        $invb2b_id = isset($receiptRef->invdtl_id) ? $receiptRef->invoiceInstallment->invb2b_id : $receiptRef->invb2b_id;
        $invoiceRef = $this->invoiceB2bRepository->getInvoiceB2bByInvId($invb2b_id)->first();


        return view('pages.receipt.referral.form')->with(
            [

                'receiptRef' => $receiptRef,
                'invoiceRef' => $invoiceRef,
                'status' => 'show',
            ]
        );
    }


    public function destroy(Request $request)
    {
        $receiptId = $request->route('detail');
        $receipt = $this->receiptRepository->getReceiptById($receiptId);

        DB::beginTransaction();
        try {

            $this->receiptRepository->deleteReceipt($receiptId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete receipt failed : ' . $e->getMessage());

            return Redirect::to('receipt/referral/' . $receiptId)->withError('Failed to delete receipt');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Receipt Referral Program', Auth::user()->first_name . ' '. Auth::user()->last_name, $receipt);

        return Redirect::to('receipt/referral')->withSuccess('Receipt successfully deleted');
    }

    public function export(Request $request)
    {
        $receipt_id = $request->route('receipt');
        $currency = $request->route('currency');

        # directors name
        $choosen_director = $request->get('selectedDirectorMail');
        $name = $this->getDirectorByEmail($choosen_director);

        $receiptRef = $this->receiptRepository->getReceiptById($receipt_id);
        $file_name = str_replace('/', '-', $receiptRef->receipt_id) . '-' . ($currency == 'idr' ? $currency : 'other') . '.pdf';       

        DB::beginTransaction();
        if (!$this->receiptAttachmentRepository->getReceiptAttachmentByReceiptId($receipt_id, $currency)) {

            try {
                
                $attachmentDetails = [
                    'receipt_id' => $receiptRef->receipt_id,
                    'currency' => $currency,
                    'sign_status' => 'not yet',
                    'recipient' => $choosen_director, # value of choosen director is email
                    'send_to_client' => 'not sent'
                ];
                $this->receiptAttachmentRepository->createReceiptAttachment($attachmentDetails);
    
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Error to store receipt referral attachment : '.$e->getMessage().' | Line '.$e->getLine());
                return response()->json(['message' => $e->getMessage()], 500);
            }
        }

        # generate file
        try {

            $companyDetail = [
                'name' => env('ALLIN_COMPANY'),
                'address' => env('ALLIN_ADDRESS'),
                'address_dtl' => env('ALLIN_ADDRESS_DTL'),
                'city' => env('ALLIN_CITY')
            ];
    
            $pdf = PDF::loadView('pages.receipt.referral.export.receipt-pdf', 
                [
                    'receiptRef' => $receiptRef, 
                    'currency' => $currency, 
                    'companyDetail' => $companyDetail,
                    'director' => $name
                ]);
    
            # Update status download
            $this->receiptRepository->updateReceipt($receipt_id, ['download_' . $currency => 1]);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::info('Export receipt referral failed: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);

        }

        # Download success
        # create log success
        $this->logSuccess('download', null, 'Receipt Referral Program', Auth::user()->first_name . ' '. Auth::user()->last_name, ['receipt_id' => $receipt_id]);

        return $pdf->download($receiptRef->receipt_id . ".pdf");
    }

    public function upload(StoreReceiptAttachmentRequest $request)
    {
        $receipt_identifier = $request->route('receipt');

        $currency = $request->currency;
        $attachment = $request->file('attachment');
        $file_name = $attachment->getClientOriginalName();

        $receipt = $this->receiptRepository->getReceiptById($receipt_identifier);
        $receipt_id = $receipt->receipt_id;

        $file_name = str_replace('/', '-', $receipt_id) . '-' . ($currency == 'idr' ? $currency : 'other') . '.pdf'; # 0001_REC_JEI_EF_I_23_idr.pdf
        $path = 'uploaded_file/receipt/referral/';

        DB::beginTransaction();
        try {

            if ($attachment->storeAs('public/' . $path, $file_name)) {
                # update request status on receipt attachment
                $attachment = $receipt->receiptAttachment()->where('currency', $currency)->first();
                $attachment->attachment = 'storage/' . $path . $file_name;
                $attachment->save();
            }

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Upload receipt referral failed : ' . $e->getMessage());
            return Redirect::to('receipt/referral/' . $receipt_identifier)->withError('Failed to upload receipt');
        }

        # Upload success
        # create log success
        $this->logSuccess('upload', null, 'Receipt Referral Program', Auth::user()->first_name . ' '. Auth::user()->last_name, ['receipt_id' => $receipt_id]);

        return Redirect::to('receipt/referral/' . $receipt_identifier)->withSuccess('Receipt successfully uploaded');
    }

    public function requestSign(Request $request)
    {
        $receipt_identifier = $request->route('receipt');
        $currency = $request->route('currency');

        $receipt = $this->receiptRepository->getReceiptById($receipt_identifier);
        $info = $receipt->receiptAttachment()->where('currency', $currency)->first();
        $to = $info->recipient;
        $name = $this->getDirectorByEmail($to);

        # check whether invoiceb2b is installment or not        
        $is_installment = is_null($receipt->invoiceB2b) ? true : false; 

        $receipt_id = $receipt->receipt_id;

        $receiptAtt = $this->receiptAttachmentRepository->getReceiptAttachmentByReceiptId($receipt_id, $currency);

        $data['email'] = $to;
        $data['recipient'] = $name;
        $data['title'] = "Request Sign of Receipt Number : " . $receipt_id;
        $data['param'] = [
            'receipt_identifier' => $receipt_identifier,
            'currency' => $currency,
            'fullname' => $receipt->invoiceB2b->referral->partner->corp_name,
            'program_name' => $receipt->invoiceB2b->referral->additional_prog_name,
            'receipt_date' => date('d F Y', strtotime($receipt->created_at)),
        ];

        DB::beginTransaction();
        try {

            # Update status request
            $this->receiptAttachmentRepository->updateReceiptAttachment($receiptAtt->id, ['request_status' => 'requested']);

            $file_name = str_replace('/', '-', $receipt->receipt_id);

            Mail::send('pages.receipt.referral.mail.view', $data, function ($message) use ($data, $file_name, $currency) {
                $message->to($data['email'], $data['recipient'])
                    ->subject($data['title'])
                    ->attach(storage_path('app/public/uploaded_file/receipt/referral/'.$file_name.'-'.$currency.'.pdf'));
            });
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::info('Failed to request sign receipt referral : ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }

        # Request Sign success
        # create log success
        $this->logSuccess('request-sign', null, 'Receipt Referral Program', Auth::user()->first_name . ' '. Auth::user()->last_name, ['receipt_id' => $receipt_id]);

        return true;
    }

    public function signAttachment(Request $request)
    {
        // if (Session::token() != $request->get('token')) {
        //     return "Your session token is expired";
        // }

        $receipt_Identifier = $request->route('receipt');
        $currency = $request->route('currency');
        $receipt = $this->receiptRepository->getReceiptById($receipt_Identifier);
        $receipt_id = $receipt->receipt_id;
        $receiptAttachment = $this->receiptAttachmentRepository->getReceiptAttachmentByReceiptId($receipt_id, $currency);
        $axis = $this->axisRepository->getAxisByType('receipt');

        if (isset($receiptAttachment->sign_status) && $receiptAttachment->sign_status == 'signed') {
            return "Receipt is already signed";
        }

        return view('pages.receipt.sign-pdf')->with(
            [
                'attachment' => $receiptAttachment->attachment,
                'currency' => $currency,
                'receipt' => $receipt,
                'axis' => $axis,
            ]
        );
    }

    public function upload_signed(Request $request)
    {
        $pdfFile = $request->file('pdfFile');
        $name = $request->file('pdfFile')->getClientOriginalName();
        $receipt_identifier = $request->route('receipt');
        $receipt = $this->receiptRepository->getReceiptById($receipt_identifier);
        $receipt_id = $receipt->receipt_id;
        $currency = $request->route('currency');

        $dataAxis = $this->axisRepository->getAxisByType('receipt');

        $attachmentDetails = [
            'sign_status' => 'signed',
            'approve_date' => Carbon::now()
        ];

        $receiptAttachment = $this->receiptAttachmentRepository->getReceiptAttachmentByReceiptId($receipt_id, $currency);

        if ($receiptAttachment->sign_status == 'signed') {
            return response()->json(['status' => 'error', 'message' => 'Document has already signed']);
        }

        DB::beginTransaction();
        try {

            # if no_data == false
            if ($request->no_data == 0) {
                $axis = [
                    'top' => $request->top,
                    'left' => $request->left,
                    'scaleX' => $request->scaleX,
                    'scaleY' => $request->scaleY,
                    'angle' => $request->angle,
                    'flipX' => $request->flipX,
                    'flipY' => $request->flipY,
                    'type' => 'receipt'
                ];

                if (isset($dataAxis)) {
                    $this->axisRepository->updateAxis($dataAxis->id, $axis);
                } else {

                    $this->axisRepository->createAxis($axis);
                }
            }

            $this->receiptAttachmentRepository->updateReceiptAttachment($receiptAttachment->id, $attachmentDetails);
            if (!$pdfFile->storeAs('public/uploaded_file/receipt/referral/', $name))
                throw new Exception('Failed to store signed receipt file');

            $data['title'] = 'Receipt No. ' . $receipt_id . ' has been signed';
            $data['receipt_id'] = $receipt_id;

            # send mail when document has been signed
            Mail::send('pages.receipt.referral.mail.signed', $data, function ($message) use ($data, $receiptAttachment) {
                $message->to(env('FINANCE_CC'), env('FINANCE_NAME'))
                    ->subject($data['title'])
                    ->attach(public_path($receiptAttachment->attachment));
            });

            DB::commit();
        } catch (Exception $e) {
            Log::error('Failed to update status after being signed : ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to update'], 500);
        }

        # Signed success
        # create log success
        $this->logSuccess('signed', null, 'Receipt Referral Program', 'Director', ['receipt_id' => $receipt_id]);

        return response()->json(['status' => 'success', 'message' => 'Receipt signed successfully']);
    }

    public function sendToClient(Request $request)
    {
        $receipt_identifier = $request->route('receipt');
        $currency = $request->route('currency');
        $receipt = $this->receiptRepository->getReceiptById($receipt_identifier);
        $receipt_id = $receipt->receipt_id;
        $receiptAttachment = $this->receiptAttachmentRepository->getReceiptAttachmentByReceiptId($receipt_id, $currency);

        $program_name = $receipt->invoiceB2b->referral->additional_prog_name;

        $data['email'] = $receipt->invoiceB2b->referral->user->email;
        $data['cc'] = [
            env('CEO_CC'),
            env('FINANCE_CC')
        ];
        $data['recipient'] = $receipt->invoiceB2b->referral->user->email;
        $data['title'] = "Receipt of program " . $program_name;
        $data['param'] = [
            'receipt_identifier' => $receipt_identifier,
            'currency' => $currency,
            'fullname' => $receipt->invoiceB2b->referral->partner->corp_name,
            'program_name' => $receipt->invoiceB2b->referral->additional_prog_name,
        ];

        try {

            Mail::send('pages.receipt.school-program.mail.client-view', $data, function ($message) use ($data, $receiptAttachment) {
                $message->to($data['email'], $data['recipient'])
                    ->cc($data['cc'])
                    ->subject($data['title'])
                    ->attach(public_path($receiptAttachment->attachment));
            });

            $attachmentDetails = [
                'send_to_client' => 'sent',
            ];

            $this->receiptAttachmentRepository->updateReceiptAttachment($receiptAttachment->id, $attachmentDetails);
        } catch (Exception $e) {

            Log::info('Failed to send receipt to client : ' . $e->getMessage());
            return false;
        }

        # Send To Client success
        # create log success
        $this->logSuccess('send-to-client', null, 'Receipt Referral Program', Auth::user()->first_name . ' '. Auth::user()->last_name, ['receipt_id' => $receipt_id]);

        return true;
    }

    public function print(Request $request)
    {
        $receipt_identifier = $request->route('receipt');
        $currency = $request->route('currency');

        $receipt = $this->receiptRepository->getReceiptById($receipt_identifier);
        $receipt_id = $receipt->receipt_id;

        $receiptAttachment = $this->receiptAttachmentRepository->getReceiptAttachmentByReceiptId($receipt_id, $currency);

        return view('pages.receipt.view-pdf')->with([
            'receiptAttachment' => $receiptAttachment,
        ]);
    }

    public function previewPdf(Request $request)
    {
        $receipt_identifier = $request->route('receipt');
        $currency = $request->route('currency');

        $receiptRef = $this->receiptRepository->getReceiptById($receipt_identifier);
        
        $director = $name = null;
        if(isset($receiptRef->invoiceB2b->invoiceAttachment))
        {
            $director = $receiptRef->invoiceB2b->invoiceAttachment()->first();
            if($director->recipient != null){
                # directors name
                $name = $this->getDirectorByEmail($director->recipient);
            }
        }

        $companyDetail = [
            'name' => env('ALLIN_COMPANY'),
            'address' => env('ALLIN_ADDRESS'),
            'address_dtl' => env('ALLIN_ADDRESS_DTL'),
            'city' => env('ALLIN_CITY')
        ];

        $pdf = PDF::loadView('pages.receipt.referral.export.receipt-pdf', 
                [
                    'receiptRef' => $receiptRef, 
                    'currency' => $currency, 
                    'companyDetail' => $companyDetail,
                    'director' => $name
                ]);

        return $pdf->stream();
    }
}
