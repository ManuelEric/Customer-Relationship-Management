<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePurchaseReqRequest extends FormRequest
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

    public function messages()
    {
        return [
            'purchase_department' => 'The selected department is invalid',
        ];
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
            'purchase_department' => 'required|exists:tbl_department,id',
            'purchase_statusrequest' => 'required|in:Urgent,Immediately,Can Wait,Done',
            'purchase_requestdate' => 'required|date',
            'purchase_notes' => 'nullable',
            'purchase_attachment' => 'required|file|mimes:docx,doc,pdf,xls,xlsx,csv',
            'requested_by' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!User::whereHas('roles', function ($q) {
                        $q->where('role_name', 'Employee');
                    })->whereId($value)) {
                        $fail('The requested by is invalid');
                    }
                },
                // Rule::exists('users', 'id')->where(function($query){
                //     $query->whereHas('roles', function($q) {
                //         $q->where('role_name', 'Employee');
                //     });
                // }),
            ],
        ];
    }

    protected function update()
    {
        return [
            'purchase_department' => 'required|exists:tbl_department,id',
            'purchase_statusrequest' => 'required|in:Urgent,Immediately,Can Wait,Done',
            'purchase_requestdate' => 'required|date',
            'purchase_notes' => 'nullable',
            'purchase_attachment' => 'nullable|file|mimes:docx,doc,pdf,xls,xlsx,csv',
            'requested_by' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!User::whereHas('roles', function ($q) {
                        $q->where('role_name', 'Employee');
                    })->whereId($value)) {
                        $fail('The requested by is invalid');
                    }
                },
                // Rule::exists('users', 'id')->where(function($query){
                //     $query->whereHas('roles', function($q) {
                //         $q->where('role_name', 'Employee');
                //     });
                // }),
            ],
        ];
    }
}
