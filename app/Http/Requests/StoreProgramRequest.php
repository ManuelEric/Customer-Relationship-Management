<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgramRequest extends FormRequest
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
        $prog_id = $this->input('prog_id');

        return [
            'prog_id' => 'required|unique:tbl_prog,prog_id,' . $prog_id . ',prog_id',
            'prog_type' => 'required|in:B2B,B2C,B2B/B2C',
            'prog_main' => 'required|exists:tbl_main_prog,id',
            'prog_sub' => 'nullable|exists:tbl_sub_prog,id',
            'prog_name' => 'required',
            'prog_mentor' => 'required|in:Mentor,Tutor,No',
            'prog_payment' => 'required|in:idr,usd,session',
            'prog_scope' => 'required|in:mentee,public,school,partner',
            'active' => 'required|in:1,0',
        ];
    }
}
