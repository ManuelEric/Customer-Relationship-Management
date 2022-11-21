@extends('layout.main')

@section('title', 'Event - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('master/event') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Event
        </a>
    </div>


    <div class="row">
        <div class="col-md-4 text-center">
            <div class="card rounded mb-3">
                <div class="card-body">
                    <img src="{{ asset('img/program.webp') }}" alt="" class="w-75">
                </div>
            </div>

            @include('pages.master.event.detail.corporate')
            @include('pages.master.event.detail.univ')
            @include('pages.master.event.detail.school')
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
                    <form action="">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <label>Event Name</label>
                                <input type="text" name="event_title" value=""
                                    class="form-control form-control-sm rounded">
                                @error('event_title')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-2">
                                <label>Description</label>
                                <textarea name="event_description"></textarea>
                                @error('event_description')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-2">
                                <label>Location</label>
                                <textarea name="event_location"></textarea>
                                @error('event_location')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Start Date </label>
                                <input type="date" name="event_startdate" value=""
                                    class="form-control form-control-sm rounded">
                                @error('event_startdate')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>End Date </label>
                                <input type="date" name="event_enddate" value=""
                                    class="form-control form-control-sm rounded">
                                @error('event_enddate')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-2">
                                <label>ALL-in PIC </label>
                                <select name="user_id" multiple class="select w-100">
                                    <option data-placeholder="true"></option>
                                </select>
                                @error('event_enddate')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mt-3 text-end">
                                <button class="btn btn-sm btn-primary rounded" type="submit"><i
                                        class="bi bi-save2 me-1"></i> Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
