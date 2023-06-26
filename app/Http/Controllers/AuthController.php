<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\MenuRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private MenuRepositoryInterface $menuRepository;

    public function __construct(MenuRepositoryInterface $menuRepository)
    {
        $this->menuRepository = $menuRepository;
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
            
            if (!$user_type = $user->user_type->first()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'password' => 'You don\'t have permission to login. If this problem persists, please contact our administrator.'
                ]);
            }

            if ($user_type->type_name != 'Full-Time' && ($user_type->pivot->end_date <= Carbon::now()->toDateString())) {
                return back()->withErrors([
                    'password' => 'Your access is expired',
                ]);
            }

            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'password' => 'Wrong email or password',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
