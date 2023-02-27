@extends('layout.main')

@section('title', 'Receipt Bigdata Platform')

@section('content')

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
        <div class="col-md-4 mb-3">
            {{-- Tools  --}}
            <div class="bg-white rounded p-2 mb-2 d-flex gap-2 shadow-sm justify-content-start">
                <div class="d-flex align-items-stretch">
                    <div class="bg-secondary px-3 text-white" style="padding-top:10px ">General</div>
                    <div class="border p-1 text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip" data-bs-title="Delete"
                                onclick="">
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
                            <div id="print" class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                data-bs-title="Download">
                                <a href="#" class="text-info">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                            <div id="upload-idr" data-bs-target="#uploadReceipt" data-bs-toggle="modal"
                                class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip" data-bs-title="Upload">
                                <a href="#" class="text-info">
                                    <i class="bi bi-upload"></i>
                                </a>
                            </div>
                            <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                data-bs-title="Request Sign" id="request-acc">
                                <a href="" class="text-info">
                                    <i class="bi bi-pen-fill"></i>
                                </a>
                            </div>

                            <div id="print" class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                data-bs-title="Print Invoice">
                                <a href="#" class="text-info">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                            <div id="send-rec-client-idr" class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                data-bs-title="Send to Client" id="send-inv-client-idr">
                                <a href="#" class="text-info">
                                    <i class="bi bi-send"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Other  --}}
                <div class="d-flex align-items-stretch">
                    <div class="bg-secondary px-3 text-white" style="padding-top:10px ">Other Currency</div>
                    <div class="border p-1 text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <div id="print-other" class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                data-bs-title="Download">
                                <a href="" class="text-info">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                            <div id="upload-other" data-bs-target="#uploadReceipt" data-bs-toggle="modal"
                                class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip" data-bs-title="Upload">
                                <a href="" class="text-info">
                                    <i class="bi bi-upload"></i>
                                </a>
                            </div>
                            <div id="request-acc-other" class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                data-bs-title="Request Sign" id="request-acc-other">
                                <a href="" class="text-info">
                                    <i class="bi bi-pen-fill"></i>
                                </a>
                            </div>
                            <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                data-bs-title="Print Invoice">
                                <a href="#" class="text-info">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                            <div id="send-rec-client-other" class="btn btn-sm py-1 border btn-light"
                                data-bs-toggle="tooltip" data-bs-title="Send to Client">
                                <a href="#" class="text-info">
                                    <i class="bi bi-send"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="card rounded mb-3">
                <div class="card-body text-center">
                    <h3><i class="bi bi-person"></i></h3>
                    <h4> {{ $receiptRef->invoiceB2b->referral->partner->corp_name }} </h4>
                    <h6> {{ $receiptRef->invoiceB2b->referral->additional_prog_name }} </h6>
                    <div class="d-flex flex-wrap justify-content-center mt-3">
                        <button class="btn btn-sm btn-outline-danger rounded mx-1 my-1"
                            onclick="confirmDelete('{{'receipt/referral'}}', {{$receiptRef->id}})">
                            <i class="bi bi-trash2 me-1"></i> Delete
                        </button>
                        @if (!$receiptRef->receiptAttachment()->where('currency', 'idr')->first())
                            {!! $exportIdr !!}
                            {!! $uploadIdr !!}
                        @elseif ($receiptRef->receiptAttachment()->where('currency', 'idr')->where('sign_status', 'not yet')->first())
                            <button class="btn btn-sm btn-outline-warning rounded mx-1 my-1" id="request-acc">
                                <i class="bi bi-pen me-1"></i> Request Sign IDR
                            </button>
                        @else
                            <a href="{{ route('receipt.referral.print', ['receipt' => $receiptRef->id, 'currency' => 'idr']) }}" 
                                class="btn btn-sm btn-outline-info rounded mx-1 my-1" target="blank">
                                <i class="bi bi-printer me-1"></i> Print IDR
                            </a>
                            <button class="btn btn-sm btn-outline-info rounded mx-1 my-1" id="send-inv-client-idr">
                                <i class="bi bi-printer me-1"></i> Send Receipt IDR to Client
                            </button>
                        @endif

                        @if ($receiptRef->invoiceB2b->currency != "idr")
                            @if (!$receiptRef->receiptAttachment()->where('currency', 'other')->where('sign_status', 'signed')->first())
                                {!! $exportOther !!}
                                {!! $uploadOther !!}
                            @elseif ($receiptRef->receiptAttachment()->where('currency', 'other')->where('sign_status', 'not yet')->first())
                                <button class="btn btn-sm btn-outline-warning rounded mx-1 my-1" id="request-acc-other">
                                    <i class="bi bi-pen me-1"></i> Request Sign Other
                                </button>
                            @else
                                <a href="{{ route('receipt.referral.print', ['receipt' => $receiptRef->id, 'currency' => 'other']) }}" 
                                    class="btn btn-sm btn-outline-info rounded mx-1 my-1" target="blank">
                                    <i class="bi bi-printer me-1"></i> Print Other
                                </a>
                                <button class="btn btn-sm btn-outline-info rounded mx-1 my-1" id="send-inv-client-other">
                                    <i class="bi bi-printer me-1"></i> Send Receipt Other to Client
                                </button>
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
                             // create <a> tag dinamically
                            var fileLink = document.createElement('a');
                            fileLink.href = url;

                            // it forces the name of the downloaded file
                            fileLink.download = '{{ $receiptRef->receipt_id }}' + '_other';;

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
                    .get('{{ route('receipt.referral.export', ['receipt' => $receiptRef->id, 'currency' => 'idr']) }}', {
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
                            fileLink.download = '{{ $receiptRef->receipt_id }}' + '_idr';;

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
