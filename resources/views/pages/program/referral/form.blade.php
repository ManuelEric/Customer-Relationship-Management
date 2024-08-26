@extends('layout.main')

@section('title', 'Referral ')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Referral</a></li>
    <li class="breadcrumb-item active" aria-current="page">Form Referral</li>
@endsection
@section('content')

    @php
        $disabled = isset($referral) && isset($edit) ? null : (isset($edit) ? null : 'disabled');
    @endphp

    <div class="row">
        <div class="col-md-4 text-center">
            <div class="card rounded mb-3">
                <div class="card-body">
                    <img loading="lazy"  src="{{ asset('img/program.webp') }}" alt="" class="w-75">
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
                                <label>Type <sup class="text-danger">*</sup></label>
                                <select name="referral_type" class="select w-100" id="type" aria-labelledby="typeHelpBlock" onchange="checkType()"
                                    {{ $disabled }}>
                                    <option data-placeholder="true"></option>
                                    <option value="In"
                                        {{ isset($referral->referral_type) && $referral->referral_type == 'In' || old('referral_type') == 'In' ? 'selected' : null }}>
                                        Referral In</option>
                                    <option value="Out"
                                        {{ isset($referral->referral_type) && $referral->referral_type == 'Out' || old('referral_type') == 'Out' ? 'selected' : null }}>
                                        Referral Out</option>
                                </select>
                                <div id="typeHelpBlock" class="form-text">
                                    Base on client
                                </div>
                                @error('referral_type')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Partner Name <sup class="text-danger">*</sup></label>
                                <select name="partner_id" class="select w-100" {{ $disabled }}>
                                    <option data-placeholder="true"></option>
                                    @foreach ($partners as $partner)
                                        <option value="{{ $partner->corp_id }}"
                                            {{ isset($referral->partner_id) && $referral->partner_id == $partner->corp_id || old('partner_id') == $partner->corp_id ? 'selected' : null }}>
                                            {{ $partner->corp_name }}</option>
                                    @endforeach
                                </select>
                                @error('partner_id')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Program Name <sup class="text-danger">*</sup></label>
                                <div id="selectProgram">
                                    <select name="prog_id" class="select w-100" {{ $disabled }}>
                                        <option data-placeholder="true"></option>
                                        @foreach ($programs as $program)
                                            <option value="{{ $program->prog_id }}"
                                                {{ isset($referral->prog_id) && $referral->prog_id == $program->prog_id || old('prog_id') == $program->prog_id ? 'selected' : null }}>
                                                {{ $program->program_name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('prog_id')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div id="inputProgram" class="d-none">
                                    <input type="text" name="additional_prog_name"
                                        value="{{ isset($referral->additional_prog_name) ? $referral->additional_prog_name : old('additional_prog_name') }}"
                                        class="form-control form-control-sm rounded" {{ $disabled }}>
                                </div>
                                @error('additional_prog_name')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Referral Date <sup class="text-danger">*</sup></label>
                                <input type="date" name="ref_date"
                                    value="{{ isset($referral->ref_date) ? $referral->ref_date : old('ref_date') }}"
                                    class="form-control form-control-sm rounded" {{ $disabled }}>
                                @error('ref_date')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Participant <sup class="text-danger">*</sup></label>
                                <input type="number" name="number_of_student"
                                    value="{{ isset($referral->number_of_student) ? $referral->number_of_student : old('number_of_student') }}"
                                    class="form-control form-control-sm rounded" {{ $disabled }}>
                                @error('number_of_student')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Referral fee <sup class="text-danger">*</sup></label>
                                <div class="row g-0">
                                    <div class="col-3">
                                        <select name="currency" id="currency" class="select w-100" {{ $disabled }}  onchange="checkCurrency()">
                                            <option value="IDR" {{ isset($referral->currency) && $referral->currency == "IDR" ? "selected" : null }}>IDR</option>
                                            <option value="USD" {{ isset($referral->currency) && $referral->currency == "USD" ? "selected" : null }}>USD</option>
                                            <option value="SGD" {{ isset($referral->currency) && $referral->currency == "SGD" ? "selected" : null }}>SGD</option>
                                            <option value="GBP" {{ isset($referral->currency) && $referral->currency == "GBP" ? "selected" : null }}>GBP</option>
                                            <option value="AUD" {{ isset($referral->currency) && $referral->currency == "AUD" ? "selected" : null }}>AUD</option>
                                            <option value="VND" {{ isset($referral->currency) && $referral->currency == "VND" ? "selected" : null }}>VND</option>
                                            <option value="MYR" {{ isset($referral->currency) && $referral->currency == "MYR" ? "selected" : null }}>MYR</option>
                                            <option value="JPY" {{ isset($referral->currency) && $referral->currency == "JPY" ? "selected" : null }}>JPY</option>
                                            <option value="CNY" {{ isset($referral->currency) && $referral->currency == "CNY" ? "selected" : null }}>CNY</option>
                                            <option value="THB" {{ isset($referral->currency) && $referral->currency == "THB" ? "selected" : null }}>THB</option>
                                        </select>
                                    </div>
                                    <div class="col-9">
                                        <input type="number" name="revenue" id="revenue"
                                            value=
                                                "{{ isset($referral) ?
                                                        ($referral->currency == "IDR" ? $referral->revenue : $referral->revenue_other)
                                                    : old('revenue')
                                                }}"
                                            class="form-control form-control-sm rounded" {{ $disabled }}
                                            oninput="checkReferralOther()">
                                        @error('revenue')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2 referral-other d-none">
                                <label>Current Rate to IDR <sup class="text-danger">*</sup></label>
                                    <input type="text" name="curs_rate" id="curs_rate"
                                        value="{{ isset($referral->curs_rate) ? $referral->curs_rate : old('curs_rate') }}"
                                        class="form-control form-control-sm rounded" {{ $disabled }}>

                                    @error('curs_rate')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                            </div>
                            <div class="col-md-6 mb-2 referral-other d-none">
                                <label>Referral Fee IDR <sup class="text-danger">*</sup></label>
                                <input type="number" name="revenue_idr" id="revenue_idr"
                                    value="{{ 
                                            isset($referral) ?
                                                (isset($referral->revenue) && $referral->currency != "IDR" ? $referral->revenue : old('revenue_idr')) 
                                            : null
                                        }}"
                                    class="form-control form-control-sm rounded" {{ $disabled }}>
                                @error('revenue_idr')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-2">
                                <label>Notes</label>
                                <textarea name="notes" {{ $disabled }}>
                                    {{ isset($referral->notes) ? $referral->notes : old('notes') }}
                                </textarea>
                                @error('notes')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-2">
                                <label>PIC <sup class="text-danger">*</sup></label>
                                <select name="empl_id" class="select w-100" {{ $disabled }}>
                                    <option data-placeholder="true"></option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                            {{ isset($referral->empl_id) && $referral->empl_id == $employee->id || old('empl_id') == $employee->id ? 'selected' : null }}>
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

        function checkCurrency() {
            let cur = $('#currency').val()
            // console.log()
            const currency = ['USD', 'SGD', 'GBP', 'AUD', 'VND', 'MYR', 'JPY', 'CNY', 'THB']
            if (currency.includes(cur)) {
                $('.referral-other').removeClass('d-none')
            } else {
                $('.referral-other').addClass('d-none')
            }
        }

        function checkReferralOther() {
            let kurs = $('#curs_rate').val()
            let price = $('#revenue').val()
    
            $('#revenue_idr').val(price * kurs)
            
        }
        

        $(document).ready(function() {

            $("#currency").on('change', function() {

                var current_rate = $("#curs_rate").val()

                {{--  checkCurrencyDetail()  --}}
                

                    showLoading()
                    var base_currency = $(this).val();
                    var to_currency = 'IDR';
    
                    var link = "{{ url('/') }}/api/current/rate/"+base_currency+"/"+to_currency
    
                    axios.get(link)
                        .then(function (response) {
    
                            var rate = response.data.rate;
                            $("#curs_rate").val(rate)
                            swal.close()
    
                        }).catch(function (error) {
    
                            $("#curs_rate").val('')
                            swal.close()
                            notification('error', 'Something went wrong. Please try again');
    
                        })
                

            })

            

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

    @if (isset($referral->currency) && $referral->currency != 'IDR')
        <script>
            $(document).ready(function() {
                $('#currency').val('{{ $referral->currency }}').trigger('change')
            })
        </script>
    @endif

    @if (!empty(old('currency')))
        <script>
            $(document).ready(function() {
                $('#currency').val("{{ old('currency') }}").trigger('change')
            })
        </script>
    @endif

    @if (!empty(old('referral_type')))
        <script>
            $(document).ready(function() {
                $('#type').val("{{ old('referral_type') }}").trigger('change')
            })
        </script>
    @endif
@endsection
