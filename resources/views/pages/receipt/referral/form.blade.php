@extends('layout.main')

@section('title', 'Receipt Bigdata Platform')

@section('content')

    @if(isset($receiptRef) && count($receiptRef->receiptAttachment) > 0)
        @foreach ($receiptRef->receiptAttachment as $key => $att)
            @php
                $isIdr[$key] = ($att->currency == 'idr');
                $isOther[$key] = ($att->currency == 'other');
                $isSigned[$key] = ($att->sign_status == 'signed');
                $isNotYet[$key] = ($att->sign_status == 'not yet');
            @endphp
        @endforeach
    @endif

    @php
        $exportIdr = '<a href="#export" id="export_idr"
                            class="btn btn-sm btn-outline-info rounded mx-1 my-1">
                            <i class="bi bi-printer me-1"></i> Export IDR
                      </a>';
        $uploadIdr = '<button class="btn btn-sm btn-outline-warning rounded mx-1 my-1"
                            data-bs-toggle="modal" data-bs-target="#uploadReceipt" id="upload_idr">
                            <i class="bi bi-file-arrow-up me-1"></i> Upload IDR
                      </button>';
        $exportOther = '<a href="#export" id="export_other"
                            class="btn btn-sm btn-outline-info rounded mx-1 my-1">
                            <i class="bi bi-printer me-1"></i> Export Other
                        </a>';
        $uploadOther = '<button class="btn btn-sm btn-outline-warning rounded mx-1 my-1"
                            data-bs-toggle="modal" data-bs-target="#uploadReceipt" id="upload_other">
                            <i class="bi bi-file-arrow-up me-1"></i> Upload Other
                        </button>';

    @endphp

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('receipt/referral') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Receipt
        </a>
    </div>


    <div class="row">
        <div class="col-md-4">
            <div class="card rounded mb-3">
                <div class="card-body text-center">
                    <h3><i class="bi bi-person"></i></h3>
                    <h4> {{ $receiptRef->invoiceB2b->referral->partner->corp_name }} </h4>
                    <h6> {{ $receiptRef->invoiceB2b->referral->additional_prog_name }} </h6>
                    <div class="d-flex flex-wrap justify-content-center mt-3">
                        <button class="btn btn-sm btn-outline-danger rounded mx-1 my-1">
                            <i class="bi bi-trash2 me-1"></i> Delete
                        </button>
                        @if (!isset($receiptRef->invoiceB2b->refund) && isset($receiptRef))
                                @if(count($receiptRef->receiptAttachment) > 0)
                                    @if(count($receiptRef->receiptAttachment) > 1)
                                        @foreach ($receiptRef->receiptAttachment as $key => $att)
                                            @if(($isIdr[$key] && $isNotYet[$key]))
                                                {!! $exportIdr !!}
                                                {!! $uploadIdr !!}
                                            @elseif(($isOther[$key] && $isNotYet[$key]) && $receiptRef->invoiceB2b->currency != 'idr') 
                                                {!! $exportOther !!}
                                                {!! $uploadOther !!}
                                            @endif                                        
                                        @endforeach
                                    @else
                                        @if(((!$isIdr[0] || !$isOther[0])) && $receiptRef->invoiceB2b->currency != 'idr' && $isNotYet[0])
                                            {!! $exportIdr !!}
                                            {!! $uploadIdr !!}
                                            {!! $exportOther !!}
                                            {!! $uploadOther !!}
                                        @elseif(($isNotYet[0] && $isIdr[0]) || ($isSigned[0] && $isOther[0]))
                                            {!! $exportIdr !!}
                                            {!! $uploadIdr !!}
                                        @elseif(($isNotYet[0] && $isOther[0]) || ($isSigned[0] && $isIdr[0]) && $receiptRef->invoiceB2b->currency != 'idr')
                                            {!! $exportOther !!}
                                            {!! $uploadOther !!}
                                        @endif
                                    @endif
                                @else
                                    @if($receiptRef->invoiceB2b->currency == 'idr')
                                        {!! $exportIdr !!}
                                        {!! $uploadIdr !!}
                                    @else
                                        {!! $exportIdr !!}
                                        {!! $uploadIdr !!}
                                        {!! $exportOther !!}
                                        {!! $uploadOther !!}
                                    @endif
                                @endif

                            @if(isset($receiptRef) && count($receiptRef->receiptAttachment) > 0)
                                 @if(count($receiptRef->receiptAttachment) > 1)
                                    @foreach ($receiptRef->receiptAttachment as $attachment)
                                        @if($attachment->sign_status == 'signed')
                                            <a href="{{ route('receipt.referral.print', ['receipt' => $receiptRef->id, 'currency' => $attachment->currency]) }}" 
                                                class="btn btn-sm btn-outline-info rounded mx-1 my-1" target="blank">
                                                <i class="bi bi-printer me-1"></i> Print {{ ($attachment->currency == 'idr' ? 'IDR' : 'Others') }}
                                            </a>
                                            <button class="btn btn-sm btn-outline-info rounded mx-1 my-1" id="send-inv-client-{{ ($attachment->currency == 'idr' ? 'idr' : 'other') }}">
                                                <i class="bi bi-printer me-1"></i> Send Receipt {{ ($attachment->currency == 'idr' ? 'IDR' : 'Others') }} to Client
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-outline-warning rounded mx-1 my-1" id="request-acc{{ $attachment->currency == 'other' ? '-other' : ''  }}">
                                                <i class="bi bi-pen me-1"></i> Request Sign {{ $attachment->currency == 'other' ? 'Other' : 'IDR'  }}
                                            </button>
                                        @endif
                                    @endforeach
                                @else
                                    @if($receiptRef->receiptAttachment[0]->sign_status == 'signed')
                                        <a href="{{ route('receipt.referral.print', ['receipt' => $receiptRef->id, 'currency' => $receiptRef->receiptAttachment[0]->currency]) }}" 
                                            class="btn btn-sm btn-outline-info rounded mx-1 my-1" target="blank">
                                            <i class="bi bi-printer me-1"></i> Print {{ ($receiptRef->receiptAttachment[0]->currency == 'idr' ? 'IDR' : 'Others') }}
                                        </a>
                                        <button class="btn btn-sm btn-outline-info rounded mx-1 my-1" id="send-inv-client-{{ $receiptRef->receiptAttachment[0]->currency == 'other' ? 'other' : 'idr'}}">
                                            <i class="bi bi-printer me-1"></i> Send Receipt {{ $receiptRef->receiptAttachment[0]->currency == 'other' ? 'Other' : 'IDR'  }} to Client
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-outline-warning rounded mx-1 my-1" id="request-acc{{ ($receiptRef->receiptAttachment[0]->currency == 'other' ? '-other' : '') }}">
                                            <i class="bi bi-pen me-1"></i> Request Sign {{ $receiptRef->receiptAttachment[0]->currency == 'other' ? 'Other' : 'IDR'  }}
                                        </button>
                                    @endif 
                                @endif
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            @include('pages.receipt.referral.form-detail.client')

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
                            <td>{{ $receiptRef->receipt_id }}</td>
                        </tr>
                        <tr>
                            <td>Receipt Date :</td>
                            <td>{{ date('d M Y H:i:s', strtotime($receiptRef->created_at)) }}</td>
                        </tr>
                        <tr>
                            <td>Payment Method :</td>
                            <td>{{ $receiptRef->receipt_method }}</td>
                        </tr>
                        @if($receiptRef->receipt_method == 'Cheque')
                            <tr>
                                <td>Cheque No : </td>
                                <td>{{ $receiptRef->receipt_cheque }}</td>
                            </tr>
                        @endif
                        @if ($receiptRef->invoiceB2b->currency != "idr")
                            <tr>
                                <td>Curs Rate :</td>
                                <td>{{ $receiptRef->invoiceB2b->rate }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td>Amount :</td>
                            <td>
                                @if ($receiptRef->receipt_amount != null && $receiptRef->invoiceB2b->currency != "idr")
                                    {{ $receiptRef->receipt_amount }}
                                     ( {{ $receiptRef->receipt_amount_idr }} )
                                @else
                                    {{ $receiptRef->receipt_amount_idr }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            @include('pages.receipt.referral.form-detail.invoice')
        </div>
    </div>

    {{-- Upload Receipt  --}}
    <div class="modal fade" id="uploadReceipt" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between">
                    <span>
                        Upload Receipt
                    </span>
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div class="modal-body w-100">
                    <form action="{{ route('receipt.referral.upload', ['receipt' => $receiptRef->id]) }}" method="POST" id="receipt" enctype="multipart/form-data">
                        @csrf
                        <div class="put"></div>
                        <div class="row g-2">
                            <input type="hidden" name="currency" id="currency">
                            <div class="col-md-12">
                                <div class="mb-1">
                                    <label for="">
                                        File <sup class="text-danger">*</sup>
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <input type="file" name="attachment" id="attachment" class="form-control"
                                            required value="">
                                    </div>
                                        @error('attachment')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
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
                    .get('{{ route('receipt.referral.send_to_client', ['receipt' => $receiptRef->id, 'currency' => 'idr']) }}')
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
                    .get('{{ route('receipt.referral.send_to_client', ['receipt' => $receiptRef->id, 'currency' => 'other']) }}')
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
                    .get('{{ route('receipt.referral.export', ['receipt' => $receiptRef->id, 'currency' => 'other']) }}', {
                        responseType: 'arraybuffer'
                    })
                    .then(response => {
                        console.log(response)

                        let blob = new Blob([response.data], { type: 'application/pdf' }),
                            url = window.URL.createObjectURL(blob)

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
                    .get('{{ route('receipt.referral.export', ['receipt' => $receiptRef->id, 'currency' => 'idr']) }}', {
                        responseType: 'arraybuffer'
                    })
                    .then(response => {
                        console.log(response)

                        let blob = new Blob([response.data], { type: 'application/pdf' }),
                            url = window.URL.createObjectURL(blob)

                        window.open(url) // Mostly the same, I was just experimenting with different approaches, tried link.click, iframe and other solutions
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
                    .get('{{ route('receipt.referral.request_sign', ['receipt' => $receiptRef->id, 'currency' => 'idr']) }}', {
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
                    .get('{{  route('receipt.referral.request_sign', ['receipt' => $receiptRef->id, 'currency' => 'other']) }}', {
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
    </script>
@endsection
