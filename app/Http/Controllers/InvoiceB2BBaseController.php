<?php

namespace App\Http\Controllers;

use App\Http\Traits\DirectorListTrait;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\InvoiceAttachmentRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
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
        $invNum = $request->route('invoice');
        $currency = $request->route('currency');

        $invoiceB2B = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);
        $invoice_id = $invoiceB2B->invb2b_id;

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
        $name = $this->getDirectorByEmail($to);

        $invoiceB2b = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);
        $invoice_id = $invoiceB2b->invb2b_id;
        $invoice_num = $invoiceB2b->invb2b_num;
        $file_name = str_replace('/', '-', $invoice_id) . '-' . ($currency == 'idr' ? $currency : 'other') . '.pdf'; # 0001_INV_JEI_EF_I_23_idr.pdf

        $path = 'uploaded_file/invoice/'.$this->module['name'].'/';
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

        $data['email'] = $to; # our director email
        $data['recipient'] = $name; # our director name
        $data['title'] = "Request Sign of Invoice Number : " . $invoice_id;
        $data['param'] = [
            'invb2b_num' => $invoice_num,
            'currency' => $currency,
            'fullname' => $invoiceB2b->{$this->module['name']}->{$this->module['subject']['class']}->{$this->module['subject']['attribute']},
            'invoice_date' => date('d F Y', strtotime($invoiceB2b->invb2b_date)),
            'invoice_duedate' => date('d F Y', strtotime($invoiceB2b->invb2b_duedate))
        ];

        # condition
        if (isset($this->module['program']['class']))
            $data['param']['program_name'] = $invoiceB2b->{$this->module['name']}->{$this->module['program']['class']}->{$this->module['program']['attribute']};
        else
            $data['param']['program_name'] = $invoiceB2b->{$this->module['name']}->{$this->module['program']['attribute']};

        try {

            $pdf = PDF::loadView('pages.invoice.'.$this->module['segment'].'.export.invoice-pdf', [
                'invoiceB2b' => $invoiceB2b,
                'currency' => $currency,
                'companyDetail' => $companyDetail,
                'director' => $name
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

            Mail::send('pages.invoice.'.$this->module['segment'].'.mail.view', $data, function ($message) use ($data, $pdf, $invoice_id) {
                $message->to($data['email'], $data['recipient'])
                    ->subject($data['title'])
                    ->attachData($pdf->output(), $invoice_id . '.pdf');
            });
        } catch (Exception $e) {

            Log::info('Failed to request sign invoice '.$this->module['raw'].' : ' . $e->getMessage());
            return $e->getMessage();
        }

        # Request Sign success
        # create log success
        $this->logSuccess('request-sign', null, 'Invoice B2B', Auth::user()->first_name . ' '. Auth::user()->last_name, ['invoice_id' => $invoice_id]);

        return true;
    }

    public function signAttachment(Request $request)
    {
        $invNum = $request->route('invoice');
        $currency = $request->route('currency');
        $invoiceB2b = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);
        $invoice_id = $invoiceB2b->invb2b_id;
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
                'invoice' => $invoiceB2b,
            ]
        );
    }

    public function upload(Request $request)
    {
        $pdfFile = $request->file('pdfFile');
        $name = $request->file('pdfFile')->getClientOriginalName();
        $invNum = $request->route('invoice');
        $invoiceB2b = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);
        $invoice_id = $invoiceB2b->invb2b_id;
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
                    'type' => 'invoice'
                ];

                if (isset($dataAxis)) {
                    $this->axisRepository->updateAxis($dataAxis->id, $axis);
                } else {
                    $this->axisRepository->createAxis($axis);
                }
            }

            $this->invoiceAttachmentRepository->updateInvoiceAttachment($invoiceAttachment->id, $attachmentDetails);

            if (!$pdfFile->storeAs('public/uploaded_file/invoice/'.$this->module['name'].'/', $name))
                throw new Exception('Failed to store signed invoice file');

            $data['title'] = 'Invoice No. ' . $invoice_id . ' has been signed';
            $data['invoice_id'] = $invoice_id;

            # send mail when document has been signed
            Mail::send('pages.invoice.'.$this->module['segment'].'.mail.signed', $data, function ($message) use ($data, $invoiceAttachment) {
                $message->to(env('FINANCE_CC'), env('FINANCE_NAME'))
                    ->subject($data['title'])
                    ->attach(public_path($invoiceAttachment->attachment));
            });

            DB::commit();
        } catch (Exception $e) {
            Log::error('Failed to update status after being signed '.$this->module['raw'].' : ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to update'], 500);
        }

        # Signed success
        # create log success
        $this->logSuccess('signed', null, 'Invoice B2B', 'Director', ['invoice_id' => $invoice_id]);

        return response()->json(['status' => 'success', 'message' => 'Invoice signed successfully']);
    }

    public function previewPdf(Request $request)
    {
        $invNum = $request->route('invoice');
        $currency = $request->route('currency');

        $invoiceB2b = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);

        $companyDetail = [
            'name' => env('ALLIN_COMPANY'),
            'address' => env('ALLIN_ADDRESS'),
            'address_dtl' => env('ALLIN_ADDRESS_DTL'),
            'city' => env('ALLIN_CITY')
        ];

        $pdf = PDF::loadView('pages.invoice.'.$this->module['segment'].'.export.invoice-pdf', [
            'invoiceB2b' => $invoiceB2b,
            'currency' => $currency,
            'companyDetail' => $companyDetail
        ]);

        return $pdf->stream();
    }

    public function sendToClient(Request $request)
    {
        $invNum = $request->route('invoice');
        $currency = $request->route('currency');
        $invoiceB2b = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);
        $invoice_id = $invoiceB2b->invb2b_id;
        $invoiceAttachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('B2B', $invoice_id, $currency);

        # because for referral
        # they fetching using **->referral->additional_prog_name
        if ($this->module['name'] != 'referral')
            $program_name = $invoiceB2b->{$this->module['name']}->{$this->module['program']['class']}->{$this->module['program']['attribute']};
        else
            $program_name = $invoiceB2b->{$this->module['name']}->{$this->module['program']['attribute']};

        $param_program_name = isset($invoiceB2b->{$this->module['name']}->{$this->module['program']['class']}->sub_prog) ? $invoiceB2b->{$this->module['name']}->{$this->module['program']['class']}->main_prog->prog_name . ' - ' . $invoiceB2b->{$this->module['name']}->{$this->module['program']['class']}->sub_prog->sub_prog_name : $invoiceB2b->{$this->module['name']}->{$this->module['program']['class']}->main_prog->prog_name;

        if (!isset($invoiceB2b->{$this->module['name']}->user)) {
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
        if (!isset($invoiceB2b->{$this->module['name']}->{$this->module['subject']['class']}->{$this->module['subject']['sub_class']}[0]->{$this->module['subject']['pic']['email']}))
            return response()->json(['message' => "Please complete their email in order to send the invoice mail"], 500);
        

        $data['email'] = $invoiceB2b->{$this->module['name']}->{$this->module['subject']['class']}->{$this->module['subject']['sub_class']}[0]->{$this->module['subject']['pic']['email']}; # email to pic of the partner program
        $data['recipient'] = $invoiceB2b->{$this->module['name']}->{$this->module['subject']['class']}->{$this->module['subject']['sub_class']}[0]->{$this->module['subject']['pic']['name']}; # name of the pic of the partner program
        $data['cc'] = [env('CEO_CC'), env('FINANCE_CC')];
        $data['title'] = "Invoice of program " . $program_name;
        $data['param'] = [
            'invb2b_num' => $invNum,
            'currency' => $currency,
            'fullname' => $invoiceB2b->{$this->module['name']}->{$this->module['subject']['class']}->{$this->module['subject']['attribute']},
            'program_name' => $param_program_name, # main prog name - sub prog name
        ];


        try {

            Mail::send('pages.invoice.'.$this->module['segment'].'.mail.client-view', $data, function ($message) use ($data, $invoiceAttachment) {
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

            Log::info('Failed to send invoice to client '.$this->module['raw'].' : ' . $e->getMessage());

            return response()->json(
                [
                    'message' => 'Something went wrong when sending invoice to client. Please try again'
                ],
                500
            );
        }

        # Send To Client success
        # create log success
        $this->logSuccess('send-to-client', null, 'Invoice B2B', Auth::user()->first_name . ' '. Auth::user()->last_name, ['invoice_id' => $invoice_id]);


        // return true;
        return response()->json(
            [
                'success' => true,
                'message' => "Invoice has been send to client",
            ]
        );
    }
}
