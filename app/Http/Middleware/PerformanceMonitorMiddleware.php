<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\PerformanceMonitor;
use Illuminate\Support\Facades\Cache;

class PerformanceMonitorMiddleware
{
    private $performanceMonitor;

    public function __construct(PerformanceMonitor $performanceMonitor)
    {
        $this->performanceMonitor = $performanceMonitor;
    }

    public function handle($request, Closure $next)
    {
        // Skip monitoring for static assets and API calls
        $path = $request->path();
        if ($this->shouldSkipMonitoring($path)) {
            return $next($request);
        }

        // Start time tracking
        $startTime = microtime(true);
        
        // Execute request
        $response = $next($request);
        
        // Track metrics after response using a callback
        $monitor = $this->performanceMonitor;
        app()->terminating(function () use ($request, $startTime, $monitor) {
            $duration = (microtime(true) - $startTime) * 1000;
            
            // Only track if duration is significant
            if ($duration > 200) {
                $monitor->trackMetric('response_time', $duration, [
                    'path' => $request->path(),
                    'method' => $request->method()
                ]);
            }
            
            // Track memory only if significant
            $memoryUsage = memory_get_peak_usage(true) / 1024 / 1024;
            if ($memoryUsage > 25) {
                $monitor->trackMetric('memory_usage', $memoryUsage);
            }
            
            // Track cache metrics
            if (Cache::has('performance_metrics_last_check')) {
                $hits = Cache::get('cache_hits', 0);
                $misses = Cache::get('cache_misses', 0);
                $total = $hits + $misses;
                
                if ($total > 0) {
                    $monitor->trackMetric('cache_hit_ratio', $hits / $total);
                }
            }
        });
        
        return $response;
    }
    
    private function shouldSkipMonitoring($path): bool
    {
        // Skip static assets
        $skipPaths = ['assets', 'css', 'js', 'images', 'fonts', 'vendor'];
        foreach ($skipPaths as $skipPath) {
            if (str_starts_with($path, $skipPath)) {
                return true;
            }
        }
        
        // Skip API metrics endpoint to prevent recursion
        if (str_contains($path, 'performance/metrics')) {
            return true;
        }
        
        return false;
    }
}
