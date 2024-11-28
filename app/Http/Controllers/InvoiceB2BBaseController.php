<?php

namespace App\Http\Controllers;

use App\Enum\LogModule;
use App\Http\Traits\DirectorListTrait;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\InvoiceAttachmentRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PDF;

class InvoiceB2BBaseController extends Controller
{
    use DirectorListTrait;
    use LoggingTrait;

    public function getModule()
    {
        switch (request()->segment(2)) {

            case "corporate-program":
            case "invoice-corp":
                $this->module = [
                    'raw' => 'Corporate Program',
                    'segment' => 'corporate-program',
                    'name' => 'partner_prog',
                    'subject' => [
                        'class' => 'corp',
                        'attribute' => 'corp_name',
                        'sub_class' => 'pic',
                        'pic' => [
                            'name' => 'pic_name',
                            'email' => 'pic_mail'
                        ]
                    ],
                    'program' => [
                        'class' => 'program',
                        'attribute' => 'program_name'
                    ]
                ];
                break;

            case "referral":
            case "invoice-ref":
                $this->module = [
                    'raw' => 'Referral Program',
                    'segment' => 'referral',
                    'name' => 'referral',
                    'subject' => [
                        'class' => 'partner',
                        'attribute' => 'corp_name',
                        'sub_class' => 'pic',
                        'pic' => [
                            'name' => 'pic_name',
                            'email' => 'pic_mail',
                        ]
                    ],
                    'program' => [
                        'class' => null,
                        'attribute' => 'additional_prog_name'
                    ]
                ];
                break;

            case "school-program":
            case "invoice-sch":
                $this->module = [
                    'raw' => 'School Program',
                    'segment' => 'school-program',
                    'name' => 'sch_prog',
                    'subject' => [
                        'class' => 'school',
                        'attribute' => 'sch_name',
                        'sub_class' => 'detail',
                        'pic' => [
                            'name' => 'schdetail_fullname',
                            'email' => 'schdetail_email'
                        ]
                    ],
                    'program' => [
                        'class' => 'program',
                        'attribute' => 'program_name'
                    ]
                ];

                break;

        }

        return $this->module;
    }

    public function export(Request $request)
    {
        $inv_num = $request->route('invoice');
        $currency = $request->route('currency');

        $invoice_B2B = $this->invoiceB2bRepository->getInvoiceB2bById($inv_num);
        $invoice_id = $invoice_B2B->invb2b_id;

        $invoice_attachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('B2B', $invoice_id, $currency);

        return view('pages.invoice.view-pdf')->with([
            'invoiceAttachment' => $invoice_attachment,
        ]);
    }

    public function requestSign(Request $request, LogService $log_service)
    {
        $inv_num = $request->route('invoice');
        $currency = $request->route('currency');
        $to = $request->get('to');
        $name = $this->getDirectorByEmail($to);

        $invoice_b2b = $this->invoiceB2bRepository->getInvoiceB2bById($inv_num);
        $invoice_id = $invoice_b2b->invb2b_id;
        $invoice_num = $invoice_b2b->invb2b_num;
        $file_name = str_replace('/', '-', $invoice_id) . '-' . ($currency == 'idr' ? $currency : 'other') . '.pdf'; # 0001_INV_JEI_EF_I_23_idr.pdf

        $path = 'uploaded_file/invoice/'.$this->module['name'].'/';
        $attachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('B2B', $invoice_id, $currency);

        $attachment_details = [
            'invb2b_id' => $invoice_id,
            'currency' => $currency,
            'recipient' => $to,
            'attachment' => 'storage/' . $path . $file_name,
        ];

        $company_detail = [
            'name' => env('ALLIN_COMPANY'),
            'address' => env('ALLIN_ADDRESS'),
            'address_dtl' => env('ALLIN_ADDRESS_DTL'),
            'city' => env('ALLIN_CITY')
        ];

        $data['email'] = $to; # our director email
        $data['recipient'] = $name; # our director name
        $data['title'] = "Request Sign of Invoice Number : " . $invoice_id;
        $data['param'] = [
            'invb2b_num' => $invoice_num,
            'currency' => $currency,
            'fullname' => $invoice_b2b->{$this->module['name']}->{$this->module['subject']['class']}->{$this->module['subject']['attribute']},
            'invoice_date' => date('d F Y', strtotime($invoice_b2b->invb2b_date)),
            'invoice_duedate' => date('d F Y', strtotime($invoice_b2b->invb2b_duedate))
        ];

        # condition
        if (isset($this->module['program']['class']))
            $data['param']['program_name'] = $invoice_b2b->{$this->module['name']}->{$this->module['program']['class']}->{$this->module['program']['attribute']};
        else
            $data['param']['program_name'] = $invoice_b2b->{$this->module['name']}->{$this->module['program']['attribute']};

        try {

            $pdf = PDF::loadView('pages.invoice.'.$this->module['segment'].'.export.invoice-pdf', [
                'invoiceB2b' => $invoice_b2b,
                'currency' => $currency,
                'companyDetail' => $company_detail,
                'director' => $name
            ]);

            # Generate PDF file
            $content = $pdf->download();
            Storage::disk('public')->put($path . $file_name, $content);

            # if attachment exist then update attachement else insert attachement
            if (isset($attachment)) {
                $this->invoiceAttachmentRepository->updateInvoiceAttachment($attachment->id, $attachment_details);
            } else {
                $this->invoiceAttachmentRepository->createInvoiceAttachment($attachment_details);
            }

            Mail::send('pages.invoice.'.$this->module['segment'].'.mail.view', $data, function ($message) use ($data, $pdf, $invoice_id) {
                $message->to($data['email'], $data['recipient'])
                    ->subject($data['title'])
                    ->attachData($pdf->output(), $invoice_id . '.pdf');
            });
        } catch (Exception $e) {

            $log_service->createErrorLog(LogModule::REQUEST_SIGN_INVOICE_B2B, $e->getMessage(), $e->getLine(), $e->getFile(), $attachment_details);
            return $e->getMessage();
        }

        # Request Sign success
        # create log success
        $log_service->createSuccessLog(LogModule::REQUEST_SIGN_INVOICE_B2B, 'Successfully Send Request Sign', $attachment_details);

        return true;
    }

