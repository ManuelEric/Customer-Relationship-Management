@extends('layout.main')

@section('title', 'Partner Program - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('program/client') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Partner Program
        </a>
    </div>


    <div class="row">
        <div class="col-md-4">
            <div class="card rounded mb-3">
                <div class="card-body text-center">
                    <h4>{{ $partner->corp_name }}</h4>
                    @if(isset($edit))
                        <h6>Program Name</h6>
                    @endif
                    <div class="mt-3 d-flex justify-content-center">
                        <button type="button" class="btn btn-sm btn-outline-danger rounded mx-1">
                            <i class="bi bi-trash2"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
            @include('pages.program.corporate-program.detail.corporate')
            @if(!empty($edit))
                @include('pages.program.corporate-program.detail.speaker')
            @endif
        </div>

        <div class="col-md-8">
            <div class="card rounded mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-tags me-2"></i>
                            Partner Program Detail
                        </h6>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label for="">
                                Program Name <sup class="text-danger">*</sup>
                            </label>
                        </div>
                        <div class="col-md-9">
                            <select name="prog_id" id="" class="select w-100"  {{ empty($partnerProgram) || isset($edit) ? '' : 'disabled' }}>
                                <option data-placeholder="true"></option>
                                @if(isset($partnerProgram->prog_id))
                                    @if(isset($edit))
                                        @foreach ($programs as $program)
                                            <option value="{{ $program->prog_id }}" {{ $partnerProgram->prog_id == $program->prog_id ? 'selected' : ''}}>
                                                {{ $program->prog_program }}
                                            </option>
                                        @endforeach
                                    @else        
                                        <option value="{{ $partnerProgram->prog_id }}" selected>
                                            {{ $partnerProgram->program->prog_program }}
                                        </option>
                                    @endif
                                @elseif(empty($partnerProgram))
                                    @foreach ($programs as $program)
                                        <option value="{{ $program->prog_id }}" {{ old('prog_id') == $program->prog_id ? "selected" : "" }}>
                                            {{ $program->prog_program }}
                                        </option>
                                    @endforeach
                            
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label for="">
                                Is Corporate Scheme? <sup class="text-danger">*</sup>
                            </label>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-3">
                                    <select name="" id="" class="select w-100">
                                        <option data-placeholder="true"></option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label for="">
                                Date <sup class="text-danger">*</sup>
                            </label>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <small>First Discuss <sup class="text-danger">*</sup></small>
                                    <input type="date" name="" id=""
                                        class="form-control form-control-sm rounded">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2 ">
                        <div class="col-md-3">
                            <label for="">
                                Approach Status
                            </label>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <small>Status <sup class="text-danger">*</sup></small>
                                    <select name="" id="approach_status" class="select w-100"
                                        onchange="checkStatus()">
                                        <option data-placeholder="true"></option>
                                        <option value="Pending">Pending</option>
                                        <option value="Success">Success</option>
                                        <option value="Denied">Denied</option>
                                    </select>
                                </div>
                                <div class="col-md-6 success_status d-none">
                                    <small>Success Date <sup class="text-danger">*</sup></small>
                                    <input type="date" name="" id=""
                                        class="form-control form-control-sm rounded">
                                </div>
                                <div class="col-md-6 denied_status d-none">
                                    <small>Denied Date <sup class="text-danger">*</sup></small>
                                    <input type="date" name="" id=""
                                        class="form-control form-control-sm rounded">
                                </div>
                                <div class="col-md-6 denied_status d-none my-2">
                                
                                        <label>Reason <sup class="text-danger">*</sup> </label>
                                        <div class="classReason">
                                            <select name="reason_id" class="select w-100"
                                                style="display: none !important; width:100% !important" id="selectReason"
                                                onchange="otherOption($(this).val())"
                                                {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                                <option data-placeholder="true"></option>
                                                    @if(isset($schoolProgram->reason_id) || isset($edit))
                                                        @if(isset($edit))    
                                                            @foreach ($reasons as $reason)
                                                                <option value="{{ $reason->reason_id }}" {{ $schoolProgram->reason_id == $reason->reason_id ? 'selected' : ''}}>
                                                                    {{ $reason->reason_name }}
                                                                </option>
                                                            @endforeach
                                                            <option value="other">
                                                                Other option
                                                            </option>
                                                        @else
                                                                <option value="{{ $schoolProgram->reason_id }}" selected>
                                                                    {{ $schoolProgram->reason->reason_name }}
                                                                </option>        
                                                        @endif
                                                    @elseif(empty($schoolProgram))
                                                        @foreach ($reasons as $reason)
                                                            <option value="{{ $reason->reason_id }}" {{ old('reason_id') == $reason->reason_id ? "selected" : "" }}>
                                                                {{ $reason->reason_name }}
                                                            </option>
                                                        @endforeach
                                                        <option value="other">
                                                            Other option
                                                        </option>
                                                    @endif
                                            </select>
                                                @error('reason_id')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                        </div>
                                            
                                        <div class="d-flex align-items-center d-none" id="inputReason">
                                            <input type="text" name="other_reason"
                                                class="form-control form-control-sm rounded">
                                            <div class="float-end cursor-pointer" onclick="resetOption()">
                                                <b>
                                                    <i class="bi bi-x text-danger"></i>
                                                </b>
                                            </div>
                                        </div>
                                        @error('other_reason')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror

                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3 success_status d-none">
                        <div class="col-md-3">
                            <label for="">
                                Program Detail <sup class="text-danger">*</sup></small>
                            </label>
                        </div>
                        <div class="col-md-9">
                            <div class="card">
                                <div class="card-header">
                                    Partner Program
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small>Participants <sup class="text-danger">*</sup></small></small>
                                            <input type="number" name="" id=""
                                                class="form-control form-control-sm rounded">
                                        </div>
                                        <div class="col-md-6">
                                            <small>Total Fee <sup class="text-danger">*</sup></small></small>
                                            <input type="number" name="" id=""
                                                class="form-control form-control-sm rounded">
                                        </div>
                                        <div class="col-md-6">
                                            <small>Start Date <sup class="text-danger">*</sup></small>
                                            <input type="date" name="" id=""
                                                class="form-control form-control-sm rounded">
                                        </div>
                                        <div class="col-md-6">
                                            <small>End Date <sup class="text-danger">*</sup></small>
                                            <input type="date" name="" id=""
                                                class="form-control form-control-sm rounded">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="">
                                Notes
                            </label>
                        </div>
                        <div class="col-md-9">
                            <textarea name="" id="" class="w-100"></textarea>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label for="">
                                PIC
                            </label>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <select name="empl_id" id="" class="select w-100" {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                        <option data-placeholder="true"></option>
                                        @if(isset($schoolProgram->empl_id))
                                            @if(isset($edit))
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->id }}" 
                                                        {{ $schoolProgram->empl_id ==  $employee->id ? 'selected' : ''}}>
                                                        {{ $employee->first_name }} {{ $employee->last_name }}</option>
                                                @endforeach    
                                            @else
                                                <option value="{{ $schoolProgram->empl_id }}" selected>
                                                    {{ $schoolProgram->user->first_name }} {{ $schoolProgram->user->last_name }}
                                                </option>
                                            @endif
                                        @elseif(empty($schoolProgram))
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}" {{ old('empl_id') == $employee->id ? "selected" : "" }}>{{ $employee->first_name }} {{ $employee->last_name }}</option>
                                            @endforeach
                                        @endif
                                        
                                    </select>
                                    @error('empl_id')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="mt-3 text-end">
                        <button class="btn btn-sm btn-primary rounded">
                            <i class="bi bi-save2 me-2"></i> Submit
                        </button>
                    </div>
                </div>
            </div>
            @if(!empty($edit))
                @include('pages.program.corporate-program.detail.attachment')
            @endif
        </div>
    </div>


    <script>
        function checkStatus() {
            let status = $('#approach_status').val();
            if (status == 'Pending') {
                $('.denied_status').addClass('d-none')
                $('.success_status').addClass('d-none')
            } else if (status == 'Success') {
                $('.denied_status').addClass('d-none')
                $('.success_status').removeClass('d-none')
            } else if (status == 'Denied') {
                $('.denied_status').removeClass('d-none')
                $('.success_status').addClass('d-none')
            }
        }

        function otherOption(value) {

            if (value == 'other') {
                $('.classReason').addClass('d-none')
                $('#inputReason').removeClass('d-none')
                $('#inputReason input').focus()
            } else {
                $('#inputReason').addClass('d-none')
                $('.classReason').removeClass('d-none')
            }
        }

        function resetOption() {
            $('.classReason').removeClass('d-none')
            $('#selectReason').val(null).trigger('change')
            $('#inputReason').addClass('d-none')
            $('#inputReason input').val(null)
        }
        $(document).ready(function() {})
    </script>
@endsection
