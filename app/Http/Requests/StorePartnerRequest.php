<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePartnerRequest extends FormRequest
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
            'pt_name' => 'required',
            'pt_email' => 'required|email',
            'pt_phone' => 'required',
            'pt_institution' => 'required',
            'pt_address' => 'required'
        ];
    }
}
