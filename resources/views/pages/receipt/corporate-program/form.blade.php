@extends('layout.main')

@section('title', 'Receipt Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('receipt/corporate-program') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Receipt
        </a>
    </div>


    <div class="row">
        <div class="col-md-4 mb-3">
            {{-- Tools  --}}
            <div class="bg-white rounded p-2 mb-2 d-flex gap-2 shadow-sm justify-content-start">
                <div class="d-flex align-items-stretch">
                    <div class="bg-secondary px-3 text-white" style="padding-top:10px ">General</div>
                    <div class="border p-1 text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip" data-bs-title="Delete"
                                onclick="confirmDelete('receipt/client-program/', '{{ $receipt->id }}')">
                                <a href="#" class="text-danger">
                                    <i class="bi bi-trash2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- IDR  --}}
                <div class="d-flex align-items-stretch">
                    <div class="bg-secondary px-3 text-white" style="padding-top:10px ">IDR</div>
                    <div class="border p-1 text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            @if (!$receipt->receiptAttachment()->where('currency', 'idr')->first())
                                @if ($receipt->invoiceProgram->invoiceAttachment()->where('currency', 'idr')->first())
                                    <div id="print" class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                        data-bs-title="Download">
                                        <a href="#" class="text-info">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </div>
                                    <div id="upload-idr" data-bs-target="#uploadReceipt" data-bs-toggle="modal"
                                        class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                        data-bs-title="Upload">
                                        <a href="#" class="text-info">
                                            <i class="bi bi-upload"></i>
                                        </a>
                                    </div>
                                @endif
                            @elseif ($receipt->receiptAttachment()->where('currency', 'idr')->where('sign_status', 'not yet')->first())
                                <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                    data-bs-title="Request Sign" id="request-acc">
                                    <a href="" class="text-info">
                                        <i class="bi bi-pen-fill"></i>
                                    </a>
                                </div>
                            @else
                                <div id="print" class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                    data-bs-title="Print Invoice">
                                    <a href="#" class="text-info">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>
                                <div id="send-rec-client-idr" class="btn btn-sm py-1 border btn-light"
                                    data-bs-toggle="tooltip" data-bs-title="Send to Client" id="send-inv-client-idr">
                                    <a href="#" class="text-info">
                                        <i class="bi bi-send"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Other  --}}
                @if ($receipt->invoiceProgram->currency != 'idr')
                    <div class="d-flex align-items-stretch">
                        <div class="bg-secondary px-3 text-white" style="padding-top:10px ">Other Currency</div>
                        <div class="border p-1 text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                @if (!$receipt->receiptAttachment()->where('currency', 'other')->first())
                                    @if ($receipt->invoiceProgram->invoiceAttachment()->where('currency', 'other')->first())
                                        <div id="print-other" class="btn btn-sm py-1 border btn-light"
                                            data-bs-toggle="tooltip" data-bs-title="Download">
                                            <a href="" class="text-info">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </div>
                                        <div id="upload-other" data-bs-target="#uploadReceipt" data-bs-toggle="modal"
                                            class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                            data-bs-title="Upload">
                                            <a href="" class="text-info">
                                                <i class="bi bi-upload"></i>
                                            </a>
                                        </div>
                                    @endif
                                @elseif ($receipt->receiptAttachment()->where('currency', 'other')->where('sign_status', 'not yet')->first())
                                    <div id="request-acc-other" class="btn btn-sm py-1 border btn-light"
                                        data-bs-toggle="tooltip" data-bs-title="Request Sign" id="request-acc-other">
                                        <a href="" class="text-info">
                                            <i class="bi bi-pen-fill"></i>
                                        </a>
                                    </div>
                                @else
                                    <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                        data-bs-title="Print Invoice">
                                        <a href="{{ route('receipt.client-program.print', ['receipt' => $receipt->id, 'currency' => 'other']) }}"
                                            class="text-info">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                    <div id="send-rec-client-other" class="btn btn-sm py-1 border btn-light"
                                        data-bs-toggle="tooltip" data-bs-title="Send to Client">
                                        <a href="#" class="text-info">
                                            <i class="bi bi-send"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            
            <div class="card rounded mb-3">
                <div class="card-body text-center">
                    <h3><i class="bi bi-person"></i></h3>
                    <h4>{{ $receiptPartner->invoiceB2b->partner_prog->corp->corp_name }}</h4>
                    <h6>{{ $receiptPartner->invoiceB2b->partner_prog->program->sub_prog ? $receiptPartner->invoiceB2b->partner_prog->program->sub_prog->sub_prog_name . ' - ' : '' }}
                        {{ $receiptPartner->invoiceB2b->partner_prog->program->prog_program }}</h6>
                </div>
            </div>

            {{-- @include('pages.receipt.corporate-program.form-detail.refund') --}}
            @include('pages.receipt.corporate-program.form-detail.client')

        </div>

        <div class="col-md-8">
            <div class="card rounded mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-person me-2"></i>
                            Receipt
                        </h6>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-hover">
                        <tr>
                            <td width="20%">Receipt ID :</td>
                            <td>{{ $receiptPartner->receipt_id }}</td>
                        </tr>
                        <tr>
                            <td>Receipt Date :</td>
                            <td>{{ date('d M Y H:i:s', strtotime($receiptPartner->created_at)) }}</td>
                        </tr>
                        @if (isset($receiptPartner->invdtl_id))
                            <tr>
                                <td>Installment Name :</td>
                                <td>{{ $receiptPartner->invoiceInstallment->invdtl_installment }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td>Payment Method :</td>
                            <td>{{ $receiptPartner->receipt_method }}</td>
                        </tr>
                        @if ($receiptPartner->receipt_method == 'Cheque')
                            <tr>
                                <td>Cheque No : </td>
                                <td>{{ $receiptPartner->receipt_cheque }}</td>
                            </tr>
                        @endif
                        @if ($receiptPartner->invoiceB2b->currency != 'idr')
                            <tr>
                                <td>Curs Rate :</td>
                                <td>{{ $receiptPartner->invoiceB2b->rate }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td>Amount :</td>
                            <td>
                                @if ($receiptPartner->receipt_amount != null && $receiptPartner->invoiceB2b->currency != 'idr')
                                    {{ $receiptPartner->receipt_amount }}
                                    ( {{ $receiptPartner->receipt_amount_idr }} )
                                @else
                                    {{ $receiptPartner->receipt_amount_idr }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            @include('pages.receipt.corporate-program.form-detail.invoice')

            {{-- Receipt Progress  --}}
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h6 class="my-0">
                        Receipt Progress
                    </h6>
                </div>
                <div class="card-body position-relative h-auto pb-5">
                    {{-- IDR  --}}
                    <div class="text-center">
                        <h6>IDR</h6>
                        <section class="step-indicator">
                            <div class="step step1 active">
                                <div class="step-icon">1</div>
                                <p>Download</p>
                            </div>
                            <div class="indicator-line active"></div>
                            <div class="step step2">
                                <div class="step-icon">2</div>
                                <p>Upload</p>
                            </div>
                            <div class="indicator-line"></div>
                            <div class="step step3">
                                <div class="step-icon">3</div>
                                <p>Request Sign</p>
                            </div>
                            <div class="indicator-line"></div>
                            <div class="step step4">
                                <div class="step-icon">4</div>
                                <p>Signed</p>
                            </div>
                            <div class="indicator-line"></div>
                            <div class="step step5">
                                <div class="step-icon">5</div>
                                <p>Print or Send to Client</p>
                            </div>
                        </section>
                    </div>

                    {{-- Other  --}}
                    <div class="text-center mt-5">
                        <hr>
                        <h6>Other Currency</h6>
                        <section class="step-indicator">
                            <div class="step step1 active">
                                <div class="step-icon">1</div>
                                <p>Download</p>
                            </div>
                            <div class="indicator-line active"></div>
                            <div class="step step2">
                                <div class="step-icon">2</div>
                                <p>Upload</p>
                            </div>
                            <div class="indicator-line"></div>
                            <div class="step step3">
                                <div class="step-icon">3</div>
                                <p>Request Sign</p>
                            </div>
                            <div class="indicator-line"></div>
                            <div class="step step4">
                                <div class="step-icon">4</div>
                                <p>Signed</p>
                            </div>
                            <div class="indicator-line"></div>
                            <div class="step step5">
                                <div class="step-icon">5</div>
                                <p>Print or Send to Client</p>
                            </div>
                        </section>
                    </div>
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
                                        <input type="text" name="receipt" id="receipt_amount_other" class="form-control"
                                            required value="">
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
                                        <input type="text" name="receipt" id="receipt_amount" class="form-control"
                                            required value="">
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
            let session = $('#session').val()

            if (cur == 'other') {
                $('.currency-detail').removeClass('d-none')
            } else {
                $('.currency-detail').addClass('d-none')
            }

            // check seesion 
            if (session) {
                checkSession()
            }
        }

        function checkCurrencyDetail() {
            let detail = $('#currency_detail').val()
            $('#current_rate').removeAttr('disabled')
            if (detail) {
                $('.currency-icon').html(currencySymbol(detail))
            }
        }

        function checkSession() {
            let session = $('#session').val()
            let cur = $('#currency').val()

            $('.invoice').removeClass('d-none')
            $('.session-detail').addClass('d-none')

            if (session == 'yes') {
                $('.session').removeClass('d-none')
                $('.session-currency').addClass('d-none')
                if (cur == 'idr') {
                    $('.session-idr').removeClass('d-none')
                } else {
                    $('.session-other').removeClass('d-none')
                }
            } else {
                $('.not-session').removeClass('d-none')
                $('.not-session-currency').addClass('d-none')
                if (cur == 'idr') {
                    $('.not-session-idr').removeClass('d-none')
                } else {
                    $('.not-session-other').removeClass('d-none')
                }
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

        $("#installment-list .detail").each(function() {
            $(this).click(function() {
                var link = "{{ url('/') }}/receipt/corporate-program/" + $(this).data('recid')
                window.location = link
            })
        })
    </script>
@endsection
