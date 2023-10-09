<?php

namespace App\Http\Controllers;

use App\Interfaces\InvoiceAttachmentRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use Illuminate\Http\Request;

class InvoiceB2BBaseControler extends Controller
{
    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    protected InvoiceAttachmentRepositoryInterface $invoiceAttachmentRepository;
    protected $module;

    public function __construct(
        InvoiceB2bRepositoryInterface $invoiceB2bRepository, 
        InvoiceAttachmentRepositoryInterface $invoiceAttachmentRepository,
        $module)
    {
        $this->invoiceB2bRepository = $invoiceB2bRepository;
        $this->invoiceAttachmentRepository = $invoiceAttachmentRepository;


    }

    private function GetModule(Request $request)
    {
        switch ($request->segment(2)) {

            case "corporate-program":
                $this->module = "partner_prog";
                break;

        }
    }

    public function export(Request $request)
    {
        $a = $this->module;
        echo $a;exit;
        $invNum = $request->route('invoice');
        $currency = $request->route('currency');

        $invoiceB2B = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);
        $invoice_id = $invoiceB2B->invb2b_id;

        $invoiceAttachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('B2B', $invoice_id, $currency);

        return view('pages.invoice.view-pdf')->with([
            'invoiceAttachment' => $invoiceAttachment,
        ]);
    }
}
