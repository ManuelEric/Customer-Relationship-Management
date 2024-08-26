<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\AgendaSpeaker;
use App\Models\User;


class StoreEdufLeadSpeakerRequest extends FormRequest
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
            'required_if' => 'The :attribute field is required',
        ];
    }


    public function rules()
    {
        return $this->isMethod('POST') ? $this->store() : $this->update();
    }

    protected function store()
    {
        $schProgId = $this->route('sch_prog');
        $startTime = $this->input('start_time');
        $endTime = $this->input('end_time');

        $rules = [
            'speaker' => 'required',
            'start_time' => [
                'required',
                // function ($attribute, $value, $fail) use ($schProgId, $endTime) {
                //     if (!Event::where('event_startdate', 'like', $value.'%')->whereEventId($schProgId)) {

                //         $fail('The start time is invalid');

                //     } elseif (AgendaSpeaker::where('event_id', $schProgId)->whereBetween('start_time', [date('Y-m-d H:i:s', strtotime($value)), date('Y-m-d H:i:s', strtotime($endTime))])->count() > 0) {

                //         $fail('Another speaker has booked at '.date('H:i', strtotime($value)).' on '.date('d M Y', strtotime($value)).'. Please select an empty schedule');

                //     }
                // }
            ],
            'end_time' => [
                'required',
                // function ($attribute, $value, $fail) use ($schProgId, $startTime) {
                //     if (!Event::where('event_enddate', 'like', $value.'%')->whereEventId($schProgId)) {

                //         $fail('The start time is invalid');

                //     } elseif (AgendaSpeaker::where('event_id', $schProgId)->whereBetween('end_time', [date('Y-m-d H:i:s', strtotime($startTime)), date('Y-m-d H:i:s', strtotime($value))])->count() > 0) {

                //         $fail('Another speaker has booked at '.date('H:i', strtotime($value)).' on '.date('d M Y', strtotime($value)).'. Please select an empty schedule');

                //     }
                // }
            ],
        ];

        $rules['speaker'] = function ($attribute, $value, $fail) use ($schProgId) {
            if (!User::whereHas('roles', function ($query) {
                $query->where('role_name', 'employee');
            })->find($value)) {

                $fail('The ALL-In speaker is invalid' . $value);
            } elseif (AgendaSpeaker::where('empl_id', $value)->where('eduf_id', $schProgId)->where('start_time', '=', $this->input('start_time'))->where('end_time', '=', $this->input('end_time'))->first()) {

                $fail('The ALL-In speaker has already added before, cannot add same speaker at the same schedule');
            }
        };




        return $rules;
    }

    protected function update()
    {
        return [
            'status_speaker' => 'required|in:1,2',
            'agendaId' => 'required|exists:tbl_agenda_speaker,id',
            'notes_reason' => 'required_if:status_speaker,2|nullable',
        ];
    }
}
