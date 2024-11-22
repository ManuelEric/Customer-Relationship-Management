<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAcceptanceRequest extends FormRequest
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

    public function attributes()
    {
        return [
            'alumni' => 'alumni',
            'uni_id' => 'university',
            'major' => 'major',
            'status' => 'status'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'uni_id.*' => 'required|exists:tbl_univ,univ_id',
            'major.*' => 'required|exists:tbl_major,id',
            'status.*' => 'required|in:waitlisted,accepted,denied,chosen'
        ];
        
        if($this->isMethod('POST')) 
            $rules['alumni'] = 'required|exists:tbl_client,id';

        return $rules;
    }
}
