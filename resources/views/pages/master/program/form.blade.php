@extends('layout.main')

@section('title', 'Programs')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Program</a></li>
    <li class="breadcrumb-item active" aria-current="page">View Detail</li>
@endsection

@section('content')
    <?php
    $prog['main'] = ['Admissions Mentoring', 'Career Exploration', 'Eduverse', 'Academic & Test Preparation', 'Others'];
    
    $prog['admissions'] = ['Admissions Mentoring', 'Essay Clinic', 'Interview Preparation'];
    $prog['exploration'] = ['Career Bootcamp', 'Exploration', 'JuniorXplorer', 'PassionXplorer', 'Global Immersion Program'];
    $prog['eduverse'] = ['Application Bootcamp', 'Group Mentoring'];
    $prog['academic'] = ['Academic Tutoring', 'ACT', 'SAT', 'Subject Tutoring'];
    $prog['other'] = ['Event', 'Info Session', 'Other'];
    ?>


    <div class="row g-3">
        <div class="col-md-3 text-center">
            <div class="card rounded">
                <div class="card-body">
                    <img loading="lazy"  loading="lazy" src="{{ asset('img/icon/program.webp') }}" alt="" class="w-25">
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card rounded">
                <div class="card-body">
                    <form
                        action="{{ isset($program) ? url('master/program') . '/' . $program->prog_id : url('master/program') }}"
                        method="POST">
                        @csrf
                        @if (isset($program))
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Program ID <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="prog_id" class="form-control form-control-sm rounded"
                                        value="{{ isset($program->prog_id) ? $program->prog_id : old('prog_id') }}">
                                    <input type="hidden" name="old_prog_id"
                                        value="{{ isset($program->prog_id) ? $program->prog_id : null }}">
                                    @error('prog_id')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Program Type <sup class="text-danger">*</sup>
                                    </label>
                                    <select type="text" name="prog_type" class="select w-100" value="">
                                        <option data-placeholder="true"></option>
                                        <option value="B2B"
                                            {{ (isset($program->prog_type) && $program->prog_type == 'B2B') || old('prog_type') == 'B2B' ? 'selected' : null }}>
                                            B2B</option>
                                        <option value="B2C"
                                            {{ (isset($program->prog_type) && $program->prog_type == 'B2C') || old('prog_type') == 'B2C' ? 'selected' : null }}>
                                            B2C</option>
                                        <option value="B2B/B2C"
                                            {{ (isset($program->prog_type) && $program->prog_type == 'B2B/B2C') || old('prog_type') == 'B2B/B2C' ? 'selected' : null }}>
                                            B2B/B2C</option>
                                    </select>
                                    @error('prog_type')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Main Program <sup class="text-danger">*</sup>
                                    </label>
                                    <select type="text" name="prog_main" class="select w-100" id="mainProgram">
                                        <option data-placeholder="true"></option>
                                        @foreach ($main_programs as $main_program)
                                            <option value="{{ $main_program->id }}"
                                                {{ (isset($program->main_prog_id) && $program->main_prog_id == $main_program->id) || old('prog_main') == $main_program->id ? 'selected' : null }}>
                                                {{ $main_program->prog_name }}</option>
                                            {{-- <option value="{{ $main_program->id }}">{{ $main_program->prog_name }}</option> --}}
                                        @endforeach
                                    </select>
                                    @error('prog_main')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Sub Program
                                    </label>
                                    <select type="text" name="prog_sub" class="select w-100" id="subProgram">
                                        <option data-placeholder="true"></option>
                                    </select>
                                    @error('prog_sub')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">
                                        Program Name <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="prog_name" class="form-control form-control-sm rounded"
                                        value="{{ isset($program->prog_program) ? $program->prog_program : old('prog_name') }}">
                                    @error('prog_name')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label for="">
                                        Need Mentor/Tutor <sup class="text-danger">*</sup>
                                    </label>
                                    <select type="text" name="prog_mentor" class="select w-100">
                                        <option data-placeholder="true"></option>
                                        <option value="Mentor"
                                            {{ (isset($program->prog_mentor) && $program->prog_mentor == 'Mentor') || old('prog_mentor') == 'Mentor' ? 'selected' : null }}>
                                            Mentor</option>
                                        <option value="Tutor"
                                            {{ (isset($program->prog_mentor) && $program->prog_mentor == 'Tutor') || old('prog_mentor') == 'Tutor' ? 'selected' : null }}>
                                            Tutor</option>
                                        <option value="No"
                                            {{ (isset($program->prog_mentor) && $program->prog_mentor == 'No') || old('prog_mentor') == 'No' ? 'selected' : null }}>
                                            No</option>
                                    </select>
                                    @error('prog_mentor')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label for="">
                                        Payment Category <sup class="text-danger">*</sup>
                                    </label>
                                    <select type="text" name="prog_payment" class="select w-100">
                                        <option data-placeholder="true"></option>
                                        <option value="idr"
                                            {{ (isset($program->prog_payment) && $program->prog_payment == 'idr') || old('prog_payment') == 'idr' ? 'selected' : null }}>
                                            IDR / Rupiah</option>
                                        <option value="usd"
                                            {{ (isset($program->prog_payment) && $program->prog_payment == 'usd') || old('prog_payment') == 'usd' ? 'selected' : null }}>
                                            USD</option>
                                        <option value="session"
                                            {{ (isset($program->prog_payment) && $program->prog_payment == 'session') || old('prog_payment') == 'session' ? 'selected' : null }}>
                                            Session</option>
                                    </select>
                                    @error('prog_payment')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label for="">
                                        Program Scope <sup class="text-danger">*</sup>
                                    </label>
                                    <select type="text" name="prog_scope" class="select w-100">
                                        <option data-placeholder="true"></option>
                                        <option value="mentee"
                                            {{ (isset($program->prog_scope) && $program->prog_scope == 'mentee') || old('prog_scope') == 'mentee' ? 'selected' : null }}>
                                            Mentee</option>
                                        <option value="public"
                                            {{ (isset($program->prog_scope) && $program->prog_scope == 'public') || old('prog_scope') == 'public' ? 'selected' : null }}>
                                            Public</option>
                                        <option value="school"
                                            {{ (isset($program->prog_scope) && $program->prog_scope == 'school') || old('prog_scope') == 'school' ? 'selected' : null }}>
                                            School</option>
                                        <option value="partner"
                                            {{ (isset($program->prog_scope) && $program->prog_scope == 'partner') || old('prog_scope') == 'partner' ? 'selected' : null }}>
                                            Partner</option>
                                    </select>
                                    @error('prog_scope')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label for="">
                                        Status <sup class="text-danger">*</sup>
                                    </label>
                                    <select name="active" class="select w-100">
                                        <option data-placeholder="true"></option>
                                        <option value="1" @selected(isset($program->prog_scope) && $program->active == 1)>Active</option>
                                        <option value="0" @selected(isset($program->prog_scope) && $program->active == 0)>Inactive</option>
                                    </select>
                                    @error('active')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="text-center">
                                    <hr>
                                    <button type="submit" class="btn btn-sm btn-primary"><i
                                            class="bi bi-save2 me-1"></i>
                                        Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <script type="text/javascript" async defer>
        // function mainUpdate() {
        //     let prog = $('#mainProgram').val();
        //     if (prog == 'Admissions Mentoring') {
        //         $('#subProgram').html('');
        //         $('#subProgram').html(
        //             '@foreach ($prog['admissions'] as $item):' +
        //             '<option value="{{ $item }}">{{ $item }}</option>' +
        //             '@endforeach'
        //         );
        //     } else if (prog == 'Career Exploration') {
        //         $('#subProgram').html('');
        //         $('#subProgram').html(
        //             '@foreach ($prog['exploration'] as $item):' +
        //             '<option value="{{ $item }}">{{ $item }}</option>' +
        //             '@endforeach'
        //         );
        //     } else if (prog == 'Eduverse') {
        //         $('#subProgram').html('');
        //         $('#subProgram').html(
        //             '@foreach ($prog['eduverse'] as $item):' +
        //             '<option value="{{ $item }}">{{ $item }}</option>' +
        //             '@endforeach'
        //         );
        //     } else if (prog == 'Academic & Test Preparation') {
        //         $('#subProgram').html('');
        //         $('#subProgram').html(
        //             '@foreach ($prog['academic'] as $item):' +
        //             '<option value="{{ $item }}">{{ $item }}</option>' +
        //             '@endforeach'
        //         );
        //     }  else if (prog == 'Others') {
        //         $('#subProgram').html('');
        //         $('#subProgram').html(
        //             '@foreach ($prog['other'] as $item):' +
        //             '<option value="{{ $item }}">{{ $item }}</option>' +
        //             '@endforeach'
        //         );
        //     }
        // }

        $(document).ready(function() {
            var a = $("#mainProgram").val()
            if (a) {
                getSubProg(a)
            }
            // mainUpdate();
            $("#mainProgram").on('change', function() {

                getSubProg($(this).val())
            })
        })

        function getSubProg(val) {
            $.ajax({
                url: '{{ url('master/program/sub_program') }}/' + val,
            }).done(function(msg) {
                let obj = JSON.parse(msg);
                var html = ''
                for (var i = 0; i < obj.length; i++) {
                    var sub_prog_id = "{{ isset($program->sub_prog_id) ? $program->sub_prog_id : null }}"
                    if (obj[i].id == sub_prog_id) {
                        html += '<option value="' + obj[i].id + '" selected>' + obj[i].sub_prog_name + '</option>'
                    } else {
                        html += '<option value="' + obj[i].id + '">' + obj[i].sub_prog_name + '</option>'
                    }
                }

                $("#subProgram").html(html)
            })
        }

        function render_subprogram(item, index, arr) {
            html = '<option value="' + arr[index]['id'] + '">' + arr[index]['sub_prog_name'] + '</option>'
        }
    </script>

@endsection
