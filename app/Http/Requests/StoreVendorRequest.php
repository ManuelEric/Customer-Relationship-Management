<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendorRequest extends FormRequest
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
            'vendor_name' => 'required',
            'vendor_address' => 'nullable',
            'vendor_phone' => 'required',
            'vendor_type' => 'nullable',
            'vendor_material' => 'nullable',
            'vendor_size' => 'nullable',
            'vendor_unitprice' => 'required|numeric',
            'vendor_processingtime' => 'nullable',
            'vendor_notes' => 'nullable'
        ];
    }
}
