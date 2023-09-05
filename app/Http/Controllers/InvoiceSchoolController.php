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
use App\Models\Invb2b;
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

use function PHPUnit\Framework\isEmpty;

class InvoiceSchoolController extends Controller
{
    use CreateInvoiceIdTrait;
    protected SchoolRepositoryInterface $schoolRepository;
    protected SchoolProgramRepositoryInterface $schoolProgramRepository;
    protected ProgramRepositoryInterface $programRepository;
    protected InvoiceAttachmentRepositoryInterface $invoiceAttachmentRepository;
    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    protected InvoiceDetailRepositoryInterface $invoiceDetailRepository;
    protected ReceiptRepositoryInterface $receiptRepository;
    protected AxisRepositoryInterface $axisRepository;

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

            $this->invoiceB2bRepository->createInvoiceB2b($invoices);
            if ($invoices['invb2b_pm'] == 'Installment') {
                $this->invoiceDetailRepository->createInvoiceDetail($installment);
            }
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Create invoice failed : ' . $e->getMessage());

            return $e->getMessage();
            exit;
            return Redirect::to('invoice/school-program/' . $schProgId . '/detail/create')->withError('Failed to create a new invoice');
        }

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

        return Redirect::to('invoice/school-program/' . $schProgId . '/detail/' . $invNum)->withSuccess('Invoice successfully updated');
    }

    public function destroy(Request $request)
    {
        $invNum = $request->route('detail');
        $schProgId = $request->route('sch_prog');

        DB::beginTransaction();
        try {

            $this->invoiceB2bRepository->deleteInvoiceB2b($invNum);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete invoice failed : ' . $e->getMessage());

            return Redirect::to('invoice/school-program/' . $schProgId . '/detail/' . $invNum)->withError('Failed to delete invoice');
        }

        return Redirect::to('invoice/school-program/status/list')->withSuccess('Invoice successfully deleted');
    }

    public function export(Request $request)
    {
        $invNum = $request->route('invoice');
        $currency = $request->route('currency');

        $invoiceSch = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);
        $invoice_id = $invoiceSch->invb2b_id;

        $invoiceAttachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('B2B', $invoice_id, $currency);

        return view('pages.invoice.view-pdf')->with([
            'invoiceAttachment' => $invoiceAttachment,
        ]);
    }

    public function requestSign(Request $request)
    {
        $invNum = $request->route('invoice');
        $currency = $request->route('currency');
        $to = $request->get('to');
        $name = $request->get('name');

        $invoiceSch = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);
        $invoice_id = $invoiceSch->invb2b_id;
        $invoice_num = $invoiceSch->invb2b_num;
        $file_name = str_replace('/', '-', $invoice_id) . '-' . ($currency == 'idr' ? $currency : 'other') . '.pdf'; # 0001_INV_JEI_EF_I_23_idr.pdf
        $path = 'uploaded_file/invoice/sch_prog/';
        $attachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('B2B', $invoice_id, $currency);

        $attachmentDetails = [
            'invb2b_id' => $invoice_id,
            'currency' => $currency,
            'recipient' => $to,
            'attachment' => 'storage/' . $path . $file_name,
        ];

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
            'invb2b_num' => $invoice_num,
            'currency' => $currency,
            'fullname' => $invoiceSch->sch_prog->school->sch_name,
            'program_name' => $invoiceSch->sch_prog->program->program_name,
            'invoice_date' => date('d F Y', strtotime($invoiceSch->invb2b_date)),
            'invoice_duedate' => date('d F Y', strtotime($invoiceSch->invb2b_duedate))
        ];

        try {

            $pdf = PDF::loadView('pages.invoice.school-program.export.invoice-pdf', [
                'invoiceSch' => $invoiceSch,
                'currency' => $currency,
                'companyDetail' => $companyDetail
            ]);

            # Generate PDF file
            $content = $pdf->download();
            Storage::disk('public')->put($path . $file_name, $content);

            # if attachment exist then update attachement else insert attachement
            if (isset($attachment)) {
                $this->invoiceAttachmentRepository->updateInvoiceAttachment($attachment->id, $attachmentDetails);
            } else {
                $this->invoiceAttachmentRepository->createInvoiceAttachment($attachmentDetails);
            }

            Mail::send('pages.invoice.school-program.mail.view', $data, function ($message) use ($data, $pdf, $invoice_id) {
                $message->to($data['email'], $data['recipient'])
                    ->subject($data['title'])
                    ->attachData($pdf->output(), $invoice_id . '.pdf');
            });
        } catch (Exception $e) {

            Log::info('Failed to request sign invoice : ' . $e->getMessage());
            return $e->getMessage();
        }

        return true;
    }

    public function signAttachment(Request $request)
    {
        // if (Session::token() != $request->get('token')) {
        //     return "Your session token is expired";
        // }

        $invNum = $request->route('invoice');
        $currency = $request->route('currency');
        $invoiceSch = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);
        $invoice_id = $invoiceSch->invb2b_id;
        $invoiceAttachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('B2B', $invoice_id, $currency);
        $axis = $this->axisRepository->getAxisByType('invoice');

        if (isset($invoiceAttachment->sign_status) && $invoiceAttachment->sign_status == 'signed') {
            return "Invoice is already signed";
        }

        return view('pages.invoice.sign-pdf')->with(
            [
                'attachment' => $invoiceAttachment->attachment,
                'axis' => $axis,
                'currency' => $currency,
                'invoice' => $invoiceSch,
            ]
        );
    }

    public function upload(Request $request)
    {
        $pdfFile = $request->file('pdfFile');
        $name = $request->file('pdfFile')->getClientOriginalName();
        $invNum = $request->route('invoice');
        $invoiceSch = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);
        $invoice_id = $invoiceSch->invb2b_id;
        $currency = $request->route('currency');
        $dataAxis = $this->axisRepository->getAxisByType('invoice');



        $attachmentDetails = [
            'sign_status' => 'signed',
            'approve_date' => Carbon::now()
        ];

        $invoiceAttachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('B2B', $invoice_id, $currency);

        if ($invoiceAttachment->sign_status == 'signed') {
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
                    'type' => 'invoice',
                ];

                if (isset($dataAxis)) {
                    $this->axisRepository->updateAxis($dataAxis->id, $axis);
                } else {

                    $this->axisRepository->createAxis($axis);
                }
            }

            $this->invoiceAttachmentRepository->updateInvoiceAttachment($invoiceAttachment->id, $attachmentDetails);
            if (!$pdfFile->storeAs('public/uploaded_file/invoice/sch_prog/', $name))
                throw new Exception('Failed to store signed invoice file');

            $data['title'] = 'Invoice No. ' . $invoice_id . ' has been signed';
            $data['invoice_id'] = $invoice_id;

            # send mail when document has been signed
            Mail::send('pages.invoice.school-program.mail.signed', $data, function ($message) use ($data, $invoiceAttachment) {
                $message->to(env('FINANCE_CC'), env('FINANCE_NAME'))
                    ->subject($data['title'])
                    ->attach(public_path($invoiceAttachment->attachment));
            });

            DB::commit();
        } catch (Exception $e) {

            Log::error('Failed to update status after being signed : ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to update'], 500);
        }
        return response()->json(['status' => 'success', 'message' => 'Invoice signed successfully']);
    }

    public function sendToClient(Request $request)
    {
        $invNum = $request->route('invoice');
        $currency = $request->route('currency');
        $invoiceSch = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);
        $invoice_id = $invoiceSch->invb2b_id;
        $invoiceAttachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('B2B', $invoice_id, $currency);
        $program_name = $invoiceSch->sch_prog->program->program_name;

        if (!isset($invoiceSch->sch_prog->user)) {
            return response()->json(
                [
                    'message' => 'This program not have PIC, please set PIC before send to client'
                ],
                500
            );
        }

        $data['email'] = $invoiceSch->sch_prog->user->email;
        $data['cc'] = [
            env('CEO_CC'),
            env('FINANCE_CC')
        ];
        $data['recipient'] = $invoiceSch->sch_prog->user->full_name;
        $data['title'] = "Invoice of program " . $program_name;
        $data['param'] = [
            'invb2b_num' => $invNum,
            'currency' => $currency,
            'fullname' => $invoiceSch->sch_prog->user->fullname,
            'program_name' => $invoiceSch->sch_prog->program->program_name
        ];

        try {

            Mail::send('pages.invoice.school-program.mail.client-view', $data, function ($message) use ($data, $invoiceAttachment) {
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

            return response()->json(
                [
                    'message' => 'Something went wrong when sending invoice to client. Please try again'
                ],
                500
            );
        }

        return response()->json(
            [
                'success' => true,
                'message' => "Invoice has been send to client",
            ]
        );
    }

    public function previewPdf(Request $request)
    {
        $invNum = $request->route('invoice');
        $currency = $request->route('currency');

        $invoiceSch = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);

        $companyDetail = [
            'name' => env('ALLIN_COMPANY'),
            'address' => env('ALLIN_ADDRESS'),
            'address_dtl' => env('ALLIN_ADDRESS_DTL'),
            'city' => env('ALLIN_CITY')
        ];

        $pdf = PDF::loadView('pages.invoice.school-program.export.invoice-pdf', [
            'invoiceSch' => $invoiceSch,
            'currency' => $currency,
            'companyDetail' => $companyDetail
        ]);

        return $pdf->stream();
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
