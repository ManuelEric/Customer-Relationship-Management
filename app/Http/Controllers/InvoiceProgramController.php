<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttachmentRequest;
use App\Http\Requests\StoreInvoiceProgramRequest;
use App\Http\Traits\CreateInvoiceIdTrait;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\InvoiceAttachmentRepositoryInterface;
use App\Interfaces\InvoiceDetailRepositoryInterface;
use App\Interfaces\InvoiceProgramRepositoryInterface;
use App\Models\InvoiceProgram;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use PDF;

class InvoiceProgramController extends Controller
{
    use CreateInvoiceIdTrait;
    private InvoiceProgramRepositoryInterface $invoiceProgramRepository;
    private ClientProgramRepositoryInterface $clientProgramRepository;
    private InvoiceDetailRepositoryInterface $invoiceDetailRepository;
    private InvoiceAttachmentRepositoryInterface $invoiceAttachmentRepository;

    public function __construct(InvoiceProgramRepositoryInterface $invoiceProgramRepository, ClientProgramRepositoryInterface $clientProgramRepository, InvoiceDetailRepositoryInterface $invoiceDetailRepository, InvoiceAttachmentRepositoryInterface $invoiceAttachmentRepository)
    {
        $this->invoiceProgramRepository = $invoiceProgramRepository;
        $this->clientProgramRepository = $clientProgramRepository;
        $this->invoiceDetailRepository = $invoiceDetailRepository;
        $this->invoiceAttachmentRepository = $invoiceAttachmentRepository;
    }

    public function index(Request $request)
    {
        $status = $request->get('s') !== NULL ? $request->get('s') : null;
        // return $this->invoiceProgramRepository->getAllInvoiceProgramDataTables($status);
        if ($request->ajax())
            return $this->invoiceProgramRepository->getAllInvoiceProgramDataTables($status);

        return view('pages.invoice.client-program.index', ['status' => $status]);
    }

    public function show(Request $request)
    {
        $clientProgId = $request->route('client_program');
        $clientProg = $this->clientProgramRepository->getClientProgramById($clientProgId);
        $invoice = $this->invoiceProgramRepository->getInvoiceByClientProgId($clientProgId);

        return view('pages.invoice.client-program.form')->with(
            [
                'status' => 'view',
                'clientProg' => $clientProg,
                'invoice' => $invoice
            ]
        );
    }

