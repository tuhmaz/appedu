@php
$configData = Helper::appClasses();
@endphp

@extends('layouts.layoutMaster')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Analytics Dashboard</h1>

    <div class="row">
        <!-- Online Visitors Card -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="display-4 text-primary mb-2">{{ $onlineVisitors }}</h3>
                    <h5 class="text-muted">Online Visitors</h5>
                    <small class="text-muted">Active in last 5 minutes</small>
                </div>
            </div>
        </div>

        <!-- Today's Visitors Card -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="display-4 text-success mb-2">{{ $todayVisitors }}</h3>
                    <h5 class="text-muted">Today's Visitors</h5>
                    <small class="text-muted">Unique by IP address</small>
                </div>
            </div>
        </div>

        <!-- Response Time Card -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="display-4 text-info mb-2">{{ number_format($averageResponseTime, 2) }}</h3>
                    <h5 class="text-muted">Average Response Time</h5>
                    <small class="text-muted">milliseconds</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Visitor Locations -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Visitor Locations</h5>
                    <small class="text-muted">Last 7 days</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Country</th>
                                    <th class="text-end">Visitors</th>
                                    <th class="text-end">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalVisitors = $visitorLocations->sum('total');
                                @endphp
                                @foreach($visitorLocations as $location)
                                    <tr>
                                        <td>{{ $location->country }}</td>
                                        <td class="text-end">{{ $location->total }}</td>
                                        <td class="text-end">
                                            {{ number_format(($location->total / $totalVisitors) * 100, 1) }}%
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Device Types -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Device Types</h5>
                    <small class="text-muted">Last 7 days</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Device</th>
                                    <th class="text-end">Visitors</th>
                                    <th class="text-end">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalDevices = $deviceTypes->sum('total');
                                @endphp
                                @foreach($deviceTypes as $device)
                                    <tr>
                                        <td>{{ ucfirst($device->device_type) }}</td>
                                        <td class="text-end">{{ $device->total }}</td>
                                        <td class="text-end">
                                            {{ number_format(($device->total / $totalDevices) * 100, 1) }}%
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hourly Trend -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Today's Visitor Trend</h5>
            <small class="text-muted">Hourly breakdown</small>
        </div>
        <div class="card-body">
            <div class="chart-container" style="height: 300px;">
                <canvas id="hourlyTrendChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hourlyData = @json($hourlyTrend);
        const labels = Array.from({length: 24}, (_, i) => `${i}:00`);
        const data = Array.from({length: 24}, () => 0);
        
        hourlyData.forEach(item => {
            data[item.hour] = item.total;
        });

        new Chart(document.getElementById('hourlyTrendChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Visitors',
                    data: data,
                    borderColor: '#4e73df',
                    tension: 0.3,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-5px);
    }
</style>
@endpush
@endsection
