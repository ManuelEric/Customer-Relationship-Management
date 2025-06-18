@extends('layout.main')

@section('title', 'Client Program ')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Client Program</a></li>
    <li class="breadcrumb-item active" aria-current="page">Form Client Program</li>
@endsection

@section('content')

    @php
        $disabled = !isset($edit) ? 'disabled' : null;
        // $open_information_for_tutor = isset($clientProgram->invoice) && $clientProgram->program->main_prog->prog_name == "Academic & Test Preparation" && $clientProgram->session_tutor === NULL ? true : false;
    @endphp

    @if ($errors->any())
        {{ implode('', $errors->all('<div>:message</div>')) }}
    @endif

    <div class="row" id="app">
        <div class="col-md-4">
            <div class="card rounded mb-3">
                <div class="card-body text-center">
                    <h3><i class="bi bi-person"></i></h3>
                    <h4><a class="text-decoration-none" target="_blank"
                            href="{{ route('student.show', ['student' => $student->id]) }}">{{ $student->full_name }}</a>
                    </h4>
                    @if (!request()->is('program/client/create*'))
                        <div class="mt-3 d-flex justify-content-center">
                            @if (!isset($clientProgram->invoice->refund))
                                <a href="{{ $disabled
                                    ? route('student.program.edit', ['student' => $student->id, 'program' => $clientProgram->clientprog_id])
                                    : url()->previous() }}"
                                    type="button" class="btn btn-sm btn-outline-warning rounded mx-1">
                                    <i class="bi {{ $disabled ? 'bi-pencil' : 'bi-arrow-left' }} me-1"></i>
                                    {{ $disabled ? 'Edit' : 'Back' }}
                                </a>
                            @endif

                            @if (isset($clientProgram))
                                <button type="button" class="btn btn-sm btn-outline-danger rounded mx-1"
                                    onclick="confirmDelete('client/student/{{ $student->id }}/program', {{ $clientProgram->clientprog_id }})">
                                    <i class="bi bi-trash2"></i> Delete
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            @if (isset($clientProgram->invoice->refund))
                @include('pages.program.client-program.detail.refund')
            @endif

            @include('pages.program.client-program.detail.client')

            {{-- Check program is admission & status success --}}
            @if (isset($clientProgram->program->main_prog_id) &&
                    $clientProgram->program->main_prog_id == 1 &&
                    $clientProgram->status == 1)
                @include('pages.program.client-program.detail.program-phase')
            @endif

            @if (isset($clientProgram) && $clientProgram->status == 0)
                @include('pages.program.client-program.detail.plan-followup')
            @endif
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
                    <form
                        action="{{ isset($clientProgram)
                            ? route('student.program.update', ['student' => $student->id, 'program' => $clientProgram->clientprog_id])
                            : route('student.program.store', ['student' => $student->id]) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @if (isset($clientProgram))
                            @method('PUT')
                        @endif
                        <input type="hidden" name="queryP" value="{{ isset($_GET['p']) ? $_GET['p'] : null }}">
                        <div class="row mb-2">
                            <div class="col-md-3">
                                <label for="">
                                    Program <sup class="text-danger">*</sup>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <select name="main_prog" v-model="main_prog" id="main_program"
                                    class="w-100 form-select form-select-sm" {{ $disabled }} @change="getSubProgram">
                                    <option value="" selected disabled>Select main program</option>
                                    @foreach ($main_programs as $main_program)
                                        <option value="{{ $main_program->id }}" @selected(!empty(old('main_prog')) && old('main_prog') == $main_program->id)>
                                            {{ $main_program->prog_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('main_prog')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-2" v-if="subPrograms.length > 0">
                            <div class="col-md-3">
                                <label for="">
                                    Sub Program <sup class="text-danger">*</sup>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <select name="sub_program" v-model="sub_program" class="form-select form-select-sm w-100"
                                    :disabled="'{{ $disabled == 'disabled' ? false : true }}' && subPrograms.length <= 0"
                                    @change="getProgramName">
                                    <option value="" selected disabled>Select sub program</option>
                                    <option :value="item.id" v-for="item in subPrograms" :key="item">
                                        @{{ item.sub_prog_name }}
                                    </option>
                                </select>
                                @error('sub_program')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-2" v-if="programNames.length > 0">
                            <div class="col-md-3">
                                <label for="">
                                    Program Name <sup class="text-danger">*</sup>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <select name="prog_id" v-model="prog_id" id="program_name"
                                    class="form-select form-select-sm w-100"
                                    :disabled="'{{ $disabled == 'disabled' ? false : true }}' && programNames.length <= 0">
                                    <option value="" selected disabled>Select program name</option>
                                    <option :value="item.prog_id" v-for="item in programNames" :key="item">
                                        @{{ item.prog_program }}
                                    </option>
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
                                        <select name="lead_id" v-model="lead_id" id="main_lead"
                                            class="form-select form-select-sm w-100" {{ $disabled }}>
                                            <option value="" selected disabled>Select conversion lead</option>
                                            @if (isset($leads) && count($leads) > 0)
                                                @foreach ($leads as $lead)
                                                    <option data-lead="{{ $lead->main_lead }}"
                                                        value="{{ $lead->lead_id }}"
                                                        @if (old('lead_id') !== null) {{ old('lead_id') == $lead->lead_id ? 'selected' : null }}
                                                            @elseif (isset($clientProgram->lead_id) && $clientProgram->lead_id == $lead->lead_id)
                                                                {{ 'selected' }} @endif>
                                                        {{ $lead->main_lead }}</option>
                                                @endforeach
                                                <option data-lead="KOL" value="kol" @selected(old('lead_id') && old('lead_id') == 'kol')
                                                    @selected(isset($clientProgram->lead_id) && $clientProgram->lead_id == 'kol')>KOL</option>
                                            @endif
                                        </select>
                                        @error('lead_id')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6" id="event" v-if="lead_id=='LS003'">
                                        <small>Event Name <sup class="text-danger">*</sup></small>
                                        <select name="clientevent_id" id="event_id"
                                            class="form-select form-select-sm w-100" {{ $disabled }}>
                                            <option data-placeholder="true"></option>
                                            @foreach ($clientEvents as $clientEvent)
                                                <option value="{{ $clientEvent->clientevent_id }}"
                                                    @if (old('clientevent_id') == $clientEvent->clientevent_id) {{ 'selected' }}
                                                    @elseif (isset($clientProgram->clientevent_id) && $clientProgram->clientevent_id == $clientEvent->clientevent_id)
                                                        {{ 'selected' }} @endif>
                                                    {{ $clientEvent->event->event_title }}</option>
                                            @endforeach
                                        </select>
                                        @error('clientevent_id')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6" id="edufair" v-if="lead_id=='LS017'">
                                        <small>Edufair Name <sup class="text-danger">*</sup></small>
                                        <select name="eduf_lead_id" id="eduf_id"
                                            class="form-select form-select-sm w-100" {{ $disabled }}>
                                            <option data-placeholder="true"></option>
                                            @forelse ($external_edufair as $edufair)
                                                <option value="{{ $edufair->id }}"
                                                    @if (old('eduf_id') == $edufair->id) {{ 'selected' }}
                                                    @elseif (isset($clientProgram) && $clientProgram->eduf_lead_id == $edufair->id)
                                                        {{ 'selected' }} @endif>
                                                    @if ($edufair->title != null)
                                                        {{ $edufair->title }}
                                                    @else
                                                        {{ $edufair->organizer_name }}
                                                    @endif
                                                </option>
                                            @empty
                                                <option>There's no data</option>
                                            @endforelse
                                        </select>
                                        @error('eduf_id')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6" id="kol" v-if="lead_id=='LS048' || lead_id=='kol'">
                                        <small>KOL Name <sup class="text-danger">*</sup></small>
                                        <select name="kol_lead_id" id="kol_lead_id"
                                            class="form-select form-select-sm w-100" {{ $disabled }}>
                                            <option data-placeholder="true"></option>
                                            @forelse ($kols as $kol)
                                                <option value="{{ $kol->lead_id }}"
                                                    @if (old('kol_lead_id') == $kol->lead_id) {{ 'selected' }}
                                                    @elseif (isset($clientProgram->lead_id) && $clientProgram->lead_id == $kol->lead_id)
                                                        {{ 'selected' }} @endif>
                                                    {{ $kol->sub_lead }}</option>
                                            @empty
                                                <option>There's no data</option>
                                            @endforelse
                                        </select>
                                        @error('kol_lead_id')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6" id="partner" v-if="lead_id=='LS010'">
                                        <small>Partner Name <sup class="text-danger">*</sup></small>
                                        <select name="partner_id" id="partner_id"
                                            class="form-select form-select-sm w-100" {{ $disabled }}>
                                            <option data-placeholder="true"></option>
                                            @forelse ($partners as $partner)
                                                <option value="{{ $partner->corp_id }}"
                                                    @if (old('partner_id') == $partner->corp_id) {{ 'selected' }}
                                                    @elseif (isset($clientProgram) && $clientProgram->partner_id == $partner->corp_id)
                                                        {{ 'selected' }} @endif>
                                                    {{ $partner->corp_name }}</option>
                                            @empty
                                                <option>There's no data</option>
                                            @endforelse
                                        </select>
                                        @error('partner_id')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6" id="referral" v-if="lead_id=='LS005'">
                                        <small>Referral Name <sup class="text-danger">*</sup></small>
                                        <input type="hidden" name="old_refname" id="old_refname"
                                            value="{{ isset($clientProgram->referral_code) ? $clientProgram->referral_name : null }}">
                                        <select name="referral_code" id="referral_code"
                                            class="form-select form-select-sm w-100 select-referral" {{ $disabled }}>
                                            @if (isset($clientProgram->referral_code))
                                                <option value="{{ $clientProgram->referral_code }}" selected="selected">
                                                    {{ $clientProgram->referral_name }}</option>
                                            @endif
                                            {{-- <option data-placeholder="true"></option> --}}

                                        </select>
                                        @error('referral_code')
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
                                        <input type="date" name="first_discuss_date" {{ $disabled }}
                                            id="" class="form-control form-control-sm rounded"
                                            value="{{ isset($clientProgram->first_discuss_date) ? $clientProgram->first_discuss_date : old('first_discuss_date') }}">
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
                                <textarea name="meeting_notes" {{ $disabled }} id="meeting_notes" class="w-100" id="meeting_notes">{{ isset($clientProgram->meeting_notes) ? $clientProgram->meeting_notes : old('meeting_notes') }}</textarea>
                                @error('meeting_notes')
                                    <small class="text-danger fw-light">{{ $message }}</small>
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
                                        <select name="status" v-model="status" id="program_status"
                                            class="form-select form-select-sm w-100" {{ $disabled }}>
                                            {{-- <option data-placeholder="true" {{ old('status') ?? 'selected' }}></option> --}}
                                            <option value="0">Pending
                                            </option>
                                            <option value="1">Success</option>
                                            <option value="2">Failed</option>
                                            @if (isset($clientProgram->invoice->receipt))
                                                <option value="3"
                                                    {{ old('status') !== null && old('status') == 3 ? 'selected' : null }}>
                                                    Refund</option>
                                            @endif
                                            @if (isset($clientProgram))
                                                <option value="4">Hold</option>
                                                <option value="5">Stop</option>
                                            @endif
                                        </select>
                                        @error('status')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 program-detail" id="success_date" v-if="status==1">
                                        <small>Success Date <sup class="text-danger">*</sup></small>
                                        <input type="date" name="success_date" id="" {{ $disabled }}
                                            class="form-control form-control-sm rounded"
                                            value="{{ isset($clientProgram->success_date) ? $clientProgram->success_date : old('success_date') }}">
                                        @error('success_date')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 program-detail" id="failed_date" v-if="status==2">
                                        <small>Failed Date <sup class="text-danger">*</sup></small>
                                        <input type="date" name="failed_date" id="" {{ $disabled }}
                                            class="form-control form-control-sm rounded"
                                            value="{{ isset($clientProgram->failed_date) ? $clientProgram->failed_date : old('failed_date') }}">
                                        @error('failed_date')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 program-detail" id="refund_date" v-if="status==3">
                                        <small>Refund Date <sup class="text-danger">*</sup></small>
                                        <input type="date" name="refund_date" id="" {{ $disabled }}
                                            class="form-control form-control-sm rounded"
                                            value="{{ isset($clientProgram->refund_date) ? $clientProgram->refund_date : old('refund_date') }}">
                                        @error('refund_date')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>


                                    <div class="col-md-6 mt-2 program-detail" id="reason" v-if="status==2">
                                        <small>Reason <sup class="text-danger">*</sup></small>
                                        <div class="classReason" v-if="reason_id != 'other'">
                                            <select name="reason_id" v-model="reason_id"
                                                class="form-select form-select-sm w-100" {{ $disabled }}
                                                id="selectReason">
                                                <option data-placeholder="true"></option>
                                                @foreach ($reasons as $reason)
                                                    <option value="{{ $reason->reason_id }}"
                                                        @if (isset($clientProgram->reason_id) && $clientProgram->reason_id == $reason->reason_id) {{ 'selected' }}
                                                            @elseif (old('reason_id') == $reason->reason_id)
                                                                {{ 'selected' }} @endif>
                                                        {{ $reason->reason_name }}
                                                    </option>
                                                @endforeach
                                                <option value="other">
                                                    Other option
                                                </option>
                                            </select>
                                            @error('reason_id')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="d-flex align-items-center" id="inputReason"
                                            v-if="reason_id=='other'">
                                            <input type="text" name="other_reason" {{ $disabled }}
                                                class="form-control form-control-sm rounded">
                                            <div class="float-end cursor-pointer" @click="reason_id=null">
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
                                    <div class="col-md-6 mt-2 program-detail" id="reason_notes"
                                        v-if="reason_id == 'other'">
                                        <small>Reason Notes </small>
                                        <input type="text" name="reason_notes" id="" {{ $disabled }}
                                            class="form-control form-control-sm rounded"
                                            value="{{ isset($clientProgram->reason_notes) ? $clientProgram->reason_notes : old('reason_notes') }}">
                                        @error('reason_notes')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-12 mt-2 program-detail d-none" id="refund_notes"
                                        v-if="status == 3">
                                        <label for="">Refund Notes</label>
                                        <textarea name="refund_notes" id="refund_notes">{{ isset($clientProgram->refund_notes) ? $clientProgram->refund_notes : old('refund_notes') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- PROGRAM DETAIL  --}}

                        {{-- MENTORING DETAIL  --}}
                        <div class="program-detail" v-if="main_prog==1 && (status==0 || status==1)">
                            @include('pages.program.client-program.form-detail.mentoring')
                        </div>
                        {{-- TUTORING DETAIL  --}}
                        <div class="program-detail" id="pending_tutoring"
                            v-if="(main_prog==4 || main_prog==7 || main_prog==8 || main_prog==9) && (status==0 || status==1) && prog_id!='SATPREP'">
                            @include('pages.program.client-program.form-detail.tutoring')
                        </div>
                        {{-- SAT DETAIL  --}}
                        <div class="program-detail" id="success_sat_act" v-if="main_prog==4 && prog_id=='SATPREP'">
                            @include('pages.program.client-program.form-detail.sat-act')
                        </div>

                        {{-- END PROGRAM DETAIL  --}}

                        <div class="row mb-3 program-detail" id="running_status" v-if="status==1">
                            <div class="col-md-3">
                                <label for="">
                                    Running Status <sup class="text-danger">*</sup>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <select name="prog_running_status" id="" class="select w-100"
                                            {{ $disabled }}>
                                            <option data-placeholder="true"></option>
                                            <option value="0"
                                                @if (isset($clientProgram->prog_running_status) && $clientProgram->prog_running_status == 0) {{ 'selected' }}
                                                @elseif (old('prog_running_status') == 0)
                                                    {{ 'selected' }} @endif>
                                                Not yet</option>
                                            <option value="1"
                                                @if (isset($clientProgram->prog_running_status) && $clientProgram->prog_running_status == 1) {{ 'selected' }}
                                                @elseif (old('prog_running_status') == 1)
                                                    {{ 'selected' }} @endif>
                                                Ongoing</option>
                                            <option value="2"
                                                @if (isset($clientProgram->prog_running_status) && $clientProgram->prog_running_status == 2) {{ 'selected' }}
                                                @elseif (old('prog_running_status') == 2)
                                                    {{ 'selected' }} @endif>
                                                Done</option>
                                        </select>
                                        @error('prog_running_status')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- MENTOR && TUTOR  --}}
                        @include('pages.program.client-program.form-detail.mentor-tutor')

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="">
                                    PIC <sup class="text-danger">*</sup>
                                </label>
                            </div>
                            <!-- Update 01052024 not pushed -->
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <select name="empl_id" id="internal-pic" class="select w-100"
                                            {{ $disabled ?? (!$disabled && Session::get('user_role') == 'Employee' ? 'disabled' : '') }}>
                                            <option data-placeholder="true"></option>
                                            @foreach ($internalPIC as $pic)
                                                <option value="{{ $pic->id }}" @selected(old('empl_id') == $pic->id)
                                                    @selected(isset($clientProgram->empl_id) && $clientProgram->empl_id == $pic->id) @selected(Session::get('user_role') == 'Employee' && !isset($clientProgram) && Auth::user()->id == $pic->id)>
                                                    {{ $pic->first_name . ' ' . $pic->last_name }}</option>
                                            @endforeach
                                        </select>
                                        @if (!$disabled && Session::get('user_role') == 'Employee')
                                            <input type="hidden" name="empl_id" value="{{ Auth::user()->id }}">
                                        @endif
                                        @error('empl_id')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- end of update -->
                        </div>
                        <hr>
                        @if (!$disabled)
                            <div class="mt-3 text-md-end text-center">
                                <button type="submit" class="btn btn-sm btn-primary rounded">
                                    <i class="bi bi-save2 me-2"></i> Submit
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script>
        const {
            createApp,
            ref,
            onMounted,
            onUpdated
        } = Vue

        createApp({
            setup() {
                // Variable 
                const main_prog = ref('{{ $clientProgram->program->main_prog_id ?? old('main_prog') }}')
                const sub_program = ref('{{ $clientProgram->program->sub_prog_id ?? old('sub_program') }}')
                const prog_id = ref('{{ $clientProgram->prog_id ?? old('prog_id') }}')
                const lead_id = ref('{{ $clientProgram->lead_id ?? old('lead_id') }}')
                const status = ref('{{ $clientProgram->status ?? (old('status') ?? 0) }}')
                const reason_id = ref('{{ $clientProgram->reason_id ?? old('reason_id') }}')

                const subPrograms = ref([])
                const programNames = ref([])
                // End Variable 

                // Function 
                const getSubProgram = async (isTrigger = true) => {
                    Swal.fire({
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        width: '100px',
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Reset value exclude edit client program
                    if (isTrigger) {
                        sub_program.value = null;
                        prog_id.value = null;
                        subPrograms.value = [];
                        programNames.value = [];
                    }

                    const link = '{{ url('api/get/sub-program/main') }}/' + main_prog.value;

                    try {
                        const response = await axios.get(link, {
                            headers: {
                                'crm-authorization': '{{ env('CRM_AUTHORIZATION_KEY') }}'
                            }
                        });

                        subPrograms.value = response.data;

                        if (subPrograms.value.length === 0) {
                            getProgramName();
                        }
                    } catch (error) {
                        console.error(error);
                    } finally {
                        Swal.close();
                    }
                }

                const getProgramName = async () => {
                    Swal.fire({
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        width: '100px',
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Variable Link
                    let link = '{{ url('api/get/program/') }}/main/' + main_prog.value + '/sub/';
                    if (sub_program.value) {
                        link += sub_program.value;
                    }

                    try {
                        const response = await axios.get(link, {
                            headers: {
                                'crm-authorization': '{{ env('CRM_AUTHORIZATION_KEY') }}'
                            }
                        });

                        programNames.value = response.data;
                    } catch (error) {
                        console.error(error);
                    } finally {
                        Swal.close();
                    }
                }

                const renderCKEditor = () => {
                    var myEditor;

                    document.querySelectorAll('textarea:not(#review):not(#swal2-textarea)').forEach(function(
                        element) {
                        // Cek apakah sudah diinisialisasi
                        if (element.getAttribute('data-ckeditor-initialized') === 'true') {
                            return; // Skip jika sudah diinisialisasi
                        }

                        ClassicEditor
                            .create(element, {
                                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList',
                                    'numberedList', 'blockQuote'
                                ],
                                heading: {
                                    options: [{
                                            model: 'paragraph',
                                            title: 'Paragraph',
                                            class: 'ck-heading_paragraph'
                                        },
                                        {
                                            model: 'heading1',
                                            view: 'h1',
                                            title: 'Heading 1',
                                            class: 'ck-heading_heading1'
                                        },
                                        {
                                            model: 'heading2',
                                            view: 'h2',
                                            title: 'Heading 2',
                                            class: 'ck-heading_heading2'
                                        }
                                    ]
                                }
                            })
                            .then(editor => {
                                console.log('Editor was initialized', editor);
                                myEditor = editor;

                                // Tandai bahwa editor telah diinisialisasi
                                element.setAttribute('data-ckeditor-initialized', 'true');
                            })
                            .catch(error => {
                                console.error(error);
                            });
                    });
                }

                // End function 

                onUpdated(() => {
                    $('.select').select2({
                        placeholder: "Select value",
                        allowClear: true
                    });

                    renderCKEditor()
                })

                onMounted(async () => {
                    if (main_prog.value) {
                        await getSubProgram(false)
                        await getProgramName()
                    }
                })

                return {
                    main_prog,
                    sub_program,
                    prog_id,
                    lead_id,
                    status,
                    reason_id,
                    subPrograms,
                    programNames,
                    getSubProgram,
                    getProgramName
                }
            }
        }).mount('#app')
    </script>
@endsection
