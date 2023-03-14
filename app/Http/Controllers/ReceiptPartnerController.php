<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceSchRequest;
use App\Http\Requests\StoreReceiptAttachmentRequest;
use App\Http\Requests\StoreReceiptRequest;
use App\Http\Requests\StoreReceiptSchRequest;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\PartnerProgramRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\InvoiceDetailRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Interfaces\ReceiptAttachmentRepositoryInterface;
use App\Interfaces\RefundRepositoryInterface;
use App\Interfaces\AxisRepositoryInterface;
use App\Http\Traits\CreateInvoiceIdTrait;
use App\Models\Invb2b;
use App\Models\Receipt;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use PDF;



class ReceiptPartnerController extends Controller
{
    use CreateInvoiceIdTrait;
    protected SchoolRepositoryInterface $schoolRepository;
    protected PartnerProgramRepositoryInterface $partnerProgramRepository;
    protected ProgramRepositoryInterface $programRepository;
    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    protected InvoiceDetailRepositoryInterface $invoiceDetailRepository;
    protected ReceiptRepositoryInterface $receiptRepository;
    protected ReceiptAttachmentRepositoryInterface $receiptAttachmentRepository;
    protected RefundRepositoryInterface $refundRepository;
    protected AxisRepositoryInterface $axisRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository, PartnerProgramRepositoryInterface $partnerProgramRepository, ProgramRepositoryInterface $programRepository, InvoiceB2bRepositoryInterface $invoiceB2bRepository, InvoiceDetailRepositoryInterface $invoiceDetailRepository, ReceiptRepositoryInterface $receiptRepository, RefundRepositoryInterface $refundRepository, AxisRepositoryInterface $axisRepository, ReceiptAttachmentRepositoryInterface $receiptAttachmentRepository)
    {
        $this->schoolRepository = $schoolRepository;
        $this->partnerProgramRepository = $partnerProgramRepository;
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
            return $this->receiptRepository->getAllReceiptCorpDataTables();
        }
        return view('pages.receipt.corporate-program.index');
    }

    public function store(StoreReceiptRequest $request)
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

        $receipts['receipt_cat'] = 'partner';

        $invoice = $this->invoiceB2bRepository->getInvoiceB2bById($invb2b_num);
        $partnerProgId = $invoice->partnerprog_id;
        $partner_prog = $this->partnerProgramRepository->getPartnerProgramById($partnerProgId);

        // return $invoice->inv_detail();
        // exit;

        $invb2b_id = $invoice->invb2b_id;

        # generate receipt id
        $last_id = Receipt::whereMonth('created_at', date('m'))->max(DB::raw('substr(receipt_id, 1, 4)'));

        # Use Trait Create Invoice Id
        $receipt_id = $this->getInvoiceId($last_id, $partner_prog->prog_id);

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
            return Redirect::to('invoice/corporate-program/' . $partnerProgId . '/detail/' . $invb2b_num)->withError('Failed to create a new receipt');
        }

        return Redirect::to('invoice/corporate-program/' . $partnerProgId . '/detail/' . $invb2b_num)->withSuccess('Receipt successfully created');
    }

    public function show(Request $request)
    {
        $receiptId = $request->route('detail');

        $receiptPartner = $this->receiptRepository->getReceiptById($receiptId);
        $invb2b_id = isset($receiptPartner->invdtl_id) ? $receiptPartner->invoiceInstallment->invb2b_id : $receiptPartner->invb2b_id;
        $invoicePartner = $this->invoiceB2bRepository->getInvoiceB2bByInvId($invb2b_id)->first();


        return view('pages.receipt.corporate-program.form')->with(
            [

                'receiptPartner' => $receiptPartner,
                'invoicePartner' => $invoicePartner,
                'status' => 'show',
            ]
        );
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

            return Redirect::to('receipt/corporate-program/' . $receiptId)->withError('Failed to delete receipt');
        }

        return Redirect::to('receipt/corporate-program')->withSuccess('Receipt successfully deleted');
    }

    public function export(Request $request)
    {
        $receipt_id = $request->route('receipt');
        $currency = $request->route('currency');

        $receiptPartner = $this->receiptRepository->getReceiptById($receipt_id);

        $companyDetail = [
            'name' => env('ALLIN_COMPANY'),
            'address' => env('ALLIN_ADDRESS'),
            'address_dtl' => env('ALLIN_ADDRESS_DTL'),
            'city' => env('ALLIN_CITY')
        ];

        $pdf = PDF::loadView('pages.receipt.corporate-program.export.receipt-pdf', ['receiptPartner' => $receiptPartner, 'currency' => $currency, 'companyDetail' => $companyDetail]);

        # Update status download
        $this->receiptRepository->updateReceipt($receipt_id, ['download_' . $currency => 1]);

        return $pdf->download($receiptPartner->receipt_id . ".pdf");
    }

    public function upload(StoreReceiptAttachmentRequest $request)
    {
        $receipt_identifier = $request->route('receipt');

        $currency = $request->currency;
        $attachment = $request->file('attachment');
        $file_name = $attachment->getClientOriginalName();

        $receipt = $this->receiptRepository->getReceiptById($receipt_identifier);
        $receipt_id = $receipt->receipt_id;

        $file_name = str_replace('/', '_', $receipt_id) . '_' . ($currency == 'idr' ? $currency : 'other') . '.pdf'; # 0001_REC_JEI_EF_I_23_idr.pdf
        $path = 'uploaded_file/receipt/partner_prog/';

        $receiptAttachments = [
            'receipt_id' => $receipt_id,
            'attachment' => 'storage/' . $path . $file_name,
            'currency' => $currency,
        ];

        DB::beginTransaction();
        try {

            if ($attachment->storeAs('public/' . $path, $file_name)) {
                $this->receiptAttachmentRepository->createReceiptAttachment($receiptAttachments);
            }

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Upload receipt failed : ' . $e->getMessage());
            return Redirect::to('receipt/corporate-program/' . $receipt_identifier)->withError('Failed to upload receipt');
        }

        return Redirect::to('receipt/corporate-program/' . $receipt_identifier)->withSuccess('Receipt successfully uploaded');
    }

    public function requestSign(Request $request)
    {
        $receipt_identifier = $request->route('receipt');
        $currency = $request->route('currency');

        $receipt = $this->receiptRepository->getReceiptById($receipt_identifier);
        $receipt_id = $receipt->receipt_id;

        $receiptAtt = $this->receiptAttachmentRepository->getReceiptAttachmentByReceiptId($receipt_id, $currency);

        // $companyDetail = [
        //     'name' => env('ALLIN_COMPANY'),
        //     'address' => env('ALLIN_ADDRESS'),
        //     'address_dtl' => env('ALLIN_ADDRESS_DTL'),
        //     'city' => env('ALLIN_CITY')
        // ];

        $data['email'] = env('DIRECTOR_EMAIL');
        $data['recipient'] = env('DIRECTOR_NAME');
        $data['title'] = "Request Sign of Receipt Number : " . $receipt_id;
        $data['param'] = [
            'receipt_identifier' => $receipt_identifier,
            'currency' => $currency,
            'fullname' => $receipt->invoiceB2b->partner_prog->corp->corp_name,
            'program_name' => $receipt->invoiceB2b->partner_prog->program->program_name,
            'receipt_date' => date('d F Y', strtotime($receipt->created_at)),
        ];

        try {

            # Update status request
            $this->receiptAttachmentRepository->updateReceiptAttachment($receiptAtt->id, ['request_status' => 'requested']);

            Mail::send('pages.receipt.corporate-program.mail.view', $data, function ($message) use ($data) {
                $message->to($data['email'], $data['recipient'])
                    ->subject($data['title']);
            });
        } catch (Exception $e) {

            Log::info('Failed to request sign receipt : ' . $e->getMessage());
            return $e->getMessage();
        }

        return true;
    }

    public function signAttachment(Request $request)
    {
        if (Session::token() != $request->get('token')) {
            return "Your session token is expired";
        }

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

            if (!$pdfFile->storeAs('public/uploaded_file/receipt/partner_prog/', $name))
                throw new Exception('Failed to store signed receipt file');

            $data['title'] = 'Receipt No. ' . $receipt_id . ' has been signed';
            $data['receipt_id'] = $receipt_id;

            # send mail when document has been signed
            Mail::send('pages.receipt.corporate-program.mail.signed', $data, function ($message) use ($data, $receiptAttachment) {
                $message->to(env('FINANCE_CC'), env('FINANCE_NAME'))
                    ->subject($data['title'])
                    ->attach(public_path($receiptAttachment->attachment));
            });

            DB::commit();
        } catch (Exception $e) {
            Log::error('Failed to update status after being signed : ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to update'], 500);
        }
        return response()->json(['status' => 'success', 'message' => 'Receipt signed successfully']);
    }

    public function sendToClient(Request $request)
    {
        $receipt_identifier = $request->route('receipt');
        $currency = $request->route('currency');
        $receipt = $this->receiptRepository->getReceiptById($receipt_identifier);
        $receipt_id = $receipt->receipt_id;
        $receiptAttachment = $this->receiptAttachmentRepository->getReceiptAttachmentByReceiptId($receipt_id, $currency);

        $program_name = $receipt->invoiceB2b->partner_prog->program->prog_program;

        if ($receipt->invoiceB2b->partner_prog->program->sub_prog_id > 0) {
            $program_name = $receipt->invoiceB2b->partner_prog->program->prog_sub . ' - ' . $receipt->invoiceB2b->partner_prog->program->prog_program;
        }

        $data['email'] = $receipt->invoiceB2b->partner_prog->user->email;
        $data['cc'] = [
            env('CEO_CC'),
            env('FINANCE_CC')
        ];
        $data['recipient'] = $receipt->invoiceB2b->partner_prog->user->email;
        $data['title'] = "ALL-In Eduspace | Invoice of program : " . $program_name;
        $data['param'] = [
            'receipt_identifier' => $receipt_identifier,
            'currency' => $currency,
            'fullname' => $receipt->invoiceB2b->partner_prog->corp->corp_name,
            'program_name' => $receipt->invoiceB2b->partner_prog->program->program_name,
        ];

        try {

            Mail::send('pages.receipt.corporate-program.mail.client-view', $data, function ($message) use ($data, $receiptAttachment) {
                $message->to($data['email'], $data['recipient'])
                    ->cc($data['cc'])
                    ->subject($data['title'])
                    ->attach($receiptAttachment->attachment);
            });

            $attachmentDetails = [
                'send_to_client' => 'sent',
            ];

            $this->receiptAttachmentRepository->updateReceiptAttachment($receiptAttachment->id, $attachmentDetails);
        } catch (Exception $e) {

            Log::info('Failed to send receipt to client : ' . $e->getMessage());
            return false;
        }

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
}
