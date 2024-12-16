<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MobileAppAuthentication
{
    /**
     * التحقق من أن الطلب قادم من التطبيق المحمول
     */
    public function handle(Request $request, Closure $next): Response
    {
        // التحقق من أن الطلب من التطبيق المحمول
        if (!$this->isValidMobileRequest($request)) {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مصرح بالوصول. هذه الخدمة متاحة فقط عبر تطبيق الهاتف المحمول.'
            ], 403);
        }

        return $next($request);
    }

    /**
     * التحقق من صحة الطلب
     */
    private function isValidMobileRequest(Request $request): bool
    {
        // التحقق من User-Agent
        $userAgent = $request->header('User-Agent');
        if (!$userAgent || !$this->isValidUserAgent($userAgent)) {
            return false;
        }

        // التحقق من API Key
        $apiKey = $request->header('X-Api-Key');
        if (!$apiKey || $apiKey !== config('app.mobile_api_key')) {
            return false;
        }

        // التحقق من أن الطلب من التطبيق
        $mobileApp = $request->header('X-Mobile-App');
        if (!$mobileApp || $mobileApp !== 'true') {
            return false;
        }

        return true;
    }

    /**
     * التحقق من صحة User Agent
     */
    private function isValidUserAgent(string $userAgent): bool
    {
        $validAgents = [
            'YourAppName-Android',
            'YourAppName-iOS'
        ];

        foreach ($validAgents as $agent) {
            if (str_contains($userAgent, $agent)) {
                return true;
            }
        }

        return false;
    }
}
