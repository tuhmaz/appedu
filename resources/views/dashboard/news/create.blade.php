@extends('layouts/layoutMaster')

@section('title', __('Create News'))
@section('page-script')
  @vite(['resources/assets/js/forms-editors.js'])
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="base-url" content="{{ url('/') }}">
<meta name="upload-url" content="{{ route('upload.image') }}">
<div class="content-body">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-light text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ri-pencil-line me-2"></i>{{ __('Create News') }}
                </h5>
                <a href="{{ route('news.index', ['country' => request('country', 'jordan')]) }}" class="btn btn-light btn-sm">
                    <i class="ri-arrow-go-back-line me-1"></i>{{ __('Back to List') }}
                </a>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('news.store', ['country' => request('country', 'jordan')]) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- حقل اختيار الدولة -->
                    <div class="mb-3">
                        <label for="country" class="form-label">{{ __('Country') }}</label>
                        <select name="country" class="form-control">
                            <option value="jordan" {{ request('country') == 'jordan' ? 'selected' : '' }}>Jordan</option>
                            <option value="saudi" {{ request('country') == 'saudi' ? 'selected' : '' }}>Saudi Arabia</option>
                            <option value="egypt" {{ request('country') == 'egypt' ? 'selected' : '' }}>Egypt</option>
                            <option value="palestine" {{ request('country') == 'palestine' ? 'selected' : '' }}>Palestine</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label">{{ __('Title') }}</label>
                        <input type="text" name="title" class="form-control" placeholder="{{ __('Title') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">{{ __('Description') }}</label>
                        <textarea id="summernote" name="description" rows="15" class="form-control"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">{{ __('Category') }}</label>
                        <select name="category" class="form-control" required>
                            <option value="extra_teachers">{{ __('Extra Teachers') }}</option>
                            <option value="ministry_news">{{ __('Ministry News') }}</option>
                            <option value="school_management">{{ __('School Management') }}</option>
                            <option value="school_broadcast">{{ __('School Broadcast') }}</option>
                            <option value="evaluation_tools">{{ __('Evaluation Tools') }}</option>
                            <option value="general_information">{{ __('General Information') }}</option>
                            <option value="administrative_plans">{{ __('Administrative Plans') }}</option>
                            <option value="miscellaneous">{{ __('Miscellaneous') }}</option>
                            <option value="teacher_promotions">{{ __('Teacher Promotions') }}</option>
                            <option value="achievement_files">{{ __('Achievement Files') }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">{{ __('Image') }}</label>
                        <input type="file" name="image" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="meta_description" class="form-label">{{ __('Meta Description') }}</label>
                        <input type="text" name="meta_description" class="form-control" id="meta_description" required>
                    </div>

                    <div class="mb-3">
                        <label for="keywords" class="form-label">{{ __('Keywords') }}</label>
                        <input type="text" name="keywords" class="form-control" id="keywords" required>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="ri-save-line me-1"></i>{{ __('Submit') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
