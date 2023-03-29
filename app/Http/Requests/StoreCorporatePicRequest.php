<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCorporatePicRequest extends FormRequest
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
            'pic_name.required' => 'The full name field is required',
            'pic_name.alpha' => 'The full name must only contain letters',
            'pic_phone.required' => 'The phone number field is required',
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
            'pic_name' => 'required|alpha',
            'pic_mail' => 'nullable|email',
            'pic_phone' => 'required',
            'pic_linkedin' => 'nullable|url'
        ];
    }
}
