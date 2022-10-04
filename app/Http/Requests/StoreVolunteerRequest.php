<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVolunteerRequest extends FormRequest
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
            'volunt_firstname' => 'required',
            'volunt_lastname' => 'nullable',
            'volunt_mail' => 'required|email',
            'volunt_address' => 'nullable',
            'volunt_phone' => 'required',
            'volunt_graduatedfr' => 'nullable',
            'volunt_major' => 'nullable',
            'volunt_position' => 'nullable',
        ];
    }
}
