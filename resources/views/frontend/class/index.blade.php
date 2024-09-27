@php
    $configData = Helper::appClasses();
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
    use Detection\MobileDetect;
    $detect = new MobileDetect;

    // Array of available colors
    $colors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'dark'];
    $colorCount = count($colors);

    // Icons based on grade_name
    $icons = [
        '1' => 'ti ti-number-0',
        '2' => 'ti ti-number-1',
        '3' => 'ti ti-number-2',
        '4' => 'ti ti-number-3',
        '5' => 'ti ti-number-4',
        '6' => 'ti ti-number-5',
        '7' => 'ti ti-number-6',
        '8' => 'ti ti-number-7',
        '9' => 'ti ti-number-8',
        '10' => 'ti ti-number-9',
        '11' => 'ti ti-number-10-small',
        '12' => 'ti ti-number-11-small',
        '13' => 'ti ti-number-12-small',
        'default' => 'ti ti-book',
    ];

    // Get the selected database from the session
    $database = session('database', 'mysql'); // Default to 'mysql' if not set
@endphp

@extends('layouts/layoutFront')

@section('title', __('Our Classes'))  <!-- Title Tag Added -->

@section('content')
<!-- Hero Section -->
<section class="section-py first-section-pt help-center-header position-relative overflow-hidden" style="background-color: rgb(32, 44, 69);">
    <div class="container">
        <h1 class="display-4 text-white">{{ __('welcome_school_classes') }}</h1>
        <p class="lead text-white">{{ __('explore_classes') }}</p>
    </div>
</section>

<!-- Breadcrumb -->
<div class="container px-4 mt-4">
    <ol class="breadcrumb breadcrumb-style2" aria-label="breadcrumbs">
        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="ti ti-home-check"></i>{{ __('Home') }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ __('Classes') }}</li>
    </ol>
    <div class="progress mt-2">
        <div class="progress-bar" role="progressbar" style="width: 50%;"></div>
    </div>
</div>

<!-- Google Ads - Top Ad (After Breadcrumb) -->
<div class="container mt-4 mb-4">
    @if(config('settings.google_ads_desktop_classes') || config('settings.google_ads_mobile_classes'))
        <div class="ads-container text-center">
            @if($detect->isMobile())
                {!! config('settings.google_ads_mobile_classes') !!}
            @else
                {!! config('settings.google_ads_desktop_classes') !!}
            @endif
        </div>
    @endif
</div>

<!-- Classes Section -->
<section class="section-classes text-center" id="classes-section">
    <div class="school-classes container py-5">
        <h2 class="text-center mb-4">{{ __('Our Classes') }}</h2>
        <div class="row">
            @forelse($classes as $index => $class)
                @php
                    // Assign icon based on grade_name or use default
                    $icon = $icons[$class->grade_level] ?? $icons['default'];

                    // Determine the display route
                    $routeName = request()->is('dashboard/*') ? 'dashboard.class.show' : 'frontend.class.show';

                    // Assign color based on index
                    $color = $colors[$index % $colorCount];
                @endphp

                <div class="col-xl-4 col-md-6 col-sm-12 mb-4">
                    <!-- Pass database as part of the route parameters -->
                    <a href="{{ route($routeName, ['id' => $class->id, 'database' => $database]) }}"
                       class="btn btn-outline-{{ $color }} btn-block d-flex align-items-center justify-content-center"
                       style="padding: 15px;">
                        <i class="badge bg-cyan text-cyan-fg {{ $icon }} me-2"></i>
                        {{ $class->grade_name }}
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-center">{{ __('No classes available.') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Google Ads - Bottom Ad -->
<div class="container mt-4 mb-4">
    @if(config('settings.google_ads_desktop_classes_2') || config('settings.google_ads_mobile_classes_2'))
        <div class="ads-container text-center">
            @if($detect->isMobile())
                {!! config('settings.google_ads_mobile_classes_2') !!}
            @else
                {!! config('settings.google_ads_desktop_classes_2') !!}
            @endif
        </div>
    @endif
</div>
@endsection
