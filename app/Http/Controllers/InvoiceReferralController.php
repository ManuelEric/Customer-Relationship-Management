<?php

namespace App\Http\Controllers;

use App\Enum\LogModule;
use App\Http\Requests\StoreInvoiceB2bRequest;
use App\Http\Requests\StoreInvoiceReferralRequest;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\ReferralRepositoryInterface;
use App\Interfaces\InvoiceAttachmentRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\InvoiceDetailRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Interfaces\AxisRepositoryInterface;
use App\Http\Traits\CreateInvoiceIdTrait;
use App\Http\Traits\DirectorListTrait;
use App\Http\Traits\LoggingTrait;
use App\Models\Invb2b;
use App\Services\Log\LogService;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
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


class InvoiceReferralController extends InvoiceB2BBaseController
{
    use DirectorListTrait;
    use CreateInvoiceIdTrait;
    use LoggingTrait;
    protected ReferralRepositoryInterface $referralRepository;
    protected ProgramRepositoryInterface $programRepository;
    protected InvoiceAttachmentRepositoryInterface $invoiceAttachmentRepository;
    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    protected InvoiceDetailRepositoryInterface $invoiceDetailRepository;
    protected ReceiptRepositoryInterface $receiptRepository;
    protected CorporateRepositoryInterface $corporateRepository;
    protected AxisRepositoryInterface $axisRepository;
    public $module;

    public function __construct(ReferralRepositoryInterface $referralRepository, ProgramRepositoryInterface $programRepository, InvoiceB2bRepositoryInterface $invoiceB2bRepository, InvoiceDetailRepositoryInterface $invoiceDetailRepository, ReceiptRepositoryInterface $receiptRepository, InvoiceAttachmentRepositoryInterface $invoiceAttachmentRepository, CorporateRepositoryInterface $corporateRepository, AxisRepositoryInterface $axisRepository)
    {
        $this->referralRepository = $referralRepository;
        $this->programRepository = $programRepository;
        $this->invoiceAttachmentRepository = $invoiceAttachmentRepository;
        $this->invoiceB2bRepository = $invoiceB2bRepository;
        $this->invoiceDetailRepository = $invoiceDetailRepository;
        $this->receiptRepository = $receiptRepository;
        $this->corporateRepository = $corporateRepository;
        $this->axisRepository = $axisRepository;
        $this->module = $this->getModule();
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
                case 'reminder':
                    return $this->invoiceB2bRepository->getAllInvoiceReferralDataTables($status);
                    break;
            }
        }

        return view('pages.invoice.referral.index')->with(['status' => $status]);
    }

    public function create(Request $request)
    {
        $ref_id = $request->route('referral');

        $referral = $this->referralRepository->getReferralById($ref_id);

        $partner_id = $referral->partner_id;

        # retrieve corp data by id
        $partner = $this->corporateRepository->getCorporateById($partner_id);

        return view('pages.invoice.referral.form')->with(
            [
                'referral' => $referral,
                'partner' => $partner,
                'status' => 'create',
            ]
        );
    }

    public function store(StoreInvoiceReferralRequest $request, LogService $log_service)
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
        $this_month = $now->month;

        $last_id = Invb2b::whereMonth('created_at', $this_month)->whereYear('created_at', date('Y'))->max(DB::raw('substr(invb2b_id, 1, 4)'));

        // Use Trait Create Invoice Id
        $inv_id = $this->getInvoiceId($last_id, 'REF-OUT');

        $invoices['invb2b_id'] = $inv_id;
        $invoices['ref_id'] = $ref_id;

        DB::beginTransaction();
        try {

            $invoice_created = $this->invoiceB2bRepository->createInvoiceB2b($invoices);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_INVOICE_REFERRAL, $e->getMessage(), $e->getLine(), $e->getFile(), $invoices);

            return Redirect::to('invoice/referral/' . $ref_id . '/detail/create')->withError('Failed to create a new invoice');
        }

        # store Success
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_INVOICE_REFERRAL, 'New invoice has been added', $invoices);

        return Redirect::to('invoice/referral/status/list')->withSuccess('Invoice successfully created');
    }

    public function show(Request $request)
    {
        $ref_id = $request->route('referral');
        $inv_num = $request->route('detail');

        $referral = $this->referralRepository->getReferralById($ref_id);

        $partner_id = $referral->partner_id;

        $partner = $this->corporateRepository->getCorporateById($partner_id);

        $invoice_ref = $this->invoiceB2bRepository->getInvoiceB2bById($inv_num);


        return view('pages.invoice.referral.form')->with(
            [
                'referral' => $referral,
                'partner' => $partner,
                'invoiceRef' => $invoice_ref,
                'status' => 'show',
            ]
        );
    }

    public function edit(Request $request)
    {
        $inv_num = $request->route('detail');
        $ref_id = $request->route('referral');

        $referral = $this->referralRepository->getReferralById($ref_id);

        $partner_id = $referral->partner_id;

        $partner = $this->corporateRepository->getCorporateById($partner_id);

        $invoice_ref = $this->invoiceB2bRepository->getInvoiceB2bById($inv_num);

        return view('pages.invoice.referral.form')->with(
            [
                'status' => 'edit',
                'referral' => $referral,
                'partner' => $partner,
                'invoiceRef' => $invoice_ref,
            ]
        );
    }

    public function update(StoreInvoiceReferralRequest $request, LogService $log_service)
    {

        $ref_id = $request->route('referral');
        $inv_num = $request->route('detail');
        $old_invoice = $this->invoiceB2bRepository->getInvoiceB2bById($inv_num);

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
                $invoices['currency'] = $invoices['select_currency'];
                break;
        }

        unset($invoices['invb2b_totpriceidr_other']);
        unset($invoices['invb2b_wordsidr_other']);


        $invoices['ref_id'] = $ref_id;
        $inv_b2b = $this->invoiceB2bRepository->getInvoiceB2bById($inv_num);
        $inv_id = $inv_b2b->invb2b_id;

        DB::beginTransaction();
        try {

            $this->invoiceB2bRepository->updateInvoiceB2b($inv_num, $invoices);

            if (count($inv_b2b->invoiceAttachment) > 0) {
                $this->invoiceAttachmentRepository->deleteInvoiceAttachmentByInvoiceB2bId($inv_id);
            }

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_INVOICE_REFERRAL, $e->getMessage(), $e->getLine(), $e->getFile(), $invoices);

            return Redirect::to('invoice/referral/' . $ref_id . '/detail/' . $inv_num)->withError('Failed to update invoice');
        }

        # Update success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_INVOICE_REFERRAL, 'Invoice has been updated', $invoices);

        return Redirect::to('invoice/referral/' . $ref_id . '/detail/' . $inv_num)->withSuccess('Invoice successfully updated');
    }

    public function destroy(Request $request, LogService $log_service)
    {
        $inv_num = $request->route('detail');
        $ref_id = $request->route('referral');
        $invoice = $this->invoiceB2bRepository->getInvoiceB2bById($inv_num);

        DB::beginTransaction();
        try {

            $this->invoiceB2bRepository->deleteInvoiceB2b($inv_num);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_INVOICE_REFERRAL, $e->getMessage(), $e->getLine(), $e->getFile(), $invoice->toArray());

            return Redirect::to('invoice/referral/' . $ref_id . '/detail/' . $inv_num)->withError('Failed to delete invoice');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_INVOICE_REFERRAL, 'Invoice has been deleted', $invoice->toArray());

        return Redirect::to('invoice/referral/status/list')->withSuccess('Invoice successfully deleted');
    }
}
