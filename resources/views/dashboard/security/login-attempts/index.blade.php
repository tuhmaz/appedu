@php
$configData = Helper::appClasses();
@endphp

@extends('layouts.layoutMaster')

@section('title', 'تتبع محاولات تسجيل الدخول')

@section('vendor-style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" />
@endsection

@section('content')
<div class="container-fluid">
    <!-- إحصائيات سريعة -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body d-flex align-items-center">
                    <div class="stats-icon">
                        <i class="ri-shield-keyhole-line"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">{{ $statistics['total'] }}</h4>
                        <div>إجمالي المحاولات</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body d-flex align-items-center">
                    <div class="stats-icon">
                        <i class="ri-check-double-line"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">{{ $statistics['successful'] }}</h4>
                        <div>المحاولات الناجحة</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body d-flex align-items-center">
                    <div class="stats-icon">
                        <i class="ri-close-circle-line"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">{{ $statistics['failed'] }}</h4>
                        <div>المحاولات الفاشلة</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body d-flex align-items-center">
                    <div class="stats-icon">
                        <i class="ri-time-line"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">{{ $statistics['recent_failed'] }}</h4>
                        <div>المحاولات الفاشلة (24 ساعة)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول المحاولات -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">سجل محاولات تسجيل الدخول</h5>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary btn-sm" onclick="window.location.reload()">
                    <i class="ri-refresh-line me-1"></i>
                    تحديث
                </button>
                <form action="{{ route('login-attempts.clear-old') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('هل أنت متأكد من حذف المحاولات القديمة؟')">
                        <i class="ri-delete-bin-line me-1"></i>
                        حذف المحاولات القديمة
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success d-flex align-items-center">
                <i class="ri-checkbox-circle-line me-2"></i>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center">
                <i class="ri-error-warning-line me-2"></i>
                {{ session('error') }}
            </div>
            @endif
            
            <form action="{{ route('login-attempts.delete-selected') }}" method="POST" id="attempts-form">
                @csrf
                <div class="table-responsive">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="select-all">
                            <label class="form-check-label" for="select-all">تحديد الكل</label>
                        </div>
                        <button type="submit" class="btn btn-danger btn-sm" id="delete-selected" style="display: none;">
                            <i class="ri-delete-bin-line me-1"></i>
                            حذف المحدد
                        </button>
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="40">#</th>
                                <th>البريد الإلكتروني</th>
                                <th>IP</th>
                                <th>الحالة</th>
                                <th>نوع المحاولة</th>
                                <th>سبب الفشل</th>
                                <th>التاريخ</th>
                                <th>التفاصيل</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attempts as $attempt)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input attempt-checkbox" 
                                                name="selected_attempts[]" 
                                                value="{{ $attempt->id }}">
                                        </div>
                                    </td>
                                    <td>{{ $attempt->email }}</td>
                                    <td>{{ $attempt->ip_address }}</td>
                                    <td>
                                        @if($attempt->successful)
                                            <span class="badge bg-success">
                                                <i class="ri-check-line me-1"></i>
                                                ناجحة
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="ri-close-circle-line me-1"></i>
                                                فاشلة
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($attempt->attempt_type)
                                            @case('brute_force')
                                                <span class="badge bg-danger">
                                                    <i class="ri-spam-2-line me-1"></i>
                                                    محاولة اختراق متكررة
                                                </span>
                                                @break
                                            @case('sql_injection')
                                                <span class="badge bg-danger">
                                                    <i class="ri-bug-line me-1"></i>
                                                    SQL Injection
                                                </span>
                                                @break
                                            @case('xss_attempt')
                                                <span class="badge bg-danger">
                                                    <i class="ri-code-line me-1"></i>
                                                    XSS محاولة
                                                </span>
                                                @break
                                            @case('bot_attempt')
                                                <span class="badge bg-warning">
                                                    <i class="ri-robot-line me-1"></i>
                                                    محاولة روبوت
                                                </span>
                                                @break
                                            @case('proxy_attempt')
                                                <span class="badge bg-warning">
                                                    <i class="ri-shield-cross-line me-1"></i>
                                                    VPN/Proxy استخدام
                                                </span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">
                                                    <i class="ri-user-line me-1"></i>
                                                    عادية
                                                </span>
                                        @endswitch
                                    </td>
                                    <td>{{ $attempt->failure_reason ?: '-' }}</td>
                                    <td>{{ $attempt->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>
                                        <a href="{{ route('login-attempts.show', $attempt) }}" class="btn btn-sm btn-info">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <i class="ri-information-line me-1"></i>
                                        لا توجد محاولات تسجيل دخول
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>

            <div class="mt-4">
                {{ $attempts->links() }}
            </div>
        </div>
    </div>
</div>

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.attempt-checkbox');
    const deleteSelected = document.getElementById('delete-selected');
    const form = document.getElementById('attempts-form');

    // تحديد/إلغاء تحديد الكل
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateDeleteButton();
    });

    // تحديث حالة زر الحذف عند تغيير أي checkbox
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateDeleteButton();
            // تحديث حالة "تحديد الكل" إذا تم تحديد/إلغاء تحديد كل الصناديق
            selectAll.checked = [...checkboxes].every(c => c.checked);
        });
    });

    // تحديث ظهور زر الحذف
    function updateDeleteButton() {
        const hasChecked = [...checkboxes].some(c => c.checked);
        deleteSelected.style.display = hasChecked ? 'block' : 'none';
    }

    // تأكيد الحذف
    form.addEventListener('submit', function(e) {
        if (!confirm('هل أنت متأكد من حذف المحاولات المحددة؟')) {
            e.preventDefault();
        }
    });
});
</script>
@endsection

<style>
/* تنسيق الأيقونات في المربعات العلوية */
.stats-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    margin-right: 1rem;
    position: relative;
}

.stats-icon::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 50%;
    border: 2px solid rgba(255, 255, 255, 0.5);
}

.stats-icon i {
    font-size: 24px;
    color: #fff;
}

/* تنسيق البطاقات */
.card .card-body {
    padding: 1.25rem;
}

.card h4 {
    font-size: 1.5rem;
    margin-bottom: 0.25rem;
}

.card .text-white div:last-child {
    font-size: 0.875rem;
    opacity: 0.9;
}

/* تنسيق رأس الجدول */
.table thead th {
    background-color: #e9ecef;
    border-bottom: 2px solid #dee2e6;
    color: #495057;
    font-weight: 600;
    padding: 0.75rem;
    vertical-align: middle;
    white-space: nowrap;
}

/* تنسيق خلايا الجدول */
.table td {
    padding: 0.75rem;
    vertical-align: middle;
}

/* تنسيق الصفوف عند المرور عليها */
.table tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

/* تنسيق حدود الجدول */
.table-bordered th,
.table-bordered td {
    border: 1px solid #dee2e6;
}
</style>

@endsection
