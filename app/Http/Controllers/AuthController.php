<?php

namespace App\Http\Controllers;

use App\Http\Traits\LoggingTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\MenuRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\UserTypeRepositoryInterface;
use App\Models\User;
use App\Services\Authorization\AuthorizationService;
use Carbon\Carbon;
use Exception;
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

    public function login(
        Request $request,
        AuthorizationService $authorizationService,
        )
    {
        $credentials = $request->validate([
            'email' => 'required|exists:users,email',
            'password' => 'required',
        ]);

        # check credentials
        if (!Auth::attempt($credentials))
            return back()->withErrors([ 'password' => 'Wrong email or password' ]);
        
        // try {

            $user = Auth::user();
            $userId = $user->id;
            $authorizationService->checkPermissionFromUserType($userId);
            $scopes = $authorizationService->checkUserRole($user);
            [$generatedToken, $acceptableUserRole] = $authorizationService->authorize($user, $scopes);
    
            $request->session()->put('user_role', $acceptableUserRole);
            $request->session()->put('access_token', $generatedToken);
            $request->session()->put('scope', $scopes);
            
        // } catch (Exception $e) {

        //     Log::debug('Error:'. $e->getMessage());
        //     return back()->withError($e->getMessage());

        // }


        # login Success
        # create log success
        $this->logSuccess('auth', null, 'Login', $request->email);
        // $request->session()->regenerate();

        
        return redirect()->intended('/dashboard2');
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
