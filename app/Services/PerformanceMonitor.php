<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PerformanceMonitor
{
    private const METRICS_PREFIX = 'performance_metrics:';
    private const METRICS_EXPIRY = 3600; // 1 hour

    protected function cleanupOldData()
    {
        $cutoff = now()->subDays(7)->timestamp;
        
        foreach ($this->getMetricKeys() as $key) {
            Redis::zremrangebyscore("metrics:{$key}", '-inf', $cutoff);
        }
    }

    public function trackMetric(string $name, $value, array $tags = [])
    {
        // تجاهل القيم الصغيرة جداً
        if ($value < 0.001) {
            return;
        }

        $timestamp = now()->timestamp;
        $data = [
            'value' => round((float)$value, 2),
            'timestamp' => $timestamp,
            'tags' => $tags
        ];

        Redis::zadd(self::METRICS_PREFIX . $name, $timestamp, json_encode($data));

        // تنظيف البيانات القديمة كل 100 عملية تتبع
        if (rand(1, 100) === 1) {
            $this->cleanupOldData();
        }

        Redis::expire(self::METRICS_PREFIX . $name, self::METRICS_EXPIRY);
    }

    public function trackDatabaseQuery($sql, $time)
    {
        if ($time > 100) { // تتبع الاستعلامات التي تستغرق أكثر من 100ms
            $this->trackMetric('slow_query', $time, [
                'sql' => $sql,
                'threshold' => 100
            ]);
        }
    }

    public function trackMemoryUsage()
    {
        $memoryUsage = round(memory_get_usage(true) / 1024 / 1024, 2); // تحويل إلى ميجابايت
        $this->trackMetric('memory_usage', $memoryUsage);
    }

    public function trackCacheHit($key)
    {
        $this->trackMetric('cache_hit', 1, ['key' => $key]);
    }

    public function trackCacheMiss($key)
    {
        $this->trackMetric('cache_miss', 1, ['key' => $key]);
    }

    public function getMetrics(string $name, $minutes = 30)
    {
        $key = self::METRICS_PREFIX . $name;
        $start = now()->subMinutes($minutes)->timestamp;
        
        // الحصول على آخر 30 نقطة فقط
        $metrics = Redis::zrevrangebyscore($key, '+inf', $start, [
            'limit' => [0, 30]
        ]);

        return array_reverse($metrics); // ترتيب تصاعدي حسب الوقت
    }

    public function getMetricHistory($metric, $minutes = 30)
    {
        $start = now()->subMinutes($minutes)->timestamp;
        $end = now()->timestamp;
        
        return collect(Redis::zrangebyscore(
            self::METRICS_PREFIX . $metric,
            $start,
            $end
        ))->map(function ($item) {
            return json_decode($item, true);
        })->values();
    }

    public function getPerformanceStats()
    {
        return [
            'memory_usage' => $this->getLatestMetric('memory_usage'),
            'avg_response_time' => $this->calculateAverageMetric('response_time'),
            'response_times' => $this->getMetrics('response_time'),
            'slow_queries' => $this->getMetrics('slow_query'),
            'cache_hit_ratio' => $this->getCacheHitRatio(),
            'current_connections' => $this->getCurrentConnections(),
            'cpu_usage' => $this->getCpuUsage()
        ];
    }

    private function getLatestMetric(string $name)
    {
        $key = self::METRICS_PREFIX . $name;
        $latest = Redis::zrevrange($key, 0, 0);
        
        if (!empty($latest)) {
            $data = json_decode($latest[0], true);
            return isset($data['value']) ? round((float)$data['value'], 2) : 0;
        }
        
        return 0;
    }

    protected function calculateAverageMetric($metric, $minutes = 5)
    {
        $values = $this->getMetricHistory($metric, $minutes)
            ->pluck('value')
            ->filter(function ($value) {
                return $value >= 0.001; // تجاهل القيم الصغيرة جداً
            });
        
        return $values->isEmpty() ? 0 : $values->average();
    }

    public function getCacheHitRatio()
    {
        $hits = count($this->getMetrics('cache_hit'));
        $misses = count($this->getMetrics('cache_miss'));
        $total = $hits + $misses;

        return $total > 0 ? ($hits / $total) * 100 : 0;
    }

    private function getCurrentConnections()
    {
        try {
            // استخدام عدد الجلسات النشطة كمؤشر للاتصالات
            return DB::table('sessions')
                ->where('last_activity', '>=', now()->subMinutes(5)->timestamp)
                ->count();
        } catch (\Exception $e) {
            \Log::warning('Could not get current connections: ' . $e->getMessage());
            return 0;
        }
    }

    private function getCpuUsage()
    {
        if (PHP_OS_FAMILY === 'Windows') {
            try {
                // استخدام wmic لقياس استخدام المعالج على Windows
                $cmd = 'wmic cpu get loadpercentage /value';
                $output = shell_exec($cmd);
                
                if ($output && preg_match('/LoadPercentage=(\d+)/', $output, $matches)) {
                    return (float) $matches[1];
                }
                
                // طريقة بديلة باستخدام PowerShell
                $cmd = 'powershell "Get-WmiObject Win32_Processor | Select-Object -ExpandProperty LoadPercentage"';
                $output = shell_exec($cmd);
                
                if ($output) {
                    return (float) trim($output);
                }
            } catch (\Exception $e) {
                \Log::warning('Could not get CPU usage on Windows: ' . $e->getMessage());
            }
            
            // إذا فشلت كل المحاولات، نعيد قيمة افتراضية
            return 0;
        } else {
            // استخدام sys_getloadavg على أنظمة Unix
            try {
                $load = sys_getloadavg();
                return isset($load[0]) ? (float) $load[0] * 100 : 0;
            } catch (\Exception $e) {
                \Log::warning('Could not get CPU load average: ' . $e->getMessage());
                return 0;
            }
        }
    }

    protected function getMetricKeys()
    {
        return [
            'response_time',
            'memory_usage',
            'slow_query',
            'cache_hit',
            'cache_miss'
        ];
    }
}
