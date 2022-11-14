@extends('layout.main')

@section('title', 'School - Bigdata Platform')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('instance/school') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> School
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="{{ asset('img/school.jpg') }}" alt="" class="w-75">
                    <h5>
                        {{ isset($school) ? $school->sch_name : 'Add New School' }}
                    </h5>
                    @if (isset($school))
                        <div class="mt-3 d-flex justify-content-center">
                            @if (isset($edit))
                                <a href="{{ url('instance/school/' . $school->sch_id) }}"
                                    class="btn btn-sm btn-outline-info rounded mx-1">
                                    <i class="bi bi-arrow-left"></i> Back
                                </a>
                            @else
                                <a href="{{ url('instance/school/' . $school->sch_id . '/edit') }}"
                                    class="btn btn-sm btn-outline-info rounded mx-1">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            @endif
                            <form action="{{ url('instance/school/' . $school->sch_id) }}" method="POST">
                                @csrf
                                @method('delete')
                                <button class="btn btn-sm btn-outline-danger rounded mx-1" type="submit">
                                    <i class="bi bi-trash2"></i> Delete
                                </button>
                            </form>
                            <button class="btn btn-sm btn-outline-primary rounded mx-1" data-bs-toggle="modal"
                                data-bs-target="#programForm">
                                <i class="bi bi-plus"></i> Add Program
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-building me-2"></i>
                            School Detail
                        </h6>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ url(isset($school) ? 'instance/school/' . $school->sch_id : 'instance/school') }}"
                        method="POST">
                        @csrf
                        @if (isset($school))
                            @method('put')
                        @endif

                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label>School Name <sup class="text-danger">*</sup> </label>
                                    <input type="text" name="sch_name" class="form-control form-control-sm rounded"
                                        value="{{ isset($school->sch_name) ? $school->sch_name : null }}"
                                        {{ empty($school) || isset($edit) ? '' : 'disabled' }}>
                                    @error('sch_name')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label>Type <sup class="text-danger">*</sup></label>
                                    <select name="sch_type" class="select w-100"
                                        {{ empty($school) || isset($edit) ? '' : 'disabled' }}>
                                        <option {{ !isset($school->sch_type) ? 'selected' : null }}>Please select one
                                        </option>
                                        <option value="National"
                                            {{ isset($school->sch_type) && $school->sch_type == 'National' ? 'selected' : null }}>
                                            National
                                        </option>
                                        <option value="International"
                                            {{ isset($school->sch_type) && $school->sch_type == 'International' ? 'selected' : null }}>
                                            International</option>
                                    </select>
                                    @error('sch_type')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label>Curriculum <sup class="text-danger">*</sup></label>
                                    <select name="sch_curriculum" class="select w-100"
                                        {{ empty($school) || isset($edit) ? '' : 'disabled' }}>
                                        <option {{ !isset($school->sch_curriculum) ? 'selected' : null }}>Please select
                                            one
                                        </option>
                                        @foreach ($curriculums as $curriculum)
                                            <option value="{{ $curriculum->name }}"
                                                {{ isset($school->sch_curriculum) && $school->sch_curriculum == $curriculum->name ? 'selected' : null }}>
                                                {{ $curriculum->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('sch_curriculum')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label>Target</label>
                                    <select name="sch_score" class="select w-100"
                                        {{ empty($school) || isset($edit) ? '' : 'disabled' }}>
                                        <option value="7"
                                            {{ isset($school->sch_score) && $school->sch_score == 7 ? 'selected' : null }}>
                                            Up
                                            Market
                                        </option>
                                        <option value="5"
                                            {{ isset($school->sch_score) && $school->sch_score == 5 ? 'selected' : null }}>
                                            Mid
                                            Market
                                        </option>
                                        <option value="3"
                                            {{ isset($school->sch_score) && $school->sch_score == 3 ? 'selected' : null }}>
                                            Low
                                            Market
                                        </option>
                                    </select>
                                    @error('sch_score')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label>Instagram</label>
                                    <input type="text" name="sch_insta" class="form-control form-control-sm rounded"
                                        value="{{ isset($school->sch_insta) ? $school->sch_insta : null }}"
                                        {{ empty($school) || isset($edit) ? '' : 'disabled' }}>
                                    @error('sch_insta')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label>School Mail</label>
                                    <input type="email" name="sch_mail" class="form-control form-control-sm rounded"
                                        value="{{ isset($school->sch_mail) ? $school->sch_mail : null }}"
                                        {{ empty($school) || isset($edit) ? '' : 'disabled' }}>
                                    @error('sch_mail')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label>Telephone</label>
                                    <input type="text" name="sch_phone" class="form-control form-control-sm rounded"
                                        value="{{ isset($school->sch_phone) ? $school->sch_phone : null }}"
                                        {{ empty($school) || isset($edit) ? '' : 'disabled' }}>
                                    @error('sch_phone')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label>City</label>
                                    <input type="text" name="sch_city" class="form-control form-control-sm rounded"
                                        value="{{ isset($school->sch_city) ? $school->sch_city : null }}"
                                        {{ empty($school) || isset($edit) ? '' : 'disabled' }}>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label>Location</label>
                                    <textarea type="text" name="sch_location" {{ empty($school) || isset($edit) ? '' : 'readonly' }}>{{ isset($school->sch_location) ? $school->sch_location : null }}</textarea>
                                    @error('sch_location')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        @if (empty($school) || isset($edit))
                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save"></i>
                                    Submit</button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if (isset($details))
        <div class="card mt-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="">
                    <h6 class="m-0 p-0">
                        <i class="bi bi-building me-2"></i>
                        Teacher/Counselor
                    </h6>
                </div>
                <div class="">
                    <a href="{{ url('instance/school/' . $school->sch_id . '/detail/create') }}">
                        <button class="btn btn-sm btn-info">
                            <i class="bi bi-plus"></i> Add
                        </button>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fullname</th>
                            <th>Email</th>
                            <th>Grade</th>
                            <th>Position</th>
                            <th>Phone</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($details as $detail)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $detail->schdetail_fullname }}</td>
                                <td>{{ $detail->schdetail_email }}</td>
                                <td>{{ $detail->schdetail_grade }}</td>
                                <td>{{ $detail->schdetail_position }}</td>
                                <td>{{ $detail->schdetail_phone }}</td>
                                <td class="d-flex text-center justify-content-end">
                                    <a class="btn btn-sm btn-warning mx-1"
                                        href="{{ url('instance/school/' . $detail->sch_id . '/detail/' . $detail->schdetail_id . '/edit') }}"><i
                                            class="bi bi-pencil"></i>
                                    </a>
                                    <form
                                        action="{{ url('instance/school/' . $detail->sch_id . '/detail/' . $detail->schdetail_id) }}"
                                        method="POST">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-sm btn-danger" type="submit">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    @endif

    <!-- Modal -->
    <div class="modal modal-md fade" id="programForm" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="m-0 p-0">
                        <i class="bi bi-plus me-2"></i>
                        Add Program
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-2">
                                <label for="">Program Name</label>
                                <select class="modal-select w-100" name="program_id">
                                    <option data-placeholder="true"></option>
                                    @for ($i = 0; $i < 5; $i++)
                                        <option value="{{ $i }}">Test {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                <label for="">Lead Source</label>
                                <select class="modal-select w-100" name="program_id">
                                    <option data-placeholder="true"></option>
                                    @for ($i = 0; $i < 5; $i++)
                                        <option value="{{ $i }}">Test {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                <label for="">PIC</label>
                                <select class="modal-select w-100" name="program_id">
                                    <option data-placeholder="true"></option>
                                    @for ($i = 0; $i < 5; $i++)
                                        <option value="{{ $i }}">Test {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                <label for="">First Discuss</label>
                                <input type="date" name="" class="form-control form-control-sm rounded">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                <label for="">Planned Follow Up</label>
                                <input type="date" name="" class="form-control form-control-sm rounded">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-2">
                                <label for="">Notes</label>
                                <textarea name="" id="" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-sm btn-outline-danger rounded-3" data-bs-dismiss="modal">
                                    <i class="bi bi-x me-1"></i>
                                    Cancel
                                </button>
                                <button class="btn btn-sm btn-primary rounded-3">
                                    <i class="bi bi-save2"></i>
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

@endsection
