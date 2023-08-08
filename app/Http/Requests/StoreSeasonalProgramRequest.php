<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSeasonalProgramRequest extends FormRequest
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
            'prog_id' => 'Program Name',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $seasonalProgramId = $this->input('id') ?? null;
        $progId = $this->input('prog_id');
        $start = $this->input('start');
        $end = $this->input('end');

        return [
            'prog_id' => [
                'exists:tbl_prog,prog_id',
                Rule::unique('tbl_seasonal_lead')->where( function ($query) use ($progId, $start, $end) {
                    $query->where('prog_id', $progId)->where('start', $start)->where('end', $end);
                })->when($seasonalProgramId, function ($query) use ($seasonalProgramId) {
                    $query->ignore($seasonalProgramId, 'id');
                })
            ],
            'start' => 'date',
            'end' => 'date|after_or_equal:start',
            'sales_date' => 'date',
        ];
    }
}
