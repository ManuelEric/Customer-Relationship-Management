@extends('layout.main')

@section('title', 'Corporate - Bigdata Platform')

@section('content')
    @php
        $type = ['Corporate', 'Individual Professional', 'Tutoring Center', 'Course Center', 'Agent', 'Community', 'NGO'];
        sort($type);
        
        $partnership_type = ['Market Sharing', 'Program Collaborator', 'Internship', 'External Mentor'];
        sort($partnership_type);
    @endphp
    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('instance/corporate/') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Corporate
        </a>
    </div>

    <div class="row">
        <div class="col-md-4 mb-2">
            <div class="card mb-3">
                <div class="card-body text-center">
                    <img src="{{ asset('img/school.jpg') }}" alt="" class="w-75">
                    <h5>
                        {{ isset($corporate) ? $corporate->corp_name : 'Add New Partner' }}
                    </h5>
                    @if (isset($corporate))
                        <div class="mt-3 d-flex justify-content-center">
                            @if (isset($edit))
                                <a href="{{ url('instance/corporate/' . strtolower($corporate->corp_id)) }}"
                                    class="btn btn-sm btn-outline-info rounded mx-1">
                                    <i class="bi bi-arrow-left"></i> Back
                                </a>
                            @else
                                <a href="{{ url('instance/corporate/' . strtolower($corporate->corp_id) . '/edit') }}"
                                    class="btn btn-sm btn-outline-info rounded mx-1">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            @endif
                            <button type="button"
                                onclick="confirmDelete('instance/corporate', '{{ $corporate->corp_id }}')"
                                class="btn btn-sm btn-outline-danger rounded mx-1">
                                <i class="bi bi-trash2"></i> Delete
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            @if (isset($corporate) && empty($edit))
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="">
                            <h6 class="m-0 p-0">
                                <i class="bi bi-building me-2"></i>
                                Programs
                            </h6>
                        </div>
                        <div class="">
                            <a href="{{ url('program/corporate/'. strtolower($corporate->corp_id) . '/detail/create') }}"
                                class="btn btn-sm btn-outline-primary rounded mx-1">
                                <i class="bi bi-plus"></i>
                            </a>
                        </div>
                    </div>
                    <div class="list-group list-group-flush">
                        @for ($i = 0; $i < 3; $i++)
                            <a href="#" class="list-group-item">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="badge badge-primary w-100 me-2 text-start">
                                        Program Name
                                    </div>
                                    <div class="badge badge-primary">
                                        Success
                                    </div>
                                </div>
                            </a>
                        @endfor
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="">
                            <h6 class="m-0 p-0">
                                <i class="bi bi-building me-2"></i>
                                Joined Event
                            </h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @for ($i = 0; $i < 3; $i++)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="">
                                            <strong>Event Name</strong> <br>
                                            Start Date - End Date
                                        </div>
                                        <div class="">
                                            <a href="#" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="">
                            <h6 class="m-0 p-0">
                                <i class="bi bi-person me-2"></i>
                                PIC
                            </h6>
                        </div>
                        <div class="">
                            <button class="btn btn-sm btn-outline-primary rounded mx-1" data-bs-toggle="modal"
                                data-bs-target="#picForm">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="list-group list-group-flush">
                        @if (isset($pics))
                            @foreach ($pics as $pic)
                                <div class="list-group-item">
                                    <div class="d-flex align-items-center mb-1">
                                        <strong class="text-muted me-2">
                                            {{ $pic->pic_name }}
                                        </strong>
                                        <div class="">
                                            <a href="#"
                                                onclick="returnData('{{ $corporate->corp_id }}','{{ $pic->id }}')"
                                                class="text-decoration-none" data-bs-target="#picForm">
                                                <i class="bi bi-pencil text-warning"></i>
                                            </a>
                                            <a href="#"
                                                onclick="confirmDelete('instance/corporate/{{ $corporate->corp_id }}/detail', '{{ $pic->id }}')"
                                                class="text-decoration-none">
                                                <i class="bi bi-trash2 text-danger"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="badge badge-success me-2">
                                            <i class="bi bi-envelope me-1"></i> {{ $pic->pic_mail }}
                                        </div>
                                        <div class="badge badge-info me-2">
                                            <i class="bi bi-phone me-1"></i> {{ $pic->pic_phone }}
                                        </div>
                                        <a href="{{ $pic->pic_linkedin }}"
                                            class="btn btn-sm btn-outline-primary rounded-circle">
                                            <i class="bi bi-linkedin"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif

        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-building me-2"></i>
                            Partner Detail
                        </h6>
                    </div>
                </div>
                <div class="card-body">
                    <form
                        action="{{ isset($corporate) ? route('corporate.update', ['corporate' => $corporate->corp_id]) : route('corporate.store') }}"
                        method="POST">
                        @csrf
                        @if (isset($corporate))
                            @method('PUT')
                            <input type="hidden" name="corp_id" value="{{ $corporate->corp_id }}">
                        @endif

                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label>Corporate / Partner Name <sup class="text-danger">*</sup></label>
                                <input type="text" name="corp_name"
                                    value="{{ isset($corporate->corp_name) ? $corporate->corp_name : old('corp_name') }}"
                                    class="form-control form-control-sm rounded"
                                    {{ empty($corporate) || isset($edit) ? '' : 'disabled' }}>
                                @error('corp_name')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-2">
                                <label>Industry</label>
                                <input type="text" name="corp_industry"
                                    value="{{ isset($corporate->corp_industry) ? $corporate->corp_industry : old('corp_industry') }}"
                                    class="form-control form-control-sm rounded"
                                    {{ empty($corporate) || isset($edit) ? '' : 'disabled' }}>
                                @error('corp_industry')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-2">
                                <label>Email <sup class="text-danger">*</sup></label>
                                <input type="email" name="corp_mail"
                                    value="{{ isset($corporate->corp_mail) ? $corporate->corp_mail : old('corp_mail') }}"
                                    class="form-control form-control-sm rounded"
                                    {{ empty($corporate) || isset($edit) ? '' : 'disabled' }}>
                                @error('corp_mail')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-2">
                                <label>Phone <sup class="text-danger">*</sup></label>
                                <input type="text" name="corp_phone"
                                    value="{{ isset($corporate->corp_phone) ? $corporate->corp_phone : old('corp_phone') }}"
                                    class="form-control form-control-sm rounded"
                                    {{ empty($corporate) || isset($edit) ? '' : 'disabled' }}>
                                @error('corp_phone')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-2">
                                <label>Instagram</label>
                                <input type="text" name="corp_insta"
                                    value="{{ isset($corporate->corp_insta) ? $corporate->corp_insta : old('corp_insta') }}"
                                    class="form-control form-control-sm rounded"
                                    {{ empty($corporate) || isset($edit) ? '' : 'disabled' }}>
                                @error('corp_insta')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-2">
                                <label>Website <sup class="text-danger">*</sup></label>
                                <input type="text" name="corp_site" placeholder="https://xxxxxx.xxxx"
                                    value="{{ isset($corporate->corp_site) ? $corporate->corp_site : old('corp_site') }}"
                                    class="form-control form-control-sm rounded"
                                    {{ empty($corporate) || isset($edit) ? '' : 'disabled' }}>
                                @error('corp_site')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-2">
                                <label>Region</label>
                                <input type="text" name="corp_region"
                                    value="{{ isset($corporate->corp_region) ? $corporate->corp_region : old('corp_region') }}"
                                    class="form-control form-control-sm rounded"
                                    {{ empty($corporate) || isset($edit) ? '' : 'disabled' }}>
                                @error('corp_region')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-2">
                                <label>Address</label>
                                <textarea name="corp_address" cols="30" rows="10">{{ isset($corporate->corp_address) ? $corporate->corp_address : old('corp_address') }}</textarea>
                            </div>

                            <div class="col-md-4 mb-2">
                                <label for="">Country Type <sup class="text-danger">*</sup></label>
                                <select name="country_type" class="select w-100">
                                    <option data-placeholder="true"></option>
                                    <option value="Indonesia"
                                        {{ (isset($corporate->country_type) && $corporate->country_type == 'Indonesia') || old('country_type') == 'Indonesia' ? 'selected' : null }}>
                                        Indonesia</option>
                                    <option value="Overseas"
                                        {{ (isset($corporate->country_type) && $corporate->country_type == 'Overseas') || old('country_type') == 'Overseas' ? 'selected' : null }}>
                                        Overseas</option>
                                </select>
                                @error('country_type')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-2">
                                <label for="">Type <sup class="text-danger">*</sup></label>
                                <select name="type" class="select w-100">
                                    <option data-placeholder="true"></option>
                                    @for ($i = 0; $i < count($type); $i++)
                                        <option value="{{ $type[$i] }}"
                                            {{ (isset($corporate->type) && $corporate->type == $type[$i]) || old('type') == $type[$i] ? 'selected' : null }}>
                                            {{ $type[$i] }}</option>
                                    @endfor
                                </select>
                                @error('type')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-2">
                                <label for="">Partnership Type </label>
                                <select name="partnership_type" class="select w-100">
                                    <option data-placeholder="true"></option>
                                    @for ($i = 0; $i < count($partnership_type); $i++)
                                        <option value="{{ $partnership_type[$i] }}"
                                            {{ (isset($corporate->partnership_type) && $corporate->partnership_type == $partnership_type[$i]) || old('partnership_type') == $partnership_type[$i] ? 'selected' : null }}>
                                            {{ $partnership_type[$i] }}</option>
                                    @endfor
                                </select>
                                @error('partnership_type')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-2">
                                <label>Note</label>
                                <textarea name="corp_note" cols="30" rows="10">{{ isset($corporate->corp_note) ? $corporate->corp_note : old('corp_note') }}</textarea>
                                @error('corp_note')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            @if (empty($corporate) || isset($edit))
                                <div class="text-end mt-3">
                                    <button class="btn btn-sm btn-primary rounded" type="submit"><i
                                            class="bi bi-save2 me-1"></i> Submit</button>
                                </div>
                            @endif
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    @if (isset($corporate))
        <div class="modal modal-md fade" id="picForm" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="m-0 p-0">
                            <i class="bi bi-plus me-2"></i>
                            Contact Person
                        </h4>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('corporate.detail.store', ['corporate' => $corporate->corp_id]) }}"
                            id="detailForm" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="">Full Name</label>
                                        <input type="text" name="pic_name" id="pic_name"
                                            class="form-control form-control-sm rounded">
                                        @error('name')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="">Email</label>
                                        <input type="email" name="pic_mail" id="pic_mail"
                                            class="form-control form-control-sm rounded">
                                        @error('email')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="">Phone Number</label>
                                        <input type="text" name="pic_phone" id="pic_phone"
                                            class="form-control form-control-sm rounded">
                                        @error('phone')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="">Linkedin</label>
                                        <input type="text" name="pic_linkedin" id="pic_linkedin"
                                            class="form-control form-control-sm rounded">
                                        @error('linkedin')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-3"
                                            data-bs-dismiss="modal">
                                            <i class="bi bi-x me-1"></i>
                                            Cancel
                                        </button>
                                        <button type="submit" class="btn btn-sm btn-primary rounded-3">
                                            <i class="bi bi-save2"></i>
                                            Save
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        // Select2 Modal 
        $(document).ready(function() {
            $('.modal-select').select2({
                dropdownParent: $('#programForm .modal-content'),
                placeholder: "Select value",
                allowClear: true
            });
        });
    </script>
    <script type="text/javascript">
        function returnData(corporate_id, pic_id) {

            $("#detailForm").append('<input type="hidden" name="_method" value="PUT">');
            Swal.showLoading()
            let link = "{{ url('instance/corporate') }}/" + corporate_id + '/detail/' + pic_id

            axios.get(link)
                .then(function(response) {
                    // handle success
                    let data = response.data.data
                    $('#pic_name').val(data.pic_name)
                    $('#pic_mail').val(data.pic_mail)
                    $('#pic_phone').val(data.pic_phone)
                    $('#pic_linkedin').val(data.pic_linkedin)

                    $('#detailForm').attr('action', '{{ url('instance/corporate') }}/' + corporate_id + '/detail/' +
                        data.id)
                    Swal.close()
                    $("#picForm").modal('show')
                })
                .catch(function(error) {
                    // handle error
                    Swal.close()
                    notification(error.response.data.success, error.response.data.message)
                })
        }

        @if (isset($corporate))
            function resetForm() {
                $("#detailForm").trigger('reset');
                $("#detailForm").attr('action',
                    "{{ route('corporate.detail.store', ['corporate' => $corporate->corp_id]) }}")
                $("#detailForm").find('input[name=_method]').remove()
            }
        @endif
    </script>

@endsection
