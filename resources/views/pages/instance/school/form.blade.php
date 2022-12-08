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
            <div class="card mb-3">
                <div class="card-body text-center">
                    <img src="{{ asset('img/school.jpg') }}" alt="" class="w-75">
                    <h5>
                        {{ isset($school) ? $school->sch_name : 'Add New School' }}
                    </h5>
                    @if (isset($school))
                        <div class="mt-3 d-flex justify-content-center">
                            @if (isset($edit))
                                <a href="{{ url('instance/school/' . strtolower($school->sch_id)) }}"
                                    class="btn btn-sm btn-outline-info rounded mx-1">
                                    <i class="bi bi-arrow-left"></i> Back
                                </a>
                            @else
                                <a href="{{ url('instance/school/' . strtolower($school->sch_id) . '/edit') }}"
                                    class="btn btn-sm btn-outline-info rounded mx-1">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            @endif
                            <button type="button" onclick="confirmDelete('instance/school', '{{ $school->sch_id }}')"
                                class="btn btn-sm btn-outline-danger rounded mx-1">
                                <i class="bi bi-trash2"></i> Delete
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            @if (isset($school) && empty($edit))
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="">
                            <h6 class="m-0 p-0">
                                <i class="bi bi-building me-2"></i>
                                Programs
                            </h6>
                        </div>
                        <div class="">
                            <a href="{{ url('program/school/'. strtolower($school->sch_id)) .'/detail/create' }}"
                                class="btn btn-sm btn-outline-primary rounded mx-1">
                                <i class="bi bi-plus"></i>
                            </a>
                        </div>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse ($schoolPrograms as $schoolProgram)
                            <div class="list-group-item">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="text-start">
                                        <div class="">
                                            {{ $schoolProgram->program->prog_program }}
                                        </div>
                                        <small>
                                            @if ($schoolProgram->status == 0)
                                                Pending
                                            @elseif ($schoolProgram->status == 1)
                                                Success
                                            @elseif ($schoolProgram->status == 2)
                                                Denied
                                            @endif
                                        </small>
                                    </div>
                                    <a href="{{ url('program/school/'. strtolower($school->sch_id) .'/detail/'. $schoolProgram->id) }}" class="fs-6 text-warning">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                        <div class="list-group-item">
                                <div class="text-center">
                                   
                                <small>

                                    No have data
                                </small>
                                </div>
                        </div>
                        @endforelse
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
                                        value="{{ isset($school->sch_name) ? $school->sch_name : old('sch_name') }}"
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
                                        <option data-placeholder="true"></option>
                                        <option value="National"
                                            {{ (isset($school->sch_type) && $school->sch_type == 'National') || old('sch_type') == 'National' ? 'selected' : null }}>
                                            National
                                        </option>
                                        <option value="International"
                                            {{ (isset($school->sch_type) && $school->sch_type == 'International') || old('sch_type') == 'International' ? 'selected' : null }}>
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
                                        <option data-placeholder="true"></option>
                                        @foreach ($curriculums as $curriculum)
                                            <option value="{{ $curriculum->name }}"
                                                {{ (isset($school->sch_curriculum) && $school->sch_curriculum == $curriculum->name) || old('sch_curriculum') == $curriculum->name ? 'selected' : null }}>
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
                                        <option data-placeholder="true"></option>
                                        <option value="7"
                                            {{ (isset($school->sch_score) && $school->sch_score == 7) || old('sch_score') == 7 ? 'selected' : null }}>
                                            Up
                                            Market
                                        </option>
                                        <option value="5"
                                            {{ (isset($school->sch_score) && $school->sch_score == 5) || old('sch_score') == 5 ? 'selected' : null }}>
                                            Mid
                                            Market
                                        </option>
                                        <option value="3"
                                            {{ (isset($school->sch_score) && $school->sch_score == 3) || old('sch_score') == 3 ? 'selected' : null }}>
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
                                        value="{{ isset($school->sch_insta) ? $school->sch_insta : old('sch_insta') }}"
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
                                        value="{{ isset($school->sch_mail) ? $school->sch_mail : old('sch_mail') }}"
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
                                        value="{{ isset($school->sch_phone) ? $school->sch_phone : old('sch_phone') }}"
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
                                        value="{{ isset($school->sch_city) ? $school->sch_city : old('sch_city') }}"
                                        {{ empty($school) || isset($edit) ? '' : 'disabled' }}>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label>Location</label>
                                    <textarea type="text" name="sch_location" {{ empty($school) || isset($edit) ? '' : 'disabled' }}>{{ isset($school->sch_location) ? $school->sch_location : null }}</textarea>
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
                            <button class="btn btn-sm btn-outline-primary" onclick="resetForm()" data-bs-toggle="modal"
                                data-bs-target="#picForm">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
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
                                                <button type="button" class="btn btn-sm btn-outline-warning mx-1"
                                                    data-bs-toggle="modal" data-bs-target="#picForm"
                                                    onclick="getPIC('{{ url('instance/school/' . $detail->sch_id . '/detail/' . $detail->schdetail_id . '/edit') }}')"><i
                                                        class="bi bi-pencil"></i>
                                                </button>
                                                <button type="button"
                                                    onclick="confirmDelete('instance/school/{{ $detail->sch_id }}/detail', '{{ $detail->schdetail_id }}')"
                                                    class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>


    @if (isset($school))
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
                        <form action="{{ url('instance/school/' . $school->sch_id . '/detail') }}" method="POST"
                            id="picAction">
                            @csrf
                            <div class="put"></div>
                            <input type="hidden" readonly name="sch_id" value="{{ $school->sch_id }}">
                            <div class="row mb-2">
                                <div class="col-md-6 mb-2">
                                    <label>Fullname <sup class="text-danger">*</sup></label>
                                    <input type="text" name="schdetail_name[]"
                                        class="form-control form-control-sm rounded" id="cp_fullname">
                                    @error('schdetail_name.0')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>E-mail <sup class="text-danger">*</sup></label>
                                    <input type="email" name="schdetail_mail[]"
                                        class="form-control form-control-sm rounded" id="cp_mail">
                                    @error('schdetail_mail.0')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>Phone Number <sup class="text-danger">*</sup></label>
                                    <input type="text" name="schdetail_phone[]"
                                        class="form-control form-control-sm rounded" id="cp_phone">
                                    @error('schdetail_phone.0')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>Status <sup class="text-danger">*</sup></label>
                                    <select name="schdetail_position[]" class="modal-select w-100" id="cp_status">
                                        <option data-placeholder="true"></option>
                                        <option value="Principal">
                                            Principal</option>
                                        <option value="Counselor">
                                            Counselor</option>
                                        <option value="Teacher">
                                            Teacher</option>
                                        <option value="Marketing">
                                            Marketing</option>
                                    </select>
                                    @error('schdetail_position.0')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-2">
                                    <label>School Grade <sup class="text-danger">*</sup></label>
                                    <select name="schdetail_grade[]" class="modal-select w-100" id="cp_grade">
                                        <option data-placeholder="true"></option>
                                        <option value="Middle School">
                                            Middle School</option>
                                        <option value="High School">
                                            High School</option>
                                        <option value="Middle School & High School">
                                            Middle School & High School</option>
                                    </select>
                                    @error('schdetail_grade.0')
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

    <script>
        // Select2 Modal 
        $(document).ready(function() {
            $('.modal-program').select2({
                dropdownParent: $('#programForm .modal-content'),
                placeholder: "Select value",
                allowClear: true
            });
        });

        $(document).ready(function() {
            $('.modal-select').select2({
                dropdownParent: $('#picForm .modal-content'),
                placeholder: "Select value",
                allowClear: true
            });
        });
    </script>

    <script>
        @if (isset($school))
            function resetForm() {
                $('#cp_fullname').val(null)
                $('#cp_mail').val(null)
                $('#cp_grade').val(null).trigger('change')
                $('#cp_phone').val(null)
                $('#cp_status').val(null).trigger('change')
                $('.put').html('');
                $('#picAction').attr('action',
                    '{{ isset($school) ? url('instance/school/' . $school->sch_id . '/detail') : url('instance/school/') }}'
                )
            }
        @endif


        function getPIC(link) {
            axios.get(link)
                .then(function(response) {
                    // handle success
                    let id = response.data.school_id
                    let cp = response.data.schoolDetail
                    $('#cp_fullname').val(cp.schdetail_fullname)
                    $('#cp_mail').val(cp.schdetail_email)
                    $('#cp_grade').val(cp.schdetail_grade).trigger('change')
                    $('#cp_phone').val(cp.schdetail_phone)
                    $('#cp_status').val(cp.schdetail_position).trigger('change')

                    let url = "{{ url('instance/school/') }}/" + id + "/detail/" + cp.schdetail_id
                    $('#picAction').attr('action', url)

                    let html =
                        '@method('put')' +
                        '<input type="hidden" readonly name="schdetail_id" value="' + cp.schdetail_id + '">'
                    $('.put').html(html);


                    // console.log(url)
                })
                .catch(function(error) {
                    // handle error
                    console.log(error);
                })
        }

        // Make a request for a user with a given ID
    </script>

@endsection
