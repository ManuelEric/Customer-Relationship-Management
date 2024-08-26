<?php

namespace App\Services\Authorization;

use App\Interfaces\UserTypeRepositoryInterface;
use Exception;
use Google\Service\Compute\HttpRedirectAction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthorizationService
{
    protected UserTypeRepositoryInterface $userTypeRepository;

    public function __construct(UserTypeRepositoryInterface $userTypeRepository)
    {
        $this->userTypeRepository = $userTypeRepository;
    }

    public function checkPermissionFromUserType($userId)
    {
        $user_type = $this->userTypeRepository->getActiveUserTypeByUserId($userId);

        # when user doesn't have a permission to access CRM
        if (!$user_type) {
            Auth::logout();
            // $request->session()->invalidate();
            // $request->session()->regenerateToken();
            throw new Exception('You don\'t have permission. If this problem persists, please contact our administrator.');
        }

        # when user permissions are expired.
        if ($user_type->type_name != 'Full-Time' && ($user_type->pivot->end_date <= Carbon::now()->toDateString())) {
            Auth::logout();
            throw new Exception('Your access is expired');
        }

        return $user_type;
    }

    public function checkUserRole($user)
    {
        $userFullname = $user->first_name . ' ' . $user->last_name;
        # just in case both of the condition above to check user type was passed by
        # validate the user roles before looping
        if ( count($user->roles) == 0 )
        {
            Log::error("{$userFullname} was trying to log-in but failed because cannot continue the looping user roles process since he/she doesn't have any role.");
            throw new Exception('Something went wrong. Please contact our administrator to help you login.');
        }
        

        # the idea was to loop through the user roles and ordered by id asc
        # so the bigger id will overwrite the smaller one
        foreach ( $user->roles()->orderBy('id', 'asc')->get() as $userRole )
        {
            $roleName = str_replace(' ', '-', strtolower($userRole->role_name));
            if ( $roleName == 'employee' || $roleName == 'super-admin' || $roleName == 'admin' )
            {
                # the scopes variables should between "employee", "super admin", "admin"
                $scopes = [$roleName];
            }
        }

        return $scopes;
    }

    public function authorize($user, $scopes)
    {
        $userFullname = $user->first_name . ' ' . $user->last_name;
        # just in case both of the condition above to check user type was passed by
        # validate the scopes
        if (empty($scopes))
        {
            Log::error("{$userFullname} was trying to log-in but failed because he/she doesn't have an acceptable role.");
            throw new Exception('Something went wrong. Please contact our administrator to help you login.');
        }

        

        # create access token 
        # in order to access api with data session
        if (!$generatedToken = $user->createToken('Grant User Access', $scopes)->accessToken) 
            Log::error("{$userFullname} was trying to log-in but failed to generate token.");


        # by default user role will follow the scopes
        $acceptableUserRole = str_replace(' ', '', $scopes[0]);

        # because role Admin could have came from many departments
        # so we need to check whether he/she is a sales admin / or any other department
        # and because for now, there will be only 1 admin from client management (sales admin)
        if ( $user->department()->where('dept_name', 'Client Management')->exists() )
        {
            $acceptableUserRole = 'SalesAdmin';
        }

        return [
            $generatedToken, $acceptableUserRole
        ];
    }
}