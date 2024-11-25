<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserAccessRequest extends FormRequest
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
            'menu_data' => 'required|boolean',
            'copy_data' => 'required|boolean',
            'export_data' => 'required|boolean',
            'param' => 'required|in:menu,copy,export',
            'user' => 'required|exists:users,id'
        ];
    }

   
}
