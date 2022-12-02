@extends('layout.main')

@section('title', 'School Program - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('instance/school/'. strtolower($school->sch_id)) }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> School Program
        </a>
    </div>


    <div class="row">
        <div class="col-md-4">
            <div class="card rounded mb-3">
                <div class="card-body text-center">
                    <h4>Michael Nathan</h4>
                    <h6>Program Name</h6>
                    @if (isset($schoolProgram))
                        <div class="mt-3 d-flex justify-content-center">
                            @if (isset($edit))
                                <a href="{{ url('program/school/' . strtolower($school->sch_id) .'/detail/'. $schoolProgram->id ) }}"
                                    class="btn btn-sm btn-outline-info rounded mx-1">
                                    <i class="bi bi-arrow-left"></i> Back
                                </a>
                            @else
                                <a href="{{ url('program/school/' . $school->sch_id . '/detail/'. $schoolProgram->id .'/edit') }}"
                                    class="btn btn-sm btn-outline-info rounded mx-1">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            @endif
                            <button type="button"
                                onclick="confirmDelete('{{'program/school/' . $school->sch_id . '/detail'}}', {{$schoolProgram->id}})"
                                class="btn btn-sm btn-outline-danger rounded mx-1">
                                <i class="bi bi-trash2"></i> Delete
                            </button>
                        </div>
                    @endif
                   
                </div>
            </div>
            @include('pages.program.school-program.detail.school')
            @include('pages.program.school-program.detail.speaker')
        </div>

        <div class="col-md-8">
            <div class="card rounded mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-tags me-2"></i>
                            School Program Detail
                        </h6>
                    </div>
                </div>
                
                <div class="card-body">
                    
                    <form action="{{ url(isset($edit) ? 'program/school/' . $school->sch_id . '/detail/' . $schoolProgram->id : 'program/school/' . $school->sch_id . '/detail') }}" method="POST">
                        @csrf
                       @if(isset($edit))
                            @method('put')
                       @endif
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="">
                                Program Name <sup class="text-danger">*</sup>
                            </label>
                            
                        </div>
                        <div class="col-md-9">
                            <select name="prog_id" id="" class="select w-100" {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                <option data-placeholder="true"></option>
                                @if(isset($schoolProgram->prog_id))
                                    @if(isset($edit))
                                        @foreach ($programs as $program)
                                            <option value="{{ $program->prog_id }}" {{ $schoolProgram->prog_id == $program->prog_id ? 'selected' : ''}}>
                                                {{ $program->prog_program }}
                                            </option>
                                        @endforeach
                                    @else        
                                        <option value="{{ $schoolProgram->prog_id }}" selected>
                                            {{ $schoolProgram->program->prog_program }}
                                        </option>
                                    @endif
                                @elseif(empty($schoolProgram))
                                    @foreach ($programs as $program)
                                        <option value="{{ $program->prog_id }}" >
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
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="">
                                Date <sup class="text-danger">*</sup>
                            </label>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <small>First Discuss</small>
                                    <input type="date" name="first_discuss" id="" 
                                    value="{{ isset($schoolProgram->first_discuss) ? $schoolProgram->first_discuss :  old('first_discuss') }}"
                                        class="form-control form-control-sm rounded" 
                                        {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>

                                </div>
                                @error('first_discuss')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                                <div class="col-md-6">
                                    <small>Planned Follow Up</small>
                                    <input type="date" name="planned_followup" id=""
                                    value="{{ isset($schoolProgram->planned_followup) ? $schoolProgram->planned_followup :  old('planned_followup') }}"
                                        class="form-control form-control-sm rounded"
                                        {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                </div>
                                @error('planned_followup')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
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
                            <textarea name="notes" id="" class="w-100"  {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                {{ isset($schoolProgram->notes) ? $schoolProgram->notes :  old('notes') }}
                            </textarea>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label for="">
                                Approach Status
                            </label>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <small>Status</small>
                                    <select name="status" id="approach_status" class="select w-100"
                                        onchange="checkStatus()" {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
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
                                    <small>Success Date</small>
                                    <input type="date" name="success_date" id=""
                                        class="form-control form-control-sm rounded"
                                        value="{{ isset($schoolProgram->success_date) ? $schoolProgram->success_date :  old('success_date') }}"
                                        {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                </div>
                                <div class="col-md-6 denied_status d-none">
                                    <small>Denied Date</small>
                                    <input type="date" name="denied_date" id=""
                                        class="form-control form-control-sm rounded"
                                        value="{{ isset($schoolProgram->denied_date) ? $schoolProgram->denied_date :  old('denied_date') }}"
                                        {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                </div>
                                <div class="col-md-6 denied_status d-none my-2">
                                
                                        <label>Reason</label>
                                        <div class="classReason">
                                            <select name="reason_id" class="select w-100"
                                                style="display: none !important" id="selectReason"
                                                onchange="otherOption($(this).val())"
                                                {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                                <option data-placeholder="true"></option>
                                                    @if(isset($schoolProgram->reason_id))
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
                                                            <option value="{{ $reason->reason_id }}">
                                                                {{ $reason->reason_name }}
                                                            </option>
                                                        @endforeach
                                                        <option value="other">
                                                            Other option
                                                        </option>
                                                    @endif
                                            </select>
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
                    {{-- Program Detail  --}}
                    <div class="row mb-3 success_status d-none">
                        <div class="col-md-3">
                            <label for="">
                                Program Detail
                            </label>
                        </div>
                        <div class="col-md-9">
                            {{-- Admissions Program  --}}
                            <div class="card">
                                <div class="card-header">
                                    School Program
                                </div>
                                <div class="card-body">
                                    {{-- if success  --}}
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <small>Start Program Date</small>
                                            <input type="date" name="start_program_date" id=""
                                                class="form-control form-control-sm rounded"
                                                value="{{ isset($schoolProgram->start_program_date) ? $schoolProgram->start_program_date :  old('start_program_date') }}"
                                                {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                            @error('start_program_date')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <small>End Program Date</small>
                                            <input type="date" name="end_program_date" id=""
                                                class="form-control form-control-sm rounded"
                                                value="{{ isset($schoolProgram->end_program_date) ? $schoolProgram->end_program_date :  old('end_program_date') }}"
                                                {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                            @error('end_program_date')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-12 mb-2">
                                            <small>Place</small>
                                            <input type="text" name="place" id=""
                                                class="form-control form-control-sm rounded"
                                                value="{{ isset($schoolProgram->place) ? $schoolProgram->place :  old('place') }}"
                                                {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small>Participants</small>
                                            <input type="number" name="participants" id=""
                                                class="form-control form-control-sm rounded"
                                                value="{{ isset($schoolProgram->participants) ? $schoolProgram->participants :  old('participants') }}"
                                                {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small>Total Fee</small>
                                            <input type="number" name="total_fee" id=""
                                                class="form-control form-control-sm rounded"
                                                value="{{ isset($schoolProgram->total_fee) ? $schoolProgram->total_fee :  old('total_fee') }}"
                                                {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small>Total Hours</small>
                                            <input type="number" name="total_hours" id=""
                                                class="form-control form-control-sm rounded"
                                                value="{{ isset($schoolProgram->total_hours) ? $schoolProgram->total_hours :  old('total_hours') }}"
                                                {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small>Running Status</small>
                                            <select name="running_status" id="" class="select w-100">
                                                <option data-placeholder="true"></option>
                                                @if(isset($schoolProgram->running_status))
                                                    <option value="Not yet" {{ $schoolProgram->running_status == 'Not yet' ? 'selected' : ''}} {{ isset($edit) ? '' : 'disabled' }}>Pending</option>
                                                    <option value="On going" {{ $schoolProgram->running_status == 'On going' ? 'selected' : ''}} {{ isset($edit) ? '' : 'disabled' }}>Success</option>
                                                    <option value="Done" {{ $schoolProgram->running_status == 'Done' ? 'selected' : ''}} {{ isset($edit) ? '' : 'disabled' }}>Denied</option>
                                                @elseif(empty($schoolProgram))
                                                    <option value="Not yet">Not yet</option>
                                                    <option value="On going">On going</option>
                                                    <option value="Done">Done</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-12 mb-2">
                                            <small>Notes</small>
                                            <textarea name="notes_detail" id=""  {{ empty($schoolProgram) || isset($edit) ? '' : 'disabled' }}>
                                                {{ isset($schoolProgram->notes_detail) ? $schoolProgram->notes_detail :  old('notes_detail') }}
                                            </textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                                <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
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
                    @if (empty($schoolProgram) || isset($edit))
                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-sm btn-primary rounded">
                            <i class="bi bi-save2 me-2"></i> Submit
                        </button>
                    </div>
                    @endif
                    </form>
                </div>
            </div>
          
            @if(isset($success))
                @include('pages.program.school-program.detail.attachment')
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

    </script>
    @if(isset($schoolProgram))
        <script>
            $(document).ready(function(){
                // checkStatus()
                $('#approach_status').val('{{$schoolProgram->status}}').trigger('change')
            })

            // $("#approach_status").val(1).trigger('change')
        </script>
    @endif

@endsection
