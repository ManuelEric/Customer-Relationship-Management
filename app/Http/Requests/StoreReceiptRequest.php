<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReceiptRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'rec_currency' => 'required|in:idr,usd,gbp,sgd',
            'receipt_amount' => 'required_unless:rec_currency,idr',
            'receipt_amount_idr' => 'required_if:rec_currency,idr',
            'receipt_date' => 'required|date',
            'receipt_words' => 'required_unless:rec_currency,idr',
            'receipt_words_idr' => 'required_if:rec_currency,idr',
            'receipt_method' => 'required|in:Wire Transfer,Cash,Cheque',
            'receipt_cheque' => 'required_if:receipt_method,Cheque'
        ];
    }
}
