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
            'major_group' => 'major group',
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
            'major_group.*' => 'required',
            'major.*' => 'required|exists:tbl_major,id',
            'status.*' => 'required|in:waitlisted,accepted,denied,chosen'
        ];
        
        /**
         * if method is post
         * then add validation for column alumni
         */
        if ($this->isMethod('POST')) 
            $rules['alumni'] = 'required|exists:tbl_client,id';


        /**
         * if method is patch
         * then add validation for column is_picked
         */
        if ($this->isMethod('PATCH'))
            $rules['is_picked'] = 'required';

        return $rules;
    }
}
