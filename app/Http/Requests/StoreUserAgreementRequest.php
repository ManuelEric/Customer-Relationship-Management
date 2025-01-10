<?php

namespace App\Http\Requests;

use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class StoreUserAgreementRequest extends FormRequest
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
        $userId = $this->route('user');
        $user = $this->userRepository->rnGetUserById($userId);

        $rules = [];

        if ( !$user->roles->where('id', $this->input('role'))->first() )
        {
            $rules += ["agreement" => 'required|mimes:pdf|max:5000'];
        }

        $user_role_id = $this->role_agreement;
        if ( $user_subject = $this->userRepository->rnGetUserSubjectById($user_role_id) )
        {
            if ( $user_subject->agreement !== NULL )
            {
                $rules += [
                    "agreement" => 'nullable',
                ];
            }
        }

        $rules = [
            'role_agreement' => 'required',
            'subject_id' => 'required',
            'year' => 'required',
            'grade.*' => 'required_if:role,4|nullable',
            'fee_individual.*' => 'required',
            'fee_group.*' => 'nullable',
            'additional_fee.*' => 'nullable',
            'head.*' => 'nullable',
        ];

        return $rules;
    }

}
