<?php

namespace App\Services;

use App\Models\FailedLogin;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LoginAttemptService
{
    const MAX_ATTEMPTS = 5;
    const LOCKOUT_MINUTES = 30;

    public function recordFailedAttempt(Request $request)
    {
        FailedLogin::create([
            'email' => $request->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'attempted_at' => now(),
        ]);
    }

    public function hasTooManyAttempts(string $email, string $ip)
    {
        $recentAttempts = FailedLogin::where('email', $email)
            ->where('ip_address', $ip)
            ->where('attempted_at', '>=', now()->subMinutes(self::LOCKOUT_MINUTES))
            ->count();

        return $recentAttempts >= self::MAX_ATTEMPTS;
    }

    public function getAttemptsLeft(string $email, string $ip)
    {
        $recentAttempts = FailedLogin::where('email', $email)
            ->where('ip_address', $ip)
            ->where('attempted_at', '>=', now()->subMinutes(self::LOCKOUT_MINUTES))
            ->count();

        return max(0, self::MAX_ATTEMPTS - $recentAttempts);
    }

    public function clearAttempts(string $email)
    {
        FailedLogin::where('email', $email)
            ->where('attempted_at', '<', now())
            ->delete();
    }

    public function getLockoutTime(string $email, string $ip)
    {
        $lastAttempt = FailedLogin::where('email', $email)
            ->where('ip_address', $ip)
            ->latest('attempted_at')
            ->first();

        if ($lastAttempt) {
            return $lastAttempt->attempted_at->addMinutes(self::LOCKOUT_MINUTES);
        }

        return null;
    }
}
