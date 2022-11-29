@extends('layout.main')

@section('title', 'School Program - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('program/School') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> School Program
        </a>
    </div>


    <div class="row">
        <div class="col-md-4">
            <div class="card rounded mb-3">
                <div class="card-body text-center">
                    <h4>Michael Nathan</h4>
                    <h6>Program Name</h6>
                    <div class="mt-3 d-flex justify-content-center">
                        <button type="button" class="btn btn-sm btn-outline-danger rounded mx-1">
                            <i class="bi bi-trash2"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
            @include('pages.program.school-program.detail.school')
            @include('pages.program.school-program.detail.speaker')
        </div>

        <div class="col-md-8">
            <div class="card rounded mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-tags me-2"></i>
                            School Program Detail
                        </h6>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="">
                                Program Name <sup class="text-danger">*</sup>
                            </label>
                        </div>
                        <div class="col-md-9">
                            <select name="" id="" class="select w-100">
                                <option data-placeholder="true"></option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="">
                                Date <sup class="text-danger">*</sup>
                            </label>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <small>First Discuss</small>
                                    <input type="date" name="" id=""
                                        class="form-control form-control-sm rounded">
                                </div>
                                <div class="col-md-6">
                                    <small>Last Discuss</small>
                                    <input type="date" name="" id=""
                                        class="form-control form-control-sm rounded">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="">
                                Notes
                            </label>
                        </div>
                        <div class="col-md-9">
                            <textarea name="" id="" class="w-100"></textarea>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label for="">
                                Approach Status
                            </label>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <small>Status</small>
                                    <select name="" id="approach_status" class="select w-100"
                                        onchange="checkStatus()">
                                        <option data-placeholder="true"></option>
                                        <option value="Pending">Pending</option>
                                        <option value="Success">Success</option>
                                        <option value="Denied">Denied</option>
                                    </select>
                                </div>
                                <div class="col-md-6 success_status d-none">
                                    <small>Success Date</small>
                                    <input type="date" name="" id=""
                                        class="form-control form-control-sm rounded">
                                </div>
                                <div class="col-md-6 denied_status d-none">
                                    <small>Denied Date</small>
                                    <input type="date" name="" id=""
                                        class="form-control form-control-sm rounded">
                                </div>
                                <div class="col-md-6 denied_status d-none my-2">
                                    <small>Reason</small>
                                    <input type="date" name="" id=""
                                        class="form-control form-control-sm rounded">
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Program Detail  --}}
                    <div class="row mb-3 success_status d-none">
                        <div class="col-md-3">
                            <label for="">
                                Program Detail
                            </label>
                        </div>
                        <div class="col-md-9">
                            {{-- Admissions Program  --}}
                            <div class="card">
                                <div class="card-header">
                                    School Program
                                </div>
                                <div class="card-body">
                                    {{-- if success  --}}
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <small>Start Program Date</small>
                                            <input type="date" name="" id=""
                                                class="form-control form-control-sm rounded">
                                        </div>
                                        <div class="col-md-6">
                                            <small>End Program Date</small>
                                            <input type="date" name="" id=""
                                                class="form-control form-control-sm rounded">
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-12 mb-2">
                                            <small>Place</small>
                                            <input type="text" name="" id=""
                                                class="form-control form-control-sm rounded">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small>Participants</small>
                                            <input type="number" name="" id=""
                                                class="form-control form-control-sm rounded">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small>Total Fee</small>
                                            <input type="number" name="" id=""
                                                class="form-control form-control-sm rounded">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small>Total Hours</small>
                                            <input type="number" name="" id=""
                                                class="form-control form-control-sm rounded">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small>Running Status</small>
                                            <select name="" id="" class="select w-100">
                                                <option data-placeholder="true"></option>
                                            </select>
                                        </div>
                                        <div class="col-md-12 mb-2">
                                            <small>Notes</small>
                                            <textarea name="" id=""></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label for="">
                                PIC
                            </label>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <select name="" id="" class="select w-100">
                                        <option data-placeholder="true"></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="mt-3 text-end">
                        <button class="btn btn-sm btn-primary rounded">
                            <i class="bi bi-save2 me-2"></i> Submit
                        </button>
                    </div>
                </div>
            </div>

            @include('pages.program.school-program.detail.attachment')
        </div>
    </div>


    <script>
        function checkStatus() {
            let status = $('#approach_status').val();
            if (status == 'Pending') {
                $('.denied_status').addClass('d-none')
                $('.success_status').addClass('d-none')
            } else if (status == 'Success') {
                $('.denied_status').addClass('d-none')
                $('.success_status').removeClass('d-none')
            } else if (status == 'Denied') {
                $('.denied_status').removeClass('d-none')
                $('.success_status').addClass('d-none')
            }
        }
    </script>
@endsection
