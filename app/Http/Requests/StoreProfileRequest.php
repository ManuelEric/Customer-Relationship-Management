<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfileRequest extends FormRequest
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
        # validation for password only
        if ( (bool) $this->input('form:password') == true)
            return $this->updatePasswordOnly();
        
        # validation for general information on users table
        return [
            'first_name' => 'required',
            'last_name' => 'nullable',
            'address' => 'required',
            'email' => 'email|required',
            'phone' => 'required'
        ];
    }

    private function updatePasswordOnly()
    {
        return [
            'password' => 'required|min:6|confirmed'
        ];
    }
}
