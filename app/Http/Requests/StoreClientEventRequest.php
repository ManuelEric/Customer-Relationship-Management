<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Lead;

class StoreClientEventRequest extends FormRequest
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

    //  public function messages()
    //  {
    //      return [
    //          'unique' => 'The :attribute has already been taken at same time.',
    //      ];
    //  }

     
    public function attributes()
    {
        return [
            'client_id' => 'Client Name',
            'event_id' => 'Event Name',
            'lead_id' => 'Conversion Lead',
        ];
    }

    public function rules()
    {
        return $this->input('existing_client') == '1' || $this->isMethod('PUT') ? $this->isExisting() : $this->notExisting();
    }

    protected function isExisting(){
        $rules = [
            'client_id' => 'required|exists:tbl_client,id',
            'event_id' => [
                'required_if:lead_id,LS004',
                Rule::unique('tbl_client_event')->where(function ($query) {
                    $query->where('client_id', $this->input('client_id'))->where('event_id', $this->input('event_id'));
                })->when($this->isMethod('PUT'), function($query) { # when the method is PUT, ignore the client id
                    $query->ignore($this->input('client_id'), 'client_id');
                })
            ],
            'eduf_id' => 'required_if:lead_id,LS018',
            'kol_lead_id' => [
                function ($attribute, $value, $fail) {
                    if ($this->input('lead_id') == 'kol' && empty($value))
                        $fail('The KOL name field is required');

                    if (!Lead::where('main_lead', 'KOL')->where('lead_id', $value)->get()) 
                        $fail('The KOL name is invalid');
                }
            ],
            'joined_date' => 'required|date',
            'status' => 'required|in:0,1',
        ];

        if ($this->input('lead_id') != "kol") {
            $rules['lead_id'] = 'required|exists:tbl_lead,lead_id';
        }

        return $rules;
    }

    protected function notExisting(){
        $rules = [
            'first_name' => 'required',
            'last_name' => 'nullable',
            'mail' => 'required|email|unique:tbl_client,mail',
            'phone' => 'required|min:10|max:12',
            'dob' => 'required',
            'state' => 'required',
            'status_client' => 'required|in:Student,Parent,Teacher/Counsellor',
            'sch_id' => [
                'required_if:status_client,Student,Teacher/Conselor',
                function ($attribute, $value, $fail) {
                    if ($this->input('sch_id') != "add-new") {
                        Rule::exists('tbl_sch', 'sch_id');
                    }
                }
            ],
            'st_grade' => 'required_if:status_client,Mentee,Teacher/Conselor',
            'st_graduation_year' => 'nullable',
            'sch_name' => 'sometimes|required_if:sch_id,add-new|unique:tbl_sch,sch_name',
            'sch_type' => 'required_if:sch_id,add-new',
            'sch_curriculum' => 'required_if:sch_id,add-new',
            'sch_score' => 'required_if:sch_id,add-new',
            'event_id' => [
                'required_if:lead_id,LS004'
            ],
            'eduf_id' => 'required_if:lead_id,LS018',
            'kol_lead_id' => [
                function ($attribute, $value, $fail) {
                    if ($this->input('lead_id') == 'kol' && empty($value))
                        $fail('The KOL name field is required');

                    if (!Lead::where('main_lead', 'KOL')->where('lead_id', $value)->get()) 
                        $fail('The KOL name is invalid');
                }
            ],
            'joined_date' => 'required|date',
            'status' => 'required|in:0,1',
        ];

        if ($this->input('lead_id') != "kol") {
            $rules['lead_id'] = 'required|exists:tbl_lead,lead_id';
        }

        return $rules;
    }
}
