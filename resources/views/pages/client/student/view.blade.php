@extends('layout.main')
<style>
    .lcs_wrap {
        scale: 0.7;
        margin-top: -4px;
        margin-left: -10px;
    }
</style>

@section('title', 'Students Detail')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Students</a></li>
    <li class="breadcrumb-item active" aria-current="page">View Detail</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-7">
            <div class="card rounded mb-2">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="col">
                            <h3 class="m-0 mb-2 p-0">{{ $student->full_name }}</h3>
                            <small class="text-muted">
                                <i class="bi bi-calendar-day me-1"></i> Join Date:
                                {{ date('d M Y', strtotime($student->created_at)) }} |
                                <i class="bi bi-calendar-date mx-1"></i> Last Update:
                                {{ date('d M Y', strtotime($student->updated_at)) }}
                            </small>
                        </div>
                        <div class="col-2 text-end">
                            <a href="{{ url('client/student/' . $student->id . '/edit') }}"
                                class="btn btn-warning btn-sm rounded p-2"><i class="bi bi-pencil"></i></a>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                E-mail
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            {{ $student->mail }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                Phone Number
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            {{ $student->phone }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                Address
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            {!! $student->address !!}
                            {!! $student->postal_code ? $student->postal_code . '<br>' : null !!}
                            {{ $student->city }} {{ $student->state }}
                        </div>
                    </div>
                    @if ($student->school != null)
                        <div class="row mb-2 g-1">
                            <div class="col d-flex justify-content-between">
                                <label>
                                    School Name
                                </label>
                                <label>:</label>
                            </div>
                            <div class="col-md-9 col-8">
                                {{ $student->school->sch_name }}
                            </div>
                        </div>
                    @endif
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                Grade
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            {{ $viewStudent->grade_now <= 12 ? $viewStudent->grade_now : 'Not High School' }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                Graduation Year
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            {{ $student->graduation_year_real }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                Gap Year
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            {{ $student->gap_year }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                Follow-up Priority
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            {{ $student->st_levelinterest }}
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                Lead
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            {{ $student->lead_source }} 
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                Is Funding
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            @switch($student->is_funding)
                                @case('1')
                                    Yes
                                @break

                                @case('0')
                                    No
                                @break

                                @case(null)
                                @break
                            @endswitch
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                Register As
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            @switch($student->register_as)
                                @case('student')
                                    Student
                                @break

                                @case('parent')
                                    Parent
                                @break

                                @case(null)
                                @break
                            @endswitch
                        </div>
                    </div>
                    <div class="row mb-2 g-1">
                        <div class="col d-flex justify-content-between">
                            <label>
                                Active Status
                            </label>
                            <label>:</label>
                        </div>
                        <div class="col-md-9 col-8">
                            <input type="checkbox" name="st_status" id="status" value=""
                                @checked($student->st_statusact == 1)>
                        </div>
                    </div>
                </div>
            </div>

            @include('pages.client.student.component.pic-client')
            @include('pages.client.student.component.interest-program')
        </div>
        <div class="col-md-5">
            @include('pages.client.student.component.interest-country')
            @include('pages.client.student.component.dream-university')
            @include('pages.client.student.component.interest-major')
            @include('pages.client.student.component.parents-info')
            @include('pages.client.student.component.lead-tracking')
        </div>
        <div class="col-md-12">
            @include('pages.client.student.component.client-program')
        </div>

        <div class="col-md-12 mt-2">
            @include('pages.client.student.component.client-event')
        </div>
    </div>

    <script src="{{ asset('js/lc_switch.min.js') }}"></script>
    <script>
        lc_switch('input[type=checkbox]', {

            // ON text
            on_txt: 'ON',

            // OFF text
            off_txt: 'OFF',

            // Custom ON color. Supports gradients
            on_color: '#0083B8',

            // enable compact mode
            compact_mode: false

        });
    </script>

    <script type="text/javascript">
        // $("#status").on('change', async function() {
        $('.lcs_switch').on('click', async function() {

            // Swal.fire({
            //     width: 100,
            //     backdrop: '#4e4e4e7d',
            //     allowOutsideClick: false,
            // })
            // swal.showLoading()

            // var val = $(this).val() // for select option
            var class_names = $(this).attr('class');
            var getLcsStatus = class_names.split(' ');
            var current_value = getLcsStatus[2];

            var val = current_value == "lcs_off" ? 0 : 1;

            var link = "{{ url('/') }}/client/student/{{ $student->id }}/status/" + val

            await axios.get(link)
                .then(function(response) {

                    Swal.close()
                    notification("success", response.data.message)
                })
                .catch(function(error) {
                    // handle error
                    Swal.close()
                    notification("error", error.response.data.message)
                })
        })
    </script>

    <script>
         $(document).ready(function() {
            $('.modal-select2').select2({
                dropdownParent: $('#addInterestProgram .modal-content'),
                placeholder: "Select value",
                allowClear: true
            });
            $('.modal-select').select2({
                dropdownParent: $('#modalPICclient .modal-content'),
                placeholder: "Select value",
                allowClear: true
            });
        });
    </script>
@endsection
