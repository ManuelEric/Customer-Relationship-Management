@extends('layout.main')

@section('title', 'Client Program - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('program/client') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Client Program
        </a>
    </div>


    <div class="row">
        <div class="col-md-4">
            <div class="card rounded mb-3">
                <div class="card-body text-center">
                    <h3><i class="bi bi-person"></i></h3>
                    <h4>Michael Nathan</h4>
                    @if (!request()->is('program/client/create*'))
                        <h6>Program Name</h6>
                        <div class="mt-3 d-flex justify-content-center">
                            <a href="{{ request()->is('program/client/1') ? url('program/client/1/edit') : url('program/client/1') }}"
                                type="button" class="btn btn-sm btn-outline-warning rounded mx-1">
                                <i
                                    class="bi {{ request()->is('program/client/1') ? 'bi-pencil' : 'bi-arrow-left' }} me-1"></i>
                                {{ request()->is('program/client/1') ? 'Edit' : 'Back' }}
                            </a>

                            <button type="button" class="btn btn-sm btn-outline-danger rounded mx-1">
                                <i class="bi bi-trash2"></i> Delete
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            @include('pages.program.client-program.detail.client')
            @include('pages.program.client-program.detail.plan-followup')
        </div>

        <div class="col-md-8">
            <div class="card rounded mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-tags me-2"></i>
                            Client Program Detail
                        </h6>
                    </div>
                </div>
                <div class="card-body">
                    <form action="">
                        @csrf
                        <div class="row mb-2">
                            <div class="col-md-3">
                                <label for="">
                                    Program Name <sup class="text-danger">*</sup>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <select name="" id="program_name" class="select w-100"
                                    onchange="changeProgramStatus()">
                                    <option data-placeholder="true"></option>
                                    <option value="Mentoring">Admissions Mentorig</option>
                                    <option value="Tutoring">Academic Tutoring</option>
                                    <option value="SAT">SAT</option>
                                    <option value="ACT">SAT</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-3">
                                <label for="">
                                    Conversion Lead <sup class="text-danger">*</sup>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small>Main Lead <sup class="text-danger">*</sup></small>
                                        <select name="" id="main_lead" class="select w-100"
                                            onchange="changeMainLead()">
                                            <option data-placeholder="true"></option>
                                            <option value="Event">Event</option>
                                            <option value="Edufair">Edufair</option>
                                            <option value="KOL">KOL</option>
                                            <option value="Others">Others</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 d-none" id="sub_lead">
                                        <small>Sub Lead <sup class="text-danger">*</sup></small>
                                        <select name="" id="" class="select w-100">
                                            <option data-placeholder="true"></option>
                                            <option value="Option 1">Option 1</option>
                                            <option value="Option 2">Option 2</option>
                                            <option value="Option 3">Option 3</option>
                                        </select>
                                    </div>
                                </div>
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
                                        <small>First Discuss <sup class="text-danger">*</sup></small>
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
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="">
                                    Program Status <sup class="text-danger">*</sup>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small>Status <sup class="text-danger">*</sup></small>
                                        <select name="" id="program_status" class="select w-100"
                                            onchange="changeProgramStatus()">
                                            <option data-placeholder="true"></option>
                                            <option value="Pending">Pending</option>
                                            <option value="Success">Success</option>
                                            <option value="Failed">Failed</option>
                                            <option value="Refund">Refund</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 program-detail d-none" id="success_date">
                                        <small>Success Date <sup class="text-danger">*</sup></small>
                                        <input type="date" name="" id=""
                                            class="form-control form-control-sm rounded">
                                    </div>
                                    <div class="col-md-6 program-detail d-none" id="failed_date">
                                        <small>Failed Date <sup class="text-danger">*</sup></small>
                                        <input type="date" name="" id=""
                                            class="form-control form-control-sm rounded">
                                    </div>
                                    <div class="col-md-6 program-detail d-none" id="refund_date">
                                        <small>Refund Date <sup class="text-danger">*</sup></small>
                                        <input type="date" name="" id=""
                                            class="form-control form-control-sm rounded">
                                    </div>


                                    <div class="col-md-12 mt-2 program-detail d-none" id="reason">
                                        <small>Reason <sup class="text-danger">*</sup></small>
                                        <input type="text" name="" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Program Detail  --}}
                        <div class="program-detail d-none" id="pending_mentoring">
                            @include('pages.program.client-program.form-detail.pending-mentoring')
                        </div>
                        <div class="program-detail d-none" id="success_mentoring">
                            @include('pages.program.client-program.form-detail.success-mentoring')
                        </div>
                        <div class="program-detail d-none" id="pending_tutoring">
                            @include('pages.program.client-program.form-detail.pending-tutoring')
                        </div>
                        <div class="program-detail d-none" id="success_tutoring">
                            @include('pages.program.client-program.form-detail.success-tutoring')
                        </div>
                        <div class="program-detail d-none" id="success_sat_act">
                            @include('pages.program.client-program.form-detail.success-sat-act')
                        </div>


                        <div class="row mb-3 program-detail d-none" id="running_status">
                            <div class="col-md-3">
                                <label for="">
                                    Running Status <sup class="text-danger">*</sup>
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
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="">
                                    PIC <sup class="text-danger">*</sup>
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
                            <button type="submit" class="btn btn-sm btn-primary rounded">
                                <i class="bi bi-save2 me-2"></i> Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @include('pages.program.client-program.detail.payment')
        </div>
    </div>


    <script>
        function changeMainLead() {
            let mainLead = $('#main_lead').val()
            if (mainLead == 'KOL' || mainLead == 'Edufair' || mainLead == 'Event') {
                $('#sub_lead').removeClass('d-none')
            } else {
                $('#sub_lead').addClass('d-none')
                $('#sub_lead select').val('').trigger('change')
            }
        }

        function changeProgramStatus() {
            let programName = $('#program_name').val()
            let programStatus = $('#program_status').val()
            $('.program-detail').addClass('d-none')

            if (programStatus == 'Pending') {
                if (programName == 'Mentoring') {
                    $('#pending_mentoring').removeClass('d-none')
                } else if (programName == 'Tutoring') {
                    $('#pending_tutoring').removeClass('d-none')
                }
            } else if (programStatus == 'Success') {
                $('#success_date').removeClass('d-none')
                $('#running_status').removeClass('d-none')
                if (programName == 'Mentoring') {
                    $('#success_mentoring').removeClass('d-none')
                } else if (programName == 'Tutoring') {
                    $('#success_tutoring').removeClass('d-none')
                } else if (programName == 'ACT') {
                    $('#success_sat_act').removeClass('d-none')
                } else if (programName == 'SAT') {
                    $('#success_sat_act').removeClass('d-none')
                }
            } else if (programStatus == 'Failed') {
                $('#failed_date').removeClass('d-none')
                $('#reason').removeClass('d-none')
            } else if (programStatus == 'Refund') {
                $('#refund_date').removeClass('d-none')
                $('#reason').removeClass('d-none')
            }
        }
    </script>
@endsection
