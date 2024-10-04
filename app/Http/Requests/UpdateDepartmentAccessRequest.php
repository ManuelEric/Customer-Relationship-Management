<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentAccessRequest extends FormRequest
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
            'department_id' => 'required|exists:tbl_department,id',
            'menu_id' => 'required|exists:tbl_menus,id',
            'menu_data' => 'required|in:true,false',
            'copy_data' => 'required|in:true,false',
            'export' => 'required|in:true,false',
            'param' => 'required|in:menu,copy,export'
        ];
    }

   
}
