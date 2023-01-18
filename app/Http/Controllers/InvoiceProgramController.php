<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceProgramRequest;
use App\Http\Traits\CreateInvoiceIdTrait;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\InvoiceDetailRepositoryInterface;
use App\Interfaces\InvoiceProgramRepositoryInterface;
use App\Models\InvoiceProgram;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use PDF;

class InvoiceProgramController extends Controller
{
    use CreateInvoiceIdTrait;
    private InvoiceProgramRepositoryInterface $invoiceProgramRepository;
    private ClientProgramRepositoryInterface $clientProgramRepository;
    private InvoiceDetailRepositoryInterface $invoiceDetailRepository;

    public function __construct(InvoiceProgramRepositoryInterface $invoiceProgramRepository, ClientProgramRepositoryInterface $clientProgramRepository, InvoiceDetailRepositoryInterface $invoiceDetailRepository)
    {
        $this->invoiceProgramRepository = $invoiceProgramRepository;
        $this->clientProgramRepository = $clientProgramRepository;
        $this->invoiceDetailRepository = $invoiceDetailRepository;
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
        $raw_currency[1] = $request->currency_detail;

        # fetching currency till get the currency
        $currency = null;
        foreach ($raw_currency as $key => $val) {
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

                for ($i = 0 ; $i < $limit ; $i++) {

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
            return Redirect::to('invoice/client-program/create?prog='.$request->clientprog_id)->withError('Failed to store invoice program');

        }

        return Redirect::to('invoice/client-program?s=list')->withSuccess('Invoice has been created');

    }

    public function create(Request $request)
    {
        if (!isset($request->prog) OR !$clientProg = $this->clientProgramRepository->getClientProgramById($request->prog)){
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

                for ($i = 0 ; $i < $limit ; $i++) {

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
            return Redirect::to('invoice/client-program/'.$request->clientprog_id.'/edit')->withError('Failed to update invoice program');

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
            return Redirect::to('invoice/client-program/'.$clientProgId)->withError('Failed to delete invoice program');

        }

        return Redirect::to('invoice/client-program?s=needed')->withSuccess('Invoice has been deleted');
    }

    public function export(Request $request)
    {
        $clientprog_id = $request->route('client_program');
        $clientProg = $this->clientProgramRepository->getClientProgramById($clientprog_id);
        $invoice_id = $clientProg->invoice->inv_id;

        $companyDetail = [
            'name' => env('ALLIN_COMPANY'),
            'address' => env('ALLIN_ADDRESS'),
            'address_dtl' => env('ALLIN_ADDRESS_DTL'),
            'city' => env('ALLIN_CITY')
        ];

        $pdf = PDF::loadView('pages.invoice.client-program.export.invoice-pdf', ['clientProg' => $clientProg, 'companyDetail' => $companyDetail]);
        return $pdf->download($invoice_id.".pdf");

        // return view('pages.invoice.client-program.export.invoice-pdf')->with(
        //     [
        //         'clientProg' => $clientProg, 'companyDetail' => $companyDetail, 'is_session' => true
        //     ]
        // );
    }
}
