@extends('layout.main')

@section('title', 'Referral - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('instance/referral') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Referral
        </a>
    </div>


    <div class="row">
        <div class="col-md-4 text-center">
            <div class="card rounded mb-3">
                <div class="card-body">
                    <img src="{{ asset('img/program.webp') }}" alt="" class="w-75">
                    <div class="mt-3 d-flex justify-content-center">
                        <a href="#" class="btn btn-sm btn-outline-info rounded mx-1">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded mx-1">
                            <i class="bi bi-trash2"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card rounded mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-tags me-2"></i>
                            Referral
                        </h6>
                    </div>
                </div>
                <div class="card-body">
                    <form action="">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label>Type</label>
                                <select name="event_title" class="select w-100" id="type" onchange="checkType()">
                                    <option data-placeholder="true"></option>
                                    <option value="in">Referral In</option>
                                    <option value="out">Referral Out</option>
                                </select>
                                @error('event_title')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Partner Name</label>
                                <select name="event_title" class="select w-100">
                                    <option data-placeholder="true"></option>
                                </select>
                                @error('event_title')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Program Name</label>
                                <div id="selectProgram">
                                    <select name="event_title" class="select w-100">
                                        <option data-placeholder="true"></option>
                                    </select>
                                </div>
                                <div id="inputProgram" class="d-none">
                                    <input type="text" name="event_title" value=""
                                        class="form-control form-control-sm rounded">
                                </div>
                                @error('event_title')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Referral Date</label>
                                <input type="date" name="event_title" value=""
                                    class="form-control form-control-sm rounded">
                                @error('event_title')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Participant</label>
                                <input type="number" name="event_title" value=""
                                    class="form-control form-control-sm rounded">
                                @error('event_title')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Amount</label>
                                <input type="number" name="event_title" value=""
                                    class="form-control form-control-sm rounded">
                                @error('event_title')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-2">
                                <label>Notes</label>
                                <textarea name="event_description"></textarea>
                                @error('event_description')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-2">
                                <label>PIC </label>
                                <select name="type" class="select w-100">
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

    <script>
        function checkType() {
            let type = $('#type').val()
            if (type == 'in' || type == 'exs') {
                $('#selectProgram').removeClass('d-none')
                $('#inputProgram').addClass('d-none')
            } else {
                $('#inputProgram').removeClass('d-none')
                $('#selectProgram').addClass('d-none')
            }
        }
    </script>
@endsection
