<?php

namespace App\Http\Requests;

use App\Interfaces\UserRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
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

    protected function store()
    {
        $i = 0;
        $total_roles = count($this->input('role'));
        while (
            $i < $total_roles
        ) {
            $rules = [
                'emergency_contact' =>  'required_if:role.' . $i . ',1,8'
            ];
            $i++;
        }

        $rules += [
            'first_name' => 'required',
            'last_name' => 'nullable',
            'email' => 'required|email',
            'phone' => 'required|unique:users,phone',

            // 'emergency_contact' => 'required_if:role.*,1,8',
            'datebirth' => 'required',
            'address' => 'required',

            'graduated_from.*' => 'nullable',
            'degree.*' => 'nullable',
            'major.*' => 'nullable',

            'role.*' => 'required|in:1,2,3,4,8',
            'department' => 'required',
            'position' => 'required',
            'hiredate' => 'required',
            'type' => 'required|exists:tbl_user_type,id',
            'start_period' => 'required',
            'end_period' => 'required_unless:type,1', # 1 is type : Full-Time

            'curriculum_vitae' => 'nullable|mimes:pdf|max:5000',
            'bankname' => 'required',
            'bankacc' => 'required',
            'nik' => 'required',
            'idcard' => 'required|mimes:pdf,jpeg,jpg,png|max:5000',
            'npwp' => 'nullable',
            'tax' => 'nullable|mimes:pdf,jpeg,jpg,png|max:5000',
            'health_insurance' => 'nullable|mimes:pdf,jpeg,jpg,png|max:5000',
            'empl_insurance' => 'nullable|mimes:pdf,jpeg,jpg,png|max:5000'

        ];

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
                'emergency_contact' =>  'required_if:role.' . $i . ',1,8'
            ];
            $i++;
        }

        $rules += [
            'first_name' => 'required',
            'last_name' => 'nullable',
            'email' => 'required|email',
            'phone' => 'required',
            // 'emergency_contact' => 'required_if:role,1,8|nullable',
            'datebirth' => 'required',
            'address' => 'required',

            'graduated_from.*' => 'nullable',
            'degree.*' => 'nullable',
            'major.*' => 'nullable',

            'role.*' => 'required|in:1,2,3,4,8',
            'department' => 'required',
            'position' => 'required',
            'hiredate' => 'required',
            'type' => 'required|exists:tbl_user_type,id',
            'start_period' => 'required',
            'end_period' => 'required_unless:type,1', # 1 is type : Full-Time

            'curriculum_vitae' => 'nullable|mimes:pdf|max:5000',
            'bankname' => 'required',
            'bankacc' => 'required',
            'nik' => 'required',
            'npwp' => 'nullable',
            'tax' => 'nullable|mimes:pdf,jpeg,jpg,png|max:5000',
            'health_insurance' => 'nullable|mimes:pdf,jpeg,jpg,png|max:5000',
            'empl_insurance' => 'nullable|mimes:pdf,jpeg,jpg,png|max:5000'

        ];

        $userId = $this->route('user');
        $user = $this->userRepository->getUserById($userId);

        if ($user->idcard == null)
            $rules['idcard'] = 'required|mimes:pdf,jpeg,jpg,png|max:5000';

        // if ($user->tax == null)
        //     $rules['tax'] = 'required|mimes:pdf|max:5000';

        // if ($user->health_insurance == null)
        //     $rules['health_insurance'] = 'required|mimes:pdf|max:5000';

        // if ($user->empl_insurance == null)
        //     $rules['empl_insurance'] = 'required|mimes:pdf|max:5000';

        return $rules;
    }
}
