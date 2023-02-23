@extends('layout.main')

@section('title', 'Receipt Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('receipt/corporate-program') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Receipt
        </a>
    </div>


    <div class="row">
        <div class="col-md-4">
            <div class="card rounded mb-3">
                <div class="card-body text-center">
                    <h3><i class="bi bi-person"></i></h3>
                    <h4>{{ $receiptPartner->invoiceB2b->partner_prog->corp->corp_name }}</h4>
                    <h6>{{ $receiptPartner->invoiceB2b->partner_prog->program->sub_prog ? $receiptPartner->invoiceB2b->partner_prog->program->sub_prog->sub_prog_name.' - ' : '' }} {{$receiptPartner->invoiceB2b->partner_prog->program->prog_program}}</h6>
                    <div class="d-flex flex-wrap justify-content-center mt-3">
                        <button class="btn btn-sm btn-outline-danger rounded mx-1 my-1"
                            onclick="confirmDelete('{{'receipt/corporate-program'}}', {{$receiptPartner->id}})">
                            <i class="bi bi-trash2 me-1"></i> Delete
                        </button>
                        @if (!$receiptPartner->receiptAttachment()->where('currency', 'idr')->first())
                            {!! $exportIdr !!}
                            {!! $uploadIdr !!}
                        @elseif ($receiptPartner->receiptAttachment()->where('currency', 'idr')->where('sign_status', 'not yet')->first())
                            <button class="btn btn-sm btn-outline-warning rounded mx-1 my-1" id="request-acc">
                                <i class="bi bi-pen me-1"></i> Request Sign IDR
                            </button>
                        @else
                            <a href="{{ route('receipt.corporate.print', ['receipt' => $receiptPartner->id, 'currency' => 'idr']) }}" 
                                class="btn btn-sm btn-outline-info rounded mx-1 my-1" target="blank">
                                <i class="bi bi-printer me-1"></i> Print IDR
                            </a>
                            <button class="btn btn-sm btn-outline-info rounded mx-1 my-1" id="send-inv-client-idr">
                                <i class="bi bi-printer me-1"></i> Send Receipt IDR to Client
                            </button>
                        @endif

                        @if (!$receiptPartner->receiptAttachment()->where('currency', 'other')->where('sign_status', 'signed')->first())
                            {!! $exportOther !!}
                            {!! $uploadOther !!}
                        @elseif ($receiptPartner->receiptAttachment()->where('currency', 'other')->where('sign_status', 'not yet')->first())
                            <button class="btn btn-sm btn-outline-warning rounded mx-1 my-1" id="request-acc-other">
                                <i class="bi bi-pen me-1"></i> Request Sign Other
                            </button>
                        @else
                            <a href="{{ route('receipt.corporate.print', ['receipt' => $receiptPartner->id, 'currency' => 'other']) }}" 
                                class="btn btn-sm btn-outline-info rounded mx-1 my-1" target="blank">
                                <i class="bi bi-printer me-1"></i> Print Other
                            </a>
                            <button class="btn btn-sm btn-outline-info rounded mx-1 my-1" id="send-inv-client-other">
                                <i class="bi bi-printer me-1"></i> Send Receipt Other to Client
                            </button>
                        @endif
                    </div>
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
                        @if(isset($receiptPartner->invdtl_id))
                            <tr>
                                <td>Installment Name :</td>
                                <td>{{ $receiptPartner->invoiceInstallment->invdtl_installment }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td>Payment Method :</td>
                            <td>{{ $receiptPartner->receipt_method }}</td>
                        </tr>
                         @if($receiptPartner->receipt_method == 'Cheque')
                            <tr>
                                <td>Cheque No : </td>
                                <td>{{ $receiptPartner->receipt_cheque }}</td>
                            </tr>
                        @endif
                        @if ($receiptPartner->invoiceB2b->currency != "idr")
                            <tr>
                                <td>Curs Rate :</td>
                                <td>{{ $receiptPartner->invoiceB2b->rate }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td>Amount :</td>
                            <td>
                                 @if ($receiptPartner->receipt_amount != null && $receiptPartner->invoiceB2b->currency != "idr")
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

            $("#send-inv-client-idr").on('click', function(e) {
                e.preventDefault()
                Swal.showLoading()
                axios
                    .get('{{ route('receipt.corporate.send_to_client', ['receipt' => $receiptPartner->id, 'currency' => 'idr']) }}')
                    .then(response => {
                        swal.close()
                        notification('success', 'Receipt has been send to client')
                    })
                    .catch(error => {
                        notification('error', 'Something went wrong when sending receipt to client. Please try again');
                        swal.close()
                    })
            })

            $("#send-inv-client-other").on('click', function(e) {
                e.preventDefault()
                Swal.showLoading()
                axios
                    .get('{{ route('receipt.corporate.send_to_client', ['receipt' => $receiptPartner->id, 'currency' => 'other']) }}')
                    .then(response => {
                        swal.close()
                        notification('success', 'Receipt has been send to client')
                    })
                    .catch(error => {
                        notification('error', 'Something went wrong when sending receipt to client. Please try again');
                        swal.close()
                    })
            })

            $("#export_other").on('click', function(e) {
                e.preventDefault();

                Swal.showLoading()                
                axios
                    .get('{{ route('receipt.corporate.export', ['receipt' => $receiptPartner->id, 'currency' => 'other']) }}', {
                        responseType: 'arraybuffer'
                    })
                    .then(response => {
                        console.log(response)

                        let blob = new Blob([response.data], { type: 'application/pdf' }),
                            url = window.URL.createObjectURL(blob)
                             // create <a> tag dinamically
                            var fileLink = document.createElement('a');
                            fileLink.href = url;

                            // it forces the name of the downloaded file
                            fileLink.download = '{{ $receiptPartner->receipt_id }}' + '_other';;

                            // triggers the click event
                            fileLink.click();

                        window.open(url) // Mostly the same, I was just experimenting with different approaches, tried link.click, iframe and other solutions
                        swal.close()
                        notification('success', 'Invoice has been exported')
                    })
                    .catch(error => {
                        notification('error', 'Something went wrong while exporting the invoice')
                        swal.close()
                    })
                })

            $("#export_idr").on('click', function(e) {
                e.preventDefault();

                Swal.showLoading()                
                axios
                    .get('{{ route('receipt.corporate.export', ['receipt' => $receiptPartner->id, 'currency' => 'idr']) }}', {
                        responseType: 'arraybuffer'
                    })
                    .then(response => {
                        console.log(response)

                        let blob = new Blob([response.data], { type: 'application/pdf' }),
                            url = window.URL.createObjectURL(blob)
                            // create <a> tag dinamically
                            var fileLink = document.createElement('a');
                            fileLink.href = url;

                            // it forces the name of the downloaded file
                            fileLink.download = '{{ $receiptPartner->receipt_id }}' + '_idr';;

                            // triggers the click event
                            fileLink.click();

                        window.open(url)
                         // Mostly the same, I was just experimenting with different approaches, tried link.click, iframe and other solutions
                        swal.close()
                        notification('success', 'Invoice has been exported')
                    })
                    .catch(error => {
                        notification('error', 'Something went wrong while exporting the invoice')
                        swal.close()
                    })
                })
        });

        $("#upload_idr").on('click', function(e) {
            e.preventDefault();

            $("#currency").val('idr')
        });

        $("#upload_other").on('click', function(e) {
            e.preventDefault();

            $("#currency").val('other')
        });

        $("#request-acc").on('click', function(e) {
            e.preventDefault();

            Swal.showLoading()                
                axios
                    .get('{{ route('receipt.corporate.request_sign', ['receipt' => $receiptPartner->id, 'currency' => 'idr']) }}', {
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
                    .get('{{  route('receipt.corporate.request_sign', ['receipt' => $receiptPartner->id, 'currency' => 'other']) }}', {
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
