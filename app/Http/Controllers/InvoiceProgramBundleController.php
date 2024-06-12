<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceProgramBundleRequest;
use App\Http\Traits\CreateInvoiceIdTrait;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\InvoiceAttachmentRepositoryInterface;
use App\Interfaces\InvoiceDetailRepositoryInterface;
use App\Interfaces\InvoiceProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Jobs\Invoice\ProcessEmailRequestSignJob;
use App\Jobs\Invoice\ProcessEmailRequestSignJobBundle;
use App\Jobs\Invoice\ProcessEmailToClientJob;
use App\Models\InvoiceProgram;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use PDF;
use DateTime;

class InvoiceProgramBundleController extends Controller
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

    public function createBundle($uuid)
    {
        $incomingRequestBundle = $uuid;

        if (!isset($incomingRequestBundle) or !$bundle = $this->clientProgramRepository->getBundleProgramByUUID($incomingRequestBundle)){
            return Redirect::to('invoice/client-program?s=needed&b=true')->withError('We cannot continue the process at this time. Please try again later.');
        }

        return view('pages.invoice.client-program.form-bundle')->with(
            [
                'status' => 'create',
                'bundle' => $bundle,
                'invoice' => null
            ]
        );
    }

    public function storeBundle(StoreInvoiceProgramBundleRequest $request)
    {
        $bundlingId = $request->bundling_id;
        $bundling = $this->clientProgramRepository->getClientProgramById($bundlingId);

        $raw_currency = [];
        $raw_currency[0] = $request->currency;
        $raw_currency[1] = $request->currency != "idr" ? $request->currency_detail : null;
        # fetching currency till get the currency
        $currency = null;
        foreach ($raw_currency as $key => $val) {
            if ($val != NULL)
                $currency = $val != "other" ? $val : null;
        }

        if (in_array('idr', $raw_currency)) {

            $invoiceDetails = $request->only([
                'bundling_id',
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
        } elseif (in_array('other', $raw_currency)) {

            $invoiceDetails = [
                'bundling_id' => $request->bundling_id,
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
        } 

        $invoiceDetails['inv_category'] = $param;
        // $invoiceDetails['session'] = isset($invoiceDetails['session']) && $invoiceDetails['session'] != 0 ? $invoiceDetails['session'] : 0;
        if ($currency !== null)
            $invoiceDetails['currency'] = $currency;

        $invoiceDetails['inv_paymentmethod'] = $invoiceDetails['inv_paymentmethod'] == "full" ? 'Full Payment' : 'Installment';

        $invoiceDetails['created_at'] = $invoiceDetails['invoice_date'] . ' ' . date('H:i:s');

        DB::beginTransaction();
        try {

            $last_id = InvoiceProgram::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->max(DB::raw('substr(inv_id, 1, 4)'));

            # Use Trait Create Invoice Id
            $inv_id = $this->getInvoiceId($last_id, 'BDL', $invoiceDetails['invoice_date']);
         
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
            Log::error('Store invoice bundle program failed : ' . $e->getMessage());
            return Redirect::to('invoice/client-program/create?bundle=' . $request->bundling_id)->withError('Failed to store invoice bundle program');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Invoice bundle Program', Auth::user()->first_name . ' '. Auth::user()->last_name, $invoiceProgramCreated);

        return Redirect::to('invoice/client-program/bundle/'. $request->bundling_id)->withSuccess('Invoice has been created');
    }

    public function showBundle(Request $request)
    {
        $bundlingId = $request->route('bundle');
        $bundle = $this->clientProgramRepository->getBundleProgramByUUID($bundlingId);
        $invoice = $this->invoiceProgramRepository->getInvoiceByBundlingId($bundlingId);

        return view('pages.invoice.client-program.form-bundle')->with(
            [
                'status' => 'view',
                'bundle' => $bundle,
                'invoice' => $invoice
            ]
        );
    }

    public function editBundle(Request $request)
    {
        $bundlingId = $request->route('bundle');
        $bundle = $this->clientProgramRepository->getBundleProgramByUUID($bundlingId);
        $invoice = $this->invoiceProgramRepository->getInvoiceByBundlingId($bundlingId);

        return view('pages.invoice.client-program.form-bundle')->with(
            [
                'status' => 'edit',
                'bundle' => $bundle,
                'invoice' => $invoice
            ]
        );
    }

    public function updateBundle(StoreInvoiceProgramBundleRequest $request)
    {
        $bundlingId = $request->bundling_id;
        $bundling = $this->clientProgramRepository->getClientProgramById($bundlingId);

        $invoice = $this->invoiceProgramRepository->getInvoiceByBundlingId($bundlingId);
        $inv_id = $invoice->inv_id;

        # fetching currency till get the currency
        // $currency = null;
        $currency = $request->currency;

        switch ($request->currency) {

            case "idr":

                    $invoiceDetails = $request->only([
                        'bundling_id',
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
                
                break;

            case "other":

                    $invoiceDetails = [
                        'bundling_id' => $request->bundling_id,
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
                

                break;
        }
        

        $invoiceDetails['inv_category'] = $invoiceDetails['currency'];
        $invoiceDetails['session'] = isset($invoiceDetails['session']) && $invoiceDetails['session'] != 0 ? $invoiceDetails['session'] : 0;
        $invoiceDetails['currency'] = $currency == "other" ? $invoiceDetails['currency_detail'] : $currency;
        $invoiceDetails['inv_paymentmethod'] = $invoiceDetails['inv_paymentmethod'] == "full" ? 'Full Payment' : 'Installment';
        unset($invoiceDetails['currency_detail']);

        $invoiceDetails['created_at'] = $invoiceDetails['invoice_date'] . ' ' . date('H:i:s');

        DB::beginTransaction();
        try {

            # when created date / invoice date has changed 
            # then check if old invoice_id same or not with the new invoice id using created at
            if ( $invoice->created_at != $invoiceDetails['created_at']) {
                
                $last_id = InvoiceProgram::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->max(DB::raw('substr(inv_id, 1, 4)'));
    
                # Use Trait Create Invoice Id
                $new_inv_id = $this->getInvoiceId($last_id, 'BDL', $invoiceDetails['invoice_date']);
                $invoiceDetails['inv_id'] = $new_inv_id;
            }

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
            return Redirect::to('invoice/client-program/bundle/' . $request->bundling_id . '/edit')->withError('Failed to update invoice program');
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Invoice Client Program', Auth::user()->first_name . ' '. Auth::user()->last_name, $invoiceDetails, $invoice);

        return Redirect::to('invoice/client-program/bundle/' . $request->bundling_id)->withSuccess('Invoice has been updated');
    }

    public function destroyBundle(Request $request)
    {
        $bundling_id = $request->route('bundle');
        $invProg = $this->invoiceProgramRepository->getInvoiceByBundlingId($bundling_id);
        DB::beginTransaction();
        try {

            $this->invoiceProgramRepository->deleteInvoiceByBundlingId($bundling_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete invoice program failed : ' . $e->getMessage());
            return Redirect::to('invoice/client-program/bundle/' . $bundling_id)->withError('Failed to delete invoice program');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Invoice Client Program', Auth::user()->first_name . ' '. Auth::user()->last_name, $invProg);

        return Redirect::to('invoice/client-program?s=needed&b=true')->withSuccess('Invoice has been deleted');
    }

    private function previewBundleFromDashboard($currency, $bundle, $director)
    {
        if ($currency == "idr")
            $view = 'pages.invoice.client-program.export.invoice-bundle-pdf';
        else
            $view = 'pages.invoice.client-program.export.invoice-bundle-pdf-foreign';

        $companyDetail = [
            'name' => env('ALLIN_COMPANY'),
            'address' => env('ALLIN_ADDRESS'),
            'address_dtl' => env('ALLIN_ADDRESS_DTL'),
            'city' => env('ALLIN_CITY')
        ];

        $pdf = PDF::loadView($view, ['bundle' => $bundle, 'companyDetail' => $companyDetail, 'director' => $director]);
        return $pdf->stream();
    }

    public function previewBundle(Request $request)
    {
        $bundling_id = $request->route('bundle');
        $currency = $request->route('currency');

        # query
        $preview = $request->get('key');
        $director = $request->get('dir');

        if (!$bundle = $this->clientProgramRepository->getBundleProgramByUUID($bundling_id))
            abort(404);

        if ($preview == 'dashboard') {
            return $this->previewBundleFromDashboard($currency, $bundle, $director);
        }


        $invoice = $bundle->invoice_b2c;

        $attachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('Program', $invoice->inv_id, $currency);

        return view('pages.invoice.sign-pdf')->with(
            [
                'invoice' => $invoice,
                'attachment' => $attachment
            ]
        );
    }

    public function requestSignBundle(Request $request)
    {
        $bundling_id = $request->route('bundle');
        $bundle = $this->clientProgramRepository->getBundleProgramByUUID($bundling_id);
        $invoice_id = $bundle->invoice_b2c->inv_id;

        $type = $request->get('type');
        $to = $request->get('to'); # our director mail
        $name = $request->get('name'); # our director name

        if ($type == "idr")
            $view = 'pages.invoice.client-program.export.invoice-bundle-pdf';
        else
            $view = 'pages.invoice.client-program.export.invoice-bundle-pdf-foreign';

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
            'bundling_id' => $bundling_id,
            'currency' => $type,
            'fullname' => $bundle->details[0]->client_program->client->full_name,
            'program_name' => $bundle->details[0]->client_program->program->program_name . ' (Bundle Package)',
            'invoice_date' => date('d F Y', strtotime($bundle->invoice_b2c->created_at)),
            'invoice_duedate' => date('d F Y', strtotime($bundle->invoice_b2c->inv_duedate))
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
                'bundle' => $bundle,
                'company_detail' => $companyDetail, 
                'director' => $name,
                'file_name' => $file_name
            ];

            # dispatching the job to the queue
            ProcessEmailRequestSignJobBundle::dispatch($data, $attachmentDetails, $invoice_id)->onQueue('inv-email-request-sign');
            
        } catch (Exception $e) {

            Log::info('Failed to request sign invoice : ' . $e->getMessage() . ' | Line ' . $e->getLine());
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }

        # Request Sign success
        # create log success
        $this->logSuccess('request-sign', null, 'Invoice Client Program', Auth::user()->first_name . ' '. Auth::user()->last_name, ['invoice_id' => $invoice_id]);

        return response()->json(['message' => 'Invoice sent successfully.']);
    }

    public function upload(Request $request)
    {
        $pdfFile = $request->file('pdfFile');
        $name = $request->file('pdfFile')->getClientOriginalName();

        $bundling_id = $request->route('bundle');
        $bundle = $this->clientProgramRepository->getBundleProgramByUUID($bundling_id);
        $invoice = $bundle->invoice_b2c;
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

    public function printBundle(Request $request)
    {
        $bundling_id = $request->route('bundle');
        $currency = $request->route('currency');

        if (!$bundle = $this->clientProgramRepository->getBundleProgramByUUID($bundling_id))
            abort(404);

        $invoice = $bundle->invoice_b2c;
        $attachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('Program', $invoice->inv_id, $currency);

        return view('pages.invoice.view-pdf')->with(
            [
                'invoice' => $invoice,
                'attachment' => $attachment
            ]
        );
    }

    public function sendToClientBundle(Request $request)
    {
        $bundling_id = $request->route('bundle');
        $type_recipient = $request->route('type_recipient');
        $bundle = $this->clientProgramRepository->getBundleProgramByUUID($bundling_id);
        $invoice = $bundle->invoice_b2c;
        $invoice_id = $invoice->inv_id;
        $currency = $request->route('currency');
        $attachment = $invoice->invoiceAttachment()->where('currency', $currency)->first();

        $pic_mail = $bundle->details[0]->client_program->internalPic->email;
        

        switch ($type_recipient) {
            case 'Parent':
                $data['email'] = $bundle->details[0]->client_program->client->parents[0]->mail;
                $data['recipient'] = $bundle->details[0]->client_program->client->parents[0]->full_name;
                break;

            case 'Client':
                $data['email'] = $bundle->details[0]->client_program->client->mail;
                $data['recipient'] = $bundle->details[0]->client_program->client->full_name;
                break;
        }

        $data['cc'] = [
            env('CEO_CC'),
            env('FINANCE_CC'),
            $pic_mail
        ];
        $data['param'] = [
            'bundling_id' => $bundling_id,
            'program_name' => $bundle->details[0]->client_program->program->program_name . ' (Bundle Package)'
        ];
        $data['title'] = "Invoice of program " . $bundle->details[0]->client_program->program->program_name;

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
    // ================== End Bundle =================
}
