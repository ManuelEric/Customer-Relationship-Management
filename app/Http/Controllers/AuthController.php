<?php

namespace App\Http\Controllers;

use App\Http\Traits\LoggingTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\MenuRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\UserTypeRepositoryInterface;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class AuthController extends Controller
{
    use LoggingTrait;
    private MenuRepositoryInterface $menuRepository;
    private UserTypeRepositoryInterface $userTypeRepository;

    public function __construct(MenuRepositoryInterface $menuRepository, UserTypeRepositoryInterface $userTypeRepository)
    {
        $this->menuRepository = $menuRepository;
        $this->userTypeRepository = $userTypeRepository;
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|exists:users,email',
            'password' => 'required',
        ]);

        
        # check credentials
        if (Auth::attempt($credentials)) {
            
            $user = Auth::user();
            $user_type = $this->userTypeRepository->getActiveUserTypeByUserId($user->id);
                        
            if (!$user_type) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withError([
                    'password' => 'You don\'t have permission to login. If this problem persists, please contact our administrator.'
                ]);
            }

            if ($user_type->type_name != 'Full-Time' && ($user_type->pivot->end_date <= Carbon::now()->toDateString())) {
                return back()->withError([
                    'password' => 'Your access is expired',
                ]);
            }

            # login Success
            # create log success
            $this->logSuccess('auth', null, 'Login', $request->email);
            
            $request->session()->regenerate();



            # check roles
            # set the default scopes
            $scopes = ['employee'];
            $request->session()->put('user_role', 'Employee');
            if ($user->roles()->where('role_name', 'Super Admin')->exists()) {
                $scopes = ['super-admin'];
                $request->session()->put('user_role', 'SuperAdmin');


                if (!$token = $user->createToken('Grant User Access', $scopes)->accessToken) 
                    Log::error('Failed to generate token');

                Log::info($token);
                
                # store the access token
                $request->session()->put([
                    'access_token' => $token,
                    'scopes' => $scopes
                ]);

            } else {

                if ($user->roles()->where('role_name', 'Admin')->exists() && $user->department()->where('dept_name', 'Client Management')->exists()) {
                    $scopes = ['sales-admin'];
                    $request->session()->put('user_role', 'SalesAdmin');
                } 
                
            }
            

            # if scope is employee
            if (in_array('employee', $scopes)) {

                # create access token 
                # in order to access api with data session
                if (!$token = $user->createToken('Grant User Access', $scopes)->accessToken) 
                    Log::error('Failed to generate token');
                
                # store the access token
                $request->session()->put([
                    'access_token' => $token,
                    'scopes' => $scopes
                ]);
            }
            
            return redirect()->intended('/dashboard2');
            
        }

        return back()->withErrors([
            'password' => 'Wrong email or password',
        ]);
    }

    public function logout(Request $request)
    {
        # logout Success
        # create log success
        $this->logSuccess('auth', null, 'Logout', Auth::user()->email);

        Auth::logout();
        Cache::flush();
        
        $request->session()->invalidate();
        $request->session()->forget('user_role');
        $request->session()->regenerateToken();

        # revoke token
        if ($request->user()) {
            $token = $request->user()->token();
            $token->revoke();
        }

        return redirect('/');
    }

    public function logoutFromExpirationTime(Request $request)
    {
        $timeout = 3600;
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->forget('user_role');
        $request->session()->regenerateToken();
        return Redirect::to('login')->withError('You had not activity in '.$timeout/60 .' minutes ago.');
    }
}
