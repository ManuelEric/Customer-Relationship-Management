<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceB2bRequest;
use App\Http\Requests\StoreAttachmentB2bRequest;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\InvoiceAttachmentRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\InvoiceDetailRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Interfaces\AxisRepositoryInterface;
use App\Http\Traits\CreateInvoiceIdTrait;
use App\Http\Traits\LoggingTrait;
use App\Models\Invb2b;
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

use function PHPUnit\Framework\isEmpty;

class InvoiceSchoolController extends InvoiceB2BBaseController
{
    use CreateInvoiceIdTrait;
    use LoggingTrait;
    protected SchoolRepositoryInterface $schoolRepository;
    protected SchoolProgramRepositoryInterface $schoolProgramRepository;
    protected ProgramRepositoryInterface $programRepository;
    protected InvoiceAttachmentRepositoryInterface $invoiceAttachmentRepository;
    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    protected InvoiceDetailRepositoryInterface $invoiceDetailRepository;
    protected ReceiptRepositoryInterface $receiptRepository;
    protected AxisRepositoryInterface $axisRepository;
    public $module;

    public function __construct(SchoolRepositoryInterface $schoolRepository, SchoolProgramRepositoryInterface $schoolProgramRepository, ProgramRepositoryInterface $programRepository, InvoiceB2bRepositoryInterface $invoiceB2bRepository, InvoiceDetailRepositoryInterface $invoiceDetailRepository, ReceiptRepositoryInterface $receiptRepository, InvoiceAttachmentRepositoryInterface $invoiceAttachmentRepository, AxisRepositoryInterface $axisRepository)
    {
        $this->schoolRepository = $schoolRepository;
        $this->schoolProgramRepository = $schoolProgramRepository;
        $this->programRepository = $programRepository;
        $this->invoiceAttachmentRepository = $invoiceAttachmentRepository;
        $this->invoiceB2bRepository = $invoiceB2bRepository;
        $this->invoiceDetailRepository = $invoiceDetailRepository;
        $this->receiptRepository = $receiptRepository;
        $this->axisRepository = $axisRepository;
        $this->module = $this->getModule();
    }

    public function index(Request $request)
    {
        $status = $request->route('status');

        if ($request->ajax()) {
            switch ($status) {
                case 'needed':
                    return $this->invoiceB2bRepository->getAllInvoiceNeededSchDataTables();
                    break;
                case 'list':
                case 'reminder':
                    return $this->invoiceB2bRepository->getAllInvoiceSchDataTables($status);
                    break;
            }
        }

        return view('pages.invoice.school-program.index')->with(['status' => $status]);
    }

    public function create(Request $request)
    {
        $schProgId = $request->route('sch_prog');

        $schoolProgram = $this->schoolProgramRepository->getSchoolProgramById($schProgId);

        $schoolId = $schoolProgram->sch_id;

        # retrieve school data by id
        $school = $this->schoolRepository->getSchoolById($schoolId);

        return view('pages.invoice.school-program.form')->with(
            [
                'schoolProgram' => $schoolProgram,
                'school' => $school,
                'status' => 'create',
            ]
        );
    }

