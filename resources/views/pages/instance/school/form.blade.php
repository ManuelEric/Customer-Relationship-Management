@extends('layout.main')

@section('title', 'School ')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Schools</a></li>
    <li class="breadcrumb-item active" aria-current="page">Form School</li>
@endsection
@section('content')

    @php
        $error_pic = $error_visit = false;
    @endphp
    @if (
        $errors->has('schdetail_name.*') ||
            $errors->has('schdetail_mail.*') ||
            $errors->has('schdetail_phone.*') ||
            $errors->has('schdetail_position.*') ||
            $errors->has('schdetail_grade.*'))
        @php
            $error_pic = true;
        @endphp
    @elseif ($errors->first('internal_pic') || $errors->first('school_pic') || $errors->first('visit_date'))
        @php
            $error_visit = true;
        @endphp
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body text-center">
                    <img src="{{ asset('img/school.jpg') }}" alt="" class="w-75">
                    <h5>
                        {{ isset($school) ? $school->sch_name : 'Add New School' }}
                    </h5>
                    @if(isset($school))
                    
                        <p>
                            <a class="text-primary text-decoration-none cursor-pointer" target="_blank" href="{{ url('client/student?sch='.$school->sch_name) }}">
                                {{ $school->client->count() }} Students Connected
                            </a> 
                            | 
                            <a class="text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#aliasModal">
                                {{ $aliases->count() }} Aliases
                            </a>
                        </p>
                    @endif
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
                            <a href="{{ url('program/school/' . strtolower($school->sch_id)) . '/detail/create' }}"
                                class="btn btn-sm btn-outline-primary rounded mx-1">
                                <i class="bi bi-plus"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-2">
                        @if (count($schoolPrograms) == 0)
                            <div class="list-group-item text-center py-3">
                                No Program Yet
                            </div>
                        @endif
                        <ul class="list-group">
                            @foreach ($schoolPrograms as $schoolProgram)
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="text-start">
                                            <div class="" style="margin-bottom: -5px">
                                                {{ $schoolProgram->program->prog_program }}
                                            </div>
                                            <small>
                                                @if ($schoolProgram->status == 0)
                                                    Pending
                                                @elseif ($schoolProgram->status == 1)
                                                    Success
                                                @elseif ($schoolProgram->status == 2)
                                                    Rejected
                                                @elseif ($schoolProgram->status == 3)
                                                    Refund
                                                @elseif ($schoolProgram->status == 4)
                                                    Accepted
                                                @elseif ($schoolProgram->status == 5)
                                                    Cancel
                                                @endif
                                            </small>
                                        </div>
                                        <a href="{{ url('program/school/' . strtolower($school->sch_id) . '/detail/' . $schoolProgram->id) }}"
                                            class="fs-6 text-warning">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                @include('pages.instance.school.detail.school-visit')

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
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>School Name <sup class="text-danger">*</sup> </label>
                                    @if (!isset($school))
                                    <select name="choosen_school" class="w-100" @disabled(!empty($school) && !isset($edit))>
                                        
                                    </select>
                                    @endif
                                    <input type="text" name="sch_name" @class([
                                        'form-control',
                                        'form-control-sm',
                                        'rounded',
                                        'd-none' => !isset($school)
                                    ])
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
                                        <option value="International" @selected(isset($school->sch_type) && $school->sch_type == 'International')
                                            @selected(old('sch_type') == 'International')>
                                            International</option>
                                        <option value="National" @selected(isset($school->sch_type) && $school->sch_type == 'National') @selected(old('sch_type') == 'National')>
                                            National
                                        </option>
                                        <option value="National_plus" @selected(isset($school->sch_type) && $school->sch_type == 'National_plus')
                                            @selected(old('sch_type') == 'National_plus')>
                                            National+</option>
                                        <option value="National_private" @selected(isset($school->sch_type) && $school->sch_type == 'National_private')
                                            @selected(old('sch_type') == 'National_private')>
                                            National Private</option>
                                        <option value="Home_schooling" @selected(isset($school->sch_type) && $school->sch_type == 'Home_schooling')
                                            @selected(old('sch_type') == 'Home_schooling')>
                                            Home Schooling</option>
                                    </select>
                                    @error('sch_type')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label>Target <sup class="text-danger">*</sup></label>
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
                                    <label>Curriculum <sup class="text-danger">*</sup></label>
                                    <select name="sch_curriculum[]" class="select w-100" multiple
                                        {{ empty($school) || isset($edit) ? '' : 'disabled' }}>
                                        <option data-placeholder="true"></option>
                                        @foreach ($curriculums as $curriculum)
                                            <option value="{{ $curriculum->id }}"
                                                {{ (isset($school->curriculum) && in_array($curriculum->id, $school->curriculum->pluck('id')->toArray())) || (old('sch_curriculum') !== null && in_array($curriculum->name, old('sch_curriculum'))) ? 'selected' : null }}>
                                                {{ $curriculum->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('sch_curriculum')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
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

                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label>Status</label>
                                    <select name="status" class="select w-100" {{ empty($school) || isset($edit) ? '' : 'disabled' }}>
                                        <option value="1" @selected(isset($school) && $school->status == 1)>Active</option>
                                        <option value="0" @selected(isset($school) && $school->status == 0)>Inactive</option>
                                    </select>
                                    @error('status')
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
                                <i class="bi bi-people me-2"></i>
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
                        @if ($details->count() > 0)
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
                                                    @if ($detail->is_pic == true)
                                                        <i class="bi bi-star-fill me-2 my-2 text-warning"
                                                            title="PIC"></i>
                                                    @endif
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
                        @else
                            <div>
                                There's no contact person
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    @if (isset($school))
        <div class="modal modal-md fade" id="aliasModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="m-0 p-0">
                            <i class="bi bi-chat-dots me-2"></i>
                            Aliases of "{{ $school->sch_name }}"
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success" role="alert">
                            <i class="bi bi-info-circle"></i> Just a reminder that the alias is necessary for accurate searches. Please make sure to add more alias to help client find their school name
                        </div>

                        <table class="table table-striped w-100 mb-4">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Alias</th>
                                    <th class="text-center w-10">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($aliases as $alias)
                                    @php
                                        $max = $loop->iteration;
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}. </td>
                                        <td>{{ $alias->alias }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('school.alias.destroy', ['school' => $alias->sch_id, 'alias' => $alias->id ]) }}" class="text-danger delete-alias">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    @if ($aliases->count() == $loop->iteration)
                                    <tr>
                                        <td>{{ $max + 1 }}</td>
                                        <td>
                                            <form action="{{ route('school.alias.store', ['school' => $school->sch_id]) }}" method="POST" id="new-alias-form">
                                                @csrf
                                                <input type="text" placeholder="Add new alias" class="form-control-sm border-1 w-100" name="alias" value="">
                                                @error('alias')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </form>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary" type="submit" form="new-alias-form">
                                                <i class="bi bi-arrow-return-left"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>

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
                </div>
            </div>
        </div>

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
                        <form
                            @if ($error_pic === true) action="{{ url('instance/school/' . $school->sch_id . '/detail/' . old('schdetail_id')) }}"
                            @else
                                action="{{ url('instance/school/' . $school->sch_id . '/detail') }}" @endif
                            method="POST" id="picAction">
                            @csrf
                            <div class="put">
                                @if ($error_pic === true)
                                    @method('put')
                                    <input type="hidden" readonly name="schdetail_id"
                                        value="{{ old('schdetail_id') }}">
                                @endif
                            </div>
                            <input type="hidden" readonly name="sch_id" value="{{ $school->sch_id }}">
                            <div class="row mb-2">
                                <div class="col-md-12 mb-2">
                                    <label>Fullname <sup class="text-danger">*</sup></label>
                                    <input type="text" name="schdetail_name[]"
                                        class="form-control form-control-sm rounded" id="cp_fullname"
                                        value="{{ old('schdetail_name') ? old('schdetail_name')[0] : null }}">
                                    @error('schdetail_name.0')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-2">
                                    <label>E-mail <sup class="text-danger">*</sup></label>
                                    <input type="email" name="schdetail_mail[]"
                                        class="form-control form-control-sm rounded" id="cp_mail"
                                        value="{{ old('schdetail_mail') ? old('schdetail_mail')[0] : null }}">
                                    @error('schdetail_mail.0')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-2">
                                    <label>Phone Number <sup class="text-danger">*</sup></label>
                                    <input type="text" name="schdetail_phone[]"
                                        class="form-control form-control-sm rounded" id="cp_phone"
                                        value="{{ old('schdetail_phone') ? old('schdetail_phone')[0] : null }}">
                                    @error('schdetail_phone.0')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-2">
                                    <label>Person as <sup class="text-danger">*</sup></label>
                                    <select name="schdetail_position[]" class="modal-select w-100" id="cp_status">
                                        <option data-placeholder="true"></option>
                                        <option value="Principal" @selected(old('schdetail_position') && old('schdetail_position')[0] == 'Principal')>
                                            Principal</option>
                                        <option value="Counselor" @selected(old('schdetail_position') && old('schdetail_position')[0] == 'Counselor')>
                                            Counselor</option>
                                        <option value="Teacher" @selected(old('schdetail_position') && old('schdetail_position')[0] == 'Teacher')>
                                            Teacher</option>
                                        <option value="Marketing" @selected(old('schdetail_position') && old('schdetail_position')[0] == 'Marketing')>
                                            Marketing</option>
                                        <option value="Learning Journey Coordinator" @selected(old('schdetail_position') && old('schdetail_position')[0] == 'Learning Journey Coordinator')>
                                            Learning Journey Coordinator</option>
                                        <option value="Head/Owner" @selected(old('schdetail_position') && old('schdetail_position')[0] == 'Head/Owner')>
                                            Head/Owner</option>
                                        <option value="Vice Principal" @selected(old('schdetail_position') && old('schdetail_position')[0] == 'Vice Principal')>
                                            Vice Principal</option>
                                    </select>
                                    @error('schdetail_position.0')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-2">
                                    <label>Educational Stage <sup class="text-danger">*</sup></label>
                                    <select name="schdetail_grade[]" class="modal-select w-100" id="cp_grade">
                                        <option data-placeholder="true"></option>
                                        <option value="Middle School" @selected(old('schdetail_grade') && old('schdetail_grade')[0] == 'Middle School')>
                                            Middle School</option>
                                        <option value="High School" @selected(old('schdetail_grade') && old('schdetail_grade')[0] == 'High School')>
                                            High School</option>
                                        <option value="Middle School & High School" @selected(old('schdetail_grade') && old('schdetail_grade')[0] == 'Middle School & High School')>
                                            Middle School & High School</option>
                                    </select>
                                    @error('schdetail_grade.0')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="">Is he/she is a PIC?</label>
                                    <input type="hidden" value="false" id="is_pic" name="is_pic">
                                    <div class="form-check ms-4">
                                        <input class="form-check-input" type="radio" name="pic_status" value="1"
                                            @checked(old('pic_status') == 1)>
                                        <label class="form-check-label">Yes</label>
                                    </div>
                                    <div class="form-check ms-4">
                                        <input class="form-check-input" type="radio" name="pic_status" value="0"
                                            @checked(old('pic_status') == 0) @checked(old('pic_status') !== null)>
                                        <label class="form-check-label">No</label>
                                    </div>
                                    @error('is_pic')
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

