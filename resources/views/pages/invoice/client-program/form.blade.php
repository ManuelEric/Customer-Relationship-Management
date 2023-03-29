@extends('layout.main')

@section('title', 'Invoice Bigdata Platform')

@section('content')

    @php
        $disabled = isset($status) && $status == 'view' ? 'disabled' : null;
    @endphp

    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ url('invoice/client-program?s=needed') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Invoice
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card rounded mb-3">
                <div class="card-body text-center">
                    <h3><i class="bi bi-person"></i></h3>
                    <h4>{{ $clientProg->client->full_name }}</h4>

                    <a
                        href="{{ route('student.program.show', ['student' => $clientProg->client->id, 'program' => $clientProg->clientprog_id]) }}" class="text-primary text-decoration-none cursor-pointer" target="_blank">
                        <h6 class="d-flex flex-column">
                            @php
                                $programName = explode('-', $clientProg->program_name);
                            @endphp
                            @for ($i = 0; $i < count($programName); $i++)
                                {{ $programName[$i] }}  <br>
                            @endfor
                        </h6>
                    </a>
                </div>
            </div>

            {{-- Tools  --}}
            @if (isset($invoice) && !isset($invoice->refund))
                <div class="bg-white rounded p-2 mb-3 d-flex align-items-stretch gap-2 shadow-sm justify-content-center">
                    <div class="border p-1 text-center flex-fill">
                        <div class="d-flex gap-1 justify-content-center">
                            <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                data-bs-title="{{ $status == 'edit' ? 'Back' : 'Edit' }}">
                                <a href="{{ $status == 'edit' ? url('invoice/client-program/' . $clientProg->clientprog_id) : url('invoice/client-program/' . $clientProg->clientprog_id . '/edit') }}"
                                    class="text-warning">
                                    <i class="bi {{ $status == 'edit' ? 'bi-arrow-left' : 'bi-pencil' }}"></i>
                                </a>
                            </div>
                            <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip" data-bs-title="Cancel"
                                onclick="confirmDelete('invoice/client-program', {{ $clientProg->clientprog_id }})">
                                <a href="#" class="text-danger">
                                    <i class="bi bi-trash2"></i>
                                </a>
                            </div>
                        </div>
                        <hr class="my-1">
                        <small>General</small>
                    </div>
                    <div class="border p-1 text-center flex-fill">
                        <div class="d-flex gap-1 justify-content-center">
                            @if (isset($invoice) && !$invoice->invoiceAttachment()->where('currency', 'idr')->where('sign_status', 'signed')->first())
                                <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                    data-bs-title="Request Sign" id="request-acc">
                                    <a href="" class="text-info">
                                        <i class="bi bi-pen-fill"></i>
                                    </a>
                                </div>
                            @else
                                <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                    data-bs-title="Print Invoice">
                                    <a href="{{ route('invoice.program.print', ['client_program' => $clientProg->clientprog_id, 'currency' => 'idr']) }}"
                                        class="text-info">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>
                                <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                    data-bs-title="Send to Client" id="send-inv-client-idr">
                                    <a href="#" class="text-info">
                                        <i class="bi bi-send"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                        <hr class="my-1">
                        <small class="text-center">IDR</small>
                    </div>

                    @if ($invoice->currency != 'idr')
                        <div class="border p-1 text-center flex-fill">
                            <div class="d-flex gap-1 justify-content-center">
                                @if (!isset($invoice->refund) && isset($invoice) && !$invoice->invoiceAttachment()->where('currency', 'other')->where('sign_status', 'signed')->first())
                                    <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                        data-bs-title="Request Sign" id="request-acc-other">
                                        <a href="" class="text-info">
                                            <i class="bi bi-pen-fill"></i>
                                        </a>
                                    </div>
                                @else
                                    <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                        data-bs-title="Print Invoice">
                                        <a href="{{ route('invoice.program.print', ['client_program' => $clientProg->clientprog_id, 'currency' => 'other']) }}"
                                            class="text-info">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                    <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                        data-bs-title="Send to Client" id="send-inv-client-other">
                                        <a href="#" class="text-info">
                                            <i class="bi bi-send"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <hr class="my-1">
                            <small class="text-center">Other Currency</small>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Invoice Progress  --}}
            @if (isset($invoice) && !isset($invoice->refund))
                <div class="card shadow-sm mb-3">
                    <div class="card-header">
                        <h6 class="my-0">
                            Invoice Progress
                        </h6>
                    </div>
                    <div class="card-body position-relative h-auto pb-5">
                        {{-- IDR  --}}
                        @php
                            $invoiceHasBeenRequested = $invoice->invoiceAttachment()->where('currency', 'idr')->where('sign_status', 'not yet')->first();
                            $invoiceHasBeenSigned = $invoice->invoiceAttachment()->where('currency', 'idr')->where('sign_status', 'signed')->first();
                            $invoiceHasSentToClient = $invoice->invoiceAttachment()->where('currency', 'idr')->where('sign_status', 'signed')->where('send_to_client', 'sent')->first();
                        @endphp
                        <div class="text-center">
                            <h6>IDR</h6>
                            <section class="step-indicator">
                                <div @class([
                                    'step-one',
                                    'step',
                                    'step1',
                                    'active' => $invoiceHasBeenRequested || $invoiceHasBeenSigned || $invoiceHasSentToClient
                                ])>
                                    <div @class([
                                        'step-one',
                                        'step-icon',
                                        'active' => $invoiceHasBeenRequested || $invoiceHasBeenSigned || $invoiceHasSentToClient
                                    ])>1</div>
                                    <p>Request Sign</p>
                                </div>
                                <div @class([
                                    'step-one',
                                    'indicator-line',
                                    'active' => $invoiceHasBeenRequested || $invoiceHasBeenSigned || $invoiceHasSentToClient
                                ])></div>
                                <div @class([
                                    'step',
                                    'step2',
                                    'active' => $invoiceHasBeenSigned || $invoiceHasSentToClient
                                ])>
                                    <div @class([
                                        'step-icon',
                                        'active' => $invoiceHasBeenSigned || $invoiceHasSentToClient
                                    ])>2</div>
                                    <p>Signed</p>
                                </div>
                                <div @class([
                                    'indicator-line',
                                    'active' => $invoiceHasBeenSigned || $invoiceHasSentToClient
                                ])></div>
                                <div @class([
                                    'step-three',
                                    'step',
                                    'step3',
                                    'active' => $invoiceHasSentToClient
                                ])>
                                    <div @class([
                                        'step-three',
                                        'step-icon',
                                        'active' => $invoiceHasSentToClient
                                    ])>3</div>
                                    <p>Print or Send to Client</p>
                                </div>
                            </section>
                        </div>

                        {{-- Other  --}}
                        @php
                            $invoiceHasBeenRequested_other = $invoice->invoiceAttachment()->where('currency', 'other')->where('sign_status', 'not yet')->first();
                            $invoiceHasBeenSigned_other = $invoice->invoiceAttachment()->where('currency', 'other')->where('sign_status', 'signed')->first();
                            $invoiceHasSentToClient_other = $invoice->invoiceAttachment()->where('currency', 'other')->where('sign_status', 'signed')->where('send_to_client', 'sent')->first();
                        @endphp
                        <div class="text-center mt-5">
                            <hr>
                            <h6>Other Currency</h6>
                            <section class="step-indicator">
                                <div @class([
                                    'step-one-other',
                                    'step',
                                    'step1',
                                    'active' => $invoiceHasBeenRequested_other || $invoiceHasBeenSigned_other || $invoiceHasSentToClient_other
                                ])>
                                    <div @class([
                                        'step-one-other',
                                        'step-icon',
                                        'active' => $invoiceHasBeenRequested_other || $invoiceHasBeenSigned_other || $invoiceHasSentToClient_other
                                    ])>1</div>
                                    <p>Request Sign</p>
                                </div>
                                <div @class([
                                    'step-one-other',
                                    'indicator-line',
                                    'active' => $invoiceHasBeenRequested_other || $invoiceHasBeenSigned_other || $invoiceHasSentToClient_other
                                ])></div>
                                <div @class([
                                    'step',
                                    'step2',
                                    'active' => $invoiceHasBeenSigned_other || $invoiceHasSentToClient_other
                                ])>
                                    <div @class([
                                        'step-icon',
                                        'active' => $invoiceHasBeenSigned_other || $invoiceHasSentToClient_other
                                    ])>2</div>
                                    <p>Signed</p>
                                </div>
                                <div  @class([
                                    'indicator-line',
                                    'active' => $invoiceHasBeenSigned_other || $invoiceHasSentToClient_other
                                ])></div>
                                <div @class([
                                    'step-three-other',
                                    'step',
                                    'step3',
                                    'active' => $invoiceHasSentToClient_other
                                ])>
                                    <div @class([
                                        'step-three-other',
                                        'step-icon',
                                        'active' => $invoiceHasSentToClient_other
                                    ])>3</div>
                                    <p>Print or Send to Client</p>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            @endif

            @if (isset($invoice->receipt) && $clientProg->status == 3)
                @include('pages.invoice.client-program.detail.refund')
            @endif

            @include('pages.invoice.client-program.form-detail.client')

            @if ($status == 'view' && isset($invoice->invoiceDetail))
                @include('pages.invoice.client-program.form-detail.installment-list')
            @endif
        </div>

        <div class="col-md-8">
            <div class="card rounded mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="py-2">
                        <h6 class="m-0 p-0">
                            <i class="bi bi-person me-2"></i>
                            Invoice {{ isset($invoice) ? ' : ' . $invoice->inv_id : null }}
                        </h6>
                    </div>
                    <div class="">
                        {{-- @if ($invoice === null && isset($invoice->receipt)) --}}
                        @if (isset($invoice) && $invoice->inv_paymentmethod == 'Full Payment' && !isset($invoice->receipt))
                            <button class="btn btn-sm btn-outline-primary py-1"
                                onclick="checkReceipt();setIdentifier('Full Payment', '{{ $invoice->id }}')">
                                <i class="bi bi-plus"></i> Receipt
                            </button>
                        @endif
                        @if (isset($invoice->receipt) && $invoice->inv_paymentmethod == 'Full Payment')
                            <a href="{{ route('receipt.client-program.show', ['receipt' => $invoice->receipt->id]) }}">
                                <button class="btn btn-sm btn-outline-warning py-1">
                                    <i class="bi bi-eye"></i> View Receipt
                                </button>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <form
                        action="{{ isset($invoice) ? route('invoice.program.update', ['client_program' => $clientProg->clientprog_id]) : route('invoice.program.store') }}"
                        method="POST" id="invoice-form">
                        @csrf
                        @if (isset($invoice))
                            @method('PUT')
                        @endif
                        <input type="hidden" name="clientprog_id" value="{{ $clientProg->clientprog_id }}">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="">Currency <sup class="text-danger">*</sup></label>
                                <select id="currency" name="currency" class="select w-100" onchange="checkCurrency()"
                                    {{ $disabled }}>
                                    <option value="idr"
                                        @if (
                                            ($clientProg->program->prog_payment == 'session' or $clientProg->program->prog_payment == 'idr') &&
                                                !isset($invoice)) {{ 'selected' }}
                                    @elseif (isset($invoice) && $invoice->inv_category == 'idr')
                                        {{ 'selected' }} @endif>
                                        IDR</option>
                                    <option value="other"
                                        @if ($clientProg->program->prog_payment != 'session' && $clientProg->program->prog_payment != 'idr' && !isset($invoice)) {{ 'selected' }}
                                    @elseif (isset($invoice) && $invoice->inv_category == 'other')
                                        {{ 'selected' }} @endif>
                                        Other Currency</option>
                                </select>
                                @error('currency')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3 currency-detail d-none">
                                <label for="">Currency Detail <sup class="text-danger">*</sup></label>
                                {{ old('currency') }}
                                <select class="select w-100" name="currency_detail" id="currency_detail"
                                    {{ $disabled !== null ? $disabled : 'onchange="checkCurrencyDetail()"' }}>
                                    <option data-placeholder="true"></option>
                                    <option value="usd"
                                        @if (isset($invoice->currency) && $invoice->currency == 'usd') {{ 'selected' }}
                                        @elseif (old('currency') !== null && in_array('usd', (array) old('currency_detail')))
                                            {{ 'selected' }} @endif>
                                        USD</option>
                                    <option value="sgd"
                                        @if (isset($invoice->currency) && $invoice->currency == 'sgd') {{ 'selected' }}
                                        @elseif (old('currency') !== null && in_array('sgd', (array) old('currency_detail')))
                                            {{ 'selected' }} @endif>
                                        SGD</option>
                                    <option value="gbp"
                                        @if (isset($invoice->currency) && $invoice->currency == 'gbp') {{ 'selected' }}
                                        @elseif (old('currency') !== null && in_array('gbp', (array) old('currency_detail')))
                                            {{ 'selected' }} @endif>
                                        GBP</option>
                                </select>
                            </div>

                            <div class="col-md-3  mb-3 currency-detail d-none">
                                <label for="">Current Rate to IDR <sup class="text-danger">*</sup></label>
                                <input type="number" name="curs_rate" id="current_rate"
                                    value="{{ isset($invoice->curs_rate) ? $invoice->curs_rate : old('curs_rate') }}"
                                    {{ $disabled }} class="form-control form-control-sm rounded">
                                @error('curs_rate')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="">Is Session? <sup class="text-danger">*</sup></label>
                                <select name="is_session" id="session" class="select w-100" onchange="checkSession()"
                                    {{ $disabled }}>
                                    <option data-placeholder="true"></option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                                {{-- @error('is_session')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror --}}
                            </div>

                            {{-- SESSION  --}}
                            <div class="col-md-12 session-detail d-none session mb-3">
                                {{-- IDR  --}}
                                <div class="session-currency d-none session-idr">
                                    @include('pages.invoice.client-program.form-detail.session-idr')
                                </div>

                                {{-- OTHER  --}}
                                <div class="session-currency d-none session-other">
                                    @include('pages.invoice.client-program.form-detail.session-other')
                                </div>

                            </div>

                            {{-- NOT SESSION  --}}
                            <div class="col-md-12 session-detail d-none not-session mb-3">
                                {{-- IDR  --}}
                                <div class="not-session-currency d-none not-session-idr">
                                    @include('pages.invoice.client-program.form-detail.not-session-idr')
                                </div>

                                {{-- OTHER  --}}
                                <div class="not-session-currency d-none not-session-other">
                                    @include('pages.invoice.client-program.form-detail.not-session-other')
                                </div>
                            </div>

                            <div class="col-md-12">
                                <input type="hidden" name="" id="total_idr"
                                    value="{{ isset($invoice->inv_totalprice_idr) ? $invoice->inv_totalprice_idr : null }}">
                                <input type="hidden" name="" id="total_other"
                                    value="{{ isset($invoice->inv_totalprice) ? $invoice->inv_totalprice : null }}">
                            </div>

                            <div class="col-md-5 mb-3 invoice d-none">
                                <label for="">Payment Method <sup class="text-danger">*</sup></label>
                                <select name="inv_paymentmethod" id="payment_method" class="select w-100"
                                    onchange="checkPayment()" {{ $disabled }}>
                                    <option data-placeholder="true"></option>
                                    <option value="full"
                                        @if (isset($invoice->inv_paymentmethod) && $invoice->inv_paymentmethod == 'Full Payment') {{ 'selected' }}
                                        @elseif (old('inv_paymentmethod') !== null && old('inv_paymentmethod') == 'full')
                                            {{ 'selected' }} @endif>
                                        Full Payment</option>
                                    <option value="installment"
                                        @if (isset($invoice->inv_paymentmethod) && $invoice->inv_paymentmethod == 'Installment') {{ 'selected' }}
                                        @elseif (old('inv_paymentmethod') !== null && old('inv_paymentmethod') == 'installment')
                                            {{ 'selected' }} @endif>
                                        Installment</option>
                                </select>
                                {{-- @error('inv_paymentmethod')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror --}}
                            </div>
                            <div class="col-md-7">
                                <div class="row">
                                    <div class="col-md-6 mb-3 invoice d-none">
                                        <label for="">Created Date <sup class="text-danger">*</sup></label>
                                        <input type="date" name="invoice_date" id=""
                                            value="{{ date('Y-m-d') }}" readonly {{ $disabled }}
                                            class='form-control form-control-sm rounded'>
                                        {{-- @error('invoice_date')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror --}}
                                    </div>
                                    <div class="col-md-6 mb-3 invoice d-none">
                                        <label for="">Due Date <sup class="text-danger">*</sup></label>
                                        <input type="date" name="inv_duedate" id=""
                                            value="{{ isset($invoice->inv_duedate) ? $invoice->inv_duedate : old('inv_duedate') }}"
                                            {{ $disabled }} class='form-control form-control-sm rounded'>
                                        {{-- @error('inv_duedate')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror --}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                {{-- IDR  --}}
                                <div class="installment-card d-none installment-idr">
                                    @include('pages.invoice.client-program.form-detail.installment-idr')
                                </div>

                                <div class="installment-card d-none installment-other">
                                    @include('pages.invoice.client-program.form-detail.installment-other')
                                </div>

                            </div>
                            <div class="col-md-12 mb-3 invoice d-none">
                                <label for="">Notes</label>
                                <textarea name="inv_notes" id="" {{ $disabled }}>{{ isset($invoice->inv_notes) ? $invoice->inv_notes : old('inv_notes') }}</textarea>
                                {{-- @error('inv_notes')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror --}}
                            </div>
                            <div class="col-md-12 mb-3 invoice d-none">
                                <label for="">Terms & Condition</label>
                                <textarea name="inv_tnc" id="" {{ $disabled }}>{{ isset($invoice->inv_tnc) ? $invoice->inv_tnc : old('inv_tnc') }}</textarea>
                                {{-- @error('inv_tnc')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror --}}
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            @if ($status != 'view')
                                <button class="btn btn-primary" type="button" id="submit-form">
                                    <i class="bi bi-receipt"></i>
                                    <small>
                                        {{ isset($invoice) ? 'Update' : 'Create' }} Invoice
                                    </small>
                                </button>
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
                    <form action="{{ route('receipt.client-program.store') }}" method="POST" id="receipt">
                        @csrf
                        <input type="hidden" name="clientprog_id" value="{{ $clientProg->clientprog_id }}">
                        <input type="hidden" name="identifier" id="identifier">
                        <input type="hidden" name="paymethod" id="paymethod">
                        <input type="hidden" name="currency"
                            value="{{ isset($invoice->currency) ? $invoice->currency : null }}">
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
                                        <input type="text" name="receipt_amount" id="receipt_amount_other"
                                            class="form-control" value="">
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
                                        <input type="text" name="receipt_amount_idr" id="receipt_amount"
                                            class="form-control" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-1">
                                    <label for="">
                                        Date <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="date" name="receipt_date" value="{{ date('Y-m-d') }}"
                                        id="receipt_date" class="form-control form-control-sm rounded">
                                </div>
                            </div>
                            <div class="col-md-12 receipt-other d-none">
                                <div class="mb-1">
                                    <label for="">
                                        Word <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="receipt_words" id="receipt_word_other"
                                        class="form-control form-control-sm rounded" value="" readonly>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-1">
                                    <label for="">
                                        Word <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="receipt_words_idr" id="receipt_word"
                                        class="form-control form-control-sm rounded" value="" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="">
                                        Payment Method <sup class="text-danger">*</sup>
                                    </label>
                                    <select name="receipt_method" class="modal-select w-100" id="receipt_payment"
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
                                    <input type="text" name="receipt_cheque" id="receipt_cheque"
                                        class="form-control form-control-sm rounded" value="" disabled>
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
        function setIdentifier(paymethod, id) {
            $("#identifier").val(id);
            $("#paymethod").val(paymethod);
        }

        $(document).ready(function() {

            $("#currency_detail").on('change', function() {

                showLoading()
                var base_currency = $(this).val();
                var to_currency = 'IDR';

                var link = "{{ url('/') }}/api/current/rate/"+base_currency+"/"+to_currency

                axios.get(link)
                    .then(function (response) {

                        var rate = response.data.rate;
                        $("#current_rate").val(rate)
                        swal.close()

                    }).catch(function (error) {

                        swal.close()
                        notification('error', 'Something went wrong. Please try again');

                    })

            })

            $("#receipt_amount_other").on('keyup', function() {
                var val = $(this).val()
                var currency = $("#receipt input[name=currency]").val()
                var curs_rate = $("#current_rate").val();
                switch (currency) {
                    case 'usd':
                        currency = ' Dollar';
                        break;
                    case 'sgd':
                        currency = ' Singapore Dollar';
                        break;
                    case 'gbp':
                        currency = ' Pound';
                        break;
                    default:
                        currency = '';
                        totprice = '-'
                        break;
                }
                $("#receipt_word_other").val(wordConverter(val) + currency)
                $("#receipt_amount").val(val * curs_rate)
                $("#receipt_word").val(wordConverter(val * curs_rate) + " Rupiah")
            })

            $("#receipt_amount").on('keyup', function() {
                var val = $(this).val()
                $("#receipt_word").val(wordConverter(val) + " Rupiah")
            })

            $('.modal-select').select2({
                dropdownParent: $('#addReceipt .modal-content'),
                placeholder: "Select value",
                allowClear: true
            });

            @if (
                ($clientProg->program->prog_payment == 'idr' || $clientProg->program->prog_payment == 'session') &&
                    !isset($invoice))
                $("#currency").val('idr').trigger('change')
            @elseif (isset($invoice) && $invoice->inv_category == 'idr')
                $("#currency").val('idr').trigger('change')
            @else
                $("#currency").val('other').trigger('change')
            @endif

            @switch (strtolower($clientProg->program->prog_payment))
                @case('usd')
                $("#currency_detail").val("usd").trigger('change')
                @break

                @case('sgd')
                $("#currency_detail").val("sgd").trigger('change')
                @break

                @case('gbp')
                $("#currency_detail").val("gbp").trigger('change')
                @break
            @endswitch

            @if (isset($invoice))
                @if (isset($invoice->inv_paymentmethod) && $invoice->inv_paymentmethod == 'Full Payment')
                    $("#payment_method").val('full').trigger('change')
                @elseif (old('inv_paymentmethod') == 'Full Payment')
                    $("#payment_method").val('full').trigger('change')
                @else
                    $("#payment_method").val('installment').trigger('change')
                @endif
            @endif

            @if ($clientProg->program->prog_payment == 'session' && !isset($invoice))
                $("#session").val('yes').trigger('change')
            @elseif (isset($invoice) && $invoice->inv_category == 'session')
                $("#session").val('yes').trigger('change')
            @else
                $("#session").val('no').trigger('change')
            @endif

            // old
            @if (old('currency') !== null && in_array('idr', (array) old('currency')))
                $("#currency").val('idr').trigger('change')
            @elseif (old('currency') !== null && in_array('other', (array) old('currency')))
                $("#currency").val('other').trigger('change')
            @endif


            @if (old('is_session') == 'yes')
                $("#session").val('yes').trigger('change')
            @else
                $("#session").val('no').trigger('change')
            @endif
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

        $(document).ready(function() {
            @if (old('inv_paymentmethod'))
                $("#payment_method").val("{{ old('inv_paymentmethod') }}").trigger('change');
            @endif

            $("#request-acc").on('click', function(e) {
                e.preventDefault();
                showLoading()

                axios
                    .get(
                        '{{ route('invoice.program.request_sign', ['client_program' => $clientProg->clientprog_id]) }}', {
                            params: {
                                type: 'idr'
                            }
                        })
                    .then(response => {

                        swal.close()
                        notification('success', 'Sign has been requested')
                        $(".step-one").addClass('active')
                    })
                    .catch(error => {
                        console.log(error)
                        swal.close()
                        notification('error', error.message)
                        // notification('error', 'Something went wrong while send email')
                    })
            })

            $("#request-acc-other").on('click', function(e) {
                e.preventDefault();

                showLoading()
                axios
                    .get(
                        '{{ route('invoice.program.request_sign', ['client_program' => $clientProg->clientprog_id]) }}', {
                            params: {
                                type: 'other'
                            }
                        })
                    .then(response => {
                        
                        swal.close()
                        notification('success', 'Sign has been requested')
                        $(".step-one-other").addClass('active')
                    })
                    .catch(error => {
                        console.log(error)
                        notification('error', 'Something went wrong while send email')
                        swal.close()
                    })
            })

            $("#send-inv-client-idr").on('click', function(e) {
                e.preventDefault()
                showLoading()

                axios
                    .get(
                        '{{ route('invoice.program.send_to_client', ['client_program' => $clientProg->clientprog_id, 'currency' => 'idr']) }}'
                    )
                    .then(response => {
                        swal.close()
                        notification('success', 'Invoice has been send to client')
                        $('.step-three').addClass('active');
                    })
                    .catch(error => {
                        notification('error',
                            'Something went wrong when sending invoice to client. Please try again');
                        swal.close()
                    })
            })

            $("#send-inv-client-other").on('click', function(e) {
                e.preventDefault()
                showLoading()

                axios
                    .get(
                        '{{ route('invoice.program.send_to_client', ['client_program' => $clientProg->clientprog_id, 'currency' => 'other']) }}'
                    )
                    .then(response => {
                        swal.close()
                        notification('success', 'Invoice has been send to client')
                        $('.step-three-other').addClass('active');
                    })
                    .catch(error => {
                        notification('error',
                            'Something went wrong when sending invoice to client. Please try again');
                        swal.close()
                    })
            })

            $("#print").on('click', function(e) {
                e.preventDefault();

                showLoading()
                axios
                    .get(
                        '{{ route('invoice.program.export', ['client_program' => $clientProg->clientprog_id]) }}', {
                            responseType: 'arraybuffer',
                            params: {
                                type: 'idr'
                            }
                        })
                    .then(response => {

                        let blob = new Blob([response.data], {
                                type: 'application/pdf'
                            }),
                            url = window.URL.createObjectURL(blob)

                        window.open(
                            url
                        ) // Mostly the same, I was just experimenting with different approaches, tried link.click, iframe and other solutions
                        swal.close()
                        notification('success', 'Invoice has been exported')
                    })
                    .catch(error => {
                        notification('error', 'Something went wrong while exporting the invoice')
                        swal.close()
                    })
            })

            $("#print-other").on('click', function(e) {
                e.preventDefault();

                showLoading()
                axios
                    .get(
                        '{{ route('invoice.program.export', ['client_program' => $clientProg->clientprog_id]) }}', {
                            responseType: 'arraybuffer',
                            params: {
                                type: 'other'
                            }
                        })
                    .then(response => {

                        let blob = new Blob([response.data], {
                                type: 'application/pdf'
                            }),
                            url = window.URL.createObjectURL(blob)

                        window.open(
                            url
                        ) // Mostly the same, I was just experimenting with different approaches, tried link.click, iframe and other solutions
                        swal.close()
                        notification('success', 'Invoice has been exported')
                    })
                    .catch(error => {
                        notification('error', 'Something went wrong while exporting the invoice')
                        swal.close()
                    })
            })
        })

        $("#submit-form").click(function(e) {
            e.preventDefault();

            var currency = $("#currency").val()
            if (currency == "idr") {

                var tot_percent = 0;
                $('.percentage').each(function() {
                    tot_percent += parseInt($(this).val())
                })

                if (tot_percent < 100) {
                    notification('error',
                        'Installment amount is not right. Please double check before create invoice')
                    return;
                }

            } else if (currency == "other") {

                var tot_percent = 0;
                $('.percentage-other').each(function() {
                    tot_percent += parseInt($(this).val())
                })

                if (tot_percent < 100) {
                    notification('error',
                        'Installment amount is not right. Please double check before create invoice')
                    return;
                }

            }


            $("#invoice-form").submit()


        })
    </script>
@endsection
