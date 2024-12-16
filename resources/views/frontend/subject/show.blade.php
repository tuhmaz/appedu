@php
$configData = Helper::appClasses();
use Detection\MobileDetect;

$detect = new MobileDetect;

@endphp

@extends('layouts/layoutFront')

@section('title', $subject->subject_name)

@section('content')

<section class="section-py first-section-pt help-center-header position-relative overflow-hidden" style="background-color: rgb(32, 44, 69); padding-bottom: 20px;">
  <h4 class="text-center text-white fw-semibold">{{ $subject->subject_name }}</h4>
  <p class="text-center text-white px-4 mb-0">{{ __('Choose Semester and Category') }}</p>
</section>

<div class="container px-4 mt-4">
  <ol class="breadcrumb breadcrumb-style2" aria-label="breadcrumbs">
    <li class="breadcrumb-item">
      <a href="{{ route('home') }}">
        <i class="ti ti-home-check"></i>{{ __('Home') }}
      </a>
    </li>
    <li class="breadcrumb-item">
      <a href="{{ route('class.index', ['database' => $database ?? session('database', 'default_database')]) }}">
        {{ __('Classes') }}
      </a>
    </li>
    <li class="breadcrumb-item">
      <a href="{{ route('frontend.class.show', ['database' => $database ?? session('database'),'id' => $subject->schoolClass->id]) }}">
        {{ $subject->schoolClass->grade_name }}
      </a>
    </li>
    <li class="breadcrumb-item active">{{ $subject->subject_name }}</li>
  </ol>
  <div class="progress mt-2">
    <div class="progress-bar" role="progressbar" style="width: 50%;"></div>
  </div>
</div>

<section class="section-py bg-body first-section-pt" style="padding-top: 10px;">
  <div class="container">
    @if(config('settings.google_ads_desktop_subject') || config('settings.google_ads_mobile_subject'))
      <div class="ads-container text-center mb-4">
        @if($detect->isMobile())
          {!! config('settings.google_ads_mobile_subject') !!}
        @else
          {!! config('settings.google_ads_desktop_subject') !!}
        @endif
      </div>
    @endif

    <!-- Semester One -->
    <div class="card px-3 mb-4">
      <div class="content-header text-white text-center bg-primary py-4">
        <h3 class="text-white mb-0">{{ __('Semester One') }} - {{ $subject->subject_name }}</h3>
      </div>
      <div class="content-body p-4">
        @foreach($semesters->where('semester_name', __('Semester One')) as $semester)
          <div class="d-grid gap-2 d-md-flex justify-content-center mb-3">
            <a href="{{ route('frontend.subject.articles', ['database' => $database ?? session('database'),'subject' => $subject->id, 'semester' => $semester->id, 'category' => 'plans']) }}" 
              class="btn btn-outline-primary btn-lg px-4">
              <i class="ti ti-book me-2"></i>{{ __('Study Plans') }}
            </a>
            <a href="{{ route('frontend.subject.articles', ['database' => $database ?? session('database'),'subject' => $subject->id,'semester' => $semester->id, 'category' => 'papers']) }}" 
              class="btn btn-outline-success btn-lg px-4">
              <i class="ti ti-file-text me-2"></i>{{ __('Worksheets') }}
            </a>
            <a href="{{ route('frontend.subject.articles', ['database' => $database ?? session('database'),'subject' => $subject->id,'semester' => $semester->id, 'category' => 'tests']) }}" 
              class="btn btn-outline-danger btn-lg px-4">
              <i class="ti ti-writing-sign me-2"></i>{{ __('Tests') }}
            </a>
            <a href="{{ route('frontend.subject.articles', ['database' => $database ?? session('database'),'subject' => $subject->id,'semester' => $semester->id, 'category' => 'books']) }}" 
              class="btn btn-outline-warning btn-lg px-4">
              <i class="ti ti-books me-2"></i>{{ __('School Books') }}
            </a>
          </div>
        @endforeach
      </div>
    </div>

    <!-- Semester Two -->
    <div class="card px-3">
      <div class="content-header text-white text-center bg-primary py-4">
        <h3 class="text-white mb-0">{{ __('Semester Two') }} - {{ $subject->subject_name }}</h3>
      </div>
      <div class="content-body p-4">
        @foreach($semesters->where('semester_name', __('Semester Two')) as $semester)
          <div class="d-grid gap-2 d-md-flex justify-content-center mb-3">
            <a href="{{ route('frontend.subject.articles', ['database' => $database ?? session('database'),'subject' => $subject->id,'semester' => $semester->id, 'category' => 'plans']) }}" 
              class="btn btn-outline-primary btn-lg px-4">
              <i class="ti ti-book me-2"></i>{{ __('Study Plans') }}
            </a>
            <a href="{{ route('frontend.subject.articles', ['database' => $database ?? session('database'),'subject' => $subject->id,'semester' => $semester->id, 'category' => 'papers']) }}" 
              class="btn btn-outline-success btn-lg px-4">
              <i class="ti ti-file-text me-2"></i>{{ __('Worksheets') }}
            </a>
            <a href="{{ route('frontend.subject.articles', ['database' => $database ?? session('database'),'subject' => $subject->id,'semester' => $semester->id, 'category' => 'tests']) }}" 
              class="btn btn-outline-danger btn-lg px-4">
              <i class="ti ti-writing-sign me-2"></i>{{ __('Tests') }}
            </a>
            <a href="{{ route('frontend.subject.articles', ['database' => $database ?? session('database'),'subject' => $subject->id,'semester' => $semester->id, 'category' => 'books']) }}" 
              class="btn btn-outline-warning btn-lg px-4">
              <i class="ti ti-books me-2"></i>{{ __('School Books') }}
            </a>
          </div>
        @endforeach
      </div>
    </div>

    @if(config('settings.google_ads_desktop_subject_2') || config('settings.google_ads_mobile_subject_2'))
      <div class="ads-container text-center mt-4">
        @if($detect->isMobile())
          {!! config('settings.google_ads_mobile_subject_2') !!}
        @else
          {!! config('settings.google_ads_desktop_subject_2') !!}
        @endif
      </div>
    @endif

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
