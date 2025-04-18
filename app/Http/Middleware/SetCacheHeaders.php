<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetCacheHeaders
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
        $response = $next($request);

        // Set Cache-Control headers
        $response->header('Cache-Control', 'no-store,no-cache, must-revalidate, post-check=0, pre-check=0, max-age=30'); // 1 week (604800 seconds)
        
        # old
        // $response->header('Cache-Control', 'public, max-age=604800'); // 1 week (604800 seconds)
        
        return $response;
    }
}
