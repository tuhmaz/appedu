@extends('layouts/layoutMaster')

@php
use Illuminate\Support\Str;
@endphp

@section('page-style')
    @vite(['resources/assets/vendor/scss/calendar.scss', 'resources/assets/vendor/js/calendar.js'])
@endsection

@section('content')
<div class="container mt-5">
    <div class="card">
        {{-- Header --}}
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="ti ti-calendar me-2"></i> {{ __('Calendar') }}
            </h5>
            <a href="#addEventModal" class="btn btn-success" data-bs-toggle="modal">
                <i class="ti ti-plus me-1"></i> {{ __('Add New Event') }}
            </a>
        </div>

        {{-- Calendar --}}
        <div class="card-body">
            <div class="row">
                <div class="col-md-7">
                    <h3 class="text-center mb-4">{{ now()->format('F Y') }}</h3>
                    <div class="calendar">
                        <div class="month-year text-center mb-3">
                            <span id="month">{{ now()->format('F') }}</span>
                            <span class="year">{{ now()->format('Y') }}</span>
                        </div>
                        <div class="days text-center">
                            {{-- Days of the week, starting with Saturday --}}
                            @foreach (['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                                <div class="day-label">{{ __($day) }}</div>
                            @endforeach

                            {{-- Days in the calendar --}}
                            @php
                                $startOfMonth = now()->startOfMonth();
                                $startOfWeek = $startOfMonth->copy()->startOfWeek(Carbon\Carbon::SATURDAY);
                                $endOfMonth = now()->endOfMonth();
                                $endOfWeek = $endOfMonth->copy()->endOfWeek(Carbon\Carbon::FRIDAY);
                                $currentDate = $startOfWeek->copy();
                            @endphp

                            @while ($currentDate <= $endOfWeek)
                                @php
                                    $hasEvent = $events->contains(fn($event) => $event->event_date == $currentDate->format('Y-m-d'));
                                    $eventDetails = $hasEvent ? $events->firstWhere('event_date', $currentDate->format('Y-m-d')) : null;
                                @endphp

                                <div class="day {{ $currentDate->isToday() ? 'today' : '' }} {{ $hasEvent ? 'has-event' : '' }}"
                                     @if ($hasEvent)
                                        data-bs-toggle="modal"
                                        data-bs-target="#eventModal"
                                        data-title="{{ $eventDetails->title }}"
                                        data-description="{{ $eventDetails->description }}"
                                        data-date="{{ $eventDetails->event_date }}"
                                     @endif>
                                    <div class="content">
                                        {{ $currentDate->day }}
                                    </div>
                                </div>

                                @php
                                    $currentDate->addDay();
                                @endphp
                            @endwhile
                        </div>
                    </div>
                </div>

                {{-- Add Event Form --}}
                <div class="col-md-5">
                    <form action="{{ route('events.store') }}" method="POST" class="card p-4 shadow-sm">
                        @csrf
                        <h5 class="mb-4">{{ __('Add New Event') }}</h5>
                        <div class="form-group mb-3">
                            <label for="title">{{ __('Event Title') }}</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description">{{ __('Description') }}</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label for="event_date">{{ __('Event Date') }}</label>
                            <input type="date" name="event_date" class="form-control" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="event_time">{{ __('Event Time') }}</label>
                            <input type="time" name="event_time" class="form-control">
                        </div>

                        <div class="form-group mb-3">
                            <label for="location">{{ __('Location') }}</label>
                            <input type="text" name="location" class="form-control">
                        </div>

                        <div class="form-group mb-3">
                            <label for="status">{{ __('Status') }}</label>
                            <select name="status" class="form-control">
                                <option value="upcoming">{{ __('Upcoming') }}</option>
                                <option value="ongoing">{{ __('Ongoing') }}</option>
                                <option value="completed">{{ __('Completed') }}</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">{{ __('Add Event') }}</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Events Table --}}
        <div class="card-footer">
            <h5>{{ __('Current Events') }}</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Description') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Time') }}</th>
                            <th>{{ __('Location') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($events as $event)
                        <tr>
                            <td>{{ $event->title }}</td>
                            <td>{{ Str::limit($event->description, 50) }}</td>
                            <td>{{ $event->event_date->format('Y-m-d') }}</td>
                            <td>{{ $event->event_time ? $event->event_time->format('H:i') : '-' }}</td>
                            <td>{{ $event->location ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $event->status === 'upcoming' ? 'primary' : ($event->status === 'ongoing' ? 'success' : 'secondary') }}">
                                    {{ __($event->status) }}
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editEventModal{{ $event->id }}">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <form action="{{ route('events.destroy', $event->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Are you sure?') }}')">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- Modal تحرير الحدث --}}
                        <div class="modal fade" id="editEventModal{{ $event->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ __('Edit Event') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('events.update', $event->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            
                                            <div class="form-group mb-3">
                                                <label>{{ __('Title') }}</label>
                                                <input type="text" name="title" class="form-control" value="{{ $event->title }}" required>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label>{{ __('Description') }}</label>
                                                <textarea name="description" class="form-control">{{ $event->description }}</textarea>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label>{{ __('Date') }}</label>
                                                <input type="date" name="event_date" class="form-control" value="{{ $event->event_date->format('Y-m-d') }}" required>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label>{{ __('Time') }}</label>
                                                <input type="time" name="event_time" class="form-control" value="{{ $event->event_time ? $event->event_time->format('H:i') : '' }}">
                                            </div>

                                            <div class="form-group mb-3">
                                                <label>{{ __('Location') }}</label>
                                                <input type="text" name="location" class="form-control" value="{{ $event->location }}">
                                            </div>

                                            <div class="form-group mb-3">
                                                <label>{{ __('Status') }}</label>
                                                <select name="status" class="form-control">
                                                    <option value="upcoming" {{ $event->status === 'upcoming' ? 'selected' : '' }}>{{ __('Upcoming') }}</option>
                                                    <option value="ongoing" {{ $event->status === 'ongoing' ? 'selected' : '' }}>{{ __('Ongoing') }}</option>
                                                    <option value="completed" {{ $event->status === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                                </select>
                                            </div>

                                            <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal إضافة حدث جديد --}}
<div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEventModalLabel">{{ __('Add New Event') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('events.store') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label>{{ __('Title') }}</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>{{ __('Description') }}</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label>{{ __('Date') }}</label>
                        <input type="date" name="event_date" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>{{ __('Time') }}</label>
                        <input type="time" name="event_time" class="form-control">
                    </div>

                    <div class="form-group mb-3">
                        <label>{{ __('Location') }}</label>
                        <input type="text" name="location" class="form-control">
                    </div>

                    <div class="form-group mb-3">
                        <label>{{ __('Status') }}</label>
                        <select name="status" class="form-control">
                            <option value="upcoming">{{ __('Upcoming') }}</option>
                            <option value="ongoing">{{ __('Ongoing') }}</option>
                            <option value="completed">{{ __('Completed') }}</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">{{ __('Add Event') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
