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
use App\Interfaces\AxisRepositoryInterface;
use App\Http\Traits\CreateInvoiceIdTrait;
use App\Http\Traits\DirectorListTrait;
use App\Http\Traits\LoggingTrait;
use App\Models\Invb2b;
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

        $last_id = Invb2b::whereMonth('created_at', $thisMonth)->whereYear('created_at', date('Y'))->max(DB::raw('substr(invb2b_id, 1, 4)'));

        // Use Trait Create Invoice Id
        $inv_id = $this->getInvoiceId($last_id, 'REF-OUT');

        $invoices['invb2b_id'] = $inv_id;
        $invoices['ref_id'] = $ref_id;

        DB::beginTransaction();
        try {

            $invoiceCreated = $this->invoiceB2bRepository->createInvoiceB2b($invoices);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Create invoice failed : ' . $e->getMessage());

            return Redirect::to('invoice/referral/' . $ref_id . '/detail/create')->withError('Failed to create a new invoice');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Invoice Referral Program', Auth::user()->first_name . ' '. Auth::user()->last_name, $invoiceCreated);


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
        $oldInvoice = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);

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
        $inv_b2b = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);
        $inv_id = $inv_b2b->invb2b_id;

        DB::beginTransaction();
        try {

            $this->invoiceB2bRepository->updateInvoiceB2b($invNum, $invoices);

            if (count($inv_b2b->invoiceAttachment) > 0) {
                $this->invoiceAttachmentRepository->deleteInvoiceAttachmentByInvoiceB2bId($inv_id);
            }

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update invoice failed : ' . $e->getMessage());

            return Redirect::to('invoice/referral/' . $ref_id . '/detail/' . $invNum)->withError('Failed to update invoice');
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Invoice Referral Program', Auth::user()->first_name . ' '. Auth::user()->last_name, $invoices, $oldInvoice);

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
}
