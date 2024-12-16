<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimitMiddleware
{
    public function handle(Request $request, Closure $next, string $limiterName = 'api'): Response
    {
        // تطبيق Rate Limiting
        $executed = RateLimiter::attempt(
            $limiterName,
            $this->maxAttempts($limiterName),
            function() use ($next, $request) {
                return $next($request);
            },
            $this->decayMinutes($limiterName)
        );

        if (! $executed) {
            return response()->json([
                'message' => 'تم تجاوز الحد المسموح به من الطلبات',
                'retry_after' => RateLimiter::availableIn($limiterName),
            ], 429);
        }

        $response = $next($request);

        // إضافة headers للـ Rate Limiting
        return $this->addRateLimitHeaders(
            $response,
            $limiterName,
            $request
        );
    }

    protected function maxAttempts(string $limiterName): int
    {
        return match($limiterName) {
            'login' => 5,
            'authenticated_api' => 120,
            'public_api' => 30,
            default => 60,
        };
    }

    protected function decayMinutes(string $limiterName): int
    {
        return match($limiterName) {
            'login' => 5,
            'authenticated_api' => 1,
            'public_api' => 1,
            default => 1,
        };
    }

    protected function addRateLimitHeaders(Response $response, string $key, Request $request): Response
    {
        $response->headers->add([
            'X-RateLimit-Limit' => $this->maxAttempts($key),
            'X-RateLimit-Remaining' => RateLimiter::remaining($key, $this->maxAttempts($key)),
            'X-RateLimit-Reset' => RateLimiter::availableIn($key),
        ]);

        return $response;
    }
}
