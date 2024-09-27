<?php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
?>
@extends('layouts/layoutMaster')

 
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
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-pencil-alt"></i> {{ __('edit_news') }}
                </h5>
                <a href="{{ route('news.index', ['country' => request('country', 'jordan')]) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-arrow-left"></i> {{ __('back_to_list') }}
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

                <form action="{{ route('news.update', ['news' => $news->id, 'country' => request('country', 'jordan')]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

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
                        <input type="text" name="title" class="form-control" placeholder="{{ __('title') }}" value="{{ $news->title }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">{{ __('Description') }}</label>
                        <textarea id="summernote" name="description" class="form-control" placeholder="{{ __('description') }}" required>{{ $news->description }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">{{ __('Category') }}</label>
                        <select name="category" class="form-control" required>
                            <option value="extra_teachers" {{ $news->category == 'extra_teachers' ? 'selected' : '' }}>{{ __('Extra Teachers') }}</option>
                            <option value="ministry_news" {{ $news->category == 'ministry_news' ? 'selected' : '' }}>{{ __('Ministry News') }}</option>
                            <option value="school_management" {{ $news->category == 'school_management' ? 'selected' : '' }}>{{ __('School Management') }}</option>
                            <option value="school_broadcast" {{ $news->category == 'school_broadcast' ? 'selected' : '' }}>{{ __('School Broadcast') }}</option>
                            <option value="evaluation_tools" {{ $news->category == 'evaluation_tools' ? 'selected' : '' }}>{{ __('Evaluation Tools') }}</option>
                            <option value="general_information" {{ $news->category == 'general_information' ? 'selected' : '' }}>{{ __('General Information') }}</option>
                            <option value="administrative_plans" {{ $news->category == 'administrative_plans' ? 'selected' : '' }}>{{ __('Administrative Plans') }}</option>
                            <option value="miscellaneous" {{ $news->category == 'miscellaneous' ? 'selected' : '' }}>{{ __('Miscellaneous') }}</option>
                            <option value="teacher_promotions" {{ $news->category == 'teacher_promotions' ? 'selected' : '' }}>{{ __('Teacher Promotions') }}</option>
                            <option value="achievement_files" {{ $news->category == 'achievement_files' ? 'selected' : '' }}>{{ __('Achievement Files') }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="meta_description" class="form-label">{{ __('Meta Description') }}</label>
                        <input type="text" name="meta_description" class="form-control" id="meta_description" placeholder="{{ __('meta_description') }}" value="{{ $news->meta_description }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="keywords" class="form-label">{{ __('Keywords') }}</label>
                        <input type="text" name="keywords" class="form-control" id="keywords" placeholder="{{ __('keywords') }}" value="{{ $news->keywords }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">{{ __('Image') }}</label>
                        <input type="file" name="image" class="form-control">
                        @if ($news->image != 'noimage.jpg')
                            <img src="{{ Storage::url('images/' . $news->image) }}" alt="{{ $news->alt }}" class="img-thumbnail mt-3" style="max-width: 200px;">
                        @endif
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('Update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
