<?php

namespace App\Jobs;

use App\Models\LoginAttempt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CleanupLoginAttempts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        try {
            $successfulCount = LoginAttempt::where('successful', true)->count();

            if ($successfulCount > 1000) {
                // احتفظ بأحدث 1000 محاولة ناجحة واحذف الباقي
                $keepAttempts = LoginAttempt::where('successful', true)
                    ->latest()
                    ->take(1000)
                    ->pluck('id');

                $deleted = LoginAttempt::where('successful', true)
                    ->whereNotIn('id', $keepAttempts)
                    ->delete();

                Log::info("تم حذف {$deleted} من محاولات تسجيل الدخول الناجحة القديمة");
            }
        } catch (\Exception $e) {
            Log::error('خطأ في تنظيف محاولات تسجيل الدخول: ' . $e->getMessage());
        }
    }
}
