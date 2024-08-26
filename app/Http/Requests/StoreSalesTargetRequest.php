<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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

     public function messages()
     {
         return [
             'unique' => 'The :attribute has already been taken at same time.',
         ];
     }

     
    public function attributes()
    {
        return [
            'prog_id' => 'Program Name',
            'main_prog_id' => 'Main Program',
        ];
    }

    public function rules()
    {
        $prog_id = $this->input('prog_id');
        $month_year = $this->input('month_year') . '-01';

        return [
            'main_prog_id' => 'required|exists:tbl_main_prog,id',
            'prog_id' => 'nullable|exists:tbl_prog,prog_id',
            // 'prog_id' => ['required','exists:tbl_prog,prog_id', Rule::unique('tbl_sales_target')->where(function ($query) use ($month_year){
            //     return $query->where('month_year', $month_year);
            // })],
            'total_participant' => 'required|integer',
            'total_target' => 'required|integer',
            'month_year' => 'required',
        ];
    }
}
