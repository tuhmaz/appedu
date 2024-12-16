<?php

namespace App\Http\RateLimiter;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class ApiRateLimiter
{
    public function configureRateLimiting(): void
    {
        // تحديد Rate Limiting للـ API العام
        RateLimiter::for('api', function (Request $request) {
            $key = $request->user()?->id ?: $request->ip();
            
            return [
                // حد عام للطلبات - زيادة الحد لاستيعاب حركة المرور العالية
                Limit::perMinute(300)->by($key),
                
                // حد خاص لمحاولات تسجيل الدخول - يبقى منخفضاً لأسباب أمنية
                Limit::perMinute(5)
                    ->by('auth:' . $key)
                    ->response(function () {
                        return response()->json([
                            'message' => 'تم تجاوز الحد المسموح به من المحاولات. الرجاء المحاولة بعد دقيقة.',
                            'status' => 429
                        ], 429);
                    }),
            ];
        });

        // Rate Limiting للقراءة العامة (مثل قراءة المقالات)
        RateLimiter::for('public_content', function (Request $request) {
            return [
                // السماح بـ 2000 طلب لكل IP خلال 5 دقائق
                Limit::perMinutes(5, 2000)->by($request->ip()),
                // حد يومي مرتفع
                Limit::perDay(50000)->by($request->ip()),
            ];
        });

        // Rate Limiting للمستخدمين المسجلين
        RateLimiter::for('authenticated_api', function (Request $request) {
            return $request->user() ? 
                // حد أعلى للمستخدمين المسجلين
                Limit::perMinute(500)->by($request->user()->id) :
                // حد للزوار غير المسجلين
                Limit::perMinute(300)->by($request->ip());
        });

        // Rate Limiting للعمليات الحساسة (مثل التعليقات والتفاعلات)
        RateLimiter::for('interactions', function (Request $request) {
            return [
                // 30 تفاعل كل 5 دقائق لكل مستخدم
                Limit::perMinutes(5, 30)->by($request->user()?->id ?: $request->ip()),
                // 500 تفاعل في اليوم
                Limit::perDay(500)->by($request->user()?->id ?: $request->ip()),
            ];
        });

        // Rate Limiting لمحاولات تسجيل الدخول
        RateLimiter::for('login', function (Request $request) {
            $key = 'login.' . $request->ip();
            return [
                // 5 محاولات كل 5 دقائق
                Limit::perMinutes(5, 5)->by($key),
                // 20 محاولة في اليوم
                Limit::perDay(20)->by($key),
            ];
        });
    }
}
