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

    public function prepareForValidation()
    {
        $this->merge([
            'receipt_cat' => 'student',
            'created_at' => $this->receipt_date,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'rec_currency' => 'required|in:idr,usd,gbp,sgd,aud',
            'receipt_amount' => 'required_unless:rec_currency,idr',
            'receipt_amount_idr' => 'required_if:rec_currency,idr',
            'receipt_date' => 'required|date',
            'receipt_words' => 'required_unless:rec_currency,idr',
            'receipt_words_idr' => 'required_if:rec_currency,idr',
            'receipt_method' => 'required|in:Wire Transfer,Cash,Cheque',
            'receipt_cheque' => 'required_if:receipt_method,Cheque',
            'pph23' => 'nullable',
            'receipt_cat' => 'nullable',
            'created_at' => 'nullable',

            # from form blade
            'identifier' => 'nullable',
            'paymethod' => 'nullable',
            'clientprog_id' => 'required',
            'is_child_program_bundle' => 'nullable',
        ];
    }
}
