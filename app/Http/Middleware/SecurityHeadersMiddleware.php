<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // السماح بالوصول إلى الملفات الثابتة
        $path = $request->path();
        if (str_contains($path, '.js') || 
            str_contains($path, '.css') || 
            str_contains($path, '.scss') || 
            str_contains($path, '.map') || 
            str_contains($path, '.jpg') || 
            str_contains($path, '.png') || 
            str_contains($path, '.gif') || 
            str_contains($path, '.woff') || 
            str_contains($path, '.woff2') || 
            str_contains($path, '.ttf')) {
            return $response;
        }

        // تجاهل الرؤوس الأمنية لمسارات المصادقة والملفات الثابتة
        if ($request->is('login', 'register', 'password/reset', 'password/email', 'logout', 'js/*', 'css/*', 'images/*', 'fonts/*', 'assets/*')) {
            return $response;
        }

        // إعدادات أساسية للأمان
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');

        // تكوين CSP مع السماح بالموارد المحلية
        $csp = [
            "default-src 'self' 'unsafe-inline' 'unsafe-eval' * data: blob:",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' * data: blob:",
            "style-src 'self' 'unsafe-inline' * data: blob:",
            "img-src 'self' * data: blob:",
            "font-src 'self' * data:",
            "connect-src 'self' *",
            "media-src 'self' *",
            "form-action 'self'",
            "frame-ancestors 'self'"
        ];
        
        $response->headers->set('Content-Security-Policy', implode('; ', $csp));

        // إعدادات CORS مرنة
        if ($request->header('Origin')) {
            $response->headers->set('Access-Control-Allow-Origin', $request->header('Origin'));
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', '*');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Expose-Headers', '*');
        }

        // تكوين التخزين المؤقت
        if ($request->is('api/*')) {
            // لا تخزين مؤقت لطلبات API
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        } else {
            // السماح بالتخزين المؤقت للمحتوى الثابت
            $response->headers->set('Cache-Control', 'public, max-age=31536000');
        }

        return $response;
    }
}
