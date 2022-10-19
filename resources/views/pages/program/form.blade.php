@extends('layout.main')

@section('title', 'Program - Bigdata Platform')

@section('content')

    <?php
    $prog['main'] = ['Admissions Mentoring', 'Career Exploration', 'Eduverse', 'Academic & Test Preparation', 'Others'];
    
    $prog['admissions'] = ['Admissions Mentoring', 'Essay Clinic', 'Interview Preparation'];
    $prog['exploration'] = ['Career Bootcamp','Exploration', 'JuniorXplorer', 'PassionXplorer', 'Global Immersion Program'];
    $prog['eduverse'] = ['Application Bootcamp', 'Group Mentoring'];
    $prog['academic'] = ['Academic Tutoring', 'ACT', 'SAT', 'Subject Tutoring'];
    $prog['other'] = ['Event','Info Session', 'Other'];
    ?>

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('master/program') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Program
        </a>
    </div>

    <div class="card rounded">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4 text-center">
                    <img src="{{ asset('img/program.webp') }}" alt="" class="w-75">
                </div>
                <div class="col-md-8">
                    <form action="" method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Program ID <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="asset_name" class="form-control form-control-sm rounded"
                                        value="">
                                    @error('prog_id')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Program Type
                                    </label>
                                    <select type="text" name="prog_type" class="select w-100" value="">
                                        <option data-placeholder="true"></option>
                                        <option value="B2B">B2B</option>
                                        <option value="B2C">B2C</option>
                                        <option value="B2B/B2C">B2B</option>
                                    </select>
                                    @error('prog_type')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Main Program
                                    </label>
                                    <select type="text" name="prog_main" class="select w-100" id="mainProgram"
                                        onchange="mainUpdate()" >
                                        <option data-placeholder="true"></option>
                                        @foreach ($prog['main'] as $item)
                                            <option value="{{ $item }}">{{ $item }}</option>
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
                                        Program Name
                                    </label>
                                    <input type="text" name="prog_name" class="form-control form-control-sm rounded"
                                        value="">
                                    @error('prog_name')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Need Mentor/Tutor
                                    </label>
                                    <select type="text" name="prog_mentor" class="select w-100">
                                        <option data-placeholder="true"></option>
                                        <option value="Mentor">Mentor</option>
                                        <option value="Tutor">Tutor</option>
                                        <option value="No">No</option>
                                    </select>
                                    @error('prog_mentor')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Payment Category
                                    </label>
                                    <select type="text" name="prog_payment" class="select w-100">
                                        <option data-placeholder="true"></option>
                                        <option value="idr">IDR / Rupiah</option>
                                        <option value="usd">USD</option>
                                        <option value="session">Session</option>
                                    </select>
                                    @error('prog_payment')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="text-center">
                                    <hr>
                                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-save2 me-1"></i>
                                        Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        function mainUpdate() {
            let prog = $('#mainProgram').val();
            if (prog == 'Admissions Mentoring') {
                $('#subProgram').html('');
                $('#subProgram').html(
                    '@foreach($prog["admissions"] as $item):' +
                    '<option value="{{$item}}">{{$item}}</option>' +
                    '@endforeach'
                );
            } else if (prog == 'Career Exploration') {
                $('#subProgram').html('');
                $('#subProgram').html(
                    '@foreach($prog["exploration"] as $item):' +
                    '<option value="{{$item}}">{{$item}}</option>' +
                    '@endforeach'
                );
            } else if (prog == 'Eduverse') {
                $('#subProgram').html('');
                $('#subProgram').html(
                    '@foreach($prog["eduverse"] as $item):' +
                    '<option value="{{$item}}">{{$item}}</option>' +
                    '@endforeach'
                );
            } else if (prog == 'Academic & Test Preparation') {
                $('#subProgram').html('');
                $('#subProgram').html(
                    '@foreach($prog["academic"] as $item):' +
                    '<option value="{{$item}}">{{$item}}</option>' +
                    '@endforeach'
                );
            }  else if (prog == 'Others') {
                $('#subProgram').html('');
                $('#subProgram').html(
                    '@foreach($prog["other"] as $item):' +
                    '<option value="{{$item}}">{{$item}}</option>' +
                    '@endforeach'
                );
            }
        }

        $(document).ready(function() { 
            mainUpdate();
        })
    </script>

@endsection
