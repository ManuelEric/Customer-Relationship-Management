<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
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
        return $this->isMethod('POST') ? $this->store() : $this->update();
    }

    protected function store()
    {
        return [
            'event_title' => 'required|unique:tbl_events,event_title',
            'event_description' => 'nullable',
            'event_location' => 'required|max:250',
            'event_startdate' => 'required|before_or_equal:event_enddate',
            'event_enddate' => 'required|after_or_equal:event_startdate',
            'user_id.*' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!User::whereHas('roles', function ($q) {
                        $q->where('role_name', 'Employee');
                    })->find($value)) {
                        $fail('The submitted pic was invalid employee');
                    }
                },
            ],
            'event_target' => 'required|min:1',
        ];
    }

    protected function update()
    {
        $eventId = $this->route('event');

        return [
            'event_title' => 'required|unique:tbl_events,event_title,' . $eventId . ',event_id',
            'event_description' => 'required',
            'event_location' => 'required|max:250',
            'event_startdate' => 'required|before_or_equal:event_enddate',
            'event_enddate' => 'required|after_or_equal:event_startdate',
            'user_id.*' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!User::whereHas('roles', function ($q) {
                        $q->where('role_name', 'Employee');
                    })->find($value)) {
                        $fail('The submitted pic was not employee is invalid');
                    }
                },
            ]
        ];
    }
}
