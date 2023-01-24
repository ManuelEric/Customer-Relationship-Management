<?php

namespace App\Http\Requests;

use App\Interfaces\InvoiceB2bRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;

class StoreRefundSchoolRequest extends FormRequest
{

    private InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function __construct(InvoiceB2bRepositoryInterface $invoiceB2bRepository)
    {
        $this->invoiceB2bRepository = $invoiceB2bRepository;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $invb2b_num = $this->route('invoice');
        $invoice = $this->invoiceB2bRepository->getInvoiceB2bById($invb2b_num);
        $total_payment = $invoice->invb2b_totpriceidr;
        $total_paid = $invoice->receipt()->sum('receipt_amount_idr');

        return [
            'total_price' => 'required|numeric|in:' . $total_payment,
            'total_payment' => 'required|numeric|in:' . $total_paid,
            'percentage_payment' => 'nullable|numeric|max:100',
            'refunded_amount' => 'required|numeric',
            'refunded_tax_percentage' => 'nullable|numeric|max:100',
            'refunded_tax_amount' => 'nullable|numeric',
            'total_refunded' => 'required|numeric|lte:total_paid',

        ];
    }
}
