<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class DashboardRequest extends FormRequest
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
        /**
         * Incoming Requests for Sales Dashboard
         */
        $qdate = $this->get('cp-month') ?? ($this->get('qdate') ?? Carbon::now()->format('Y-m'));
        $this->merge([
            'qdate' => $qdate,
            'start' => Carbon::parse($qdate)->firstOfMonth()->format('Y-m-d'),
            'end' => Carbon::parse($qdate)->endOfMonth()->format('Y-m-d'),
            'quuid' => $this->get('quser'),
            'program_id' => NULL,
            'qparam_year1' => (int) Carbon::now()->subYears(1)->format('Y'),
            'qparam_year2' => (int) Carbon::now()->format('Y'),
            'qyear' => 'current'
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
            'qdate' => 'nullable|date_format:Y-m',
            'start' => 'nullable|date_format:Y-m-d',
            'end' => 'nullable|date_format:Y-m-d',
            'quuid' => 'nullable',
            'program_id' => 'nullable',
            'qparam_year1' => 'min_digits:4,max_digits:4',
            'qparam_year2' => 'min_digits:4,max_digits:4',
            'qyear' => 'nullable|in:last-3-year,current'
        ];
    }
}
