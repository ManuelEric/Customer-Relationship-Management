<?php

namespace App\Http\Requests;

use App\Models\ClientProgram;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvoiceProgramRequest extends FormRequest
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
        $clientProgId = $this->input('clientProgId');
        $clientProgram = ClientProgram::find($clientProgId);

        $rules = [
            'clientProgIds' => 'required|exists:tbl_client_prog,clientprog_id',
            'currency' => [
                'required',
                function ($attribute, $value, $fail) use ($clientProgram) {
                    if ($index = array_search('other', $value) !== false)
                        unset($value[$index]);

                    if ($value[0] != $clientProgram->program->prog_payment)
                        $fail('Based on payment program from master program, it should be '.strtoupper($clientProgram->program->prog_payment));
                }
            ],
            'session' => [
                function ($attribute, $value, $fail) use ($clientProgram) {
                    if ($clientProgram->program->prog_payment == "session" && $value == "no")
                        $fail('Is session has to be "yes" based on master program session');
                }
            ],
            'inv_price_idr' => 'required_if:currency,idr',
            'inv_earlybird_idr' => 'required_if:currency,idr',
            'inv_discount_idr' => 'required_if:currency,idr',
            'inv_totalnumber_idr' => 'required_if:currency,idr',
            'inv_words_idr' => 'required_if:currency,idr',
            'inv_paymentmethod' => 'required',
            'invoice_date' => 'required',
            'inv_duedate' => 'required',
            'inv_notes' => 'required',
            'inv_tnc' => 'nullable'
        ];

        return $rules;
    }
}
