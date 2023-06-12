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
    </style>
@endsection
@section('body')
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
                        <form action="" method="">
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
                                    </select>
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
                            <div class="row row-cols-1">
                                <div class="col mb-3">
                                    <label for="i_leadsource">I know this event from</label>
                                    <select type="text" name="leadsource" id="i_leadsource" class="select w-100">
                                        <option value=""></option>
                                        @for ($i =1; $i < 5; $i++)
                                            <option value="Lead Source {{$i}}">Lead Source {{ $i }}</option>
                                        @endfor
                                    </select>
                                    @error('leadsource')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-light text-primary">
                                    Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
