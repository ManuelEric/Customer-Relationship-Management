@extends('layout.main')

@section('title', 'Receipt of School Program')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Receipt</a></li>
    <li class="breadcrumb-item active" aria-current="page">View Detail</li>
@endsection
@section('content')

    @include('pages.receipt.pic-modal')

    <div class="row">
        <div class="col-md-4 mb-3">

            <div class="card rounded mb-3">
                <div class="card-body text-center">
                    <h3><i class="bi bi-person"></i></h3>
                    <h4>{{ $invoiceSch->sch_prog->school->sch_name }}</h4>
                    <a href="{{ route('school.detail.show', ['school' => $invoiceSch->sch_prog->school->sch_id, 'detail' => $invoiceSch->sch_prog->id]) }}"
                        class="text-primary text-decoration-none cursor-pointer" target="_blank">
                        <div class="card p-2">
                            <label class="text-muted mb-1">
                                Program Name:
                            </label>
                            <h6 class="text-primary">
                                {{ $invoiceSch->sch_prog->program->program_name }}
                            </h6>
                        </div>
                    </a>
                    <div class="d-flex flex-wrap justify-content-center mt-3">

                    </div>
                </div>
            </div>

            {{-- @include('pages.receipt.school-program.form-detail.refund') --}}
            @include('pages.receipt.school-program.form-detail.client')

        </div>

        <div class="col-md-8">
            {{-- Tools  --}}
            <div class="bg-white rounded p-2 mb-2 d-flex gap-2 shadow-sm justify-content-start">
                <div class="d-flex align-items-stretch">
                    <div class="bg-secondary px-3 text-white" style="padding-top:10px ">General</div>
                    <div class="border p-1 text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip" data-bs-title="Delete"
                                onclick="confirmDelete('{{ 'receipt/school-program' }}', {{ $receiptSch->id }})">
                                <a href="#" class="text-danger">
                                    <i class="bi bi-trash2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                @if (!isset($invoiceSch->refund))
                    {{-- IDR  --}}

                    @php
                        $receiptAttachment = $receiptSch
                            ->receiptAttachment()
                            ->where('currency', 'idr')
                            ->first();
                        $receiptAttachmentRequested = $receiptSch
                            ->receiptAttachment()
                            ->where('currency', 'idr')
                            ->where('request_status', 'requested')
                            ->first();
                        $receiptAttachmentSigned = $receiptSch
                            ->receiptAttachment()
                            ->where('currency', 'idr')
                            ->where('sign_status', 'signed')
                            ->first();
                        $receiptAttachmentNotYet = $receiptSch
                            ->receiptAttachment()
                            ->where('currency', 'idr')
                            ->where('sign_status', 'not yet')
                            ->first();
                        $receiptAttachmentSent = $receiptSch
                            ->receiptAttachment()
                            ->where('currency', 'idr')
                            ->where('send_to_client', 'sent')
                            ->first();
                    @endphp
                    {{-- @if (!$invoiceSch->invoiceAttachment()->where('currency', 'idr')->first()) --}}
                    <div class="d-flex align-items-stretch">
                        <div class="bg-secondary px-3 text-white" style="padding-top:10px ">IDR</div>
                        <div class="border p-1 text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                    data-bs-title="Preview Receipt">
                                    <a href="{{ route('receipt.school.preview_pdf', ['receipt' => $receiptSch->id, 'currency' => 'idr']) }}"
                                        class="text-info" target="blank">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                </div>
                                @if (!$receiptAttachment)
                                    <div id="print" class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                        data-bs-title="Download">
                                        <a href="#" class="text-info" id="export_idr">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </div>
                                    <div id="upload-idr" data-bs-target="#uploadReceipt" data-bs-toggle="modal"
                                        class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                        data-bs-title="Upload">
                                        <a href="#" class="text-info" id="upload_idr">
                                            <i class="bi bi-upload"></i>
                                        </a>
                                    </div>
                                @elseif(isset($receiptAttachmentNotYet))
                                    {{-- <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                        data-bs-title="Request Sign" id="request-acc">
                                        <a href="#" class="text-info">
                                            <i class="bi bi-pen-fill"></i>
                                        </a>
                                    </div> --}}
                                    <div class="btn btn-sm py-1 border btn-light" id="openModalRequestSignIdr" data-curr="idr"
                                        data-bs-toggle="modal" data-bs-target="#requestSignModal">
                                        <a href="#" class="text-info" data-bs-toggle="tooltip" data-bs-title="Request Sign">
                                            <i class="bi bi-pen-fill"></i>
                                        </a>
                                    </div>
                                @else
                                    <div id="print" class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                        data-bs-title="Print Invoice">
                                        <a href="{{ route('receipt.school.print', ['receipt' => $receiptSch->id, 'currency' => 'idr']) }}"
                                            class="text-info" target="blank">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                    <div id="send-rec-client-idr" class="btn btn-sm py-1 border btn-light"
                                        data-bs-toggle="tooltip" data-bs-title="Send to Client"
                                        onclick="confirmSendToClient('{{ url('/') }}/receipt/school-program/{{ $receiptSch->id }}/send', 'idr', 'receipt')">
                                        <a href="#" class="text-info">
                                            <i class="bi bi-send"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    {{-- @endif --}}

                    {{-- Other  --}}
                    @php
                        $receiptAttachmentOther = $receiptSch
                            ->receiptAttachment()
                            ->where('currency', 'other')
                            ->first();
                        $receiptAttachmentRequestedOther = $receiptSch
                            ->receiptAttachment()
                            ->where('currency', 'other')
                            ->where('request_status', 'requested')
                            ->first();
                        $receiptAttachmentSignedOther = $receiptSch
                            ->receiptAttachment()
                            ->where('currency', 'other')
                            ->where('sign_status', 'signed')
                            ->first();
                        $receiptAttachmentNotYetOther = $receiptSch
                            ->receiptAttachment()
                            ->where('currency', 'other')
                            ->where('sign_status', 'not yet')
                            ->first();
                        $receiptAttachmentSentOther = $receiptSch
                            ->receiptAttachment()
                            ->where('currency', 'other')
                            ->where('send_to_client', 'sent')
                            ->first();
                    @endphp
                    @if ($invoiceSch->currency != 'idr')
                        <div class="d-flex align-items-stretch">
                            <div class="bg-secondary px-3 text-white" style="padding-top:10px ">Other Currency</div>
                            <div class="border p-1 text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                        data-bs-title="Preview Receipt">
                                        <a href="{{ route('receipt.school.preview_pdf', ['receipt' => $receiptSch->id, 'currency' => 'other']) }}"
                                            class="text-info" target="blank">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                    </div>
                                    @if (!$receiptAttachmentOther)
                                        <div id="print-other" class="btn btn-sm py-1 border btn-light"
                                            data-bs-toggle="tooltip" data-bs-title="Download">
                                            <a href="#" class="text-info" id="export_other">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </div>
                                        <div id="upload-other" data-bs-target="#uploadReceipt" data-bs-toggle="modal"
                                            class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                            data-bs-title="Upload">
                                            <a href="#" class="text-info" id="upload_other">
                                                <i class="bi bi-upload"></i>
                                            </a>
                                        </div>
                                    @elseif(isset($receiptAttachmentNotYetOther))
                                        {{-- <div id="request-acc-other" class="btn btn-sm py-1 border btn-light"
                                            data-bs-toggle="tooltip" data-bs-title="Request Sign" id="request-acc-other">
                                            <a href="#" class="text-info">
                                                <i class="bi bi-pen-fill"></i>
                                            </a>
                                        </div> --}}
                                        <div class="btn btn-sm py-1 border btn-light" id="openModalRequestSignIdr" data-curr="other"
                                            data-bs-toggle="modal" data-bs-target="#requestSignModal">
                                            <a href="#" class="text-info" data-bs-toggle="tooltip" data-bs-title="Request Sign">
                                                <i class="bi bi-pen-fill"></i>
                                            </a>
                                        </div>
                                    @else
                                        <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                            data-bs-title="Print Invoice">
                                            <a href="{{ route('receipt.school.print', ['receipt' => $receiptSch->id, 'currency' => 'other']) }}"
                                                class="text-info" target="blank">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        </div>
                                        <div id="send-rec-client-other" class="btn btn-sm py-1 border btn-light"
                                            data-bs-toggle="tooltip" data-bs-title="Send to Client"
                                            onclick="confirmSendToClient('{{ url('/') }}/receipt/school-program/{{ $receiptSch->id }}/send', 'other', 'receipt')">
                                            <a href="#" class="text-info">
                                                <i class="bi bi-send"></i>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
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
                            <td>{{ $receiptSch->receipt_id }}</td>
                        </tr>
                        <tr>
                            <td>Receipt Date :</td>
                            <td>{{ isset($receiptSch->receipt_date) ? date('M d, Y', strtotime($receiptSch->receipt_date)) : date('M d, Y H:i:s', strtotime($receiptSch->created_at)) }}
                            </td>
                        <tr>
                            <td>Created At :</td>
                            <td>{{ $receiptSch->created_at }}
                            </td>
                        </tr>
                        @if (isset($receiptSch->invdtl_id))
                            <tr>
                                <td>Installment Name :</td>
                                <td>{{ $receiptSch->invoiceInstallment->invdtl_installment }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td>Payment Method : </td>
                            <td>{{ $receiptSch->receipt_method }}</td>
                        </tr>
                        @if ($receiptSch->receipt_method == 'Cheque')
                            <tr>
                                <td>Cheque No : </td>
                                <td>{{ $receiptSch->receipt_cheque }}</td>
                            </tr>
                        @endif
                        @if ($invoiceSch->currency != 'idr')
                            <tr>
                                <td>Curs Rate :</td>
                                <td>{{ $invoiceSch->rate }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td>PPH23 :</td>
                            <td>
                                {{ $receiptSch->pph23 != null ? $receiptSch->pph23.'%' : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td>Amount :</td>
                            <td>
                                @if ($receiptSch->receipt_amount != 'null' && $invoiceSch->currency != 'idr')
                                    {{ $receiptSch->receipt_amount }}
                                    ( {{ $receiptSch->receipt_amount_idr }} )
                                @else
                                    {{ $receiptSch->receipt_amount_idr }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            @include('pages.receipt.school-program.form-detail.invoice')

            @if (!isset($invoiceSch->refund))
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
                                <div
                                    class="step step1 {{ $receiptSch->download_idr == 1 || $receiptAttachmentSigned || isset($receiptAttachmentNotYet) ? 'active' : '' }}">
                                    <div class="step-icon">1</div>
                                    <p>Download</p>
                                </div>
                                <div
                                    class="indicator-line {{ $receiptSch->download_idr == 1 || $receiptAttachmentSigned || isset($receiptAttachmentNotYet) ? 'active' : '' }}">
                                </div>
                                <div
                                    class="step step2 {{ isset($receiptAttachmentNotYet) || $receiptAttachmentSigned ? 'active' : '' }}">
                                    <div class="step-icon">2</div>
                                    <p>Upload</p>
                                </div>
                                <div
                                    class="indicator-line {{ isset($receiptAttachmentNotYet) || $receiptAttachmentSigned ? 'active' : '' }}">
                                </div>
                                <div
                                    class="step step3 {{ $receiptAttachmentRequested || $receiptAttachmentSigned ? 'active' : '' }}">
                                    <div class="step-icon">3</div>
                                    <p>Request Sign</p>
                                </div>
                                <div
                                    class="indicator-line {{ $receiptAttachmentRequested || $receiptAttachmentSigned ? 'active' : '' }}">
                                </div>
                                <div class="step step4 {{ $receiptAttachmentSigned ? 'active' : '' }}">
                                    <div class="step-icon">4</div>
                                    <p>Signed</p>
                                </div>
                                <div class="indicator-line {{ $receiptAttachmentSigned ? 'active' : '' }}"></div>
                                <div class="step step5 {{ $receiptAttachmentSent ? 'active' : '' }}">
                                    <div class="step-icon">5</div>
                                    <p>Print or Send to Client</p>
                                </div>
                            </section>
                        </div>

                        {{-- Other  --}}
                        @if ($invoiceSch->currency != 'idr')
                            <div class="text-center mt-5">
                                <hr>
                                <h6>Other Currency</h6>
                                <section class="step-indicator">
                                    <div class="step step1 {{ $receiptSch->download_other == 1 ? 'active' : '' }}">
                                        <div class="step-icon">1</div>
                                        <p>Download</p>
                                    </div>
                                    <div class="indicator-line {{ $receiptSch->download_other == 1 ? 'active' : '' }}">
                                    </div>
                                    <div
                                        class="step step2 {{ isset($receiptAttachmentNotYetOther) || $receiptAttachmentSignedOther ? 'active' : '' }}">
                                        <div class="step-icon">2</div>
                                        <p>Upload</p>
                                    </div>
                                    <div
                                        class="indicator-line {{ isset($receiptAttachmentNotYetOther) || $receiptAttachmentSignedOther ? 'active' : '' }}">
                                    </div>
                                    <div class="step step3 {{ $receiptAttachmentRequestedOther ? 'active' : '' }}">
                                        <div class="step-icon">3</div>
                                        <p>Request Sign</p>
                                    </div>
                                    <div class="indicator-line {{ $receiptAttachmentRequestedOther ? 'active' : '' }}">
                                    </div>
                                    <div class="step step4 {{ $receiptAttachmentSignedOther ? 'active' : '' }}">
                                        <div class="step-icon">4</div>
                                        <p>Signed</p>
                                    </div>
                                    <div class="indicator-line {{ $receiptAttachmentSignedOther ? 'active' : '' }}"></div>
                                    <div class="step step5 {{ $receiptAttachmentSentOther ? 'active' : '' }}">
                                        <div class="step-icon">5</div>
                                        <p>Print or Send to Client</p>
                                    </div>
                                </section>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
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
                    <form action="{{ route('receipt.school.upload', ['receipt' => $receiptSch->id]) }}" method="POST"
                        id="receipt" enctype="multipart/form-data">
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

    @if ($errors->has('attachment'))
        <script>
            $(document).ready(function() {
                $('#uploadReceipt').modal('show');

            })
        </script>
    @endif

    <script>
        function sendToClient(link) {

            showLoading()
            axios
                .get(link)
                .then(response => {
                    swal.close()
                    notification('success', 'Receipt has been send to client')
                    setTimeout(location.reload.bind(location), 3000);

                    $("#sendToClient--modal").modal('hide');
                })
                .catch(error => {
                    notification('error', 'Something went wrong when sending receipt to client. Please try again');
                    swal.close()
                })
        }

        $(document).ready(function() {
            $('.modal-select').select2({
                dropdownParent: $('#addReceipt .modal-content'),
                placeholder: "Select value",
                allowClear: true
            });

            $("#export_other").on('click', function(e) {
                e.preventDefault();

                Swal.showLoading()
                axios
                    .get(
                        '{{ route('receipt.school.export', ['receipt' => $receiptSch->id, 'currency' => 'other']) }}', {
                            responseType: 'arraybuffer'
                        })
                    .then(response => {
                        // console.log(response)

                        @php
                            $file_name = str_replace('/', '-', $receiptSch->receipt_id) . '-' . 'other' . '.pdf';
                        @endphp

                        let blob = new Blob([response.data], {
                                type: 'application/pdf'
                            }),
                            url = window.URL.createObjectURL(blob)
                        // create <a> tag dinamically
                        var fileLink = document.createElement('a');
                        fileLink.href = url;

                        // it forces the name of the downloaded file
                        fileLink.download = '{{ $file_name }}';

                        // triggers the click event
                        fileLink.click();

                        window.open(
                            url) // Mostly the same, I was just experimenting with different approaches, tried link.click, iframe and other solutions
                        swal.close()
                        notification('success', 'Receipt has been exported')
                        setTimeout(location.reload.bind(location), 3000);
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
                    .get(
                        '{{ route('receipt.school.export', ['receipt' => $receiptSch->id, 'currency' => 'idr']) }}', {
                            responseType: 'arraybuffer'
                        })
                    .then(response => {
                        // console.log(response)

                        @php
                            $file_name = str_replace('/', '-', $receiptSch->receipt_id) . '-' . 'idr' . '.pdf';
                        @endphp

                        let blob = new Blob([response.data], {
                                type: 'application/pdf'
                            }),
                            url = window.URL.createObjectURL(blob)
                        // create <a> tag dinamically
                        var fileLink = document.createElement('a');
                        fileLink.href = url;

                        // it forces the name of the downloaded file
                        fileLink.download = '{{ $file_name }}';

                        // triggers the click event
                        fileLink.click();

                        window.open(url)
                        // Mostly the same, I was just experimenting with different approaches, tried link.click, iframe and other solutions
                        swal.close()
                        notification('success', 'Receipt has been exported')
                        setTimeout(location.reload.bind(location), 3000);
                    })
                    .catch(error => {
                        notification('error', 'Something went wrong while exporting the invoice')
                        swal.close()
                    })
            })
        });

        $("#upload-idr").on('click', function(e) {
            // e.preventDefault();

            $("#currency").val('idr')
        });

        $("#upload-other").on('click', function(e) {
            // e.preventDefault();

            $("#currency").val('other')
        });

        function requestAcc(link, currency) {

            showLoading();
            var inv_rec_pic = $("input[name=pic_sign]:checked").val();
            var inv_rec_pic_name = $("input[name=pic_sign]:checked").data('name');

            axios
                .get(link, {
                        // responseType: 'arraybuffer',
                        params: {
                            type: currency,
                            to: inv_rec_pic,
                            name: inv_rec_pic_name
                        }
                    })
                .then(response => {
                    swal.close()
                    notification('success', 'Sign has been requested')
                    setTimeout(location.reload.bind(location), 3000);
                    $("#requestSignModal").modal('hide');
                    $("#requestSign--modal").modal('hide'); // this modal is for confirmation box 
                })
                .catch(error => {
                    // console.log(error)
                    notification('error', 'Something went wrong while send email')
                })
        }

        $(document).on("click", "#openModalRequestSignIdr", function() {
            var curr = $(this).data('curr');
            var currency = "'" + curr + "'";

            var url = '{{ url("/") }}/receipt/school-program/{{ $receiptSch->id }}/request_sign/'+curr;

            $('#sendToChoosenPic').attr("onclick", "confirmRequestSign('"+ url +"', "+ currency +")");

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

        $("#installment-list .detail").each(function() {
            $(this).click(function() {
                var link = "{{ url('/') }}/receipt/school-program/" + $(this).data('recid')
                window.location = link
            })
        })
    </script>
@endsection
