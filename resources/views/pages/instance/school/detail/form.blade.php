@extends('layout.main')

@section('title', 'School - Bigdata Platform')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('instance/school/' . $school_id) }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> School Detail
        </a>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-person me-2"></i>
                            Contact Person
                        </h6>
                    </div>
                    @if (!isset($schoolDetail))
                        <div class="">
                            <div style="float:right"><button onclick="addCP()" class="btn btn-sm btn-info rounded">Add
                                    Contact
                                    Person</button></div>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <form id="storeCP"
                        action="{{ url(isset($schoolDetail) ? 'instance/school/' . $school_id . '/detail/' . $schoolDetail->schdetail_id : 'instance/school/' . $school_id . '/detail') }}"
                        method="POST">
                        @csrf
                        @if (isset($schoolDetail))
                            @method('put')
                            <input type="hidden" readonly name="schdetail_id" value="{{ $schoolDetail->schdetail_id }}">
                        @endif

                        <input type="hidden" readonly name="sch_id" value="{{ $school_id }}">
                        <div id="multiplePIC">
                            <div class="row mb-2">
                                <div class="col-md-3">
                                    <label>Fullname <sup class="text-danger">*</sup></label>
                                    <input type="text" name="schdetail_name[]"
                                        class="form-control form-control-sm rounded"
                                        value="{{ isset($schoolDetail->schdetail_fullname) ? $schoolDetail->schdetail_fullname : null }}">
                                    @error('schdetail_name.0')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label>E-mail <sup class="text-danger">*</sup></label>
                                    <input type="email" name="schdetail_mail[]"
                                        class="form-control form-control-sm rounded"
                                        value="{{ isset($schoolDetail->schdetail_email) ? $schoolDetail->schdetail_email : null }}">
                                    @error('schdetail_mail.0')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label>Phone Number <sup class="text-danger">*</sup></label>
                                    <input type="text" name="schdetail_phone[]"
                                        class="form-control form-control-sm rounded"
                                        value="{{ isset($schoolDetail->schdetail_phone) ? $schoolDetail->schdetail_phone : null }}">
                                    @error('schdetail_phone.0')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label>Status <sup class="text-danger">*</sup></label>
                                    <select name="schdetail_position[]" class="form-select form-select-sm rounded">
                                        <option {{ !isset($schoolDetail->schdetail_position) ? 'selected' : null }}>
                                            Please
                                            select one
                                        </option>
                                        <option value="Principal"
                                            {{ isset($schoolDetail->schdetail_position) && $schoolDetail->schdetail_position == 'Principal' ? 'selected' : null }}>
                                            Principal</option>
                                        <option value="Counselor"
                                            {{ isset($schoolDetail->schdetail_position) && $schoolDetail->schdetail_position == 'Counselor' ? 'selected' : null }}>
                                            Counselor</option>
                                        <option value="Teacher"
                                            {{ isset($schoolDetail->schdetail_position) && $schoolDetail->schdetail_position == 'Teacher' ? 'selected' : null }}>
                                            Teacher</option>
                                        <option value="Marketing"
                                            {{ isset($schoolDetail->schdetail_position) && $schoolDetail->schdetail_position == 'Marketing' ? 'selected' : null }}>
                                            Marketing</option>
                                    </select>
                                    @error('schdetail_position.0')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label>School Grade <sup class="text-danger">*</sup></label>
                                    <select name="schdetail_grade[]" class="form-select form-select-sm rounded">
                                        <option {{ !isset($schoolDetail->schdetail_grade) ? 'selected' : null }}>
                                            Please
                                            select
                                            one</option>
                                        <option value="Middle School"
                                            {{ isset($schoolDetail->schdetail_grade) && $schoolDetail->schdetail_grade == 'Middle School' ? 'selected' : null }}>
                                            Middle School</option>
                                        <option value="High School"
                                            {{ isset($schoolDetail->schdetail_grade) && $schoolDetail->schdetail_grade == 'High School' ? 'selected' : null }}>
                                            High School</option>
                                        <option value="Middle School & High School"
                                            {{ isset($schoolDetail->schdetail_grade) && $schoolDetail->schdetail_grade == 'Middle School & High School' ? 'selected' : null }}>
                                            Middle School & High School</option>
                                    </select>
                                    @error('schdetail_grade.0')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-3">
                            <button type="submit" form="storeCP" class="btn btn-sm btn-primary rounded">
                                <i class="bi bi-save2 me-2"></i> Save Contact
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" async defer>
        function addCP() {
            $("#multiplePIC").append("<div class='row mb-2'>" +
                "<div class='col-md-3'>" +
                "<label>Fullname <sup class='text-danger'>*</sup></label>" +
                '<input type="text" name="schdetail_name[]" class="form-control form-control-sm rounded">' +
                "</div>" +
                "<div class='col-md-2'>" +
                "<label>E-mail <sup class='text-danger'>*</sup></label>" +
                '<input type="email" name="schdetail_mail[]" class="form-control form-control-sm rounded">' +
                "</div>" +
                "<div class='col-md-2'>" +
                "<label>Phone Number <sup class='text-danger'>*</sup></label>" +
                '<input type="text" name="schdetail_phone[]" class="form-control form-control-sm rounded">' +
                "</div>" +
                "<div class='col-md-2'>" +
                "<label>Status <sup class='text-danger'>*</sup></label>" +
                '<select name="schdetail_position[]" class="form-select form-select-sm rounded">' +
                "<option>Please select one</option>" +
                '<option value="Principal">Principal</option>' +
                '<option value="Counselor">Counselor</option>' +
                '<option value="Teacher">Teacher</option>' +
                '<option value="Marketing">Marketing</option>' +
                "</select>" +
                "" +
                "</div>" +
                "<div class='col-md-3'>" +
                "<label>School Grade <sup class='text-danger'>*</sup></label>" +
                '<select name="schdetail_grade[]" class="form-select form-select-sm rounded">' +
                "<option>Please select one</option>" +
                '<option value="Middle School">Middle School</option>' +
                '<option value="High School">High School</option>' +
                '<option value="Middle School & High School">Middle School & High School</option>' +
                "</select>" +
                "</div>" +
                "</div>");
        }
    </script>
@endsection
