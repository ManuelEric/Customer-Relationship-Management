@extends('layout.main')

@section('title', 'Client Event - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('program/event') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Client Event
        </a>
    </div>


    <div class="row">
        <div class="col-md-4 text-center">
            <div class="card rounded mb-3">
                <div class="card-body">
                    <img src="{{ asset('img/program.webp') }}" alt="" class="w-75">
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card rounded mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-tags me-2"></i>
                            Client Event
                        </h6>
                    </div>
                </div>
                <div class="card-body">
                    <form action="">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label>Existing Client <sup class="text-danger">*</sup></label>
                                <div class="d-flex align-items-center">
                                    <div class="form-check ms-4">
                                        <input class="form-check-input exist" type="radio" name="existing_client"
                                            id="exist1" value="1" checked onchange="checkExist(this)">
                                        <label class="" for="exist1">
                                            Yes
                                        </label>
                                    </div>
                                    <div class="form-check ms-5">
                                        <input class="form-check-input exist" type="radio" name="existing_client"
                                            id="exist2" value="0" onchange="checkExist(this)">
                                        <label class="" for="exist2">
                                            No
                                        </label>
                                    </div>
                                </div>
                                @error('event_title')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-8 mb-2 client" id="existing_client">
                                <label>Client Name <sup class="text-danger">*</sup></label>
                                <select name="event_title" class="select w-100">
                                    <option data-placeholder="true"></option>
                                </select>
                                @error('event_title')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3 d-none client" id="new_client">
                                <div class="card">
                                    <div class="card-header">
                                        Client Detail
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <label>First Name <sup class="text-danger">*</sup></label>
                                                <input type="text" name=""
                                                    class="form-control form-control-sm rounded">
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label>Last Name <sup class="text-danger">*</sup></label>
                                                <input type="text" name=""
                                                    class="form-control form-control-sm rounded">
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label>Email <sup class="text-danger">*</sup></label>
                                                <input type="email" name=""
                                                    class="form-control form-control-sm rounded">
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label>Phone Number <sup class="text-danger">*</sup></label>
                                                <input type="email" name=""
                                                    class="form-control form-control-sm rounded">
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label>Status <sup class="text-danger">*</sup></label>
                                                <select name="" id="" class="select w-100">
                                                    <option data-placeholder="true"></option>
                                                    <option value="Mentee">Mentee</option>
                                                    <option value="Parent">Parent</option>
                                                    <option value="Teacher/Counsellor">Teacher/Counsellor</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6 mb-2">
                                <label>Event Name <sup class="text-danger">*</sup></label>
                                <select name="event_title" class="select w-100">
                                    <option data-placeholder="true"></option>
                                </select>
                                @error('event_title')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Joined Date <sup class="text-danger">*</sup></label>
                                <input type="date" name="event_title" value=""
                                    class="form-control form-control-sm rounded">
                                @error('event_title')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Conversion Lead <sup class="text-danger">*</sup></label>
                                <select name="" id="" class="select w-100">
                                    <option data-placeholder="true"></option>
                                </select>
                                @error('event_title')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Status <sup class="text-danger">*</sup></label>
                                <select name="" id="" class="select w-100">
                                    <option value="0">Join</option>
                                    <option value="1">Attend</option>
                                </select>
                                @error('event_title')
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

    <script>
        function checkExist(radio) {
            let exist = radio.value
            $('.client').addClass('d-none')
            if (exist == 1) {
                $('#existing_client').removeClass('d-none')
            } else {
                $('#new_client').removeClass('d-none')
            }
        }
    </script>
@endsection
