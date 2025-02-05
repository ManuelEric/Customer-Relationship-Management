<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
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

                if ( !$request->session()->has('scope') )
                    return redirect('/login');
                

                $scopes = $request->session()->get('scope');

                switch ($scopes) {
                    case in_array('employee', $scopes):
                        if($user->department()->where('dept_name', 'Client Management')->exists()){
                            return redirect()->intended('/dashboard/sales');
                        }else if($user->department()->where('dept_name', 'Business Development')->exists()){
                            return redirect()->intended('/dashboard/partnership');
                        }else if($user->department()->where('dept_name', 'Digital')->exists()){
                            return redirect()->intended('/dashboard/digital');
                        }else if($user->department()->where('dept_name', 'Finance & Operation')->exists()){
                            return redirect()->intended('/dashboard/finance');
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
