<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalesTargetRequest extends FormRequest
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

     
    public function attributes()
    {
        return [
            'prog_id' => 'Program Name',
        ];
    }

    public function rules()
    {
        $prog_id = $this->input('prog_id');

        return [
            'prog_id' => 'required|exists:tbl_prog,prog_id',
            'total_participant' => 'required|integer',
            'total_target' => 'required|integer',
            'month_year' => 'required',
        ];
    }
}
