@php
$configData = Helper::appClasses();
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
@endphp

@extends('layouts.layoutMaster')

@section('title', __('Dashboard Overview'))

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endsection

@section('content')
<div class="container-fluid p-4">
    <!-- Welcome Section -->
    <div class="card mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="avatar avatar-md bg-primary rounded-3">
                        <i class="ti ti-user-circle-filled ti-md text-white"></i>
                    </div>
                </div>
                <div class="col">
                    <h4 class="mb-1 fw-semibold">
                        {{ __('Welcome back') }}, {{ Auth::user()->name }}!
                    </h4>
                    <p class="mb-0 text-muted">
                        {{ __("Here's what's happening with your sites today") }}
                    </p>
                </div>
                <div class="col-12 col-md-auto mt-3 mt-md-0">
                    <span class="badge bg-label-primary">
                        <i class="ti ti-calendar-stats ti-md"></i>
                        {{ now()->format('F d, Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="avatar bg-label-primary rounded">
                            <i class="ti ti-users-group ti-md"></i>
                        </div>
                        <div class="badge bg-label-primary rounded-pill">{{ __('Users') }}</div>
                    </div>
                    <h4 class="mb-2">{{ number_format($usersCount) }}</h4>
                    <div class="d-flex align-items-center text-muted">
                        <i class="ti ti-chart-bar-filled me-2"></i>
                        <span>{{ __('Total Registered Users') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="avatar bg-label-success rounded">
                            <i class="ti ti-article-filled ti-md"></i>
                        </div>
                        <div class="badge bg-label-success rounded-pill">{{ __('Articles') }}</div>
                    </div>
                    <h4 class="mb-2">{{ number_format($articlesCount) }}</h4>
                    <div class="d-flex align-items-center text-muted">
                        <i class="ti ti-chart-bar-filled me-2"></i>
                        <span>{{ __('Published Articles') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="avatar bg-label-info rounded">
                            <i class="ti ti-news-filled ti-md"></i>
                        </div>
                        <div class="badge bg-label-info rounded-pill">{{ __('News') }}</div>
                    </div>
                    <h4 class="mb-2">{{ number_format($newsCount) }}</h4>
                    <div class="d-flex align-items-center text-muted">
                        <i class="ti ti-chart-bar-filled me-2"></i>
                        <span>{{ __('Published News') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="avatar bg-label-warning rounded">
                            <i class="ti ti-shield-check-filled ti-md"></i>
                        </div>
                        <div class="badge bg-label-warning rounded-pill">{{ __('Staff') }}</div>
                    </div>
                    <h4 class="mb-2">{{ number_format($adminsCount + $supervisorsCount) }}</h4>
                    <div class="d-flex align-items-center text-muted">
                        <i class="ti ti-chart-bar-filled me-2"></i>
                        <span>{{ __('Total Staff Members') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Country Statistics -->
    <div class="row g-4">
        @foreach (['saudi' => ['Saudi Arabia', 'success'], 'egypt' => ['Egypt', 'info'], 'palestine' => ['Palestine', 'primary']] as $key => $data)
        <div class="col-12 col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3 bg-label-{{ $data[1] }} rounded">
                            <i class="ti ti-map-pin-filled ti-md"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ __($data[0]) }}</h5>
                            <small class="text-muted">{{ __('Regional Statistics') }}</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar me-2 bg-label-{{ $data[1] }} rounded">
                                    <i class="ti ti-article-filled ti-sm"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ number_format($subdomainArticlesCount[$key]) }}</h6>
                                    <small class="text-muted">{{ __('Articles') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar me-2 bg-label-{{ $data[1] }} rounded">
                                    <i class="ti ti-news-filled ti-sm"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ number_format($subdomainNewsCount[$key]) }}</h6>
                                    <small class="text-muted">{{ __('News') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Latest Updates -->
                    <h6 class="mb-3">{{ __('Latest Updates') }}</h6>
                    <ul class="timeline mb-0">
                        @foreach($subdomainLatestArticles[$key]->take(3) as $article)
                        <li class="timeline-item pb-4 border-left-dashed">
                            <span class="timeline-indicator timeline-indicator-{{ $data[1] }}">
                                <i class="ti ti-article-filled"></i>
                            </span>
                            <div class="timeline-event">
                                <div class="timeline-header">
                                    <h6 class="mb-0">
                                        <a href="{{ route('articles.show', $article->id) }}" class="text-body">
                                            {{ Str::limit($article->title, 40) }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="ti ti-clock-filled me-1"></i>
                                        {{ $article->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </li>
                        @endforeach

                        @foreach($subdomainLatestNews[$key]->take(3) as $news)
                        <li class="timeline-item pb-4 border-left-dashed">
                            <span class="timeline-indicator timeline-indicator-{{ $data[1] }}">
                                <i class="ti ti-news-filled"></i>
                            </span>
                            <div class="timeline-event">
                                <div class="timeline-header">
                                    <h6 class="mb-0">
                                        <a href="{{ route('news.show', $news->id) }}" class="text-body">
                                            {{ Str::limit($news->title, 40) }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="ti ti-clock-filled me-1"></i>
                                        {{ $news->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Latest Activity -->
    <div class="row g-4 mt-4">
        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">
                            <i class="ti ti-article-filled me-2"></i>{{ __('Latest Articles') }}
                        </h5>
                        <a href="{{ route('articles.index') }}" class="btn btn-primary btn-sm">
                            <i class="ti ti-external-link me-1"></i>{{ __('View All') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="p-0 m-0">
                        @foreach($latestArticles as $article)
                        <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="ti ti-article-filled"></i>
                                </span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">
                                        <a href="{{ route('articles.show', $article->id) }}" class="text-body">
                                            {{ $article->title }}
                                        </a>
                                    </h6>
                                    <small class="text-muted d-block">
                                        <i class="ti ti-clock-filled me-1"></i>
                                        {{ $article->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="badge bg-label-primary rounded-pill">{{ $article->created_at->format('M d') }}</div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">
                            <i class="ti ti-news-filled me-2"></i>{{ __('Latest News') }}
                        </h5>
                        <a href="{{ route('news.index') }}" class="btn btn-primary btn-sm">
                            <i class="ti ti-external-link me-1"></i>{{ __('View All') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="p-0 m-0">
                        @foreach($latestNews as $news)
                        <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="ti ti-news-filled"></i>
                                </span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">
                                        <a href="{{ route('news.show', $news->id) }}" class="text-body">
                                            {{ $news->title }}
                                        </a>
                                    </h6>
                                    <small class="text-muted d-block">
                                        <i class="ti ti-clock-filled me-1"></i>
                                        {{ $news->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="badge bg-label-info rounded-pill">{{ $news->created_at->format('M d') }}</div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
