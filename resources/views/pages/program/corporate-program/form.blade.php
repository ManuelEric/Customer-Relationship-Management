@extends('layout.main')

@section('title', 'Partner Program - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('program/corporate') }}" class="text-decoration-none text-muted">
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
                    <form action="{{ url('program/corporate/' . $partner->corp_id . '/detail') }}" method="POST">
                        @csrf
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
                                @error('prog_id')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
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
                                        <select name="is_corporate_scheme" id="" class="select w-100" {{ empty($partnerProgram) || isset($edit) ? '' : 'disabled' }}>
                                            <option data-placeholder="true"></option>
                                            @if(isset($partnerProgram))
                                                <option value="1" {{ $partnerProgram->is_corporate_scheme == 1 ? 'selected' : '' }}>Yes</option>
                                                <option value="2" {{ $partnerProgram->is_corporate_scheme == 2 ? 'selected' : '' }}>No</option>
                                            @elseif (empty($partnerProgram))
                                                <option value="1" {{ old('is_corporate_scheme') == 1 ? "selected" : "" }}>Yes</option>
                                                <option value="2" {{ old('is_corporate_scheme') == 2 ? "selected" : "" }}>No</option>
                                            @endif
                                        </select>
                                        @error('is_corporate_scheme')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
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
                                        <input type="date" name="first_discuss" id=""
                                            value="{{ isset($partnerProgram->first_discuss) ? $partnerProgram->first_discuss :  old('first_discuss') }}"
                                            class="form-control form-control-sm rounded"
                                            {{ empty($partnerProgram) || isset($edit) ? '' : 'disabled' }}>
                                    </div>
                                    @error('first_discuss')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
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
                                        <select name="status" id="approach_status" class="select w-100"
                                            onchange="checkStatus()" {{ empty($partnerProgram) || isset($edit) ? '' : 'disabled' }}>
                                            <option data-placeholder="true"></option>
                                            <option value="0">Pending</option>
                                            <option value="1">Success</option>
                                            <option value="2">Denied</option>
                                        </select>
                                        @error('status')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 success_status d-none">
                                        <small>Success Date <sup class="text-danger">*</sup></small>
                                        <input type="date" name="success_date" id=""
                                            class="form-control form-control-sm rounded"
                                            value="{{ isset($partnerProgram->success_date) ? $partnerProgram->success_date :  old('success_date') }}"
                                            {{ empty($partnerProgram) || isset($edit) ? '' : 'disabled' }}>
                                        @error('success_date')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 denied_status d-none">
                                        <small>Denied Date <sup class="text-danger">*</sup></small>
                                        <input type="date" name="denied_date" id=""
                                            class="form-control form-control-sm rounded"
                                            value="{{ isset($partnerProgram->denied_date) ? $partnerProgram->denied_date :  old('denied_date') }}"
                                            {{ empty($partnerProgram) || isset($edit) ? '' : 'disabled' }}>
                                        @error('denied_date')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 denied_status d-none my-2">
                                    
                                            <label>Reason <sup class="text-danger">*</sup> </label>
                                            <div class="classReason">
                                                <select name="reason_id" class="select w-100"
                                                    style="display: none !important; width:100% !important" id="selectReason"
                                                    onchange="otherOption($(this).val())"
                                                    {{ empty($partnerProgram) || isset($edit) ? '' : 'disabled' }}>
                                                    <option data-placeholder="true"></option>
                                                        @if(isset($partnerProgram->reason_id) || isset($edit))
                                                            @if(isset($edit))    
                                                                @foreach ($reasons as $reason)
                                                                    <option value="{{ $reason->reason_id }}" {{ $partnerProgram->reason_id == $reason->reason_id ? 'selected' : ''}}>
                                                                        {{ $reason->reason_name }}
                                                                    </option>
                                                                @endforeach
                                                                <option value="other">
                                                                    Other option
                                                                </option>
                                                            @else
                                                                    <option value="{{ $partnerProgram->reason_id }}" selected>
                                                                        {{ $partnerProgram->reason->reason_name }}
                                                                    </option>        
                                                            @endif
                                                        @elseif(empty($partnerProgram))
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
                                                    @error('other_reason')
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
                                                <input type="number" name="number_of_student" id=""
                                                    class="form-control form-control-sm rounded"
                                                    value="{{ isset($partnerProgram->number_of_student) ? $partnerProgram->number_of_student :  old('number_of_student') }}"
                                                    {{ empty($partnerProgram) || isset($edit) ? '' : 'disabled' }}>
                                                @error('number_of_student')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <small>Total Fee <sup class="text-danger">*</sup></small></small>
                                                <input type="number" name="total_fee" id=""
                                                    class="form-control form-control-sm rounded"
                                                    value="{{ isset($partnerProgram->total_fee) ? $partnerProgram->total_fee :  old('total_fee') }}"
                                                    {{ empty($partnerProgram) || isset($edit) ? '' : 'disabled' }}>
                                                @error('total_fee')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <small>Start Date <sup class="text-danger">*</sup></small>
                                                <input type="date" name="start_date" id=""
                                                    class="form-control form-control-sm rounded"
                                                    value="{{ isset($partnerProgram->start_date) ? $partnerProgram->start_date :  old('start_date') }}"
                                                    {{ empty($partnerProgram) || isset($edit) ? '' : 'disabled' }}>
                                                @error('start_date')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <small>End Date <sup class="text-danger">*</sup></small>
                                                <input type="date" name="end_date" id=""
                                                    class="form-control form-control-sm rounded"
                                                    value="{{ isset($partnerProgram->end_date) ? $partnerProgram->end_date :  old('end_date') }}"
                                                    {{ empty($partnerProgram) || isset($edit) ? '' : 'disabled' }}>
                                                @error('end_date')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
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
                                <textarea name="notes" id="" class="w-100"></textarea>
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
                                        <select name="empl_id" id="" class="select w-100" {{ empty($partnerProgram) || isset($edit) ? '' : 'disabled' }}>
                                            <option data-placeholder="true"></option>
                                            @if(isset($partnerProgram->empl_id))
                                                @if(isset($edit))
                                                    @foreach ($employees as $employee)
                                                        <option value="{{ $employee->id }}" 
                                                            {{ $partnerProgram->empl_id ==  $employee->id ? 'selected' : ''}}>
                                                            {{ $employee->first_name }} {{ $employee->last_name }}</option>
                                                    @endforeach    
                                                @else
                                                    <option value="{{ $partnerProgram->empl_id }}" selected>
                                                        {{ $partnerProgram->user->first_name }} {{ $partnerProgram->user->last_name }}
                                                    </option>
                                                @endif
                                            @elseif(empty($partnerProgram))
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
                    </form>
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
            if (status == '0') {
                $('.denied_status').addClass('d-none')
                $('.success_status').addClass('d-none')
            } else if (status == '1') {
                $('.denied_status').addClass('d-none')
                $('.success_status').removeClass('d-none')
            } else if (status == '2') {
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

    @if(isset($partnerProgram))
    <script>
        $(document).ready(function(){
            $('#approach_status').val('{{$partnerProgram->status}}').trigger('change')
            // $('#selectReason').select2()
        })

    </script>
    @endif

    @if(
        $errors->has('success_date') || 
        $errors->has('number_of_student') || 
        $errors->has('total_fee') ||
        $errors->has('start_date') ||
        $errors->has('end_date')
        )
        
        <script>
            $(document).ready(function(){
                $('#approach_status').val('1').trigger('change')
            })

        </script>

    @endif

    @if(
        $errors->has('denied_date') || 
        $errors->has('reason_id') ||
        $errors->has('other_reason')
        )
        
        <script>
            $(document).ready(function(){
                $('#approach_status').val('2').trigger('change')
            })

        </script>


    @endif
@endsection
