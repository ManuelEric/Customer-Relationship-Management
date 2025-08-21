<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogRouteChange
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
        $uri = trim($request->getRequestUri(), '/');

        // Skip logging for log-viewer routes
        if (str_starts_with($uri, 'log-viewer')) {
            return $next($request);
        }

        try {
            Log::notice('Route accessed');
        } catch (Exception $e) {
            Log::error('Error accessing route', [
                'exception' => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
            ]);
        }

        return $next($request);
    }
}
