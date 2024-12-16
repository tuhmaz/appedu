<?php

namespace App\Traits;

use App\Services\CacheService;
use Illuminate\Support\Facades\App;

trait Cacheable
{
    protected function cache(): CacheService
    {
        return App::make(CacheService::class);
    }

    /**
     * تحديد مدة الكاش حسب نوع البيانات
     */
    protected function getCacheDuration(string $type): int
    {
        return match($type) {
            'static' => 60 * 24,     // 24 ساعة للبيانات الثابتة
            'dynamic' => 60 * 6,     // 6 ساعات للبيانات المتغيرة
            'frequent' => 60,        // ساعة واحدة للبيانات المتكررة
            'volatile' => 15,        // 15 دقيقة للبيانات سريعة التغير
            default => 30            // 30 دقيقة كوقت افتراضي
        };
    }

    /**
     * إنشاء مفتاح الكاش
     */
    protected function createCacheKey(string $prefix, ...$params): string
    {
        $database = request()->database ?? 'default';
        return $this->cache()->makeKey($prefix, $database, ...$params);
    }

    /**
     * حذف الكاش المرتبط
     */
    protected function clearRelatedCache(array $keys): void
    {
        foreach ($keys as $key) {
            $this->cache()->forget($key);
        }
    }

    /**
     * تحديث الكاش
     */
    protected function refreshCache(string $key, callable $callback, ?int $duration = null): mixed
    {
        return $this->cache()->refresh($key, $callback, $duration);
    }
}
