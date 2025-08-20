<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        $user = Auth::user();

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {

                if (! $request->session()->has('scope')) {
                    return redirect('/auth/logout');
                }

                $scopes = $request->session()->get('scope');

                switch ($scopes) {
                    case in_array('employee', $scopes):
                        if ($user->departments()->where('dept_name', 'Client Management')->exists()) {
                            return redirect()->intended('/dashboard/sales');
                        } elseif ($user->departments()->where('dept_name', 'Business Development')->exists()) {
                            return redirect()->intended('/dashboard/partnership');
                        } elseif ($user->departments()->where('dept_name', 'Digital')->exists()) {
                            return redirect()->intended('/dashboard/digital');
                        } elseif ($user->departments()->where('dept_name', 'Finance & Operation')->exists()) {
                            return redirect()->intended('/dashboard/finance');
                        } else {
                            return redirect()->intended('/dashboard/sales');
                        }
                        break;
                    case in_array('super-admin', $scopes):
                    case in_array('sales-admin', $scopes):
                        return redirect()->intended('/dashboard/sales');
                        break;
                }
            }

        }

        return $next($request);
    }
}
