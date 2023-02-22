<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceB2bRequest;
use App\Http\Requests\StoreInvoiceReferralRequest;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\ReferralRepositoryInterface;
use App\Interfaces\InvoiceAttachmentRepositoryInterface;
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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use PDF;


class InvoiceReferralController extends Controller
{
    use CreateInvoiceIdTrait;
    protected ReferralRepositoryInterface $referralRepository;
    protected ProgramRepositoryInterface $programRepository;
    protected InvoiceAttachmentRepositoryInterface $invoiceAttachmentRepository;
    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    protected InvoiceDetailRepositoryInterface $invoiceDetailRepository;
    protected ReceiptRepositoryInterface $receiptRepository;
    protected CorporateRepositoryInterface $corporateRepository;

    public function __construct(ReferralRepositoryInterface $referralRepository, ProgramRepositoryInterface $programRepository, InvoiceB2bRepositoryInterface $invoiceB2bRepository, InvoiceDetailRepositoryInterface $invoiceDetailRepository, ReceiptRepositoryInterface $receiptRepository, InvoiceAttachmentRepositoryInterface $invoiceAttachmentRepository, CorporateRepositoryInterface $corporateRepository)
    {
        $this->referralRepository = $referralRepository;
        $this->programRepository = $programRepository;
        $this->invoiceAttachmentRepository = $invoiceAttachmentRepository;
        $this->invoiceB2bRepository = $invoiceB2bRepository;
        $this->invoiceDetailRepository = $invoiceDetailRepository;
        $this->receiptRepository = $receiptRepository;
        $this->corporateRepository = $corporateRepository;
    }

    public function index(Request $request)
    {
        $status = $request->route('status');

        if ($request->ajax()) {
            switch ($status) {
                case 'needed':
                    return $this->invoiceB2bRepository->getAllInvoiceNeededReferralDataTables();
                    break;
                case 'list':
                    return $this->invoiceB2bRepository->getAllInvoiceReferralDataTables();
                    break;
            }
        }

        return view('pages.invoice.referral.index')->with(['status' => $status]);
    }

    public function create(Request $request)
    {
        $ref_id = $request->route('referral');

        $referral = $this->referralRepository->getReferralById($ref_id);

        $partnerId = $referral->partner_id;

        # retrieve corp data by id
        $partner = $this->corporateRepository->getCorporateById($partnerId);

        return view('pages.invoice.referral.form')->with(
            [
                'referral' => $referral,
                'partner' => $partner,
                'status' => 'create',
            ]
        );
    }

    public function store(StoreInvoiceReferralRequest $request)
    {

        $ref_id = $request->route('referral');
        $invoices = $request->only([
            'select_currency',
            'currency',
            'curs_rate',
            'invb2b_totpriceidr',
            'invb2b_totpriceidr_other',
            'invb2b_totprice',
            'invb2b_wordsidr',
            'invb2b_wordsidr_other',
            'invb2b_words',
            'invb2b_pm',
            'invb2b_date',
            'invb2b_duedate',
            'invb2b_notes',
            'invb2b_tnc',
        ]);


        switch ($invoices['select_currency']) {
            case 'other':
                $invoices['invb2b_totpriceidr'] = $invoices['invb2b_totpriceidr_other'];
                $invoices['invb2b_wordsidr'] = $invoices['invb2b_wordsidr_other'];
                break;

            case 'idr':
                $invoices['currency'] = 'idr';
                unset($invoices['invb2b_totprice']);
                unset($invoices['invb2b_words']);
                // unset($invoices['currency']);
                break;
        }

        unset($invoices['invb2b_totpriceidr_other']);
        unset($invoices['invb2b_wordsidr_other']);


        $now = Carbon::now();
        $thisMonth = $now->month;

        $last_id = Invb2b::whereMonth('created_at', $thisMonth)->max(DB::raw('substr(invb2b_id, 1, 4)'));

        // Use Trait Create Invoice Id
        $inv_id = $this->getInvoiceId($last_id, 'REF-OUT');

        $invoices['invb2b_id'] = $inv_id;
        $invoices['ref_id'] = $ref_id;

        DB::beginTransaction();
        try {

            $this->invoiceB2bRepository->createInvoiceB2b($invoices);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Create invoice failed : ' . $e->getMessage());

            return Redirect::to('invoice/referral/' . $ref_id . '/detail/create')->withError('Failed to create a new invoice');
        }

        return Redirect::to('invoice/referral/status/list')->withSuccess('Invoice successfully created');
    }

