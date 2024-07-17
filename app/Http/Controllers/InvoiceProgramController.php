<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttachmentRequest;
use App\Http\Requests\StoreInvoiceProgramBundleRequest;
use App\Http\Requests\StoreInvoiceProgramBundlingRequest;
use App\Http\Requests\StoreInvoiceProgramRequest;
use App\Http\Traits\CreateInvoiceIdTrait;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\InvoiceAttachmentRepositoryInterface;
use App\Interfaces\InvoiceDetailRepositoryInterface;
use App\Interfaces\InvoiceProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Jobs\Invoice\ProcessEmailRequestSignJob;
use App\Jobs\Invoice\ProcessEmailToClientJob;
use App\Models\InvoiceProgram;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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
    use LoggingTrait;
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

        # s is stand for status
        # and going to be used as a parameter that going to be shown
        $status = $request->get('s') !== NULL ? $request->get('s') : null;
        $isBundle = $request->get('b') !== NULL ? true : false;
        if ($request->ajax()) {

            # when is bundle is set to true
            # meaning, view will be shown bundling programs
            if ($isBundle)
                return $this->invoiceProgramRepository->getProgramBundle_InvoiceProgram($status);

            # else
            return $this->invoiceProgramRepository->getAllInvoiceProgramDataTables($status);
        }

        return view('pages.invoice.client-program.index', [
            'status' => $status,
            'isBundle' => $isBundle
        ]);
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

        # validation invoice bundle
        # master invoice bundle must be created first
        if($request->is_bundle > 0 && !isset($clientProg->bundlingDetail->bundling->invoice_b2c)){
            return Redirect::to('invoice/client-program/create?prog=' . $request->clientprog_id)->withError('Create master invoice bundle first!');
        }

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
                'created_at' => $request->invoice_date,
                'inv_duedate' => $request->inv_duedate,
                'inv_notes' => $request->inv_notes,
                'inv_tnc' => $request->inv_tnc
            ];
            $param = "other";
        }

        $invoiceDetails['inv_category'] = $invoiceDetails['is_session'] == "yes" ? "session" : $param;
        $invoiceDetails['session'] = isset($invoiceDetails['session']) && $invoiceDetails['session'] != 0 ? $invoiceDetails['session'] : 0;
        if ($currency !== null)
            $invoiceDetails['currency'] = $currency;

        $invoiceDetails['inv_paymentmethod'] = $invoiceDetails['inv_paymentmethod'] == "full" ? 'Full Payment' : 'Installment';

        $invoiceDetails['created_at'] = Carbon::parse($invoiceDetails['invoice_date'] . ' ' . date('H:i:s'));

        DB::beginTransaction();
        try {

            $last_id = InvoiceProgram::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->max(DB::raw('substr(inv_id, 1, 4)'));
            # Use Trait Create Invoice Id
            $inv_id = $this->getInvoiceId($last_id, $clientProg->prog_id, $invoiceDetails['invoice_date']);
            
            if($request->is_bundle > 0){
                $last_id = InvoiceProgram::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('bundling_id', $clientProg->bundlingDetail->bundling_id)->max(DB::raw('substr(inv_id, 1, 4)'));
                
                $bundlingDetails = $this->clientProgramRepository->getBundleProgramDetailByBundlingId($clientProg->bundlingDetail->bundling_id);

                $clientIdsBundle = $incrementBundle = [];
                $is_cross_client = false;
                
                foreach ($bundlingDetails as $key => $bundlingDetail) {
                    $incrementBundle[$bundlingDetail->client_program->clientprog_id] = $key + 1;
                    $clientIdsBundle[] = $bundlingDetail->client_program->client->id;
                }

                if(count(array_count_values($clientIdsBundle)) > 1)
                    $is_cross_client = true;

                # Use Trait Create Invoice Id
                $inv_id = $this->getInvoiceId($last_id, $clientProg->prog_id, $invoiceDetails['invoice_date'], ['is_bundle' => $request->is_bundle, 'is_cross_client' => $is_cross_client, 'increment_bundle' => $incrementBundle[$clientProgId]]);
            }

            $invoiceProgramCreated = $this->invoiceProgramRepository->createInvoice(['inv_id' => $inv_id, 'inv_status' => 1] + $invoiceDetails);
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
            Log::error('Store invoice program failed : ' . $e->getMessage() . ' | Line: ' . $e->getLine());
            return Redirect::to('invoice/client-program/create?prog=' . $request->clientprog_id)->withError('Failed to store invoice program');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Invoice Client Program', Auth::user()->first_name . ' '. Auth::user()->last_name, $invoiceProgramCreated);

        return Redirect::to('invoice/client-program/' . $clientProgId)->withSuccess('Invoice has been created');
    }

    public function create(Request $request)
    {
        # call GET parameters
        
        if(isset($request->bundle) && $request->bundle)
            return app('App\Http\Controllers\InvoiceProgramBundleController')->createBundle($request->bundle);

        $incomingRequestProg = $request->prog;
        
        if (!isset($incomingRequestProg) or !$clientProg = $this->clientProgramRepository->getClientProgramById($incomingRequestProg)){
            return Redirect::to('invoice/client-program?s=needed')->withError('We cannot continue the process at this time. Please try again later.');
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
        

        $invoiceDetails['inv_category'] = $invoiceDetails['is_session'] == "yes" ? "session" : $invoiceDetails['currency'];
        $invoiceDetails['session'] = isset($invoiceDetails['session']) && $invoiceDetails['session'] != 0 ? $invoiceDetails['session'] : 0;
        $invoiceDetails['currency'] = $currency == "other" ? $invoiceDetails['currency_detail'] : $currency;
        $invoiceDetails['inv_paymentmethod'] = $invoiceDetails['inv_paymentmethod'] == "full" ? 'Full Payment' : 'Installment';
        unset($invoiceDetails['currency_detail']);

        $invoiceDetails['created_at'] = Carbon::parse($invoiceDetails['invoice_date'] . ' ' . date('H:i:s'));

        DB::beginTransaction();
        try {

            # when created date / invoice date has changed 
            # then check if old invoice_id same or not with the new invoice id using created at
            if ( date('Y-m-d', strtotime($invoice->created_at)) != $invoiceDetails['created_at']) {
                
                // $last_id = InvoiceProgram::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->max(DB::raw('substr(inv_id, 1, 4)'));
                $last_id = InvoiceProgram::whereMonth('created_at', Carbon::parse($invoiceDetails['created_at'])->format('m'))->whereYear('created_at', Carbon::parse($invoiceDetails['created_at'])->format('Y'))->max(DB::raw('substr(inv_id, 1, 4)'));
    
                # Use Trait Create Invoice Id
                $new_inv_id = $this->getInvoiceId($last_id, $invoice->clientprog->prog_id, $invoiceDetails['invoice_date']);
                $invoiceDetails['inv_id'] = substr($inv_id, 0, 4) == $last_id ? $inv_id : $new_inv_id;

                if($request->is_bundle > 0){
                    // $last_id = InvoiceProgram::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('bundling_id', $invoice->clientprog->bundlingDetail->bundling_id)->max(DB::raw('substr(inv_id, 1, 4)'));
                    $last_id = InvoiceProgram::whereMonth('created_at', Carbon::parse($invoiceDetails['created_at'])->format('m'))->whereYear('created_at', Carbon::parse($invoiceDetails['created_at'])->format('Y'))->where('bundling_id', $invoice->clientprog->bundlingDetail->bundling_id)->max(DB::raw('substr(inv_id, 1, 4)'));
                    
                    $bundlingDetails = $this->clientProgramRepository->getBundleProgramDetailByBundlingId($invoice->clientprog->bundlingDetail->bundling_id);
    
                    $clientIdsBundle = $incrementBundle = [];
                    $is_cross_client = false;
                    
                    foreach ($bundlingDetails as $key => $bundlingDetail) {
                        $incrementBundle[$bundlingDetail->client_program->clientprog_id] = $key + 1;
                        $clientIdsBundle[] = $bundlingDetail->client_program->client->id;
                    }
    
                    if(count(array_count_values($clientIdsBundle)) > 1)
                        $is_cross_client = true;
    
                    # Use Trait Create Invoice Id
                    $new_inv_id = $this->getInvoiceId($last_id, $invoice->clientprog->prog_id, $invoiceDetails['invoice_date'], ['is_bundle' => $request->is_bundle, 'is_cross_client' => $is_cross_client, 'increment_bundle' => $incrementBundle[$clientProgId]]);
                    $invoiceDetails['inv_id'] = substr($inv_id, 0, 4) == $last_id ? $inv_id : $new_inv_id;
                }
            }

            # update invoice 
            $update = $this->invoiceProgramRepository->updateInvoice($inv_id, $invoiceDetails);
            $invoiceWasChanged = $update['invoiceWasChanged'];

            # if there was a change to invoice
            # delete the invoice attachment in order to finance able to do the request sign
            if ($invoiceWasChanged === true) 
                $this->invoiceAttachmentRepository->deleteInvoiceAttachmentByInvoiceId($inv_id);
            


            # do this if payment method was changed from installment to full payment
            # check if invoice has installments
            # then remove it
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
                        'inv_id' => $new_inv_id,
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

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Invoice Client Program', Auth::user()->first_name . ' '. Auth::user()->last_name, $invoiceDetails, $invoice);

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
        $invProg = $this->invoiceProgramRepository->getInvoiceByClientProgId($clientProgId);
        DB::beginTransaction();
        try {

            $this->invoiceProgramRepository->deleteInvoiceByClientProgId($clientProgId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete invoice program failed : ' . $e->getMessage());
            return Redirect::to('invoice/client-program/' . $clientProgId)->withError('Failed to delete invoice program');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Invoice Client Program', Auth::user()->first_name . ' '. Auth::user()->last_name, $invProg);

        return Redirect::to('invoice/client-program?s=needed')->withSuccess('Invoice has been deleted');
    }

    public function export(Request $request)
    {
        $clientprog_id = $request->route('client_program');
        $clientProg = $this->clientProgramRepository->getClientProgramById($clientprog_id);

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
        $to = $request->get('to'); # our director mail
        $name = $request->get('name'); # our director name

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

            # these data used for generate PDF file that will be sent into email
            $attachmentDetails = [
                'view' => $view,
                'invoice_id' => $invoice_id,
                'client_prog' => $clientProg,
                'company_detail' => $companyDetail, 
                'director' => $name,
                'file_name' => $file_name
            ];

            # dispatching the job to the queue
            ProcessEmailRequestSignJob::dispatch($data, $attachmentDetails, $invoice_id)->onQueue('inv-email-request-sign');
            
        } catch (Exception $e) {

            Log::info('Failed to request sign invoice : ' . $e->getMessage() . ' | Line ' . $e->getLine());
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }

        # Request Sign success
        # create log success
        $this->logSuccess('request-sign', null, 'Invoice Client Program', Auth::user()->first_name . ' '. Auth::user()->last_name, ['invoice_id' => $invoice_id]);

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
        $type_recipient = $request->route('type_recipient');
        $clientProg = $this->clientProgramRepository->getClientProgramById($clientprog_id);
        $invoice = $clientProg->invoice;
        $invoice_id = $invoice->inv_id;
        $currency = $request->route('currency');
        $attachment = $invoice->invoiceAttachment()->where('currency', $currency)->first();

        $pic_mail = $clientProg->internalPic->email;
        

        switch ($type_recipient) {
            case 'Parent':
                $data['email'] = $clientProg->client->parents[0]->mail;
                $data['recipient'] = $clientProg->client->parents[0]->full_name;
                break;

            case 'Client':
                $data['email'] = $clientProg->client->mail;
                $data['recipient'] = $clientProg->client->full_name;
                break;
        }

        $data['cc'] = [
            env('CEO_CC'),
            env('FINANCE_CC'),
            $pic_mail
        ];
        $data['param'] = [
            'clientprog_id' => $clientprog_id,
            'program_name' => $clientProg->program->program_name
        ];
        $data['title'] = "Invoice of program " . $clientProg->program->program_name;

        try {

            ProcessEmailToClientJob::dispatch($data, $attachment, $this->invoiceAttachmentRepository, $invoice_id)->onQueue('inv-send-to-client');

        } catch (Exception $e) {

            Log::info('Failed to send invoice to client : ' . $e->getMessage());
            return response()->json(['message' => 'Failed to send invoice to client.'], 500);
        }

        # Send To Client success
        # create log success
        $this->logSuccess('send-to-client', null, 'Invoice Client Program', Auth::user()->first_name . ' '. Auth::user()->last_name, ['invoice_id' => $invoice_id, 'recipient' => $type_recipient]);

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

        # Signed success
        # create log success
        $this->logSuccess('signed', null, 'Invoice Client Program', 'Director', ['invoice_id' => $inv_id]);

        return response()->json(['status' => 'success', 'message' => 'Invoice signed successfully']);
    }

    private function previewFromDashboard($currency, $clientProg, $director)
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

        $pdf = PDF::loadView($view, ['clientProg' => $clientProg, 'companyDetail' => $companyDetail, 'director' => $director]);
        return $pdf->stream();
    }

    public function preview(Request $request)
    {
        $clientprog_id = $request->route('client_program');
        $currency = $request->route('currency');

        # query
        $preview = $request->get('key');
        $director = $request->get('dir');

        if (!$clientProg = $this->clientProgramRepository->getClientProgramById($clientprog_id))
            abort(404);

        if ($preview == 'dashboard') {
            return $this->previewFromDashboard($currency, $clientProg, $director);
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
        # sendTo is determined when clicking the recipient modal
        # whether parent or children
        $sendTo = $request->sendTo;

        $parent = $request->parent_id != null ? $this->clientRepository->getClientById($request->parent_id) : null;

        switch ($sendTo) {

            case "parent":
                $client = $this->clientRepository->getClientById($parent != null ? $request->parent_id : $request->client_id);
                break;

            case "child":
                $client = $this->clientRepository->getClientById($request->client_id); # client id means child id
                break;

        }
        
        # get the data from blade
        $target_fullname = $client->full_name;
        $phone = $request->phone; # this value is from input name target_phone in blade view, will be changed depends on sendTo

        # check if the phone number has changed or not
        # if the phone number changed then update the client data on database
        if ($client->phone != $phone) {

            Log::debug('Phone number changed from : '.$client->phone.' to : '.$phone);
            $this->clientRepository->updateClient($client->id, ['phone' => $phone]);
        }

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

        $text = $parent != null ? "Dear Mr/Mrs " . $target_fullname . "," : "Dear " . $client->first_name . " " . $client->last_name . ",";
        $text .= "%0A";
        $text .= "%0A";
        $text .= "Thank you for trusting EduALL as your independent university consultant to help your child reach their dream to top universities.";
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


        return response()->json(['status' => 'success', 'message' => 'Success Update Email Client'], 200);
    }

}
