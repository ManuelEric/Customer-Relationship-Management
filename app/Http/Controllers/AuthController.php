<?php

namespace App\Http\Controllers;

use App\Enum\LogModule;
use App\Http\Requests\LoginRequest;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\MenuRepositoryInterface;
use App\Interfaces\UserTypeRepositoryInterface;
use App\Services\Authorization\AuthorizationService;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
        LoginRequest $request,
        AuthorizationService $authorizationService,
        LogService $log_service,
    ) {
        $credentials = $request->safe()->only(['email', 'password']);

        // check credentials
        if (! Auth::attempt($credentials, true)) {
            return back()->withErrors(['password' => 'Wrong email or password']);
        }

        try {

            $user = Auth::user()->load(['roles', 'departments']);
            $userId = $user->id;
            $authorizationService->checkPermissionFromUserType($userId);
            $scopes = $authorizationService->checkUserRole($user);
            [$generatedToken, $acceptableUserRole] = $authorizationService->authorize($user, $scopes);

            $request->session()->put('user_role', $acceptableUserRole);
            $request->session()->put('access_token', $generatedToken);
            $request->session()->put('scope', $scopes);

            // $clientIP = $request->ip();
            // Log::alert($clientIP);

        } catch (Exception $e) {

            $log_service->createErrorLog(LogModule::USER_LOGIN, $e->getMessage(), $e->getLine(), $e->getFile());

            return back()->withError("An issue occurs when attempting to log in. {$e->getMessage()}");

        }

        // login Success
        // create log success
        $log_service->createSuccessLog(LogModule::USER_LOGIN, "{$user->fullname} has logged in.");

        switch ($scopes) {
            case in_array('super-admin', $scopes):
            case in_array('sales-admin', $scopes):
                return redirect()->intended('/dashboard/sales/client-program');
                break;

            case in_array('employee', $scopes):
                if ($user->departments()->where('dept_name', 'Client Management')->exists()) {
                    return redirect()->intended('/dashboard/sales/client-program');
                } elseif ($user->departments()->where('dept_name', 'Business Development')->exists()) {
                    return redirect()->intended('/dashboard/partnership/agenda');
                } elseif ($user->departments()->where('dept_name', 'Digital')->exists()) {
                    return redirect()->intended('/dashboard/digital');
                } elseif ($user->departments()->where('dept_name', 'Finance & Operation')->exists()) {
                    return redirect()->intended('/dashboard/finance/outstanding-payment');
                }
                break;
        }
    }

    public function logout(
        Request $request,
        LogService $log_service,
    ) {
        $user = Auth::user();
        // logout Success
        // create log success
        $log_service->createSuccessLog(LogModule::USER_LOGOUT, "{$user->full_name} has logged out.");

        Auth::logout();
        Cache::flush();

        $request->session()->invalidate();
        $request->session()->forget('user_role');
        $request->session()->regenerateToken();

        // revoke token
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

        return Redirect::to('login')->withError('You had not activity in '.$timeout / 60 .' minutes ago.');
    }
}
