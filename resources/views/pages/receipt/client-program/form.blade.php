@extends('layout.main')

@section('title', 'Receipt Bigdata Platform')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('receipt/client-program?s=list') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Receipt
        </a>
    </div>


    <div class="row">
        <div class="col-md-4">
            <div class="card rounded mb-3">
                <div class="card-body text-center">
                    <h3><i class="bi bi-person"></i></h3>
                    <h4>{{ $client_prog->client->full_name }}</h4>
                    <h6 class="d-flex flex-column">
                        @php
                            $programName = explode('-', $client_prog->program_name);
                        @endphp
                        @for ($i = 0; $i < count($programName); $i++)
                            <span
                                @if ($i > 0) style="font-size:.8em;color:blue" @endif>{{ $programName[$i] }}</span>
                        @endfor
                    </h6>
                    <div class="d-flex flex-wrap justify-content-center mt-3">
                        <button class="btn btn-sm btn-outline-danger rounded mx-1 my-1"
                            onclick="confirmDelete('receipt/client-program/', '{{ $receipt->id }}')">
                            <i class="bi bi-trash2 me-1"></i> Delete
                        </button>
                        <a href="#export-idr" id="print"
                            class="btn btn-sm btn-outline-info rounded mx-1 my-1">
                            <i class="bi bi-printer me-1"></i> Print
                        </a>
                        @if (isset($client_prog->invoice->currency) && $client_prog->invoice->currency != "idr")
                            <a href="#export-idr" id="print-other"
                                class="btn btn-sm btn-outline-info rounded mx-1 my-1">
                                <i class="bi bi-printer me-1"></i> Print Foreign
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            @include('pages.receipt.client-program.form-detail.refund')

            @include('pages.receipt.client-program.form-detail.client')

        </div>

        <div class="col-md-8">
            <div class="card rounded mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-person me-2"></i>
                            Receipt Detail
                        </h6>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-hover">
                        <tr>
                            <td width="20%">Receipt ID :</td>
                            <td>{{ $receipt->receipt_id }}</td>
                        </tr>
                        <tr>
                            <td>Receipt Date :</td>
                            <td>{{ date('d M Y H:i:s', strtotime($receipt->created_at)) }}</td>
                        </tr>
                        @if (isset($receipt->invoiceInstallment))
                            <tr>
                                <td>Installment Name :</td>
                                <td>{{ $receipt->invoiceInstallment->invdtl_installment }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td>Payment Method :</td>
                            <td>{{ $receipt->receipt_method }}</td>
                        </tr>
                        <tr>
                            <td>Amount :</td>
                            <td>
                                @if ($receipt->receipt_amount != null)
                                    {{ $receipt->receipt_amount }}
                                    ( {{ $receipt->receipt_amount_idr }} )
                                @else
                                    {{ $receipt->receipt_amount_idr }}
                                @endif
                                {{-- $20 (Rp. 300.000) --}}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            @include('pages.receipt.client-program.form-detail.invoice')
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.modal-select').select2({
                dropdownParent: $('#addReceipt .modal-content'),
                placeholder: "Select value",
                allowClear: true
            });

            $("#print").on('click', function(e) {
                e.preventDefault();

                Swal.showLoading()                
                axios
                    .get('{{ route('receipt.client-program.export', ['receipt' => $receipt->id]) }}', {
                        responseType: 'arraybuffer',
                        params: {
                            type: 'idr'
                        }
                    })
                    .then(response => {

                        let blob = new Blob([response.data], { type: 'application/pdf' }),
                            url = window.URL.createObjectURL(blob)

                        window.open(url) // Mostly the same, I was just experimenting with different approaches, tried link.click, iframe and other solutions
                        swal.close()
                        notification('success', 'Receipt in Rupiah has been exported')
                    })
                    .catch(error => {
                        notification('error', 'Something went wrong while exporting the receipt')
                        swal.close()
                    })
            })

            $("#print-other").on('click', function(e) {
                e.preventDefault();

                Swal.showLoading()                
                axios
                    .get('{{ route('receipt.client-program.export', ['receipt' => $receipt->id]) }}', {
                        responseType: 'arraybuffer',
                        params: {
                            type: 'other'
                        }
                    })
                    .then(response => {

                        let blob = new Blob([response.data], { type: 'application/pdf' }),
                            url = window.URL.createObjectURL(blob)

                        window.open(url) // Mostly the same, I was just experimenting with different approaches, tried link.click, iframe and other solutions
                        swal.close()
                        notification('success', 'Receipt in Rupiah has been exported')
                    })
                    .catch(error => {
                        notification('error', 'Something went wrong while exporting the receipt')
                        swal.close()
                    })
            })
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
                var link = "{{ url('/') }}/receipt/client-program/" + $(this).data('recid')
                window.location = link
            })
        })
    </script>
@endsection
