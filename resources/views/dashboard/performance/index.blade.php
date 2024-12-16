@php
$configData = Helper::appClasses();
@endphp

@extends('layouts.layoutMaster')

@section('title', 'لوحة تحكم الأداء')

@section('vendor-style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.41.0/dist/apexcharts.css">
@endsection

@section('vendor-script')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.41.0/dist/apexcharts.min.js"></script>
@endsection

@section('page-style')
<style>
.performance-card {
    transition: transform 0.2s ease-in-out;
    height: 100%;
    min-height: 160px;
}
.performance-card:hover {
    transform: translateY(-5px);
}
.metric-value {
    font-size: 2rem;
    font-weight: 600;
    line-height: 1.2;
    margin: 1rem 0;
}
.metric-label {
    font-size: 0.875rem;
    color: #566a7f;
    margin-bottom: 1rem;
}
.chart-container {
    min-height: 400px;
    margin: 1rem 0;
}
.card {
    margin-bottom: 1.5rem;
}
.card-header {
    padding: 1.5rem;
    border-bottom: 1px solid rgba(0,0,0,0.1);
}
.card-body {
    padding: 1.5rem;
}
@media (max-width: 768px) {
    .metric-value {
        font-size: 1.5rem;
    }
    .chart-container {
        min-height: 300px;
    }
}
</style>
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card bg-primary mb-4">
                <div class="card-body py-3 px-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-white me-2 flex-shrink-0">
                            <span class="avatar-initial rounded-circle text-primary">
                                <i class="ti ti-chart-bar ti-md"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0 text-white">لوحة تحكم الأداء</h4>
                            <small class="text-white text-opacity-75">مراقبة أداء النظام في الوقت الفعلي</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="card-info">
                            <p class="card-text">متوسط وقت الاستجابة</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2">{{ number_format($stats['avg_response_time'], 2) }}</h4>
                                <small>ms</small>
                            </div>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded p-2">
                                <i class='bx bx-time bx-sm'></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="card-info">
                            <p class="card-text">استخدام الذاكرة</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2">{{ number_format($stats['memory_usage'], 2) }}</h4>
                                <small>MB</small>
                            </div>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-success rounded p-2">
                                <i class="bx bx-memory-card bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="card-info">
                            <p class="card-text">نسبة نجاح الكاش</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2">{{ number_format($stats['cache_hit_ratio'], 1) }}</h4>
                                <small>%</small>
                            </div>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-info rounded p-2">
                                <i class="bx bx-data bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="card-info">
                            <p class="card-text">الاستعلامات البطيئة</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2">{{ count($stats['slow_queries']) }}</h4>
                                <small>استعلام</small>
                            </div>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-warning rounded p-2">
                                <i class="bx bx-error bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- System Metrics -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">مقاييس النظام</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="metric-card">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-label-primary rounded p-2">
                                            <i class="bx bx-chip bx-sm"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0">استخدام المعالج</h6>
                                        <small class="text-muted">{{ number_format($stats['cpu_usage'], 1) }}%</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="metric-card">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-label-success rounded p-2">
                                            <i class="bx bx-group bx-sm"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0">الاتصالات الحالية</h6>
                                        <small class="text-muted">{{ number_format($stats['current_connections']) }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- وقت الاستجابة -->
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header pb-3">
                    <h5 class="card-title mb-0">وقت الاستجابة</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <div id="response-time-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- استخدام الذاكرة -->
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header pb-3">
                    <h5 class="card-title mb-0">استخدام الذاكرة</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <div id="memory-usage-chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cache DOM elements
    const elements = {
        responseTime: document.getElementById('response-time'),
        memoryUsage: document.getElementById('memory-usage'),
        cpuUsage: document.getElementById('cpu-usage'),
        cacheHitRatio: document.getElementById('cache-hit-ratio')
    };

    // Chart configuration with optimized options
    const chartConfig = {
        chart: {
            height: 350,
            type: 'line',
            animations: {
                enabled: false
            },
            toolbar: {
                show: false
            }
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        markers: {
            size: 0
        },
        tooltip: {
            enabled: true,
            shared: true
        },
        grid: {
            show: true,
            borderColor: '#f0f0f0',
            strokeDashArray: 0,
            position: 'back'
        }
    };

    // Initialize charts with optimized data
    const responseTimeChart = new ApexCharts(
        document.querySelector("#response-time-chart"), 
        {
            ...chartConfig,
            series: [{
                name: 'وقت الاستجابة (مللي ثانية)',
                data: {!! json_encode($charts['response_times']->map(function($point) {
                    return [$point['timestamp'] * 1000, round($point['value'], 2)];
                })->values()->all()) !!}
            }],
            yaxis: {
                labels: {
                    formatter: (val) => `${val.toFixed(2)} ms`
                }
            }
        }
    );

    const memoryUsageChart = new ApexCharts(
        document.querySelector("#memory-usage-chart"), 
        {
            ...chartConfig,
            series: [{
                name: 'استخدام الذاكرة (ميجابايت)',
                data: {!! json_encode($charts['memory_usage']->map(function($point) {
                    return [$point['timestamp'] * 1000, round($point['value'], 2)];
                })->values()->all()) !!}
            }],
            yaxis: {
                labels: {
                    formatter: (val) => `${val.toFixed(0)} MB`
                }
            }
        }
    );

    responseTimeChart.render();
    memoryUsageChart.render();

    // Efficient update function using requestAnimationFrame
    let updatePending = false;
    
    function updateMetrics() {
        if (updatePending) return;
        updatePending = true;

        requestAnimationFrame(async () => {
            try {
                const response = await fetch('{{ route("performance.metrics") }}');
                const data = await response.json();
                
                // Batch DOM updates
                const updates = {
                    'response-time': `${data.current.avg_response_time.toFixed(2)} ms`,
                    'memory-usage': `${data.current.memory_usage} MB`,
                    'cpu-usage': `${data.current.cpu_usage}%`,
                    'cache-hit-ratio': `${(data.current.cache_hit_ratio * 100).toFixed(1)}%`
                };

                Object.entries(updates).forEach(([id, value]) => {
                    const element = document.getElementById(id);
                    if (element) element.textContent = value;
                });

                // Update charts efficiently
                responseTimeChart.updateSeries([{
                    data: data.charts.response_times.map(point => [point.timestamp * 1000, point.value])
                }]);

                memoryUsageChart.updateSeries([{
                    data: data.charts.memory_usage.map(point => [point.timestamp * 1000, point.value])
                }]);
            } catch (error) {
                console.error('Error updating metrics:', error);
            }
            
            updatePending = false;
        });
    }

    // Update metrics every 60 seconds
    const updateInterval = setInterval(updateMetrics, 60000);

    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        clearInterval(updateInterval);
    });
});
</script>
@endsection
