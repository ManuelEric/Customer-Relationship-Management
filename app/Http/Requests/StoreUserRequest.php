<?php

namespace App\Http\Requests;

use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class StoreUserRequest extends FormRequest
{
    use StandardizePhoneNumberTrait;

    private UserRepositoryInterface $userRepository;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
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

    protected function prepareForValidation()
    {
        $this->merge([
            'phone' => $this->tnSetPhoneNumber($this->phone),
            'emergency_contact_phone' => $this->emergency_contact_phone != null ? $this->tnSetPhoneNumber($this->emergency_contact_phone) : null,
            'emergency_contact_relation_name' => $this->emergency_contact_relation_name,
            'position_id' => $this->position,
            'password' => Hash::make('12345678'),
        ]);
    }

    protected function store()
    {
        $i = 0;
        $total_roles = count($this->input('role'));
        while (
            $i < $total_roles
        ) {
            $rules = [
                'emergency_contact_phone' =>  'required_if:role.' . $i . ',1,8',
                'emergency_contact_relation_name' => 'required_if:role.' . $i . ',1,8'
            ];
            $i++;
        }

        $rules += [
            'first_name' => 'required',
            'last_name' => 'nullable',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|unique:users,phone',
            'datebirth' => 'required',
            'address' => 'required',

            'graduated_from.*' => 'nullable',
            'degree.*' => 'nullable',
            'major.*' => 'nullable',
            'graduation_date.*' => 'nullable',

            'role.*' => 'required|in:1,2,3,4,18,19,20',

            'curriculum_vitae' => 'nullable|mimes:pdf|max:5000',
            'bank_name' => 'required',
            'account_name' => 'required',
            'account_no' => 'required',
            'nik' => 'required',
            'idcard' => 'required|mimes:pdf,jpeg,jpg,png|max:5000',
            'npwp' => 'nullable',
            'tax' => 'nullable|mimes:pdf,jpeg,jpg,png|max:5000',
            'health_insurance' => 'nullable|mimes:pdf,jpeg,jpg,png|max:5000',
            'empl_insurance' => 'nullable|mimes:pdf,jpeg,jpg,png|max:5000'

        ];

        if(!in_array(19, $this->input('role'))){
            $rules += [
                'department' => 'required', # 19 is professional
                'position' => 'required',
                'hiredate' => 'required',
                'type' => 'required|exists:tbl_user_type,id',
                'start_period' => 'required',
                'end_period' => 'required_unless:type,1', # 1 is type : Full-Time
            ];
        }

        // if($total_roles > 0){
        //     # Tutor || Editor || Individual Professional || External Mentor
        //     if(in_array(4, $this->input('role')) || in_array(3, $this->input('role')) || in_array(19, $this->input('role')) || in_array(20, $this->input('role'))){
        //         $rules += [
        //             'agreement.*' => 'required|mimes:pdf|max:5000',
        //             'subject_id.*' => 'required_if:role,4',
        //             'role_type.*' => 'required_if:role,3|required_if:role,19',
        //             'year.*' => 'required',
        //             'grade.*.*' => 'required_if:role,4',
        //             'fee_individual.*.*' => 'required',
        //             'fee_group.*.*' => 'nullable',
        //             'additional_fee.*.*' => 'nullable',
        //             'head.*.*' => 'nullable',
        //         ];
        //     }
        // }
        
        return $rules;
    }

    protected function update()
    {
        $i = 0;
        $total_roles = count($this->input('role'));
        while (
            $i < $total_roles
        ) {
            $rules = [
                'emergency_contact_phone' =>  'required_if:role.' . $i . ',1,8',
                'emergency_contact_relation_name' => 'required_if:role.' . $i . ',1,8'
            ];
            $i++;
        }

        $rules += [
            'first_name' => 'required',
            'last_name' => 'nullable',
            'email' => 'required|email',
            'phone' => 'required',
            'datebirth' => 'required',
            'address' => 'required',

            'graduated_from.*' => 'nullable',
            'degree.*' => 'nullable',
            'major.*' => 'nullable',
            'graduation_date.*' => 'nullable',

            'role.*' => 'required|in:1,2,3,4,18,19,20',
            
            'curriculum_vitae' => 'nullable|mimes:pdf|max:5000',
            'bank_name' => 'required',
            'account_name' => 'required',
            'account_no' => 'required',
            'nik' => 'required',
            'npwp' => 'nullable',
            'tax' => 'nullable|mimes:pdf,jpeg,jpg,png|max:5000',
            'health_insurance' => 'nullable|mimes:pdf,jpeg,jpg,png|max:5000',
            'empl_insurance' => 'nullable|mimes:pdf,jpeg,jpg,png|max:5000'

        ];

        if(!in_array(19, $this->input('role'))){
            $rules += [
                'department' => 'required', # 19 is professional
                'position' => 'required',
                'hiredate' => 'required',
                'type' => 'required|exists:tbl_user_type,id',
                'start_period' => 'required',
                'end_period' => 'required_unless:type,1', # 1 is type : Full-Time
            ];
        }

        $userId = $this->route('user');
        $user = $this->userRepository->rnGetUserById($userId);

        if ($user->idcard == null)
            $rules['idcard'] = 'required|mimes:pdf,jpeg,jpg,png|max:5000';

        // if ( $total_roles > 0 )
        // {
        //     if ( in_array(4, $this->input('role')) )
        //     {
        //         for ($i = 0; $i < count($this->subject_id) ; $i++)
        //         {
        //             if ( !$tutor_role_information = $user->roles->where('id', 4)->first() )
        //             {
        //                 $rules += ["agreement.*" => 'required|mimes:pdf|max:5000'];
        //                 break;
        //             }

        //             $user_role_id = $tutor_role_information->pivot->id;
        //             if ( $role_subject = $this->userRepository->rnGetUserSubjectById($user_role_id) )
        //             {
        //                 if ( $role_subject->agreement !== NULL )
        //                 {
        //                     $rules += [
        //                         "agreement.{$i}" => 'nullable',
        //                     ];
        //                 }
        //             }
        //         }

        //         $rules += [
        //             'subject_id.*' => 'required_if:role,4',
        //             'year.*' => 'required',
        //             'role_type.*' => 'required_if:role,3|required_if:role,19',
        //             'grade.*.*' => 'required_if:role,4',
        //             'fee_individual.*.*' => 'required',
        //             'fee_group.*.*' => 'nullable',
        //             'additional_fee.*.*' => 'nullable',
        //             'head.*.*' => 'nullable',
        //         ];
        //     }
        // }
        return $rules;
    }
}
