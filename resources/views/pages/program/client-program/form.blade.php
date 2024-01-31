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
    {{--     
    @if ($open_information_for_tutor)
    <div class="alert alert-danger">
        The specific field that needs your attention is "Session Detail". Currently, it appears to be blank, and we kindly request you to provide the necessary information.
    </div>
    @endif --}}

    <div class="row">
        <div class="col-md-4">
            <div class="card rounded mb-3">
                <div class="card-body text-center">
                    <h3><i class="bi bi-person"></i></h3>
                    <h4>{{ $student->full_name }}</h4>
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
                        method="POST">
                        @csrf
                        @if (isset($clientProgram))
                            @method('PUT')
                        @endif
                        <input type="hidden" name="queryP" value="{{ isset($_GET['p']) ? $_GET['p'] : null }}">
                        <div class="row mb-2">
                            <div class="col-md-3">
                                <label for="">
                                    Program Name <sup class="text-danger">*</sup>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <select name="prog_id" id="program_name" class="select w-100"
                                    onchange="changeProgramStatus()" {{ $disabled }}>
                                    <option data-placeholder="true"></option>
                                    @foreach ($programs as $program)
                                        <option data-pmentor="{{ $program->prog_mentor }}"
                                            data-mprog="{{ $program->main_prog->prog_name }}"
                                            data-sprog="{{ isset($program->sub_prog->sub_prog_name) ? $program->sub_prog->sub_prog_name : null }}"
                                            value="{{ $program->prog_id }}"
                                            @if (!empty(old('prog_id')) && old('prog_id') == $program->prog_id) {{ 'selected' }}
                                            @elseif (isset($clientProgram) && $clientProgram->prog_id == $program->prog_id)
                                                {{ 'selected' }} @endif>
                                            {{ $program->prog_sub != '-' ? $program->prog_sub . ' - ' : '' }}
                                            {{ $program->prog_program }}</option>
                                    @endforeach
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
                                        <select name="lead_id" id="main_lead" class="select w-100" {{ $disabled }}>
                                            <option data-placeholder="true"></option>
                                            @if (isset($leads) && count($leads) > 0)
                                                @foreach ($leads as $lead)
                                                    <option data-lead="{{ $lead->main_lead }}"
                                                        value="{{ $lead->lead_id }}"
                                                        @if (old('lead_id') !== null) {{ old('lead_id') == $lead->lead_id ? 'selected' : null }}
                                                            @elseif (isset($clientProgram->lead_id) && $clientProgram->lead_id == $lead->lead_id)
                                                                {{ 'selected' }} @endif>
                                                        {{ $lead->main_lead }}</option>
                                                @endforeach
                                                {{-- <option value="program">ALL-in Event</option>
                                                <option value="edufair">Edufair External</option> --}}
                                                <option data-lead="KOL" value="kol"
                                                    @if (old('lead_id') && old('lead_id') == 'kol') {{ 'selected' }}
                                                    @elseif (isset($clientProgram->lead_id) && $clientProgram->lead->main_lead == 'KOL')
                                                        {{ 'selected' }} @endif>
                                                    KOL</option>
                                            @endif
                                        </select>
                                        @error('lead_id')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 d-none" id="event">
                                        <small>Sub Lead <sup class="text-danger">*</sup></small>
                                        <select name="clientevent_id" id="event_id" class="select w-100"
                                            {{ $disabled }}>
                                            <option data-placeholder="true"></option>
                                            @foreach ($clientEvents as $clientEvent)
                                                <option value="{{ $clientEvent->clientevent_id }}"
                                                    @if (old('clientevent_id') == $clientEvent->clientevent_id) {{ 'selected' }}
                                                    @elseif (isset($clientProgram->clientevent_id) && $clientProgram->clientevent_id == $clientEvent->clientevent_id)
                                                        {{ 'selected' }} @endif>
                                                    {{ $clientEvent->event->event_title }}</option>
                                            @endforeach
                                        </select>
                                        @error('event_id')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 d-none" id="edufair">
                                        <small>Sub Lead <sup class="text-danger">*</sup></small>
                                        <select name="eduf_lead_id" id="eduf_id" class="select w-100"
                                            {{ $disabled }}>
                                            <option data-placeholder="true"></option>
                                            @forelse ($external_edufair as $edufair)
                                                <option value="{{ $edufair->id }}"
                                                    @if (old('eduf_id') == $edufair->id) {{ 'selected' }}
                                                    @elseif (isset($clientProgram) && ($clientProgram->eduf_lead_id == $edufair->id))
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
                                    <div class="col-md-6 d-none" id="kol">
                                        <small>Sub Lead <sup class="text-danger">*</sup></small>
                                        <select name="kol_lead_id" id="kol_lead_id" class="select w-100"
                                            {{ $disabled }}>
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
                                    <div class="col-md-6 d-none" id="partner">
                                        <small>Sub Lead <sup class="text-danger">*</sup></small>
                                        <select name="partner_id" id="partner_id" class="select w-100" {{ $disabled }}>
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
                                    <div class="col-md-6 d-none" id="referral">
                                        <small>Sub Lead <sup class="text-danger">*</sup></small>
                                        <select name="referral_code" id="referral_code" class="select w-100" {{ $disabled }}>
                                            <option data-placeholder="true"></option>
                                            @if (isset($listReferral) && count($listReferral) > 0)
                                                @foreach ($listReferral as $referral)
                                                    <option value="{{ $referral->viewClientRefCode->ref_code }}"
                                                        @if (old('referral_code') == $referral->viewClientRefCode->ref_code) {{ 'selected' }}
                                                        @elseif (isset($clientProgram) && $clientProgram->referral_code == $referral->viewClientRefCode->ref_code)
                                                            {{ 'selected' }} @endif>
                                                            {{ $referral->full_name }}</option>
                                                @endforeach
                                            @endif
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
                                <textarea name="meeting_notes" {{ $disabled }} id="" class="w-100">{{ isset($clientProgram->meeting_notes) ? $clientProgram->meeting_notes : old('meeting_notes') }}</textarea>
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
                                        <select name="status" id="program_status" class="select w-100"
                                            onchange="changeProgramStatus()" {{ $disabled }}>
                                            {{-- <option data-placeholder="true" {{ old('status') ?? 'selected' }}></option> --}}
                                            <option value="0"
                                                @if (old('status') !== null && old('status') == 0) {{ 'selected' }} @endif>Pending
                                            </option>
                                            <option value="1"
                                                {{ old('status') !== null && old('status') == 1 ? 'selected' : null }}>
                                                Success</option>
                                            <option value="2"
                                                {{ old('status') !== null && old('status') == 2 ? 'selected' : null }}>
                                                Failed</option>
                                            @if (isset($clientProgram->invoice->receipt))
                                                <option value="3"
                                                    {{ old('status') !== null && old('status') == 3 ? 'selected' : null }}>
                                                    Refund</option>
                                            @endif
                                        </select>
                                        @error('status')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 program-detail d-none" id="success_date">
                                        <small>Success Date <sup class="text-danger">*</sup></small>
                                        <input type="date" name="success_date" id="" {{ $disabled }}
                                            class="form-control form-control-sm rounded"
                                            value="{{ isset($clientProgram->success_date) ? $clientProgram->success_date : old('success_date') }}">
                                        @error('success_date')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 program-detail d-none" id="failed_date">
                                        <small>Failed Date <sup class="text-danger">*</sup></small>
                                        <input type="date" name="failed_date" id="" {{ $disabled }}
                                            class="form-control form-control-sm rounded"
                                            value="{{ isset($clientProgram->failed_date) ? $clientProgram->failed_date : old('failed_date') }}">
                                        @error('failed_date')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 program-detail d-none" id="refund_date">
                                        <small>Refund Date <sup class="text-danger">*</sup></small>
                                        <input type="date" name="refund_date" id="" {{ $disabled }}
                                            class="form-control form-control-sm rounded"
                                            value="{{ isset($clientProgram->refund_date) ? $clientProgram->refund_date : old('refund_date') }}">
                                        @error('refund_date')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>


                                    <div class="col-md-12 mt-2 program-detail d-none" id="reason">
                                        <small>Reason <sup class="text-danger">*</sup></small>
                                        <div class="classReason">
                                            <select name="reason_id" class="select w-100" {{ $disabled }}
                                                style="display: none !important; width:100% !important" id="selectReason"
                                                onchange="otherOption($(this).val())">
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

                                        <div class="d-flex align-items-center d-none" id="inputReason">
                                            <input type="text" name="other_reason" {{ $disabled }}
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

                                    <div class="col-md-12 mt-2 program-detail" id="refund_notes">
                                        <label for="">Refund Notes</label>
                                        <textarea name="refund_notes" id="">{{ isset($clientProgram->refund_notes) ? $clientProgram->refund_notes : old('refund_notes') }}</textarea>
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
                        <section id="available-mentor" class="d-none mentor-tutor">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="">
                                        Main Mentor <sup class="text-danger">*</sup>
                                    </label>
                                </div>
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <select name="main_mentor" id="" class="select w-100"
                                                {{ $disabled }}>
                                                <option data-placeholder="true"></option>
                                                @foreach ($mentors as $mentor)
                                                    <option value="{{ $mentor->id }}"
                                                        @if (old('main_mentor') == $mentor->id) {{ 'selected' }}
                                                        @elseif (isset($clientProgram->clientMentor) &&
                                                                $clientProgram->clientMentor()->orderBy('tbl_client_mentor.id', 'asc')->count() > 0)
                                                            @if ($clientProgram->clientMentor()->orderBy('tbl_client_mentor.id', 'asc')->first()->id == $mentor->id)
                                                                {{ 'selected' }} @endif
                                                        @endif
                                                        >{{ $mentor->first_name . ' ' . $mentor->last_name }}</option>
                                                @endforeach
                                            </select>
                                            @error('main_mentor')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="">
                                        Backup Mentor
                                    </label>
                                </div>
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <select name="backup_mentor" id="" class="select w-100"
                                                {{ $disabled }}>
                                                <option data-placeholder="true"></option>
                                                @foreach ($mentors as $mentor)
                                                    <option value="{{ $mentor->id }}"
                                                        @if (old('backup_mentor') == $mentor->id) {{ 'selected' }}
                                                        @elseif (isset($clientProgram->clientMentor) &&
                                                                $clientProgram->clientMentor()->orderBy('tbl_client_mentor.id', 'desc')->count() > 1)
                                                            @if ($clientProgram->clientMentor()->orderBy('tbl_client_mentor.id', 'desc')->first()->id == $mentor->id)
                                                                {{ 'selected' }} @endif
                                                        @endif
                                                        >{{ $mentor->first_name . ' ' . $mentor->last_name }}</option>
                                                @endforeach
                                            </select>
                                            @error('backup_mentor')
                                                <small class="text-danger fw-light">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <section id="available-tutor" class="d-none mentor-tutor">
                            <div id="tutoring" class="d-none">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="">
                                            Tutor <sup class="text-danger">*</sup>
                                        </label>
                                    </div>
                                    <div class="col-md">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <select name="tutor_id" id="" class="select w-100"
                                                    {{ $disabled }}>
                                                    <option data-placeholder="true"></option>
                                                    @foreach ($tutors as $tutor)
                                                        <option value="{{ $tutor->id }}"
                                                            @if (isset($clientProgram->clientMentor) && $clientProgram->clientMentor()->count() > 0) @if ($clientProgram->clientMentor()->first()->id == $tutor->id)
                                                                    {{ 'selected' }} @endif
                                                            @endif
                                                            @selected(old('tutor_id') == $tutor->id)
                                                            >{{ $tutor->first_name .' ' .$tutor->last_name .' - ' .json_encode($tutor->roles()->where('role_name', 'Tutor')->pluck('tutor_subject')->toArray()) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('tutor_id')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="sat-act" class="d-none">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="">
                                            Tutor 1<sup class="text-danger">*</sup>
                                        </label>
                                    </div>
                                    <div class="col-md">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <select name="tutor_1" class="select w-100" {{ $disabled }}>
                                                    <option data-placeholder="true"></option>
                                                    @foreach ($tutors as $tutor)
                                                        <option value="{{ $tutor->id }}"
                                                            @if (isset($clientProgram->clientMentor) && $clientProgram->clientMentor()->count() > 0) @if ($clientProgram->clientMentor()->orderBy('tbl_client_mentor.id', 'asc')->first()->id == $tutor->id)
                                                                    {{ 'selected' }} @endif
                                                        @elseif (old('tutor_1') == $tutor->id) {{ 'selected' }}
                                                            @endif
                                                            >{{ $tutor->first_name .' ' .$tutor->last_name .' - ' .json_encode($tutor->roles()->where('role_name', 'Tutor')->pluck('tutor_subject')->toArray()) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('tutor_1')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" name="timesheet_1" id=""
                                                    {{ $disabled }} class="form-control form-control-sm rounded"
                                                    placeholder="Timesheet 1"
                                                    value="{{ isset($clientProgram->clientMentor[0]->pivot->timesheet_link) ? $clientProgram->clientMentor[0]->pivot->timesheet_link : old('timesheet_1') }}">
                                                @error('timesheet_1')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="">
                                            Tutor 2<sup class="text-danger">*</sup>
                                        </label>
                                    </div>
                                    <div class="col-md">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <select name="tutor_2" class="select w-100" {{ $disabled }}>
                                                    <option data-placeholder="true"></option>
                                                    @foreach ($tutors as $tutor)
                                                        <option value="{{ $tutor->id }}"
                                                            @if (isset($clientProgram->clientMentor) && $clientProgram->clientMentor()->count() > 1) @if ($clientProgram->clientMentor()->orderBy('tbl_client_mentor.id', 'desc')->first()->id == $tutor->id)
                                                                    {{ 'selected' }} @endif
                                                        @elseif (old('tutor_2') == $tutor->id) {{ 'selected' }}
                                                            @endif
                                                            >{{ $tutor->first_name .' ' .$tutor->last_name .' - ' .json_encode($tutor->roles()->where('role_name', 'Tutor')->pluck('tutor_subject')->toArray()) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('tutor_2')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" name="timesheet_2" id=""
                                                    {{ $disabled }} class="form-control form-control-sm rounded"
                                                    placeholder="Timesheet 2"
                                                    value="{{ isset($clientProgram->clientMentor[1]->pivot->timesheet_link) ? $clientProgram->clientMentor[1]->pivot->timesheet_link : old('timesheet_2') }}">
                                                @error('timesheet_2')
                                                    <small class="text-danger fw-light">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
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
                                        <select name="empl_id" id="internal-pic" class="select w-100" {{ !$disabled && Session::get('user_role') == 'Employee' ? 'disabled' : '' }}>
                                            <option data-placeholder="true"></option>
                                            @foreach ($internalPIC as $pic)
                                                <option value="{{ $pic->id }}"
                                                    @if (old('empl_id') == $pic->id) {{ 'selected' }}
                                                    @elseif (isset($clientProgram->empl_id) && $clientProgram->empl_id == $pic->id)
                                                        {{ 'selected' }} 
                                                    @elseif (Session::get('user_role') == 'Employee' && !isset($clientProgram) && Auth::user()->id == $pic->id)
                                                        {{ 'selected' }}    
                                                    @endif>
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

            @include('pages.program.client-program.detail.payment')
        </div>
    </div>

@endsection
@push('scripts')
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
        console.log("A")
        var program = $("#program_name option:selected")
        var lead = $(this).select2().find(":selected").data('lead')
        let programName = program.text()

        if (programName) {
            if (lead.includes('All-In Event')) {

                $("#event").removeClass('d-none')
                $("#edufair").addClass("d-none")
                $("#kol").addClass("d-none")
                $("#partner").addClass("d-none")
                $("#referral").addClass("d-none")

            } else if (lead.includes('External Edufair')) {

                $("#event").addClass("d-none")
                $("#edufair").removeClass("d-none")
                $("#kol").addClass("d-none")
                $("#partner").addClass("d-none")
                $("#referral").addClass("d-none")

            } else if (lead.includes('KOL')) {

                $("#event").addClass("d-none")
                $("#edufair").addClass("d-none")
                $("#kol").removeClass("d-none")
                $("#partner").addClass("d-none")
                $("#referral").addClass("d-none")

            } else if (lead.includes('All-In Partners')) {

                $("#event").addClass("d-none")
                $("#edufair").addClass("d-none")
                $("#kol").addClass("d-none")
                $("#partner").removeClass("d-none")
                $("#referral").addClass("d-none")

            } else if (lead.includes('Referral')) {

                $("#event").addClass("d-none")
                $("#edufair").addClass("d-none")
                $("#kol").addClass("d-none")
                $("#partner").addClass("d-none")
                $("#referral").removeClass("d-none")

            } else {

                $("#event").addClass("d-none")
                $("#edufair").addClass("d-none")
                $("#kol").addClass("d-none")
                $("#partner").addClass("d-none")
                $("#referral").addClass("d-none")

            }
        } else {
            notification('warning', 'Please, select program name first!')
            $('#main_lead').select2('destroy');
            $('#main_lead').val(null);
            $('#main_lead').select2({
                placeholder: "Select value",
                allowClear: true
            });
            $('#program_name').select2('open');
        }
    })


    function changeProgramStatus() {

        var program = $("#program_name option:selected")
        let programName = program.text()
        let prog_mentor = program.data('pmentor');
        let programMainProg = program.data('mprog')
        let programSubProg = program.data('sprog')
        let programStatus = $('#program_status').val()
        $('.program-detail').addClass('d-none')
        $('.mentor-tutor').addClass('d-none')

        if (programName) {
            if (programStatus == 0) { // pending
                if (programMainProg.includes('Admission') || programSubProg.includes('Admission')) { // mentoring

                    $('#pending_mentoring').removeClass('d-none')

                } else if (programMainProg.includes('Tutoring') || programSubProg.includes('Tutoring')) {

                    $('#pending_tutoring').removeClass('d-none')

                }
            } else if (programStatus == 1) { // success
                $('#success_date').removeClass('d-none')
                $('#running_status').removeClass('d-none')

                // Detail Program Check 
                if (programMainProg.includes('Admission') || programSubProg.includes('Admission')) { // mentoring
                    $('#success_mentoring').removeClass('d-none')
                } else if (programMainProg.includes('Tutoring') || programSubProg.includes('Tutoring')) {
                    $('#success_tutoring').removeClass('d-none')
                } else if (programMainProg.includes('ACT') || programSubProg.includes('ACT') || programMainProg
                    .includes(
                        'SAT') || programSubProg.includes('SAT')) {

                    $('#success_sat_act').removeClass('d-none')
                }

                // Mentor & Tutor Needs Check 
                switch (prog_mentor) {
                    case "Mentor":
                        $("#available-mentor").removeClass("d-none")
                        $("#available-tutor").addClass("d-none")

                        break;

                    case "Tutor":
                        $("#available-mentor").addClass("d-none")
                        $("#available-tutor").removeClass("d-none")
                        if (programMainProg.includes('Tutoring') || programSubProg.includes('Tutoring')) {
                            $('#tutoring').removeClass('d-none')
                            $('#sat-act').addClass('d-none')
                        } else if (programMainProg.includes('ACT') || programSubProg.includes('ACT') || programMainProg
                            .includes('SAT') || programSubProg.includes('SAT')) {
                            $('#tutoring').addClass('d-none')
                            $('#sat-act').removeClass('d-none')

                        }
                        break;
                }

            } else if (programStatus == 2) { // failed
                $('#failed_date').removeClass('d-none')
                $('#reason').removeClass('d-none')
            } else if (programStatus == 3) { // refund
                $('#refund_date').removeClass('d-none')
                $('#refund_notes').removeClass('d-none')
                $('#reason').removeClass('d-none')
            }
        } else {
            notification('warning', 'Please, select program name first!')
            $('#program_status').select2('destroy');
            $('#program_status').val(null);
            $('#program_status').select2({
                placeholder: "Select value",
                allowClear: true
            });
            $('#program_name').select2('open');
        }

    }

    // changeProgramStatus()
</script>
<script>
    $(document).ready(function() {

        $("input[name=session]").on('change', function() {
            var start_date = $("input[name=prog_start_date]").val();
            var end_date = $("input[name=prog_end_date]").val();

            var start_date_local = start_date + "T00:00";
            var end_date_local = end_date + "T23:59";

            if (start_date == '' || end_date == '') {
                notification('error',
                    'Please fill the start date and end date before fill the schedule session.');
                $(this).val(null);
                return;
            }

            var val = $(this).val();

            if (val < 1) {
                $(this).val(1)
                val = 1;
            }

            var i = 1;
            var html = '';

            while (i <= val) {

                html += '<div class="row mb-3 schedule-' + i + '">' +
                    '<div class="col-md-3">' +
                    '<label>Session ' + i + '.<sup class="text-danger">*</sup></label>' +
                    '</div>' +
                    '<div class="col-md-5">' +
                    '<small>Schedule</small>' +
                    '<input type="datetime-local" required class="form-control form-control-sm rounded" min="' +
                    start_date_local + '" max="' + end_date_local + '" name="sessionDetail[]">' +
                    '</div>' +
                    '<div class="col-md-4">' +
                    '<small>Zoom link</small>' +
                    '<input type="url" required class="form-control form-control-sm rounded" name="sessionLinkMeet[]">' +
                    '</div>' +
                    '</div>';

                i++;
            }

            $("#section-session").html(html);
        })

        @if (isset($clientProgram) && $clientProgram->status !== false)
            $("#program_status").val("{{ $clientProgram->status }}").trigger('change');
        @endif

        @error('followup_date')
            $("#plan").modal('show')
        @enderror

        const documentReady = () => {

            @if (isset($p) && $p !== null)
                $("#program_name").select2().val("{{ $p }}").trigger('change');
            @elseif (isset($clientProgram))
                $("#program_name").select2().val("{{ $clientProgram->prog_id }}").trigger('change');
            @endif

            @if (old('lead_id') !== null)
                $("#main_lead").select2().val("{{ old('lead_id') }}").trigger('change');
            @elseif (isset($clientProgram->lead_id))
                @if ($clientProgram->lead->main_lead == 'KOL')
                    $("#main_lead").select2().val("kol").trigger('change');
                @else
                    $("#main_lead").select2().val("{{ $clientProgram->lead_id }}").trigger('change');
                @endif
            @endif

            @if (old('event_id') !== null)
                $("#event_id").select2().val("{{ old('event_id') }}").trigger('change');
            @endif

            @if (old('kol_lead_id') !== null)
                $("#kol_lead_id").select2().val("{{ old('kol_lead_id') }}").trigger('change');
            @endif

            @if (old('eduf_id') !== null)
                $("#eduf_id").select2().val("{{ old('eduf_id') }}").trigger('change');
            @endif

            @if (old('partner_id') !== null)
                $("#partner_id").select2().val("{{ old('partner_id') }}").trigger('change');
            @endif

            @if (old('referral_code') !== null)
                $("#referral_code").select2().val("{{ old('referral_code') }}").trigger('change');
            @endif

            @if (old('status') !== null)
                $("#program_status").select2().val("{{ (int) old('status') }}").trigger('change');
            @endif

        }

        documentReady();
    })
</script>
@endpush
