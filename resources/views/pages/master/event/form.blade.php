@extends('layout.main')

@section('title', 'Event - Bigdata Platform')

@section('content')

    @php
        $disabled = isset($event) && isset($edit) ? null : (isset($edit) ? null : 'disabled');
    @endphp

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('master/event') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Event
        </a>
    </div>

    {{-- @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="pb-0 mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif --}}


    <div class="row">
        <div class="col-md-4 text-center">
            <div class="card rounded mb-3">
                <div class="card-body">
                    <img src="{{ asset('img/program.webp') }}" alt="" class="w-75">
                    @if (isset($event) && !isset($true))
                        <div class="mt-3 d-flex justify-content-center">
                            <a href="{{ route('event.edit', ['event' => $event->event_id]) }}"
                                class="btn btn-sm btn-outline-info rounded mx-1">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger rounded mx-1"
                                onclick="confirmDelete('master/event', '{{ $event->event_id }}')">
                                <i class="bi bi-trash2"></i> Delete
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            @if (isset($event))
                @include('pages.master.event.detail.corporate')
                @include('pages.master.event.detail.univ')
                @include('pages.master.event.detail.school')
            @endif
        </div>
        <div class="col-md-8">
            <div class="card rounded mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-tags me-2"></i>
                            Event
                        </h6>
                    </div>
                </div>
                <div class="card-body">
                    <form
                        action="{{ isset($event) ? route('event.update', ['event' => $event->event_id]) : route('event.store') }}"
                        method="POST">
                        @csrf
                        @if (isset($event))
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <label>Event Name</label>
                                <input type="text" name="event_title"
                                    value="{{ isset($event->event_title) ? $event->event_title : null }}"
                                    class="form-control form-control-sm rounded" {{ $disabled }}>
                                @error('event_title')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-2">
                                <label>Description</label>
                                <textarea name="event_description" {{ $disabled }}>{{ isset($event->event_description) ? $event->event_description : null }}</textarea>
                                @error('event_description')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-2">
                                <label>Location</label>
                                <textarea name="event_location" {{ $disabled }}>{{ isset($event->event_location) ? $event->event_location : null }}</textarea>
                                @error('event_location')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Start Date </label>
                                <input type="datetime-local" name="event_startdate"
                                    value="{{ isset($event->event_startdate) ? $event->event_startdate : null }}"
                                    class="form-control form-control-sm rounded" {{ $disabled }}>
                                @error('event_startdate')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>End Date </label>
                                <input type="datetime-local" name="event_enddate"
                                    value="{{ isset($event->event_enddate) ? $event->event_enddate : null }}"
                                    class="form-control form-control-sm rounded" {{ $disabled }}>
                                @error('event_enddate')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-2">
                                <label>ALL-in PIC</label>
                                <select name="user_id[]" multiple class="select w-100" {{ $disabled }}>
                                    <option data-placeholder="true"></option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                            {{ isset($eventPic) ? (in_array($employee->id, $eventPic) ? 'selected' : null) : null }}>
                                            {{ $employee->first_name . ' ' . $employee->last_name }}</option>
                                    @endforeach
                                </select>
                                @error('event_enddate')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            @if (!$disabled)
                                <div class="mt-3 text-end">
                                    <button class="btn btn-sm btn-primary rounded" type="submit"><i
                                            class="bi bi-save2 me-1"></i> Submit</button>
                                </div>
                            @endif

                        </div>
                    </form>
                </div>
            </div>

            @if (isset($event))
                @include('pages.master.event.detail.speaker')
            @endif
        </div>
    </div>
    @if ($disabled)
        <script async defer>
            tinymce.init({
                selector: 'textarea',
                readonly: true,
                height: "250",
                menubar: false,
                // plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
                toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
            });
        </script>
    @endif
    <script>
        $(document).ready(function() {
            $('.modal-select-univ').select2({
                dropdownParent: $('#univ .modal-body'),
                placeholder: "Select value",
                allowClear: true
            });

            $('.modal-select-school').select2({
                dropdownParent: $('#school .modal-body'),
                placeholder: "Select value",
                allowClear: true
            });

            $('.modal-select-partner').select2({
                dropdownParent: $('#partner .modal-body'),
                placeholder: "Select value",
                allowClear: true
            });
        });
    </script>
@endsection
