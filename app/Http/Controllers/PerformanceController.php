<?php

namespace App\Http\Controllers;

use App\Services\PerformanceMonitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class PerformanceController extends Controller
{
    private $performanceMonitor;

    public function __construct(PerformanceMonitor $performanceMonitor)
    {
        $this->performanceMonitor = $performanceMonitor;
    }

    public function dashboard()
    {
        $stats = $this->performanceMonitor->getPerformanceStats();
        
        // Process chart data
        $charts = [
            'response_times' => $this->processMetrics($stats['response_times'] ?? []),
            'memory_usage' => $this->processMetrics($stats['memory_usage'] ?? [])
        ];
        
        return view('dashboard.performance.index', [
            'stats' => $stats,
            'charts' => $charts,
            'cacheMetrics' => $this->getCacheMetrics(),
            'databaseMetrics' => $this->getDatabaseMetrics()
        ]);
    }

    public function metrics()
    {
        // Cache the raw stats
        $stats = Cache::remember('current_stats', 5, function () {
            return $this->performanceMonitor->getPerformanceStats();
        });
        
        // Process chart data
        $charts = [
            'response_times' => $this->processMetrics($stats['response_times'] ?? []),
            'memory_usage' => $this->processMetrics($stats['memory_usage'] ?? [])
        ];
        
        return response()->json([
            'current' => $stats,
            'charts' => $charts
        ]);
    }

    private function processMetrics($metrics)
    {
        return collect($metrics)
            ->map(function ($item) {
                $data = is_string($item) ? json_decode($item, true) : $item;
                return [
                    'timestamp' => $data['timestamp'] ?? now()->timestamp,
                    'value' => round($data['value'] ?? 0, 2)
                ];
            })
            ->sortBy('timestamp')
            ->values()
            ->take(30);
    }

    private function getCacheMetrics()
    {
        return Cache::remember('cache_metrics', 5, function () {
            return [
                'hits' => Cache::get('cache_hits', 0),
                'misses' => Cache::get('cache_misses', 0),
                'ratio' => $this->calculateCacheHitRatio()
            ];
        });
    }

    private function calculateCacheHitRatio()
    {
        $hits = Cache::get('cache_hits', 0);
        $misses = Cache::get('cache_misses', 0);
        $total = $hits + $misses;
        return $total > 0 ? round($hits / $total, 2) : 0;
    }

    private function getDatabaseMetrics()
    {
        return Cache::remember('database_metrics', 5, function () {
            $slowQueries = collect($this->performanceMonitor->getMetrics('slow_query', 10));
            return [
                'slow_queries' => $slowQueries
                    ->map(function ($query) {
                        $data = is_string($query) ? json_decode($query, true) : $query;
                        return [
                            'sql' => $data['tags']['sql'] ?? '',
                            'duration' => round($data['value'] ?? 0, 2),
                            'timestamp' => $data['timestamp'] ?? now()->timestamp
                        ];
                    })
                    ->sortByDesc('duration')
                    ->values()
                    ->take(5)
            ];
        });
    }
}
