@extends('layout.main')

@section('title', 'Invoice')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Invoice</a></li>
    <li class="breadcrumb-item active" aria-current="page">View Detail</li>
@endsection

@section('content')

    @php
        $disabled = isset($status) && $status == 'view' ? 'disabled' : null;
        $clientProg = $bundle->details->first()->client_program;
    @endphp

    {{-- @if($errors->any())
        {{ implode('', $errors->all('<div>:message</div>')) }}
    @endif --}}

    <div class="row">
        <div class="col-md-4">
            <div class="card rounded mb-3">
                <div class="card-body text-center">
                  
                    <h3><i class="bi bi-person"></i></h3>
                    <h4>{{ $clientProg->client->full_name }}</h4>

                    <a href="{{ url('client/student/' . $clientProg->client->id) }}"
                        class="text-primary text-decoration-none cursor-pointer" target="_blank">
                        <div class="card p-2 cursor-pointer">
                            <label for="" class="text-muted m-0 mb-2">Program Name:</label>
                            <h6 class="mb-1">
                                Bundling Program
                            </h6>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Tools  --}}
            @if (isset($invoice) && !isset($invoice->refund))
                <div class="bg-white rounded p-2 mb-3 d-flex align-items-stretch gap-2 shadow-sm justify-content-center">

                    @if (isset($invoice) && !isset($invoice->receipt))
                        <div class="border p-1 text-center flex-fill">
                            <div class="d-flex gap-1 justify-content-center">
                                <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                    data-bs-title="{{ $status == 'edit' ? 'Back' : 'Edit' }}">
                                    <a href="{{ $status == 'edit' ? url('invoice/client-program/bundle/' . $bundle->uuid) : url('invoice/client-program/bundle/' . $bundle->uuid . '/edit') }}"
                                        class="text-warning">
                                        <i class="bi {{ $status == 'edit' ? 'bi-arrow-left' : 'bi-pencil' }}"></i>
                                    </a>
                                </div>
                                <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                    data-bs-title="Cancel"
                                    onclick="confirmDelete('invoice/client-program/bundle', '{{ $bundle->uuid }}')">
                                    <a href="#" class="text-danger">
                                        <i class="bi bi-trash2"></i>
                                    </a>
                                </div>
                            </div>
                            <hr class="my-1">
                            <small>General</small>
                        </div>
                    @endif
                    <div class="border p-1 text-center flex-fill">
                        <div class="d-flex gap-1 justify-content-center">
                            <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                data-bs-title="Preview Invoice">
                                <a href="#" data-curr="idr" data-bs-toggle="modal" data-bs-target="#previewSignModal" class="openModalPreviewSign text-info">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                            </div>
                            @if (isset($invoice) && !$invoice->invoiceAttachment()->where('currency', 'idr')->where('sign_status', 'signed')->first())
                                <div class="btn btn-sm py-1 border btn-light" id="openModalRequestSignIdr" data-curr="idr"
                                    data-bs-toggle="modal" data-bs-target="#requestSignModal">
                                    <a href="#" class="text-info" data-bs-toggle="tooltip" data-bs-title="Request Sign">
                                        <i class="bi bi-pen-fill"></i>
                                    </a>
                                </div>
                            @else
                                <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                    data-bs-title="Print Invoice">
                                    <a href="{{ route('invoice.program.print_bundle', ['bundle' => $bundle->uuid, 'currency' => 'idr']) }}"
                                        class="text-info">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>
                                <div class="btn btn-sm py-1 border btn-light" id="openModalSendToClientIdr" data-curr="idr"
                                    data-bs-toggle="modal" data-bs-target="#sendToClientModal">
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
                                <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                    data-bs-title="Preview Invoice">
                                    <a href="#" data-curr="other" data-bs-toggle="modal" data-bs-target="#previewSignModal" class="openModalPreviewSign text-info" target="blank">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                </div>
                                @if ( !isset($invoice->refund) && isset($invoice) && !$invoice->invoiceAttachment()->where('currency', 'other')->where('sign_status', 'signed')->first())
                                    <div class="btn btn-sm py-1 border btn-light" id="openModalRequestSignIdr" data-curr="other"
                                        data-bs-toggle="modal" data-bs-target="#requestSignModal">
                                        <a href="#" class="text-info" data-bs-toggle="tooltip" data-bs-title="Request Sign">
                                            <i class="bi bi-pen-fill"></i>
                                        </a>
                                    </div>
                                @else
                                    <div class="btn btn-sm py-1 border btn-light" data-bs-toggle="tooltip"
                                        data-bs-title="Print Invoice">
                                        <a href="{{ route('invoice.program.print_bundle', ['bundle' => $bundle->uuid, 'currency' => 'other']) }}"
                                            class="text-info">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                    <div class="btn btn-sm py-1 border btn-light" id="openModalSendToClientOther"
                                        data-curr="other" data-bs-toggle="modal" data-bs-target="#sendToClientModal">
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
                            $invoiceHasBeenRequested = $invoice
                                ->invoiceAttachment()
                                ->where('currency', 'idr')
                                ->where('sign_status', 'not yet')
                                ->first();
                            $invoiceHasBeenSigned = $invoice
                                ->invoiceAttachment()
                                ->where('currency', 'idr')
                                ->where('sign_status', 'signed')
                                ->first();
                            $invoiceHasSentToClient = $invoice
                                ->invoiceAttachment()
                                ->where('currency', 'idr')
                                ->where('sign_status', 'signed')
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
                                        $invoiceHasBeenRequested ||
                                        $invoiceHasBeenSigned ||
                                        $invoiceHasSentToClient,
                                ])>
                                    <div @class([
                                        'step-one',
                                        'step-icon',
                                        'active' =>
                                            $invoiceHasBeenRequested ||
                                            $invoiceHasBeenSigned ||
                                            $invoiceHasSentToClient,
                                    ])>1</div>
                                    <p>Request Sign</p>
                                </div>
                                <div @class([
                                    'step-one',
                                    'indicator-line',
                                    'active' =>
                                        $invoiceHasBeenRequested ||
                                        $invoiceHasBeenSigned ||
                                        $invoiceHasSentToClient,
                                ])></div>
                                <div @class([
                                    'step',
                                    'step2',
                                    'active' => $invoiceHasBeenSigned || $invoiceHasSentToClient,
                                ])>
                                    <div @class([
                                        'step-icon',
                                        'active' => $invoiceHasBeenSigned || $invoiceHasSentToClient,
                                    ])>2</div>
                                    <p>Signed</p>
                                </div>
                                <div @class([
                                    'indicator-line',
                                    'active' => $invoiceHasBeenSigned || $invoiceHasSentToClient,
                                ])></div>
                                <div @class([
                                    'step-three',
                                    'step',
                                    'step3',
                                    'active' => $invoiceHasSentToClient,
                                ])>
                                    <div @class([
                                        'step-three',
                                        'step-icon',
                                        'active' => $invoiceHasSentToClient,
                                    ])>3</div>
                                    <p>Print or Send to Client</p>
                                </div>
                            </section>
                        </div>

                        {{-- Other  --}}
                        @if ($invoice->currency != 'idr')
                            @php
                                $invoiceHasBeenRequested_other = $invoice
                                    ->invoiceAttachment()
                                    ->where('currency', 'other')
                                    ->where('sign_status', 'not yet')
                                    ->first();
                                $invoiceHasBeenSigned_other = $invoice
                                    ->invoiceAttachment()
                                    ->where('currency', 'other')
                                    ->where('sign_status', 'signed')
                                    ->first();
                                $invoiceHasSentToClient_other = $invoice
                                    ->invoiceAttachment()
                                    ->where('currency', 'other')
                                    ->where('sign_status', 'signed')
                                    ->where('send_to_client', 'sent')
                                    ->first();
                            @endphp
                            <div class="text-center mt-5">
                                <hr>
                                <h6>Other Currency</h6>
                                <section class="step-indicator">
                                    <div @class([
                                        'step-one-other',
                                        'step',
                                        'step1',
                                        'active' =>
                                            $invoiceHasBeenRequested_other ||
                                            $invoiceHasBeenSigned_other ||
                                            $invoiceHasSentToClient_other,
                                    ])>
                                        <div @class([
                                            'step-one-other',
                                            'step-icon',
                                            'active' =>
                                                $invoiceHasBeenRequested_other ||
                                                $invoiceHasBeenSigned_other ||
                                                $invoiceHasSentToClient_other,
                                        ])>1</div>
                                        <p>Request Sign</p>
                                    </div>
                                    <div @class([
                                        'step-one-other',
                                        'indicator-line',
                                        'active' =>
                                            $invoiceHasBeenRequested_other ||
                                            $invoiceHasBeenSigned_other ||
                                            $invoiceHasSentToClient_other,
                                    ])></div>
                                    <div @class([
                                        'step',
                                        'step2',
                                        'active' => $invoiceHasBeenSigned_other || $invoiceHasSentToClient_other,
                                    ])>
                                        <div @class([
                                            'step-icon',
                                            'active' => $invoiceHasBeenSigned_other || $invoiceHasSentToClient_other,
                                        ])>2</div>
                                        <p>Signed</p>
                                    </div>
                                    <div @class([
                                        'indicator-line',
                                        'active' => $invoiceHasBeenSigned_other || $invoiceHasSentToClient_other,
                                    ])></div>
                                    <div @class([
                                        'step-three-other',
                                        'step',
                                        'step3',
                                        'active' => $invoiceHasSentToClient_other,
                                    ])>
                                        <div @class([
                                            'step-three-other',
                                            'step-icon',
                                            'active' => $invoiceHasSentToClient_other,
                                        ])>3</div>
                                        <p>Print or Send to Client</p>
                                    </div>
                                </section>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if (isset($invoice->receipt) && $clientProg->status == 3)
                @include('pages.invoice.client-program.detail.refund')
            @endif

            @include('pages.invoice.client-program.form-detail.list-program')
            @include('pages.invoice.client-program.form-detail.client-bundle')

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
                        <h6 class="mt-2 mb-0 p-0">
                            <i class="bi bi-calendar-week me-2"></i>
                            {{ isset($invoice) ? 'Date : ' . $invoice->created_at : '' }} 
                        </h6>
                    </div>
                    <div class="">
                        {{-- @if ($invoice === null && isset($invoice->receipt)) --}}
                        @if (isset($invoice) && $invoice->inv_paymentmethod == 'Full Payment' && !isset($invoice->receipt))
                            <button class="btn btn-sm btn-outline-primary py-1"
                                onclick="checkReceipt();setIdentifier('Full Payment', '{{ $invoice->id }}');setDefault('{{ $invoice->inv_totalprice }}', '{{ $invoice->inv_totalprice_idr }}')">
                                <i class="bi bi-plus"></i> Receipt
                            </button>
                        @endif
                        @if (isset($invoice->receipt) && $invoice->inv_paymentmethod == 'Full Payment')
                            <a href="{{ route('receipt.client-program.show', ['receipt' => $invoice->receipt->id, 'b' => true]) }}">
                                <button class="btn btn-sm btn-outline-warning py-1">
                                    <i class="bi bi-eye"></i> View Receipt
                                </button>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <form
                        action="{{ isset($invoice) ? route('invoice.program.update_bundle', ['bundle' => $bundle->uuid]) : route('invoice.program.store_bundle', ['bundle' => $bundle->uuid]) }}"
                        method="POST" id="invoice-form">
                        @csrf
                        @if (isset($invoice))
                            @method('PUT')
                        @endif
                        <input type="hidden" name="bundling_id" value="{{ $bundle->uuid }}">
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
                                <select class="select w-100" name="currency_detail" id="currency_detail"
                                    {{ $disabled !== null ? $disabled : 'onchange=checkCurrencyDetail()' }}>
                                    <option data-placeholder="true"></option>
                                    <option value="usd" @if (old('currency') !== null && in_array('usd', (array) old('currency_detail'))) {{ 'selected' }} @endif>
                                        USD</option>
                                    <option value="sgd" @if (old('currency') !== null && in_array('sgd', (array) old('currency_detail'))) {{ 'selected' }} @endif>
                                        SGD</option>
                                    <option value="gbp" @if (old('currency') !== null && in_array('gbp', (array) old('currency_detail'))) {{ 'selected' }} @endif>
                                        GBP</option>
                                    <option value="aud" @if (old('currency') !== null && in_array('aud', (array) old('currency_detail'))) {{ 'selected' }} @endif>
                                        AUD</option>
                                    <option value="vnd" @if (old('currency') !== null && in_array('vnd', (array) old('currency_detail'))) {{ 'selected' }} @endif>
                                        VND</option>
                                    <option value="myr" @if (old('currency') !== null && in_array('myr', (array) old('currency_detail'))) {{ 'selected' }} @endif>
                                        MYR</option>
                                    <option value="jpy" @if (old('currency') !== null && in_array('jpy', (array) old('currency_detail'))) {{ 'selected' }} @endif>
                                        JPY</option>
                                    <option value="cny" @if (old('currency') !== null && in_array('cny', (array) old('currency_detail'))) {{ 'selected' }} @endif>
                                        CNY</option>
                                    <option value="thb" @if (old('currency') !== null && in_array('thb', (array) old('currency_detail'))) {{ 'selected' }} @endif>
                                        THB</option>
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

                            
                            {{-- NOT SESSION  --}}
                            <div class="col-md-12 session-detail not-session mb-3">
                                {{-- IDR  --}}
                                <div class="not-session-currency not-session-idr">
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
                                @error('inv_paymentmethod')
                                    <small class="text-danger fw-light">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-7">
                                <div class="row">
                                    <div class="col-md-6 mb-3 invoice d-none">
                                        <label for="">Created Date <sup class="text-danger">*</sup></label>
                                        <input type="date" name="invoice_date" id=""
                                            value="{{ isset($invoice->created_at) ? date('Y-m-d', strtotime($invoice->created_at)) : date('Y-m-d') }}" {{ $disabled }}
                                            class='form-control form-control-sm rounded'>
                                        @error('invoice_date')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3 invoice d-none">
                                        <label for="">Due Date <sup class="text-danger">*</sup></label>
                                        <input type="date" name="inv_duedate" id=""
                                            value="{{ isset($invoice->inv_duedate) ? $invoice->inv_duedate : old('inv_duedate') }}"
                                            {{ $disabled }} class='form-control form-control-sm rounded'>
                                        @error('inv_duedate')
                                            <small class="text-danger fw-light">{{ $message }}</small>
                                        @enderror
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
                    <form action="{{ route('receipt.client-program.store_bundle') }}" method="POST" id="receipt">
                        @csrf
                        <input type="hidden" name="bundling_id" value="{{ $bundle->uuid }}">
                        <input type="hidden" name="identifier" id="identifier">
                        <input type="hidden" name="paymethod" id="paymethod">
                        <input type="hidden" name="rec_currency"
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
                                @error('receipt_amount')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
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
                                @error('receipt_amount_idr')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <div class="mb-1">
                                    <label for="">
                                        Date <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="date" name="receipt_date" value="{{ date('Y-m-d') }}"
                                        id="receipt_date" class="form-control form-control-sm rounded">
                                </div>
                                @error('receipt_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 receipt-other d-none">
                                <div class="mb-1">
                                    <label for="">
                                        Word <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="receipt_words" id="receipt_word_other"
                                        class="form-control form-control-sm rounded" value="" readonly>
                                </div>
                                @error('receipt_words')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <div class="mb-1">
                                    <label for="">
                                        Word <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="receipt_words_idr" id="receipt_word"
                                        class="form-control form-control-sm rounded" value="" readonly>
                                </div>
                                @error('receipt_words_idr')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
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
                                @error('receipt_method')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="">
                                        Cheque No <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" name="receipt_cheque" id="receipt_cheque"
                                        class="form-control form-control-sm rounded" value="" disabled>
                                </div>
                                @error('receipt_cheque')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
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

    {{-- @if ($clientProg->client->parents->count() > 0) --}}
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
                        <div class="d-flex justify-content-around">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input input-recipient" id="stc-parent" type="radio" name="recipient" value="Parent" onchange="checkRecipient()" {{ $clientProg->client->parents->count() > 0 ? 'checked' : null}}>
                                <label class="form-check-label" for="stc-parent">Parent</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input input-recipient" id="stc-client" type="radio" name="recipient" value="Client" onchange="checkRecipient()" {{ $clientProg->client->parents->count() < 1  ? 'checked' : null}}>
                                <label class="form-check-label" for="stc-client">Client</label>
                            </div>
                        </div>

                        <input type="hidden" name="clientprog_id" id="clientprog_id"
                            value="{{ $clientProg->clientprog_id }}" class="form-control w-100">
                        <input type="hidden" name="client_id" id="client_id"
                            value="{{ $clientProg->client->parents->count() > 0 ? $clientProg->client->parents[0]->id : $clientProg->client->id }}" class="form-control w-100">
                        <label for="">Email</label>
                        <input type="mail" name="mail" id="mail"
                            value="{{ $clientProg->client->parents->count() > 0 ? $clientProg->client->parents[0]->mail : $clientProg->client->mail }}" class="form-control w-100">
                    </div>
                    {{-- <hr> --}}
                    <div class="d-flex justify-content-between">
                        <button type="button" href="#" class="btn btn-outline-danger btn-sm"
                            data-bs-dismiss="modal">
                            <i class="bi bi-x-square me-1"></i>
                            Cancel</button>

                        <button type="submit" id="ConfirmSendToClient" class="btn btn-primary btn-sm">
                            <i class="bi bi-save2 me-1"></i>
                            Send</button>
                    </div>
                    {{-- </form> --}}
                </div>
            </div>
        </div>
    </div>
    {{-- @endif --}}

    @include('pages.invoice.pic-modal')

    <script>
        $(document).ready(function() {
            @if (
                $errors->first('receipt_amount') ||
                    $errors->first('receipt_amount_idr') ||
                    $errors->first('receipt_date') ||
                    $errors->first('receipt_words') ||
                    $errors->first('receipt_words_idr') ||
                    $errors->first('receipt_method') ||
                    $errors->first('receipt_cheque'))
                $("button[data-idn='{{ old('identifier') }}']").click()
            @endif

            @if (
                $errors->has('total_payment') ||
                    $errors->has('total_paid') ||
                    $errors->has('percentage_refund') ||
                    $errors->has('refund_amount') ||
                    $errors->has('tax_percentage') ||
                    $errors->has('tax_amount') ||
                    $errors->has('total_refunded'))
                $("#refund").modal('show')
            @endif
        })
    </script>
    <script>

        function checkRecipient(){
            var recipient = $("input[name=recipient]:checked").val();
            switch (recipient) {
                case 'Parent':
                    $("#client_id").val('{{ $clientProg->client->parents->count() > 0 ? $clientProg->client->parents[0]->id : null}}')
                    $("#mail").val('{{ $clientProg->client->parents->count() > 0 ? $clientProg->client->parents[0]->mail : null }}')
                    break;
                    
                case 'Client':
                    $("#client_id").val('{{ $clientProg->client->id }}')
                    $("#mail").val('{{ $clientProg->client->mail }}')
                    break;
            }
        }

        function setIdentifier(paymethod, id) {
            $("#identifier").val(id);
            $("#paymethod").val(paymethod);
        }

        function setDefault(other_amount, idr_amount) {
            var currency = $("#receipt input[name=rec_currency]").val()
            switch (currency) {
                case 'usd':
                    currency = ' Dollars';
                    break;
                case 'sgd':
                    currency = ' Singapore Dollars';
                    break;
                case 'gbp':
                    currency = ' British Pounds';
                    break;
                default:
                    currency = '';
                    break;
            }
            $("#receipt_amount").val(idr_amount)
            $("#receipt_word").val(wordConverter(idr_amount) + " Rupiah")

            if (other_amount > 0) {
                $("#receipt_amount_other").val(other_amount)
                $("#receipt_word_other").val(wordConverter(other_amount) + currency)
            }
        }

        $(document).ready(function() {

            @if (!$disabled)
                $("#currency_detail").on('change', function() {

                    var current_rate = $("#current_rate").val()
                    checkCurrencyDetail();

                    showLoading()
                    var base_currency = $(this).val();
                    var to_currency = 'IDR';

                    var link = "{{ url('/') }}/api/current/rate/" + base_currency + "/" + to_currency

                    axios.get(link)
                        .then(function(response) {

                            var rate = response.data.rate;
                            $("#current_rate").val(rate)
                            swal.close()

                        }).catch(function(error) {

                            $("#current_rate").val('');

                            swal.close()
                            notification('error',
                                'Something went wrong while trying to get the currency rate');

                        })

                })
            @endif

            $("#receipt_amount_other").on('keyup', function() {
                var val = $(this).val()
                var currency = $("#receipt input[name=rec_currency]").val()
                var curs_rate = $("#current_rate").val();
                switch (currency) {
                    case 'usd':
                        currency = ' Dollars';
                        break;
                    case 'sgd':
                        currency = ' Singapore Dollars';
                        break;
                    case 'gbp':
                        currency = ' British Pounds';
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

            @if (isset($invoice))

                // change the currency-icon 
                var detail = "{{ $invoice->currency }}"
                $('.currency-icon').html(currencySymbol(detail))

                @switch ($invoice->inv_category)
                    @case('idr')
                    $("#currency").val('idr').trigger('change')
                    @break

                    @case('session')
                    @if ($invoice->currency == 'idr')
                        $("#currency").val('idr').trigger('change')
                    @else
                        $("#currency").val('other').trigger('change')
                        $("#currency_detail").val('{{ $invoice->currency }}').trigger('change')
                    @endif
                    $("#session").val('yes').trigger('change')
                    @break

                    @default
                    $("#currency").val('other').trigger('change')
                    $("#currency_detail").val('{{ $invoice->currency }}').trigger('change')
                @endswitch
            @else
                @switch (strtolower($clientProg->program->prog_payment))
                    @case('usd')
                    console.log("usd")
                    $("#currency_detail").val("usd").trigger('change')
                    @break

                    @case('sgd')
                    $("#currency_detail").val("sgd").trigger('change')
                    @break

                    @case('gbp')
                    $("#currency_detail").val("gbp").trigger('change')
                    @break
                @endswitch

                @if ($clientProg->program->prog_payment == 'idr' || $clientProg->program->prog_payment == 'session')
                    $("#currency").val('idr').trigger('change')
                @else
                    $("#currency").val('other').trigger('change')
                @endif
            @endif


            // @if (isset($invoice) && $invoice->currency)
            //     $("#currency_detail").val('{{ $invoice->currency }}').trigger('change');
            //     let detail = $('#currency_detail').val()
            //     if (detail) {
            //         $('.currency-icon').html(currencySymbol(detail))
            //     }
            // @endif

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


            @if (old('is_session'))
                @if (old('is_session') == 'yes')
                    $("#session").val('yes').trigger('change')
                @else
                    $("#session").val('no').trigger('change')
                @endif
            @endif
        });

        function checkCurrency() {
            checkPayment()
            let cur = $('#currency').val()
      

            if (cur == 'other') {
                $('.currency-detail').removeClass('d-none')
            } else {
                $('.currency-detail').addClass('d-none')
            }

                checkSession()
            
        }

        // $("#current_rate").on('keyup', function() {
        //     checkNotSessionOther()
        // })

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

           
                $('.not-session').removeClass('d-none')
                $('.not-session-currency').addClass('d-none')
                if (cur == 'idr') {
                    $('.not-session-idr').removeClass('d-none')
                } else {
                    $('.not-session-other').removeClass('d-none')
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

        $(document).on("click", ".openModalPreviewSign", function() {
            var curr = $(this).data('curr');
            cur = "'" + curr + "'";

            const url = "{{ url('/') }}/invoice/client-program/bundle/{{ $bundle->uuid }}/preview/" + curr
            
            $("#previewForm").attr('action', url)
        });

        $(document).on('change', '#previewForm input[name=preview_pic_sign]', function() {
            const pickedDir = $(this).val();
            
            var form_action = $("#previewForm").attr('action');
            
            var action = new URL(form_action);
            const find = new URLSearchParams(action.search);
            if (find.has('dir')) {

                action.searchParams.set('dir', pickedDir);
                action = action.toString();
                
            } else {
                
                const queryParams = new URLSearchParams({
                    key: 'dashboard',
                    dir: pickedDir
                });
    
                action += `?${queryParams.toString()}`;
            }

            $(".download-preview").attr('onclick', `downloadFilePreview('${action}')`);

            $("#previewForm").attr('action', action);
            

        })

        function downloadFilePreview(action)
        {
            window.open(action, '_blank');
        }

        $(document).on("click", "#openModalRequestSignIdr", function() {
            var curr = $(this).data('curr');
            curr = "'" + curr + "'";
            $('#sendToChoosenPic').attr("onclick",
            "confirmRequestSign('{{ route('invoice.program.request_sign_bundle', ['bundle' => $bundle->uuid]) }}', " +
                curr + ")");
        });

        $(document).on("click", "#openModalSendToClientIdr", function() {
            var curr = $(this).data('curr');
            curr = "'" + curr + "'";

            $('#ConfirmSendToClient').attr("onclick",
                "confirmSendToClient('{{ url('/') }}/invoice/client-program/bundle/{{ $bundle->uuid }}/send', " +
                curr + ", 'invoice')");
        });

        $(document).on("click", "#openModalSendToClientOther", function() {
            var curr = $(this).data('curr');
            curr = "'" + curr + "'";

            $('#ConfirmSendToClient').attr("onclick",
                "confirmSendToClient('{{ url('/') }}/invoice/client-program/bundle/{{ $bundle->uuid }}/send', " +
                curr + ", 'invoice')");
        });

        function sendToClient(link) {
            $("#sendToClient--modal").modal('hide');
            $('#sendToClientModal').modal('hide');
            showLoading()
            var recipient = $("input[name=recipient]:checked").val();

            var linkUpdateMail = '{{ url('/') }}/invoice/client-program/' + $('#clientprog_id').val() +
                '/update/mail';
            axios.post(linkUpdateMail, {
                    client_id: $('#client_id').val(),
                    mail: $('#mail').val(),
                })
                .then(function(response1) {

                    axios
                        .get(link + '/' + recipient)
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
                .catch(function(error) {
                    swal.close();
                    notification('error', error)
                })



        }

        function requestAcc(link, currency)
        {
            showLoading()
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
                    $(".step-one").addClass('active')
                    $("#requestSignModal").modal('hide');
                    $("#requestSign--modal").modal('hide'); // this modal is for confirmation box   
                })
                .catch(error => {
                    console.log(error)
                    swal.close()
                    notification('error', error.message)
                    // notification('error', 'Something went wrong while send email')
                })
        }

        $(document).ready(function() {
            $('.invoice').removeClass('d-none')

            @if (old('inv_paymentmethod'))
                $("#payment_method").val("{{ old('inv_paymentmethod') }}").trigger('change');
            @endif

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

            var currency = $("#currency").val();
            if (currency == "other")
                currency = $("#currency_detail").val();
            var payment_method = $("#payment_method").val();
            if (payment_method == "installment") {

                if (currency == "idr") {

                    var tot_percent = 0;
                    $('.percentage').each(function() {
                        tot_percent += parseInt($(this).val())
                    })

                    var tot_amount = 0;
                    $('.amount').each(function() {
                        tot_amount += parseInt($(this).val())
                    })

                    var real_total_amount = $("#not_session_idr_total").val();

                    if ( (tot_percent < 100) && (tot_amount != real_total_amount)) {
                        notification('error',
                            'Installment amount is not right. Please double check before create an invoice')
                        return;
                    }

                } else if (currency == "other") {

                    var tot_percent = 0;
                    $('.percentage-other').each(function() {
                        tot_percent += parseInt($(this).val())
                    })

                    if ( (tot_percent < 100) && (tot_amount != real_total_amount)) {
                        notification('error',
                            'Installment amount is not right. Please double check before create an invoice')
                        return;
                    }

                }


            }
            $("#invoice-form").submit()


        })
    </script>
@endsection
