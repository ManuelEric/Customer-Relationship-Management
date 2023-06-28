@extends('app')
@section('title', 'Registration Form')
@section('css')
    <style>
        .select2-container .select2-selection--single,
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
        }

        .iti {display: block !important; color: black}
    </style>
@endsection
@section('body')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

    <div class="container">
        <div class="row">
            <div class="col-12 p-3">
                <div class="card">
                    <div class="card-header text-white" style="background: #233872;">
                        <h5 class="my-1">
                            Let us know you better by filling out this form!
                        </h6>
                    </div>
                    <div class="card-body text-white" style="background: #233872;">
                        <form action="{{ route('submit.registration') }}" method="POST" id="registration-form">
                            @csrf
                            <div class="row row-cols-lg-2 row-cols-1 align-items-start g-3 mb-3">
                                <div class="col">
                                    <label for="i_name">Name</label>
                                    <input type="text" name="name" id="i_name" class="form-control">
                                    @error('name')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                                <div class="col">
                                    <label for="i_childname">Child's Name</label>
                                    <input type="text" name="child_name" id="i_childname" class="form-control">
                                    <small class="text-warning">* if you are a student, then fill in this column with your
                                        name</small>
                                    @error('child_name')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                            </div>
                            <div class="row row-cols-lg-2 row-cols-1 align-items-start g-3 mb-3">
                                <div class="col">
                                    <label for="i_email">Email</label>
                                    <input type="email" name="email" id="i_email" class="form-control">
                                    @error('email')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                                <div class="col">
                                    <label for="i_phone">Phone Number</label>
                                    <input type="text" name="phone" id="i_phone" class="form-control">
                                    
                                    <input type="hidden" name="fullnumber">
                                    <small class="text-warning phone-validation"></small>
                                    @error('phone')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                            </div>
                            <div class="row row-cols-lg-2 row-cols-1 align-items-start g-3 mb-3">
                                <div class="col">
                                    <label for="i_school">School</label>
                                    <select type="school" name="school" id="i_school" class="select w-100">
                                        <option value=""></option>
                                        @foreach ($schools as $school)
                                            <option value="{{ $school->sch_id }}">{{ $school->sch_name }}</option>
                                        @endforeach
                                        <option value="new-school">Add new</option>
                                    </select>
                                    <div class="d-none" id="new-school-container">
                                        <input type="text" name="new_school_box" class="form-control">
                                        <small class="text-warning">* if your school does not exist, write your school name on the column above</small>
                                    </div>
                                    @error('school')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                                <div class="col">
                                    <label for="i_grade">Expected Graduation Year</label>
                                    <select type="text" name="grade" id="i_grade" class="select w-100">
                                        <option value=""></option>
                                        @for ($i = date('Y'); $i < date('Y') + 5; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    @error('grade')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label for="i_program" class="mb-2">I would like to know more about</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="program" checked
                                            value="admission_mentoring">
                                        <label class="form-check-label">Admission Mentoring</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="program"
                                            value="university_application_essay">
                                        <label class="form-check-label">University Application Essay</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="program"
                                            value="academic_tutoring">
                                        <label class="form-check-label">Academic Tutoring</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="program"
                                            value="cv_essay_sat">
                                        <label class="form-check-label">CV, Essay, SAT</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="program"
                                            value="career_exploration">
                                        <label class="form-check-label">Career Exploration</label>
                                    </div>
                                    @error('program')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-light text-primary" type="button">
                                    Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $("#i_school").on('select2:select', function (e) {
            var data = e.params.data;
            var id = data.id;
            
            if (id == "new-school")
            {
                $("#new-school-container").removeClass('d-none');
            }
        })
    </script>
    <script>
        const phoneInputField = document.querySelector("#i_phone");
        const phoneInput = window.intlTelInput(phoneInputField, {
            utilsScript:
            "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        });

        // checker
        $("#i_phone").on('keyup', function (e) {
            if (!phoneInput.isValidNumber()) {
                $(".phone-validation").html("Invalid number")
                return false;
            }

            $(".phone-validation").html("")
        })
        
        // registration
        $("#registration-form button").click(function (e) {
            e.preventDefault();

            // initiate 
            var form = $("#registration-form");
            var url = form.attr('action');
            var number = phoneInput.getNumber();

            $("input[name='fullnumber']").val(number);
            form.submit();
        })
    </script>
@endsection
