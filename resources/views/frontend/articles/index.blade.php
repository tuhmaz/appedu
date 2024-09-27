@php
    $configData = Helper::appClasses();
    use Detection\MobileDetect;
    $detect = new MobileDetect;
    $database = session('database', 'mysql');
    $subjectData = \App\Models\Subject::on($database)->find($subject->id);
    $url = url()->current();
    $class = $detect->isMobile() ? 'navbar-mobile' : '';

    $pageTitle = $subjectData->subject_name . ' - ' . $subjectData->schoolClass->grade_name;

@endphp

@extends('layouts/layoutFront')

@section('title', $pageTitle) 


@section('content')

<section class="section-py first-section-pt help-center-header position-relative overflow-hidden" style="background-color: rgb(32, 44, 69); padding-bottom: 20px;">
    <h4 class="text-center text-white fw-semibold">{{ $subjectData->subject_name }}</h4>
    <p class="text-center text-white px-4 mb-0">{{ $subjectData->subject_name }} - {{ $semester->semester_name }}</p>
</section>

<!-- Breadcrumb -->
<div class="container px-4 mt-4">
    <ol class="breadcrumb breadcrumb-style2" aria-label="breadcrumbs">
        <li class="breadcrumb-item">
            <a href="{{ route('home') }}">
                <i class="ti ti-home-check"></i>{{ __('Home') }}
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('class.index') }}">
                {{ __('Classes') }}
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('frontend.class.show', ['id' => $subject->schoolClass->id]) }}">
                {{ $subject->schoolClass->grade_name }}
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('frontend.subjects.show', ['subject' => $subject->id]) }}">
                {{ $subject->subject_name }}
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            {{ __($category) }} - {{ $semester->semester_name }}
        </li>
    </ol>
    <div class="progress mt-2">
        <div class="progress-bar" role="progressbar" style="width: 75%;"></div>
    </div>
</div>

<!-- Google Ads - Top Ad (After Hero Section) -->
<div class="container mt-4 mb-4">
    @if(config('settings.google_ads_desktop_article') || config('settings.google_ads_mobile_article'))
    <div class="ads-container text-center">
        @if($detect->isMobile())
        {!! config('settings.google_ads_mobile_article') !!}
        @else
        {!! config('settings.google_ads_desktop_article') !!}
        @endif
    </div>
    @endif
</div>

<!-- Content Section -->
<section class="section-py bg-body first-section-pt " style="padding-top: 10px;">
    <div class="container">
        <div class="card px-3 mt-6">
            <div class="row">
                <div class="content-header text-white text-center bg-primary py-4">
                    <h3 class="text-white">
                        @switch($category)
                            @case('plans')
                                {{ __('study_plans') }}
                                @break
                            @case('papers')
                                {{ __('worksheets') }}
                                @break
                            @case('tests')
                                {{ __('tests') }}
                                @break
                            @case('books')
                                {{ __('school_books') }}
                                @break
                            @default
                                {{ __('articles') }}
                        @endswitch - {{ $subjectData->subject_name }}
                    </h3>
                </div>

                <!-- Article Content -->
                <div class="content-body text-center">
                    @forelse ($articles as $article)
                    @php
                        $file = $article->files->first();
                        $fileType = $file ? $file->file_type : 'default';

                        // Get the file type and corresponding text and image
                        $fileTypeText = match($fileType) {
                            'pdf' => 'PDF',
                            'doc', 'docx' => 'Word Document',
                            'xls', 'xlsx' => 'Excel Spreadsheet',
                            default => 'Unknown',
                        };

                        $imagePath = match($fileType) {
                            'pdf' => asset('assets/img/icon/pdf-icon.png'),
                            'doc', 'docx' => asset('assets/img/icon/word-icon.png'),
                            'xls', 'xlsx' => asset('assets/img/icon/excel-icon.png'),
                            default => asset('assets/img/icon/default-icon.png'),
                        };
                    @endphp

                    <div class="list-group mb-3">
                        <a href="{{ route('frontend.articles.show', ['article' => $article->id]) }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <img src="{{ $imagePath }}" class="rounded-circle me-4 w-px-50 img-fluid" alt="File Icon">
                            <div class="w-100 d-flex justify-content-between">
                                <div class="file-info">
                                    <h6 class="mb-1">{{ $article->title }}</h6>
                                </div>
                            </div>
                        </a>
                    </div>
                    @empty
                    <p>{{ __('no_articles_available') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Google Ads - Bottom Ad (After Article List) -->
        <div class="container mt-4 mb-4">
            @if(config('settings.google_ads_desktop_article_2') || config('settings.google_ads_mobile_article_2'))
            <div class="ads-container text-center">
                @if($detect->isMobile())
                {!! config('settings.google_ads_mobile_article_2') !!}
                @else
                {!! config('settings.google_ads_desktop_article_2') !!}
                @endif
            </div>
            @endif
        </div>

        <!-- Social Media Icons -->
        <div class="content-footer text-center py-4 bg-light mt-4">
            <div class="social-icons">
                <a href="{{ config('settings.facebook') }}" class="me-2"><i class="ti ti-brand-facebook"></i></a>
                <a href="{{ config('settings.twitter') }}" class="me-2"><i class="ti ti-brand-twitter"></i></a>
                <a href="{{ config('settings.tiktok') }}" class="me-2"><i class="ti ti-brand-tiktok"></i></a>
                <a href="{{ config('settings.linkedin') }}" class="me-2"><i class="ti ti-brand-linkedin"></i></a>
                <a href="{{ config('settings.whatsapp') }}"><i class="ti ti-brand-whatsapp"></i></a>
            </div>
        </div>
    </div>
</section>

@endsection
