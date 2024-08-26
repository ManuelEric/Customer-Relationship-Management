<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TimesheetAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ( $request->header('Header-ET') )
        {
            if ( \App\Models\TokenLib::where('header_name', 'Header-ET')->where('value', $request->header('Header-ET'))->where('expires_at', '>', Carbon::now())->exists() )
                return $next($request);
        }

        throw new HttpResponseException(
            response()->json([
                'errors' => 'Unauthorized.'
            ], JsonResponse::HTTP_UNAUTHORIZED)
        );
    }
}
