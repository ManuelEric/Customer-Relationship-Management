@extends('layout.main')

@section('title', 'Invoice Bigdata Platform')

@section('content')

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
                    <h4>Partner Name</h4>
                    <h6>Program Name</h6>
                    <div class="d-flex justify-content-center mt-3">
                        <a href="{{ url('program/client/1') }}" class="btn btn-sm btn-outline-info rounded mx-1"
                            target="_blank">
                            <i class="bi bi-eye me-1"></i> More
                        </a>

                        <a href="{{ $status == 'edit' ? url('invoice/corporate-program/1') : url('invoice/corporate-program/1/edit') }}"
                            class="btn btn-sm btn-outline-warning rounded mx-1">
                            <i class="bi {{ $status == 'edit' ? 'bi-arrow-left' : 'bi-pencil' }}  me-1"></i>
                            {{ $status == 'edit' ? 'Back' : 'Edit' }}
                        </a>

                        <button class="btn btn-sm btn-outline-danger rounded mx-1">
                            <i class="bi bi-trash2 me-1"></i> Delete
                        </button>
                    </div>
                </div>
            </div>

            @include('pages.invoice.corporate-program.form-detail.client')

            @include('pages.invoice.corporate-program.form-detail.installment-list')
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
                        <button class="btn btn-sm btn-outline-primary py-1">
                            <i class="bi bi-plus"></i> Receipt
                        </button>
                        <button class="btn btn-sm btn-outline-warning py-1">
                            <i class="bi bi-eye"></i> View Receipt
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="">Currency</label>
                            <select id="currency" class="select w-100" onchange="checkCurrency()">
                                <option value="idr">IDR</option>
                                <option value="other">Other Currency</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3 currency-detail d-none">
                            <label for="">Currency Detail</label>
                            <select class="select w-100" id="currency_detail" onchange="checkCurrencyDetail()">
                                <option data-placeholder="true"></option>
                                <option value="usd">USD</option>
                                <option value="sgd">SGD</option>
                            </select>
                        </div>

                        <div class="col-md-3  mb-3 currency-detail d-none">
                            <label for="">Current Rate to IDR</label>
                            <input type="number" name="" id="current_rate"
                                class="form-control form-control-sm rounded" disabled>
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
                            <input type="hidden" name="" id="total_idr">
                            <input type="hidden" name="" id="total_other">
                        </div>

                        <div class="col-md-5 mb-3">
                            <label for="">Payment Method</label>
                            <select name="" id="payment_method" class="select w-100" onchange="checkPayment()">
                                <option data-placeholder="true"></option>
                                <option value="full">Full Payment</option>
                                <option value="installment">Installment</option>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="">Invoice Date</label>
                                    <input type="date" name="" id=""
                                        class='form-control form-control-sm rounded'>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="">Invoice Due Date</label>
                                    <input type="date" name="" id=""
                                        class='form-control form-control-sm rounded'>
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
                            <textarea name="" id=""></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="">Terms & Condition</label>
                            <textarea name="" id=""></textarea>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
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
                $('.currency-icon').html(detail.toUpperCase())
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
    </script>
@endsection
