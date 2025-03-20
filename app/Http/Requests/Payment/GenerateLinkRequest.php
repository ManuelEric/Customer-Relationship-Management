<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class GenerateLinkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'payment_method' => $this->route('payment_method'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payment_method' => 'required|in:CC,VA',
            'bank' => 'required_if:payment_method,VA|in:BCA,BRI,NIAGA,MANDIRI',
            'installment' => 'required',
            'id' => 'required'
        ];
    }

    public function attributes(): array
    {
        return [
            
            'payment_method' => 'Payment Method',
            'bank' => 'Bank Name',
            'id' => 'Identifier',
        ];
    }


}
