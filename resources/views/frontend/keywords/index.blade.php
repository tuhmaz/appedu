@extends('layouts.layoutFront')

@section('title', 'All Keywords')

@section('page-style')
@vite('resources/assets/vendor/scss/pages/front-page-help-center.scss') <!-- الاعتماد على نفس ملف التنسيق -->
@endsection

@section('content')
<div class="container mt-4">
    <!-- Hero Section -->
    <section class="section-py first-section-pt help-center-header position-relative overflow-hidden" style="background-color: rgb(32, 44, 69); padding-bottom: 20px;">
        <h1 class="text-center text-white fw-semibold">All Keywords</h1> <!-- H1 لسحابة الكلمات -->
        <p class="text-center text-white px-4 mb-0">Explore all available keywords</p>
    </section>

    <!-- Breadcrumb -->
    <div class="container px-4 mt-4">
        <ol class="breadcrumb breadcrumb-style2" aria-label="breadcrumbs">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">
                    <i class="ti ti-home-check"></i>{{ __('Home') }}
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Keywords</li>
        </ol>
        <div class="progress mt-2">
            <div class="progress-bar" role="progressbar" style="width: 100%;"></div>
        </div>
    </div>

    <!-- Keywords Cloud -->
    <section class="section-py bg-body first-section-pt" style="padding-top: 10px;">
        <div class="container">
            <div class="card px-3 mt-6">
                <div class="row">
                    <div class="content-header text-center bg-primary py-3">
                        <h2 class="text-white">Keywords Cloud</h2> <!-- H2 للكلمات -->
                    </div>

                    <div class="content-body text-center mt-3">
                        @if($keywords->count())
                            <div class="keywords-cloud">
                                @foreach($keywords as $keyword)
                                    <a href="{{ route('articles.indexByKeyword', ['keywords' => $keyword->keyword]) }}" class="keyword-item">
                                        {{ $keyword->keyword }}
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p>No keywords found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
