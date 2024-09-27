@php
$configData = Helper::appClasses();
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
@endphp

@extends('layouts/layoutFront')

@section('title', __('news_title'))

@section('content')

<section class="section-py first-section-pt help-center-header position-relative overflow-hidden" style="background-color: rgb(32, 44, 69);">
</section>

<div class="container px-4 mt-2">
  <ol class="breadcrumb breadcrumb-style2 mx-auto" aria-label="breadcrumbs">
    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="ti ti-home-check"></i>{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('frontend.news.index') }}">{{ __('news') }}</a></li>
  </ol>
  <div class="progress mt-2 mx-auto">
    <div class="progress-bar" role="progressbar" style="width: 50%;"></div>
  </div>
</div>

<section class="section-py bg-body first-section-pt">
  <div class="container">
    <div class="row flex-column-reverse flex-lg-row">
      <!-- Sidebar for Categories -->
      <div class="col-lg-3 mb-4">
        <div class="list-group">
          <a href="#" class="list-group-item list-group-item-action active">{{ __('all_categories') }}</a>
          @foreach($categories as $category)
          <a href="{{ route('frontend.news.category', $category) }}" class="list-group-item list-group-item-action">{{ $category }}</a>
          @endforeach
        </div>
      </div>

      <!-- News Cards -->
      <div class="col-lg-9">
        <div class="row">
          @foreach($news as $newsItem)
          <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100 shadow-sm d-flex flex-column">
              <img src="{{ asset('storage/images/' . $newsItem->image) }}" class="card-img-top img-fluid" alt="{{ $newsItem->alt }}" style="height: 200px; object-fit: cover;">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title">{{ $newsItem->title }}</h5>
                <p class="card-text">{{ Str::limit(strip_tags($newsItem->description), 60) }}</p>
                <div class="mt-auto">
                  <a href="{{ route('frontend.news.show', $newsItem->id) }}" class="btn btn-primary btn-sm">{{ __('read_more') }}</a>
                </div>
              </div>
              <div class="card-footer text-muted">
                {{ __('published_on') }} {{ $newsItem->created_at->format('d M Y') }}
              </div>
            </div>
          </div>
          @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
          {{ $news->links() }}
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
