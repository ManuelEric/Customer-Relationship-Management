@extends('layout.main')

@section('title', 'University - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('instance/university') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> University
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body text-center">
                    <img src="{{ asset('img/school.jpg') }}" alt="" class="w-75">

                    @if (isset($university))
                        <div class="mt-3 d-flex justify-content-center">
                            @if (isset($edit))
                                <a href="{{ url('instance/university/' . strtolower($university->univ_id)) }}"
                                    class="btn btn-sm btn-outline-info rounded mx-1">
                                    <i class="bi bi-arrow-left"></i> Back
                                </a>
                            @else
                                <a href="{{ url('instance/university/' . strtolower($university->univ_id) . '/edit') }}"
                                    class="btn btn-sm btn-outline-info rounded mx-1">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            @endif
                            <button type="button"
                                onclick="confirmDelete('instance/university', '{{ $university->univ_id }}')"
                                class="btn btn-sm btn-outline-danger rounded mx-1">
                                <i class="bi bi-trash2"></i> Delete
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            @if (isset($university) && empty($edit))
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
            @endif
        </div>
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-building me-2"></i>
                            University Detail
                        </h6>
                    </div>
                </div>
                <div class="card-body">
                    <form
                        action="{{ url(isset($university) ? 'instance/university/' . $university->univ_id : 'instance/university') }}"
                        method="POST" id="formUniv">
                        @csrf
                        @if (isset($university))
                            @method('put')
                        @endif
                        <div class="put"></div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">
                                        University Name
                                    </label>
                                    <input type="text" name="univ_name" class="form-control form-control-sm rounded"
                                        value="{{ isset($university) ? $university->univ_name : old('univ_name') }}"
                                        {{ empty($university) || isset($edit) ? '' : 'disabled' }}>
                                    @error('univ_name')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Email
                                    </label>
                                    <input type="text" name="univ_email" class="form-control form-control-sm rounded"
                                        value="" {{ empty($university) || isset($edit) ? '' : 'disabled' }}>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">
                                        Phone Number
                                    </label>
                                    <input type="text" name="univ_phone" class="form-control form-control-sm rounded"
                                        value="" {{ empty($university) || isset($edit) ? '' : 'disabled' }}>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">
                                        Country
                                    </label>
                                    <div class="w-100">
                                        <select name="univ_country" id="univ_country" class="select w-100"
                                            {{ empty($university) || isset($edit) ? '' : 'disabled' }}>
                                            <option data-placeholder="true"></option>
                                            @foreach ($countries as $item)
                                                <option value="{{ $item->name }}"
                                                    {{ (isset($university) ? $university->univ_country : old('univ_country')) == $item->name ? 'selected' : '' }}>
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('univ_country')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">
                                        Address
                                    </label>
                                    <textarea name="univ_address" id="univ_address" class="form-control form-control-sm rounded" style="height: 300px">{{ isset($university) ? $university->univ_address : old('univ_address') }}</textarea>
                                    @error('univ_address')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            @if (empty($university) || isset($edit))
                                <div class="col-md-12 mt-2">
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="bi bi-save2 me-1"></i>
                                            Submit</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            @if (isset($university) && empty($edit))
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="">
                            <h6 class="m-0 p-0">
                                <i class="bi bi-building me-2"></i>
                                Contact Person
                            </h6>
                        </div>
                        <div class="">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#picForm">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Position</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>No</td>
                                    <td>Full Name</td>
                                    <td>Email</td>
                                    <td>Phone Number</td>
                                    <td>Position</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                                            data-bs-target="#picForm">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash2"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>


                {{-- Modal Add & Update Contact Person --}}
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
                                <form action="#" method="POST" id="picAction">
                                    @csrf
                                    <div class="put"></div>
                                    <div class="row mb-2">
                                        <div class="col-md-6 mb-2">
                                            <label>Fullname <sup class="text-danger">*</sup></label>
                                            <input type="text" name="pic_name"
                                                class="form-control form-control-sm rounded" id="cp_fullname">
                                            @error('pic_name')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label>E-mail <sup class="text-danger">*</sup></label>
                                            <input type="email" name="pic_email"
                                                class="form-control form-control-sm rounded" id="cp_mail">
                                            @error('pic_email')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label>Phone Number <sup class="text-danger">*</sup></label>
                                            <input type="text" name="pic_phone"
                                                class="form-control form-control-sm rounded" id="cp_phone">
                                            @error('pic_phone')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label>Position <sup class="text-danger">*</sup></label>
                                            <div class="classPosition">
                                                <select name="pic_position" class="modal-select w-100"
                                                    style="display: none !important" id="selectPosition"
                                                    onchange="changePosition()">
                                                    <option data-placeholder="true"></option>
                                                    <option value="Admissions Advisor">
                                                        Admissions Advisor</option>
                                                    <option value="Former Admission Officer">
                                                        Former Admission Officer</option>
                                                    <option value="new">
                                                        New Position</option>
                                                </select>
                                            </div>

                                            <div class="d-flex align-items-center d-none" id="inputPosition">
                                                <input type="text" name="pic_position"
                                                    class="form-control form-control-sm rounded">
                                                <div class="float-end cursor-pointer" onclick="resetPosition()">
                                                    <b>
                                                        <i class="bi bi-x text-danger"></i>
                                                    </b>
                                                </div>
                                            </div>

                                            @error('pic_position')
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
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function selectModal() {
            $('.modal-select').select2({
                dropdownParent: $('#picForm .modal-content'),
                placeholder: "Select value",
                containerCssClass: "show-hide",
                allowClear: true
            });
        }

        function changePosition() {
            let position = $('#selectPosition').val()
            if (position == 'new') {
                $('.classPosition').addClass('d-none')
                $('#inputPosition').removeClass('d-none')
                $('#inputPosition input').focus()
            } else {
                $('#inputPosition').addClass('d-none')
                $('.classPosition').removeClass('d-none')
            }
        }

        function resetPosition() {
            $('.classPosition').removeClass('d-none')
            $('#selectPosition').val(null).trigger('change')
            $('#inputPosition').addClass('d-none')
            $('#inputPosition input').val(null)
        }

        $(document).ready(function() {
            selectModal()
        });
    </script>
@endsection
