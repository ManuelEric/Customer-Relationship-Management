@extends('layout.main')

@section('title', 'Referral - Bigdata Platform')

@section('content')

    @php
        $disabled = isset($referral) && isset($edit) ? null : (isset($edit) ? null : 'disabled');
    @endphp

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('program/referral') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Referral
        </a>
    </div>


    <div class="row">
        <div class="col-md-4 text-center">
            <div class="card rounded mb-3">
                <div class="card-body">
                    <img src="{{ asset('img/program.webp') }}" alt="" class="w-75">
                    @if (isset($referral))
                        <div class="mt-3 d-flex justify-content-center">
                            @if (empty($edit))
                                <a href="{{ route('referral.edit', ['referral' => $referral->id]) }}"
                                    class="btn btn-sm btn-outline-info rounded mx-1">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            @else
                                <a href="{{ route('referral.show', ['referral' => $referral->id]) }}"
                                    class="btn btn-sm btn-outline-info rounded mx-1">
                                    <i class="bi bi-arrow-left"></i>
                                    Back</a>
                            @endif
                            <button type="button" class="btn btn-sm btn-outline-danger rounded mx-1"
                                onclick="confirmDelete('program/referral', '{{ $referral->id }}')">
                                <i class="bi bi-trash2"></i> Delete
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card rounded mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-tags me-2"></i>
                            Referral
                        </h6>
                    </div>
                </div>
                <div class="card-body">
                    <form
                        action="{{ isset($referral) ? route('referral.update', ['referral' => $referral->id]) : route('referral.store') }}"
                        method="POST">
                        @csrf
                        @if (isset($referral))
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label>Type</label>
                                <select name="referral_type" class="select w-100" id="type" onchange="checkType()"
                                    {{ $disabled }}>
                                    <option data-placeholder="true"></option>
                                    <option value="In"
                                        {{ isset($referral->referral_type) && $referral->referral_type == 'In' ? 'selected' : null }}>
                                        Referral In</option>
                                    <option value="Out"
                                        {{ isset($referral->referral_type) && $referral->referral_type == 'Out' ? 'selected' : null }}>
                                        Referral Out</option>
                                </select>
                                @error('referral_type')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Partner Name</label>
                                <select name="partner_id" class="select w-100" {{ $disabled }}>
                                    <option data-placeholder="true"></option>
                                    @foreach ($partners as $partner)
                                        <option value="{{ $partner->corp_id }}"
                                            {{ isset($referral->partner_id) && $referral->partner_id == $partner->corp_id ? 'selected' : null }}>
                                            {{ $partner->corp_name }}</option>
                                    @endforeach
                                </select>
                                @error('partner_id')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Program Name</label>
                                <div id="selectProgram">
                                    <select name="prog_id" class="select w-100" {{ $disabled }}>
                                        <option data-placeholder="true"></option>
                                        @foreach ($programs as $program)
                                            <option value="{{ $program->prog_id }}"
                                                {{ isset($referral->prog_id) && $referral->prog_id == $program->prog_id ? 'selected' : null }}>
                                                {{ $program->prog_program }} from {{ $program->main_prog->prog_name }}
                                                {{ isset($program->sub_prog) ? ' - ' . $program->sub_prog->sub_prog_name : null }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('prog_id')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div id="inputProgram" class="d-none">
                                    <input type="text" name="additional_prog_name"
                                        value="{{ isset($referral->additional_prog_name) ? $referral->additional_prog_name : null }}"
                                        class="form-control form-control-sm rounded" {{ $disabled }}>
                                </div>
                                @error('additional_prog_name')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Referral Date</label>
                                <input type="date" name="ref_date"
                                    value="{{ isset($referral->ref_date) ? $referral->ref_date : null }}"
                                    class="form-control form-control-sm rounded" {{ $disabled }}>
                                @error('ref_date')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Participant</label>
                                <input type="number" name="number_of_student"
                                    value="{{ isset($referral->number_of_student) ? $referral->number_of_student : null }}"
                                    class="form-control form-control-sm rounded" {{ $disabled }}>
                                @error('number_of_student')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Amount</label>
                                <div class="row g-0">
                                    <div class="col-3">
                                        <select name="" id="" class="select w-100">
                                            <option value="IDR">IDR</option>
                                            <option value="USD">USD</option>
                                            <option value="SGD">SGD</option>
                                        </select>
                                    </div>
                                    <div class="col-9">
                                        <input type="number" name="revenue"
                                            value="{{ isset($referral->revenue) ? $referral->revenue : null }}"
                                            class="form-control form-control-sm rounded" {{ $disabled }}>
                                        @error('revenue')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label>Notes</label>
                                <textarea name="notes" {{ $disabled }}>
                                    {{ isset($referral->notes) ? $referral->notes : null }}
                                </textarea>
                                @error('notes')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-2">
                                <label>PIC </label>
                                <select name="empl_id" class="select w-100" {{ $disabled }}>
                                    <option data-placeholder="true"></option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                            {{ isset($referral->empl_id) && $referral->empl_id == $employee->id ? 'selected' : null }}>
                                            {{ $employee->first_name . ' ' . $employee->last_name }}</option>
                                    @endforeach
                                </select>
                                @error('empl_id')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            @if (!$disabled)
                                <div class="mt-3 text-end">
                                    <button class="btn btn-sm btn-primary rounded" type="submit"><i
                                            class="bi bi-save2 me-1"></i> Submit</button>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function checkType() {
            let type = $('#type').val()
            if (type == 'In') {
                $('#selectProgram').removeClass('d-none')
                $('#inputProgram').addClass('d-none')
            } else {
                $('#inputProgram').removeClass('d-none')
                $('#selectProgram').addClass('d-none')
            }
        }

        $(document).ready(function() {

            @if (isset($referral) && $referral->referral_type == 'In')
                $('#selectProgram').removeClass('d-none')
                $('#inputProgram').addClass('d-none')
            @else
                $('#inputProgram').removeClass('d-none')
                $('#selectProgram').addClass('d-none')
            @endif

            //     $("input[name=revenue]").change(function() {
            //         var val = $(this).val()

            //         $(this).val(formatRupiah(val))
            //     })
        })
    </script>
@endsection
