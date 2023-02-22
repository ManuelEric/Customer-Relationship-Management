@extends('layout.main')

@section('title', 'Invoice Bigdata Platform')

@section('content')

    @if(isset($invoiceRef) && count($invoiceRef->invoiceAttachment) > 0)
        @foreach ($invoiceRef->invoiceAttachment as $key => $att)
            @php
                $isIdr[$key] = ($att->currency == 'idr');
                $isOther[$key] = ($att->currency == 'other');
                $isSigned[$key] = ($att->sign_status == 'signed');
                $isNotYet[$key] = ($att->sign_status == 'not yet');
            @endphp
        @endforeach
    @endif

    @php
        $requestSignIdr = '<a class="btn btn-sm btn-outline-warning rounded mx-1" id="request-acc">
                                <i class="bi bi-pen me-1"></i> Request Sign IDR
                            </a>';
        $requestSignOther = '<button class="btn btn-sm btn-outline-warning rounded mx-1" id="request-acc-other">
                                <i class="bi bi-pen me-1"></i> Request Sign Other
                            </button>';
    @endphp

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('invoice/referral/status/needed') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Invoice
        </a>
    </div>


    <div class="row">
        <div class="col-md-4">
            <div class="card rounded mb-3">
                <div class="card-body text-center">
                    <h3><i class="bi bi-person"></i></h3>
                    <h4>{{ $partner->corp_name }}</h4>
                    <h6>
                        @if(isset($referral->prog_id))
                            {{ $referral->program->sub_prog ? $referral->program->sub_prog->sub_prog_name.' - ':''}}{{ $referral->program->prog_program }}
                        @else
                            {{ $referral->additional_prog_name }}
                        @endif
                    </h6>
                    <div class="d-flex justify-content-center mt-3">
                        {{-- <a href="{{ url('program/referral/1') }}" class="btn btn-sm btn-outline-info rounded mx-1"
                            target="_blank">
                            <i class="bi bi-eye me-1"></i> More
                        </a> --}}

                        @if(isset($invoiceRef))
                            <a href="{{ $status == 'edit' ? route('invoice-ref.detail.show', ['referral' => $referral->id, 'detail' => $invoiceRef->invb2b_num]) : route('invoice-ref.detail.edit', ['referral' => $referral->id, 'detail' => $invoiceRef->invb2b_num]) }}"
                                class="btn btn-sm btn-outline-warning rounded mx-1">
                                <i class="bi {{ $status == 'edit' ? 'bi-arrow-left' : 'bi-pencil' }}  me-1"></i>
                                {{ $status == 'edit' ? 'Back' : 'Edit' }}
                            </a>

                            <button class="btn btn-sm btn-outline-danger rounded mx-1"
                                onclick="confirmDelete('{{ 'invoice/referral/' . $referral->id . '/detail' }}', {{ $invoiceRef->invb2b_num }})">
                                <i class="bi bi-trash2 me-1"></i> Delete
                            </button>
                        @endif
                    </div>
                    @if (isset($invoiceRef) && count($invoiceRef->invoiceAttachment) > 0)
                        <div class="d-flex justify-content-center mt-2">
                            @if(count($invoiceRef->invoiceAttachment) > 1)
                                @foreach ($invoiceRef->invoiceAttachment as $attachment)
                                    @if($attachment->sign_status == 'signed')
                                        <a href="{{ route('invoice-ref.export', ['invoice' => $invoiceRef->invb2b_num, 'currency' => $attachment->currency]) }}" 
                                            class="btn btn-sm btn-outline-info rounded mx-1 my-1" target="blank">
                                            <i class="bi bi-printer me-1"></i> Print {{ ($attachment->currency == 'idr' ? 'IDR' : 'Others') }}
                                        </a>
                                    @endif
                                @endforeach
                            @else
                                @if($invoiceRef->invoiceAttachment[0]->sign_status == 'signed')
                                    <a href="{{ route('invoice-ref.export', ['invoice' => $invoiceRef->invb2b_num, 'currency' => $invoiceRef->invoiceAttachment[0]->currency]) }}" 
                                        class="btn btn-sm btn-outline-info rounded mx-1 my-1" target="blank">
                                        <i class="bi bi-printer me-1"></i> Print {{$invoiceRef->invoiceAttachment[0]->currency == 'idr' ? 'IDR' : 'Others'}}
                                    </a>
                                @endif            
                            @endif
                        </div>
                    @endif

                    @if (!isset($invoiceRef->refund) && isset($invoiceRef))
                        <div class="d-flex justify-content-center mt-2" style="margin-bottom:10px">
                            @if(count($invoiceRef->invoiceAttachment) > 0)
                                @if(count($invoiceRef->invoiceAttachment) > 1)
                                    @foreach ($invoiceRef->invoiceAttachment as $key => $att)
                                        @if(($isIdr[$key] && $isNotYet[$key]))
                                            {!! $requestSignIdr !!}
                                        @elseif(($isOther[$key] && $isNotYet[$key]) && $invoiceRef->currency != 'idr') 
                                            {!! $requestSignOther !!}
                                        @endif                                        
                                    @endforeach
                                @else
                                    @if(((!$isIdr[0] || !$isOther[0])) && $invoiceRef->currency != 'idr' && $isNotYet[0])
                                        {!! $requestSignIdr !!}
                                        {!! $requestSignOther !!}
                                    @elseif(($isNotYet[0] && $isIdr[0]) || ($isSigned[0] && $isOther[0]))
                                        {!! $requestSignIdr !!}
                                    @elseif(($isNotYet[0] && $isOther[0]) || ($isSigned[0] && $isIdr[0]) && $invoiceRef->currency != 'idr')
                                        {!! $requestSignOther !!}
                                    @endif
                                @endif
                            @else
                                @if($invoiceRef->currency == 'idr')
                                    {!! $requestSignIdr !!}
                                @else
                                    {!! $requestSignIdr !!}
                                    {!! $requestSignOther !!}
                                @endif
                            @endif
                        </div>
                    @endif
                    @if (isset($invoiceRef) && count($invoiceRef->invoiceAttachment) > 0)
                        <div class="d-flex justify-content-center">
                            @if(count($invoiceRef->invoiceAttachment) > 1)
                                @foreach ($invoiceRef->invoiceAttachment as $attachment)
                                    @if($attachment->sign_status == 'signed')
                                        <button class="btn btn-sm btn-outline-info rounded mx-1" id="send-inv-client-{{ ($attachment->currency == 'idr' ? 'idr' : 'other') }}">
                                            <i class="bi bi-printer me-1"></i> Send Invoice {{ ($attachment->currency == 'idr' ? 'IDR' : 'Others') }} to Client
                                        </button>
                                    @endif
                                @endforeach
                            @else
                                @if($invoiceRef->invoiceAttachment[0]->sign_status == 'signed')
                                    <button class="btn btn-sm btn-outline-info rounded mx-1" id="send-inv-client-{{ ($invoiceRef->invoiceAttachment[0]->currency == 'idr' ? 'idr' : 'other') }}">
                                        <i class="bi bi-printer me-1"></i> Send Invoice {{ ($invoiceRef->invoiceAttachment[0]->currency == 'idr' ? 'IDR' : 'Others') }} to Client
                                    </button>
                                @endif            
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            @include('pages.invoice.referral.form-detail.client')
        </div>

        <div class="col-md-8">
            <div class="card rounded mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-person me-2"></i>
                            Invoice
                        </h6>
                    </div>
                    <div class="">
                        @if(isset($invoiceRef) && !isset($invoiceRef->receipt) && $invoiceRef->invb2b_pm == 'Full Payment' && $status != 'edit')
                            <button class="btn btn-sm btn-outline-primary py-1" onclick="checkReceipt()">
                                <i class="bi bi-plus"></i> Receipt
                            </button>
                        @endif
                        @if(isset($invoiceRef->receipt)  && $status != 'edit' && $invoiceRef->invb2b_pm == 'Full Payment')
                            <a href="{{ route('receipt.referral.show', ['detail' => $invoiceRef->receipt->id ]) }}" class="btn btn-sm btn-outline-warning py-1">
                                <i class="bi bi-eye"></i> View Receipt
                            </a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ $status == 'edit' ? route('invoice-ref.detail.update', ['referral' => $referral->id, 'detail' => $invoiceRef->invb2b_num]) : route('invoice-ref.detail.store', ['referral' => $referral->id]) }}" method="POST">
                        @csrf
                        @if ($status == 'edit')
                            @method('put')
                        @endif
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="">Currency</label>
                                <select id="currency" name="select_currency" class="select w-100" onchange="checkCurrency()"
                                    {{ empty($invoiceRef) || $status == 'edit' ? '' : 'disabled' }}>
                                    <option value="idr">IDR</option>
                                    <option value="other">Other Currency</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3 currency-detail d-none">
                                <label for="">Currency Detail</label>
                                <select class="select w-100" name="currency" id="currency_detail" onchange="checkCurrencyDetail()"
                                    {{ empty($invoiceRef) || $status == 'edit' ? '' : 'disabled' }}>
                                    <option data-placeholder="true"></option>
                                    <@if (isset($invoiceRef))
                                        <option value="usd" {{ $invoiceRef->currency == 'usd' ? 'selected' : '' }}>USD
                                        </option>
                                        <option value="sgd" {{ $invoiceRef->currency == 'sgd' ? 'selected' : '' }}>SGD
                                        </option>
                                        <option value="gbp" {{ $invoiceRef->currency == 'gbp' ? 'selected' : '' }}>GBP
                                        </option>
                                    @elseif(empty($invoiceRef))
                                        <option value="usd" {{ old('currency') == 'usd' ? 'selected' : '' }}>USD
                                        </option>
                                        <option value="sgd" {{ old('currency') == 'sgd' ? 'selected' : '' }}>SGD
                                        </option>
                                        <option value="gbp" {{ old('currency') == 'gbp' ? 'selected' : '' }}>GBP
                                        </option>
                                    @endif
                                </select>
                                @error('currency')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3  mb-3 currency-detail d-none">
                                <label for="">Current Rate to IDR</label>
                                <input type="number" name="curs_rate" id="current_rate"
                                    class="form-control form-control-sm rounded"
                                    value="{{ isset($invoiceRef) ? $invoiceRef->curs_rate : old('curs_rate') }}"
                                    {{ $status == 'edit' ? '' : 'disabled' }}>
                                @error('curs_rate')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>


                            <div class="col-md-12 mb-3">
                                {{-- IDR  --}}
                                <div class="invoice-currency invoice-idr">
                                    @include('pages.invoice.referral.form-detail.invoice-idr')
                                </div>

                                {{-- OTHER  --}}
                                <div class="invoice-currency d-none  invoice-other">
                                    @include('pages.invoice.referral.form-detail.invoice-other')
                                </div>
                            </div>

                            <div class="col-md-12">
                                <input type="hidden" name="" id="total_idr" value="{{ (isset($invoiceRef)) ? $invoiceRef->invb2b_totpriceidr : null }}">
                                <input type="hidden" name="" id="total_other" value="{{ (isset($invoiceRef)) ? $invoiceRef->invb2b_totprice : null }}">
                            </div>

                            <div class="col-md-5 mb-3">
                                <label for="">Payment Method</label>
                                <select name="invb2b_pm" id="payment_method" class="select w-100"
                                    {{ empty($invoiceRef) || $status == 'edit' ? '' : 'disabled' }}>
                                    <option value="Full Payment">Full Payment</option>
                                </select>
                                @error('invb2b_pm')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-7">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="">Invoice Date</label>
                                        <input type="date" name="invb2b_date" id=""
                                            class='form-control form-control-sm rounded'
                                            value="{{ isset($invoiceRef) ? $invoiceRef->invb2b_date : old('invb2b_date') }}"
                                            {{ empty($invoiceRef) || $status == 'edit' ? '' : 'disabled' }}>
                                        @error('invb2b_date')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="">Invoice Due Date</label>
                                        <input type="date" name="invb2b_duedate" id=""
                                            class='form-control form-control-sm rounded'
                                            value="{{ isset($invoiceRef) ? $invoiceRef->invb2b_duedate : old('invb2b_duedate') }}"
                                            {{ empty($invoiceRef) || $status == 'edit' ? '' : 'disabled' }}>
                                        @error('invb2b_duedate')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="">Notes</label>
                                <textarea name="invb2b_notes" id="">{{ isset($invoiceRef) ? $invoiceRef->invb2b_notes : old('invb2b_notes') }}</textarea>
                                @error('invb2b_notes')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="">Terms & Condition</label>
                                <textarea name="invb2b_tnc" id="">{{ isset($invoiceRef) ? $invoiceRef->invb2b_tnc : old('invb2b_tnc') }}</textarea>
                                @error('invb2b_tnc')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                             @if (empty($invoiceRef) || $status == 'edit')
                                <div class="mt-3 text-end">
                                    <button type="submit" class="btn btn-sm btn-primary rounded" id="submit-form">
                                        <i class="bi bi-save2 me-2"></i> Submit
                                    </button>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Receipt  --}}
    <div class="modal fade" id="addReceiptReferral" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between">
                    <span>
                        Add Receipt
                    </span>
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div class="modal-body w-100">
                    <form action="{{ isset($invoiceRef) ? route('receipt.referral.store', ['invoice' => $invoiceRef->invb2b_num ]) : '' }}" method="POST" id="receipt">
                        @csrf
                        <input type="hidden" name="currency" value="{{ isset($invoiceRef->currency) ? $invoiceRef->currency : null }}">
                        <div class="row g-2">
                            <div class="col-md-3 receipt-other d-none">
                                <div class="mb-1">
                                    <label for="">
                                        Amount <sup class="text-danger">*</sup>
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text currency-icon" id="basic-addon1">
                                            $
                                        </span>
                                        <input type="number" name="receipt_amount" id="receipt_amount_other"
                                            class="form-control" value="">
                                        @error('receipt_amount')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="mb-1">
                                    <label for="">
                                        Amount <sup class="text-danger">*</sup>
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text" id="basic-addon1">
                                            Rp
                                        </span>
                                        <input type="text" name="receipt_amount_idr" id="receipt_amount" class="form-control"
                                         value="">
                                         @error('receipt_amount_idr')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-1">
                                    <label for="">
                                        Date <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="date" name="receipt_date" value="{{ date('Y-m-d') }}" id="receipt_date"
                                        class="form-control form-control-sm rounded" value="">
                                </div>
                            </div>
                            <div class="col-md-12 receipt-other d-none">
                                <div class="mb-1">
                                    <label for="">
                                        Word <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="receipt_words" id="receipt_word_other"
                                        class="form-control form-control-sm rounded" value="" readonly>
                                    @error('receipt_words')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-1">
                                    <label for="">
                                        Word <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="receipt_words_idr" id="receipt_word"
                                        class="form-control form-control-sm rounded" value="" readonly>
                                    @error('receipt_words_idr')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="">
                                        Payment Method <sup class="text-danger">*</sup>
                                    </label>
                                    <select name="receipt_method" class="modal-select w-100" id="receipt_payment"
                                        onchange="checkPaymentReceipt()">
                                        <option value="Wire Transfer">Wire Transfer</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Cheque">Cheque</option>
                                    </select>
                                    @error('receipt_method')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="">
                                        Cheque No <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="receipt_cheque" id="receipt_cheque"
                                        class="form-control form-control-sm rounded" value="" disabled>
                                    @error('receipt_cheque')
                                        <small class="text-danger fw-light">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="#" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                                <i class="bi bi-x-square me-1"></i>
                                Cancel</a>
                            <button type="submit" value="receipt" class="btn btn-primary btn-sm">
                                <i class="bi bi-save2 me-1"></i>
                                Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.modal-select').select2({
                dropdownParent: $('#addReceiptReferral .modal-content'),
                placeholder: "Select value",
                allowClear: true
            });

            $("#receipt_amount_other").on('keyup', function() {
                var val = $(this).val()
                var currency = $("#receipt input[name=currency]").val()
                var curs_rate = $("#current_rate").val();
                switch (currency) {
                    case 'usd':
                        currency = ' Dollar';
                        break;
                    case 'sgd':
                        currency = ' Singapore Dollar';
                        break;
                    case 'gbp':
                        currency = ' Pound';
                        break;
                    default:
                        currency = '';
                        totprice = '-'
                        break;
                }  
                $("#receipt_word_other").val(wordConverter(val) + currency)
                $("#receipt_amount").val(val*curs_rate)
                $("#receipt_word").val(wordConverter(val*curs_rate) + " Rupiah")
            })

            $("#receipt_amount").on('keyup', function() {
                var val = $(this).val()
                $("#receipt_word").val(wordConverter(val) + " Rupiah")
            })
        });
        
        function checkCurrency() {
            let cur = $('#currency').val()
            $('.invoice-currency').addClass('d-none')
            if (cur == 'other') {
                $('.invoice-other').removeClass('d-none')
                $('.currency-detail').removeClass('d-none')
            } else {
                $('.invoice-idr').removeClass('d-none')
                $('.currency-detail').addClass('d-none')
            }
        }

        function checkCurrencyDetail() {
            let detail = $('#currency_detail').val()
            $('#current_rate').removeAttr('disabled')
            if (detail) {
                $('.currency-icon').html(currencySymbol(detail))
            }
        }

        function checkReceipt() {
            let cur = $('#currency').val()
            let detail = $('#currency_detail').val()

            $('#addReceiptReferral').modal('show')
            if (cur == 'other') {
                $('.receipt-other').removeClass('d-none')
                $('.currency-icon').html(currencySymbol(detail))
            } else {
                $('.receipt-other').addClass('d-none')
            }

        }

        function checkPaymentReceipt() {
            let payment = $('#receipt_payment').val()
            if (payment == 'Cheque') {
                $('#receipt_cheque').removeAttr('disabled')
            } else {
                $('#receipt_cheque').attr('disabled', 'disabled')
            }
        }
    </script>

    {{-- receipt --}}

    @if(
        $errors->has('receipt_amount') | 
        $errors->has('receipt_amount_idr') | 
        $errors->has('receipt_words') | 
        $errors->has('receipt_words_idr') |
        $errors->has('receipt_method') |
        $errors->has('receipt_cheque')
        )
                
        <script>
            $(document).ready(function(){
                $('#addReceiptReferral').modal('show'); 
                checkReceipt();
                
              
            })
        </script>

    @endif

    @if(isset($invoiceRef->currency) && $invoiceRef->currency != 'idr') 
        <script>
            $(document).ready(function() {
                $('#currency').val('other').trigger('change')
            })
        </script>
    @else
        <script>
            $(document).ready(function(){
                $('#currency').val('idr').trigger('change')
            })
        </script>
    @endif

    @if (isset($invoiceRef->invb2b_pm))
        <script>
            $(document).ready(function() {
                $('#payment_method').val('{{ $invoiceRef->invb2b_pm }}').trigger('change')
            })
        </script>
    @endif

    @if(!empty(old('invb2b_pm')))
        <script>
            $(document).ready(function(){
                $('#payment_method').val("{{old('invb2b_pm')}}").trigger('change')
            })

        </script>
    @endif

    @if(!empty(old('select_currency')))
        <script>
            $(document).ready(function(){
                $('#currency').val("{{old('select_currency')}}").trigger('change')
            })

        </script>
    @endif

    <script>
        @if (isset($invoiceRef))
            $("#send-inv-client-idr").on('click', function(e) {
                e.preventDefault()
                Swal.showLoading()
                axios
                    .get('{{ route('invoice-ref.send_to_client', ['invoice' => $invoiceRef->invb2b_num, 'currency' => 'idr']) }}')
                    .then(response => {
                        swal.close()
                        notification('success', 'Invoice has been send to client')
                    })
                    .catch(error => {
                        notification('error', 'Something went wrong when sending invoice to client. Please try again');
                        swal.close()
                    })
            })

            $("#send-inv-client-other").on('click', function(e) {
                e.preventDefault()
                Swal.showLoading()
                axios
                    .get('{{ route('invoice-ref.send_to_client', ['invoice' => $invoiceRef->invb2b_num, 'currency' => 'other']) }}')
                    .then(response => {
                        swal.close()
                        notification('success', 'Invoice has been send to client')
                    })
                    .catch(error => {
                        notification('error', 'Something went wrong when sending invoice to client. Please try again');
                        swal.close()
                    })
                })

            $("#request-acc").on('click', function(e) {
                    e.preventDefault();

                    Swal.showLoading()                
                    axios
                        .get('{{  route('invoice-ref.request_sign', ['invoice' => $invoiceRef->invb2b_num, 'currency' => 'idr']) }}', {
                            responseType: 'arraybuffer',
                            params: {
                                type: 'idr'
                            }
                        })
                        .then(response => {
                            swal.close()
                            notification('success', 'Sign has been requested')
                        })
                        .catch(error => {
                            notification('error', 'Something went wrong while send email')
                            swal.close()
                        })
                })

                $("#request-acc-other").on('click', function(e) {
                    e.preventDefault();

                    Swal.showLoading()                
                    axios
                        .get('{{  route('invoice-ref.request_sign', ['invoice' => $invoiceRef->invb2b_num, 'currency' => 'other']) }}', {
                            responseType: 'arraybuffer',
                            params: {
                                type: 'other'
                            }
                        })
                        .then(response => {
                            swal.close()
                            notification('success', 'Sign has been requested')
                        })
                        .catch(error => {
                            notification('error', 'Something went wrong while send email')
                            swal.close()
                        })
                })      
        @endif
    </script>
@endsection
