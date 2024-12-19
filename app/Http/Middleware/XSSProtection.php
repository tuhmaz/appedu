<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class XSSProtection
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
        // تنظيف جميع المدخلات
        $input = $request->all();
        array_walk_recursive($input, function (&$item) {
            if (is_string($item)) {
                // تنظيف النصوص من أي محتوى ضار
                $item = strip_tags($item);
                $item = htmlspecialchars($item, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        });
        $request->merge($input);

        $response = $next($request);

        // إضافة رؤوس الأمان الأساسية
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // تكوين CSP للسماح بـ Vite و SCSS
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://alemedu.com; " .
               "style-src 'self' 'unsafe-inline' https://alemedu.com; " .
               "img-src 'self' data: blob: https://alemedu.com; " .
               "font-src 'self' data: https://alemedu.com; " .
               "connect-src 'self' wss://alemedu.com https://alemedu.com; " .
               "media-src 'self' https://alemedu.com; " .
               "object-src 'none'; " .
               "base-uri 'self';";

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