    public function signAttachment(Request $request)
    {
        $inv_num = $request->route('invoice');
        $currency = $request->route('currency');
        $invoice_b2b = $this->invoiceB2bRepository->getInvoiceB2bById($inv_num);
        $invoice_id = $invoice_b2b->invb2b_id;
        $invoice_attachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('B2B', $invoice_id, $currency);
        $axis = $this->axisRepository->getAxisByType('invoice');

        if (isset($invoice_attachment->sign_status) && $invoice_attachment->sign_status == 'signed') {
            return "Invoice is already signed";
        }

        return view('pages.invoice.sign-pdf')->with(
            [
                'attachment' => $invoice_attachment->attachment,
                'axis' => $axis,
                'currency' => $currency,
                'invoice' => $invoice_b2b,
            ]
        );
    }

    public function upload(Request $request, LogService $log_service)
    {
        $pdf_file = $request->file('pdfFile');
        $name = $request->file('pdfFile')->getClientOriginalName();
        $inv_num = $request->route('invoice');
        $invoice_b2b = $this->invoiceB2bRepository->getInvoiceB2bById($inv_num);
        $invoice_id = $invoice_b2b->invb2b_id;
        $currency = $request->route('currency');
        $data_axis = $this->axisRepository->getAxisByType('invoice');

        $attachment_details = [
            'sign_status' => 'signed',
            'approve_date' => Carbon::now()
        ];

        $invoice_attachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('B2B', $invoice_id, $currency);

        if ($invoice_attachment->sign_status == 'signed') {
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
                    'type' => 'invoice'
                ];

                if (isset($data_axis)) {
                    $this->axisRepository->updateAxis($data_axis->id, $axis);
                } else {
                    $this->axisRepository->createAxis($axis);
                }
            }

            $this->invoiceAttachmentRepository->updateInvoiceAttachment($invoice_attachment->id, $attachment_details);

            if (!$pdf_file->storeAs('public/uploaded_file/invoice/'.$this->module['name'].'/', $name))
                throw new Exception('Failed to store signed invoice file');

            $data['title'] = 'Invoice No. ' . $invoice_id . ' has been signed';
            $data['invoice_id'] = $invoice_id;

            # send mail when document has been signed
            Mail::send('pages.invoice.'.$this->module['segment'].'.mail.signed', $data, function ($message) use ($data, $invoice_attachment) {
                $message->to(env('FINANCE_CC'), env('FINANCE_NAME'))
                    ->subject($data['title'])
                    ->attach(public_path($invoice_attachment->attachment));
            });

