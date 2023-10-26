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
        return [
            'alumni' => 'required|exists:tbl_client,id',
            'uni_id.*' => 'exists:tbl_univ,univ_id',
            'major.*' => 'exists:tbl_major,id',
            'status.*' => 'in:waitlisted,accepted,denied,chosen'
        ];
    }
}
