@php
$configData = Helper::appClasses();
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

@endphp

@extends('layouts/layoutFront')


@section('title', 'Landing - Front Pages')


@section('vendor-style')
@vite([
'resources/assets/vendor/libs/nouislider/nouislider.scss',
'resources/assets/vendor/libs/swiper/swiper.scss'
])
@endsection


@section('page-style')
@vite(['resources/assets/vendor/scss/pages/front-page-landing.scss'])
@endsection


@section('vendor-script')
@vite([
'resources/assets/vendor/libs/nouislider/nouislider.js',
'resources/assets/vendor/libs/swiper/swiper.js'
])
@endsection
@section('page-script')
@vite(['resources/assets/js/front-page-landing.js','resources/js/filterhome.js'])
@endsection
@section('content')
<section class="section-py first-section-pt help-center-header position-relative overflow-hidden" style="background-color: rgb(32, 44, 69); padding-bottom: 20px;">
  <div class="container mt-12">
    @guest
    <h1 class="display-4 text-white text-center">{{ __('welcome') }} {{ config('settings.site_name') }}</h1>
    <p class="lead text-white text-center">{{ __('explore_classes') }}</p>
    @endguest
  </div>
</section>
<div class="container px-4 mt-4">
  <ol class="breadcrumb breadcrumb-style2" aria-label="breadcrumbs">
    <li class="breadcrumb-item">
      <a href="{{ route('home') }}">
        <i class="ti ti-home-check"></i>
        {{ __('Home') }}
      </a>
    </li>
  </ol>
  <div class="progress mt-2">
    <div class="progress-bar" role="progressbar" style="width: 25%;"></div>
  </div>
