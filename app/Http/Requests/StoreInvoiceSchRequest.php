<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use Illuminate\Support\Facades\Request;

class StoreInvoiceSchRequest extends FormRequest
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
            'invb2b_price' => 'Price',
            'invb2b_priceidr_other' => 'Price',
            'invb2b_priceidr' => 'Price',
            'invb2b_participants' => 'Participants',
            'invb2b_participants_other' => 'Participants',
            'invb2b_disc' => 'Discount',
            'invb2b_discidr_other' => 'Discount',
            'invb2b_discidr' => 'Discount',
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
            'invdtl_installment.*' => 'Installment Name',
            'invdtl_installment_other.*' => 'Installment Name',
            'invdtl_duedate.*' => 'Installment Due Date',
            'invdtl_duedate_other.*' => 'Installment Due Date',
            'invdtl_percentage.*' => 'Installment Percentage',
            'invdtl_percentage_other.*' => 'Installment Percentage',
            'invdtl_amountidr.*' => 'Installment Amount',
            'invdtl_amount_other.*' => 'Installment Amount',
            'invdtl_amountidr_other.*' => 'Installment Amount',
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
            'invb2b_priceidr' => 'required|integer',
            'invb2b_participants' => 'required|integer',
            'invb2b_discidr' => 'required|integer',
            'invb2b_totpriceidr' => 'required|integer',
            'invb2b_wordsidr' => 'required',
            'invb2b_date' => 'required|date|before_or_equal:invb2b_duedate',
            'invb2b_duedate' => 'required|date|after_or_equal:invb2b_date',
            'invb2b_pm' => 'required|in:Full Payment,Installment',
            'invb2b_notes' => 'nullable',
            'invb2b_tnc' => 'nullable',
            'invdtl_installment.*' => 'required_if:invb2b_pm,Installment|distinct',

            // 'invdtl_installment.*' => [Rule::when('invdtl_installment', 'required', function ($input) {
            //     return $this->input('select_currency') == 'idr' && $this->input('invb2b_pm') == 'installment';
            // })],
            // 'invdtl_installment_other.*' => [Rule::when('invdtl_installment_other', 'required', function ($input) {
            //     return $input->select_currency == 'other' && $input->invb2b_pm == 'installment';
            // })],
            // 'invdtl_duedate.*' => [Rule::when('invdtl_duedate.*', function ($input) {
            //     return request()->select_currency == 'idr' && request()->invb2b_pm == 'installment';
            // })],
            // 'invdtl_duedate.*' => 'required_unless:invb2b_pm,full|required_if:select_currency,idr|date|before_or_equal:invb2b_duedate|after_or_equal:invb2b_date|nullable',
            // 'invdtl_duedate_other.*' => 'required_unless:invb2b_pm,full|required_if:select_currency,other|required_with:invdtl_pm|date|before_or_equal:invb2b_duedate|after_or_equal:invb2b_date|nullable',
            'invdtl_duedate.*' => 'required_if:invb2b_pm,Installment|nullable|date|before_or_equal:invb2b_duedate|after_or_equal:invb2b_date',
            'invdtl_percentage.*' => 'required_if:invb2b_pm,Installment',
            'invdtl_amountidr.*' => 'required_if:invb2b_pm,Installment',
            // 'date', 'before_or_equal:invb2b_duedate', 'after_or_equal:invb2b_date'


            // 'date', 'before_or_equal:invb2b_duedate', 'after_or_equal:invb2b_date'


        ];
    }

    protected function other()
    {

        return [
            'invb2b_price' => 'required|integer',
            'invb2b_priceidr_other' => 'required|integer',
            'invb2b_participants_other' => 'required|integer',
            'invb2b_disc' => 'required|integer',
            'invb2b_discidr_other' => 'required|integer',
            'invb2b_totprice' => 'required|integer',
            'invb2b_totpriceidr_other' => 'required|integer',
            'invb2b_words' => 'required',
            'invb2b_wordsidr_other' => 'required',
            'invb2b_date' => 'required|date|before_or_equal:invb2b_duedate',
            'invb2b_duedate' => 'required|date|after_or_equal:invb2b_date',
            'invb2b_pm' => 'required|in:Full Payment,Installment',
            'invb2b_notes' => 'nullable',
            'invb2b_tnc' => 'nullable',
            'curs_rate' => 'integer|nullable',
            'currency' => 'in:gbp,usd,sgd|nullable',
            // 'invdtl_installment.*' => 'required_if:invb2b_pm,installment|nullable',
            // 'invdtl_installment.*' => ['required_if:invb2b_pm,installment','nullable', Rule::unique('tbl_invdtl', 'invdtl_installment')->where(fn ($query) => $query->where('invb2b_id', $invoiceB2b->invb2b_id))],
            // 'invdtl_installment_other.*' => 'required_if:invb2b_pm,installment|nullable',
            // 'invdtl_duedate.*' => 'required_unless:invb2b_pm,installment|required_if:select_currency,idr|date|before_or_equal:invb2b_duedate|after_or_equal:invb2b_date|nullable',
            // 'invdtl_duedate_other.*' => 'required_unless:invb2b_pm,full|required_if:select_currency,other|required_with:invdtl_pm|date|before_or_equal:invb2b_duedate|after_or_equal:invb2b_date|nullable',

            'invdtl_installment_other.*' => 'required_if:invb2b_pm,Installment|distinct',

            'invdtl_duedate_other.*' => 'required_if:invb2b_pm,Installment|nullable|date|before_or_equal:invb2b_duedate|after_or_equal:invb2b_date',
            'invdtl_percentage_other.*' => 'required_if:invb2b_pm,Installment',
            'invdtl_amount_other.*' => 'required_if:invb2b_pm,Installment',
            'invdtl_amountidr_other.*' => 'required_if:invb2b_pm,Installment',


        ];
    }
}