            DB::commit();
        } catch (Exception $e) {

            $log_service->createErrorLog(LogModule::REQUEST_SIGN_INVOICE_B2B, $e->getMessage(), $e->getLine(), $e->getFile(), $attachment_details);
            return response()->json(['status' => 'error', 'message' => 'Failed to update'], 500);
        }

        # Signed success
        # create log success
        $log_service->createSuccessLog(LogModule::APPROVE_ATTACHMENT_INVOICE_B2B, 'Successfully signed invoice', $invoice_attachment->toArray());

        return response()->json(['status' => 'success', 'message' => 'Invoice signed successfully']);
    }

    public function previewPdf(Request $request)
    {
        $inv_num = $request->route('invoice');
        $currency = $request->route('currency');

        $invoice_b2b = $this->invoiceB2bRepository->getInvoiceB2bById($inv_num);

        $company_detail = [
            'name' => env('ALLIN_COMPANY'),
            'address' => env('ALLIN_ADDRESS'),
            'address_dtl' => env('ALLIN_ADDRESS_DTL'),
            'city' => env('ALLIN_CITY')
        ];

        $pdf = PDF::loadView('pages.invoice.'.$this->module['segment'].'.export.invoice-pdf', [
            'invoiceB2b' => $invoice_b2b,
            'currency' => $currency,
            'companyDetail' => $company_detail
        ]);
        
        return $pdf->stream();
    }

    public function sendToClient(Request $request, LogService $log_service)
    {
        $inv_num = $request->route('invoice');
        $currency = $request->route('currency');
        $invoice_b2b = $this->invoiceB2bRepository->getInvoiceB2bById($inv_num);
        $invoice_id = $invoice_b2b->invb2b_id;
        $invoice_attachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('B2B', $invoice_id, $currency);

        # because for referral
        # they fetching using **->referral->additional_prog_name
        if ($this->module['name'] != 'referral'){
            $program_name = $invoice_b2b->{$this->module['name']}->{$this->module['program']['class']}->{$this->module['program']['attribute']};
            $param_program_name = isset($invoice_b2b->{$this->module['name']}->{$this->module['program']['class']}->sub_prog) ? $invoice_b2b->{$this->module['name']}->{$this->module['program']['class']}->main_prog->prog_name . ' - ' . $invoice_b2b->{$this->module['name']}->{$this->module['program']['class']}->sub_prog->sub_prog_name : $invoice_b2b->{$this->module['name']}->{$this->module['program']['class']}->main_prog->prog_name;
        } else {
            $program_name = $invoice_b2b->{$this->module['name']}->{$this->module['program']['attribute']};
            $param_program_name = $program_name;
        }


        if (!isset($invoice_b2b->{$this->module['name']}->user)) {
            return response()->json(
                [
                    'message' => 'This program not have PIC, please set PIC before send to client'
                ],
                500
            );
        }

        # get partner pic
        #$getPartnerPics = $invoicePartner->partner_prog->corp->pic->where('is_pic', '1')->toArray();
        #$pic = $getPartnerPics[0];

        # uncomment if they want the email send directly to pic partner 
        #$data['email'] = $pic->pic_mail;
        #$data['recipient'] = $pic->pic_name;

        # validate the their pic email
        if (!isset($invoice_b2b->{$this->module['name']}->{$this->module['subject']['class']}->{$this->module['subject']['sub_class']}[0]->{$this->module['subject']['pic']['email']}) || $invoice_b2b->{$this->module['name']}->{$this->module['subject']['class']}->{$this->module['subject']['sub_class']}[0]->{$this->module['subject']['pic']['email']} == '')
            return response()->json(['message' => "Please complete their email in order to send the invoice mail"], 500);
        

        $data['email'] = $invoice_b2b->{$this->module['name']}->{$this->module['subject']['class']}->{$this->module['subject']['sub_class']}[0]->{$this->module['subject']['pic']['email']}; # email to pic of the partner program
        $data['recipient'] = $invoice_b2b->{$this->module['name']}->{$this->module['subject']['class']}->{$this->module['subject']['sub_class']}[0]->{$this->module['subject']['pic']['name']}; # name of the pic of the partner program
        $data['cc'] = [env('CEO_CC'), env('FINANCE_CC')];
        $data['title'] = "Invoice of program " . $program_name;
        $data['param'] = [
            'invb2b_num' => $inv_num,
            'currency' => $currency,
            'fullname' => $invoice_b2b->{$this->module['name']}->{$this->module['subject']['class']}->{$this->module['subject']['attribute']},
            'program_name' => $param_program_name, # main prog name - sub prog name
        ];

        try {

            Mail::send('pages.invoice.'.$this->module['segment'].'.mail.client-view', $data, function ($message) use ($data, $invoice_attachment) {
                $message->to($data['email'], $data['recipient'])
                    ->cc($data['cc'])
                    ->subject($data['title'])
                    ->attach(public_path($invoice_attachment->attachment));
            });

            $attachment_details = [
                'send_to_client' => 'sent',
            ];

            $this->invoiceAttachmentRepository->updateInvoiceAttachment($invoice_attachment->id, $attachment_details);
        } catch (Exception $e) {

            $log_service->createErrorLog(LogModule::SEND_INVOICE_B2B_TO_CLIENT, $e->getMessage(), $e->getLine(), $e->getFile(), $invoice_attachment->toArray());

            return response()->json(
                [
                    'message' => 'Something went wrong when sending invoice to client. Please try again'
                ],
                500
            );
        }

        # Send To Client success
        # create log success
        $log_service->createSuccessLog(LogModule::SEND_INVOICE_B2B_TO_CLIENT, 'Successfully Send invoice to client', $invoice_attachment->toArray());

        // return true;
        return response()->json(
            [
                'success' => true,
                'message' => "Invoice has been send to client",
            ]
        );
    }
}
