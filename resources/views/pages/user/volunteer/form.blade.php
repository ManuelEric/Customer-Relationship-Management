@extends('layout.main')

@section('title', 'Volunteer - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('user/volunteer') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Volunteer
        </a>
    </div>

    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card rounded mb-3">
                <div class="card-body">
                    <img src="{{ asset('img/mentee.jpg') }}" class="w-100">
                    <h4 class="text-center">Add Volunteer</h4>

                    @if(isset($volunteer))
                        <div class="text-center mt-2">
                                <a class="btn btn-sm btn-success {{$volunteer->volunt_status == 1 ? 'disabled' : ''}}" id="update-status-active">
                                    <i class="bi bi-check"></i>
                                    Activate</a>
                            
                                <a class="btn btn-sm btn-outline-danger {{$volunteer->volunt_status == 0 ? 'disabled' : ''}}" id="update-status-deactive">
                                    <i class="bi bi-x"></i>
                                    Deactivate</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card rounded mb-3">
                <div class="card-header">
                    <h5 class="p-0 m-0">Volunteer Detail</h5>
                </div>
                <div class="card-body">
                    <form action="{{ url(isset($volunteer) ? 'user/volunteer/' . $volunteer->volunt_id : 'user/volunteer') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @if (isset($volunteer))
                            @method('put')
                        @endif
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="">
                                    First Name <sup class="text-danger">*</sup>
                                </label>
                                <input type="text" name="volunt_firstname" class="form-control form-control-sm rounded"
                                    value="{{ isset($volunteer->volunt_firstname) ? $volunteer->volunt_firstname : old('volunt_firstname') }}">
                                @error('volunt_firstname')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="">
                                    Last Name
                                </label>
                                <input type="text" name="volunt_lastname" class="form-control form-control-sm rounded"
                                    value="{{ isset($volunteer->volunt_lastname) ? $volunteer->volunt_lastname : old('volunt_lastname') }}">
                                @error('volunt_lastname')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="">
                                    Email <sup class="text-danger">*</sup>
                                </label>
                                <input type="email" name="volunt_mail" class="form-control form-control-sm rounded"
                                    value="{{ isset($volunteer->volunt_mail) ? $volunteer->volunt_mail : old('volunt_mail') }}">
                                @error('email')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="">
                                    Phone Number <sup class="text-danger">*</sup>
                                </label>
                                <input type="text" name="volunt_phone" class="form-control form-control-sm rounded"
                                    value="{{ isset($volunteer->volunt_phone) ? $volunteer->volunt_phone : old('volunt_phone') }}">
                                @error('volunt_phone')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="">
                                    Address
                                </label>
                                <textarea name="volunt_address" cols="30" rows="10">{{ isset($volunteer->volunt_address) ? $volunteer->volunt_address : old('volunt_address') }}
                                </textarea>
                                @error('volunt_address')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="">
                                    Graduated From
                                </label>
                                <input type="text" name="volunt_graduatedfr" class="form-control form-control-sm rounded"
                                    value="{{ isset($volunteer->volunt_graduatedfr) ? $volunteer->volunt_graduatedfr : old('volunt_graduatedfr') }}">

                                {{-- <select name="volunt_graduatedfr" id="" class="select w-100">
                                    <option data-placeholder="true"></option>
                                    @foreach ($universities as $university)
                                        <option value="{{ $university->univ_id }}"
                                            @if((isset($volunteer) && $volunteer->volunt_graduatedfr == $university->univ_id) || (!empty(old('volunt_graduatedfr')) && old('volunt_graduatedfr') == $university->univ_id))
                                                {{'selected'}}
                                            @endif
                                            >
                                            {{ $university->univ_name }}
                                        </option>
                                    @endforeach
                                </select> --}}
                                @error('volunt_graduatedfr')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="">
                                    Major
                                </label>
                                <input type="text" name="volunt_major" class="form-control form-control-sm rounded"
                                    value="{{ isset($volunteer->volunt_major) ? $volunteer->volunt_major : old('volunt_major') }}">
                                @error('volunt_major')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="">
                                    Position
                                </label>
                                <input type="text" name="volunt_position" class="form-control form-control-sm rounded"
                                    value="{{ isset($volunteer->volunt_position) ? $volunteer->volunt_position : old('volunt_position') }}">
                                @error('volunt_position')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                @include('pages.user.volunteer.form-detail.attachment')
                            </div>

                            <div class="col-md-12">
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="bi bi-save me-2"></i> Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        @if (isset($volunteer))
            $("#update-status-active").on('click', function(e) {
                e.preventDefault()
                Swal.showLoading()
                axios
                    .get(
                        '{{ route('volunteer.update.status', ['volunteer' => $volunteer->volunt_id, 'status' => 1]) }}'
                    )
                    .then(response => {
                        swal.close()
                        notification('success', response.data.message)
                        setTimeout(location.reload.bind(location), 3000);
                    })
                    .catch(error => {
                        notification('error',
                            response.data.message);
                        swal.close()
                    })
            })

             $("#update-status-deactive").on('click', function(e) {
                e.preventDefault()
                Swal.showLoading()
                axios
                    .get(
                        '{{ route('volunteer.update.status', ['volunteer' => $volunteer->volunt_id, 'status' => 0]) }}'
                    )
                    .then(response => {
                        swal.close()
                        notification('success', response.data.message)
                        setTimeout(location.reload.bind(location), 3000);
                    })
                    .catch(error => {
                        notification('error',
                            response.data.message);
                        swal.close()
                    })
            })
        @endif
    </script>
@endsection
