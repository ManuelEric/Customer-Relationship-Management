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
                    <img loading="lazy"  src="{{ asset('img/mentee.webp') }}" class="w-100">
                    <h4 class="text-center">Add Volunteer</h4>

                    <div class="text-center mt-2">
                        @if(isset($volunteer))
                            <button @class([
                                'btn btn-sm btn-success',
                                'd-none' => $volunteer->active == 1,
                            ]) id="activate-user">
                                <i class="bi bi-check"></i>
                                Activate</button>
                                
                            <button @class([
                                'btn btn-sm btn-outline-danger',
                                'd-none' => $volunteer->active == 0,
                            ]) id="deactivate-user">
                                <i class="bi bi-x"></i>
                                Deactivate</button>
                        @endif
                    </div>
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
                                {{-- <input type="text" name="volunt_graduatedfr" class="form-control form-control-sm rounded"
                                    value="{{ isset($volunteer->volunt_graduatedfr) ? $volunteer->volunt_graduatedfr : old('volunt_graduatedfr') }}"> --}}

                                <select name="volunt_graduatedfr" id="" class="select w-100">
                                    <option data-placeholder="true"></option>
                                    @foreach ($universities as $university)
                                        <option value="{{ $university->univ_id }}"
                                            @if((isset($volunteer) && $volunteer->univ_id == $university->univ_id) || (!empty(old('volunt_graduatedfr')) && old('volunt_graduatedfr') == $university->univ_id))
                                                {{'selected'}}
                                            @endif
                                            >
                                            {{ $university->univ_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('volunt_graduatedfr')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="">
                                    Major
                                </label>
                                {{-- <input type="text" name="volunt_major" class="form-control form-control-sm rounded"
                                    value="{{ isset($volunteer->volunt_major) ? $volunteer->volunt_major : old('volunt_major') }}"> --}}
                                <select name="volunt_major" id="" class="select w-100">
                                    <option data-placeholder="true"></option>
                                    @foreach ($majors as $major)
                                        <option value="{{ $major->id }}"
                                            @if((isset($volunteer) && $volunteer->major_id == $major->id) || (!empty(old('volunt_major')) && old('volunt_major') == $major->id))
                                                {{'selected'}}
                                            @endif
                                            >
                                            {{ $major->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('volunt_major')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="">
                                    Position
                                </label>
                                {{-- <input type="text" name="volunt_position" class="form-control form-control-sm rounded"
                                    value="{{ isset($volunteer->volunt_position) ? $volunteer->volunt_position : old('volunt_position') }}"> --}}
                                <select name="volunt_position" id="" class="select w-100">
                                    <option data-placeholder="true"></option>
                                    @foreach ($positions as $position)
                                        <option value="{{ $position->id }}"
                                            @if((isset($volunteer) && $volunteer->position_id == $position->id) || (!empty(old('volunt_position')) && old('volunt_position') == $position->id))
                                                {{'selected'}}
                                            @endif
                                            >
                                            {{ $position->position_name }}
                                        </option>
                                    @endforeach
                                </select>
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

            $("#deactivate-user").on('click', function() {
                changeStatus('deactivate')
            })

            $("#activate-user").on('click', function() {
                changeStatus('activate')
            })

            function changeStatus(status)
            {
                // show modal 
                var myModal = new bootstrap.Modal(document.getElementById('deactiveUser'))
                myModal.show()

                // $("#deactivate-user--app-3103").attr('onclick', 'deactivateUser(\''+status+'\')')
                $("#deactivate-user--app-3103").bind('click', function() {
                    deactivateUser(status)
                })

                
            }

            function deactivateUser(status) {
                showLoading()
                
                axios.post('{{ route('volunteer.update.status', ['volunteer' => $volunteer->volunt_id]) }}', {
                        _token: '{{ csrf_token() }}',
                        params: {
                            new_status: status
                        },
                    })
                    .then((response) => {
                        console.log(response);

                        Swal.close()
                        $("#deactiveUser").modal('hide')
                        notification('success', response.data.message)
                        switch(status) {
                            case "activate":
                                $("#activate-user").addClass('d-none')
                                $("#deactivate-user").removeClass('d-none')
                                break;

                            case "deactivate":
                                $("#activate-user").removeClass('d-none')
                                $("#deactivate-user").addClass('d-none')
                                break;
                        }

                    }, (error) => {
                        console.log(error)
                        Swal.close()
                        notification('error', 'Something went wrong. Please try again or contact the administrator.')
                    });
            }
        @endif
                

        @if (isset($volunteer))

            // curriculum vitae button
            $(".curriculum-vitae-container .download").on('click', function() {
                window.open("{{ route('volunteer.file.download', ['volunteer' => $volunteer->volunt_id, 'filetype' => 'CV']) }}", '_blank');
            })

            $(".curriculum-vitae-container .remove").on('click', function() {
                $(this).parent().find('.upload-file').removeClass('d-none');
                $(this).addClass('d-none')
                $(this).parent().find('.download').addClass('d-none');
            })

            $(".curriculum-vitae-container").on('click', '.rollback', function() {
                $(this).parent().addClass('d-none')
                $(this).parent().parent().find('.remove').removeClass('d-none');
                $(this).parent().parent().find('.download').removeClass('d-none');
            })

            // ktp button
            $(".ktp-container .download").on('click', function() {
                window.open("{{ route('volunteer.file.download', ['volunteer' => $volunteer->volunt_id, 'filetype' => 'ID']) }}", '_blank');
            })

            $(".ktp-container .remove").on('click', function() {
                $(this).parent().find('.upload-file').removeClass('d-none');
                $(this).addClass('d-none')
                $(this).parent().find('.download').addClass('d-none');
            })

            $(".ktp-container").on('click', '.rollback', function() {
                $(this).parent().addClass('d-none')
                $(this).parent().parent().find('.remove').removeClass('d-none');
                $(this).parent().parent().find('.download').removeClass('d-none');
            })

            // tax button
            $(".tax-container .download").on('click', function() {
                window.open("{{ route('volunteer.file.download', ['volunteer' => $volunteer->volunt_id, 'filetype' => 'TX']) }}", '_blank');
            })

            $(".tax-container .remove").on('click', function() {
                $(this).parent().find('.upload-file').removeClass('d-none');
                $(this).addClass('d-none')
                $(this).parent().find('.download').addClass('d-none');
            })

            $(".tax-container").on('click', '.rollback', function() {
                $(this).parent().addClass('d-none')
                $(this).parent().parent().find('.remove').removeClass('d-none');
                $(this).parent().parent().find('.download').removeClass('d-none');
            })

            // hi button
            $(".hi-container .download").on('click', function() {
                window.open("{{ route('volunteer.file.download', ['volunteer' => $volunteer->volunt_id, 'filetype' => 'HI']) }}", '_blank');
            })

            $(".hi-container .remove").on('click', function() {
                $(this).parent().find('.upload-file').removeClass('d-none');
                $(this).addClass('d-none')
                $(this).parent().find('.download').addClass('d-none');
            })

            $(".hi-container").on('click', '.rollback', function() {
                $(this).parent().addClass('d-none')
                $(this).parent().parent().find('.remove').removeClass('d-none');
                $(this).parent().parent().find('.download').removeClass('d-none');
            })

            // ei button
            $(".ei-container .download").on('click', function() {
                window.open("{{ route('volunteer.file.download', ['volunteer' => $volunteer->volunt_id, 'filetype' => 'EI']) }}", '_blank');
            })

            $(".ei-container .remove").on('click', function() {
                $(this).parent().find('.upload-file').removeClass('d-none');
                $(this).addClass('d-none')
                $(this).parent().find('.download').addClass('d-none');
            })

            $(".ei-container").on('click', '.rollback', function() {
                $(this).parent().addClass('d-none')
                $(this).parent().parent().find('.remove').removeClass('d-none');
                $(this).parent().parent().find('.download').removeClass('d-none');
            })
        @endif
    </script>
@endsection
