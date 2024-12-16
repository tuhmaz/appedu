@php
$configData = Helper::appClasses();
@endphp

@extends('layouts.layoutMaster')

@section('title', 'تفاصيل محاولة تسجيل الدخول')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="ri-file-list-line me-2"></i>
                تفاصيل محاولة تسجيل الدخول
            </h5>
            <a href="{{ route('login-attempts.index') }}" class="btn btn-secondary btn-sm">
                <i class="ri-arrow-left-line me-1"></i>
                العودة للقائمة
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th class="bg-light" style="width: 200px;">
                                <i class="ri-mail-line me-2"></i>
                                البريد الإلكتروني
                            </th>
                            <td>{{ $attempt->email }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">
                                <i class="ri-global-line me-2"></i>
                                عنوان IP
                            </th>
                            <td>{{ $attempt->ip_address }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">
                                <i class="ri-shield-keyhole-line me-2"></i>
                                الحالة
                            </th>
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
                        </tr>
                        <tr>
                            <th class="bg-light">
                                <i class="ri-error-warning-line me-2"></i>
                                سبب الفشل
                            </th>
                            <td>{{ $attempt->failure_reason ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">
                                <i class="ri-window-line me-2"></i>
                                متصفح المستخدم
                            </th>
                            <td>{{ $attempt->user_agent }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">
                                <i class="ri-calendar-line me-2"></i>
                                تاريخ المحاولة
                            </th>
                            <td>{{ $attempt->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
