<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use Illuminate\Support\Facades\Request;

class StoreInvoiceReferralRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;

    public function __construct(InvoiceB2bRepositoryInterface $invoiceB2bRepository)
    {
        $this->invoiceB2bRepository = $invoiceB2bRepository;
    }

    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'invb2b_totprice' => 'Total price',
            'invb2b_totpriceidr_other' => 'Total price',
            'invb2b_totpriceidr' => 'Total price',
            'invb2b_words' => 'Words',
            'invb2b_wordsidr_other' => 'Words',
            'invb2b_wordsidr' => 'Words',
            'invb2b_date' => 'Invoice date',
            'invb2b_duedate' => 'Invoice due date',
            'invb2b_pm' => 'Payment Method',
            'invb2b_notes' => 'Notes',
            'invb2b_tnc' => 'Terms & Condition',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        // return $this->isMethod('POST') ? $this->store() : $this->update();
        return $this->input('select_currency') == 'idr' ? $this->idr() : $this->other();
    }

    protected function idr()
    {
        return [
            'invb2b_totpriceidr' => 'required|integer',
            'invb2b_wordsidr' => 'required',
            'invb2b_date' => 'required|date|before_or_equal:invb2b_duedate',
            'invb2b_duedate' => 'required|date|after_or_equal:invb2b_date',
            'invb2b_pm' => 'required|in:Full Payment',
            'invb2b_notes' => 'nullable',
            'invb2b_tnc' => 'nullable',
        ];
    }

    protected function other()
    {

        return [
            'invb2b_totprice' => 'required|integer',
            'invb2b_totpriceidr_other' => 'required|integer',
            'invb2b_words' => 'required',
            'invb2b_wordsidr_other' => 'required',
            'invb2b_date' => 'required|date|before_or_equal:invb2b_duedate',
            'invb2b_duedate' => 'required|date|after_or_equal:invb2b_date',
            'invb2b_pm' => 'required|in:Full Payment',
            'invb2b_notes' => 'nullable',
            'invb2b_tnc' => 'nullable',
            'curs_rate' => 'integer|nullable',
            'currency' => 'in:gbp,usd,sgd|nullable',
        ];
    }
}
