<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Lead;
use App\Interfaces\EventRepositoryInterface;

class StoreClientEventEmbedRequest extends FormRequest
{
    private EventRepositoryInterface $eventRepository;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    // public function authorize()
    // {
    //     return true;
    // }

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
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
            'required_with' => 'The :attribute field is required',
        ];
    }


    public function rules()
    {
        return [
            'name' => 'required_if:user_type,Parent|nullable',
            'child_name' => 'required_with:name,email,phone|nullable',
            'email' => 'required_if:user_type,Parent|nullable|email',
            'phone' => 'required_if:user_type,Parent|nullable|min:10|max:15',
            'email_child' => 'required_with:name,email,phone|email',
            'phone_child' => 'required_with:name,email,phone|min:10|max:15',
            'school' => 'required',
            'other_school' => 'sometimes|required_if:school,add-new|unique:tbl_sch,sch_name',
            'grade' => 'required',
            'leadsource' => 'required|exists:tbl_lead,lead_id',

        ];
    }
}
