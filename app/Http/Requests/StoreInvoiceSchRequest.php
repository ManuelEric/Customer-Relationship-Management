<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceSchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'invb2b_price' => 'Price',
            'invb2b_priceidr' => 'Price',
            'invb2b_participants' => 'Participants',
            'invb2b_participants_other' => 'Participants',
            'invb2b_disc' => 'Discount',
            'invb2b_discidr' => 'Discount',
            'invb2b_totprice' => 'Total price',
            'invb2b_totpriceidr' => 'Total price',
            'invb2b_words' => 'Words',
            'invb2b_wordsidr' => 'Words',
            'invb2b_date' => 'Invoice date',
            'invb2b_duedate' => 'Invoice due date',
            'invb2b_pm' => 'Payment Method',
            'invb2b_notes' => 'Notes',
            'invb2b_tnc' => 'Terms & Condition'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'invb2b_price' => 'required_if:select_currency,other|integer|nullable',
            'invb2b_priceidr' => 'required_if:select_currency,idr|integer|nullable',
            'invb2b_participants' => 'required_if:select_currency,idr|integer|nullable',
            'invb2b_participants_other' => 'required_if:select_currency,other|integer|nullable',
            'invb2b_disc' => 'required_if:select_currency,other|integer|nullable',
            'invb2b_discidr' => 'required_if:select_currency,idr|integer|nullable',
            'invb2b_totprice' => 'required_if:select_currency,other|integer|nullable',
            'invb2b_totpriceidr' => 'required_if:select_currency,idr|integer|nullable',
            'invb2b_words' => 'required_if:select_currency,other|nullable',
            'invb2b_wordsidr' => 'required_if:select_currency,idr|nullable',
            'invb2b_date' => 'required|date',
            'invb2b_duedate' => 'required|date',
            'invb2b_pm' => 'required|in:full,installment',
            'invb2b_notes' => 'nullable',
            'invb2b_tnc' => 'nullable',
            'cursrate' => 'required_if:select_currency,other|integer|nullable',
            'currency' => 'required_if:select_currency,other|in:GDP,USD,SGD|nullable',
        ];
    }
}