    public function show(Request $request)
    {
        $ref_id = $request->route('referral');
        $invNum = $request->route('detail');

        $referral = $this->referralRepository->getReferralById($ref_id);

        $partnerId = $referral->partner_id;

        $partner = $this->corporateRepository->getCorporateById($partnerId);

        $invoiceRef = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);


        return view('pages.invoice.referral.form')->with(
            [
                'referral' => $referral,
                'partner' => $partner,
                'invoiceRef' => $invoiceRef,
                'status' => 'show',
            ]
        );
    }

    public function edit(Request $request)
    {
        $invNum = $request->route('detail');
        $ref_id = $request->route('referral');

        $referral = $this->referralRepository->getReferralById($ref_id);

        $partnerId = $referral->partner_id;

        $partner = $this->corporateRepository->getCorporateById($partnerId);

        $invoiceRef = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);

        return view('pages.invoice.referral.form')->with(
            [
                'status' => 'edit',
                'referral' => $referral,
                'partner' => $partner,
                'invoiceRef' => $invoiceRef,
            ]
        );
    }

    public function update(StoreInvoiceReferralRequest $request)
    {

        $ref_id = $request->route('referral');
        $invNum = $request->route('detail');

        $invoices = $request->only([
            'select_currency',
            'currency',
            'curs_rate',
            'invb2b_totpriceidr',
            'invb2b_totpriceidr_other',
            'invb2b_totprice',
            'invb2b_wordsidr',
            'invb2b_wordsidr_other',
            'invb2b_words',
            'invb2b_pm',
            'invb2b_date',
            'invb2b_duedate',
            'invb2b_notes',
            'invb2b_tnc',
        ]);

        switch ($invoices['select_currency']) {
            case 'other':
                $invoices['invb2b_totpriceidr'] = $invoices['invb2b_totpriceidr_other'];
                $invoices['invb2b_wordsidr'] = $invoices['invb2b_wordsidr_other'];
                break;

            case 'idr':
                unset($invoices['invb2b_totprice']);
                unset($invoices['invb2b_words']);
                unset($invoices['currency']);
                break;
        }

        unset($invoices['invb2b_totpriceidr_other']);
        unset($invoices['invb2b_wordsidr_other']);

        $invoices['ref_id'] = $ref_id;

        DB::beginTransaction();
        try {

            $this->invoiceB2bRepository->updateInvoiceB2b($invNum, $invoices);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update invoice failed : ' . $e->getMessage());

            return $e->getMessage();
            exit;
            return Redirect::to('invoice/referral/' . $ref_id . '/detail/' . $invNum)->withError('Failed to update invoice');
        }

        return Redirect::to('invoice/referral/' . $ref_id . '/detail/' . $invNum)->withSuccess('Invoice successfully updated');
    }

    public function destroy(Request $request)
    {
        $invNum = $request->route('detail');
        $ref_id = $request->route('referral');

        DB::beginTransaction();
        try {

            $this->invoiceB2bRepository->deleteInvoiceB2b($invNum);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete invoice failed : ' . $e->getMessage());

            return Redirect::to('invoice/referral/' . $ref_id . '/detail/' . $invNum)->withError('Failed to delete invoice');
        }

        return Redirect::to('invoice/referral/status/list')->withSuccess('Invoice successfully deleted');
    }

    public function export(Request $request)
    {
        $invNum = $request->route('invoice');
        $currency = $request->route('currency');

        $invoiceRef = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);
        $invoice_id = $invoiceRef->invb2b_id;

        $invoiceAttachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('B2B', $invoice_id, $currency);

        return view('pages.invoice.view-pdf')->with([
            'invoiceAttachment' => $invoiceAttachment,
        ]);
    }

    public function requestSign(Request $request)
    {
        $invNum = $request->route('invoice');
        $currency = $request->route('currency');

        $invoiceRef = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);
        $invoice_id = $invoiceRef->invb2b_id;
        $invoice_num = $invoiceRef->invb2b_num;
        $file_name = str_replace('/', '_', $invoice_id) . '_' . ($currency == 'idr' ? $currency : 'other') . '.pdf'; # 0001_INV_JEI_EF_I_23_idr.pdf
        $path = 'uploaded_file/invoice/referral/';
        $attachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('B2B', $invoice_id, $currency);

        $attachmentDetails = [
            'invb2b_id' => $invoice_id,
            'currency' => $currency,
            'attachment' => 'storage/' . $path . $file_name,
        ];

        // $companyDetail = [
        //     'name' => env('ALLIN_COMPANY'),
        //     'address' => env('ALLIN_ADDRESS'),
        //     'address_dtl' => env('ALLIN_ADDRESS_DTL'),
        //     'city' => env('ALLIN_CITY')
        // ];

        $data['email'] = 'test@gmail.com';
        $data['recipient'] = 'test name';
        $data['title'] = "Request Sign of Invoice Number : " . $invoice_id;
        $data['param'] = [
            'invb2b_num' => $invoice_num,
            'currency' => $currency,
        ];

        try {

            $pdf = PDF::loadView('pages.invoice.referral.export.invoice-pdf', [
                'invoiceRef' => $invoiceRef,
                'currency' => $currency,
                // 'companyDetail' => $companyDetail
            ]);

            // # Generate PDF file
            $content = $pdf->download();
            Storage::disk('public')->put($path . $file_name, $content);

            # if attachment exist then update attachement else insert attachement
            if (isset($attachment)) {
                $this->invoiceAttachmentRepository->updateInvoiceAttachment($attachment->id, $attachmentDetails);
            } else {
                $this->invoiceAttachmentRepository->createInvoiceAttachment($attachmentDetails);
            }

            Mail::send('pages.invoice.referral.mail.view', $data, function ($message) use ($data) {
                $message->to($data['email'], $data['recipient'])
                    ->subject($data['title']);
            });
        } catch (Exception $e) {

            Log::info('Failed to request sign invoice : ' . $e->getMessage());
            return $e->getMessage();
        }

        return true;
    }

    public function signAttachment(Request $request)
    {
        if (Session::token() != $request->get('token')) {
            return "Your session token is expired";
        }

        $invNum = $request->route('invoice');
        $currency = $request->route('currency');
        $invoiceRef = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);
        $invoice_id = $invoiceRef->invb2b_id;
        $invoiceAttachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('B2B', $invoice_id, $currency);

        if (isset($invoiceAttachment->sign_status) && $invoiceAttachment->sign_status == 'signed') {
            return "Invoice is already signed";
        }

        return view('pages.invoice.sign-pdf')->with(
            [
                'attachment' => $invoiceAttachment->attachment,
                'currency' => $currency,
                'invoice' => $invoiceRef,
            ]
        );
    }

    public function upload(Request $request)
    {
        $pdfFile = $request->file('pdfFile');
        $name = $request->file('pdfFile')->getClientOriginalName();
        $invNum = $request->route('invoice');
        $invoiceRef = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);
        $invoice_id = $invoiceRef->invb2b_id;
        $currency = $request->route('currency');

        $invoiceAttachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('B2B', $invoice_id, $currency);

        if ($pdfFile->storeAs('public/uploaded_file/invoice/referral/', $name)) {

            $attachmentDetails = [
                'sign_status' => 'signed',
                'approve_date' => Carbon::now()->toDateString()
            ];

            $this->invoiceAttachmentRepository->updateInvoiceAttachment($invoiceAttachment->id, $attachmentDetails);

            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'error']);
        }
    }

    public function sendToClient(Request $request)
    {
        $invNum = $request->route('invoice');
        $currency = $request->route('currency');
        $invoiceRef = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);
        $invoice_id = $invoiceRef->invb2b_id;
        $invoiceAttachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('B2B', $invoice_id, $currency);
        $program_name = $invoiceRef->referral->additional_prog_name;

        $data['email'] = $invoiceRef->referral->user->email;
        $data['cc'] = ['test1@example.com', 'test2@example.com'];
        $data['recipient'] = 'Test Name';
        $data['title'] = "ALL-In Eduspace | Invoice of program : " . $program_name;
        $data['param'] = [
            'invb2b_num' => $invNum,
            'currency' => $currency,
        ];

        try {

            Mail::send('pages.invoice.referral.mail.client-view', $data, function ($message) use ($data, $invoiceAttachment) {
                $message->to($data['email'], $data['recipient'])
                    ->cc($data['cc'])
                    ->subject($data['title'])
                    ->attach(public_path($invoiceAttachment->attachment));
            });

            $attachmentDetails = [
                'send_to_client' => 'sent',
            ];

            $this->invoiceAttachmentRepository->updateInvoiceAttachment($invoiceAttachment->id, $attachmentDetails);
        } catch (Exception $e) {

            Log::info('Failed to send invoice to client : ' . $e->getMessage());
            return false;
        }

        return true;
    }
}
