<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMobileApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-Mobile-API-Key');
        
        if (!$apiKey || $apiKey !== config('app.mobile_api_key')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Invalid API key.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
