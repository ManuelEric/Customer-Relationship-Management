<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttachmentRequest;
use App\Http\Requests\StoreInvoiceProgramRequest;
use App\Http\Traits\CreateInvoiceIdTrait;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\InvoiceAttachmentRepositoryInterface;
use App\Interfaces\InvoiceDetailRepositoryInterface;
use App\Interfaces\InvoiceProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Models\InvoiceProgram;
use DateTime;
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
    private ClientRepositoryInterface $clientRepository;

    public function __construct(InvoiceProgramRepositoryInterface $invoiceProgramRepository, ClientProgramRepositoryInterface $clientProgramRepository, InvoiceDetailRepositoryInterface $invoiceDetailRepository, InvoiceAttachmentRepositoryInterface $invoiceAttachmentRepository, ClientRepositoryInterface $clientRepository)
    {
        $this->invoiceProgramRepository = $invoiceProgramRepository;
        $this->clientProgramRepository = $clientProgramRepository;
        $this->invoiceDetailRepository = $invoiceDetailRepository;
        $this->invoiceAttachmentRepository = $invoiceAttachmentRepository;
        $this->clientRepository = $clientRepository;
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

            $last_id = InvoiceProgram::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->max(DB::raw('substr(inv_id, 1, 4)'));

            # Use Trait Create Invoice Id
            $inv_id = $this->getInvoiceId($last_id, $clientProg->prog_id);

            $this->invoiceProgramRepository->createInvoice(['inv_id' => $inv_id, 'inv_status' => 1] + $invoiceDetails);
            // $this->invoiceProgramRepository->createInvoice(['inv_id' => $inv_id, 'inv_status' => 0] + $invoiceDetails);

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

        return Redirect::to('invoice/client-program/' . $clientProgId)->withSuccess('Invoice has been created');
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
                'invoice' => null
            ]
        );
    }

    public function update(StoreInvoiceProgramRequest $request)
    {
        $clientProgId = $request->clientprog_id;

        $invoice = $this->invoiceProgramRepository->getInvoiceByClientProgId($clientProgId);
        $inv_id = $invoice->inv_id;

        # fetching currency till get the currency
        // $currency = null;
        $currency = $request->currency;
        $is_session = $request->is_session;

        switch ($request->currency) {

            case "idr":
                if ($is_session == "no") {

                    $invoiceDetails = $request->only([
                        'clientprog_id',
                        'currency',
                        'currency_detail',
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
                } else if ($is_session == "yes") {

                    $invoiceDetails = [
                        'clientprog_id' => $request->clientprog_id,
                        'currency' => $request->currency,
                        'currency_detail' => $request->currency_detail,
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
                }
                break;

            case "other":
                if ($is_session == "no") {

                    $invoiceDetails = [
                        'clientprog_id' => $request->clientprog_id,
                        'currency' => $request->currency,
                        'currency_detail' => $request->currency_detail,
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
                } else if ($is_session == "yes") {

                    $invoiceDetails = [
                        'clientprog_id' => $request->clientprog_id,
                        'currency' => $request->currency,
                        'currency_detail' => $request->currency_detail,
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

                break;
        }

        # old code
        // foreach ($request->currency as $key => $val) {
        //     if ($val != NULL)
        //         $currency = $val != "other" ? $val : null;
        // }

        // if (in_array('idr', $request->currency) && $request->is_session == "no") {

        //     $invoiceDetails = $request->only([
        //         'clientprog_id',
        //         'currency',
        //         'is_session',
        //         'inv_price_idr',
        //         'inv_earlybird_idr',
        //         'inv_discount_idr',
        //         'inv_totalprice_idr',
        //         'inv_words_idr',
        //         'inv_paymentmethod',
        //         'invoice_date',
        //         'inv_duedate',
        //         'inv_notes',
        //         'inv_tnc'
        //     ]);
        //     $param = "idr";
        // } elseif (in_array('idr', $request->currency) && $request->is_session == "yes") {

        //     $invoiceDetails = [
        //         'clientprog_id' => $request->clientprog_id,
        //         'currency' => $request->currency,
        //         'is_session' => $request->is_session,
        //         'session' => $request->session__si,
        //         'duration' => $request->duration__si,
        //         'inv_price_idr' => $request->inv_price_idr__si,
        //         'inv_earlybird_idr' => $request->inv_earlybird_idr__si,
        //         'inv_discount_idr' => $request->inv_discount_idr__si,
        //         'inv_totalprice_idr' => $request->inv_totalprice_idr__si,
        //         'inv_words_idr' => $request->inv_words_idr__si,
        //         'inv_paymentmethod' => $request->inv_paymentmethod,
        //         'invoice_date' => $request->invoice_date,
        //         'inv_duedate' => $request->inv_duedate,
        //         'inv_notes' => $request->inv_notes,
        //         'inv_tnc' => $request->inv_tnc
        //     ];
        //     $param = "idr";
        // } elseif (in_array('other', $request->currency) && $request->is_session == "no") {

        //     $invoiceDetails = [
        //         'clientprog_id' => $request->clientprog_id,
        //         'currency' => $request->currency,
        //         'curs_rate' => $request->curs_rate,
        //         'is_session' => $request->is_session,
        //         'inv_price' => $request->inv_price__nso,
        //         'inv_earlybird' => $request->inv_earlybird__nso,
        //         'inv_discount' => $request->inv_discount__nso,
        //         'inv_totalprice' => $request->inv_totalprice__nso,
        //         'inv_words' => $request->inv_words__nso,
        //         'inv_price_idr' => $request->inv_price_idr__nso,
        //         'inv_earlybird_idr' => $request->inv_earlybird_idr__nso,
        //         'inv_discount_idr' => $request->inv_discount_idr__nso,
        //         'inv_totalprice_idr' => $request->inv_totalprice_idr__nso,
        //         'inv_words_idr' => $request->inv_words_idr__nso,
        //         'inv_paymentmethod' => $request->inv_paymentmethod,
        //         'invoice_date' => $request->invoice_date,
        //         'inv_duedate' => $request->inv_duedate,
        //         'inv_notes' => $request->inv_notes,
        //         'inv_tnc' => $request->inv_tnc
        //     ];
        //     $param = "other";
        // } elseif (in_array('other', $request->currency) && $request->is_session == "yes") {

        //     $invoiceDetails = [
        //         'clientprog_id' => $request->clientprog_id,
        //         'currency' => $request->currency,
        //         'curs_rate' => $request->curs_rate,
        //         'is_session' => $request->is_session,
        //         'session' => $request->session__so,
        //         'duration' => $request->duration__so,
        //         'inv_price' => $request->inv_price__so,
        //         'inv_earlybird' => $request->inv_earlybird__so,
        //         'inv_discount' => $request->inv_discount__so,
        //         'inv_totalprice' => $request->inv_totalprice__so,
        //         'inv_words' => $request->inv_words__so,
        //         'inv_price_idr' => $request->inv_price_idr__so,
        //         'inv_earlybird_idr' => $request->inv_earlybird_idr__so,
        //         'inv_discount_idr' => $request->inv_discount_idr__so,
        //         'inv_totalprice_idr' => $request->inv_totalprice_idr__so,
        //         'inv_words_idr' => $request->inv_words_idr__so,
        //         'inv_paymentmethod' => $request->inv_paymentmethod,
        //         'invoice_date' => $request->invoice_date,
        //         'inv_duedate' => $request->inv_duedate,
        //         'inv_notes' => $request->inv_notes,
        //         'inv_tnc' => $request->inv_tnc
        //     ];
        //     $param = "other";
        // }
        # end of old code

        // $invoiceDetails['inv_category'] = $invoiceDetails['is_session'] == "yes" ? "session" : $invoiceDetails['currency'][0];
        $invoiceDetails['inv_category'] = $invoiceDetails['is_session'] == "yes" ? "session" : $invoiceDetails['currency'];
        $invoiceDetails['session'] = isset($invoiceDetails['session']) && $invoiceDetails['session'] != 0 ? $invoiceDetails['session'] : 0;
        $invoiceDetails['currency'] = $currency == "other" ? $invoiceDetails['currency_detail'] : $currency;
        $invoiceDetails['inv_paymentmethod'] = $invoiceDetails['inv_paymentmethod'] == "full" ? 'Full Payment' : 'Installment';
        unset($invoiceDetails['currency_detail']);

        DB::beginTransaction();
        try {

            $this->invoiceProgramRepository->updateInvoice($inv_id, $invoiceDetails);

            # check if invoice has installments
            # if yes then remove it when status updated from installment to full payment
            if (isset($invoice->invoiceDetail) && $invoice->invoiceDetail->count() > 0)
                $this->invoiceDetailRepository->deleteInvoiceDetailByInvId($inv_id);

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

            # if update invoice success
            # then delete the invoice attachment
            if ($invoice->invoiceAttachment->count() > 0)
                $this->invoiceAttachmentRepository->deleteInvoiceAttachmentByInvoiceId($inv_id);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update invoice program failed : ' . $e->getMessage());
            return Redirect::to('invoice/client-program/' . $request->clientprog_id . '/edit')->withError('Failed to update invoice program');
        }

        return Redirect::to('invoice/client-program/' . $clientProgId)->withSuccess('Invoice has been updated');
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

        $invoice = $clientProg->invoice;
        /* START ~ */
        $currency = $request->route('currency'); # this variable not used from client program detail page
        # use variable below instead
        $currency = $invoice->currency;

        if ($currency != 'idr')
            $currency = 'other';
        /* ~ END */

        $attachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('Program', $invoice->inv_id, $currency);

        return view('pages.invoice.view-pdf')->with(
            [
                'invoice' => $invoice,
                // 'attachment' => $attachment->attachment
                'attachment' => $attachment
            ]
        );
    }

    public function requestSign(Request $request)
    {
        $clientprog_id = $request->route('client_program');
        $clientProg = $this->clientProgramRepository->getClientProgramById($clientprog_id);
        $invoice_id = $clientProg->invoice->inv_id;

        $type = $request->get('type');
        $to = $request->get('to');
        $name = $request->get('name');

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

        $data['email'] = $to;
        $data['recipient'] = $name;
        $data['title'] = "Request Sign of Invoice Number : " . $invoice_id;
        $data['param'] = [
            'clientprog_id' => $clientprog_id,
            'currency' => $type,
            'fullname' => $clientProg->client->full_name,
            'program_name' => $clientProg->program->program_name,
            'invoice_date' => date('d F Y', strtotime($clientProg->invoice->created_at)),
            'invoice_duedate' => date('d F Y', strtotime($clientProg->invoice->inv_duedate))
        ];

        $attachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('Program', $invoice_id, $type);

        try {

            # generate invoice as a PDF file
            $file_name = str_replace('/', '-', $invoice_id) . '-' . $type;
            $pdf = PDF::loadView($view, ['clientProg' => $clientProg, 'companyDetail' => $companyDetail]);
            Storage::put('public/uploaded_file/invoice/client/' . $file_name . '.pdf', $pdf->output());

            # insert to invoice attachment
            $attachmentDetails = [
                'inv_id' => $invoice_id,
                'currency' => $type,
                'sign_status' => 'not yet',
                'recipient' => $to,
                'send_to_client' => 'not sent',
                'attachment' => $file_name . '.pdf'
            ];

            if (isset($attachment)) {
                $this->invoiceAttachmentRepository->updateInvoiceAttachment($attachment->id, $attachmentDetails);
            } else {
                $this->invoiceAttachmentRepository->createInvoiceAttachment($attachmentDetails);
            }

            # send email to related person that has authority to give a signature
            Mail::send('pages.invoice.client-program.mail.view', $data, function ($message) use ($data, $pdf, $invoice_id) {
                $message->to($data['email'], $data['recipient'])
                    ->subject($data['title'])
                    ->attachData($pdf->output(), $invoice_id . '.pdf');
            });
        } catch (Exception $e) {

            Log::info('Failed to request sign invoice : ' . $e->getMessage() . ' | Line ' . $e->getLine());
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }

        return response()->json(['message' => 'Invoice sent successfully.']);
    }

    public function download(Request $request)
    {
        $clientprog_id = $request->client_program;
        $clientProg = $this->clientProgramRepository->getClientProgramById($clientprog_id);
        $invoice = $clientProg->invoice;

        return response()->download(storage_path('app/public/uploaded_file/invoice/' . $invoice->attachment));
    }

    public function sendToClient(Request $request)
    {
        $clientprog_id = $request->route('client_program');
        $clientProg = $this->clientProgramRepository->getClientProgramById($clientprog_id);
        $invoice = $clientProg->invoice;
        $invoice_id = $invoice->inv_id;
        $currency = $request->route('currency');
        $attachment = $invoice->invoiceAttachment()->where('currency', $currency)->first();

        $pic_mail = $clientProg->internalPic->email;


        $data['email'] = $clientProg->client->parents[0]->mail;
        // $data['email'] = $clientProg->client->mail;
        $data['cc'] = [
            env('CEO_CC'),
            env('FINANCE_CC'),
            $pic_mail
        ];
        $data['recipient'] = $clientProg->client->parents[0]->full_name;
        // $data['recipient'] = $clientProg->client->full_name;
        $data['param'] = [
            'clientprog_id' => $clientprog_id,
            'program_name' => $clientProg->program->program_name
        ];
        $data['title'] = "Invoice of program " . $clientProg->program->program_name;

        try {

            Mail::send('pages.invoice.client-program.mail.client-view', $data, function ($message) use ($data, $attachment) {
                $message->to($data['email'], $data['recipient'])
                    ->cc($data['cc'])
                    ->subject($data['title'])
                    ->attach(storage_path('app/public/uploaded_file/invoice/client/' . $attachment->attachment));
            });

            # update status send to client
            $newDetails['send_to_client'] = 'sent';
            !$this->invoiceAttachmentRepository->updateInvoiceAttachment($attachment->id, $newDetails);
        } catch (Exception $e) {

            Log::info('Failed to send invoice to client : ' . $e->getMessage());
            return response()->json(['message' => 'Failed to send invoice to client.'], 500);
        }

        return response()->json(['message' => 'Successfully sent invoice to client.']);
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
        $file_name = str_replace('/', '-', $inv_id) . '-' . $currency . '.pdf';

        $attachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('Program', $inv_id, $currency);

        $newDetails = [
            'sign_status' => 'signed',
            'approve_date' => Carbon::now()
        ];

        DB::beginTransaction();
        try {

            $this->invoiceAttachmentRepository->updateInvoiceAttachment($attachment->id, $newDetails);
            if (!$pdfFile->storeAs('public/uploaded_file/invoice/client/', $file_name))
                throw new Exception('Failed to store signed invoice file');

            $data['title'] = 'Invoice No. ' . $inv_id . ' has been signed';
            $data['inv_id'] = $inv_id;

            # send mail when document has been signed
            Mail::send('pages.invoice.client-program.mail.signed', $data, function ($message) use ($data, $file_name) {
                $message->to(env('FINANCE_CC'), env('FINANCE_NAME'))
                    ->subject($data['title'])
                    ->attach(storage_path('app/public/uploaded_file/invoice/client/' . $file_name));
            });

            DB::commit();
        } catch (Exception $e) {

            Log::error('Failed to update status after being signed : ' . $e->getMessage());
            return response()->json(['status' => 'success', 'message' => 'Failed to update'], 500);
        }

        return response()->json(['status' => 'success', 'message' => 'Invoice signed successfully']);
    }

    private function previewFromDashboard($currency, $clientProg)
    {
        if ($currency == "idr")
            $view = 'pages.invoice.client-program.export.invoice-pdf';
        else
            $view = 'pages.invoice.client-program.export.invoice-pdf-foreign';

        $companyDetail = [
            'name' => env('ALLIN_COMPANY'),
            'address' => env('ALLIN_ADDRESS'),
            'address_dtl' => env('ALLIN_ADDRESS_DTL'),
            'city' => env('ALLIN_CITY')
        ];

        $pdf = PDF::loadView($view, ['clientProg' => $clientProg, 'companyDetail' => $companyDetail]);
        return $pdf->stream();
    }

    public function preview(Request $request)
    {
        $clientprog_id = $request->route('client_program');
        $currency = $request->route('currency');
        $preview = $request->get('key');

        if (!$clientProg = $this->clientProgramRepository->getClientProgramById($clientprog_id))
            abort(404);

        if ($preview == 'dashboard') {
            return $this->previewFromDashboard($currency, $clientProg);
        }


        $invoice = $clientProg->invoice;

        $attachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('Program', $invoice->inv_id, $currency);

        return view('pages.invoice.sign-pdf')->with(
            [
                'invoice' => $invoice,
                'attachment' => $attachment
            ]
        );
    }

    public function print(Request $request)
    {
        $clientprog_id = $request->route('client_program');
        $currency = $request->route('currency');

        if (!$clientProg = $this->clientProgramRepository->getClientProgramById($clientprog_id))
            abort(404);

        $invoice = $clientProg->invoice;
        $attachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('Program', $invoice->inv_id, $currency);

        return view('pages.invoice.view-pdf')->with(
            [
                'invoice' => $invoice,
                'attachment' => $attachment
            ]
        );
    }

    # handler for reminder parents to pay
    public function remindParentsByEmail(Request $request)
    {
        $invoiceId = $request->invoice_id;
        $invoice_master = $this->invoiceProgramRepository->getInvoiceByInvoiceId($invoiceId);
        $clientprogram_master = $invoice_master->clientprog;
        $program_name = ucwords(strtolower($clientprogram_master->invoice_program_name));
        $child_master = $clientprogram_master->client;
        $parents_master = $clientprogram_master->client->parents;
        $one_hisher_parent_information = $parents_master[0];
        $parent_fullname = $one_hisher_parent_information->full_name;
        $parent_mail = $one_hisher_parent_information->mail;
        if ($parent_mail === null)
            // throw new Exception('Reminder cannot be send without a parent\'s mail. Please complete the parent\'s information.');
            return response()->json(['message' => 'Reminder cannot be send without a parent\'s mail. Please complete the parent\'s information.'], 500);

        $subject = '7 Days Left until the Payment Deadline for ' . $program_name;

        $params = [
            'parent_fullname' => $parent_fullname,
            'parent_mail' => $parent_mail,
            'program_name' => $program_name,
            'due_date' => date('d/m/Y', strtotime($invoice_master->inv_duedate)),
            'child_fullname' => $child_master->full_name,
            'installment_notes' => $clientprogram_master->installment_notes,
            'total_payment' => $invoice_master->invoice_price_idr,
        ];

        $mail_resources = 'pages.invoice.client-program.mail.reminder-payment';

        try {
            Mail::send($mail_resources, $params, function ($message) use ($params, $subject) {
                $message->to($params['parent_mail'], $params['parent_fullname'])
                    ->cc(env('FINANCE_CC'))
                    ->subject($subject);
            });
        } catch (Exception $e) {

            Log::error($e->getMessage() . ' | Line ' . $e->getLine());
            return response()->json(['message' => $e->getMessage()]);
        }

        return response()->json(['message' => 'Reminder for ' . $parent_fullname . ' has been sent.']);
    }

    public function remindParentsByWhatsapp(Request $request)
    {
        $parent = $request->parent_id != null ? $this->clientRepository->getClientById($request->parent_id) : null;
        $client = $this->clientRepository->getClientById($parent != null ? $request->parent_id : $request->client_id);

        $parent_fullname = $request->parent_fullname;
        $phone = $request->phone;

        $client->phone != $phone ? $this->clientRepository->updateClient($client->id, ['phone' => $phone]) : null;

        $joined_program_name = ucwords(strtolower($request->program_name));
        $joined_program_name = str_replace('&', '%26', $joined_program_name);
        $invoice_duedate = date('d/m/Y', strtotime($request->invoice_duedate));
        $total_payment = "Rp. " . number_format($request->total_payment, '2', ',', '.');

        $datetime_1 = new DateTime($request->invoice_duedate);
        $datetime_2 = new DateTime(date('Y-m-d'));
        $interval = $datetime_1->diff($datetime_2);
        $date_diff = $interval->format('%a'); # format for the interval : days

        $payment_method = '';
        if ($request->payment_method != 'Full Payment') {
            $payment_method = $request->payment_method;
        }
        // $payment_method = $request->payment_method != 'Full Payment' ? ' (' . $request->payment_method . ')' : '';

        $text = $parent != null ? "Dear Mr/Mrs " . $parent_fullname . "," : "Dear " . $client->first_name . " " . $client->last_name . ",";
        $text .= "%0A";
        $text .= "%0A";
        $text .= "Thank you for trusting ALL-in Eduspace as your independent university consultant to help your child reach their dream to top universities.";
        $text .= "%0A";
        $text .= "%0A";
        $text .= "Through this message, we would like to remind you that the payment deadline for " . $joined_program_name . " " . $payment_method . " is due on " . $invoice_duedate . " or in " . $date_diff . " days.";
        $text .= "%0A";
        $text .= "%0A";
        $text .= "Amount: " . $total_payment;
        $text .= "%0A";
        $text .= "%0A";
        $text .= "Payment could be done through bank transfer to: BCA 2483016611 a/n PT Jawara Edukasih Indonesia.";
        $text .= "%0A";
        $text .= "%0A";
        $text .= "Thank you. Please ignore this message if payment has been made.";
        $text .= "%0A";
        $text .= "%0A";
        $text .= "Regards";

        $link = "https://api.whatsapp.com/send?phone=" . $phone . "&text=" . $text;
        // echo "<script>window.open('" . $link . "', '_blank')</script>";
        // return redirect()->to($link);
        return response()->json(['link' => $link]);
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
