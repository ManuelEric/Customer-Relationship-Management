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
        return $this->isMethod('POST') ? $this->store() : $this->update();
    }

    protected function store()
    {
        return [
            'volunt_firstname' => 'required',
            'volunt_lastname' => 'nullable',
            'volunt_mail' => 'required|email',
            'volunt_address' => 'nullable',
            'volunt_phone' => 'required|min:10|max:15',
            'volunt_cv' => 'required|mimes:jpeg,bmp,png,gif,svg,pdf',
            'volunt_bank_accname' => 'required',
            'volunt_bank_accnumber' => 'required',
            'volunt_nik' => 'required|integer',
            'volunt_idcard' => 'required|mimes:jpeg,bmp,png,gif,svg,pdf',
            'volunt_npwp_number' => 'nullable',
            'volunt_npwp' => 'nullable|mimes:jpeg,bmp,png,gif,svg,pdf',
            'health_insurance' => 'nullable|mimes:jpeg,bmp,png,gif,svg,pdf',
            'empl_insurance' => 'nullable|mimes:jpeg,bmp,png,gif,svg,pdf',
            'volunt_graduatedfr' => 'nullable|exists:tbl_univ,univ_id',
            'volunt_major' => 'nullable|exists:tbl_major,id',
            'volunt_position' => 'nullable|exists:tbl_position,id'
        ];
    }

    protected function update()
    {
        return [
            'volunt_firstname' => 'required',
            'volunt_lastname' => 'nullable',
            'volunt_mail' => 'required|email',
            'volunt_address' => 'nullable',
            'volunt_phone' => 'required|min:10|max:15',
            'volunt_graduatedfr' => 'nullable',
            'volunt_major' => 'nullable',
            'volunt_position' => 'nullable',
            'volunt_cv' => 'nullable|mimes:jpeg,bmp,png,gif,svg,pdf',
            'volunt_bank_accname' => 'required',
            'volunt_bank_accnumber' => 'required',
            'volunt_nik' => 'required|integer',
            'volunt_idcard' => 'nullable|mimes:jpeg,bmp,png,gif,svg,pdf',
            'volunt_npwp_number' => 'nullable',
            'volunt_npwp' => 'nullable|mimes:jpeg,bmp,png,gif,svg,pdf',
            'health_insurance' => 'nullable|mimes:jpeg,bmp,png,gif,svg,pdf',
            'empl_insurance' => 'nullable|mimes:jpeg,bmp,png,gif,svg,pdf',
            'volunt_graduatedfr' => 'nullable|exists:tbl_univ,univ_id',
            'volunt_major' => 'nullable|exists:tbl_major,id',
            'volunt_position' => 'nullable|exists:tbl_position,id'
        ];
    }
}
