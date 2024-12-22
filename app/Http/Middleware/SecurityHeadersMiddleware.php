<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    private array $allowedStaticExtensions = [
        '.js', '.css', '.scss', '.map', 
        '.jpg', '.jpeg', '.png', '.gif', '.svg', '.webp',
        '.woff', '.woff2', '.ttf', '.eot'
    ];

    private array $bypassPaths = [
        'login', 'register', 'password/reset', 'password/email', 'logout',
        'js/*', 'css/*', 'images/*', 'fonts/*', 'assets/*'
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // التحقق من امتدادات الملفات الثابتة
        $path = $request->path();
        foreach ($this->allowedStaticExtensions as $extension) {
            if (str_contains($path, $extension)) {
                return $response;
            }
        }

        // تجاهل المسارات المستثناة
        if ($request->is($this->bypassPaths)) {
            return $response;
        }

        // إزالة الرؤوس غير الضرورية
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        // إضافة رؤوس الأمان الأساسية
        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-XSS-Protection' => '1; mode=block',
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
            'Cross-Origin-Embedder-Policy' => 'require-corp',
            'Cross-Origin-Opener-Policy' => 'same-origin',
            'Cross-Origin-Resource-Policy' => 'same-origin'
        ];

        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        // تكوين Content Security Policy
        $cspDirectives = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'", // نحتاج إلى unsafe-inline/eval للوحة التحكم
            "style-src 'self' 'unsafe-inline'",
            "img-src 'self' data: blob:",
            "font-src 'self' data:",
            "connect-src 'self'",
            "media-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'"
        ];

        $response->headers->set('Content-Security-Policy', implode('; ', $cspDirectives));

        // إعدادات CORS للـ API فقط
        if ($request->is('api/*') && $request->header('Origin')) {
            $allowedOrigins = [env('APP_URL', 'https://alemedu.com')];
            $origin = $request->header('Origin');

            if (in_array($origin, $allowedOrigins)) {
                $response->headers->set('Access-Control-Allow-Origin', $origin);
                $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
                $response->headers->set('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Authorization, X-CSRF-TOKEN');
                $response->headers->set('Access-Control-Allow-Credentials', 'true');
                $response->headers->set('Access-Control-Max-Age', '86400'); // 24 ساعة
            }
        }

        return $response;
    }
}
