{{-- resources/views/frontend/keyword/keyword.blade.php --}}
@php
    $configData = Helper::appClasses();
    $pageTitle = "Articles related to: " . $keyword->keyword;
    use Illuminate\Support\Str;
@endphp


@extends('layouts/layoutFront')

@section('title', $pageTitle)

@section('page-style')
    @vite('resources/assets/vendor/scss/pages/front-page-help-center.scss')
@endsection

@section('meta')
    <!-- Meta Keywords -->
    <meta name="keywords" content="{{ $keyword->keyword }}">

    <!-- Meta Description -->
    <meta name="description" content="Find articles related to {{ $keyword->keyword }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph Tags -->
    <meta property="og:title" content="Articles related to {{ $keyword->keyword }}" />
    <meta property="og:description" content="Find articles related to {{ $keyword->keyword }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:image" content="{{ $article->image_url ?? asset('assets/img/front-pages/icons/articles_default_image.jpg') }}" />

    <!-- Twitter (X) Card Tags -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Articles related to {{ $keyword->keyword }}" />
    <meta name="twitter:description" content="Find articles related to {{ $keyword->keyword }}" />
    <meta name="twitter:image" content="{{ $article->image_url ?? asset('assets/img/front-pages/icons/articles_default_image.jpg') }}" />
@endsection

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

<!-- Hero Section -->
<section class="section-py first-section-pt help-center-header position-relative overflow-hidden" style="background-color: rgb(32, 44, 69); padding-bottom: 20px;">
    <h1 class="text-center text-white fw-semibold">{{ __('Articles Related to') }} {{ $keyword->keyword }}</h1>
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
            <a href="{{ route('frontend.keywords.index') }}">{{ __('Keywords') }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{ $keyword->keyword }}</li>
    </ol>
</div>

<div class="container mt-4">
    <!-- Displaying related articles -->
    @if($articles->isEmpty())
        <p class="text-center">{{ __('No articles found for this keyword.') }}</p>
    @else
        <div class="row">
            @foreach($articles as $article)
                <div class="col-md-4 mb-4">
                    <div class="card">
                    <img src="{{ $article->image_url ?? asset('assets/img/front-pages/icons/articles_default_image.jpg') }}" class="card-img-top" alt="{{ $article->title }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $article->title }}</h5>
                            <p class="card-text">{{ Str::limit(strip_tags($article->content), 100) }}</p>
                            <a href="{{ route('frontend.articles.show', ['article' => $article->id]) }}" class="btn btn-primary">{{ __('Read More') }}</a>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
