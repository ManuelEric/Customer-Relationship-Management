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
                    @if(isset($partnerProgram))
                        <h6>{{ $partnerProgram->program->program_name }}</h6>
                    @endif
                    @if (isset($partnerProgram))
                        <div class="mt-3 d-flex justify-content-center">
                            @if (isset($edit))
                                <a href="{{ url('program/corporate/' . strtolower($partner->corp_id) .'/detail/'. $partnerProgram->id ) }}"
                                    class="btn btn-sm btn-outline-info rounded mx-1">
                                    <i class="bi bi-arrow-left"></i> Back
                                </a>
                            @else
                                <a href="{{ url('program/corporate/' . strtolower($partner->corp_id) . '/detail/'. $partnerProgram->id .'/edit') }}"
                                    class="btn btn-sm btn-outline-info rounded mx-1">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            @endif
                            <button type="button"
                                onclick="confirmDelete('{{'program/corporate/' . strtolower($partner->corp_id) . '/detail'}}', {{$partnerProgram->id}})"
                                class="btn btn-sm btn-outline-danger rounded mx-1">
                                <i class="bi bi-trash2"></i> Delete
                            </button>
                        </div>
                    @endif
                   
                </div>
            </div>

            {{-- Refund detail --}}
            @if(isset($partnerProgram))
                @if($partnerProgram->status == 3)
                    @include('pages.program.corporate-program.detail.refund')
                @endif
            @endif

            @include('pages.program.corporate-program.detail.corporate')
            @if(isset($partnerProgram) &&  $partnerProgram->status == 1 && empty($edit))
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
                    
                    <form action="{{ url(isset($edit) ? 'program/corporate/' . strtolower($partner->corp_id) . '/detail/' . $partnerProgram->id : 'program/corporate/' . strtolower($partner->corp_id) . '/detail')  }}" method="POST">
                        @csrf
                        @if(isset($edit))
                             @method('put')
                        @endif
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
                                                    {{ $program->program_name }}
                                                </option>
                                            @endforeach
                                        @else        
                                            <option value="{{ $partnerProgram->prog_id }}" selected>
                                                {{ $partnerProgram->program->program_name }}
                                            </option>
                                        @endif
                                    @elseif(empty($partnerProgram))
                                        @foreach ($programs as $program)
                                            <option value="{{ $program->prog_id }}" {{ old('prog_id') == $program->prog_id ? "selected" : "" }}>
                                                {{ $program->program_name }}
                                            </option>
                                        @endforeach
                                
                                    @endif
                                </select>
                                @error('prog_id')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        {{-- <div class="row mb-2">
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
                        </div> --}}
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
                                            value="{{ isset($partnerProgram->first_discuss) ? date('Y-m-d', strtotime($partnerProgram->first_discuss)) :  old('first_discuss') }}"
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
                                            <option value="4">Accepted</option>
                                            <option value="2">Rejected</option>
                                            <option value="1">Success</option>
                                            <option value="5">Cancel</option>
                                            @if (isset($partnerProgram->invoiceB2b->receipt))
                                            <option value="3">Refund</option>
                                            @endif
                                        </select>
                                        @error('status')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pending_status d-none">
                                        <small>Pending Date <sup class="text-danger">*</sup></small>
                                        <input type="date" name="pending_date" id=""
                                            class="form-control form-control-sm rounded"
                                            value="{{ isset($partnerProgram->pending_date) ? $partnerProgram->pending_date :  old('pending_date') }}"
                                            {{ empty($partnerProgram) || isset($edit) ? '' : 'disabled' }}
                                            >
                                        @error('pending_date')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 accepted_status d-none">
                                        <small>Accepted Date <sup class="text-danger">*</sup></small>
                                        <input type="date" name="accepted_date" id=""
                                            class="form-control form-control-sm rounded"
                                            value="{{ isset($partnerProgram->accepted_date) ? $partnerProgram->accepted_date :  old('accepted_date') }}"
                                            {{ empty($partnerProgram) || isset($edit) ? '' : 'disabled' }}
                                            >
                                        @error('accepted_date')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 cancel_status d-none">
                                        <small>Cancel Date <sup class="text-danger">*</sup></small>
                                        <input type="date" name="cancel_date" id=""
                                            class="form-control form-control-sm rounded"
                                            value="{{ isset($partnerProgram->cancel_date) ? $partnerProgram->cancel_date :  old('cancel_date') }}"
                                            {{ empty($partnerProgram) || isset($edit) ? '' : 'disabled' }}
                                            >
                                        @error('cancel_date')
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
                                        <small>Rejected Date <sup class="text-danger">*</sup></small>
                                        <input type="date" name="denied_date" id=""
                                            class="form-control form-control-sm rounded"
                                            value="{{ isset($partnerProgram->denied_date) ? $partnerProgram->denied_date :  old('denied_date') }}"
                                            {{ empty($partnerProgram) || isset($edit) ? '' : 'disabled' }}>
                                        @error('denied_date')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                     <div class="col-md-6 refund_status d-none">
                                    <small>Refund Date <sup class="text-danger">*</sup> </small>
                                    <input type="date" name="refund_date" id=""
                                        class="form-control form-control-sm rounded"
                                        value="{{ isset($partnerProgram->refund_date) ? $partnerProgram->refund_date :  old('refund_date') }}"
                                        {{ empty($partnerProgram) || isset($edit) ? '' : 'disabled' }}>
                                    @error('refund_date')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>


                                    {{-- Refund --}}
                                    <div class="col-md-6 refund_status d-none my-2">
                                    
                                            <label>Reason <sup class="text-danger">*</sup> </label>
                                            <div class="classReasonRefund">
                                                <select name="reason_refund_id" class="select w-100"
                                                    style="display: none !important; width:100% !important" id="selectReasonRefund"
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
                                                                <option value="other_reason_refund">
                                                                    Other option
                                                                </option>
                                                            @else
                                                                   <option value="{{ $partnerProgram->reason_id }}" selected>
                                                                        {{ $partnerProgram->reason->reason_name }}
                                                                    </option>          
                                                            @endif
                                                        @elseif(empty($partnerProgram))
                                                            @foreach ($reasons as $reason)
                                                                <option value="{{ $reason->reason_id }}" {{ old('reason_refund_id') == $reason->reason_id ? "selected" : "" }}>
                                                                    {{ $reason->reason_name }}
                                                                </option>
                                                            @endforeach
                                                            <option value="other_reason_refund">
                                                                Other option
                                                            </option>
                                                        @endif
                                                </select>
                                                    @error('reason_refund_id')
                                                        <small class="text-danger fw-light">{{ $message }}</small>
                                                    @enderror
                                                    @error('other_reason_refund')
                                                        <small class="text-danger fw-light">{{ $message }}</small>
                                                    @enderror
                                                
                                            </div>
                                                
                                            <div class="d-flex align-items-center d-none" id="inputReasonRefund">
                                                <input type="text" name="other_reason_refund"
                                                    class="form-control form-control-sm rounded">
                                                <div class="float-end cursor-pointer" onclick="resetOption()">
                                                    <b>
                                                        <i class="bi bi-x text-danger"></i>
                                                    </b>
                                                </div>
                                            </div>
        
                                    </div>
                                    <div class="col-md-12 refund_status d-none my-3">
                                        <label for="">
                                        Refund  Notes
                                        </label>
                                        <textarea name="refund_notes" id="" class="w-100"  {{ empty($partnerProgram) || isset($edit) ? '' : 'disabled' }}>
                                            {{ isset($partnerProgram->refund_notes) ? $partnerProgram->refund_notes :  old('refund_notes') }}
                                        </textarea>
                                    </div>  

                                    {{-- Reason --}}
                                    <div class="col-md-6 reason d-none my-2">
                                    
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

                        <div class="row mb-3 program_detail d-none">
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
                                        <div class="row success_status d-none">
                                            <div class="col-md-6">
                                                <small>Participants <sup class="text-danger">*</sup></small></small>
                                                <input type="number" name="participants" id=""
                                                    class="form-control form-control-sm rounded"
                                                    value="{{ isset($partnerProgram->participants) ? $partnerProgram->participants :  old('participants') }}"
                                                    {{ empty($partnerProgram) || isset($edit) ? '' : 'disabled' }}>
                                                @error('participants')
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
                                        </div>
                                        <div class="row program_date d-none">
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

                        {{-- <div class="row mb-3 success_status d-none">
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
                                                <input type="number" name="participants" id=""
                                                    class="form-control form-control-sm rounded"
                                                    value="{{ isset($partnerProgram->participants) ? $partnerProgram->participants :  old('participants') }}"
                                                    {{ empty($partnerProgram) || isset($edit) ? '' : 'disabled' }}>
                                                @error('participants')
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
                        </div> --}}
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="">
                                    Notes
                                </label>
                            </div>
                            <div class="col-md-9">
                                <textarea name="notes" id="" class="w-100" {{ empty($partnerProgram) || isset($edit) ? '' : 'disabled' }}>{{ isset($partnerProgram->notes) ? $partnerProgram->notes :  old('notes') }}</textarea>
                                @error('notess')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
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
                                            @if(isset($edit))
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->id }}" 
                                                        {{ $partnerProgram->empl_id ==  $employee->id ? 'selected' : ''}}>
                                                        {{ $employee->first_name }} {{ $employee->last_name }}</option>
                                                @endforeach    
                                            @endif
                                            @if(isset($partnerProgram->empl_id))
                                                    <option value="{{ $partnerProgram->empl_id }}" selected>
                                                        {{ $partnerProgram->user->first_name }} {{ $partnerProgram->user->last_name }}
                                                    </option>
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
                        @if (empty($partnerProgram) || isset($edit))
                            <div class="mt-3 text-end">
                                <button class="btn btn-sm btn-primary rounded">
                                    <i class="bi bi-save2 me-2"></i> Submit
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
            @if(!empty($attach) && $partnerProgram->status == 1 )
                @include('pages.program.corporate-program.detail.attachment')
            @endif
        </div>
    </div>

    <script>
        function checkStatusSpeaker(agendaId) {
            let status = $('#status_speaker' + agendaId).val()
            @if(isset($partnerProgram))
                let link =
                    '{{ url('') }}/program/corporate/{{ strtolower($partner->corp_id) }}/detail/{{$partnerProgram->id}}/speaker/' +
                    agendaId
                // console.log(link)
            @endif 
            let data = new Array()

            $('#reasonForm').attr('action', link)

            if (status == 2) {
                $('#reasonModal').modal('show')
                $('#agenda_id').val(agendaId)
                $('#status_id').val(status)
            } else {
                $('#agenda_id').val(agendaId)
                $('#status_id').val(status)
                $('#notes').val('')
                $('#reasonForm').submit()
            }
        }
    </script>

    <script>
        function checkStatus() {
            let status = $('#approach_status').val();
            if (status == '0') {
                $('.pending_status').removeClass('d-none')
                $('.program_date').removeClass('d-none')
                $('.denied_status').addClass('d-none')
                $('.success_status').addClass('d-none')
                $('.refund_status').addClass('d-none')
                $('.accepted_status').addClass('d-none')
                $('.cancel_status').addClass('d-none')
                $('.reason').addClass('d-none')
                $('.program_detail').removeClass('d-none')
                $('.program_date').removeClass('d-none')
            } else if (status == '1') {
                $('.pending_status').addClass('d-none')
                $('.denied_status').addClass('d-none')
                $('.refund_status').addClass('d-none')
                $('.success_status').removeClass('d-none')
                $('.accepted_status').addClass('d-none')
                $('.cancel_status').addClass('d-none')
                $('.reason').addClass('d-none')
                $('.program_detail').removeClass('d-none')
                $('.program_date').removeClass('d-none')
            } else if (status == '2') {
                $('.pending_status').addClass('d-none')
                $('.denied_status').removeClass('d-none')
                $('.success_status').addClass('d-none')
                $('.refund_status').addClass('d-none')
                $('.accepted_status').addClass('d-none')
                $('.cancel_status').addClass('d-none')
                $('.reason').removeClass('d-none')
                $('.program_detail').addClass('d-none')
                $('.program_date').addClass('d-none')
            } else if (status == '3'){
                $('.pending_status').addClass('d-none')
                $('.refund_status').removeClass('d-none')
                $('.denied_status').addClass('d-none')
                $('.success_status').addClass('d-none')
                $('.program_date').addClass('d-none')
                $('.accepted_status').addClass('d-none')
                $('.cancel_status').addClass('d-none')
                $('.reason').addClass('d-none')
                $('.program_detail').addClass('d-none')
                $('.program_date').addClass('d-none')
            } else if (status == '4'){
                $('.pending_status').addClass('d-none')
                $('.program_date').removeClass('d-none')
                $('.refund_status').addClass('d-none')
                $('.denied_status').addClass('d-none')
                $('.success_status').addClass('d-none')
                $('.accepted_status').removeClass('d-none')
                $('.cancel_status').addClass('d-none')
                $('.reason').addClass('d-none')
                $('.program_detail').removeClass('d-none')
                $('.program_date').removeClass('d-none')
            } else if (status == '5'){
                $('.pending_status').addClass('d-none')
                $('.program_date').addClass('d-none')
                $('.refund_status').addClass('d-none')
                $('.denied_status').addClass('d-none')
                $('.success_status').addClass('d-none')
                $('.accepted_status').addClass('d-none')
                $('.cancel_status').removeClass('d-none')
                $('.reason').removeClass('d-none')
                $('.program_detail').addClass('d-none')
                $('.program_date').addClass('d-none')
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

        

        function changeSpeaker(type) {
            let id = $('#' + type + '_id').val()
         
            @if(isset($partnerProgram))
                let link = '{{ url('program/corporate/' . strtolower($partner->corp_id) . '/detail/' . $partnerProgram->id . '/edit') }}'
                let new_link = link + '?type=' + type + '&id=' + id;
            @endif

            $('.speaker-pic').removeClass('d-none')
            $('#speaker_pic').attr('name', type + '_speaker')
            Swal.showLoading()
            axios.get(new_link)
                .then((res) => {
                    // handle success
                    let data = res.data
                    $('#speaker_pic').html('<option data-placeholder="true"></option>')

                    if (type == 'partner') {
                        data.forEach(partner => {
                            $('#speaker_pic').append('<option value="' + partner.id + '">' + partner
                                .pic_name + '</option>')
                        });
                    } else {
                        data.forEach(sch => {
                            $('#speaker_pic').append('<option value="' + sch.schdetail_id + '">' + sch
                                .schdetail_fullname + '</option>')
                        });
                    }
                    Swal.close();
                })
                .catch((err) => {
                    // handle error
                    console.error(err)
                    Swal.close()
                })

        }

    </script>

    @if(isset($partnerProgram))
        @if(empty(old('status')))
            
            <script>
                $(document).ready(function(){
                    $('#approach_status').val('{{$partnerProgram->status}}').trigger('change')
                    // $('#selectReason').select2()
                })
            </script>
        @endif
    @else
        <script>
            $(document).ready(function(){
            $('#approach_status').val(0).trigger('change')
            })
        </script>
    @endif

    @if(!empty(old('status')))
        <script>
            $(document).ready(function(){
                $('#approach_status').val("{{old('status')}}").trigger('change')
            })
        </script>
    @endif

    @if($errors->has('notes_reason'))
        <script>
            $(document).ready(function(){
                $('#reasonModal').modal('show'); 
            })

        </script>

    @endif

@endsection
