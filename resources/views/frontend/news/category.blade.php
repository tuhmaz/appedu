@php
$configData = Helper::appClasses();
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Detection\MobileDetect;
$detect = new MobileDetect;
@endphp


@extends('layouts/layoutFront')

@section('title', __('news_category_title') . $category)

@section('content')
<section class="section-py first-section-pt help-center-header position-relative overflow-hidden" style="background-color: rgb(32, 44, 69);">
</section>

<div class="container px-4 mt-4">
  <ol class="breadcrumb breadcrumb-style2" aria-label="breadcrumbs">
    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="ti ti-home-check"></i>{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('frontend.news.index') }}">{{ __('news') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __($category) }}</li>
  </ol>
  <div class="progress mt-2">
    <div class="progress-bar" role="progressbar" style="width: 50%;"></div>
  </div>
</div>

<!-- Google Ads - Top Ad (After Hero Section) -->
<div class="container mt-4 mb-4">
  @if(config('settings.google_ads_desktop_news_2') || config('settings.google_ads_mobile_news_2'))
  <div class="ads-container text-center">
    @if($detect->isMobile())
    {!! config('settings.google_ads_mobile_news_2') !!}
    @else
    {!! config('settings.google_ads_desktop_news_2') !!}
    @endif
  </div>
  @endif
</div>

<!-- News Category Section -->
<section class="section-py bg-body first-section-pt">
  <div class="container">
    <h4 class="text-center">{{ __('category') }}: {{ __($category) }}</h4>
    <div class="row">
      @foreach($news as $index => $newsItem)
      <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 shadow-sm d-flex flex-column">
          <img src="{{ asset('storage/images/' . $newsItem->image) }}" class="card-img-top img-fluid" alt="{{ $newsItem->alt }}" style="height: 200px; object-fit: cover;">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ $newsItem->title }}</h5>
            <p class="card-text">{{ Str::limit(strip_tags($newsItem->description), 100) }}</p>
            <div class="mt-auto">
              <a href="{{ route('frontend.news.show', $newsItem->id) }}" class="btn btn-primary btn-sm">{{ __('read_more') }}</a>
            </div>
          </div>
          <div class="card-footer text-muted">
            {{ __('published_on') }} {{ $newsItem->created_at->format('d M Y') }}
          </div>
        </div>
      </div>

      <!-- Google Ads - Inline Ad after every 3rd news item -->
      @if(($index + 1) % 3 == 0 && $index + 1 != count($news))
      <div class="container mt-4 mb-4">
        @if(config('settings.google_ads_desktop_news') || config('settings.google_ads_mobile_news'))
        <div class="ads-container text-center">
          @if($detect->isMobile())
          {!! config('settings.google_ads_mobile_news') !!}
          @else
          {!! config('settings.google_ads_desktop_news') !!}
          @endif
        </div>
        @endif
      </div>
      @endif
      @endforeach
    </div>
    <div class="d-flex justify-content-center">
      {{ $news->links() }}
    </div>
  </div>
</section>
@endsection