    public function store(StoreInvoiceB2bRequest $request)
    {

        $schProgId = $request->route('sch_prog');
        $invoices = $request->only([
            'select_currency',
            'currency',
            'curs_rate',
            'invb2b_priceidr',
            'invb2b_priceidr_other',
            'invb2b_price',
            'invb2b_totpriceidr',
            'invb2b_totpriceidr_other',
            'invb2b_totprice',
            'invb2b_participants',
            'invb2b_participants_other',
            'invb2b_discidr',
            'invb2b_discidr_other',
            'invb2b_disc',
            'invb2b_wordsidr',
            'invb2b_wordsidr_other',
            'invb2b_words',
            'invb2b_pm',
            'invb2b_date',
            'invb2b_duedate',
            'invb2b_notes',
            'invb2b_tnc',
            'is_full_amount'
        ]);

        $installments = $request->only(
            [
                'invdtl_installment',
                'invdtl_duedate',
                'invdtl_percentage',
                'invdtl_installment_other',
                'invdtl_duedate_other',
                'invdtl_percentage_other',
                'invdtl_amount',
                'invdtl_amountidr',
                'invdtl_amountidr_other',
            ]
        );


        $cursrate = [
            'invdtl_cursrate' => $invoices['curs_rate'],
            'invdtl_currency' => $invoices['currency'],
        ];


        switch ($invoices['select_currency']) {
            case 'other':
                $invoices['invb2b_priceidr'] = $invoices['invb2b_priceidr_other'];
                $invoices['invb2b_discidr'] = $invoices['invb2b_discidr_other'];
                $invoices['invb2b_participants'] = $invoices['invb2b_participants_other'];
                $invoices['invb2b_totpriceidr'] = $invoices['invb2b_totpriceidr_other'];
                $invoices['invb2b_wordsidr'] = $invoices['invb2b_wordsidr_other'];

                unset($installments['invdtl_installment']);
                unset($installments['invdtl_duedate']);
                unset($installments['invdtl_percentage']);
                unset($installments['invdtl_amountidr']);
                break;

            case 'idr':
                $invoices['currency'] = 'idr';
                unset($invoices['invb2b_price']);
                unset($invoices['invb2b_disc']);
                unset($invoices['invb2b_totprice']);
                unset($invoices['invb2b_words']);

                unset($cursrate['invdtl_cursrate']);
                unset($installments['invdtl_installment_other']);
                unset($installments['invdtl_duedate_other']);
                unset($installments['invdtl_percentage_other']);
                unset($installments['invdtl_amount']);
                unset($installments['invdtl_amountidr_other']);
                break;
        }



        unset($invoices['invb2b_participants_other']);
        unset($invoices['invb2b_priceidr_other']);
        unset($invoices['invb2b_discidr_other']);
        unset($invoices['invb2b_totpriceidr_other']);
        unset($invoices['invb2b_wordsidr_other']);

        $now = Carbon::now();
        $thisMonth = $now->month;

        $last_id = Invb2b::whereMonth('created_at', $thisMonth)->whereYear('created_at', date('Y'))->max(DB::raw('substr(invb2b_id, 1, 4)'));

        $schoolProgram = $this->schoolProgramRepository->getSchoolProgramById($schProgId);
        $prog_id = $schoolProgram->prog_id;

        // Use Trait Create Invoice Id
        $inv_id = $this->getInvoiceId($last_id, $prog_id);

        $invoices['invb2b_id'] = $inv_id;
        $invoices['schprog_id'] = $schProgId;

        if ($invoices['invb2b_pm'] == 'Installment') {
            $installment = $this->extract_installment($inv_id, $invoices['select_currency'], $cursrate, $installments);
        }
        unset($installments);

        DB::beginTransaction();
        try {

            $invoiceCreated = $this->invoiceB2bRepository->createInvoiceB2b($invoices);
            if ($invoices['invb2b_pm'] == 'Installment') {
                $this->invoiceDetailRepository->createInvoiceDetail($installment);
            }
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Create invoice failed : ' . $e->getMessage());

            return Redirect::to('invoice/school-program/' . $schProgId . '/detail/create')->withError('Failed to create a new invoice');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Invoice School Program', Auth::user()->first_name . ' '. Auth::user()->last_name, $invoiceCreated);

        return Redirect::to('invoice/school-program/status/list')->withSuccess('Invoice successfully created');
    }

    public function show(Request $request)
    {
        $schProgId = $request->route('sch_prog');
        $invNum = $request->route('detail');

        $schoolProgram = $this->schoolProgramRepository->getSchoolProgramById($schProgId);

        $schoolId = $schoolProgram->sch_id;

        $school = $this->schoolRepository->getSchoolById($schoolId);

        $invoiceSch = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);

        $attachments = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceIdentifier('B2B', $invoiceSch->invb2b_id);

        return view('pages.invoice.school-program.form')->with(
            [
                'schoolProgram' => $schoolProgram,
                'school' => $school,
                'invoiceSch' => $invoiceSch,
                'attachments' => $attachments,
                'status' => 'show',
            ]
        );
    }

