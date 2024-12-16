<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\LoginAttemptService;

class TrackFailedLogins
{
    protected $loginAttemptService;

    public function __construct(LoginAttemptService $loginAttemptService)
    {
        $this->loginAttemptService = $loginAttemptService;
    }

    public function handle(Request $request, Closure $next)
    {
        if ($request->is('login') && $request->isMethod('post')) {
            if ($this->loginAttemptService->hasTooManyAttempts($request->email, $request->ip())) {
                $lockoutTime = $this->loginAttemptService->getLockoutTime($request->email, $request->ip());
                
                return response()->json([
                    'error' => 'too_many_attempts',
                    'message' => 'لقد تجاوزت الحد الأقصى من محاولات تسجيل الدخول. يرجى المحاولة مرة أخرى بعد ' . 
                                $lockoutTime->diffForHumans(),
                    'retry_after' => $lockoutTime->timestamp
                ], 429);
            }
        }

        $response = $next($request);

        if ($request->is('login') && $request->isMethod('post') && $response->getStatusCode() === 422) {
            $this->loginAttemptService->recordFailedAttempt($request);
            
            $attemptsLeft = $this->loginAttemptService->getAttemptsLeft($request->email, $request->ip());
            $response->setData(array_merge($response->getData(true), [
                'attempts_left' => $attemptsLeft,
                'message' => 'فشل تسجيل الدخول. المحاولات المتبقية: ' . $attemptsLeft
            ]));
        }

        return $response;
    }
}
