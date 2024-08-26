<?php

namespace App\Http\Requests;

use App\Models\Lead;
use App\Models\School;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function PHPSTORM_META\map;

class StoreClientRawStudentRequest extends FormRequest
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

        $rules = [
            'nameFinal' => 'required',
            'emailFinal' => 'required|email',
            'phoneFinal' => 'required|min:10|max:15',
            'graduationFinal' => 'nullable',
            'schoolFinal' => [
                'sometimes',
                'required',
                'exists:tbl_sch,sch_id',
                function ($attribute, $value, $fail){
                    $school = School::where('sch_id', $value)->first();
                    if($school->is_verified == 'N'){
                        $fail("You can choose only verified school");
                    }
                }
            ],
            'parentType' => 'required|in:exist,new,exist_select',
            'parentName' => 'nullable',
            'parentMail' => 'nullable',
            'parentPhone' => 'nullable',
            'parentFinal' => 'nullable',
        ];

        return $rules;
    }
}
