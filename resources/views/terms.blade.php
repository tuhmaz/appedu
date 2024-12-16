@php
$configData = Helper::appClasses();
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutFront')

@section('title', 'شروط الخدمة')

@section('page-style')
{{-- Page Css files --}}
@vite('resources/assets/vendor/scss/pages/page-auth.scss')
@endsection

@section('content')
<section class="section-py first-section-pt help-center-header position-relative overflow-hidden" style="background-color: rgb(32, 44, 69); padding-bottom: 20px;">
  <h1 class="text-center text-white fw-semibold">شروط الخدمة</h1>
  <p class="text-center text-white px-4 mb-0">باستخدامك لخدماتنا، فإنك توافق على الشروط التالية</p>
</section>

<div class="container px-4 mt-4">
  <ol class="breadcrumb breadcrumb-style2" aria-label="breadcrumbs">
    <li class="breadcrumb-item">
      <a href="{{ route('home') }}">
        <i class="ti ti-home-check"></i>{{ __('home') }}
      </a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">شروط الخدمة</li>
  </ol>
  <div class="progress mt-2">
    <div class="progress-bar" role="progressbar" style="width: 100%;"></div>
  </div>
</div>

<div class="container mt-4">
  <div class="card">
    <div class="card-body">
      {!! $terms !!}
    </div>
  </div>

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
@endsection
