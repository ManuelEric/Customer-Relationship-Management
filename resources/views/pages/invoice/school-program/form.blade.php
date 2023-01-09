@extends('layout.main')

@section('title', 'Invoice Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('invoice/school-program/status/needed') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Invoice
        </a>
    </div>


    <div class="row">
        <div class="col-md-4">
            <div class="card rounded mb-3">
                <div class="card-body text-center">
                    <h3><i class="bi bi-person"></i></h3>
                    <h4>{{ $school->sch_name }}</h4>
                    <h6>{{ $schoolProgram->program->prog_program }}</h6>
                    <div class="d-flex justify-content-center mt-3">
                        <a href="{{ url('program/school/'. strtolower($school->sch_id) .'/detail/'.$schoolProgram->id) }}" class="btn btn-sm btn-outline-info rounded mx-1"
                            target="_blank">
                            <i class="bi bi-eye me-1"></i> More
                        </a>
                        @if (isset($invoiceSch))
                            <a href="{{ $status == 'edit' ? url('invoice/school-program/'. $schoolProgram->id .'/detail/'. $invoiceSch->invb2b_num) : url('invoice/school-program/'. $schoolProgram->id .'/detail/'. $invoiceSch->invb2b_num .'/edit') }}"
                                class="btn btn-sm btn-outline-warning rounded mx-1">
                                <i class="bi {{ $status == 'edit' ? 'bi-arrow-left' : 'bi-pencil' }}  me-1"></i>
                                {{ $status == 'edit' ? 'Back' : 'Edit' }}
                            </a>

                            <button class="btn btn-sm btn-outline-danger rounded mx-1"
                                onclick="confirmDelete('{{'invoice/school-program/' . $schoolProgram->id . '/detail'}}', {{$invoiceSch->invb2b_num}})">
                                <i class="bi bi-trash2 me-1"></i> Delete
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            @include('pages.invoice.school-program.form-detail.client')
            
            @if(isset($invoiceSch) && $invoiceSch->invb2b_pm == 'installment')
                @include('pages.invoice.school-program.form-detail.installment-list')
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
                    @if(isset($invoiceSch) && $invoiceSch->invb2b_pm == 'full')
                        <div class="">
                            <button class="btn btn-sm btn-outline-primary py-1" onclick="checkReceipt()">
                                <i class="bi bi-plus"></i> Receipt
                            </button>
                            <button class="btn btn-sm btn-outline-warning py-1">
                                <i class="bi bi-eye"></i> View Receipt
                            </button>
                        </div>
                    @endif
                </div>

                <div class="card-body">
                    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

                    <form action="{{ url($status == 'edit' ? 'invoice/school-program/' . $schoolProgram->id . '/detail/' . $invoiceSch->invb2b_num : 'invoice/school-program/' . $schoolProgram->id . '/detail') }}" method="POST">
                        @csrf
                        @if($status == 'edit')
                            @method('put')
                        @endif
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="">Currency</label>
                                <select id="currency" name="select_currency" class="select w-100" onchange="checkCurrency()" {{ empty($invoiceSch) || $status == 'edit' ? '' : 'disabled' }}>
                                    <option value="idr">IDR</option>
                                    <option value="other">Other Currency</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3 currency-detail d-none">
                                <label for="">Currency Detail</label>
                                <select class="select w-100" name="currency" id="currency_detail" onchange="checkCurrencyDetail()" {{ empty($invoiceSch) || $status == 'edit' ? '' : 'disabled' }}>
                                    <option data-placeholder="true"></option>
                                    @if(isset($invoiceSch))
                                        <option value="usd" {{ $invoiceSch->currency == 'usd' ? 'selected' : ''}}>USD</option>
                                        <option value="sgd" {{ $invoiceSch->currency == 'sgd' ? 'selected' : ''}}>SGD</option>
                                        <option value="gbp" {{ $invoiceSch->currency == 'gbp' ? 'selected' : ''}}>GBP</option>
                                    @elseif(empty($invoiceSch))
                                        <option value="usd" {{ old('currency') == 'usd' ? 'selected' : ''}}>USD</option>
                                        <option value="sgd" {{ old('currency') == 'sgd' ? 'selected' : ''}}>SGD</option>
                                        <option value="gbp" {{ old('currency') == 'gbp' ? 'selected' : ''}}>GBP</option>
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
                                    value="{{ (isset($invoiceSch)) ? $invoiceSch->curs_rate : old('curs_rate') }}" {{ $status=='edit' ? '' : 'disabled' }}>
                                @error('curs_rate')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>


                            <div class="col-md-12 mb-3">
                                {{-- IDR  --}}
                                <div class="invoice-currency invoice-idr">
                                    @include('pages.invoice.school-program.form-detail.invoice-idr')
                                </div>

                                {{-- OTHER  --}}
                                <div class="invoice-currency d-none  invoice-other">
                                    @include('pages.invoice.school-program.form-detail.invoice-other')
                                </div>
                            </div>

                            <div class="col-md-12">
                                <input type="hidden" name="" id="total_idr">
                                <input type="hidden" name="" id="total_other">
                                    
                            </div>

                            <div class="col-md-5 mb-3">
                                <label for="">Payment Method</label>
                                <select name="invb2b_pm" id="payment_method" class="select w-100" {{ empty($invoiceSch) || $status == 'edit' ? '' : 'disabled' }} onchange="checkPayment()">
                                    <option data-placeholder="true"></option>
                                        <option value="full">Full Payment</option>
                                        <option value="installment">Installment</option>
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
                                            value="{{ (isset($invoiceSch)) ? $invoiceSch->invb2b_date : old('invb2b_date') }}"
                                            {{ empty($invoiceSch) || $status == 'edit' ? '' : 'disabled' }}>
                                        @error('invb2b_date')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="">Invoice Due Date</label>
                                        <input type="date" name="invb2b_duedate" id=""
                                            class='form-control form-control-sm rounded'
                                            value="{{ (isset($invoiceSch)) ? $invoiceSch->invb2b_duedate : old('invb2b_duedate') }}"
                                            {{ empty($invoiceSch) || $status == 'edit' ? '' : 'disabled' }}>
                                        @error('invb2b_duedate')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                                <div class="col-md-12">
                                    {{-- IDR  --}}
                                    <div class="installment-card d-none installment-idr">
                                        @include('pages.invoice.school-program.form-detail.installment-idr')
                                    </div>
                                    
                                    <div class="installment-card d-none installment-other">
                                        @include('pages.invoice.school-program.form-detail.installment-other')
                                    </div>
                                    
                                </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="">Notes</label>
                                <textarea name="invb2b_notes" id="">{{ (isset($invoiceSch)) ? $invoiceSch->invb2b_notes : old('invb2b_notes') }}</textarea>
                                @error('invb2b_notes')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="">Terms & Condition</label>
                                <textarea name="invb2b_tnc" id="">{{ (isset($invoiceSch)) ? $invoiceSch->invb2b_tnc : old('invb2b_tnc') }}</textarea>
                                @error('invb2b_tnc')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            @if(empty($invoiceSch) || $status == 'edit')
                                <div class="mt-3 text-end">
                                    <button type="submit" class="btn btn-sm btn-primary rounded">
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
                    <form action="#" method="POST" id="receipt">
                        @csrf
                        <div class="put"></div>
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
                                        <input type="text" name="receipt" id="receipt_amount_other"
                                            class="form-control" required value="">
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
                                        <input type="text" name="receipt" id="receipt_amount"
                                            class="form-control" required value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-1">
                                    <label for="">
                                        Date <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="date" name="receipt" id="receipt_date"
                                        class="form-control form-control-sm rounded" required value="">
                                </div>
                            </div>
                            <div class="col-md-12 receipt-other d-none">
                                <div class="mb-1">
                                    <label for="">
                                        Word <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="receipt" id="receipt_word_other"
                                        class="form-control form-control-sm rounded" required value="" readonly>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-1">
                                    <label for="">
                                        Word <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="receipt" id="receipt_word"
                                        class="form-control form-control-sm rounded" required value="" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="">
                                        Payment Method <sup class="text-danger">*</sup>
                                    </label>
                                    <select name="" class="modal-select w-100" id="receipt_payment"
                                        onchange="checkPaymentReceipt()">
                                        <option value="Wire Transfer">Wire Transfer</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Cheque">Cheque</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="">
                                        Cheque No <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="receipt" id="receipt_cheque"
                                        class="form-control form-control-sm rounded" required value="" disabled>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="#" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                                <i class="bi bi-x-square me-1"></i>
                                Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm">
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
                dropdownParent: $('#addReceipt .modal-content'),
                placeholder: "Select value",
                allowClear: true
            });
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

        function checkPayment() {
            let cur = $('#currency').val()
            let method = $('#payment_method').val()

            $('.installment-card').addClass('d-none')
            if (method == 'installment') {
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

    @if(isset($invoiceSch->currency))
        <script>
            $(document).ready(function(){
                $('#currency').val('other').trigger('change')
            })
        </script>
    @endif

    @if(isset($invoiceSch->invb2b_pm))
        <script>
            $(document).ready(function(){
                $('#payment_method').val('{{$invoiceSch->invb2b_pm}}').trigger('change')
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
@endsection
