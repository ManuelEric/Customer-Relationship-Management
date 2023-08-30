@extends('layout.main')

@section('title', 'Receipt of Client Program')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Receipt</a></li>
    <li class="breadcrumb-item active" aria-current="page">View Detail</li>
@endsection
@section('content')
    
    @include('pages.receipt.pic-modal')

    <div class="row">
        <div class="col-md-4">
            <div class="card rounded mb-3">
                <div class="card-body text-center">
                    <h3><i class="bi bi-person"></i></h3>
                    <h4>{{ $client_prog->client->full_name }}</h4>
                    <a href="{{ route('student.program.show', ['student' => $client_prog->client->id, 'program' => $client_prog->clientprog_id]) }}"
                        class="text-primary text-decoration-none cursor-pointer" target="_blank">
                        <div class="card p-2">
                            <label class="text-muted mb-1">
                                Program Name:
                            </label>
                            <h6 class="text-primary">
                                {{ $client_prog->program->program_name }}
                                {{-- @php
                                $programName = explode('-', $client_prog->program_name);
                            @endphp
                            @for ($i = 0; $i < count($programName); $i++)
                                {{ $programName[$i] }} <br>
                            @endfor --}}
                            </h6>
                        </div>
                    </a>
                </div>
            </div>

            {{-- @include('pages.receipt.client-program.form-detail.refund') --}}

            @include('pages.receipt.client-program.form-detail.client')

        </div>

        <div class="col-md-8">
            {{-- Tools  --}}
            <div class="bg-white rounded p-2 mb-2 d-flex gap-2 shadow-sm justify-content-start">
                <div class="d-flex align-items-stretch">
                    <div class="bg-secondary px-3 text-white" style="padding-top:10px ">General</div>
                    <div class="border p-1 text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip" data-bs-title="Delete"
                                onclick="confirmDelete('receipt/client-program', '{{ $receipt->id }}')">
                                <a href="#" class="text-danger">
                                    <i class="bi bi-trash2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- IDR  --}}
                {{-- @if (!$receipt->invoiceProgram->invoiceAttachment()->where('currency', 'idr')->first()) --}}
                <div class="d-flex align-items-stretch">
                    <div class="bg-secondary px-3 text-white" style="padding-top:10px ">IDR</div>
                    <div class="border p-1 text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            @if (!$receipt->receiptAttachment()->where('currency', 'idr')->first())
                                <div id="print" class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                    data-bs-title="Download">
                                    <a href="#" class="text-info">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                                <div id="upload-idr" data-bs-target="#uploadReceipt" data-bs-toggle="modal"
                                    class="btn btn-sm py-1 border btn-light">
                                    <a href="#" class="text-info" data-bs-toggle="tooltip" data-bs-title="Upload">
                                        <i class="bi bi-upload"></i>
                                    </a>
                                </div>
                            @elseif ($receipt->receiptAttachment()->where('currency', 'idr')->where('sign_status', 'not yet')->first())
                                {{-- <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                    data-bs-title="Request Sign" id="request-acc">
                                    <a href="" class="text-info">
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
                                <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                    data-bs-title="Print Receipt">
                                    <a href="{{ route('receipt.client-program.print', ['receipt' => $receipt->id, 'currency' => 'idr']) }}"
                                        target="_blank" class="text-info">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>
                                {{-- <div id="send-rec-client-idr" class="btn btn-sm py-1 border btn-light"
                                    data-bs-toggle="tooltip" data-bs-title="Send to Client"
                                    onclick="confirmSendToClient('{{ url('/') }}/receipt/client-program/{{ $receipt->id }}/send', 'idr', 'receipt')">
                                    <a href="#" class="text-info">
                                        <i class="bi bi-send"></i>
                                    </a>
                                </div> --}}
                                <div class="btn btn-sm py-1 border btn-light" id="openModalSendToClientIdr" data-curr="idr"
                                    data-bs-toggle="modal" data-bs-target="#sendToClientModal">
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
                @if ($receipt->invoiceProgram->currency != 'idr')
                    <div class="d-flex align-items-stretch">
                        <div class="bg-secondary px-3 text-white" style="padding-top:10px ">Other Currency</div>
                        <div class="border p-1 text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                @if (!$receipt->receiptAttachment()->where('currency', 'other')->first())
                                    {{-- @if ($receipt->invoiceProgram->invoiceAttachment()->where('currency', 'other')->first()) --}}
                                    <div id="print-other" class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                        data-bs-title="Download">
                                        <a href="" class="text-info">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </div>
                                    <div id="upload-other" data-bs-target="#uploadReceipt" data-bs-toggle="modal"
                                        class="btn btn-sm py-1 border btn-light">
                                        <a href="javascript:void(0)" class="text-info" data-bs-toggle="tooltip"
                                            data-bs-title="Upload">
                                            <i class="bi bi-upload"></i>
                                        </a>
                                    </div>
                                    {{-- @endif --}}
                                @elseif ($receipt->receiptAttachment()->where('currency', 'other')->where('sign_status', 'not yet')->first())
                                    {{-- <div id="request-acc-other" class="btn btn-sm py-1 border btn-light"
                                        data-bs-toggle="tooltip" data-bs-title="Request Sign" id="request-acc-other">
                                        <a href="" class="text-info">
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
                                        data-bs-title="Print Receipt">
                                        <a href="{{ route('receipt.client-program.print', ['receipt' => $receipt->id, 'currency' => 'other']) }}"
                                            class="text-info">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                    {{-- <div id="send-rec-client-other" class="btn btn-sm py-1 border btn-light"
                                        data-bs-toggle="tooltip" data-bs-title="Send to Client"
                                        onclick="confirmSendToClient('{{ url('/') }}/receipt/client-program/{{ $receipt->id }}/send', 'other', 'receipt')">
                                        <a href="#" class="text-info">
                                            <i class="bi bi-send"></i>
                                        </a>
                                    </div> --}}
                                    <div class="btn btn-sm py-1 border btn-light" id="openModalSendToClientOther"
                                        data-curr="other" data-bs-toggle="modal" data-bs-target="#sendToClientModal">
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

            {{-- Receipt Progress  --}}
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h6 class="my-0">
                        Receipt Progress
                    </h6>
                </div>
                <div class="card-body position-relative h-auto pb-5">
                    {{-- IDR  --}}
                    @php
                        $receiptHasBeenDownloaded = $receipt->download_idr;
                        $receiptHasBeenStamped =
                            $receipt
                                ->receiptAttachment()
                                ->where('currency', 'idr')
                                ->count() > 0
                                ? true
                                : false; # with e-materai / uploaded
                        $receiptHasBeenRequested = $receipt
                            ->receiptAttachment()
                            ->where('currency', 'idr')
                            ->where('sign_status', 'not yet')
                            ->where('request_status', 'requested')
                            ->first();
                        $receiptHasBeenSigned = $receipt
                            ->receiptAttachment()
                            ->where('currency', 'idr')
                            ->where('sign_status', 'signed')
                            ->first();
                        $receiptHasBeenSentToClient = $receipt
                            ->receiptAttachment()
                            ->where('currency', 'idr')
                            ->where('send_to_client', 'sent')
                            ->first();
                    @endphp
                    <div class="text-center">
                        <h6>IDR</h6>
                        <section class="step-indicator">
                            <div @class([
                                'step-one',
                                'step',
                                'step1',
                                'active' =>
                                    $receiptHasBeenDownloaded ||
                                    $receiptHasBeenStamped ||
                                    $receiptHasBeenRequested ||
                                    $receiptHasBeenSigned ||
                                    $receiptHasBeenSentToClient,
                            ])>
                                <div class="step-icon">1</div>
                                <p>Download</p>
                            </div>
                            <div @class([
                                'step-one',
                                'indicator-line',
                                'active' =>
                                    $receiptHasBeenDownloaded ||
                                    $receiptHasBeenStamped ||
                                    $receiptHasBeenRequested ||
                                    $receiptHasBeenSigned ||
                                    $receiptHasBeenSentToClient,
                            ])></div>
                            <div @class([
                                'step-two',
                                'step',
                                'step2',
                                'active' =>
                                    $receiptHasBeenStamped ||
                                    $receiptHasBeenRequested ||
                                    $receiptHasBeenSigned ||
                                    $receiptHasBeenSentToClient,
                            ])>
                                <div class="step-icon">2</div>
                                <p>Upload</p>
                            </div>
                            <div @class([
                                'step-two',
                                'indicator-line',
                                'active' =>
                                    $receiptHasBeenStamped ||
                                    $receiptHasBeenRequested ||
                                    $receiptHasBeenSigned ||
                                    $receiptHasBeenSentToClient,
                            ])></div>
                            <div @class([
                                'step-three',
                                'step',
                                'step3',
                                'active' =>
                                    $receiptHasBeenRequested ||
                                    $receiptHasBeenSigned ||
                                    $receiptHasBeenSentToClient,
                            ])>
                                <div class="step-icon">3</div>
                                <p>Request Sign</p>
                            </div>
                            <div @class([
                                'step-three',
                                'indicator-line',
                                'active' =>
                                    $receiptHasBeenRequested ||
                                    $receiptHasBeenSigned ||
                                    $receiptHasBeenSentToClient,
                            ])></div>
                            <div @class([
                                'step-four',
                                'step',
                                'step4',
                                'active' => $receiptHasBeenSigned || $receiptHasBeenSentToClient,
                            ])>
                                <div class="step-icon">4</div>
                                <p>Signed</p>
                            </div>
                            <div @class([
                                'step-four',
                                'indicator-line',
                                'active' => $receiptHasBeenSigned || $receiptHasBeenSentToClient,
                            ])></div>
                            <div @class([
                                'step-five',
                                'step',
                                'step5',
                                'active' => $receiptHasBeenSentToClient,
                            ])>
                                <div class="step-icon">5</div>
                                <p>Print or Send to Client</p>
                            </div>
                        </section>
                    </div>

                    {{-- Other  --}}
                    @php
                        $receiptHasBeenDownloaded_other = $receipt->download_other;
                        $receiptHasBeenStamped_other =
                            $receipt
                                ->receiptAttachment()
                                ->where('currency', 'other')
                                ->count() > 0
                                ? true
                                : false; # with e-materai / uploaded
                        $receiptHasBeenRequested_other = $receipt
                            ->receiptAttachment()
                            ->where('currency', 'other')
                            ->where('sign_status', 'not yet')
                            ->where('request_status', 1)
                            ->first();
                        $receiptHasBeenSigned_other = $receipt
                            ->receiptAttachment()
                            ->where('currency', 'other')
                            ->where('sign_status', 'signed')
                            ->first();
                        $receiptHasBeenSentToClient_other = $receipt
                            ->receiptAttachment()
                            ->where('currency', 'other')
                            ->where('send_to_client', 'sent')
                            ->first();
                    @endphp
                    @if ($receipt->invoiceProgram->currency != 'idr')
                        <div class="text-center mt-5">
                            <hr>
                            <h6>Other Currency</h6>
                            <section class="step-indicator">
                                <div @class([
                                    'step-one-other',
                                    'step',
                                    'step1',
                                    'active' =>
                                        $receiptHasBeenDownloaded_other ||
                                        $receiptHasBeenStamped_other ||
                                        $receiptHasBeenRequested_other ||
                                        $receiptHasBeenSigned_other ||
                                        $receiptHasBeenSentToClient_other,
                                ])>
                                    <div class="step-icon">1</div>
                                    <p>Download</p>
                                </div>
                                <div @class([
                                    'step-one-other',
                                    'indicator-line',
                                    'active' =>
                                        $receiptHasBeenDownloaded_other ||
                                        $receiptHasBeenStamped_other ||
                                        $receiptHasBeenRequested_other ||
                                        $receiptHasBeenSigned_other ||
                                        $receiptHasBeenSentToClient_other,
                                ])></div>
                                <div @class([
                                    'step-two-other',
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
                                    'step-two-other',
                                    'indicator-line',
                                    'active' =>
                                        $receiptHasBeenStamped_other ||
                                        $receiptHasBeenRequested_other ||
                                        $receiptHasBeenSigned_other ||
                                        $receiptHasBeenSentToClient_other,
                                ])></div>
                                <div @class([
                                    'step-three-other',
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
                                    'step-three-other',
                                    'indicator-line',
                                    'active' =>
                                        $receiptHasBeenRequested_other ||
                                        $receiptHasBeenSigned_other ||
                                        $receiptHasBeenSentToClient_other,
                                ])></div>
                                <div @class([
                                    'step-four-other',
                                    'step',
                                    'step4',
                                    'active' =>
                                        $receiptHasBeenSigned_other || $receiptHasBeenSentToClient_other,
                                ])>
                                    <div class="step-icon">4</div>
                                    <p>Signed</p>
                                </div>
                                <div @class([
                                    'step-four-other',
                                    'indicator-line',
                                    'active' =>
                                        $receiptHasBeenSigned_other || $receiptHasBeenSentToClient_other,
                                ])></div>
                                <div @class([
                                    'step-five-other',
                                    'step',
                                    'step5',
                                    'active' => $receiptHasBeenSentToClient_other,
                                ])>
                                    <div class="step-icon">5</div>
                                    <p>Print or Send to Client</p>
                                </div>
                            </section>
                        </div>
                    @endif
                </div>
            </div>

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
                            <td>{{ isset($receipt->receipt_date) ? date('M d, Y', strtotime($receipt->receipt_date)) : date('M d, Y H:i:s', strtotime($receipt->created_at)) }}
                            </td>
                        </tr>
                        <tr>
                            <td>Created At :</td>
                            <td>{{ $receipt->created_at }}
                            </td>
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
                                @if ($receipt->receipt_amount != null && $receipt->receipt_amount != "$ 0" && $receipt->invoiceProgram->currency != 'idr')
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
                    <form action="{{ route('receipt.client-program.upload', ['receipt' => $receipt->id]) }}"
                        method="POST" id="receipt" enctype="multipart/form-data">
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

    @if ($client_prog->client->parents->count() > 0)
    <div class="modal fade" id="sendToClientModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <span>
                        Send To Client
                    </span>
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div class="modal-body w-100 text-start">
                    {{-- <form action="" method="POST" id="reminderForm"> --}}
                    @csrf
                    {{-- @method('put') --}}
                    <div class="form-group">

                        <input type="hidden" name="receipt_id" id="receipt_id" value="{{ $receipt->id }}"
                            class="form-control w-100">
                        <input type="hidden" name="clientprog_id" id="clientprog_id"
                            value="{{ $client_prog->clientprog_id }}" class="form-control w-100">
                        <input type="hidden" name="parent_id" id="parent_id"
                            value="{{ $client_prog->client->parents[0]->id }}" class="form-control w-100">
                            {{-- value="{{ $client_prog->client->id }}" class="form-control w-100"> --}}
                        <label for="">Email Parent</label>
                        <input type="mail" name="parent_mail" id="parent_mail"
                            value="{{ $client_prog->client->parents[0]->mail }}" class="form-control w-100">
                            {{-- value="{{ $client_prog->client->mail }}" class="form-control w-100"> --}}
                    </div>
                    {{-- <hr> --}}
                    <div class="d-flex justify-content-between">
                        <button type="button" href="#" class="btn btn-outline-danger btn-sm"
                            data-bs-dismiss="modal">
                            <i class="bi bi-x-square me-1"></i>
                            Cancel</button>

                        <button type="button" id="ConfirmSendToClient" class="btn btn-primary btn-sm">
                            <i class="bi bi-save2 me-1"></i>
                            Send</button>
                    </div>
                    {{-- </form> --}}
                </div>
            </div>
        </div>
    </div>
    @endif

    @if ($errors->has('attachment'))
        <script>
            $(document).ready(function() {
                $('#uploadReceipt').modal('show');
            })
        </script>
    @endif

    <script>
        $(document).on("click", "#openModalSendToClientIdr", function() {
            var curr = $(this).data('curr');
            curr = "'" + curr + "'";
            $('#ConfirmSendToClient').attr("onclick",
                "confirmSendToClient('{{ url('/') }}/receipt/client-program/{{ $receipt->id }}/send', " +
                curr + ", 'receipt')");

        });

        $(document).on("click", "#openModalSendToClientOther", function() {
            var curr = $(this).data('curr');
            curr = "'" + curr + "'";
            $('#ConfirmSendToClient').attr("onclick",
                "confirmSendToClient('{{ url('/') }}/receipt/client-program/{{ $receipt->id }}/send', " +
                curr + ", 'receipt')");
        });

        function sendToClient(link) {
            $("#sendToClient--modal").modal('hide');
            $('#sendToClientModal').modal('hide');
            showLoading()
            var linkUpdateMail = '{{ url('/') }}/receipt/client-program/' + $('#receipt_id').val() +
                '/update/parent/mail';

            axios.post(linkUpdateMail, {
                    parent_id: $('#parent_id').val(),
                    parent_mail: $('#parent_mail').val(),
                })
                .then(function(response1) {

                    axios
                        .get(link)
                        .then(response => {
                            swal.close()
                            notification('success', 'Receipt has been send to client')
                            $(".step-five").addClass('active');
                        })
                        .catch(error => {
                            notification('error',
                                'Something went wrong when sending receipt to client. Please try again');
                            swal.close()
                        })
                })
                .catch(function(error) {
                    swal.close();
                    notification('error', error)
                })

        }

        function requestAcc(link, currency) {
                
            showLoading();
            var url = '{{ url("/") }}/receipt/client-program/{{ $receipt->id }}/request_sign';
            var inv_rec_pic = $("input[name=pic_sign]:checked").val();
            var inv_rec_pic_name = $("input[name=pic_sign]:checked").data('name');

            axios
                .get(link, {
                        params: {
                            type: currency,
                            to: inv_rec_pic,
                            name: inv_rec_pic_name
                        }
                    })
                .then(response => {

                    swal.close()
                    notification('success', 'Sign has been requested')
                    $(".step-three").addClass('active');
                    $("#requestSignModal").modal('hide');
                    $("#requestSign--modal").modal('hide'); // this modal is for confirmation box 

                })
                .catch(error => {

                    // notification('error', error.message)
                    notification('error', 'Something went wrong while send email')
                    // swal.close()
                })
        }

        $(document).on("click", "#openModalRequestSignIdr", function() {
            var curr = $(this).data('curr');
            var currency = "'" + curr + "'";

            var url = "{{ route('receipt.client-program.request_sign', ['receipt' => $receipt->id]) }}"

            $('#sendToChoosenPic').attr("onclick", "confirmRequestSign('"+ url +"', "+ currency +")");

        });

        $(document).ready(function() {
            $('.modal-select').select2({
                dropdownParent: $('#addReceipt .modal-content'),
                placeholder: "Select value",
                allowClear: true
            });

            // $("#send-rec-client-other").on('click', function(e) {
            //     e.preventDefault()
            //     showLoading()

            //     axios
            //         .get(
            //             '{{ route('receipt.client-program.send_to_client', ['receipt' => $receipt->id, 'currency' => 'other']) }}'
            //         )
            //         .then(response => {
            //             swal.close()
            //             notification('success', 'Receipt has been send to client')
            //             $(".step-five-other").addClass('active');
            //         })
            //         .catch(error => {
            //             notification('error',
            //                 'Something went wrong when sending receipt to client. Please try again');
            //             swal.close()
            //         })
            // })

            

            $("#upload-idr").on('click', function() {
                $("#currency").val('idr');
            })

            $("#upload-other").on('click', function() {
                $("#currency").val('other');
            })

            $("#print").on('click', function(e) {
                e.preventDefault();

                showLoading()
                axios
                    .get('{{ route('receipt.client-program.export', ['receipt' => $receipt->id]) }}', {
                        responseType: 'arraybuffer',
                        params: {
                            type: 'idr'
                        }
                    })
                    .then(response => {

                        @php
                            $file_name = str_replace('/', '-', $receipt->receipt_id) . '-' . 'idr' . '.pdf';
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
                            url
                        ) // Mostly the same, I was just experimenting with different approaches, tried link.click, iframe and other solutions
                        swal.close()
                        notification('success', 'Receipt in Rupiah has been exported')
                        $(".step-one").addClass('active');
                    })
                    .catch(error => {
                        notification('error', 'Something went wrong while exporting the receipt')
                        swal.close()
                    })
            })

            $("#print-other").on('click', function(e) {
                e.preventDefault();

                showLoading()
                axios
                    .get('{{ route('receipt.client-program.export', ['receipt' => $receipt->id]) }}', {
                        responseType: 'arraybuffer',
                        params: {
                            type: 'other'
                        }
                    })
                    .then(response => {

                        @php
                            $file_name = str_replace('/', '-', $receipt->receipt_id) . '-' . 'other' . '.pdf';
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
                            url
                        ) // Mostly the same, I was just experimenting with different approaches, tried link.click, iframe and other solutions
                        swal.close()
                        notification('success', 'Receipt in Rupiah has been exported')
                        $(".step-one-other").addClass('active');
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
