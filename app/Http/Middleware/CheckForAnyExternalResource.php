<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckForAnyExternalResource
{
    /**
     * Specify the resources for the middleware.
     *
     * @param  array|string  $resources
     * @return string
     */
    public static function using(...$resources)
    {
        if (is_array($resources[0])) {
            return static::class.':'.implode(',', $resources[0]);
        }

        return static::class.':'.implode(',', $resources);
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, $next, ...$resources)
    {
        foreach ($resources as $resource) {

            switch ($resource)
            {
                case "timesheet":
                    $key = "Header-ET";
                    break;

                case "editing":
                    $key = "Header-EE";
                    break;

                case "mentoring":
                    $key = "Header-M";
                    break;
            }

            if (! $key)
                throw new Exception('Invalid resource');


            if (\App\Models\TokenLib::where('header_name', $key)->where('value', $request->header($key))->where('expires_at', '>', Carbon::now())->exists())
                return $next($request);
        }

        throw new HttpResponseException(
            response()->json([
                'errors' => 'Unauthorized.'
            ], JsonResponse::HTTP_UNAUTHORIZED)
        );
    }
}
