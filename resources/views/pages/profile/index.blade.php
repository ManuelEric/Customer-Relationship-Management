@extends('layout.main')

@section('title', 'Assets')

@push('styles')
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css">
    <style>
        .iti {
            display: block !important;
        }
    </style>
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('index') }}">Your profile</a></li>
    <li class="breadcrumb-item active" aria-current="page">Change Password</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4 text-center">
            <div class="card">
                <div class="card-body">
                    <img loading="lazy"  src="{{ asset('img/profile.webp') }}" alt="" class="w-75">
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-tags me-2"></i>
                            Your Information
                        </h6>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update', ['profile' => $my_info->uuid ]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        First Name <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="first_name" class="form-control form-control-sm rounded"
                                        value="{{ $my_info->first_name }}">
                                    @error('first_name')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Last Name
                                    </label>
                                    <input type="text" name="last_name" class="form-control form-control-sm rounded"
                                        value="{{ $my_info->last_name }}">
                                    @error('last_name')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">
                                        Address
                                    </label>
                                    <textarea name="address" cols="30" rows="2" class="form-control form-control-sm rounded">{{ $my_info->address }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Email
                                    </label>
                                    <input type="email" name="email" class="form-control form-control-sm rounded" value="{{ $my_info->email }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Phone
                                    </label>
                                    <input type="text" name="phone" class="form-control form-control-sm rounded" value="{{ $my_info->phone }}">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="my-4"></div>

            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-tags me-2"></i>
                            Change Your Password
                        </h6>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update', ['profile' => $my_info->uuid ]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="form:password" value="true">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        New Password
                                    </label>
                                    <input type="password" name="password" class="form-control form-control-sm rounded">
                                    @error('password')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Confirmation 
                                    </label>
                                    <input type="password" name="password_confirmation" class="form-control form-control-sm rounded">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row justify-content-center align-items-center mt-3">
                                    <div>
                                        <button type="submit" class="btn btn-sm btn-primary"><i
                                                class="bi bi-arrow-return-left"></i> Change</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://fastly.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
<script>
    var userPhone = document.querySelector('input[name="phone"]');

    const phoneInput = window.intlTelInput(userPhone, {
            utilsScript: "https://fastly.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
            initialCountry: 'id',
            onlyCountries: ["id", "us", "gb", "sg", "au", "my"],
        });

    $('input[name="phone"]').on('keyup', function(e) {
        var rendered_number = phoneInput.getNumber();
        $(this).val(rendered_number);
    });
</script>
@endpush
