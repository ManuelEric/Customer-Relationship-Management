@extends('layout.main')

@section('title', 'Employee - Bigdata Platform')

@section('content')
@php
    $departmentId = [];
    $departmentThisUser = null;
    if (isset($user) && $typeInfo = $user->user_type()->where('tbl_user_type_detail.status', 1)->first()) 
        $departmentId = $typeInfo->pivot->department_id;
        $departmentThisUser = $departments->where('id', $departmentId)->first();
@endphp

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('user/employee') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Employee
        </a>
    </div>

    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card rounded mb-3">
                <div class="card-body">
                    <img loading="lazy"  src="{{ asset('img/mentee.webp') }}" class="w-100">
                    <h4 class="text-center">
                        {{ isset($user) ? "Edit" : "Add" }}
                        {{ ucfirst(Request::route('user_role')) }}</h4>

                    <div class="text-center mt-2">
                        @if (isset($user))
                            <button @class([
                                'btn btn-sm btn-success',
                                'd-none' => $user->active == 1,
                            ]) id="activate-user" style="font-size:12px;">
                                <i class="bi bi-check"></i>
                                Activate</button>
                                
                            <button @class([
                                'btn btn-sm btn-outline-danger',
                                'd-none' => $user->active == 0,
                            ]) id="deactivate-user" style="font-size:12px;">
                                <i class="bi bi-x"></i>
                                Deactivate</button>

                            <button id="set-password" class="btn btn-sm btn-warning" style="font-size:12px;">
                                <i class="bi bi-key"></i> 
                                Set Password
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @if (isset($user) && count($user->user_type) > 0)
            <div class="card rounded mb-3">
                <div class="card-header">
                    <h5 class="p-0 m-0">Employee Type</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach ($user->user_type()->orderBy('created_at', 'desc')->get() as $type)
                            <div @class([
                                'd-flex justify-content-between align-items-center',
                                'list-group-item',
                                'bg-success text-light' => $type->pivot->status == 1
                            ])>
                                <div>
                                    {{ $type->type_name }}
                                    <div class="">
                                        {{ date('d M Y', strtotime($type->pivot->start_date)) }}
                                        @if ($type->pivot->end_date != NULL)
                                        -
                                        {{ date('d M Y', strtotime($type->pivot->end_date)) }}
                                        @else
                                            - until now
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    @if ($type->pivot->status == 1)
                                    <button onclick="confirmDelete('{{ 'user/'.Request::route('user_role').'/'.Request::route('user') }}', '{{ $type->pivot->id }}')" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            @if (isset($user) && $user->roles()->where('role_name', 'mentor')->count() > 0)
            <div class="card rounded mb-3">
                <div class="card-header">
                    <h5 class="p-0 m-0">Mentees</h5>
                </div>
                <div class="card-body text-center">
                    <h2>{{ $user->mentorClient()->wherePivot('status', 1)->count() }}</h2>
                </div>
            </div>
            @endif
        </div>
        <div class="col-md-9">
            <div class="card rounded mb-3">
                <div class="card-header d-flex align-items-center">
                    <h4 class="m-0 p-0">Employee Detail</h4>
                </div>
                <div class="card-body">
                    <form action="{{ isset($user) ? route('user.update', ['user_role' => Request::route('user_role'), 'user' => $user->id]) : route('user.store', ['user_role' => Request::route('user_role')]) }}" method="POST" enctype="multipart/form-data" id="user-form">
                        @csrf
                        @if (isset($user))
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="">First Name <sup class="text-danger">*</sup></label>
                                <input type="text" name="first_name" value="{{ isset($user->first_name) ? $user->first_name : old('first_name') }}"
                                    class="form-control form-control-sm rounded">
                                @error('first_name')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="">Last Name</label>
                                <input type="text" name="last_name" value="{{ isset($user->last_name) ? $user->last_name : old('first_name') }}"
                                    class="form-control form-control-sm rounded">
                                @error('last_name')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="">Email <sup class="text-danger">*</sup></label>
                                <input type="email" name="email" value="{{ isset($user->email) ? $user->email : old('email') }}"
                                    class="form-control form-control-sm rounded">
                                @error('email')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="">Phone Number <sup class="text-danger">*</sup></label>
                                <input type="text" name="phone" value="{{ isset($user->phone) ? $user->phone : old('phone') }}"
                                    class="form-control form-control-sm rounded">
                                @error('phone')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="">Emergency Contact {!! Request::route('user_role') == 'employee' || Request::route('user_role') == 'admin' ? '<sup class="text-danger">*</sup>' : '' !!}</label>
                                <input type="text" name="emergency_contact" value="{{ isset($user->emergency_contact) ? $user->emergency_contact : old('emergency_contact') }}"
                                    class="form-control form-control-sm rounded">
                                @error('emergency_contact')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="">Date of Birth <sup class="text-danger">*</sup></label>
                                <input type="date" name="datebirth" value="{{ isset($user->datebirth) ? $user->datebirth : old('datebirth') }}" 
                                    class="form-control form-control-sm rounded">
                                @error('datebirth')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="">Address <sup class="text-danger">*</sup></label>
                                <textarea name="address">{{ isset($user->address) ? $user->address : old('address') }}</textarea>
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
                                @include('pages.user.employee.form-detail.subject')
                            </div>
                            <div class="col-md-12 mb-3">
                                @include('pages.user.employee.form-detail.attachment')
                            </div>
                            
                        </div>
                    </form>
                    <div class="col-md-12 text-end">
                        <button type="submit" form="user-form" class="btn btn-sm btn-primary">
                            <i class="bi bi-save me-2"></i> Submit
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Modal for deactive --}}
    @include('pages.user.employee.form-detail.deactive')

    <script type="text/javascript">
        $("#role").change(function(){
            var role = $(this).val();
            if(Object.values(role).indexOf('4') > -1){
                $("#subject").removeClass('d-none');
            }
        });

        $('.modal-select').select2({
            dropdownParent: $('#modalDeactive .modal-content'),
            placeholder: "Select value",
            allowClear: true
        });

        @if (isset($user))
        $("#deactivate-user").on('click', function() {
            changeStatus('deactivate')
        })

        $("#activate-user").on('click', function() {
            changeStatus('activate')
        })

        function changeStatus(status)
        {
            var myModal = new bootstrap.Modal(document.getElementById('deactiveUser'))
            switch (status) {
                case 'activate':
                    myModal.show()
                    $("#deactivate-user--app-3103").unbind('click');
                    $("#deactivate-user--app-3103").bind('click', function() {
                        deactivateUser(status)
                    })
                    break;
                
                case 'deactivate':
                    // show modal 
                    $('#modalDeactive').modal('show');
                    
                    $("#btn-deactive").unbind('click');
                    $('#btn-deactive').bind('click', function(){
                        deactivateUser(status)
                    })
                    break;
            }            
            
        }

        function deactivateUser(status) {
            var deactivated_at = $('#deactivated_at').val();
            var pic_id = $('#pic_id').val();
            showLoading()
            
            axios.post('{{ route('user.update.status', ['user_role' => Request::route('user_role'), 'user' => $user->id]) }}', {
                    _token: '{{ csrf_token() }}',
                    params: {
                        new_status: status,
                        deactivated_at: deactivated_at,
                        new_pic: pic_id ?? null,
                        department: '{{ $departmentThisUser != null ? $departmentThisUser->dept_name : "" }}'
                    },
                })
                .then((response) => {
                    console.log(response);
                    
                    Swal.close()
                    $('#modalDeactive').modal('hide');
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

        $("#set-password").on('click', function() {
               Swal.showLoading()
                axios
                    .get(
                        '{{ route('user.set.password', ['user_role' => Request::route('user_role'), 'user' => $user->id]) }}')
                    .then(response => {
                        swal.close()
                        console.log(response);
                        notification('success', response.data.message)
                    })
                    .catch(error => {
                        Swal.close()
                        notification('error', 'Something went wrong. Please try again or contact the administrator.')
                    })
        })
        @endif

        @if (isset($user))
        // curriculum vitae button
        $(".curriculum-vitae-container .download").on('click', function() {
            window.open("{{ route('user.file.download', ['user_role' => Request::route('user_role'), 'user' => $user->id, 'filetype' => 'CV']) }}", '_blank');
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
            window.open("{{ route('user.file.download', ['user_role' => Request::route('user_role'), 'user' => $user->id, 'filetype' => 'ID']) }}", '_blank');
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
            window.open("{{ route('user.file.download', ['user_role' => Request::route('user_role'), 'user' => $user->id, 'filetype' => 'TX']) }}", '_blank');
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
            window.open("{{ route('user.file.download', ['user_role' => Request::route('user_role'), 'user' => $user->id, 'filetype' => 'HI']) }}", '_blank');
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
            window.open("{{ route('user.file.download', ['user_role' => Request::route('user_role'), 'user' => $user->id, 'filetype' => 'EI']) }}", '_blank');
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
