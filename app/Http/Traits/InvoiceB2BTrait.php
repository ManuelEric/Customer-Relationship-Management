<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

trait InvoiceB2BTrait
{

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
}
