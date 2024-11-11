<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class ReportSalesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'start' => $this->get('start') ?? Carbon::now()->startOfMonth()->format('Y-m-d'),
            'end' => $this->get('end') ?? Carbon::now()->endOfMonth()->format('Y-m-d'),
            'main' => $this->get('main'),
            'program' => $this->get('program'),
            'pic' => $this->get('pic'),
        ]);
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start' => 'nullable|date',
            'end' => 'nullable|date|after_or_equal:start',
            'main' => 'nullable|exists:tbl_main_prog,id',
            'program' => 'nullable|exists:tbl_prog,prog_id',
            'pic' => 'nullable|exists:users,id'
        ];
    }
}
