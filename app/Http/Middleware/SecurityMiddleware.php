<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SecurityMiddleware
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
        // 1. التحقق من صحة الجلسة
        if (Auth::check() && !$this->isValidSession($request)) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'جلستك انتهت. الرجاء تسجيل الدخول مرة أخرى.');
        }

        // 2. فحص محاولات التسلل
        if ($this->detectSuspiciousActivity($request)) {
            Log::warning('Suspicious activity detected', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'path' => $request->path(),
                'params' => $request->all()
            ]);
            return response()->json(['error' => 'تم رصد نشاط مشبوه'], 403);
        }

        // 3. إضافة رؤوس أمان إضافية
        $response = $next($request);
        
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $response->headers->set('Cross-Origin-Embedder-Policy', 'require-corp');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');
        
        // 4. منع تخزين الصفحات الحساسة
        if ($this->isSensitivePage($request)) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
        }

        return $response;
    }

    /**
     * التحقق من صحة الجلسة
     */
    private function isValidSession(Request $request)
    {
        $session = $request->session();
        
        // التحقق من IP المستخدم
        if ($session->has('user_ip') && $session->get('user_ip') !== $request->ip()) {
            return false;
        }
        
        // التحقق من User Agent
        if ($session->has('user_agent') && $session->get('user_agent') !== $request->userAgent()) {
            return false;
        }
        
        // تحديث معلومات الجلسة
        $session->put('user_ip', $request->ip());
        $session->put('user_agent', $request->userAgent());
        $session->put('last_activity', time());
        
        return true;
    }

    /**
     * فحص محاولات التسلل
     */
    private function detectSuspiciousActivity(Request $request)
    {
        // فحص محاولات SQL Injection
        $suspicious_patterns = [
            '/union\s+select/i',
            '/exec\s*\(/i',
            '/drop\s+table/i',
            '/<script>/i',
            '/javascript:/i',
            '/onclick/i',
            '/onload/i',
            '/eval\s*\(/i',
            '/base64_/i',
            '/document\./i'
        ];

        $input = json_encode($request->all());
        foreach ($suspicious_patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        // فحص عدد الطلبات
        $ip = $request->ip();
        $key = 'requests_count_' . $ip;
        $count = cache()->get($key, 0);
        
        if ($count > 100) { // أكثر من 100 طلب في الدقيقة
            return true;
        }
        
        cache()->put($key, $count + 1, 60); // تخزين لمدة دقيقة
        
        return false;
    }

    /**
     * التحقق مما إذا كانت الصفحة حساسة
     */
    private function isSensitivePage(Request $request)
    {
        $sensitive_routes = [
            'admin/*',
            'profile/*',
            'settings/*',
            'password/*',
            'billing/*'
        ];

        foreach ($sensitive_routes as $route) {
            if ($request->is($route)) {
                return true;
            }
        }

        return false;
    }
}
