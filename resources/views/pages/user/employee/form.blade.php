@extends('layout.main')

@section('title', 'Employee - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('user/employee') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Employee
        </a>
    </div>


    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card rounded mb-3">
                <div class="card-body">
                    <img src="{{ asset('img/mentee.jpg') }}" class="w-100">
                    <h4 class="text-center">Add Employee</h4>

                    <div class="text-center mt-2">
                        <button class="btn btn-sm btn-success">
                            <i class="bi bi-check"></i>
                            Activate</button>
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-x"></i>
                            Deactivate</button>
                    </div>
                </div>
            </div>
            <div class="card rounded mb-3">
                <div class="card-header">
                    <h5 class="p-0 m-0">Employee Type</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <div class="list-group-item">
                            Probation
                            <div class="">
                                20 April 2022 - 25 July 2024
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card rounded mb-3">
                <div class="card-header">
                    <h5 class="p-0 m-0">Mentees</h5>
                </div>
                <div class="card-body text-center">
                    <h2>24</h2>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card rounded mb-3">
                <div class="card-header d-flex align-items-center">
                    <h4 class="m-0 p-0">Employee Detail</h4>
                </div>
                <div class="card-body">
                    <form action="">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="">First Name</label>
                                <input type="text" name="first_name" id=""
                                    class="form-control form-control-sm rounded">
                                @error('first_name')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="">Last Name</label>
                                <input type="text" name="last_name" id=""
                                    class="form-control form-control-sm rounded">
                                @error('last_name')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="">Email</label>
                                <input type="email" name="email" id=""
                                    class="form-control form-control-sm rounded">
                                @error('email')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="">Phone Number</label>
                                <input type="text" name="phone_number" id=""
                                    class="form-control form-control-sm rounded">
                                @error('phone_number')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="">Emergency Contact</label>
                                <input type="text" name="emergency_contact" id=""
                                    class="form-control form-control-sm rounded">
                                @error('emergency_contact')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="">Date of Birth</label>
                                <input type="date" name="dob" id=""
                                    class="form-control form-control-sm rounded">
                                @error('dob')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="">Address</label>
                                <textarea name="address" id=""></textarea>
                                @error('address')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                @include('pages.user.employee.form-detail.education')
                            </div>
                            <div class="col-md-12 mb-3">
                                @include('pages.user.employee.form-detail.role')
                            </div>
                            <div class="col-md-12 mb-3">
                                @include('pages.user.employee.form-detail.attachment')
                            </div>
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="bi bi-save me-2"></i> Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>


    <script></script>
@endsection
