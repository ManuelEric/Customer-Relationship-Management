@extends('layout.main')

@section('title', 'Invoice Bigdata Platform')

@section('content')

    @php
        $requestSignIdr = '<a class="btn btn-sm btn-outline-warning rounded mx-1" id="request-acc">
                                <i class="bi bi-pen me-1"></i> Request Sign IDR
                            </a>';
        $requestSignOther = '<button class="btn btn-sm btn-outline-warning rounded mx-1" id="request-acc-other">
                                <i class="bi bi-pen me-1"></i> Request Sign Other
                            </button>';
    @endphp

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('invoice/corporate-program/status/needed') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Invoice
        </a>
    </div>


    <div class="row">
        <div class="col-md-4">
            <div class="card rounded mb-3">
                <div class="card-body text-center">
                    <h3><i class="bi bi-person"></i></h3>
                    <h4>{{ $partnerProgram->corp->corp_name }}</h4>
                    <h6>{{ $partnerProgram->program->sub_prog ? $partnerProgram->program->sub_prog->sub_prog_name.' - ':''}}{{ $partnerProgram->program->prog_program }}</h6>
                    <div class="d-flex justify-content-center mt-3">
                        <a href="{{ url('program/corporate/1') }}" class="btn btn-sm btn-outline-info rounded mx-1"
                            target="_blank">
                            <i class="bi bi-eye me-1"></i> More
                        </a>
                        @if (isset($invoicePartner))
                            <a href="{{ $status == 'edit' ? route('invoice-corp.detail.show', ['corp_prog' => $invoicePartner->partnerprog_id, 'detail' => $invoicePartner->invb2b_num]) : route('invoice-corp.detail.edit', ['corp_prog' => $invoicePartner->partnerprog_id, 'detail' => $invoicePartner->invb2b_num]) }}"
                                class="btn btn-sm btn-outline-warning rounded mx-1">
                                <i class="bi {{ $status == 'edit' ? 'bi-arrow-left' : 'bi-pencil' }}  me-1"></i>
                                {{ $status == 'edit' ? 'Back' : 'Edit' }}
                            </a>

                            <button class="btn btn-sm btn-outline-danger rounded mx-1"
                                 onclick="confirmDelete('{{ 'invoice/corporate-program/' . $invoicePartner->partnerprog_id . '/detail' }}', {{ $invoicePartner->invb2b_num }})">
                                <i class="bi bi-trash2 me-1"></i> Delete
                            </button>
                        @endif
                    </div>
                    @if (isset($invoicePartner) && $invoicePartner->partner_prog->status == 1)
                        <div class="d-flex justify-content-center mt-2" style="margin-bottom:10px">
                            @php
                                $invoicePartnerAttachment = $invoicePartner->invoiceAttachment()->where('currency', 'idr')->where('sign_status', 'signed')->first();
                            @endphp
                            @if (!$invoicePartnerAttachment)
                                {!! $requestSignIdr !!}
                            @else
                                <a href="{{ route('invoice-corp.export', ['invoice' => $invoicePartner->invb2b_num, 'currency' => 'idr']) }}" 
                                    class="btn btn-sm btn-outline-info rounded mx-1 my-1" target="blank">
                                    <i class="bi bi-printer me-1"></i> Print IDR
                                </a>
                                <button class="btn btn-sm btn-outline-info rounded mx-1" id="send-inv-client-idr">
                                    <i class="bi bi-printer me-1"></i> Send Invoice IDR to Client
                                </button>
                            @endif
                        </div>
                        <div class="d-flex justify-content-center mt-2" style="margin-bottom:10px">
                             @php
                                $invoicePartnerAttachmentOther = $invoicePartner->invoiceAttachment()->where('currency', 'other')->where('sign_status', 'signed')->first();
                            @endphp
                            @if (!$invoicePartnerAttachmentOther)
                                @if($invoicePartner->currency != 'idr')
                                    {!! $requestSignOther !!}
                                @endif
                            @else
                                <a href="{{ route('invoice-corp.export', ['invoice' => $invoicePartner->invb2b_num, 'currency' => 'other']) }}" 
                                    class="btn btn-sm btn-outline-info rounded mx-1 my-1" target="blank">
                                    <i class="bi bi-printer me-1"></i> Print Other {{ (!$invoicePartnerAttachmentOther ? 'a' : 'b') }}
                                </a>
                                <button class="btn btn-sm btn-outline-info rounded mx-1" id="send-inv-client-other">
                                    <i class="bi bi-printer me-1"></i> Send Invoice Other to Client
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            @if(isset($invoicePartner) && $invoicePartner->partner_prog->status == 3 && isset($invoicePartner->receipt))
                @include('pages.invoice.corporate-program.detail.refund')
            @endif

            @include('pages.invoice.corporate-program.form-detail.client')

            @if(isset($invoicePartner) && $invoicePartner->invb2b_pm == 'Installment')
                @include('pages.invoice.corporate-program.form-detail.installment-list')
            @endif
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
                        @if(isset($invoicePartner) && !isset($invoicePartner->receipt) && $invoicePartner->invb2b_pm == 'Full Payment' && $status != 'edit')
                            <button class="btn btn-sm btn-outline-primary py-1" onclick="checkReceipt()">
                                <i class="bi bi-plus"></i> Receipt
                            </button>
                        @endif
                        @if(isset($invoicePartner->receipt)  && $status != 'edit' && $invoicePartner->invb2b_pm == 'Full Payment')
                            <a href="{{ url('receipt/corporate-program/'.$invoicePartner->receipt->id) }}" class="btn btn-sm btn-outline-warning py-1">
                                <i class="bi bi-eye"></i> View Receipt
                            </a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
 
                    <form action="{{ $status == 'edit' ? route('invoice-corp.detail.update', ['corp_prog' => $invoicePartner->partnerprog_id, 'detail' => $invoicePartner->invb2b_num]) : route('invoice-corp.detail.store', ['corp_prog' => $partnerProgram->id]) }}" method="POST" id="invoice-form">
                        @csrf
                            @if ($status == 'edit')
                                @method('put')
                            @endif
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="">Currency</label>
                                <select id="currency" name="select_currency" class="select w-100" onchange="checkCurrency()"
                                    {{ empty($invoicePartner) || $status == 'edit' ? '' : 'disabled' }}>
                                    <option value="idr">IDR</option>
                                    <option value="other">Other Currency</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3 currency-detail d-none">
                                <label for="">Currency Detail</label>
                                <select class="select w-100" name="currency" id="currency_detail" 
                                    onchange="checkCurrencyDetail()"
                                    {{ empty($invoicePartner) || $status == 'edit' ? '' : 'disabled' }}>>
                                    @if (isset($invoicePartner))
                                        <option value="usd" {{ $invoicePartner->currency == 'usd' ? 'selected' : '' }}>USD
                                        </option>
                                        <option value="sgd" {{ $invoicePartner->currency == 'sgd' ? 'selected' : '' }}>SGD
                                        </option>
                                        <option value="gbp" {{ $invoicePartner->currency == 'gbp' ? 'selected' : '' }}>GBP
                                        </option>
                                    @elseif(empty($invoicePartner))
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
                                    value="{{ isset($invoicePartner) ? $invoicePartner->curs_rate : old('curs_rate') }}"
                                    {{ $status == 'edit' ? '' : 'disabled' }}>
                                @error('curs_rate')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>


                            <div class="col-md-12 mb-3">
                                {{-- IDR  --}}
                                <div class="invoice-currency invoice-idr">
                                    @include('pages.invoice.corporate-program.form-detail.invoice-idr')
                                </div>

                                {{-- OTHER  --}}
                                <div class="invoice-currency d-none  invoice-other">
                                    @include('pages.invoice.corporate-program.form-detail.invoice-other')
                                </div>
                            </div>

                            <div class="col-md-12">
                                <input type="hidden" name="" id="total_idr" value="{{ (isset($invoicePartner)) ? $invoicePartner->invb2b_totpriceidr : null }}">
                                <input type="hidden" name="" id="total_other" value="{{ (isset($invoicePartner)) ? $invoicePartner->invb2b_totpriceidr : null }}">
                            </div>

                            <div class="col-md-5 mb-3">
                                <label for="">Payment Method</label>
                                <select name="invb2b_pm" id="payment_method" class="select w-100" 
                                    {{ empty($invoicePartner) || $status == 'edit' ? '' : 'disabled' }}
                                    onchange="checkPayment()">
                                    <option data-placeholder="true"></option>
                                    <option value="Full Payment">Full Payment</option>
                                    <option value="Installment">Installment</option>
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
                                            value="{{ isset($invoicePartner) ? $invoicePartner->invb2b_date : old('invb2b_date') }}"
                                            {{ empty($invoicePartner) || $status == 'edit' ? '' : 'disabled' }}>
                                        @error('invb2b_date')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="">Invoice Due Date</label>
                                        <input type="date" name="invb2b_duedate" id=""
                                            value="{{ isset($invoicePartner) ? $invoicePartner->invb2b_duedate : old('invb2b_duedate') }}"
                                            {{ empty($invoicePartner) || $status == 'edit' ? '' : 'disabled' }}
                                            class='form-control form-control-sm rounded'>
                                        @error('invb2b_duedate')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                {{-- IDR  --}}
                                <div class="installment-card d-none installment-idr">
                                    @include('pages.invoice.corporate-program.form-detail.installment-idr')
                                </div>

                                <div class="installment-card d-none installment-other">
                                    @include('pages.invoice.corporate-program.form-detail.installment-other')
                                </div>

                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="">Notes</label>
                                <textarea name="invb2b_notes" id="">{{ isset($invoicePartner) ? $invoicePartner->invb2b_notes : old('invb2b_notes') }}</textarea>
                                @error('invb2b_notes')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="">Terms & Condition</label>
                                <textarea name="invb2b_tnc" id="">{{ isset($invoicePartner) ? $invoicePartner->invb2b_tnc : old('invb2b_tnc') }}</textarea>
                                @error('invb2b_tnc')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            @if (empty($invoicePartner) || $status == 'edit')
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
    <div class="modal fade" id="addReceipt" data-bs-backdrop="static" data-bs-keyboard="false"
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
                    <form action="{{ isset($invoicePartner) ? route('receipt.corporate.store', ['invoice' => $invoicePartner->invb2b_num ]) : '' }}" method="POST" id="receipt">
                        @csrf
                        <input type="hidden" name="identifier" id="identifier">
                        <input type="hidden" name="currency" value="{{ isset($invoicePartner->currency) ? $invoicePartner->currency : null }}">
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
                                        <input type="number" name="receipt_amount_idr" id="receipt_amount" class="form-control"
                                            required value="">
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
                                        class="form-control form-control-sm rounded" required value="">
                                </div>
                            </div>
                            <div class="col-md-12 receipt-other d-none">
                                <div class="mb-1">
                                    <label for="">
                                        Word <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="receipt_words" id="receipt_word_other"
                                        class="form-control form-control-sm rounded" required value="" readonly>
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
                                        class="form-control form-control-sm rounded" required value="" readonly>
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
                                        class="form-control form-control-sm rounded" required value="" disabled>
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
        function setIdentifier(id)
        {
            $("#identifier").val(id);
        }
        
        $(document).ready(function() {
            $('.modal-select').select2({
                dropdownParent: $('#addReceipt .modal-content'),
                placeholder: "Select value",
                allowClear: true
            });
        });

        function checkCurrency() {
            let cur = $('#currency').val()
            checkPayment();
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

        function checkPayment() {
            let cur = $('#currency').val()
            let method = $('#payment_method').val()

            $('.installment-card').addClass('d-none')
            if (method == 'Installment') {
                if (cur == 'idr') {
                    $('.installment-idr').removeClass('d-none')
                } else {
                    $('.installment-other').removeClass('d-none')
                }
            }
        }

        function checkReceipt() {
            let cur = $('#currency').val()
            let detail = $('#currency_detail').val()

            $('#addReceipt').modal('show')
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

     @if(isset($invoicePartner->currency) && $invoicePartner->currency != 'idr') 
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

    @if (isset($invoicePartner->invb2b_pm))
        <script>
            $(document).ready(function() {
                $('#payment_method').val('{{ $invoicePartner->invb2b_pm }}').trigger('change')
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

            $("#submit-form").click(function(e) {
            e.preventDefault();

            var currency = $("#currency").val()
            if (currency == "idr") {

                var tot_percent = 0;
                $('.percentage').each(function() {
                    tot_percent += parseInt($(this).val())
                })
    
                if (tot_percent < 100) {
                    notification('error', 'Installment amount is not right. Please double check before create invoice')
                    return;
                }

            } else if (currency == "other") {

                var tot_percent = 0;
                $('.percentage-other').each(function() {
                    tot_percent += parseInt($(this).val())
                })
    
                if (tot_percent < 100) {
                    notification('error', 'Installment amount is not right. Please double check before create invoice')
                    return;
                }

            }


            $("#invoice-form").submit()
        })
        </script>

    <script>

        @if (isset($invoicePartner))
            $("#send-inv-client-idr").on('click', function(e) {
                e.preventDefault()
                Swal.showLoading()
                axios
                    .get('{{ route('invoice-corp.send_to_client', ['invoice' => $invoicePartner->invb2b_num, 'currency' => 'idr']) }}')
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
                    .get('{{ route('invoice-corp.send_to_client', ['invoice' => $invoicePartner->invb2b_num, 'currency' => 'other']) }}')
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
                    .get('{{  route('invoice-corp.request_sign', ['invoice' => $invoicePartner->invb2b_num, 'currency' => 'idr']) }}', {
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
                    .get('{{  route('invoice-corp.request_sign', ['invoice' => $invoicePartner->invb2b_num, 'currency' => 'other']) }}', {
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

        $("#submit-form").click(function(e) {
            e.preventDefault();

            var currency = $("#currency").val()
            if (currency == "idr") {

                var tot_percent = 0;
                $('.percentage').each(function() {
                    tot_percent += parseInt($(this).val())
                })
    
                if (tot_percent < 100) {
                    notification('error', 'Installment amount is not right. Please double check before create invoice')
                    return;
                }

            } else if (currency == "other") {

                var tot_percent = 0;
                $('.percentage-other').each(function() {
                    tot_percent += parseInt($(this).val())
                })
    
                if (tot_percent < 100) {
                    notification('error', 'Installment amount is not right. Please double check before create invoice')
                    return;
                }

            }


            $("#invoice-form").submit()
        })


        // Receipt

        function setIdentifier(id)
        {
            $("#identifier").val(id);
        }

        $(document).ready(function() {
            $('.modal-select').select2({
                dropdownParent: $('#addReceipt .modal-content'),
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
    </script>

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
                $('#addReceipt').modal('show'); 
                checkReceipt();
                
                $("#identifier").val("{{old('identifier')}}");
              
            })
        </script>

    @endif
@endsection
