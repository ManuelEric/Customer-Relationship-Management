<?php

namespace App\Http\Requests;

use App\Models\Lead;
use App\Models\School;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

use function PHPSTORM_META\map;

class StoreClientRawTeacherRequest extends FormRequest
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
            'schoolFinal' => [
                'sometimes',
                'required',
                'exists:tbl_sch,sch_id',
                function ($attribute, $value, $fail){
                    $school = School::where('sch_id', $value)->first();
                    Log::debug(json_encode($school));
                    if($school->is_verified == 'N'){
                        $fail("You can choose only verified school");
                    }
                }
            ],
        ];

        return $rules;
    }
}