    public function edit(Request $request)
    {
        $invNum = $request->route('detail');
        $schProgId = $request->route('sch_prog');

        $schoolProgram = $this->schoolProgramRepository->getSchoolProgramById($schProgId);

        $schoolId = $schoolProgram->sch_id;

        $school = $this->schoolRepository->getSchoolById($schoolId);

        $invoiceSch = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);

        return view('pages.invoice.school-program.form')->with(
            [
                'status' => 'edit',
                'schoolProgram' => $schoolProgram,
                'school' => $school,
                'invoiceSch' => $invoiceSch,
            ]
        );
    }

    public function update(StoreInvoiceB2bRequest $request)
    {

        $schProgId = $request->route('sch_prog');
        $invNum = $request->route('detail');
        $oldInvoice = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);

        $invoices = $request->only([
            'select_currency',
            'currency',
            'curs_rate',
            'invb2b_priceidr',
            'invb2b_priceidr_other',
            'invb2b_price',
            'invb2b_totpriceidr',
            'invb2b_totpriceidr_other',
            'invb2b_totprice',
            'invb2b_participants',
            'invb2b_participants_other',
            'invb2b_discidr',
            'invb2b_discidr_other',
            'invb2b_disc',
            'invb2b_wordsidr',
            'invb2b_wordsidr_other',
            'invb2b_words',
            'invb2b_pm',
            'invb2b_date',
            'invb2b_duedate',
            'invb2b_notes',
            'invb2b_tnc',
            'is_full_amount',
        ]);

        $installments = $request->only(
            [
                'invdtl_installment',
                'invdtl_duedate',
                'invdtl_percentage',
                'invdtl_installment_other',
                'invdtl_duedate_other',
                'invdtl_percentage_other',
                'invdtl_amount',
                'invdtl_amountidr',
                'invdtl_amountidr_other',
            ]
        );

        $cursrate = [
            'invdtl_cursrate' => $invoices['curs_rate'],
            'invdtl_currency' => $invoices['currency'],
        ];

        switch ($invoices['select_currency']) {
            case 'other':
                $invoices['invb2b_priceidr'] = $invoices['invb2b_priceidr_other'];
                $invoices['invb2b_discidr'] = $invoices['invb2b_discidr_other'];
                $invoices['invb2b_participants'] = $invoices['invb2b_participants_other'];
                $invoices['invb2b_totpriceidr'] = $invoices['invb2b_totpriceidr_other'];
                $invoices['invb2b_wordsidr'] = $invoices['invb2b_wordsidr_other'];

                unset($installments['invdtl_installment']);
                unset($installments['invdtl_duedate']);
                unset($installments['invdtl_percentage']);
                unset($installments['invdtl_amountidr']);
                break;

            case 'idr':
                unset($invoices['invb2b_price']);
                unset($invoices['invb2b_disc']);
                unset($invoices['invb2b_totprice']);
                unset($invoices['invb2b_words']);
                unset($invoices['currency']);

                unset($installments['invdtl_installment_other']);
                unset($installments['invdtl_duedate_other']);
                unset($installments['invdtl_percentage_other']);
                unset($installments['invdtl_amount']);
                unset($installments['invdtl_amountidr_other']);
                break;
        }

        unset($invoices['invb2b_participants_other']);
        unset($invoices['invb2b_priceidr_other']);
        unset($invoices['invb2b_discidr_other']);
        unset($invoices['invb2b_totpriceidr_other']);
        unset($invoices['invb2b_wordsidr_other']);

        $invoices['schprog_id'] = $schProgId;

        $inv_b2b = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);
        $inv_id = $inv_b2b->invb2b_id;
        if ($invoices['invb2b_pm'] == 'Installment') {
            $NewInstallment = $this->extract_installment($inv_id, $invoices['select_currency'], $cursrate, $installments);
        }
        unset($installments);

        DB::beginTransaction();
        try {

            $this->invoiceB2bRepository->updateInvoiceB2b($invNum, $invoices);
            if ($invoices['invb2b_pm'] == 'Installment') {
                $this->invoiceDetailRepository->updateInvoiceDetailByInvB2bId($inv_id, $NewInstallment);
                $this->invoiceDetailRepository->createInvoiceDetail($NewInstallment);
            } else {
                if (count($inv_b2b->inv_detail) > 0) {
                    $this->invoiceDetailRepository->deleteInvoiceDetailByinvb2b_Id($inv_id);
                }
            }

            if (count($inv_b2b->invoiceAttachment) > 0) {
                $this->invoiceAttachmentRepository->deleteInvoiceAttachmentByInvoiceB2bId($inv_id);
            }
            // Storage::disk('public')->delete()
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update invoice failed : ' . $e->getMessage());

            return $e->getMessage();
            exit;
            return Redirect::to('invoice/school-program/' . $schProgId . '/detail/' . $invNum)->withError('Failed to update invoice');
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Invoice Client Program', Auth::user()->first_name . ' '. Auth::user()->last_name, $invoices, $oldInvoice);

        return Redirect::to('invoice/school-program/' . $schProgId . '/detail/' . $invNum)->withSuccess('Invoice successfully updated');
    }

    public function destroy(Request $request)
    {
        $invNum = $request->route('detail');
        $schProgId = $request->route('sch_prog');
        $invoice = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);

        DB::beginTransaction();
        try {

            $this->invoiceB2bRepository->deleteInvoiceB2b($invNum);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete invoice failed : ' . $e->getMessage());

            return Redirect::to('invoice/school-program/' . $schProgId . '/detail/' . $invNum)->withError('Failed to delete invoice');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Invoice School Program', Auth::user()->first_name . ' '. Auth::user()->last_name, $invoice);

        return Redirect::to('invoice/school-program/status/list')->withSuccess('Invoice successfully deleted');
    }

    protected function extract_installment($inv_id, $currency, array $cursrate,  array $installments)
    {
        if ($currency == 'other') {
            for ($i = 0; $i < count($installments['invdtl_installment_other']); $i++) {
                $installment[] = [
                    'invdtl_installment' => $installments['invdtl_installment_other'][$i],
                    'invdtl_duedate' => $installments['invdtl_duedate_other'][$i],
                    'invdtl_percentage' => $installments['invdtl_percentage_other'][$i],
                    'invdtl_amount' => $installments['invdtl_amount'][$i],
                    'invdtl_amountidr' => $installments['invdtl_amountidr_other'][$i],
                    'invdtl_cursrate' => $cursrate['invdtl_cursrate'],
                    'invdtl_currency' => $cursrate['invdtl_currency'],
                    'invb2b_id' => $inv_id,
                ];
            }
        } elseif ($currency == 'idr') {
            for ($i = 0; $i < count($installments['invdtl_installment']); $i++) {
                $installment[] = [
                    'invdtl_installment' => $installments['invdtl_installment'][$i],
                    'invdtl_duedate' => $installments['invdtl_duedate'][$i],
                    'invdtl_percentage' => $installments['invdtl_percentage'][$i],
                    'invdtl_amountidr' => $installments['invdtl_amountidr'][$i],
                    'invdtl_currency' => $currency,
                    'invb2b_id' => $inv_id,
                ];
            }
        }

        return $installment;
    }
}
