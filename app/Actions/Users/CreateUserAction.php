<?php

namespace App\Actions\Users;

use App\Http\Traits\UploadFileTrait;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CreateUserAction
{
    use UploadFileTrait;
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(
        Request $request,
        Array $new_user_details,
        Array $new_user_education_details,
        Array $new_user_role_details,
        Array $new_user_type_details
    )
    {
        # 1. store new user
        $new_user_details += ['number' => \App\Models\User::max('number') + 1];
        $new_user = $this->userRepository->rnCreateUser($new_user_details);
        $new_user_id = $new_user->id;


        # 2. store new user education to tbl_user_education
        $this->userRepository->rnCreateUserEducation($new_user, $new_user_education_details);
        
        
        # 3. store new user role to tbl_user_roles
        $this->userRepository->rnCreateUserRole($new_user, $new_user_role_details);
        
        
        
        # 4. store new user contract to tbl_user_type
        if(!in_array(19, $new_user_role_details['role'])){ # role professional
            $this->userRepository->rnCreateUserType($new_user, $new_user_type_details);
        }


        # 5. store/update new tutor subject
        // $this->userRepository->rnCreateOrUpdateUserSubject($new_user, $request);
        

        # 6. upload curriculum vitae
        $CV_file_path = $this->tnUploadFile($request, 'curriculum_vitae', 'CV-' . str_replace(' ', '_', $request->first_name . '_' . $request->last_name), 'project/crm/user/' . $new_user_id);


        # 7. upload KTP / idcard
        $ID_file_path = $this->tnUploadFile($request, 'idcard', 'ID-' . str_replace(' ', '_', $request->first_name . '_' . $request->last_name), 'project/crm/user/' . $new_user_id);


        # 8. upload tax
        $TX_file_path = $this->tnUploadFile($request, 'tax', 'TAX-' . str_replace(' ', '_', $request->first_name . '_' . $request->last_name), 'project/crm/user/' . $new_user_id);


        # 9. upload bpjs kesehatan / health insurance
        $HI_file_path = $this->tnUploadFile($request, 'health_insurance', 'HI-' . str_replace(' ', '_', $request->first_name . '_' . $request->last_name), 'project/crm/user/' . $new_user_id);


        # 10. upload bpjs ketenagakerjaan / empl insurance
        $EI_file_path = $this->tnUploadFile($request, 'empl_insurance', 'EI-' . str_replace(' ', '_', $request->first_name . '_' . $request->last_name), 'project/crm/user/' . $new_user_id);


        # update uploaded data to user table
        if ($request->hasFile('curriculum_vitae') || $request->hasFile('idcard') || $request->hasFile('tax') || $request->hasFile('health_insurance') || $request->hasFile('empl_insurance')) {
            $this->userRepository->rnUpdateUser($new_user_id, [
                'idcard' => $ID_file_path,
                'cv' => $CV_file_path,
                'tax' => $TX_file_path,
                'health_insurance' => $HI_file_path,
                'empl_insurance' => $EI_file_path
            ]);
        }

        return $new_user;
    }
}