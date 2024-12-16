@php
$configData = Helper::appClasses();
@endphp

@extends('layouts.layoutMaster')

@section('title', 'التقارير الأمنية - محاولات تسجيل الدخول')

@section('vendor-style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css">
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>
            <i class="ri-shield-keyhole-line me-2"></i>
            التقارير الأمنية
        </h4>
        <div>
            <a href="{{ route('login-attempts.export-report') }}" class="btn btn-primary">
                <i class="ri-download-2-line me-1"></i>
                تصدير التقرير
            </a>
            <a href="{{ route('login-attempts.index') }}" class="btn btn-secondary">
                <i class="ri-arrow-left-line me-1"></i>
                العودة
            </a>
        </div>
    </div>

    <!-- المحاولات المشبوهة -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-error-warning-line me-2 text-warning"></i>
                        المحاولات المشبوهة (24 ساعة الماضية)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>IP عنوان</th>
                                    <th>عدد المحاولات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($suspiciousAttempts as $attempt)
                                    <tr>
                                        <td>{{ $attempt->ip_address }}</td>
                                        <td>
                                            <span class="badge bg-danger">{{ $attempt->attempts_count }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center">لا توجد محاولات مشبوهة</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-time-line me-2 text-primary"></i>
                        المحاولات خلال الأسبوع الماضي
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="timeBasedChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- تقارير إضافية -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-global-line me-2 text-info"></i>
                        أكثر عناوين IP نشاطاً
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>IP عنوان</th>
                                    <th>المحاولات الناجحة</th>
                                    <th>المحاولات الفاشلة</th>
                                    <th>الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topIpAddresses as $ip)
                                    <tr>
                                        <td>{{ $ip->ip_address }}</td>
                                        <td>
                                            <span class="badge bg-success">{{ $ip->successful_attempts }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger">{{ $ip->failed_attempts }}</span>
                                        </td>
                                        <td>{{ $ip->total_attempts }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-user-follow-line me-2 text-warning"></i>
                        أنماط محاولات الاختراق
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>البريد الإلكتروني</th>
                                    <th>عدد المحاولات</th>
                                    <th>أسباب الفشل</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($commonPatterns as $pattern)
                                    <tr>
                                        <td>{{ $pattern->email }}</td>
                                        <td>
                                            <span class="badge bg-danger">{{ $pattern->attempts_count }}</span>
                                        </td>
                                        <td>{{ $pattern->failure_reasons ?: 'غير محدد' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // إعداد بيانات الرسم البياني
    const timeData = @json($timeBasedAttempts);
    
    const ctx = document.getElementById('timeBasedChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: timeData.map(item => item.date),
            datasets: [{
                label: 'إجمالي المحاولات',
                data: timeData.map(item => item.total_attempts),
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }, {
                label: 'المحاولات الفاشلة',
                data: timeData.map(item => item.failed_attempts),
                borderColor: 'rgb(255, 99, 132)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endsection