</div>
<div class="school-classes container py-5">
  <div class="row">
    <div class="col-md-7 mb-4">
      <h2 class="text-center mb-4">{{ __('Our Classes') }}</h2>
      <div class="row">
        @forelse($classes as $index => $class)
        @php
        $icon = $icons[$class->grade_level] ?? $icons['default'];
        $routeName = request()->is('dashboard/*') ? 'dashboard.class.show' : 'frontend.class.show';
        $color = $colors[$index % $colorCount];
        $database = session('database', 'mysql');
        @endphp
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-4">
          <a href="{{ route($routeName, ['database' => $database,'id' => $class->id]) }}"
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
    <div class="col-md-5 mb-4">
      <h3 class="text-center mb-4">{{ __('Quick search') }}</h3>
      <form id="filter-form" method="GET" action="{{ route('files.filter') }}">
        <input type="hidden" name="database" value="{{ $database ?? session('database', 'default_database') }}">
        <div class="row mb-4">
          <div class="form-group col-md-12">
            <label for="class-select">{{ __('Select Class') }}</label>
            <select id="class-select" name="class_id" class="form-control">
              <option value="">{{ __('Select Class') }}</option>
              @foreach($classes as $class)
              <option value="{{ $class->id }}">{{ $class->grade_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group col-md-12">
            <label for="subject-select">{{ __('Select Subject') }}</label>
            <select id="subject-select" name="subject_id" class="form-control" disabled>
              <option value="">{{ __('Select Subject') }}</option>
            </select>
          </div>
          <div class="form-group col-md-12">
            <label for="semester-select">{{ __('Select Semester') }}</label>
            <select id="semester-select" name="semester_id" class="form-control" disabled>
              <option value="">{{ __('Select Semester') }}</option>
            </select>
          </div>
          <div class="form-group col-md-12">
            <label for="file_category">{{ __('File Category') }}</label>
            <select class="form-control" id="file_category" name="file_category">
              <option value="">{{ __('Select Category') }}</option>
              <option value="plans">{{ __('Plans') }}</option>
              <option value="papers">{{ __('Papers') }}</option>
              <option value="tests">{{ __('Tests') }}</option>
              <option value="books">{{ __('Books') }}</option>
            </select>
          </div>
        </div>
        <div class="text-center">
          <button type="submit" class="btn btn-primary">{{ __('Filter Files') }}</button>
          <button type="reset" class="btn btn-secondary">{{ __('Reset') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

<section class="section-py bg-light" id="testimonials">
  <div class="container">
    <div class="row">
      <!-- العمود الأول -->
      <div class="col-md-6">
        <div class="category-section">
          @php
          // تقسيم الفئات إلى النصف الأول
          $firstHalfCategories = collect($categories)->slice(0, 5);
          @endphp

          @foreach($firstHalfCategories as $categoryKey => $categoryName)
            @php
            // تصفية الأخبار حسب الفئة
            $filteredNews = $news->filter(function($newsItem) use ($categoryKey) {
              return $newsItem->category === $categoryKey;
            });
            @endphp

            @if($filteredNews->isNotEmpty())
              <div class="category-section mb-5">
                <div class="card border-primary shadow-sm">
                  <div class="card-body">
                    <h5 class="card-title mb-4">{{ $categoryName }}</h5>
                    <ul class="list-group">
                      @foreach($filteredNews as $newsItem)
                        @php
                        $imagePath = $newsItem->image ? asset('storage/images/' . $newsItem->image) : asset('assets/img/pages/news-default.jpg');
                        @endphp

                        <li class="list-group-item d-flex align-items-center">
                          <img src="{{ $imagePath }}" class="img-thumbnail me-3" style="width: 80px;" alt="{{ $newsItem->title }}">
                          <div>
                            <h6 class="mb-0">{{ $newsItem->title }}</h6>
                            <small class="text-muted">{{ Str::limit($newsItem->description, 50) }}</small>
                          </div>
                          <a href="{{ route('frontend.news.show', ['id' => $newsItem->id]) }}" class="btn btn-sm btn-outline-primary ms-auto">{{ __('Read More') }}</a>
                        </li>
                      @endforeach
                    </ul>
                  </div>
                </div>
              </div>
            @else
              <p class="text-muted">{{ __('No news available for') }} {{ $categoryName }}.</p>
            @endif
          @endforeach
        </div>
      </div>

      <!-- العمود الثاني -->
      <div class="col-md-6">
        <div class="category-section">
          @php
          // تقسيم الفئات إلى النصف الثاني
          $secondHalfCategories = collect($categories)->slice(5);
          @endphp

          @foreach($secondHalfCategories as $categoryKey => $categoryName)
            @php
            // تصفية الأخبار حسب الفئة
            $filteredNews = $news->filter(function($newsItem) use ($categoryKey) {
              return $newsItem->category === $categoryKey;
            });
            @endphp

            @if($filteredNews->isNotEmpty())
              <div class="category-section mb-5">
                <div class="card border-primary shadow-sm">
                  <div class="card-body">
                    <h5 class="card-title mb-4">{{ $categoryName }}</h5>
                    <ul class="list-group">
                      @foreach($filteredNews as $newsItem)
                        @php
                        $imagePath = $newsItem->image ? asset('storage/images/' . $newsItem->image) : asset('assets/img/pages/news-default.jpg');
                        @endphp

                        <li class="list-group-item d-flex align-items-center">
                          <img src="{{ $imagePath }}" class="img-thumbnail me-3" style="width: 80px;" alt="{{ $newsItem->title }}">
                          <div>
                            <h6 class="mb-0">{{ $newsItem->title }}</h6>
                            <small class="text-muted">{{ Str::limit($newsItem->description, 50) }}</small>
                          </div>
                          <a href="{{ route('frontend.news.show', ['id' => $newsItem->id]) }}" class="btn btn-sm btn-outline-primary ms-auto">{{ __('Read More') }}</a>
                        </li>
                      @endforeach
                    </ul>
                  </div>
                </div>
              </div>
            @else
              <p class="text-muted">{{ __('No news available for') }} {{ $categoryName }}.</p>
            @endif
          @endforeach
        </div>
      </div>
    </div>
  </div>
</section>



@endsection
