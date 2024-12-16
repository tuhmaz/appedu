<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;

class LoginController extends Controller
{
    protected function analyzeLoginAttempt(Request $request, $success = false, $failureReason = null)
    {
        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());
        
        // تحليل نوع المحاولة
        $attemptType = 'normal';
        
        // فحص محاولات الوصول المشبوهة
        if (!$success) {
            // فحص تكرار المحاولات من نفس IP
            $recentAttempts = LoginAttempt::where('ip_address', $request->ip())
                ->where('created_at', '>=', now()->subHours(1))
                ->count();

            if ($recentAttempts > 5) {
                $attemptType = 'brute_force';
            }

            // فحص محاولات SQL Injection
            if (preg_match('/(union|select|from|where|delete|drop|update|insert)/i', $request->input('email'))) {
                $attemptType = 'sql_injection';
            }

            // فحص محاولات XSS
            if (preg_match('/<script|javascript:|alert\(|onclick/i', $request->input('email'))) {
                $attemptType = 'xss_attempt';
            }

            // فحص استخدام بوت
            if ($agent->isRobot()) {
                $attemptType = 'bot_attempt';
            }

            // فحص استخدام VPN/Proxy
            // هنا يمكنك إضافة خدمة للتحقق من IP
            // مثال: if (isProxy($request->ip())) { $attemptType = 'proxy_attempt'; }
        }

        // تسجيل المحاولة
        LoginAttempt::create([
            'email' => $request->input('email'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'successful' => $success,
            'failure_reason' => $failureReason,
            'attempt_type' => $attemptType
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $this->analyzeLoginAttempt($request, true);
            return redirect()->intended('dashboard');
        }

        $this->analyzeLoginAttempt($request, false, 'بيانات غير صحيحة');
        return back()->withErrors([
            'email' => 'بيانات تسجيل الدخول غير صحيحة.',
        ]);
    }
}