    public function store(StoreInvoiceProgramRequest $request)
    {
        $clientProgId = $request->clientprog_id;
        $clientProg = $this->clientProgramRepository->getClientProgramById($clientProgId);

        $raw_currency = [];
        $raw_currency[0] = $request->currency;
        $raw_currency[1] = $request->currency != "idr" ? $request->currency_detail : null;
        # fetching currency till get the currency
        $currency = null;
        foreach ($raw_currency as $key => $val) {
            if ($val != NULL)
                $currency = $val != "other" ? $val : null;
        }

        if (in_array('idr', $raw_currency) && $request->is_session == "no") {

            $invoiceDetails = $request->only([
                'clientprog_id',
                'currency',
                'is_session',
                'inv_price_idr',
                'inv_earlybird_idr',
                'inv_discount_idr',
                'inv_totalprice_idr',
                'inv_words_idr',
                'inv_paymentmethod',
                'invoice_date',
                'inv_duedate',
                'inv_notes',
                'inv_tnc'
            ]);
            $param = "idr";
        } elseif (in_array('idr', $raw_currency) && $request->is_session == "yes") {

            $invoiceDetails = [
                'clientprog_id' => $request->clientprog_id,
                'currency' => $request->currency,
                'is_session' => $request->is_session,
                'session' => $request->session__si,
                'duration' => $request->duration__si,
                'inv_price_idr' => $request->inv_price_idr__si,
                'inv_earlybird_idr' => $request->inv_earlybird_idr__si,
                'inv_discount_idr' => $request->inv_discount_idr__si,
                'inv_totalprice_idr' => $request->inv_totalprice_idr__si,
                'inv_words_idr' => $request->inv_words_idr__si,
                'inv_paymentmethod' => $request->inv_paymentmethod,
                'invoice_date' => $request->invoice_date,
                'inv_duedate' => $request->inv_duedate,
                'inv_notes' => $request->inv_notes,
                'inv_tnc' => $request->inv_tnc
            ];
            $param = "idr";
        } elseif (in_array('other', $raw_currency) && $request->is_session == "no") {

            $invoiceDetails = [
                'clientprog_id' => $request->clientprog_id,
                'currency' => $request->currency,
                'curs_rate' => $request->curs_rate,
                'is_session' => $request->is_session,
                'inv_price' => $request->inv_price__nso,
                'inv_earlybird' => $request->inv_earlybird__nso,
                'inv_discount' => $request->inv_discount__nso,
                'inv_totalprice' => $request->inv_totalprice__nso,
                'inv_words' => $request->inv_words__nso,
                'inv_price_idr' => $request->inv_price_idr__nso,
                'inv_earlybird_idr' => $request->inv_earlybird_idr__nso,
                'inv_discount_idr' => $request->inv_discount_idr__nso,
                'inv_totalprice_idr' => $request->inv_totalprice_idr__nso,
                'inv_words_idr' => $request->inv_words_idr__nso,
                'inv_paymentmethod' => $request->inv_paymentmethod,
                'invoice_date' => $request->invoice_date,
                'inv_duedate' => $request->inv_duedate,
                'inv_notes' => $request->inv_notes,
                'inv_tnc' => $request->inv_tnc
            ];
            $param = "other";
        } elseif (in_array('other', $raw_currency) && $request->is_session == "yes") {

            $invoiceDetails = [
                'clientprog_id' => $request->clientprog_id,
                'currency' => $request->currency,
                'curs_rate' => $request->curs_rate,
                'is_session' => $request->is_session,
                'session' => $request->session__so,
                'duration' => $request->duration__so,
                'inv_price' => $request->inv_price__so,
                'inv_earlybird' => $request->inv_earlybird__so,
                'inv_discount' => $request->inv_discount__so,
                'inv_totalprice' => $request->inv_totalprice__so,
                'inv_words' => $request->inv_words__so,
                'inv_price_idr' => $request->inv_price_idr__so,
                'inv_earlybird_idr' => $request->inv_earlybird_idr__so,
                'inv_discount_idr' => $request->inv_discount_idr__so,
                'inv_totalprice_idr' => $request->inv_totalprice_idr__so,
                'inv_words_idr' => $request->inv_words_idr__so,
                'inv_paymentmethod' => $request->inv_paymentmethod,
                'invoice_date' => $request->invoice_date,
                'inv_duedate' => $request->inv_duedate,
                'inv_notes' => $request->inv_notes,
                'inv_tnc' => $request->inv_tnc
            ];
            $param = "other";
        }

        $invoiceDetails['inv_category'] = $invoiceDetails['is_session'] == "yes" ? "session" : $param;
        $invoiceDetails['session'] = isset($invoiceDetails['session']) && $invoiceDetails['session'] != 0 ? $invoiceDetails['session'] : 0;
        $invoiceDetails['currency'] = $currency;
        $invoiceDetails['inv_paymentmethod'] = $invoiceDetails['inv_paymentmethod'] == "full" ? 'Full Payment' : 'Installment';

        DB::beginTransaction();
        try {

            $last_id = InvoiceProgram::whereMonth('created_at', date('m'))->max(DB::raw('substr(inv_id, 1, 4)'));

            # Use Trait Create Invoice Id
            $inv_id = $this->getInvoiceId($last_id, $clientProg->prog_id);

            $this->invoiceProgramRepository->createInvoice(['inv_id' => $inv_id, 'inv_status' => 0] + $invoiceDetails);

            # add installment details
            # check if installment information has been filled
            # either idr or other currency

            if ($invoiceDetails['inv_paymentmethod'] == "Installment") {

                # and using param to fetch data based on rupiah or other currency
                $limit = $param == "idr" ? count($request->invdtl_installment) : count($request->invdtl_installment__other);

                for ($i = 0; $i < $limit; $i++) {

                    $installmentDetails[$i] = [
                        'inv_id' => $inv_id,
                        'invdtl_installment' => $param == "idr" ? $request->invdtl_installment[$i] : $request->invdtl_installment__other[$i],
                        'invdtl_duedate' => $param == "idr" ? $request->invdtl_duedate[$i] : $request->invdtl_duedate__other[$i],
                        'invdtl_percentage' => $param == "idr" ? $request->invdtl_percentage[$i] : $request->invdtl_percentage__other[$i],
                        'invdtl_amountidr' => $param == "idr" ? $request->invdtl_amountidr[$i] : $request->invdtl_amountidr__other[$i],
                        'invdtl_cursrate' => $param == "other" ? $invoiceDetails['curs_rate'] : null,
                        'invdtl_currency' => $invoiceDetails['currency'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];

                    if ($param == "other")
                        $installmentDetails[$i]['invdtl_amount'] = $request->invdtl_amount__other[$i];
                }

                $this->invoiceDetailRepository->createInvoiceDetail($installmentDetails);
            }

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store invoice program failed : ' . $e->getMessage());
            return Redirect::to('invoice/client-program/create?prog=' . $request->clientprog_id)->withError('Failed to store invoice program');
        }

        return Redirect::to('invoice/client-program?s=list')->withSuccess('Invoice has been created');
    }

    public function create(Request $request)
    {
        if (!isset($request->prog) or !$clientProg = $this->clientProgramRepository->getClientProgramById($request->prog)) {
            return Redirect::to('invoice/client-program?s=needed');
        }

        return view('pages.invoice.client-program.form')->with(
            [
                'status' => 'create',
                'clientProg' => $clientProg,
                'invoice' => null,
            ]
        );
    }

    public function update(StoreInvoiceProgramRequest $request)
    {
        $clientProgId = $request->clientprog_id;

        $invoice = $this->invoiceProgramRepository->getInvoiceByClientProgId($clientProgId);
        $inv_id = $invoice->inv_id;

        # fetching currency till get the currency
        $currency = null;
        foreach ($request->currency as $key => $val) {
            if ($val != NULL)
                $currency = $val != "other" ? $val : null;
        }

        if (in_array('idr', $request->currency) && $request->is_session == "no") {

            $invoiceDetails = $request->only([
                'clientprog_id',
                'currency',
                'is_session',
                'inv_price_idr',
                'inv_earlybird_idr',
                'inv_discount_idr',
                'inv_totalprice_idr',
                'inv_words_idr',
                'inv_paymentmethod',
                'invoice_date',
                'inv_duedate',
                'inv_notes',
                'inv_tnc'
            ]);
            $param = "idr";
        } elseif (in_array('idr', $request->currency) && $request->is_session == "yes") {

            $invoiceDetails = [
                'clientprog_id' => $request->clientprog_id,
                'currency' => $request->currency,
                'is_session' => $request->is_session,
                'session' => $request->session__si,
                'duration' => $request->duration__si,
                'inv_price_idr' => $request->inv_price_idr__si,
                'inv_earlybird_idr' => $request->inv_earlybird_idr__si,
                'inv_discount_idr' => $request->inv_discount_idr__si,
                'inv_totalprice_idr' => $request->inv_totalprice_idr__si,
                'inv_words_idr' => $request->inv_words_idr__si,
                'inv_paymentmethod' => $request->inv_paymentmethod,
                'invoice_date' => $request->invoice_date,
                'inv_duedate' => $request->inv_duedate,
                'inv_notes' => $request->inv_notes,
                'inv_tnc' => $request->inv_tnc
            ];
            $param = "idr";
        } elseif (in_array('other', $request->currency) && $request->is_session == "no") {

            $invoiceDetails = [
                'clientprog_id' => $request->clientprog_id,
                'currency' => $request->currency,
                'curs_rate' => $request->curs_rate,
                'is_session' => $request->is_session,
                'inv_price' => $request->inv_price__nso,
                'inv_earlybird' => $request->inv_earlybird__nso,
                'inv_discount' => $request->inv_discount__nso,
                'inv_totalprice' => $request->inv_totalprice__nso,
                'inv_words' => $request->inv_words__nso,
                'inv_price_idr' => $request->inv_price_idr__nso,
                'inv_earlybird_idr' => $request->inv_earlybird_idr__nso,
                'inv_discount_idr' => $request->inv_discount_idr__nso,
                'inv_totalprice_idr' => $request->inv_totalprice_idr__nso,
                'inv_words_idr' => $request->inv_words_idr__nso,
                'inv_paymentmethod' => $request->inv_paymentmethod,
                'invoice_date' => $request->invoice_date,
                'inv_duedate' => $request->inv_duedate,
                'inv_notes' => $request->inv_notes,
                'inv_tnc' => $request->inv_tnc
            ];
            $param = "other";
        } elseif (in_array('other', $request->currency) && $request->is_session == "yes") {

            $invoiceDetails = [
                'clientprog_id' => $request->clientprog_id,
                'currency' => $request->currency,
                'curs_rate' => $request->curs_rate,
                'is_session' => $request->is_session,
                'session' => $request->session__so,
                'duration' => $request->duration__so,
                'inv_price' => $request->inv_price__so,
                'inv_earlybird' => $request->inv_earlybird__so,
                'inv_discount' => $request->inv_discount__so,
                'inv_totalprice' => $request->inv_totalprice__so,
                'inv_words' => $request->inv_words__so,
                'inv_price_idr' => $request->inv_price_idr__so,
                'inv_earlybird_idr' => $request->inv_earlybird_idr__so,
                'inv_discount_idr' => $request->inv_discount_idr__so,
                'inv_totalprice_idr' => $request->inv_totalprice_idr__so,
                'inv_words_idr' => $request->inv_words_idr__so,
                'inv_paymentmethod' => $request->inv_paymentmethod,
                'invoice_date' => $request->invoice_date,
                'inv_duedate' => $request->inv_duedate,
                'inv_notes' => $request->inv_notes,
                'inv_tnc' => $request->inv_tnc
            ];
            $param = "other";
        }

        $invoiceDetails['inv_category'] = $invoiceDetails['is_session'] == "yes" ? "session" : $invoiceDetails['currency'][0];
        $invoiceDetails['session'] = isset($invoiceDetails['session']) && $invoiceDetails['session'] != 0 ? $invoiceDetails['session'] : 0;
        $invoiceDetails['currency'] = $currency;
        $invoiceDetails['inv_paymentmethod'] = $invoiceDetails['inv_paymentmethod'] == "full" ? 'Full Payment' : 'Installment';

        DB::beginTransaction();
        try {

            $this->invoiceProgramRepository->updateInvoice($inv_id, $invoiceDetails);

            # add installment details
            # check if installment information has been filled
            # either idr or other currency
            if ($invoiceDetails['inv_paymentmethod'] == "Installment") {

                # and using param to fetch data based on rupiah or other currency
                $limit = $param == "idr" ? count($request->invdtl_installment) : count($request->invdtl_installment__other);

                for ($i = 0; $i < $limit; $i++) {

                    $installmentDetails[$i] = [
                        'inv_id' => $inv_id,
                        'invdtl_installment' => $param == "idr" ? $request->invdtl_installment[$i] : $request->invdtl_installment__other[$i],
                        'invdtl_duedate' => $param == "idr" ? $request->invdtl_duedate[$i] : $request->invdtl_duedate__other[$i],
                        'invdtl_percentage' => $param == "idr" ? $request->invdtl_percentage[$i] : $request->invdtl_percentage__other[$i],
                        'invdtl_amountidr' => $param == "idr" ? $request->invdtl_amountidr[$i] : $request->invdtl_amountidr__other[$i],
                        'invdtl_cursrate' => $param == "other" ? $invoiceDetails['curs_rate'] : null,
                        'invdtl_currency' => $invoiceDetails['currency'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];

                    if ($param == "other")
                        $installmentDetails[$i]['invdtl_amount'] = $request->invdtl_amount__other[$i];
                }

                $this->invoiceDetailRepository->updateInvoiceDetailByInvId($inv_id, $installmentDetails);
            }
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update invoice program failed : ' . $e->getMessage());
            return Redirect::to('invoice/client-program/' . $request->clientprog_id . '/edit')->withError('Failed to update invoice program');
        }

        return Redirect::to('invoice/client-program?s=list')->withSuccess('Invoice has been updated');
    }

    public function edit(Request $request)
    {
        $clientProgId = $request->route('client_program');
        $clientProg = $this->clientProgramRepository->getClientProgramById($clientProgId);
        $invoice = $this->invoiceProgramRepository->getInvoiceByClientProgId($clientProgId);

        return view('pages.invoice.client-program.form')->with(
            [
                'status' => 'edit',
                'clientProg' => $clientProg,
                'invoice' => $invoice
            ]
        );
    }

    public function destroy(Request $request)
    {
        $clientProgId = $request->route('client_program');
        DB::beginTransaction();
        try {

            $this->invoiceProgramRepository->deleteInvoiceByClientProgId($clientProgId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete invoice program failed : ' . $e->getMessage());
            return Redirect::to('invoice/client-program/' . $clientProgId)->withError('Failed to delete invoice program');
        }

        return Redirect::to('invoice/client-program?s=needed')->withSuccess('Invoice has been deleted');
    }

    public function export(Request $request)
    {
        $clientprog_id = $request->route('client_program');
        $clientProg = $this->clientProgramRepository->getClientProgramById($clientprog_id);
        $invoice_id = $clientProg->invoice->inv_id;

        $type = $request->get('type');

        if ($type == "idr")
            $view = 'pages.invoice.client-program.export.invoice-pdf';
        else
            $view = 'pages.invoice.client-program.export.invoice-pdf-foreign';

        $companyDetail = [
            'name' => env('ALLIN_COMPANY'),
            'address' => env('ALLIN_ADDRESS'),
            'address_dtl' => env('ALLIN_ADDRESS_DTL'),
            'city' => env('ALLIN_CITY')
        ];

        // $pdf = PDF::loadView($view, ['clientProg' => $clientProg, 'companyDetail' => $companyDetail]);
        // return $pdf->download($invoice_id . ".pdf");

        $currency = $request->route('currency');
        
        $invoice = $clientProg->invoice;
        $attachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('Program', $invoice->inv_id, $currency);

        return view('pages.invoice.view-pdf')->with(
            [
                'invoice' => $invoice,
                'attachment' => $attachment->attachment
            ]
        );
    }

    public function requestSign(Request $request)
    {
        $clientprog_id = $request->route('client_program');
        $clientProg = $this->clientProgramRepository->getClientProgramById($clientprog_id);
        $invoice_id = $clientProg->invoice->inv_id;

        $type = $request->get('type');

        # validate 
        # if the invoice has already requested to be signed
        if ($this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('Program', $invoice_id, $type))
            return response()->json(['message' => 'Invoice has already been requested to be signed.'], 500);

        if ($type == "idr")
            $view = 'pages.invoice.client-program.export.invoice-pdf';
        else
            $view = 'pages.invoice.client-program.export.invoice-pdf-foreign';

        $companyDetail = [
            'name' => env('ALLIN_COMPANY'),
            'address' => env('ALLIN_ADDRESS'),
            'address_dtl' => env('ALLIN_ADDRESS_DTL'),
            'city' => env('ALLIN_CITY')
        ];

        $data['email'] = env('DIRECTOR_EMAIL');
        $data['recipient'] = env('DIRECTOR_NAME');
        $data['title'] = "Request Sign of Invoice Number : " . $invoice_id;
        $data['param'] = [
            'clientprog_id' => $clientprog_id,
            'currency' => $type
        ];

        try {
            
            # generate invoice as a PDF file
            $file_name = str_replace('/', '_', $invoice_id);
            $pdf = PDF::loadView($view, ['clientProg' => $clientProg, 'companyDetail' => $companyDetail]);
            Storage::put('public/uploaded_file/invoice/'.$file_name.'.pdf', $pdf->output());
            
            # insert to invoice attachment
            $attachmentDetails = [
                'inv_id' => $invoice_id,
                'currency' => $type,
                'sign_status' => 'not yet',
                'send_to_client' => 'not sent',
                'attachment' => $file_name.'.pdf'
            ];
            $this->invoiceAttachmentRepository->createInvoiceAttachment($attachmentDetails);

            # send email to related person that has authority to give a signature
            Mail::send('pages.invoice.client-program.mail.view', $data, function ($message) use ($data, $pdf, $invoice_id) {
                $message->to($data['email'], $data['recipient'])
                    ->subject($data['title'])
                    ->attachData($pdf->output(), $invoice_id . '.pdf');
            });
            
        } catch (Exception $e) {

            Log::info('Failed to request sign invoice : ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }

        return response()->json(['message' => 'Invoice sent successfully.']);

    }

    public function createSignedAttachment(Request $request)
    {
        // if (Session::token() != $request->get('token')) {
        //     return "Your session token is expired";
        // }

        $clientprog_id = $request->route('client_program');
        $clientProg = $this->clientProgramRepository->getClientProgramById($clientprog_id);

        return view('pages.invoice.client-program.upload.view')->with(
            [
                'clientProg' => $clientProg,
            ]
        );
    }

    public function storeSignedAttachment(StoreAttachmentRequest $request)
    {
        $invoice_id = $request->invoice_id;
        $attachmentDetails = $request->only([
            'signed_attachment',
        ]);

        $file_format = $request->file('signed_attachment')->getClientOriginalExtension();

        DB::beginTransaction();
        try {

            # proses store attachment here
            if (!$request->hasFile('signed_attachment')) {
                throw new Exception('Please upload your file');
            }

            $file_name = str_replace('/', '_', $invoice_id);
            $file_format = $request->file('signed_attachment')->getClientOriginalExtension();
            $file_path = $request->file('signed_attachment')->storeAs('public/uploaded_file/invoice/', $file_name . '.' . $file_format);

            unset($attachmentDetails['signed_attachment']);
            $attachmentDetails['attachment'] = $file_name . '.' . $file_format;
            $this->invoiceProgramRepository->updateInvoice($invoice_id, $attachmentDetails);


            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::info('Upload signed attachment invoice failed : ' . $e->getMessage());
            return false;
        }

        return true;
    }

    public function download(Request $request)
    {
        $clientprog_id = $request->client_program;
        $clientProg = $this->clientProgramRepository->getClientProgramById($clientprog_id);
        $invoice = $clientProg->invoice;

        // return storage_path('app/public/uploaded_file/invoice/'.$invoice->attachment);
        return response()->download(storage_path('app/public/uploaded_file/invoice/' . $invoice->attachment));
    }

    public function sendToClient(Request $request)
    {
        $clientprog_id = $request->client_program;
        $clientProg = $this->clientProgramRepository->getClientProgramById($clientprog_id);
        $invoice = $clientProg->invoice;
        $invoice_id = $invoice->inv_id;

        $data['email'] = env('DIRECTOR_EMAIL');
        $data['recipient'] = env('DIRECTOR_NAME');
        $data['title'] = "ALL-In Eduspace | Invoice of program : " . $clientProg->program_name;
        $data['param'] = [
            'clientprog_id' => $clientprog_id
        ];

        try {

            Mail::send('pages.invoice.client-program.mail.client-view', $data, function ($message) use ($data, $invoice) {
                $message->to($data['email'], $data['recipient'])
                    ->subject($data['title'])
                    ->attach(storage_path('app/public/uploaded_file/invoice/' . $invoice->attachment));
            });

            # update status send to client
            $newDetails['send_to_client'] = 'sent';
            $this->invoiceProgramRepository->updateInvoice($invoice_id, $newDetails);
        } catch (Exception $e) {

            Log::info('Failed to send invoice to client : ' . $e->getMessage());
            return false;
        }

        return true;
    }

    public function upload(Request $request)
    {
        $pdfFile = $request->file('pdfFile');
        $name = $request->file('pdfFile')->getClientOriginalName();

        $clientprog_id = $request->route('client_program');
        $clientProg = $this->clientProgramRepository->getClientProgramById($clientprog_id);
        $invoice = $clientProg->invoice;
        $inv_id = $invoice->inv_id;
        $currency = $request->route('currency');

        $attachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('Program', $inv_id, $currency);

        $newDetails = [
            'sign_status' => 'signed',
            'approve_date' => Carbon::now()
        ];

        DB::beginTransaction();
        try {

            $this->invoiceAttachmentRepository->updateInvoiceAttachment($attachment->id, $newDetails);
            if (!$pdfFile->storeAs('public/uploaded_file/invoice/', $name))
                throw new Exception('Failed to store signed invoice file');

            DB::commit();

        } catch (Exception $e) {

            Log::error('Failed to update status after being signed : ' . $e->getMessage());
            return response()->json(['status' => 'success', 'message' => 'Failed to update'], 500);

        }

        return response()->json(['status' => 'success', 'message' => 'Invoice signed successfully']);
    }

    public function preview(Request $request)
    {
        $clientprog_id = $request->route('client_program');
        $currency = $request->route('currency');

        if (!$clientProg = $this->clientProgramRepository->getClientProgramById($clientprog_id))
            abort(404);
        
        $invoice = $clientProg->invoice;
        $attachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('Program', $invoice->inv_id, $currency);

        return view('pages.invoice.sign-pdf')->with(
            [
                'invoice' => $invoice,
                'attachment' => $attachment->attachment
            ]
        );
        // return view('pages.invoice.view-pdf');
    }
}
