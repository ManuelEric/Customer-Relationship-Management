<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUniversityRequest extends FormRequest
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

    public function messages()
    {
        return [
            'univ_name.required' => 'The University Name field is required',
            'univ_country.required' => 'The Country field is required',
            'univ_country.exists' => 'The Country is invalid',
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
            'univ_name' => 'required',
            'univ_email' => 'nullable',
            'univ_phone' => 'nullable',
            'univ_country' => 'required|exists:tbl_country,id',
            'univ_address' => 'nullable',
            // 'tag' => 'required|exists:tbl_curriculum,id',
        ];
    }
}