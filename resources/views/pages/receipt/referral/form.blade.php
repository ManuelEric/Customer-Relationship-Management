@extends('layout.main')

@section('title', 'Receipt of Referral')
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
                    <h4> {{ $invoiceRef->referral->partner->corp_name }} </h4>
                    <a href="{{ route('referral.show', ['referral' => $invoiceRef->referral->id]) }}"
                        class="text-primary text-decoration-none cursor-pointer" target="_blank">
                        <div class="card p-2">
                            <label class="text-muted mb-1">
                                Program Name:
                            </label>
                            <h6 class="text-primary">
                                @if (isset($invoiceRef->referral->prog_id))
                                    {{ $invoiceRef->referral->program->program_name }}
                                @else
                                    {{ $invoiceRef->referral->additional_prog_name }}
                                @endif
                            </h6>
                        </div>
                    </a>
                </div>
            </div>

            @include('pages.receipt.referral.form-detail.client')

        </div>

        <div class="col-md-8">
            {{-- Tools  --}}
            <div class="bg-white rounded p-2 mb-2 d-flex gap-2 shadow-sm justify-content-start">
                <div class="d-flex align-items-stretch">
                    <div class="bg-secondary px-3 text-white" style="padding-top:10px ">General</div>
                    <div class="border p-1 text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip" data-bs-title="Delete"
                                onclick="confirmDelete('{{ 'receipt/referral' }}', {{ $receiptRef->id }})">
                                <a href="#" class="text-danger">
                                    <i class="bi bi-trash2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                @if (!isset($invoiceRef->refund))
                    {{-- IDR  --}}

                    @php
                        $receiptAttachment = $receiptRef
                            ->receiptAttachment()
                            ->where('currency', 'idr')
                            ->whereNotNull('attachment')
                            ->first();
                        $receiptAttachmentRequested = $receiptRef
                            ->receiptAttachment()
                            ->where('currency', 'idr')
                            ->where('request_status', 'requested')
                            ->first();
                        $receiptAttachmentSigned = $receiptRef
                            ->receiptAttachment()
                            ->where('currency', 'idr')
                            ->where('sign_status', 'signed')
                            ->first();
                        $receiptAttachmentNotYet = $receiptRef
                            ->receiptAttachment()
                            ->where('currency', 'idr')
                            ->where('sign_status', 'not yet')
                            ->first();
                        $receiptAttachmentSent = $receiptRef
                            ->receiptAttachment()
                            ->where('currency', 'idr')
                            ->where('send_to_client', 'sent')
                            ->first();
                    @endphp

                    {{-- @if (!$invoiceRef->invoiceAttachment()->where('currency', 'idr')->first()) --}}
                    <div class="d-flex align-items-stretch">
                        <div class="bg-secondary px-3 text-white" style="padding-top:10px ">IDR</div>
                        <div class="border p-1 text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                    data-bs-title="Preview Invoice">
                                    <a href="{{ route('receipt.referral.preview_pdf', ['receipt' => $receiptRef->id, 'currency' => 'idr']) }}"
                                        class="text-info" target="blank">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                </div>
                                @if (!$receiptAttachment)
                                    <div id="print" class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                        data-bs-title="Download">
                                        <a href="#" class="text-info" id="openModalChooseDirector" data-curr="idr" data-bs-toggle="modal" data-bs-target="#chooseDirector">
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
                                        <a href="" class="text-info">
                                            <i class="bi bi-pen-fill"></i>
                                        </a>
                                    </div> --}}
                                    <div class="btn btn-sm py-1 border btn-light" onclick="confirmRequestSign('{{ route('receipt.referral.request_sign', ['receipt' => $receiptRef->id, 'currency' => 'idr']) }}', 'idr')">
                                        <a href="#" class="text-info" data-bs-toggle="tooltip" data-bs-title="Request Sign">
                                            <i class="bi bi-pen-fill"></i>
                                        </a>
                                    </div>
                                @else
                                    <div id="print" class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                        data-bs-title="Print Invoice">
                                        <a href="{{ route('receipt.referral.print', ['receipt' => $receiptRef->id, 'currency' => 'idr']) }}"
                                            class="text-info" target="blank">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                    <div id="send-rec-client-idr" class="btn btn-sm py-1 border btn-light"
                                        data-bs-toggle="tooltip" data-bs-title="Send to Client"
                                        onclick="confirmSendToClient('{{ url('/') }}/receipt/referral/{{ $receiptRef->id }}/send', 'idr', 'receipt')">
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
                        $receiptAttachmentOther = $receiptRef
                            ->receiptAttachment()
                            ->where('currency', 'other')
                            ->whereNotNull('attachment')
                            ->first();
                        $receiptAttachmentRequestedOther = $receiptRef
                            ->receiptAttachment()
                            ->where('currency', 'other')
                            ->where('request_status', 'requested')
                            ->first();
                        $receiptAttachmentSignedOther = $receiptRef
                            ->receiptAttachment()
                            ->where('currency', 'other')
                            ->where('sign_status', 'signed')
                            ->first();
                        $receiptAttachmentNotYetOther = $receiptRef
                            ->receiptAttachment()
                            ->where('currency', 'other')
                            ->where('sign_status', 'not yet')
                            ->first();
                        $receiptAttachmentSentOther = $receiptRef
                            ->receiptAttachment()
                            ->where('currency', 'other')
                            ->where('send_to_client', 'sent')
                            ->first();
                    @endphp
                    @if ($invoiceRef->currency != 'idr')
                        <div class="d-flex align-items-stretch">
                            <div class="bg-secondary px-3 text-white" style="padding-top:10px ">Other Currency</div>
                            <div class="border p-1 text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                        data-bs-title="Preview Invoice">
                                        <a href="{{ route('receipt.referral.preview_pdf', ['receipt' => $receiptRef->id, 'currency' => 'idr']) }}"
                                            class="text-info" target="blank">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                    </div>
                                    @if (!$receiptAttachmentOther)
                                        <div id="print-other" class="btn btn-sm py-1 border btn-light"
                                            data-bs-toggle="tooltip" data-bs-title="Download">
                                            <a href="#" class="text-info" id="openModalChooseDirector" data-curr="other" data-bs-toggle="modal" data-bs-target="#chooseDirector">
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
                                        <div class="btn btn-sm py-1 border btn-light" onclick="confirmRequestSign('{{ route('receipt.referral.request_sign', ['receipt' => $receiptRef->id, 'currency' => 'other']) }}', 'other')">
                                            <a href="#" class="text-info" data-bs-toggle="tooltip" data-bs-title="Request Sign">
                                                <i class="bi bi-pen-fill"></i>
                                            </a>
                                        </div>
                                    @else
                                        <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                            data-bs-title="Print Invoice">
                                            <a href="{{ route('receipt.referral.print', ['receipt' => $receiptRef->id, 'currency' => 'other']) }}"
                                                class="text-info" target="blank">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        </div>
                                        <div id="send-rec-client-other" class="btn btn-sm py-1 border btn-light"
                                            data-bs-toggle="tooltip" data-bs-title="Send to Client"
                                            onclick="confirmSendToClient('{{ url('/') }}/receipt/referral/{{ $receiptRef->id }}/send', 'other', 'receipt')">
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
                            <td>{{ $receiptRef->receipt_id }}</td>
                        </tr>
                        <tr>
                            <td>Receipt Date :</td>
                            <td>{{ isset($receiptRef->receipt_date) ? date('M d, Y', strtotime($receiptRef->receipt_date)) : date('M d, Y H:i:s', strtotime($receiptRef->created_at)) }}
                            </td>
                        <tr>
                            <td>Created At :</td>
                            <td>{{ $receiptRef->created_at }}
                            </td>
                        </tr>
                        <tr>
                            <td>Payment Method :</td>
                            <td>{{ $receiptRef->receipt_method }}</td>
                        </tr>
                        @if ($receiptRef->receipt_method == 'Cheque')
                            <tr>
                                <td>Cheque No : </td>
                                <td>{{ $receiptRef->receipt_cheque }}</td>
                            </tr>
                        @endif
                        @if ($invoiceRef->currency != 'idr')
                            <tr>
                                <td>Curs Rate :</td>
                                <td>{{ $invoiceRef->rate }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td>PPH23 :</td>
                            <td>
                                {{ $receiptRef->pph23 != null ? $receiptRef->pph23.'%' : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td>Amount :</td>
                            <td>
                                @if ($receiptRef->receipt_amount != null && $invoiceRef->currency != 'idr')
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

            @if (!isset($invoiceRef->refund))
                {{-- Receipt Progress  --}}
                <div class="card shadow-sm mb-3">
                    <div class="card-header">
                        <h6 class="my-0">
                            Receipt Progress
                        </h6>
                    </div>
                    <div class="card-body position-relative h-auto pb-4 pb-md-5">
                        {{-- IDR  --}}
                        @php
                            $receiptHasBeenDownloaded = $receiptRef->receiptAttachment()->where('currency', 'idr')->where('request_status', 'not yet')->first();
                            $receiptHasBeenStamped =
                                $receiptRef
                                    ->receiptAttachment()
                                    ->where('currency', 'idr')
                                    ->whereNotNull('attachment')
                                    ->count() > 0
                                    ? true
                                    : false; # with e-materai / uploaded
                            $receiptHasBeenRequested = $receiptRef
                                ->receiptAttachment()
                                ->where('currency', 'idr')
                                ->where('sign_status', 'not yet')
                                ->where('request_status', 'requested')
                                ->first();
                            $receiptHasBeenSigned = $receiptRef
                                ->receiptAttachment()
                                ->where('currency', 'idr')
                                ->where('sign_status', 'signed')
                                ->first();
                            $receiptHasBeenSentToClient = $receiptRef
                                ->receiptAttachment()
                                ->where('currency', 'idr')
                                ->where('send_to_client', 'sent')
                                ->first();
                        @endphp
                        <div class="text-center">
                            <h6>IDR</h6>
                            <section class="step-indicator">
                                <div @class([
                                    'step',
                                    'step 1',
                                    'active' => 
                                        $receiptHasBeenDownloaded ||
                                        $receiptHasBeenStamped ||
                                        $receiptHasBeenRequested ||
                                        $receiptHasBeenSigned ||
                                        $receiptHasBeenSentToClient,
                                ])
                                >
                                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Download">
                                        <i class="bi bi-download d-block d-md-none"></i>
                                        <div class="d-none d-md-block">1</div>
                                    </div>
                                    <p class="d-none d-md-block">Download</p>
                                </div>
                                <div @class([
                                    'indicator-line',
                                    'active' => 
                                        $receiptHasBeenDownloaded ||
                                        $receiptHasBeenStamped ||
                                        $receiptHasBeenRequested ||
                                        $receiptHasBeenSigned ||
                                        $receiptHasBeenSentToClient,
                                ])>
                                </div>
                                <div @class([
                                    'step',
                                    'step2',
                                    'active' => 
                                        $receiptHasBeenStamped ||
                                        $receiptHasBeenRequested ||
                                        $receiptHasBeenSigned ||
                                        $receiptHasBeenSentToClient,
                                ])>
                                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Upload">
                                        <i class="bi bi-upload d-block d-md-none"></i>
                                        <div class="d-none d-md-block">2</div>
                                    </div>
                                    <p class="d-none d-md-block">Upload</p>
                                </div>
                                <div @class([
                                    'indicator-line',
                                    'active' => 
                                        $receiptHasBeenStamped ||
                                        $receiptHasBeenRequested ||
                                        $receiptHasBeenSigned ||
                                        $receiptHasBeenSentToClient,
                                ])>
                                </div>
                                <div @class([
                                    'step',
                                    'step3',
                                    'active' => 
                                        $receiptHasBeenRequested ||
                                        $receiptHasBeenSigned ||
                                        $receiptHasBeenSentToClient,
                                ])>
                                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Request Sign">
                                        <i class="bi bi-pen-fill d-block d-md-none"></i>
                                        <div class="d-none d-md-block">3</div>
                                    </div>
                                    <p class="d-none d-md-block">Request Sign</p>
                                </div>
                                <div @class([
                                    'indicator-line',
                                    'active' => 
                                        $receiptHasBeenRequested ||
                                        $receiptHasBeenSigned ||
                                        $receiptHasBeenSentToClient,
                                ])></div>
                                <div @class([
                                    'step',
                                    'step4',
                                    'active' => $receiptHasBeenSigned || $receiptHasBeenSentToClient
                                ])>
                                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Signed">
                                        <i class="bi bi-check-lg d-block d-md-none"></i>
                                        <div class="d-none d-md-block">4</div>
                                    </div>
                                    <p class="d-none d-md-block">Signed</p>
                                </div>
                                <div @class([
                                    'indicator-line',
                                    'active' => $receiptHasBeenSigned || $receiptHasBeenSentToClient
                                ])></div>
                                <div @class([
                                    'step',
                                    'step5',
                                    'active' => $receiptHasBeenSentToClient
                                ])>
                                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Signed">
                                        <i class="bi bi-check-lg d-block d-md-none"></i>
                                        <div class="d-none d-md-block">4</div>
                                    </div>
                                    <p class="d-none d-md-block">Signed</p>
                                </div>
                            </section>
                        </div>

                        {{-- Other  --}}
                        @if ($invoiceRef->currency != 'idr')
                            <div class="text-center mt-5">
                                <hr>
                                @php
                                    $receiptHasBeenDownloaded_other = $receiptRef->receiptAttachment()->where('currency', 'other')->where('request_status', 'not yet')->first();
                                    $receiptHasBeenStamped_other =
                                        $receiptRef
                                            ->receiptAttachment()
                                            ->where('currency', 'other')
                                            ->whereNotNull('attachment')
                                            ->count() > 0
                                            ? true
                                            : false; # with e-materai / uploaded
                                    $receiptHasBeenRequested_other = $receiptRef
                                        ->receiptAttachment()
                                        ->where('currency', 'other')
                                        ->where('sign_status', 'not yet')
                                        ->where('request_status', 'requested')
                                        ->first();
                                    $receiptHasBeenSigned_other = $receiptRef
                                        ->receiptAttachment()
                                        ->where('currency', 'other')
                                        ->where('sign_status', 'signed')
                                        ->first();
                                    $receiptHasBeenSentToClient_other = $receiptRef
                                        ->receiptAttachment()
                                        ->where('currency', 'other')
                                        ->where('send_to_client', 'sent')
                                        ->first();
                                @endphp
                                <h6>Other Currency</h6>
                                <section class="step-indicator">
                                    <div @class([
                                        'step',
                                        'step 1',
                                        'active' => 
                                            $receiptHasBeenDownloaded_other ||
                                            $receiptHasBeenStamped_other ||
                                            $receiptHasBeenRequested_other ||
                                            $receiptHasBeenSigned_other ||
                                            $receiptHasBeenSentToClient_other,
                                    ])
                                    >
                                        <div class="step-icon">1</div>
                                        <p>Download</p>
                                    </div>
                                    <div @class([
                                        'indicator-line',
                                        'active' => 
                                            $receiptHasBeenDownloaded_other ||
                                            $receiptHasBeenStamped_other ||
                                            $receiptHasBeenRequested_other ||
                                            $receiptHasBeenSigned_other ||
                                            $receiptHasBeenSentToClient_other,
                                    ])>
                                    </div>
                                    <div @class([
                                        'step',
                                        'step2',
                                        'active' => 
                                            $receiptHasBeenStamped_other ||
                                            $receiptHasBeenRequested_other ||
                                            $receiptHasBeenSigned_other ||
                                            $receiptHasBeenSentToClient_other,
                                    ])>
                                        <div class="step-icon">2</div>
                                        <p>Upload</p>
                                    </div>
                                    <div @class([
                                        'indicator-line',
                                        'active' => 
                                            $receiptHasBeenStamped_other ||
                                            $receiptHasBeenRequested_other ||
                                            $receiptHasBeenSigned_other ||
                                            $receiptHasBeenSentToClient_other,
                                    ])>
                                    </div>
                                    <div @class([
                                        'step',
                                        'step3',
                                        'active' => 
                                            $receiptHasBeenRequested_other ||
                                            $receiptHasBeenSigned_other ||
                                            $receiptHasBeenSentToClient_other,
                                    ])>
                                        <div class="step-icon">3</div>
                                        <p>Request Sign</p>
                                    </div>
                                    <div @class([
                                        'indicator-line',
                                        'active' => 
                                            $receiptHasBeenRequested_other ||
                                            $receiptHasBeenSigned_other ||
                                            $receiptHasBeenSentToClient_other,
                                    ])></div>
                                    <div @class([
                                        'step',
                                        'step4',
                                        'active' => $receiptHasBeenSigned_other || $receiptHasBeenSentToClient_other
                                    ])>
                                        <div class="step-icon">4</div>
                                        <p>Signed</p>
                                    </div>
                                    <div @class([
                                        'indicator-line',
                                        'active' => $receiptHasBeenSigned_other || $receiptHasBeenSentToClient_other
                                    ])></div>
                                    <div @class([
                                        'step',
                                        'step5',
                                        'active' => $receiptHasBeenSentToClient_other
                                    ])>
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
                    <form action="{{ route('receipt.referral.upload', ['receipt' => $receiptRef->id]) }}" method="POST"
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
        });

        function downloadFile(url, type)
        {
            var selectedDirector = $("input[name=pic_sign]:checked").val();

            showLoading()
            axios
                .get(url, {
                        responseType: 'arraybuffer',
                        params: {
                            selectedDirectorMail: selectedDirector
                        }
                    })
                .then(response => {

                    var receiptId = "{{ $receiptRef->receipt_id }}";

                    var file_name = receiptId.replace(/\/|_/g, '-') + "-" + type + ".pdf";

                    let blob = new Blob([response.data], {
                            type: 'application/pdf'
                        }),
                        url = window.URL.createObjectURL(blob)
                    // create <a> tag dinamically
                    var fileLink = document.createElement('a');
                    fileLink.href = url;

                    // it forces the name of the downloaded file
                    fileLink.download = file_name;

                    // triggers the click event
                    fileLink.click();

                    window.open(
                        url) // Mostly the same, I was just experimenting with different approaches, tried link.click, iframe and other solutions
                    swal.close()
                    notification('success', 'Invoice has been exported')
                    setTimeout(location.reload.bind(location), 3000);
                })
                .catch(error => {
                    notification('error', 'Something went wrong while exporting the invoice')
                })
        }

        $("#upload-idr").on('click', function(e) {
            e.preventDefault();

            $("#currency").val('idr')
        });

        $("#upload-other").on('click', function(e) {
            e.preventDefault();

            $("#currency").val('other')
        });

        function requestAcc(link, currency) {

            showLoading();
            var inv_rec_pic = $("input[name=pic_sign]:checked").val();
            var inv_rec_pic_name = $("input[name=pic_sign]:checked").data('name');

            axios
                .get(link, {
                        responseType: 'arraybuffer',
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
                    notification('error', 'Something went wrong while send email')
                    // swal.close()
                })
        }

        $(document).on("click", "#openModalRequestSignIdr", function() {
            var curr = $(this).data('curr');
            var currency = "'" + curr + "'";

            var url = '{{ url("/") }}/receipt/referral/{{ $receiptRef->id }}/request_sign/'+curr;

            $('#sendToChoosenPic').attr("onclick", "confirmRequestSign('"+ url +"', "+ currency +")");

        });

        $(document).on("click", "#openModalChooseDirector", function() {
            var curr = $(this).data('curr');
            var currency = "'" + curr + "'";

            var url = "{{ url('/') }}/receipt/referral/{{ $receiptRef->id }}/export/" + curr;

            $("#download").attr("onclick", "downloadFile('"+ url +"', "+ currency +")");
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
    </script>
@endsection
