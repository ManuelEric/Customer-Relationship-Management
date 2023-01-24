<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRefundRequest extends FormRequest
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
            'total_price' => 'required',
            'total_payment' => 'required',
            'percentage_payment' => 'nullable|numeric|max:100',
            'refunded_amount' => 'required',
            'refunded_tax_percentage' => 'nullable|numeric|max:100',
            'refunded_tax_amount' => 'nullable',
            'total_refunded' => 'required',
        ];
    }
}
