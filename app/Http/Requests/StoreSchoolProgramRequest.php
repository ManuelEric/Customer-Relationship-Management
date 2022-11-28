<?php

namespace App\Http\Requests;

use App\Models\SchoolProgram;
use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolProgramRequest extends FormRequest
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
        $eventId = $this->route('event');

        return [
            'sch_id' => 'required|exists:tbl_sch,sch_id',
            'prog_id' => 'required|exists:tbl_prog,prog_id',
            'first_discuss' => 'required|date',
            'last_discuss' => 'required|date',
        ];
    }
}