@endsection

@push('scripts')
<script>
    
    $(document).ready(function() {
        @if (session()->has('success'))
            $("#aliasModal").modal('show');
        @endif

        $('.delete-alias').on('click', function(e) {
            e.preventDefault();
    
            showLoading();
    
            var link = $(this).attr('href');
    
            axios.post(link, {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                }).then(function (response) {
    
                    swal.close();
                    notification('success', response.data.message);
                    location.reload();
    
                }).catch(function (error) {
    
                    notification('error', error.message);
    
                })
        })
    })

    $("select[name=choosen_school]").select2({
        placeholder: "Write school name",
        ajax: {
            delay: 250, // wait 250 milliseconds before triggering the request
            url: '{{ url('/') }}/api/school',
            dataType: 'json',
            data: function (params) {
                var query = {
                    search: params.term
                }

                // Query parameters will be ?search=[term]
                return query;
            }, 
            processResults: function (data) {
                return {
                    results: $.map(data, function (obj) {
                        return { id: obj.sch_id, text: obj.sch_name}
                    })
                }
            }

        },
    })

    $("select[name=sch_id]").on('change', function() {
        var val = $(this).val();
        if (val == 'SCH-NEW') {

            $(this).next(".select2-container").addClass('d-none');
            $("input[name=sch_name]").removeClass('d-none');

        }
    })

    // Select2 Modal 
    $(document).ready(function() {
        $('.modal-program').select2({
            dropdownParent: $('#programForm .modal-content'),
            placeholder: "Select value",
            allowClear: true
        });

        $('.modal-select').select2({
            dropdownParent: $('#picForm .modal-content'),
            placeholder: "Select value",
            allowClear: true
        });

        @if ($error_pic === true)
            $("#picForm").modal('show')
        @elseif ($error_visit === true)
            $("#school_visit").modal('show')
        @endif

        $("input[type=radio][name=pic_status]").change(function() {
            var val = $(this).val();
            $("#is_pic").val(val);
        })

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
                $('#is_pic').val(cp.is_pic)
                $('input[type=radio][name=pic_status][value=' + cp.is_pic + ']').prop('checked', true);

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
@endpush