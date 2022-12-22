@extends('layout.main')

@section('title', 'Client Program - Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('program/client') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Client Program
        </a>
    </div>

    @if($errors->any())
        {{ implode('', $errors->all('<div>:message</div>')) }}
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card rounded mb-3">
                <div class="card-body text-center">
                    <h3><i class="bi bi-person"></i></h3>
                    <h4>{{ $student->fullname }}</h4>
                    @if (!request()->is('program/client/create*'))
                        <div class="mt-3 d-flex justify-content-center">
                            <a href="{{ request()->is('program/client/1') ? url('program/client/1/edit') : url('program/client/1') }}"
                                type="button" class="btn btn-sm btn-outline-warning rounded mx-1">
                                <i
                                    class="bi {{ request()->is('program/client/1') ? 'bi-pencil' : 'bi-arrow-left' }} me-1"></i>
                                {{ request()->is('program/client/1') ? 'Edit' : 'Back' }}
                            </a>

                            <button type="button" class="btn btn-sm btn-outline-danger rounded mx-1">
                                <i class="bi bi-trash2"></i> Delete
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            @include('pages.program.client-program.detail.client')
            @include('pages.program.client-program.detail.plan-followup')
        </div>

        <div class="col-md-8">
            <div class="card rounded mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-tags me-2"></i>
                            Client Program Detail
                        </h6>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('student.program.store', ['student' => $student->id]) }}" method="POST">
                        @csrf
                        <div class="row mb-2">
                            <div class="col-md-3">
                                <label for="">
                                    Program Name <sup class="text-danger">*</sup>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <select name="prog_id" id="program_name" class="select w-100"
                                    onchange="changeProgramStatus()">
                                    <option data-placeholder="true"></option>
                                    @forelse ($programs as $program)
                                        
                                        <option data-mprog="{{ $program->main_prog->prog_name }}" data-sprog="{{ isset($program->sub_prog->sub_prog_name) ? $program->sub_prog->sub_prog_name : null }}" 
                                            value="{{ $program->prog_id }}"
                                            @if (!empty(old('prog_id')) && old('prog_id') == $program->prog_id)
                                                {{ "selected" }}
                                            @endif
                                            >{{ $program->prog_program }}</option>
                                    @empty
                                        <option>There's no data</option>
                                    @endforelse
                                </select>
                                @error('prog_id')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-3">
                                <label for="">
                                    Conversion Lead <sup class="text-danger">*</sup>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small>Main Lead <sup class="text-danger">*</sup></small>
                                        <select name="lead_id" id="main_lead" class="select w-100">
                                            <option data-placeholder="true"></option>
                                            @if (isset($leads) && count($leads) > 0)
                                                @foreach ($leads as $lead)
                                                    <option data-lead="{{ $lead->main_lead }}" value="{{ $lead->lead_id }}"
                                                            {{ old('lead_id') == $lead->lead_id ? "selected" : null }}
                                                        >{{ $lead->main_lead }}</option>
                                                @endforeach
                                                <option data-lead="KOL" value="kol" {{ old('lead_id') == "kol" ? "selected" : null }}>KOL</option>
                                            @endif
                                        </select>
                                        @error('lead_id')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 d-none" id="event">
                                        <small>Sub Lead <sup class="text-danger">*</sup></small>
                                        <select name="event_id" id="" class="select w-100">
                                            <option data-placeholder="true"></option>
                                            @forelse ($events as $event)
                                                <option value="{{ $event->event_id }}">{{ $event->event_title }}</option>
                                            @empty
                                                <option>There's no data</option>
                                            @endforelse
                                        </select>
                                        @error('event_id')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 d-none" id="edufair">
                                        <small>Sub Lead <sup class="text-danger">*</sup></small>
                                        <select name="eduf_id" id="" class="select w-100">
                                            <option data-placeholder="true"></option>
                                            @forelse ($external_edufair as $edufair)
                                                <option value="{{ $edufair->id }}">{{ $edufair->title }}</option>
                                            @empty
                                                <option>There's no data</option>
                                            @endforelse
                                        </select>
                                        @error('eduf_id')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 d-none" id="kol">
                                        <small>Sub Lead <sup class="text-danger">*</sup></small>
                                        <select name="kol_lead_id" id="" class="select w-100">
                                            <option data-placeholder="true"></option>
                                            @forelse ($kols as $kol)
                                                <option value="{{ $kol->lead_id }}">{{ $kol->sub_lead }}</option>
                                            @empty
                                                <option>There's no data</option>
                                            @endforelse
                                        </select>
                                        @error('kol_lead_id')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 d-none" id="partner">
                                        <small>Sub Lead <sup class="text-danger">*</sup></small>
                                        <select name="partner_id" id="" class="select w-100">
                                            <option data-placeholder="true"></option>
                                            @forelse ($partners as $partner)
                                                <option value="{{ $partner->corp_id }}">{{ $partner->corp_name }}</option>
                                            @empty
                                                <option>There's no data</option>
                                            @endforelse
                                        </select>
                                        @error('partner_id')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
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
                                        <small>First Discuss <sup class="text-danger">*</sup></small>
                                        <input type="date" name="first_discuss_date" id=""
                                            class="form-control form-control-sm rounded">
                                        @error('first_discuss_date')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
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
                                <textarea name="meeting_notes" id="" class="w-100"></textarea>
                                @error('meeting_notes')
                                    
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="">
                                    Program Status <sup class="text-danger">*</sup>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small>Status <sup class="text-danger">*</sup></small>
                                        <select name="status" id="program_status" class="select w-100"
                                            onchange="changeProgramStatus()">
                                            <option data-placeholder="true"></option>
                                            <option value="0" >Pending</option>
                                            <option value="1" >Success</option>
                                            <option value="2" >Failed</option>
                                            <option value="3" >Refund</option>
                                        </select>
                                        @error('status')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 program-detail d-none" id="success_date">
                                        <small>Success Date <sup class="text-danger">*</sup></small>
                                        <input type="date" name="success_date" id=""
                                            class="form-control form-control-sm rounded">
                                        @error('statusprog_date')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 program-detail d-none" id="failed_date">
                                        <small>Failed Date <sup class="text-danger">*</sup></small>
                                        <input type="date" name="failed_date" id=""
                                            class="form-control form-control-sm rounded">
                                    </div>
                                    <div class="col-md-6 program-detail d-none" id="refund_date">
                                        <small>Refund Date <sup class="text-danger">*</sup></small>
                                        <input type="date" name="refund_date" id=""
                                            class="form-control form-control-sm rounded">
                                    </div>


                                    <div class="col-md-12 mt-2 program-detail d-none" id="reason">
                                        <small>Reason <sup class="text-danger">*</sup></small>
                                        <div class="classReason">
                                            <select name="reason_id" class="select w-100"
                                                    style="display: none !important; width:100% !important" id="selectReason"
                                                    onchange="otherOption($(this).val())">
                                                    <option data-placeholder="true"></option>
                                                    @foreach ($reasons as $reason)
                                                        <option value="{{ $reason->reason_id }}" {{ old('reason_id') == $reason->reason_id ? "selected" : "" }}>
                                                            {{ $reason->reason_name }}
                                                        </option>
                                                    @endforeach
                                                    <option value="other">
                                                        Other option
                                                    </option>
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
                                        @error('other_reason')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                        {{-- <input type="text" name="" class="form-control form-control-sm"> --}}
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Program Detail  --}}
                        <div class="program-detail d-none" id="pending_mentoring">
                            @include('pages.program.client-program.form-detail.pending-mentoring')
                        </div>
                        <div class="program-detail d-none" id="success_mentoring">
                            @include('pages.program.client-program.form-detail.success-mentoring')
                        </div>
                        <div class="program-detail d-none" id="pending_tutoring">
                            @include('pages.program.client-program.form-detail.pending-tutoring')
                        </div>
                        <div class="program-detail d-none" id="success_tutoring">
                            @include('pages.program.client-program.form-detail.success-tutoring')
                        </div>
                        <div class="program-detail d-none" id="success_sat_act">
                            @include('pages.program.client-program.form-detail.success-sat-act')
                        </div>


                        <div class="row mb-3 program-detail d-none" id="running_status">
                            <div class="col-md-3">
                                <label for="">
                                    Running Status <sup class="text-danger">*</sup>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <select name="prog_running_status" id="" class="select w-100">
                                            <option data-placeholder="true"></option>
                                            <option value="0">Not yet</option>
                                            <option value="1">Ongoing</option>
                                            <option value="2">Done</option>
                                        </select>
                                        @error('prog_running_status')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="">
                                    PIC <sup class="text-danger">*</sup>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <select name="empl_id" id="" class="select w-100">
                                            <option data-placeholder="true"></option>
                                            @foreach ($internalPIC as $pic)
                                                <option value="{{ $pic->id }}">{{ $pic->first_name.' '.$pic->last_name }}</option>
                                            @endforeach
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
                            <button type="submit" class="btn btn-sm btn-primary rounded">
                                <i class="bi bi-save2 me-2"></i> Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @include('pages.program.client-program.detail.payment')
        </div>
    </div>

    <script>
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

        $("#main_lead").on('change', function() {
            var lead = $(this).select2().find(":selected").data('lead')
            if (lead.includes('All-In Event')) {
                
                $("#event").removeClass('d-none')
                $("#edufair").addClass("d-none")
                $("#kol").addClass("d-none")
                $("#partner").addClass("d-none")

            } else if (lead.includes('External Edufair')) {

                $("#event").addClass("d-none")
                $("#edufair").removeClass("d-none")
                $("#kol").addClass("d-none")
                $("#partner").addClass("d-none")

            } else  if (lead.includes('KOL')) {

                $("#event").addClass("d-none")
                $("#edufair").addClass("d-none")
                $("#kol").removeClass("d-none")
                $("#partner").addClass("d-none")

            } else if (lead.includes('All-In Partners')) {

                $("#event").addClass("d-none")
                $("#edufair").addClass("d-none")
                $("#kol").addClass("d-none")
                $("#partner").removeClass("d-none")

            }
        })

        function changeProgramStatus() {
            var program = $("#program_name option:selected")
            let programName = program.text()
            let programMainProg = program.data('mprog')
            let programSubProg = program.data('sprog')
            let programStatus = $('#program_status').val()
            $('.program-detail').addClass('d-none')

            if (programStatus == 0) { // pending
                if (programMainProg.includes('Admission') || programSubProg.includes('Admission')) { // mentoring

                    $('#pending_mentoring').removeClass('d-none')

                } else if (programMainProg.includes('Tutoring') || programSubProg.includes('Tutoring')) {

                    $('#pending_tutoring').removeClass('d-none')

                }
            } else if (programStatus == 1) { // success
                $('#success_date').removeClass('d-none')
                $('#running_status').removeClass('d-none')
                if (programMainProg.includes('Admission') || programSubProg.includes('Admission')) { // mentoring
                    $('#success_mentoring').removeClass('d-none')
                } else if (programMainProg.includes('Tutoring') || programSubProg.includes('Tutoring')) {
                    $('#success_tutoring').removeClass('d-none')
                } else if (programMainProg.includes('ACT') || programSubProg.includes('ACT') || programMainProg.includes('SAT') || programSubProg.includes('SAT')) {
                    
                    $('#success_sat_act').removeClass('d-none')
                } 

            } else if (programStatus == 2) { // failed
                $('#failed_date').removeClass('d-none')
                $('#reason').removeClass('d-none')
            } else if (programStatus == 3) { // refund
                $('#refund_date').removeClass('d-none')
                $('#reason').removeClass('d-none')
            }
   
        }
    </script>
    <script>
        $(document).ready(function() {

            const documentReady = () => {
                @if (old('lead_id') !== NULL)
                    $("#main_lead").select2().val("{{ old('lead_id') }}").trigger('change')
                @endif

                @if (old('status') !== NULL)
                    $("#program_status").select2().val("{{ (int) old('status') }}").trigger('change')
                @endif
            }
    
            documentReady()
        })
    </script>
@endsection
