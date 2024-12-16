<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CacheService
{
    /**
     * الوقت الافتراضي للتخزين المؤقت (بالدقائق)
     */
    protected int $defaultTtl = 60;

    /**
     * الحصول على البيانات من الكاش أو تنفيذ الدالة وتخزين النتيجة
     */
    public function remember(string $key, callable $callback, ?int $ttl = null): mixed
    {
        try {
            return Cache::remember($key, $ttl ?? $this->defaultTtl * 60, $callback);
        } catch (\Exception $e) {
            Log::error("Cache error for key {$key}: " . $e->getMessage());
            return $callback();
        }
    }

    /**
     * تخزين البيانات في الكاش
     */
    public function put(string $key, mixed $value, ?int $ttl = null): bool
    {
        try {
            return Cache::put($key, $value, $ttl ?? $this->defaultTtl * 60);
        } catch (\Exception $e) {
            Log::error("Cache put error for key {$key}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * الحصول على البيانات من الكاش
     */
    public function get(string $key, mixed $default = null): mixed
    {
        try {
            return Cache::get($key, $default);
        } catch (\Exception $e) {
            Log::error("Cache get error for key {$key}: " . $e->getMessage());
            return $default;
        }
    }

    /**
     * حذف البيانات من الكاش
     */
    public function forget(string $key): bool
    {
        try {
            return Cache::forget($key);
        } catch (\Exception $e) {
            Log::error("Cache forget error for key {$key}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * تحديث التخزين المؤقت للمفتاح المحدد
     */
    public function refresh(string $key, callable $callback, ?int $ttl = null): mixed
    {
        $this->forget($key);
        return $this->remember($key, $callback, $ttl);
    }

    /**
     * إنشاء مفتاح كاش مع المعرفات
     */
    public function makeKey(string $prefix, ...$identifiers): string
    {
        return $prefix . ':' . implode(':', array_filter($identifiers));
    }

    /**
     * تنظيف الكاش بناءً على نمط معين
     */
    public function clearPattern(string $pattern): bool
    {
        try {
            $keys = Cache::getStore()->getPrefix() . $pattern;
            Cache::getStore()->flush();
            return true;
        } catch (\Exception $e) {
            Log::error("Cache clear pattern error for {$pattern}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * الحصول على إحصائيات الكاش
     */
    public function getStats(): array
    {
        try {
            return [
                'driver' => config('cache.default'),
                'size' => Cache::size(),
                'uptime' => Cache::getStore()->getUptime(),
            ];
        } catch (\Exception $e) {
            Log::error("Cache stats error: " . $e->getMessage());
            return [];
        }
    }
}
